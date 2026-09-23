<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobAnalyticsService;
use App\Services\JobIngestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(JobAnalyticsService $analytics): JsonResponse
    {
        return response()->json([
            'summary' => $analytics->summary(),
            'by_category' => $analytics->jobsByCategory(),
            'top_companies' => $analytics->topHiringCompanies(),
            'trending_skills' => $analytics->trendingSkills(),
        ]);
    }

    /**
     * Lets a demo visitor (or you, from the dashboard) trigger a live refresh
     * without shell access, gated by a shared token so it can't be spammed.
     */
    public function refresh(Request $request, JobIngestionService $ingestion, JobAnalyticsService $analytics): JsonResponse
    {
        $request->validate(['token' => 'required|string']);

        if (! hash_equals((string) env('ADMIN_REFRESH_TOKEN', ''), $request->input('token'))) {
            return response()->json(['message' => 'Invalid token.'], 403);
        }

        $result = $ingestion->syncCategories(['software-dev', 'devops', 'data', 'product', 'design']);
        $analytics->flush();

        return response()->json($result);
    }
}
