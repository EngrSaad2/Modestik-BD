<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Courier\CourierManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushOrderToCourierJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function handle(CourierManager $courierManager): void
    {
        if ($this->order->consignment_id) {
            return; // Skip if already pushed
        }

        $courierManager->driver('steadfast')->createParcel($this->order);
    }
}
