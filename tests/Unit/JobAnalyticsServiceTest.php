<?php

namespace Tests\Unit;

use App\Models\JobListing;
use App\Services\JobAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_counts_reflect_stored_listings(): void
    {
        JobListing::factory()->count(3)->create(['company_name' => 'Acme Co']);
        JobListing::factory()->count(2)->create(['company_name' => 'Globex']);

        $summary = app(JobAnalyticsService::class)->summary();

        $this->assertSame(5, $summary['total_tracked']);
        $this->assertSame(2, $summary['companies_hiring']);
    }

    public function test_trending_skills_flags_a_skill_mentioned_only_this_week(): void
    {
        JobListing::factory()->create([
            'tags' => ['rust'],
            'published_at' => now()->subDays(1),
        ]);

        $skills = app(JobAnalyticsService::class)->trendingSkills();

        $rust = collect($skills)->firstWhere('tag', 'rust');

        $this->assertNotNull($rust);
        $this->assertSame(100, $rust['change_percent']);
    }

    public function test_jobs_by_category_groups_and_orders_by_volume(): void
    {
        JobListing::factory()->count(4)->create(['category' => 'software-dev']);
        JobListing::factory()->count(1)->create(['category' => 'design']);

        $byCategory = app(JobAnalyticsService::class)->jobsByCategory();

        $this->assertSame(4, $byCategory['software-dev']);
        $this->assertSame('software-dev', array_key_first($byCategory));
    }
}
