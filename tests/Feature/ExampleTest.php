<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_guest_can_add_to_cart_and_place_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = \App\Models\User::first();
        $product = \App\Models\Product::first();
        $zone = \App\Models\ShippingZone::first();

        // 1. Add to cart
        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        $response->assertStatus(200);

        // 2. Load checkout page
        $response = $this->actingAs($user)->get(route('checkout.index'));
        $response->assertStatus(200);

        // 3. Place order
        $response = $this->actingAs($user)->post(route('checkout.place-order'), [
            'name' => 'John Doe',
            'phone' => '01700000000',
            'email' => 'john@example.com',
            'address' => 'Test Address, Dhaka',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'shipping_zone_id' => $zone->id,
            'payment_method' => 'cod'
        ]);

        // Expect redirect to order confirmation
        $response->assertRedirect();
        
        $this->assertDatabaseHas('orders', [
            'name' => 'John Doe',
            'phone' => '01700000000',
            'payment_method' => 'cod',
            'user_id' => $user->id
        ]);
    }
}
