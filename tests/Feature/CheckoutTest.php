<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\CheckLowStock;
use App\Jobs\SendOrderConfirmation;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_is_created_with_correct_items(): void
    {
        $user = User::factory()->create();
        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        $product->branches()->attach($branch->id, ['stock_level' => 50]);

        $this->actingAs($user);

        $orderService = app(OrderService::class);
        $order = $orderService->createOrder(
            ['total' => 200.00, 'address' => '123 Test St'],
            [$product->id => ['quantity' => 2, 'price' => 100.00]],
            $branch->id
        );

        $this->assertDatabaseHas('orders', [
            'id'               => $order->id,
            'user_id'          => $user->id,
            'branch_id'        => $branch->id,
            'total_amount'     => 200.00,
            'status'           => 'pending',
            'shipping_address' => '123 Test St',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'price'      => 100.00,
        ]);
    }

    public function test_stock_is_decremented_after_order(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['price' => 50.00]);
        $product->branches()->attach($branch->id, ['stock_level' => 30, 'low_stock_threshold' => 5]);

        $this->actingAs($user);

        $orderService = app(OrderService::class);
        $orderService->createOrder(
            ['total' => 250.00, 'address' => 'Test'],
            [$product->id => ['quantity' => 5, 'price' => 50.00]],
            $branch->id
        );

        $this->assertEquals(25, \DB::table('branch_product')
            ->where('product_id', $product->id)
            ->where('branch_id', $branch->id)
            ->value('stock_level'));
    }

    public function test_low_stock_job_is_dispatched(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['price' => 50.00]);
        $product->branches()->attach($branch->id, ['stock_level' => 10, 'low_stock_threshold' => 10]);

        $this->actingAs($user);

        $orderService = app(OrderService::class);
        $orderService->createOrder(
            ['total' => 50.00, 'address' => 'Test'],
            [$product->id => ['quantity' => 1, 'price' => 50.00]],
            $branch->id
        );

        Queue::assertPushed(CheckLowStock::class);
    }

    public function test_order_with_discount_is_saved_correctly(): void
    {
        $user = User::factory()->create();
        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        $product->branches()->attach($branch->id, ['stock_level' => 10]);

        $this->actingAs($user);

        $orderService = app(OrderService::class);
        $order = $orderService->createOrder(
            [
                'total'       => 90.00,
                'discount'    => 10.00,
                'coupon_code' => 'SAVE10',
                'address'     => 'Test',
            ],
            [$product->id => ['quantity' => 1, 'price' => 100.00]],
            $branch->id
        );

        $this->assertEquals(90.00, (float) $order->total_amount);
        $this->assertEquals(10.00, (float) $order->discount);
        $this->assertEquals('SAVE10', $order->coupon_code);
    }

    public function test_unauthenticated_user_cannot_access_orders(): void
    {
        $response = $this->get(route('orders.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_own_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('orders.show', $order));
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_users_order(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->get(route('orders.show', $order));
        $response->assertStatus(403);
    }
}
