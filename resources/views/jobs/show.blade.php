@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-10 space-y-6">
        <a href="{{ route('jobs.index') }}" class="text-xs text-paper-muted hover:text-paper">&larr; Back to listings</a>

        <div>
            <h1 class="text-2xl">{{ $job->title }}</h1>
            <p class="text-paper-muted text-sm mt-1">
                {{ $job->company_name }} · {{ $job->candidate_location ?? 'Location unspecified' }}
                @if ($job->salary_text) · {{ $job->salary_text }} @endif
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ($job->tags ?? [] as $tag)
                <span class="text-xs font-mono border border-ink-line px-2 py-1 text-paper-muted">{{ $tag }}</span>
            @endforeach
        </div>

        <div class="panel text-sm leading-relaxed whitespace-pre-line">
            {{ \Illuminate\Support\Str::limit($job->description, 3000) }}
        </div>

        <a href="{{ $job->apply_url }}" target="_blank" rel="noopener"
           class="inline-block border border-signal-amber text-signal-amber px-4 py-2 text-sm hover:bg-signal-amber hover:text-ink transition-colors">
            View original posting &amp; apply
        </a>
    </div>
@endsection
