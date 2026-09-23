@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-10 space-y-6">
        <h1 class="text-2xl">Browse open roles</h1>

        <form method="GET" class="flex flex-wrap gap-3">
            <input
                type="text" name="q" value="{{ request('q') }}"
                placeholder="Search title or company"
                class="bg-ink-panel border border-ink-line px-3 py-2 text-sm flex-1 min-w-[200px] focus:outline-none focus:border-signal-amber"
            >
            <select name="category" class="bg-ink-panel border border-ink-line px-3 py-2 text-sm">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="job_type" class="bg-ink-panel border border-ink-line px-3 py-2 text-sm">
                <option value="">All job types</option>
                @foreach ($jobTypes as $type)
                    <option value="{{ $type }}" @selected(request('job_type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
            <button class="border border-ink-line px-4 py-2 text-sm hover:border-signal-amber hover:text-signal-amber">
                Filter
            </button>
        </form>

        <div class="border border-ink-line divide-y divide-ink-line">
            @forelse ($jobs as $job)
                <a href="{{ route('jobs.show', $job) }}" class="flex items-center justify-between px-4 py-3 hover:bg-ink-panel">
                    <div>
                        <div class="text-sm">{{ $job->title }}</div>
                        <div class="text-xs text-paper-muted">{{ $job->company_name }} · {{ $job->candidate_location ?? 'Location unspecified' }}</div>
                    </div>
                    <div class="text-xs font-mono text-signal-teal">{{ $job->category }}</div>
                </a>
            @empty
                <p class="px-4 py-6 text-sm text-paper-muted">No postings match those filters yet.</p>
            @endforelse
        </div>

        <div>{{ $jobs->links() }}</div>
    </div>
@endsection
