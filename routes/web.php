<?php

use App\Http\Controllers\EvidenceController;
use App\Http\Middleware\EnsureUserHasRole;
use App\Livewire\Admin\ActivityLogs;
use App\Livewire\Admin\CategoryManagement;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ReportDetails;
use App\Livewire\Admin\ReportsTable;
use App\Livewire\AnonymousReportForm;
use App\Livewire\ReportTracking;
use Illuminate\Support\Facades\Route;

// Public Anonymous Routes (No Auth Required)
Route::get('/', function () {
    return redirect()->route('report.create');
});

Route::get('/report', AnonymousReportForm::class)
    ->name('report.create')
    ->middleware('throttle:5,1');

Route::get('/track', ReportTracking::class)
    ->name('report.track')
    ->middleware('throttle:10,1');

// Dashboard Alias Redirect
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

// Authenticated Admin Compliance Routes
Route::middleware([
    'auth',
    'verified',
    EnsureUserHasRole::class.':admin,super_admin,investigator',
])->prefix('admin')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/reports', ReportsTable::class)->name('admin.reports.index');
    Route::get('/reports/{report}', ReportDetails::class)->name('admin.reports.show');

    // Secure Evidence Management Routes
    Route::get('/evidence/{file}/preview', [EvidenceController::class, 'preview'])->name('admin.evidence.preview');
    Route::get('/evidence/{file}/download', [EvidenceController::class, 'download'])->name('admin.evidence.download');

    Route::get('/activity-logs', ActivityLogs::class)->name('admin.activity-logs.index');

    Route::middleware([EnsureUserHasRole::class.':admin,super_admin'])->group(function () {
        Route::get('/categories', CategoryManagement::class)->name('admin.categories.index');
    });
});
