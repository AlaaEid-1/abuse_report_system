<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Compliance Admin Login - SafeVoice</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo -->
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-emerald-500 flex items-center justify-center shadow-xl shadow-indigo-500/30 mx-auto mb-3">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-white tracking-tight">SafeVoice Portal</h2>
        <p class="mt-2 text-xs font-semibold text-indigo-400 uppercase tracking-widest">Compliance & Investigation Portal</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-slate-900 border border-slate-800 py-8 px-4 shadow-2xl rounded-2xl sm:px-10">
            
            <!-- Global Session Status -->
            @if (session('status'))
                <div class="mb-4 text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Validation Errors Summary -->
            @if ($errors->any())
                <div class="mb-4 bg-rose-500/10 border border-rose-500/20 text-rose-300 p-3 rounded-xl text-xs space-y-1">
                    <span class="font-bold block">Authentication Failed:</span>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase text-slate-300 mb-2">
                        Official Email Address
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase text-slate-300 mb-2">
                        Password
                    </label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember_me" class="ml-2 block text-xs font-medium text-slate-400">
                            Remember workstation session
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl shadow-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-semibold text-white shadow-indigo-600/30 transition-all cursor-pointer">
                        Authenticate & Access Portal
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-800/80 text-center">
                <a href="/report" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">
                    &larr; Return to Public Anonymous Reporting Portal
                </a>
            </div>
        </div>
    </div>
</body>
</html>
