<div class="max-w-4xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <!-- Header Banner -->
    <div class="text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Track Report Status</h1>
        <p class="mt-2 text-sm text-slate-400 max-w-xl mx-auto">
            Enter your unique 16-character tracking code to check status updates and communicate with investigators.
        </p>
    </div>

    <!-- Lookup Form -->
    <div class="bg-slate-950/80 border border-slate-800/80 rounded-2xl p-6 mb-8 shadow-xl">
        <form wire:submit.prevent="track" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" wire:model="tracking_code" placeholder="Enter code e.g. SV-8K9M-3P2Q-7W4X" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 text-sm font-mono uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('tracking_code') <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition-all cursor-pointer">
                Track Report
            </button>
        </form>
    </div>

    <!-- Error Message -->
    @if($errorMessage)
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-300 p-4 rounded-xl text-sm mb-8 flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    <!-- Report Details Container -->
    @if($report)
        <div class="space-y-6">
            <!-- Flash Session Status -->
            @if (session()->has('status'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 p-4 rounded-xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Report Status Card -->
            <div class="bg-slate-950/80 border border-slate-800/80 rounded-2xl p-6 sm:p-8 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Tracking Code:</span>
                            <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20">{{ $report->tracking_code }}</span>
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ $report->title }}</h2>
                    </div>

                    <!-- Status Badge -->
                    <div class="shrink-0">
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-500/10 border-amber-500/30 text-amber-400',
                                'under_review' => 'bg-sky-500/10 border-sky-500/30 text-sky-400',
                                'investigating' => 'bg-purple-500/10 border-purple-500/30 text-purple-400',
                                'resolved' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
                                'rejected' => 'bg-rose-500/10 border-rose-500/30 text-rose-400',
                            ];
                            $colorClass = $statusColors[$report->status->value] ?? 'bg-slate-800 text-slate-300';
                        @endphp
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-bold uppercase tracking-wider {{ $colorClass }}">
                            <span class="w-2 h-2 rounded-full bg-current"></span>
                            {{ $report->status->label() }}
                        </span>
                    </div>
                </div>

                <!-- Metadata Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-slate-800 text-xs">
                    <div>
                        <span class="block text-slate-500 font-medium">Category</span>
                        <span class="font-semibold text-slate-200 mt-1 block">{{ $report->category->name }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 font-medium">Severity</span>
                        <span class="font-semibold text-slate-200 mt-1 block uppercase">{{ $report->severity->label() }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 font-medium">Submitted Date</span>
                        <span class="font-semibold text-slate-200 mt-1 block">{{ $report->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 font-medium">Incident Location</span>
                        <span class="font-semibold text-slate-200 mt-1 block">{{ $report->incident_location ?? 'Not specified' }}</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="pt-6">
                    <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Report Content</h3>
                    <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4 text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                        {{ $report->description }}
                    </div>
                </div>
            </div>

            <!-- Public Status Updates Timeline -->
            <div class="bg-slate-950/80 border border-slate-800/80 rounded-2xl p-6 sm:p-8 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6">Investigation Timeline & Messages</h3>

                <div class="space-y-6">
                    @forelse($report->publicUpdates as $update)
                        <div class="flex gap-4 {{ $update->author_type->value === 'reporter' ? 'flex-row-reverse' : '' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $update->author_type->value === 'reporter' ? 'bg-emerald-600 text-white' : 'bg-indigo-600 text-white' }}">
                                {{ $update->author_type->value === 'reporter' ? 'You' : 'Admin' }}
                            </div>
                            <div class="flex-1 max-w-2xl bg-slate-900 border border-slate-800 rounded-2xl p-4 text-sm">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-semibold {{ $update->author_type->value === 'reporter' ? 'text-emerald-400' : 'text-indigo-400' }}">
                                        {{ $update->author_type->label() }}
                                    </span>
                                    <span>{{ $update->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-slate-300 leading-relaxed whitespace-pre-line">{{ $update->message }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 italic">No timeline updates recorded yet.</p>
                    @endforelse
                </div>

                <!-- Add Reporter Reply Form -->
                <form wire:submit.prevent="sendReply" class="mt-8 pt-6 border-t border-slate-800">
                    <label for="message_body" class="block text-sm font-semibold text-slate-200 mb-2">Send Anonymous Reply to Investigator</label>
                    <textarea id="message_body" wire:model="message_body" rows="3" placeholder="Type a message or answer investigator questions..." class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    @error('message_body') <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p> @enderror

                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-lg shadow-emerald-600/30 transition-all cursor-pointer">
                            Post Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
