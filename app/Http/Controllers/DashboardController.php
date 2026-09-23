<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Services\JobAnalyticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(JobAnalyticsService $analytics): View
    {
        return view('dashboard', [
            'summary' => $analytics->summary(),
            'byCategory' => $analytics->jobsByCategory(),
            'topCompanies' => $analytics->topHiringCompanies(),
            'postingsPerDay' => $analytics->postingsPerDay(),
            'trendingSkills' => $analytics->trendingSkills(),
            'jobTypes' => $analytics->jobTypeBreakdown(),
            'latestJobs' => JobListing::active()->latest('published_at')->limit(16)->get(['title', 'company_name', 'category']),
        ]);
    }
}
