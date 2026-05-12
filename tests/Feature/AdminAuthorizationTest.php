<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_access_admin_panel()
    {
        $response = $this->get('/admin');

        // Filament usually redirects to login for guests
        $response->assertRedirect('/admin/login');
    }

    /** @test */
    public function regular_user_cannot_access_admin_panel()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_admin_panel()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        // 200 or 302 (redirect to dashboard)
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}
