<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->unique()->after('payment_status');
            $table->string('stripe_payment_intent')->nullable()->unique()->after('stripe_session_id');
            $table->string('customer_name')->nullable()->after('stripe_payment_intent');
            $table->string('customer_email')->nullable()->after('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['stripe_session_id', 'stripe_payment_intent', 'customer_name', 'customer_email']);
        });
    }
};
