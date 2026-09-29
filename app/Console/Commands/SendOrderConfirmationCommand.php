<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendOrderConfirmationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-order-confirmation {order_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send order confirmation email to customer and admin in the background';

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

        try {
            if ($order->email) {
                \Illuminate\Support\Facades\Mail::to($order->email)->send(new \App\Mail\OrderConfirmationMail($order));
            }
            \Illuminate\Support\Facades\Mail::to(config('mail.from.address'))->send(new \App\Mail\OrderConfirmationMail($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Confirmation Background Mail failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
