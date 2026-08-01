<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SafeVoice') }} Admin Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Toast slide-in/out animation */
        @keyframes toast-in  { from { transform: translateX(110%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes toast-out { from { transform: translateX(0); opacity: 1; } to { transform: translateX(110%); opacity: 0; } }
        .toast-enter { animation: toast-in  0.3s cubic-bezier(0.21, 1.02, 0.73, 1) forwards; }
        .toast-leave { animation: toast-out 0.3s cubic-bezier(0.06, 0.71, 0.55, 1) forwards; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex flex-col sm:flex-row">

    <!-- Sidebar Navigation -->
    <aside class="w-full sm:w-64 bg-slate-900 border-r border-slate-800 shrink-0 flex flex-col">
        <!-- Brand Header -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center font-bold text-white shadow-md shadow-indigo-600/30">
                SV
            </div>
            <div>
                <span class="font-extrabold text-lg text-white">SafeVoice</span>
                <span class="block text-[10px] font-semibold uppercase text-indigo-400">Compliance Portal</span>
            </div>
        </div>

        <!-- User Info Badge -->
        <div class="p-4 border-b border-slate-800/80 bg-slate-950/40">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="truncate">
                    <span class="block text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</span>
                    <span class="block text-xs text-indigo-400 font-medium capitalize">{{ auth()->user()->role?->label() ?? 'Admin' }}</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow p-4 space-y-1">
            <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->is('admin/dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Dashboard</span>
            </a>

            <a href="/admin/reports" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->is('admin/reports*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Reports Triage</span>
            </a>

            @if(auth()->user()?->isAdmin())
                <a href="/admin/categories" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->is('admin/categories*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>Categories</span>
                </a>
            @endif

            <a href="/admin/activity-logs" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->is('admin/activity-logs*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Audit Logs</span>
            </a>

            <a href="/" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-slate-200 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Public Portal</span>
            </a>
        </nav>

        <!-- Logout Action -->
        <div class="p-4 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-rose-600/20 text-slate-300 hover:text-rose-300 text-sm font-semibold transition-all border border-slate-700/60 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-grow p-6 sm:p-10 overflow-y-auto">
        {{ $slot }}
    </main>

    <!-- ============================================================
         Toast Notification Stack
         Listens for: $this->dispatch('toast', message: '...', type: 'success'|'error'|'warning'|'info')
         ============================================================ -->
    <div
        x-data="{
            toasts: [],
            add(message, type = 'success') {
                const id = Date.now();
                this.toasts.push({ id, message, type, leaving: false });
                setTimeout(() => this.remove(id), 4000);
            },
            remove(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (!toast) return;
                toast.leaving = true;
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 350);
            }
        }"
        @toast.window="console.log('Toast Event Received:', $event.detail); add($event.detail.message, $event.detail.type ?? 'success')"
        class="fixed bottom-6 right-6 z-[9999] flex flex-col-reverse gap-3 pointer-events-none"
        aria-live="polite"
        role="status"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                :class="{
                    'toast-enter': !toast.leaving,
                    'toast-leave':  toast.leaving,
                    'border-emerald-500/30 bg-emerald-500/10 text-emerald-300': toast.type === 'success',
                    'border-rose-500/30    bg-rose-500/10    text-rose-300':    toast.type === 'error',
                    'border-amber-500/30   bg-amber-500/10   text-amber-300':   toast.type === 'warning',
                    'border-indigo-500/30  bg-indigo-500/10  text-indigo-300':  toast.type === 'info',
                }"
                class="pointer-events-auto flex items-start gap-3 min-w-[280px] max-w-sm rounded-2xl border px-4 py-3.5 shadow-2xl backdrop-blur-xl bg-slate-900/80"
            >
                <!-- Icon -->
                <span class="mt-0.5 shrink-0">
                    <!-- Success -->
                    <svg x-show="toast.type === 'success'" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <!-- Error -->
                    <svg x-show="toast.type === 'error'" class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <!-- Warning -->
                    <svg x-show="toast.type === 'warning'" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <!-- Info -->
                    <svg x-show="toast.type === 'info'" class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>

                <!-- Message -->
                <span class="text-sm font-medium leading-snug flex-1" x-text="toast.message"></span>

                <!-- Dismiss Button -->
                <button
                    type="button"
                    @click="remove(toast.id)"
                    class="shrink-0 opacity-50 hover:opacity-100 transition-opacity cursor-pointer"
                    :class="{
                        'text-emerald-300': toast.type === 'success',
                        'text-rose-300':    toast.type === 'error',
                        'text-amber-300':   toast.type === 'warning',
                        'text-indigo-300':  toast.type === 'info',
                    }"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    @livewireScripts
</body>
</html>
