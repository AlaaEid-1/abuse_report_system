<?php

use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Livewire\Admin\ActivityLogs;
use App\Livewire\Admin\ReportDetails;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use App\Notifications\NewReportSubmittedNotification;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create([
        'name' => 'Financial Fraud',
        'is_active' => true,
    ]);

    $this->admin = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@safevoice.org',
        'role' => UserRole::ADMIN,
    ]);
});

test('notifications are dispatched to admins when a new report is submitted', function () {
    Notification::fake();

    $service = app(ReportService::class);
    $service->createReport([
        'category_id' => $this->category->id,
        'title' => 'Test Notification Trigger Report',
        'description' => 'Detailed description for testing notifications dispatch.',
        'severity' => ReportSeverity::HIGH,
    ]);

    Notification::assertSentTo(
        [$this->admin],
        NewReportSubmittedNotification::class
    );
});

test('activity logs Livewire component displays audit logs and supports filtering', function () {
    ActivityLog::create([
        'user_id' => $this->admin->id,
        'action' => 'test.action',
        'description' => 'Custom test activity log item.',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ActivityLogs::class)
        ->set('action', 'test.action')
        ->assertSee('Custom test activity log item.');
});

test('complete end to end report lifecycle from submission to resolution', function () {
    // 1. Reporter submits report anonymously
    $service = app(ReportService::class);
    $result = $service->createReport([
        'category_id' => $this->category->id,
        'title' => 'E2E Lifecycle Test Report',
        'description' => 'Description of end to end lifecycle report testing.',
        'severity' => ReportSeverity::HIGH,
    ]);

    $trackingCode = $result['tracking_code'];
    $report = $result['report'];

    expect($report->status)->toBe(ReportStatus::PENDING);

    // 2. Admin inspects & changes status to Investigating
    Livewire::actingAs($this->admin)
        ->test(ReportDetails::class, ['report' => $report])
        ->set('new_status', 'investigating')
        ->call('updateStatus');

    $report->refresh();
    expect($report->status)->toBe(ReportStatus::INVESTIGATING);

    // 3. Reporter posts reply message
    $service->addReporterMessage($report, 'Additional details submitted by anonymous reporter.');

    expect($report->publicUpdates)->toHaveCount(3); // 1 initial + 1 status update + 1 reporter reply

    // 4. Admin updates status to Resolved
    Livewire::actingAs($this->admin)
        ->test(ReportDetails::class, ['report' => $report])
        ->set('new_status', 'resolved')
        ->call('updateStatus');

    $report->refresh();
    expect($report->status)->toBe(ReportStatus::RESOLVED);
});
