@extends('layouts.app')

@section('content')
    {{-- Hero: a live ticker of real, currently-open postings --}}
    <div class="border-b border-ink-line bg-ink-panel overflow-hidden py-3">
        <div class="ticker-track font-mono text-sm whitespace-nowrap">
            @for ($i = 0; $i < 2; $i++)
                @foreach ($latestJobs as $job)
                    <span class="px-6 flex items-center gap-2">
                        <span class="text-signal-teal">{{ $job->category }}</span>
                        <span class="text-paper">{{ $job->title }}</span>
                        <span class="text-paper-muted">— {{ $job->company_name }}</span>
                    </span>
                @endforeach
            @endfor
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-10 space-y-10">
        <div class="flex items-end justify-between">
            <div>
                <h1 class="text-2xl">Remote job market, right now</h1>
                <p class="text-paper-muted text-sm mt-1">
                    Every number below is computed from postings currently tracked from the Remotive job board —
                    no seed data, no placeholders.
                </p>
            </div>
            <button id="refresh-data" class="text-xs border border-ink-line px-3 py-2 hover:border-signal-amber hover:text-signal-amber transition-colors">
                Refresh data
            </button>
        </div>

        {{-- Stat row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-ink-line border border-ink-line">
            <div class="stat-block bg-ink border-0">
                <span class="font-mono text-3xl text-signal-amber">{{ number_format($summary['total_tracked']) }}</span>
                <span class="text-xs text-paper-muted">Postings tracked</span>
            </div>
            <div class="stat-block bg-ink border-0">
                <span class="font-mono text-3xl text-signal-teal">{{ number_format($summary['added_last_7_days']) }}</span>
                <span class="text-xs text-paper-muted">New in the last 7 days</span>
            </div>
            <div class="stat-block bg-ink border-0">
                <span class="font-mono text-3xl">{{ number_format($summary['companies_hiring']) }}</span>
                <span class="text-xs text-paper-muted">Companies hiring</span>
            </div>
            <div class="stat-block bg-ink border-0">
                <span class="font-mono text-3xl">{{ number_format($summary['categories_covered']) }}</span>
                <span class="text-xs text-paper-muted">Categories covered</span>
            </div>
        </div>

        {{-- Postings over time + category split --}}
        <div class="grid md:grid-cols-3 gap-6">
            <div class="panel md:col-span-2">
                <h2 class="text-sm text-paper-muted mb-4">Postings published per day — last 30 days</h2>
                <canvas
                    height="90"
                    data-chart-type="line"
                    data-chart='@json(["labels" => array_keys($postingsPerDay), "values" => array_values($postingsPerDay), "label" => "Postings"])'
                ></canvas>
            </div>
            <div class="panel">
                <h2 class="text-sm text-paper-muted mb-4">Jobs by category</h2>
                <canvas
                    data-chart-type="doughnut"
                    data-chart='@json(["labels" => array_keys($byCategory), "values" => array_values($byCategory)])'
                ></canvas>
            </div>
        </div>

        {{-- Top companies + trending skills --}}
        <div class="grid md:grid-cols-2 gap-6">
            <div class="panel">
                <h2 class="text-sm text-paper-muted mb-4">Companies with the most open roles</h2>
                <canvas
                    height="220"
                    data-chart-type="bar"
                    data-chart='@json(["labels" => array_keys($topCompanies), "values" => array_values($topCompanies)])'
                ></canvas>
            </div>
            <div class="panel">
                <h2 class="text-sm text-paper-muted mb-4">Trending skills — this week vs. last week</h2>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-ink-line">
                        @foreach ($trendingSkills as $row)
                            <tr>
                                <td class="py-2 font-mono">{{ $row['tag'] }}</td>
                                <td class="py-2 text-right text-paper-muted">{{ $row['mentions'] }} mentions</td>
                                <td class="py-2 pl-3 text-right font-mono {{ $row['change_percent'] >= 0 ? 'text-signal-teal' : 'text-signal-rose' }}">
                                    {{ $row['change_percent'] >= 0 ? '+' : '' }}{{ $row['change_percent'] }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-xs text-paper-muted">
            Last synced:
            {{ $summary['last_synced_at'] ? \Illuminate\Support\Carbon::parse($summary['last_synced_at'])->diffForHumans() : 'never — run php artisan jobs:fetch' }}
        </div>
    </div>
@endsection
