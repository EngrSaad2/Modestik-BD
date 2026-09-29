<?php

namespace App\Services\Courier;

use App\Models\{Order, CourierLog, Setting, OrderStatusHistory};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SteadFastService implements CourierServiceInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $secretKey;
    protected bool $isTestMode;

    public function __construct()
    {
        $this->isTestMode = env('STEADFAST_TEST_MODE', false) || Setting::get('steadfast_mode', 'live') === 'test';
        $this->baseUrl = rtrim(env('STEADFAST_BASE_URL', Setting::get('steadfast_base_url', 'https://portal.steadfast.com.bd/api/v1')), '/');
        $this->apiKey = env('STEADFAST_API_KEY', Setting::get('steadfast_api_key', ''));
        $this->secretKey = env('STEADFAST_SECRET_KEY', Setting::get('steadfast_secret_key', ''));
    }

    protected function getHeaders(): array
    {
        return [
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function createParcel(Order $order): array
    {
        $validationErrors = $order->validateForCourier();
        if (!empty($validationErrors)) {
            $msg = 'Order validation failed: ' . implode(', ', $validationErrors);
            $this->logAction($order->id, 'create_parcel', [], ['status' => 422, 'error' => $msg], 422, $msg);
            return [
                'success' => false,
                'message' => $msg,
            ];
        }

        $itemsSummary = $order->items->map(fn($item) => $item->name . ' x' . $item->quantity)->join(', ');
        $fullAddress = implode(', ', array_filter([$order->address, $order->area, $order->division, $order->district]));

        $payload = [
            'invoice' => (string) $order->order_number,
            'recipient_name' => (string) $order->name,
            'recipient_phone' => (string) $order->phone,
            'recipient_address' => (string) $fullAddress,
            'cod_amount' => (float) $order->cod_amount,
            'note' => (string) ($order->notes ? Str::limit($order->notes, 200) : 'Items: ' . Str::limit($itemsSummary, 180)),
        ];

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(15)
                ->post($this->baseUrl . '/create_order', $payload);

            $data = $response->json();
            $statusCode = $response->status();

            $this->logAction($order->id, 'create_parcel', $payload, $data, $statusCode, $response->failed() ? ($data['message'] ?? 'API Request Failed') : null);

            if ($response->successful() && isset($data['status']) && $data['status'] == 200 && isset($data['consignment'])) {
                $consignment = $data['consignment'];
                
                $order->update([
                    'courier_name' => 'steadfast',
                    'consignment_id' => $consignment['consignment_id'] ?? null,
                    'tracking_code' => $consignment['tracking_code'] ?? null,
                    'tracking_number' => $consignment['tracking_code'] ?? $order->tracking_number,
                    'courier_status' => $consignment['status'] ?? 'pending',
                    'last_courier_sync_at' => now(),
                ]);

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'comment' => "Parcel created on SteadFast (CID: {$order->consignment_id}, Tracking: {$order->tracking_code})",
                    'changed_by' => auth()->id(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Parcel created successfully on SteadFast.',
                    'consignment' => $consignment,
                    'order' => $order,
                ];
            }

            $errMsg = $data['message'] ?? ($data['errors'] ? json_encode($data['errors']) : 'SteadFast API returned error ' . $statusCode);
            return [
                'success' => false,
                'message' => 'SteadFast API Error: ' . $errMsg,
                'response' => $data,
            ];

        } catch (\Throwable $e) {
            Log::error('SteadFast Parcel Creation Exception: ' . $e->getMessage());
            $this->logAction($order->id, 'create_parcel', $payload, null, 500, $e->getMessage());
            return [
                'success' => false,
                'message' => 'SteadFast Connection Exception: ' . $e->getMessage(),
            ];
        }
    }

    public function trackByConsignmentId(string $cid): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(10)
                ->get($this->baseUrl . '/status_by_cid/' . $cid);

            $data = $response->json();
            $statusCode = $response->status();

            $this->logAction(null, 'track_cid', ['cid' => $cid], $data, $statusCode);

            if ($response->successful() && isset($data['delivery_status'])) {
                return [
                    'success' => true,
                    'status' => $data['delivery_status'],
                    'response' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to retrieve tracking info.',
                'response' => $data,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function trackByTrackingCode(string $code): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(10)
                ->get($this->baseUrl . '/status_by_trackingcode/' . $code);

            $data = $response->json();
            $statusCode = $response->status();

            $this->logAction(null, 'track_code', ['code' => $code], $data, $statusCode);

            if ($response->successful() && isset($data['delivery_status'])) {
                return [
                    'success' => true,
                    'status' => $data['delivery_status'],
                    'response' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to retrieve tracking info.',
                'response' => $data,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkBalance(): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(10)
                ->get($this->baseUrl . '/get_balance');

            $data = $response->json();
            $statusCode = $response->status();

            $this->logAction(null, 'check_balance', [], $data, $statusCode);

            if ($response->successful() && isset($data['current_balance'])) {
                return [
                    'success' => true,
                    'balance' => $data['current_balance'],
                    'message' => 'API Connection Successful. Current Balance: ৳' . number_format($data['current_balance'], 2),
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'API connected but response invalid.',
                'response' => $data,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'API Connection Failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function logAction(?int $orderId, string $action, array $req, ?array $res, int $statusCode = 200, ?string $error = null): void
    {
        try {
            CourierLog::create([
                'order_id' => $orderId,
                'courier_name' => 'steadfast',
                'action' => $action,
                'request_payload' => $req,
                'response_payload' => $res,
                'status_code' => $statusCode,
                'error_message' => $error,
                'created_by' => auth()->id(),
            ]);
        } catch (\Throwable $e) {
            Log::error('CourierLog creation failed: ' . $e->getMessage());
        }
    }
}
