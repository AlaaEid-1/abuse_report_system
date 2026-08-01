<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SafeVoice - Anonymous Abuse & Misconduct Reporting Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen bg-slate-950 text-slate-100">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-emerald-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">SafeVoice</span>
                    <span class="block text-[10px] uppercase font-semibold text-emerald-400 tracking-wider">Zero-Knowledge Platform</span>
                </div>
            </a>

            <div class="flex items-center gap-4">
                <a href="/track" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Track Report</a>
                <a href="/report" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-600/30 transition-all">Submit Report</a>
                <a href="/login" class="text-xs font-semibold text-slate-400 hover:text-slate-200 px-3 py-1.5 rounded-lg border border-slate-800 hover:border-slate-700">Admin Portal</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative py-20 lg:py-32 overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-5xl mx-auto px-4 text-center space-y-8 relative z-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Complete Identity Protection Guaranteed
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight max-w-4xl mx-auto">
                Speak Up Safely & Anonymously Without Fear of Retaliation
            </h1>

            <p class="text-base sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                SafeVoice is a zero-knowledge whistleblowing platform. Report harassment, fraud, safety hazards, or policy violations without creating an account or leaving digital footprints.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/report" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow-xl shadow-indigo-600/30 transition-all text-base flex items-center justify-center gap-2">
                    <span>Submit Anonymous Report</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="/track" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-slate-200 font-semibold border border-slate-800 transition-all text-base flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Track Existing Report</span>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 border-t border-slate-800/60 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">How SafeVoice Protects You</h2>
                <p class="text-sm text-slate-400 mt-2">Designed with zero-knowledge privacy at every layer</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center font-bold text-sm mb-4">1</div>
                    <h3 class="text-base font-bold text-white mb-2">Submit Without Account</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">No email or name requested. Describe the incident and optionally attach sanitized evidence files.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center font-bold text-sm mb-4">2</div>
                    <h3 class="text-base font-bold text-white mb-2">Get Unique Code</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Receive a cryptographically derived 16-character tracking key (e.g. SV-8K9M-3P2Q-7W4X). Save it securely.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center font-bold text-sm mb-4">3</div>
                    <h3 class="text-base font-bold text-white mb-2">Anonymous Messaging</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Communicate back-and-forth with compliance investigators through your tracking portal without exposing your identity.</p>
                </div>

                <!-- Step 4 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center font-bold text-sm mb-4">4</div>
                    <h3 class="text-base font-bold text-white mb-2">Case Resolution</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Track status transitions from Pending to Investigating and final Resolution with full transparency.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-800/80 bg-slate-950 py-8 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} SafeVoice Platform. Zero IP Logging Guarantee.</p>
            <div class="flex items-center gap-4">
                <a href="/report" class="hover:text-slate-300">Submit Report</a>
                <a href="/track" class="hover:text-slate-300">Track Report</a>
                <a href="/login" class="hover:text-slate-300">Compliance Login</a>
            </div>
        </div>
    </footer>
</body>
</html>
