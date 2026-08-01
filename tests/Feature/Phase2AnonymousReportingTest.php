<?php

use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use App\Livewire\AnonymousReportForm;
use App\Livewire\ReportTracking;
use App\Models\Category;
use App\Models\Report;
use App\Services\ReportService;
use App\Services\TrackingCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    $this->category = Category::factory()->create([
        'name' => 'Financial Fraud',
        'is_active' => true,
    ]);
});

test('tracking code generator creates unique codes and hashes', function () {
    $generator = new TrackingCodeGenerator;
    $tracking = $generator->generate();

    expect($tracking['code'])->toMatch('/^SV-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/');
    expect($tracking['hash'])->toBe(hash('sha256', $tracking['code']));
    expect($generator->hash($tracking['code']))->toBe($tracking['hash']);
});

test('report service creates anonymous report with evidence files and zero PII logging', function () {
    $file = UploadedFile::fake()->create('contract_evidence.pdf', 500, 'application/pdf');

    $service = app(ReportService::class);
    $result = $service->createReport([
        'category_id' => $this->category->id,
        'title' => 'Unapproved Wire Transfer Incident',
        'description' => 'Discovered unapproved wire transfer of $25,000 to off-shore account.',
        'incident_date' => now()->subDay()->toDateString(),
        'incident_location' => 'Finance Dept',
        'severity' => ReportSeverity::HIGH,
    ], [$file]);

    expect($result['tracking_code'])->not->toBeEmpty();

    $report = $result['report'];
    expect($report->status)->toBe(ReportStatus::PENDING);
    expect($report->files)->toHaveCount(1);
    expect($report->files->first()->original_name)->toBe('contract_evidence.pdf');
    expect(Storage::disk('local')->exists($report->files->first()->stored_path))->toBeTrue();

    // Verify activity log contains zero IP/User-Agent data
    $log = $report->activityLogs()->first();
    expect($log->ip_address)->toBeNull();
    expect($log->user_agent)->toBeNull();
});

test('anonymous report form Livewire component submits report successfully', function () {
    Livewire::test(AnonymousReportForm::class)
        ->set('category_id', $this->category->id)
        ->set('title', 'Workplace Bullying Report')
        ->set('description', 'Detailed description of workplace bullying incident exceeding twenty characters.')
        ->call('validateStep1')
        ->assertSet('currentStep', 2)
        ->set('severity', 'high')
        ->call('submit')
        ->assertSet('currentStep', 3)
        ->assertSee('Report Submitted Successfully');

    expect(Report::count())->toBe(1);
});

test('anonymous report form validates file size and format constraints', function () {
    $invalidFile = UploadedFile::fake()->create('malicious_script.exe', 500, 'application/x-msdownload');

    Livewire::test(AnonymousReportForm::class)
        ->set('category_id', $this->category->id)
        ->set('title', 'Security Incident')
        ->set('description', 'Detailed description of security incident exceeding twenty characters.')
        ->set('files', [$invalidFile])
        ->call('submit')
        ->assertHasErrors(['files.0']);
});

test('report tracking Livewire component fetches valid report by tracking code', function () {
    $service = app(ReportService::class);
    $result = $service->createReport([
        'category_id' => $this->category->id,
        'title' => 'Report for Tracking Lookup',
        'description' => 'Description of report for tracking lookup testing.',
        'severity' => ReportSeverity::MEDIUM,
    ]);

    $code = $result['tracking_code'];

    Livewire::test(ReportTracking::class)
        ->set('tracking_code', $code)
        ->call('track')
        ->assertSee('Report for Tracking Lookup')
        ->assertSee('Pending');
});

test('report tracking handles invalid tracking code gracefully', function () {
    Livewire::test(ReportTracking::class)
        ->set('tracking_code', 'SV-INVALID-CODE-9999')
        ->call('track')
        ->assertSee('No report found matching this tracking code');
});

test('reporter can send public reply on tracked report', function () {
    $service = app(ReportService::class);
    $result = $service->createReport([
        'category_id' => $this->category->id,
        'title' => 'Report with Message Reply',
        'description' => 'Description of report testing public message reply.',
        'severity' => ReportSeverity::LOW,
    ]);

    $code = $result['tracking_code'];

    Livewire::test(ReportTracking::class)
        ->set('tracking_code', $code)
        ->call('track')
        ->set('message_body', 'Here is additional context requested by investigators.')
        ->call('sendReply')
        ->assertSee('Your message has been posted to the investigator team.');

    $report = $result['report']->fresh();
    expect($report->publicUpdates)->toHaveCount(2); // 1 initial + 1 reporter reply
});
