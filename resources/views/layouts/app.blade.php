<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'JobPulse — remote job market analytics' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <header class="border-b border-ink-line">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="font-display text-lg tracking-tight">
                JobPulse
            </a>
            <nav class="flex items-center gap-6 text-sm text-paper-muted">
                <a href="{{ route('dashboard') }}" class="hover:text-paper">Dashboard</a>
                <a href="{{ route('jobs.index') }}" class="hover:text-paper">Browse jobs</a>
                <a href="/api/stats" class="hover:text-paper font-mono text-xs">/api/stats</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-ink-line mt-16">
        <div class="max-w-6xl mx-auto px-6 py-6 text-xs text-paper-muted flex justify-between">
            <span>Live data from the Remotive public job API — refreshed automatically every 6 hours.</span>
            <span>Built with Laravel by Asad Hussain</span>
        </div>
    </footer>
</body>
</html>
