<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Compliance Dashboard</h1>
            <p class="mt-1 text-sm text-slate-400">Overview of anonymous reports, triage status, and active investigations.</p>
        </div>

        <a href="/admin/reports" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition-all">
            <span>View All Reports</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Reports -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg">
            <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Total Reports</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-white">{{ $totalReports }}</span>
                <span class="text-xs text-indigo-400 font-medium">All Time</span>
            </div>
        </div>

        <!-- Card 2: Pending Triage -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg">
            <span class="text-xs font-semibold uppercase text-amber-400 tracking-wider">Pending Triage</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-amber-400">{{ $pendingCount }}</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
            </div>
        </div>

        <!-- Card 3: Under Review -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg">
            <span class="text-xs font-semibold uppercase text-sky-400 tracking-wider">Under Review</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-sky-400">{{ $underReviewCount }}</span>
                <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
            </div>
        </div>

        <!-- Card 4: Active Investigation -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg">
            <span class="text-xs font-semibold uppercase text-purple-400 tracking-wider">Investigating</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-purple-400">{{ $investigatingCount }}</span>
                <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
            </div>
        </div>

        <!-- Card 5: Resolution Rate -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg">
            <span class="text-xs font-semibold uppercase text-emerald-400 tracking-wider">Resolution Rate</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-emerald-400">{{ $resolvedPercentage }}%</span>
                <span class="text-xs text-slate-500 font-semibold">{{ $resolvedCount }} Closed</span>
            </div>
        </div>
    </div>

    <!-- Recent Reports Feed -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-lg font-bold text-white mb-4">Recent Report Submissions</h2>

        <div class="divide-y divide-slate-800">
            @forelse($recentReports as $report)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-800/40 px-3 rounded-xl transition-all">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 font-bold">{{ $report->tracking_code }}</span>
                            <span class="text-xs text-slate-400 font-medium">{{ $report->category->name }}</span>
                        </div>
                        <a href="/admin/reports/{{ $report->id }}" class="text-sm font-semibold text-white hover:text-indigo-400 transition-colors">
                            {{ $report->title }}
                        </a>
                        <span class="block text-xs text-slate-500 mt-1">Submitted {{ $report->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $report->severity->label() }}
                        </span>
                        <a href="/admin/reports/{{ $report->id }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition-all">
                            Details &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-slate-500">No reports recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
