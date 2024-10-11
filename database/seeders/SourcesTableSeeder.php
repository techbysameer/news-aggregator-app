<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Source::create([
            'label' => 'BBC News',
            'name' => 'bbc_news',
            'api_url' => 'https://newsapi.org/v2/everything?q=all&sortBy=popularity&sources=bbc-news'
        ]);

        Source::create([
            'label' => 'NY Times',
            'name' => 'ny_times',
            'api_url' => 'https://api.nytimes.com/svc/topstories/v2/technology.json'
        ]);

        Source::create([
            'label' => 'The Guardian',
            'name' => 'the_guardian',
            'api_url' => 'https://content.guardianapis.com/search?page-size=100&show-fields=all'
        ]);
    }
}
