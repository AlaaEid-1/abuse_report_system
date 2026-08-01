<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Reports Triage Table</h1>
            <p class="mt-1 text-sm text-slate-400">Search, filter, and review incoming anonymous reports.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 sm:p-5 shadow-lg space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Input -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title or SV-XXXX..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Status</label>
                <select wire:model.live="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Category</label>
                <select wire:model.live="category_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Severity Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Severity</label>
                <select wire:model.live="severity" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Severities</option>
                    @foreach($severities as $sev)
                        <option value="{{ $sev->value }}">{{ $sev->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($search || $status || $category_id || $severity)
            <div class="flex justify-end pt-2 border-t border-slate-800/60">
                <button type="button" wire:click="resetFilters" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">
                    Clear Filters
                </button>
            </div>
        @endif
    </div>

    <!-- Reports Data Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Tracking Code</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Severity</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Submitted</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-400">
                                {{ $report->tracking_code }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-300">
                                {{ $report->category->name }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-white max-w-xs truncate">
                                {{ $report->title }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-800 border border-slate-700 text-slate-300">
                                    {{ $report->severity->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
                                    {{ $report->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400 whitespace-nowrap">
                                {{ $report->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="/admin/reports/{{ $report->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                                No reports match the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-950/40">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
