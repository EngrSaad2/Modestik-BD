<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendOrderDeliveredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-order-delivered {order_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send order delivered email to customer in the background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        $order = \App\Models\Order::with('items')->find($orderId);
        
        if (!$order) {
            $this->error("Order #{$orderId} not found.");
            return 1;
        }

        if (!$order->email) {
            $this->info("Order #{$orderId} has no email address.");
            return 0;
        }

        try {
            \Illuminate\Support\Facades\Mail::to($order->email)->send(new \App\Mail\OrderDeliveredMail($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Delivered Background Mail failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
