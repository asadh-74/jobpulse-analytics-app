<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RemotiveClient
{
    private readonly string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?: config('services.remotive.url');
    }

    public function fetchJobs(?string $category = null, ?string $search = null): Collection
    {
        $response = Http::timeout(20)
            ->retry(2, 500)
            ->get($this->baseUrl, array_filter([
                'category' => $category,
                'search' => $search,
            ]));

        if ($response->failed()) {
            Log::warning('Remotive API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return collect();
        }

        return collect($response->json('jobs', []));
    }
}