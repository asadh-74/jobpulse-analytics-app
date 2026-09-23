<?php

namespace App\Services;

use App\Models\JobListing;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class JobAnalyticsService
{
    private const CACHE_TTL_SECONDS = 900; // 15 min — matches the fetch cadence

    public function summary(): array
    {
        return Cache::remember('jobpulse:summary', self::CACHE_TTL_SECONDS, function () {
            return [
                'total_tracked' => JobListing::count(),
                'added_last_7_days' => JobListing::where('first_seen_at', '>=', now()->subDays(7))->count(),
                'companies_hiring' => JobListing::distinct('company_name')->count('company_name'),
                'categories_covered' => JobListing::distinct('category')->count('category'),
                'last_synced_at' => JobListing::max('updated_at'),
            ];
        });
    }

    public function jobsByCategory(int $limit = 8): array
    {
        return Cache::remember("jobpulse:by-category:{$limit}", self::CACHE_TTL_SECONDS, function () use ($limit) {
            return JobListing::query()
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->limit($limit)
                ->pluck('total', 'category')
                ->toArray();
        });
    }

    public function topHiringCompanies(int $limit = 10): array
    {
        return Cache::remember("jobpulse:top-companies:{$limit}", self::CACHE_TTL_SECONDS, function () use ($limit) {
            return JobListing::query()
                ->selectRaw('company_name, count(*) as total')
                ->groupBy('company_name')
                ->orderByDesc('total')
                ->limit($limit)
                ->pluck('total', 'company_name')
                ->toArray();
        });
    }

    public function postingsPerDay(int $days = 30): array
    {
        return Cache::remember("jobpulse:postings-per-day:{$days}", self::CACHE_TTL_SECONDS, function () use ($days) {
            $rows = JobListing::query()
                ->selectRaw("date(published_at) as day, count(*) as total")
                ->where('published_at', '>=', now()->subDays($days))
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day');

            // Fill in the gaps so the line chart doesn't skip silent days.
            $series = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $series[$date] = (int) ($rows[$date] ?? 0);
            }

            return $series;
        });
    }

    /**
     * Ranks the skills/tags mentioned across active listings, and compares
     * this week's mention count against the previous week so the dashboard
     * can show which skills are actually trending up or down — not just a
     * static word cloud.
     */
    public function trendingSkills(int $limit = 15): array
    {
        return Cache::remember("jobpulse:trending-skills:{$limit}", self::CACHE_TTL_SECONDS, function () use ($limit) {
            $thisWeek = $this->tagCounts(now()->subDays(7), now());
            $lastWeek = $this->tagCounts(now()->subDays(14), now()->subDays(7));

            $ranked = collect($thisWeek)
                ->map(function ($count, $tag) use ($lastWeek) {
                    $previous = $lastWeek[$tag] ?? 0;
                    $change = $previous > 0
                        ? round((($count - $previous) / $previous) * 100)
                        : ($count > 0 ? 100 : 0);

                    return [
                        'tag' => $tag,
                        'mentions' => $count,
                        'change_percent' => $change,
                    ];
                })
                ->sortByDesc('mentions')
                ->take($limit)
                ->values();

            return $ranked->toArray();
        });
    }

    private function tagCounts(Carbon $from, Carbon $to): array
    {
        $counts = [];

        JobListing::query()
            ->whereBetween('published_at', [$from, $to])
            ->pluck('tags')
            ->each(function (?array $tags) use (&$counts) {
                foreach ($tags ?? [] as $tag) {
                    $tag = strtolower(trim($tag));
                    if ($tag === '') {
                        continue;
                    }
                    $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                }
            });

        return $counts;
    }

    public function jobTypeBreakdown(): array
    {
        return Cache::remember('jobpulse:job-types', self::CACHE_TTL_SECONDS, function () {
            return JobListing::query()
                ->selectRaw('coalesce(job_type, \'unspecified\') as job_type, count(*) as total')
                ->groupBy('job_type')
                ->pluck('total', 'job_type')
                ->toArray();
        });
    }

    public function flush(): void
    {
        foreach (['summary', 'by-category:8', 'top-companies:10', 'postings-per-day:30', 'trending-skills:15', 'job-types'] as $key) {
            Cache::forget("jobpulse:{$key}");
        }
    }
}
