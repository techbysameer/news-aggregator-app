<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ArticleService;
use Illuminate\Support\Facades\Log;

class ArticleController extends BaseController
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10); // Default to 10
            $filters = [
                'keyword' => $request->get('keyword'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'category' => $request->get('category'),
                'source' => $request->get('source'),
            ];

            $articles = $this->articleService->getArticles($filters, $limit);
            return $this->sendResponse(
                $articles,
                'Articles Fetched successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Error fetching articles: ' . $e->getMessage());
            return $this->sendError(
                'Error fetching articles. Please try again later.',
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show($id)
    {
        try {
            $article = $this->articleService->getArticleDetails($id);

            if (!$article) {
                return $this->sendError(
                    'Article not found.',
                    [],
                    Response::HTTP_NOT_FOUND
                );
            }
            return $this->sendResponse(
                $article,
                'Article Details Fetched.'
            );
        } catch (\Exception $e) {
            Log::error('Error fetching article details: ' . $e->getMessage());
            return $this->sendError(
                'Error fetching article detail. Please try again later.',
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
