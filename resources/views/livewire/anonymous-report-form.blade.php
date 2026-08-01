<div class="max-w-4xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <!-- Header Banner -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Zero-Knowledge Anonymous Portal
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Submit Anonymous Abuse Report</h1>
        <p class="mt-2 text-sm text-slate-400 max-w-xl mx-auto">
            Your identity, IP address, and browser data are never stored. Complete this form to submit your report securely.
        </p>
    </div>

    <!-- Stepper Navigation -->
    @if($currentStep < 3)
        <div class="mb-8">
            <div class="flex items-center justify-between max-w-xs mx-auto">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $currentStep >= 1 ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30 ring-4 ring-indigo-500/20' : 'bg-slate-800 text-slate-400' }}">
                        1
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-2">Details</span>
                </div>
                <div class="flex-1 h-0.5 mx-3 {{ $currentStep >= 2 ? 'bg-indigo-600' : 'bg-slate-800' }}"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $currentStep >= 2 ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30 ring-4 ring-indigo-500/20' : 'bg-slate-800 text-slate-400' }}">
                        2
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-2">Evidence</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Container -->
    <div class="bg-slate-950/80 border border-slate-800/80 rounded-2xl p-6 sm:p-10 shadow-2xl backdrop-blur-xl">
        
        <!-- STEP 1: Category & Details -->
        @if($currentStep === 1)
            <form wire:submit.prevent="validateStep1" class="space-y-6">
                <!-- Category Select -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-200 mb-2">
                        Abuse Category <span class="text-rose-400">*</span>
                    </label>
                    <select id="category_id" wire:model="category_id" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Report Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-200 mb-2">
                        Report Title <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" id="title" wire:model="title" placeholder="Brief summary of the incident..." class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    @error('title') <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Report Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-200 mb-2">
                        Detailed Description <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="description" wire:model="description" rows="6" placeholder="Provide factual details including what happened, timeline, involved parties (if comfortable)..." class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all leading-relaxed"></textarea>
                    @error('description') <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Step 1 Actions -->
                <div class="pt-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition-all cursor-pointer">
                        <span>Continue to Evidence</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        @endif

        <!-- STEP 2: Evidence & Severity -->
        @if($currentStep === 2)
            <form wire:submit.prevent="submit" class="space-y-6">
                <!-- Incident Date & Location (Optional) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="incident_date" class="block text-sm font-semibold text-slate-200 mb-2">Incident Date (Optional)</label>
                        <input type="date" id="incident_date" wire:model="incident_date" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('incident_date') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="incident_location" class="block text-sm font-semibold text-slate-200 mb-2">Location / Dept (Optional)</label>
                        <input type="text" id="incident_location" wire:model="incident_location" placeholder="e.g. Building A, 2nd floor" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('incident_location') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Severity Picker -->
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-2">Severity Level <span class="text-rose-400">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $val => $label)
                            <label class="relative flex items-center justify-center p-3 rounded-xl border text-xs font-semibold cursor-pointer transition-all {{ $severity === $val ? 'bg-indigo-600/20 border-indigo-500 text-indigo-300 ring-2 ring-indigo-500/30' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700' }}">
                                <input type="radio" wire:model.live="severity" value="{{ $val }}" class="sr-only">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Evidence File Upload -->
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-2">Evidence Files (Optional)</label>
                    <div class="relative border-2 border-dashed border-slate-800 hover:border-slate-700 rounded-xl p-6 text-center bg-slate-900/50 transition-all">
                        <input type="file" wire:model="files" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-8 h-8 text-slate-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm font-medium text-slate-300">Drag & drop files or click to upload</p>
                            <p class="text-xs text-slate-500 mt-1">Allowed: PDF, JPG, PNG, WEBP, DOCX, TXT (Max 10MB each)</p>
                        </div>
                    </div>
                    @error('files.*') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror

                    <!-- Selected Files List -->
                    @if(count($files) > 0)
                        <div class="mt-4 space-y-2">
                            <p class="text-xs font-semibold text-slate-400">Attached Files:</p>
                            @foreach($files as $index => $file)
                                <div class="flex items-center justify-between bg-slate-900 border border-slate-800 px-3 py-2 rounded-lg text-xs">
                                    <span class="text-slate-300 truncate">{{ $file->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeFile({{ $index }})" class="text-rose-400 hover:text-rose-300 font-semibold ml-2">Remove</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Step 2 Actions -->
                <div class="pt-4 flex items-center justify-between">
                    <button type="button" wire:click="prevStep" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition-all">
                        Back
                    </button>

                    <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-lg shadow-emerald-600/30 transition-all cursor-pointer">
                        <span wire:loading.remove>Submit Report Anonymously</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </form>
        @endif

        <!-- STEP 3: Success Screen with Tracking Code -->
        @if($currentStep === 3)
            <div class="text-center py-6 space-y-6">
                <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-2xl flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-white">Report Submitted Successfully</h2>
                    <p class="text-sm text-slate-400 mt-1 max-w-md mx-auto">
                        Your report has been encrypted and assigned to the investigation team. Save your unique tracking code below to check report status and receive updates.
                    </p>
                </div>

                <!-- Tracking Code Box -->
                <div class="bg-slate-900 border-2 border-emerald-500/40 rounded-2xl p-6 max-w-md mx-auto shadow-inner">
                    <span class="block text-xs uppercase font-bold text-emerald-400 tracking-wider mb-2">Your Anonymous Tracking Code</span>
                    <div class="text-2xl sm:text-3xl font-mono font-extrabold text-white tracking-widest select-all py-2 bg-slate-950/80 rounded-xl border border-slate-800">
                        {{ $generatedTrackingCode }}
                    </div>
                    <p class="text-xs text-slate-500 mt-3">
                        ⚠️ <span class="text-slate-300 font-semibold">Important:</span> Copy or record this code safely. Because no personal data is stored, this code is your ONLY way to access your report.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/track?code={{ $generatedTrackingCode }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition-all">
                        Track This Report Now
                    </a>
                    <button type="button" wire:click="resetForm" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition-all">
                        Submit Another Report
                    </button>
                </div>
            </div>
        @endif

    </div>
</div>
