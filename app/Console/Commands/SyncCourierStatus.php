<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Courier\CourierManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncCourierStatus extends Command
{
    protected $signature = 'courier:sync-status';
    protected $description = 'Sync active parcel delivery statuses from courier APIs every 30 minutes';

    public function handle(CourierManager $courierManager): int
    {
        $orders = Order::whereNotNull('consignment_id')
            ->whereNotIn('courier_status', ['delivered', 'cancelled', 'returned'])
            ->get();

        $this->info("Found {$orders->count()} active parcels to sync.");

        $updatedCount = 0;
        foreach ($orders as $order) {
            try {
                $res = $courierManager->driver($order->courier_name ?? 'steadfast')->trackByConsignmentId($order->consignment_id);
                if ($res['success'] && isset($res['status'])) {
                    $order->update([
                        'courier_status' => $res['status'],
                        'last_courier_sync_at' => now(),
                    ]);
                    $updatedCount++;
                }
            } catch (\Throwable $e) {
                Log::error("Courier Sync Error for Order #{$order->order_number}: " . $e->getMessage());
            }
        }

        $this->info("Successfully synced {$updatedCount} order statuses.");
        return Command::SUCCESS;
    }
}
