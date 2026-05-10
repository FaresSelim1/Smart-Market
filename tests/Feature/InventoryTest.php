<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_is_isolated_per_branch(): void
    {
        $product = Product::factory()->create();
        $branchA = Branch::factory()->create(['name' => 'Branch A']);
        $branchB = Branch::factory()->create(['name' => 'Branch B']);

        // Set different stock levels
        $product->branches()->attach($branchA->id, ['stock_level' => 50]);
        $product->branches()->attach($branchB->id, ['stock_level' => 10]);

        // Verify isolation
        $this->assertEquals(50, $product->branches()->find($branchA->id)->pivot->stock_level);
        $this->assertEquals(10, $product->branches()->find($branchB->id)->pivot->stock_level);
    }

    public function test_stock_decrement_does_not_affect_other_branches(): void
    {
        $product = Product::factory()->create();
        $branchA = Branch::factory()->create();
        $branchB = Branch::factory()->create();

        $product->branches()->attach($branchA->id, ['stock_level' => 100]);
        $product->branches()->attach($branchB->id, ['stock_level' => 50]);

        // Decrement branch A only
        \DB::table('branch_product')
            ->where('product_id', $product->id)
            ->where('branch_id', $branchA->id)
            ->decrement('stock_level', 30);

        // Branch A decremented, Branch B untouched
        $this->assertEquals(70, \DB::table('branch_product')
            ->where('product_id', $product->id)
            ->where('branch_id', $branchA->id)
            ->value('stock_level'));

        $this->assertEquals(50, \DB::table('branch_product')
            ->where('product_id', $product->id)
            ->where('branch_id', $branchB->id)
            ->value('stock_level'));
    }
}