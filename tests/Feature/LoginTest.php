<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123!'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'admin@gmail.com',
            'password' => 'Admin@123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123!'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'admin@gmail.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}