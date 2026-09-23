<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobBoardController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = JobListing::query()
            ->active()
            ->search($request->string('q')->toString())
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->input('category')))
            ->when($request->filled('job_type'), fn ($q) => $q->where('job_type', $request->input('job_type')))
            ->orderByDesc('published_at')
            ->paginate(20)
            ->withQueryString();

        $categories = JobListing::distinct()->orderBy('category')->pluck('category');
        $jobTypes = JobListing::whereNotNull('job_type')->distinct()->orderBy('job_type')->pluck('job_type');

        return view('jobs.index', compact('jobs', 'categories', 'jobTypes'));
    }

    public function show(JobListing $job): View
    {
        return view('jobs.show', compact('job'));
    }
}
