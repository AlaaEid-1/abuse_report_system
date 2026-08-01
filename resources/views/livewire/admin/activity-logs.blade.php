<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">System Audit & Activity Logs</h1>
            <p class="mt-1 text-sm text-slate-400">Tamper-evident record of all system events, status changes, and administrator actions.</p>
        </div>
    </div>

    <!-- Filter Controls Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 sm:p-5 shadow-lg space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Filter User -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">User / Actor</label>
                <select wire:model.live="user_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Actors (System & Users)</option>
                    @foreach($users as $usr)
                        <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->role->label() }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Action -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Action Type</label>
                <select wire:model.live="action" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Action Types</option>
                    @foreach($distinctActions as $act)
                        <option value="{{ $act }}">{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Date From -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Date From</label>
                <input type="date" wire:model.live="date_from" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Filter Date To -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Date To</label>
                <input type="date" wire:model.live="date_to" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        @if($user_id || $action || $date_from || $date_to)
            <div class="flex justify-end pt-2 border-t border-slate-800/60">
                <button type="button" wire:click="resetFilters" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">
                    Clear Log Filters
                </button>
            </div>
        @endif
    </div>

    <!-- Logs Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">Actor / User</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Report Tracking</th>
                        <th class="px-6 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 text-slate-400 font-mono whitespace-nowrap">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($log->user)
                                    <span class="font-semibold text-white block">{{ $log->user->name }}</span>
                                    <span class="text-[10px] text-indigo-400 font-medium capitalize">{{ $log->user->role?->label() }}</span>
                                @else
                                    <span class="font-semibold text-emerald-400 italic">Anonymous Reporter</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-bold text-slate-200 bg-slate-800 px-2 py-0.5 rounded border border-slate-700">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-300 max-w-sm">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                @if($log->report)
                                    <a href="/admin/reports/{{ $log->report->id }}" class="text-indigo-400 hover:underline font-bold">
                                        {{ $log->report->tracking_code }}
                                    </a>
                                @else
                                    <span class="text-slate-600">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-500">
                                {{ $log->ip_address ?? 'Stripped (Zero-Knowledge)' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                No activity log records match your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-950/40">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
