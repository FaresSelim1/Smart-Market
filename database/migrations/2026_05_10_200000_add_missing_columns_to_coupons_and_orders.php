<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add missing columns required by the SmartMarket project criteria:
 * - coupons: min_cart_value, is_active
 * - orders: discount, coupon_code
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('min_cart_value', 10, 2)->nullable()->default(0)->after('used_count');
            $table->boolean('is_active')->default(true)->after('min_cart_value');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('total_amount');
            $table->string('coupon_code')->nullable()->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['min_cart_value', 'is_active']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount', 'coupon_code']);
        });
    }
};
