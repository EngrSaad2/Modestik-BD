<?php

namespace App\Console\Commands;

use App\Services\Cart\AbandonedCartService;
use Illuminate\Console\Command;

class DetectAbandonedCarts extends Command
{
    protected $signature = 'cart:detect-abandoned';

    protected $description = 'Detect inactive carts and mark them as abandoned past the threshold';

    public function handle(AbandonedCartService $cartService): int
    {
        $this->info('Checking for inactive carts...');
        $count = $cartService->detectInactiveCarts();
        $this->info("✅ Marked {$count} inactive cart(s) as abandoned.");
        return Command::SUCCESS;
    }
}
