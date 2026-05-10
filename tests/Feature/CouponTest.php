<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Services\DiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    private DiscountService $discountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discountService = new DiscountService();
    }

    public function test_valid_percent_coupon_applies_discount(): void
    {
        $coupon = Coupon::factory()->create([
            'code'  => 'SAVE10',
            'type'  => 'percent',
            'value' => 10,
            'max_uses' => 100,
            'is_active' => true,
        ]);

        $result = $this->discountService->applyCoupon('SAVE10', 200.00);

        $this->assertEquals(20.00, $result['discount']);
        $this->assertEquals(180.00, $result['total']);
    }

    public function test_valid_fixed_coupon_applies_discount(): void
    {
        Coupon::factory()->create([
            'code'  => 'FLAT25',
            'type'  => 'fixed',
            'value' => 25,
            'max_uses' => 100,
            'is_active' => true,
        ]);

        $result = $this->discountService->applyCoupon('FLAT25', 200.00);

        $this->assertEquals(25.00, $result['discount']);
        $this->assertEquals(175.00, $result['total']);
    }

    public function test_expired_coupon_is_rejected(): void
    {
        Coupon::factory()->expired()->create(['code' => 'EXPIRED']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('expired');

        $this->discountService->applyCoupon('EXPIRED', 200.00);
    }

    public function test_exhausted_coupon_is_rejected(): void
    {
        Coupon::factory()->exhausted()->create(['code' => 'MAXED']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('usage limit');

        $this->discountService->applyCoupon('MAXED', 200.00);
    }

    public function test_coupon_below_min_cart_value_is_rejected(): void
    {
        Coupon::factory()->create([
            'code'           => 'MIN100',
            'type'           => 'percent',
            'value'          => 10,
            'min_cart_value'  => 100.00,
            'is_active'      => true,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Minimum cart value');

        $this->discountService->applyCoupon('MIN100', 50.00);
    }

    public function test_coupon_usage_count_increments(): void
    {
        $coupon = Coupon::factory()->create([
            'code'       => 'COUNT',
            'used_count' => 0,
            'max_uses'   => 10,
            'is_active'  => true,
        ]);

        $this->discountService->applyCoupon('COUNT', 200.00);

        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    public function test_nonexistent_coupon_is_rejected(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not found');

        $this->discountService->applyCoupon('FAKE', 200.00);
    }

    public function test_inactive_coupon_is_rejected(): void
    {
        Coupon::factory()->create([
            'code'      => 'INACTIVE',
            'is_active' => false,
        ]);

        $this->expectException(\Exception::class);

        $this->discountService->applyCoupon('INACTIVE', 200.00);
    }
}
