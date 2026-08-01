<?php

use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Livewire\Admin\CategoryManagement;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ReportDetails;
use App\Livewire\Admin\ReportsTable;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    $this->category = Category::factory()->create([
        'name' => 'Financial Fraud',
        'is_active' => true,
    ]);

    $this->admin = User::factory()->create([
        'name' => 'Compliance Manager',
        'email' => 'admin@safevoice.org',
        'role' => UserRole::ADMIN,
    ]);

    $this->investigator = User::factory()->create([
        'name' => 'Field Investigator',
        'email' => 'investigator@safevoice.org',
        'role' => UserRole::INVESTIGATOR,
    ]);
});

test('unauthenticated users cannot access admin dashboard or reports', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->get('/admin/reports')->assertRedirect('/login');
});

test('authenticated admin can access compliance dashboard', function () {
    $this->actingAs($this->admin)
        ->get('/admin/dashboard')
        ->assertStatus(200)
        ->assertSee('Compliance Dashboard');
});

test('admin dashboard calculates stats correctly', function () {
    Report::create([
        'category_id' => $this->category->id,
        'tracking_code' => 'SV-STAT-0001',
        'tracking_hash' => hash('sha256', 'SV-STAT-0001'),
        'title' => 'Report 1',
        'description' => 'Test description text.',
        'status' => ReportStatus::PENDING,
        'severity' => ReportSeverity::HIGH,
    ]);

    Livewire::actingAs($this->admin)
        ->test(Dashboard::class)
        ->assertSee('Total Reports')
        ->assertSee('Pending Triage');
});

test('reports table supports search and filtering', function () {
    $report1 = Report::create([
        'category_id' => $this->category->id,
        'tracking_code' => 'SV-SEARCH-1111',
        'tracking_hash' => hash('sha256', 'SV-SEARCH-1111'),
        'title' => 'Embezzlement in Branch A',
        'description' => 'Detailed description of embezzlement.',
        'status' => ReportStatus::PENDING,
        'severity' => ReportSeverity::HIGH,
    ]);

    $report2 = Report::create([
        'category_id' => $this->category->id,
        'tracking_code' => 'SV-SEARCH-2222',
        'tracking_hash' => hash('sha256', 'SV-SEARCH-2222'),
        'title' => 'Safety Hazard in Loading Dock',
        'description' => 'Detailed description of safety hazard.',
        'status' => ReportStatus::RESOLVED,
        'severity' => ReportSeverity::LOW,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ReportsTable::class)
        ->set('search', 'Embezzlement')
        ->assertSee('Embezzlement in Branch A')
        ->assertDontSee('Safety Hazard in Loading Dock');
});

test('admin can update report status and assign investigator', function () {
    $report = Report::create([
        'category_id' => $this->category->id,
        'tracking_code' => 'SV-UPDATE-9999',
        'tracking_hash' => hash('sha256', 'SV-UPDATE-9999'),
        'title' => 'Report to Update',
        'description' => 'Test description for updating status.',
        'status' => ReportStatus::PENDING,
        'severity' => ReportSeverity::CRITICAL,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ReportDetails::class, ['report' => $report])
        ->set('new_status', 'investigating')
        ->call('updateStatus')
        ->set('assigned_admin_id', (string) $this->investigator->id)
        ->call('assignInvestigator');

    $report->refresh();
    expect($report->status)->toBe(ReportStatus::INVESTIGATING);
    expect($report->assigned_admin_id)->toBe($this->investigator->id);
});

test('admin can save internal notes encrypted', function () {
    $report = Report::create([
        'category_id' => $this->category->id,
        'tracking_code' => 'SV-NOTE-0001',
        'tracking_hash' => hash('sha256', 'SV-NOTE-0001'),
        'title' => 'Report for Internal Note',
        'description' => 'Test description for note.',
        'status' => ReportStatus::PENDING,
        'severity' => ReportSeverity::MEDIUM,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ReportDetails::class, ['report' => $report])
        ->set('internal_note', 'Confidential witness interview results.')
        ->call('saveInternalNotes');

    $report->refresh();
    expect($report->internal_notes)->toBe('Confidential witness interview results.');
});

test('authorized user can download evidence files securely', function () {
    $report = Report::create([
        'category_id' => $this->category->id,
        'tracking_code' => 'SV-FILE-0001',
        'tracking_hash' => hash('sha256', 'SV-FILE-0001'),
        'title' => 'Report for File Download',
        'description' => 'Test description for download.',
        'status' => ReportStatus::PENDING,
        'severity' => ReportSeverity::MEDIUM,
    ]);

    Storage::disk('local')->put('evidence/sample_evidence.pdf', 'Dummy PDF content');

    $file = ReportFile::create([
        'report_id' => $report->id,
        'original_name' => 'sample_evidence.pdf',
        'stored_path' => 'evidence/sample_evidence.pdf',
        'file_type' => 'application/pdf',
        'file_size' => 1024,
    ]);

    $response = $this->actingAs($this->admin)
        ->get('/admin/evidence/'.$file->id.'/download');

    $response->assertStatus(200);
    $response->assertHeader('content-disposition', 'attachment; filename=sample_evidence.pdf');
});

test('admin can create and manage categories', function () {
    Livewire::actingAs($this->admin)
        ->test(CategoryManagement::class)
        ->call('openCreateModal')
        ->set('name', 'Environmental Violation')
        ->set('description', 'Illegal dumping and pollution reports.')
        ->call('save');

    expect(Category::where('slug', 'environmental-violation')->exists())->toBeTrue();
});
