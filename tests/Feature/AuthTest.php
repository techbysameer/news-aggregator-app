<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Notifications\ResetPasswordCustom;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test user registration
     */
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200) // Check for success status
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully.'
            ]);

        // Ensure the user was created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    /**
     * Test user login
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'testlogin@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token', 'name']
            ]);
    }
    /**
     * Test login with invalid credentials
     */
    public function test_login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'wronglogin@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wronglogin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401) // Unauthorized status
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized.',
            ]);
    }
    /**
     * Test user logout
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Make authenticated request to logout
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully.'
            ]);
    }
    /**
     * Test user can forgot password
     */
    public function test_user_can_request_password_reset_link()
    {
        // Fake the notifications
        Notification::fake();

        // Create user
        $user = User::factory()->create();

        // Make request for reset password link
        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Assert the reset link notification was sent
        Notification::assertSentTo(
            [$user],
            ResetPasswordCustom::class
        );
    }
    /**
     * Test user can reset password
     */
    public function test_user_can_reset_password()
    {
        // Simulate user and password reset request
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // Make reset password request
        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Assert password was updated
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
