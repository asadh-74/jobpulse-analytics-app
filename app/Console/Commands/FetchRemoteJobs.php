<?php

namespace App\Console\Commands;

use App\Services\JobAnalyticsService;
use App\Services\JobIngestionService;
use Illuminate\Console\Command;

class FetchRemoteJobs extends Command
{
    protected $signature = 'jobs:fetch
        {--categories=software-dev,devops,data,product,design : Comma-separated Remotive category slugs to pull}';

    protected $description = 'Fetch live listings from the Remotive API and refresh the analytics cache';

    public function handle(JobIngestionService $ingestion, JobAnalyticsService $analytics): int
    {
        $categories = array_filter(array_map('trim', explode(',', $this->option('categories'))));

        $this->info('Fetching: '.implode(', ', $categories));

        $result = $ingestion->syncCategories($categories);

        $analytics->flush();

        $this->table(
            ['Fetched', 'New', 'Updated'],
            [[$result['fetched'], $result['created'], $result['updated']]]
        );

        return self::SUCCESS;
    }
}
