<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('sku')->unique();
            $table->string('image_path')->nullable(); // Added for the UI
            $table->timestamps();
        });
        
        Schema::create('branch_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('stock_level')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->timestamps();

            $table->unique(['branch_id', 'product_id']);
        });
    }

    public function down(): void
    {
        // Drop pivot first to avoid foreign key constraints
        Schema::dropIfExists('branch_product');
        Schema::dropIfExists('products');
    }
};