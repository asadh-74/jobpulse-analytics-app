<?php

namespace Tests\Feature;

use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_and_shows_tracked_total(): void
    {
        JobListing::factory()->count(5)->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Postings tracked');
    }

    public function test_stats_api_returns_json_summary(): void
    {
        JobListing::factory()->count(3)->create();

        $response = $this->getJson('/api/stats');

        $response->assertOk()
            ->assertJsonStructure(['summary', 'by_category', 'top_companies', 'trending_skills']);
    }

    public function test_refresh_endpoint_rejects_bad_token(): void
    {
        $response = $this->postJson('/api/stats/refresh', ['token' => 'wrong']);

        $response->assertStatus(403);
    }
}
