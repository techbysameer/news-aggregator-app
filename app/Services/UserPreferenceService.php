<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ArticleRepository;
use App\Repositories\UserPreferenceRepository;

class UserPreferenceService
{
    protected $repository;
    protected $articleRepository;

    public function __construct(UserPreferenceRepository $repository, ArticleRepository $articleRepository)
    {
        $this->repository = $repository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Set user preferences
     */
    public function setUserPreferences($userId, array $data)
    {
        try {
            $action = $data['action'] ?? 'set'; // Default action is 'set'

            // Fetch existing preferences
            $existingPreferences = $this->getUserPreferences($userId);

            $newSources = $data['sources'] ?? [];
            $newCategories = $data['categories'] ?? [];
            $newAuthors = $data['authors'] ?? [];

            // Modify preferences based on the action
            if ($existingPreferences) {
                switch ($action) {
                    case 'add':
                        // Append new preferences and avoid duplicates
                        $newSources = array_unique(array_merge($existingPreferences->sources ?? [], $newSources));
                        $newCategories = array_unique(array_merge($existingPreferences->categories ?? [], $newCategories));
                        $newAuthors = array_unique(array_merge($existingPreferences->authors ?? [], $newAuthors));
                        break;

                    case 'remove':
                        // Remove specified preferences from the existing ones
                        $newSources = array_diff($existingPreferences->sources ?? [], $newSources);
                        $newCategories = array_diff($existingPreferences->categories ?? [], $newCategories);
                        $newAuthors = array_diff($existingPreferences->authors ?? [], $newAuthors);
                        break;

                    case 'set':
                    default:
                        // Overwrite with new Preferences
                        break;
                }
            }

            // Cache invalidation for personalized feed
            Cache::forget("user_{$userId}_personalized_news_feed");

            return $this->repository->setUserPreferences($userId, [
                'sources' => $newSources,
                'categories' => $newCategories,
                'authors' => $newAuthors,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to set preferences for user {$userId}: {$e->getMessage()}");
            throw new \Exception('Failed to set user preferences. Please try again later.');
        }
    }

    /**
     * Get user preferences
     */
    public function getUserPreferences($userId)
    {
        try {
            return $this->repository->getUserPreferences($userId);
        } catch (\Exception $e) {
            Log::error("Failed to fetch preferences for user {$userId}: {$e->getMessage()}");
            throw new \Exception('Failed to retrieve user preferences. Please try again later.');
        }
    }

    /**
     * Get personalized articles based on user preferences
     */
    public function getPersonalizedArticles($userId)
    {
        try {
            $cacheKey = "user_{$userId}_personalized_news_feed";

            return Cache::remember($cacheKey, 600, function () use ($userId) {
                $preferences = $this->getUserPreferences($userId);

                if (!$preferences) {
                    return []; // Return an empty result if no preferences are set
                }

                return $this->articleRepository->getArticlesBasedOnPreferences($preferences);
            });
        } catch (\Exception $e) {
            Log::error("Failed to fetch personalized articles for user {$userId}: {$e->getMessage()}");
            throw new \Exception('Failed to retrieve personalized feed. Please try again later.');
        }
    }
}
