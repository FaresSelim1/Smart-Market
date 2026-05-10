<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_status_transitions(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        // pending → processing
        $order->transitionTo('processing');
        $this->assertEquals('processing', $order->fresh()->status);

        // processing → shipped
        $order->transitionTo('shipped');
        $this->assertEquals('shipped', $order->fresh()->status);

        // shipped → delivered
        $order->transitionTo('delivered');
        $this->assertEquals('delivered', $order->fresh()->status);
    }

    public function test_cannot_skip_status_steps(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $this->expectException(\InvalidArgumentException::class);

        // pending → shipped (should fail, must go through processing)
        $order->transitionTo('shipped');
    }

    public function test_cannot_transition_from_delivered(): void
    {
        $order = Order::factory()->create(['status' => 'delivered']);

        $this->expectException(\InvalidArgumentException::class);

        $order->transitionTo('pending');
    }

    public function test_can_cancel_from_pending(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $order->transitionTo('cancelled');
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    public function test_can_cancel_from_processing(): void
    {
        $order = Order::factory()->create(['status' => 'processing']);

        $order->transitionTo('cancelled');
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    public function test_cannot_cancel_shipped_order(): void
    {
        $order = Order::factory()->create(['status' => 'shipped']);

        $this->assertFalse($order->canTransitionTo('cancelled'));
    }

    public function test_can_transition_to_check(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $this->assertTrue($order->canTransitionTo('processing'));
        $this->assertFalse($order->canTransitionTo('shipped'));
        $this->assertFalse($order->canTransitionTo('delivered'));
        $this->assertTrue($order->canTransitionTo('cancelled'));
    }
}
