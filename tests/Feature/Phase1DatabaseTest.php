<?php

use App\Enums\AuthorType;
use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportFile;
use App\Models\ReportUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeds default categories, users, and reports successfully', function () {
    $this->seed();

    expect(Category::count())->toBeGreaterThanOrEqual(6);
    expect(User::count())->toBe(3);
    expect(Report::count())->toBe(3);

    $admin = User::where('email', 'admin@safevoice.org')->first();
    expect($admin)->not->toBeNull();
    expect($admin->role)->toBe(UserRole::ADMIN);
    expect($admin->isAdmin())->toBeTrue();
});

test('report encrypted internal notes cast works properly', function () {
    $category = Category::factory()->create();

    $report = Report::create([
        'category_id' => $category->id,
        'tracking_code' => 'SV-TEST-1234-ABCD',
        'tracking_hash' => hash('sha256', 'SV-TEST-1234-ABCD'),
        'title' => 'Test Sensitive Report',
        'description' => 'Plain text description for testing.',
        'severity' => ReportSeverity::HIGH,
        'status' => ReportStatus::PENDING,
        'internal_notes' => 'Highly confidential internal note.',
    ]);

    $fetched = Report::find($report->id);
    expect($fetched->internal_notes)->toBe('Highly confidential internal note.');
    expect($fetched->description)->toBe('Plain text description for testing.');
});

test('report relationships work as expected', function () {
    $category = Category::factory()->create();
    $user = User::factory()->create(['role' => UserRole::INVESTIGATOR]);

    $report = Report::create([
        'category_id' => $category->id,
        'tracking_code' => 'SV-RELATION-TEST',
        'tracking_hash' => hash('sha256', 'SV-RELATION-TEST'),
        'title' => 'Test Relation Report',
        'description' => 'Testing model relationships.',
        'severity' => ReportSeverity::MEDIUM,
        'status' => ReportStatus::UNDER_REVIEW,
        'assigned_admin_id' => $user->id,
    ]);

    $file = ReportFile::create([
        'report_id' => $report->id,
        'original_name' => 'doc.pdf',
        'stored_path' => 'evidence/doc.pdf',
        'file_type' => 'application/pdf',
        'file_size' => 2048,
    ]);

    $update = ReportUpdate::create([
        'report_id' => $report->id,
        'author_type' => AuthorType::ADMIN,
        'user_id' => $user->id,
        'message' => 'Status update message.',
        'is_public' => true,
    ]);

    $log = ActivityLog::create([
        'user_id' => $user->id,
        'report_id' => $report->id,
        'action' => 'report.created',
        'description' => 'Test activity log entry.',
    ]);

    expect($report->category->id)->toBe($category->id);
    expect($report->assignedAdmin->id)->toBe($user->id);
    expect($report->files)->toHaveCount(1);
    expect($report->updates)->toHaveCount(1);
    expect($report->activityLogs)->toHaveCount(1);
});
