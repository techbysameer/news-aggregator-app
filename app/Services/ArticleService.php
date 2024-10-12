<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
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

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters, $limit) {
            return $this->articleRepository->fetchArticles($filters, $limit);
        });
    }

    public function getArticleDetails($id)
    {
        return Cache::remember('article_' . $id, 3600, function () use ($id) {
            return $this->articleRepository->getArticleById($id);
        });
    }
}
