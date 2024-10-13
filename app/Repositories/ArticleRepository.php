<?php

namespace App\Repositories;

use App\Models\Article;

class ArticleRepository
{
    public function fetchArticles($filters, $limit)
    {
        $query = Article::with('source');

        // Apply filters if provided
        if (!empty($filters['keyword'])) {
            // Use search_vector for full-text search
            $query->whereRaw("search_vector @@ plainto_tsquery(?)", [$filters['keyword']]);
        }

        if (!empty(($filters['start_date']) && !empty($filters['end_date']))) {
            $query->whereBetween('published_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['source'])) {
            $query->whereHas('source', function ($q) use ($filters) {
                $q->where('name', $filters['source']);
            });
        }

        // Paginate results with limit
        return $query->paginate($limit ?? 10);
    }

    public function getArticleById($id)
    {
        return Article::with('source')->findOrFail($id);
    }

    public function getArticlesBasedOnPreferences($preferences)
    {
        $query = Article::query();

        if (!empty($preferences->sources)) {
            $query->whereIn('source_id', $preferences->sources);
        }

        if (!empty($preferences->categories)) {
            $query->orWhereIn('category', $preferences->categories);
        }

        if (!empty($preferences->authors)) {
            $query->orWhereIn('author', $preferences->authors);
        }

        return $query->paginate(10);
    }
}
