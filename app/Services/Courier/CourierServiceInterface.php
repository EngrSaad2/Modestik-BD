<?php

namespace App\Services\Courier;

use App\Models\Order;

interface CourierServiceInterface
{
    /**
     * Create parcel on the courier platform.
     */
    public function createParcel(Order $order): array;

    /**
     * Track parcel status using Consignment ID.
     */
    public function trackByConsignmentId(string $cid): array;

    /**
     * Track parcel status using Tracking Code / Invoice.
     */
    public function trackByTrackingCode(string $code): array;

    /**
     * Check merchant account balance.
     */
    public function checkBalance(): array;
}
