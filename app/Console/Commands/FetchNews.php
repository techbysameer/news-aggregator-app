<?php

namespace App\Console\Commands;

use App\Models\Source;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from different sources and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sources = Source::all();

        foreach ($sources as $source) {
            $url = $this->buildApiUrl($source);

            $this->fetchAndSaveNews($url, $source);
        }
    }

    private function buildApiUrl($source)
    {
        $fromDate = now()->subDay()->toDateString();
        $toDate = now()->toDateString();

        // Set API key and dynamic dates
        switch ($source->name) {
            case 'bbc_news':
                return $source->api_url . "&from={$fromDate}&to={$toDate}&apiKey=" . config('services.newsapi.key');
            case 'ny_times':
                return $source->api_url . "?api-key=" . config('services.nytimes.key');
            case 'the_guardian':
                return $source->api_url . "&from-date={$fromDate}&to-date={$toDate}&api-key=" . config('services.guardian.key');
            default:
                throw new \Exception("Unsupported source: " . $source->name);
        }
    }

    private function fetchAndSaveNews($url, $source)
    {
        $response = Http::get($url);

        if ($response->successful()) {
            // Parse the articles based on source
            $articles = $this->parseArticles($response->json(), $source->name);

            foreach ($articles as $article) {
                $this->saveArticle($article, $source->id);
            }
        }
    }

    private function parseArticles($responseData, $sourceName)
    {
        switch ($sourceName) {
            case 'bbc_news':
                return $responseData['articles'] ?? [];
            case 'ny_times':
                return $responseData['results'] ?? [];
            case 'the_guardian':
                return $responseData['response']['results'] ?? [];
            default:
                return [];
        }
    }

    private function saveArticle($article, $sourceId)
    {
        $title = $this->extractTitle($article);
        $data = [
            'description' => $this->extractDescription($article),
            'author' => $this->extractAuthor($article),
            'content' => json_encode($article),
            'url' => $this->extractUrl($article),
            'published_at' => $this->extractPublishedAt($article),
            'category' => $this->extractCategory($article),
        ];

        // Save or update the article
        Article::updateOrCreate(
            ['source_id' => $sourceId, 'title' => $title],
            $data
        );
    }

    // Article attribute extractors, based on source-specific fields
    private function extractTitle($article)
    {
        return $article['title'] ?? $article['webTitle'] ?? 'Untitled';
    }

    private function extractDescription($article)
    {
        return $article['description'] ?? $article['abstract'] ?? $article['fields']['trailText'] ?? null;
    }

    private function extractAuthor($article)
    {
        return $article['author'] ?? $article['byline'] ?? $article['fields']['byline'] ?? 'Unknown';
    }

    private function extractUrl($article)
    {
        return $article['url'] ?? $article['webUrl'] ?? '#';
    }

    private function extractPublishedAt($article)
    {
        return $article['publishedAt'] ?? $article['published_date'] ?? $article['webPublicationDate'] ?? now();
    }

    private function extractCategory($article)
    {
        return $article['category'] ?? $article['section'] ?? $article['sectionName'] ?? 'general';
    }
}
