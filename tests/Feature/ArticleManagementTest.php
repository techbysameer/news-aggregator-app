<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;
    protected $articleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleService = Mockery::mock(ArticleService::class);
        $this->app->instance(ArticleService::class, $this->articleService);
    }

    /**
     * Test user can fetch articles successfully
     */
    public function test_user_can_fetch_articles_successfully()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Define filters and limit
        $filters = [
            'keyword' => 'test',
            'start_date' => '2024-10-01',
            'end_date' => '2024-10-12',
            'category' => 'news',
            'source' => '1',
        ];
        $limit = 10;

        // Mocking the service method
        $this->articleService->shouldReceive('getArticles')
            ->once()
            ->with($filters, $limit)
            ->andReturn(['article1', 'article2']); // Sample articles

        // Make the authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/articles?limit=' . $limit . '&keyword=' . $filters['keyword'] . '&start_date=' . $filters['start_date'] . '&end_date=' . $filters['end_date'] . '&category=' . $filters['category'] . '&source=' . $filters['source']);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => ['article1', 'article2'],
                'message' => 'Articles Fetched successfully.'
            ]);
    }
    /**
     * Test user can fetch article details successfully
     */
    public function test_user_can_fetch_article_details_successfully()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Mock the article details
        $articleId = 1;
        $mockArticle = ['id' => $articleId, 'title' => 'Test Article', 'content' => 'Content of the article'];

        // Mocking the service method
        $this->articleService->shouldReceive('getArticleDetails')
            ->once()
            ->with($articleId)
            ->andReturn($mockArticle);

        // Make the authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/articles/{$articleId}");

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => $mockArticle,
                'message' => 'Article Details Fetched.'
            ]);
    }

}
