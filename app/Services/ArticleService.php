<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Repositories\ArticleRepository;

class ArticleService
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getArticles($filters, $limit)
    {
        $cacheKey = 'articles_' . md5(serialize($filters)) . "_limit_{$limit}";

        try {
            return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters, $limit) {
                return $this->articleRepository->fetchArticles($filters, $limit);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error occurred while fetching articles");
        }
    }

    public function getArticleDetails($id)
    {
        try {
            return Cache::remember('article_' . $id, 3600, function () use ($id) {
                return $this->articleRepository->getArticleById($id);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error occurred while fetching article details");
        }
    }
}
