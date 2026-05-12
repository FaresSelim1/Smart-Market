<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_access_profile_page()
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_access_profile_page()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'user'
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
        $response->assertSee('User');
    }

    /** @test */
    public function profile_link_is_visible_in_navbar_when_authenticated()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertSee('Profile');
        $response->assertSee(route('profile'));
    }

    /** @test */
    public function profile_link_is_not_visible_in_navbar_when_guest()
    {
        $response = $this->get('/');

        $response->assertDontSee('Profile');
    }
}
