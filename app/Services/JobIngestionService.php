<?php

namespace App\Services;

use App\Models\JobListing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JobIngestionService
{
    public function __construct(private readonly RemotiveClient $client) {}

    /**
     * Pull a batch of categories and upsert them by remotive_id, so re-running
     * the fetch never creates duplicates. Listings that Remotive no longer
     * returns are left alone rather than deleted, so historical trend charts
     * stay accurate even after a job closes.
     *
     * @return array{fetched:int, created:int, updated:int}
     */
    public function syncCategories(array $categories): array
    {
        $fetched = 0;
        $created = 0;
        $updated = 0;

        foreach ($categories as $category) {
            $jobs = $this->client->fetchJobs(category: $category);
            $fetched += $jobs->count();

            foreach ($jobs as $row) {
                $existing = JobListing::where('remotive_id', $row['id'])->first();

                $attributes = [
                    'title' => $row['title'] ?? 'Untitled role',
                    'company_name' => $row['company_name'] ?? 'Unknown company',
                    'company_logo_url' => $row['company_logo'] ?? null,
                    'category' => $row['category'] ?? $category,
                    'job_type' => $row['job_type'] ?? null,
                    'candidate_location' => $row['candidate_required_location'] ?? null,
                    'salary_text' => $row['salary'] ?: null,
                    'tags' => $row['tags'] ?? [],
                    'description' => strip_tags($row['description'] ?? ''),
                    'apply_url' => $row['url'] ?? '#',
                    'published_at' => Carbon::parse($row['publication_date'] ?? now()),
                    'is_active' => true,
                ];

                if ($existing) {
                    $existing->fill($attributes)->save();
                    $updated++;
                } else {
                    JobListing::create($attributes + [
                        'remotive_id' => $row['id'],
                        'first_seen_at' => now(),
                    ]);
                    $created++;
                }
            }
        }

        return compact('fetched', 'created', 'updated');
    }
}
