<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Services\UserPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPreferencesTest extends TestCase
{
    use RefreshDatabase;

    protected $userPreferenceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userPreferenceService = Mockery::mock(UserPreferenceService::class);
        $this->app->instance(UserPreferenceService::class, $this->userPreferenceService);
    }

    /**
     * Test user can set preferences successfully
     */
    public function test_user_can_set_preferences_successfully()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Mock the preferences data
        $preferencesData = [
            'action' => 'set',
            'sources' => ['Source 1', 'Source 2'],
            'categories' => ['Category 1'],
            'authors' => ['Author X'],
        ];

        // Mocking the service method
        $this->userPreferenceService->shouldReceive('setUserPreferences')
            ->once()
            ->with($user->id, $preferencesData)
            ->andReturn($preferencesData);

        // Make the authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/preferences', $preferencesData);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => $preferencesData,
                'message' => 'Preferences saved successfully.',
            ]);
    }

    /**
     * Test user can retrieve preferences successfully
     */
    public function test_user_can_retrieve_preferences_successfully()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Mock the existing preferences
        $mockPreferences = [
            'sources' => ['Source 1', 'Source 2'],
            'categories' => ['Category 1'],
            'authors' => ['Author X'],
        ];

        // Mocking the service method
        $this->userPreferenceService->shouldReceive('getUserPreferences')
            ->once()
            ->with($user->id)
            ->andReturn((object) $mockPreferences);

        // Make the authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/preferences');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => $mockPreferences,
                'message' => 'User Preferences retrieved successfully.',
            ]);
    }
}
