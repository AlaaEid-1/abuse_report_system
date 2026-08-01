<div class="space-y-8 max-w-6xl mx-auto" x-data="{ modalImage: null }">
    <!-- Top Nav & Header -->
    <div>
        <a href="/admin/reports" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors mb-4">
            &larr; Back to Reports List
        </a>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded border border-indigo-500/20">{{ $report->tracking_code }}</span>
                    <span class="text-xs text-slate-400 font-medium">Category: {{ $report->category->name }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">{{ $report->title }}</h1>
            </div>

            <!-- Status Control Header Dropdown -->
            <div class="flex items-center gap-3 bg-slate-900 border border-slate-800 p-2 rounded-xl">
                <label class="text-xs font-semibold text-slate-400">Status:</label>
                <select wire:model.change="new_status" wire:change="updateStatus" class="bg-slate-950 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white font-bold focus:outline-none">
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2-Cols: Report Data & Evidence Gallery -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Report Content Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl space-y-6">
                <div>
                    <h2 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Description Payload</h2>
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                        {{ $report->description }}
                    </div>
                </div>

                <!-- Incident Location & Date -->
                <div class="grid grid-cols-2 gap-4 text-xs pt-4 border-t border-slate-800">
                    <div>
                        <span class="text-slate-500 font-medium block">Incident Date</span>
                        <span class="text-slate-200 font-semibold mt-1 block">{{ $report->incident_date ? $report->incident_date->format('M d, Y') : 'Not specified' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-medium block">Incident Location</span>
                        <span class="text-slate-200 font-semibold mt-1 block">{{ $report->incident_location ?? 'Not specified' }}</span>
                    </div>
                </div>
            </div>

            <!-- Evidence Files Attachment Section -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">Evidence Gallery & Files ({{ $report->files->count() }})</h2>
                    <span class="text-xs text-slate-500 font-semibold">Protected Private Storage</span>
                </div>

                <div class="space-y-4">
                    @forelse($report->files as $file)
                        <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            <!-- File Meta & Thumbnail -->
                            <div class="flex items-center gap-4">
                                @if($file->isImage())
                                    <!-- Image Thumbnail -->
                                    <div class="relative w-16 h-16 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 shrink-0 cursor-pointer group"
                                         @click="modalImage = { url: '{{ $file->previewUrl() }}', name: '{{ addslashes($file->original_name) }}' }">
                                        <img src="{{ $file->previewUrl() }}" alt="{{ $file->original_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                        <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                    </div>
                                @elseif($file->isPdf())
                                    <!-- PDF Icon -->
                                    <div class="w-16 h-16 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                @else
                                    <!-- Document Icon -->
                                    <div class="w-16 h-16 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                @endif

                                <div class="truncate">
                                    <span class="font-semibold text-slate-200 block text-sm truncate">{{ $file->original_name }}</span>
                                    <span class="text-xs text-slate-500 block mt-0.5">{{ number_format($file->file_size / 1024, 1) }} KB &bull; {{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION)) }}</span>
                                </div>
                            </div>

                            <!-- Actions Buttons -->
                            <div class="flex items-center gap-2 shrink-0">
                                @if($file->isImage())
                                    <button type="button" @click="modalImage = { url: '{{ $file->previewUrl() }}', name: '{{ addslashes($file->original_name) }}' }" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all cursor-pointer">
                                        Full Preview
                                    </button>
                                    <a href="{{ $file->downloadUrl() }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all">
                                        Download
                                    </a>
                                @elseif($file->isPdf())
                                    <a href="{{ $file->previewUrl() }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all">
                                        Open in Browser
                                    </a>
                                    <a href="{{ $file->downloadUrl() }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all">
                                        Download PDF
                                    </a>
                                @else
                                    <a href="{{ $file->downloadUrl() }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all">
                                        Download File
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 italic">No evidence files attached to this report.</p>
                    @endforelse
                </div>
            </div>

            <!-- Public Message Updates Thread -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl space-y-6">
                <h2 class="text-lg font-bold text-white">Public Communication Stream</h2>

                <div class="space-y-4">
                    @forelse($report->updates as $update)
                        <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl text-sm space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold {{ $update->author_type->value === 'admin' ? 'text-indigo-400' : 'text-emerald-400' }}">
                                    {{ $update->author_type->label() }} {{ $update->user ? '('.$update->user->name.')' : '' }}
                                </span>
                                <span class="text-slate-500">{{ $update->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-300 whitespace-pre-line">{{ $update->message }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 italic">No messages recorded.</p>
                    @endforelse
                </div>

                <!-- Add Public Message Form -->
                <form wire:submit.prevent="postPublicMessage" class="pt-4 border-t border-slate-800 space-y-3">
                    <label for="public_message" class="block text-xs font-semibold text-slate-400 uppercase">Post Message to Anonymous Reporter</label>
                    <textarea id="public_message" wire:model="public_message" rows="3" placeholder="Write message or questions for the reporter..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    @error('public_message') <p class="text-xs text-rose-400 font-medium">{{ $message }}</p> @enderror

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                            Post Public Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right 1-Col: Case Management Controls & Internal Notes -->
        <div class="space-y-8">
            <!-- Assignment Control Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Investigator Assignment</h3>

                <div>
                    <label class="block text-xs text-slate-400 mb-1 font-semibold">Assigned Investigator</label>
                    <select wire:model.change="assigned_admin_id" wire:change="assignInvestigator" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Unassigned --</option>
                        @foreach($investigators as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->name }} ({{ $inv->role->label() }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Internal Confidential Notes Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Internal Notes</h3>
                    <span class="text-[10px] font-bold text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20">Encrypted</span>
                </div>

                @if($report->internal_notes)
                    <!-- Active Saved Note Display -->
                    <div class="bg-slate-950/90 border border-slate-800 rounded-xl p-4 space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium pb-2 border-b border-slate-800/80">
                            <span class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Saved Note
                            </span>
                            <span>Updated {{ $report->updated_at?->diffForHumans() }}</span>
                        </div>
                        <div class="text-xs text-slate-200 leading-relaxed whitespace-pre-line font-normal">{{ $report->internal_notes }}</div>
                    </div>
                @endif

                <form wire:submit.prevent="saveInternalNotes" class="space-y-3">
                    <textarea wire:model="internal_note" rows="6" placeholder="Private internal investigation notes (never shown to reporter)..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed"></textarea>
                    @error('internal_note') <p class="text-xs text-rose-400 font-medium">{{ $message }}</p> @enderror

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all cursor-pointer">
                        Save Internal Notes
                    </button>
                </form>
            </div>

            <!-- Activity Log Stream -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Audit Log History</h3>

                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @forelse($report->activityLogs as $log)
                        <div class="text-xs border-l-2 border-indigo-500/50 pl-3 py-1 space-y-0.5">
                            <span class="block text-slate-300 font-medium">{{ $log->description }}</span>
                            <span class="block text-slate-500 text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic">No audit logs recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div x-show="modalImage"
         x-cloak
         @keydown.escape.window="modalImage = null"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md">
        <div class="relative max-w-4xl w-full bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]"
             @click.away="modalImage = null">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-800 bg-slate-950/80">
                <span class="text-sm font-bold text-white truncate" x-text="modalImage ? modalImage.name : ''"></span>
                <button type="button" @click="modalImage = null" class="text-slate-400 hover:text-white p-1">&times;</button>
            </div>
            <!-- Modal Image Body -->
            <div class="p-4 flex items-center justify-center overflow-auto flex-grow bg-slate-950">
                <img :src="modalImage ? modalImage.url : ''" class="max-h-[75vh] w-auto object-contain rounded-lg shadow-xl">
            </div>
        </div>
    </div>
</div>
