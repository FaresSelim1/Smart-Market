<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartStockValidationTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
    }

    /** @test */
    public function cannot_add_out_of_stock_product_to_cart()
    {
        $product = Product::factory()->create();
        // Ensure total stock is 0
        $product->branches()->detach();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This product is out of stock');

        $this->cartService->add($product->id, 1);
    }

    /** @test */
    public function cannot_add_more_than_available_stock()
    {
        $product = Product::factory()->create();
        $branch = \App\Models\Branch::factory()->create();
        $product->branches()->attach($branch->id, ['stock_level' => 5]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not enough stock available');

        $this->cartService->add($product->id, 6);
    }

    /** @test */
    public function can_add_valid_quantity_within_stock()
    {
        $product = Product::factory()->create();
        $branch = \App\Models\Branch::factory()->create();
        $product->branches()->attach($branch->id, ['stock_level' => 5]);

        $this->cartService->add($product->id, 3);

        $items = $this->cartService->getItems();
        $this->assertEquals(3, $items->firstWhere('product_id', $product->id)['quantity']);
    }

    /** @test */
    public function cannot_update_to_quantity_exceeding_stock()
    {
        $product = Product::factory()->create();
        $branch = \App\Models\Branch::factory()->create();
        $product->branches()->attach($branch->id, ['stock_level' => 5]);
        
        $this->cartService->add($product->id, 1);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not enough stock available');

        $this->cartService->updateQuantity($product->id, 10);
    }
}
