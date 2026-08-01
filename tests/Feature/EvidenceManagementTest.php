<?php

use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    $this->category = Category::factory()->create(['name' => 'Financial Fraud']);
    $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $this->report = Report::create([
        'category_id' => $this->category->id,
        'tracking_code' => 'SV-EVID-0001',
        'tracking_hash' => hash('sha256', 'SV-EVID-0001'),
        'title' => 'Evidence Test Report',
        'description' => 'Description for testing evidence viewer.',
        'severity' => ReportSeverity::HIGH,
        'status' => ReportStatus::PENDING,
    ]);
});

test('unauthenticated user cannot preview or download evidence', function () {
    $file = ReportFile::create([
        'report_id' => $this->report->id,
        'original_name' => 'secret.pdf',
        'stored_path' => 'evidence/secret.pdf',
        'file_type' => 'application/pdf',
        'file_size' => 1024,
    ]);

    $this->get('/admin/evidence/'.$file->id.'/preview')->assertRedirect('/login');
    $this->get('/admin/evidence/'.$file->id.'/download')->assertRedirect('/login');
});

test('authenticated admin can preview image file inline', function () {
    Storage::disk('local')->put('evidence/photo.png', 'fake image bytes');

    $file = ReportFile::create([
        'report_id' => $this->report->id,
        'original_name' => 'photo.png',
        'stored_path' => 'evidence/photo.png',
        'file_type' => 'image/png',
        'file_size' => 2048,
    ]);

    $response = $this->actingAs($this->admin)->get('/admin/evidence/'.$file->id.'/preview');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/png');
    $response->assertHeader('Content-Disposition', 'inline; filename="photo.png"');
});

test('authenticated admin can preview PDF file inline in browser', function () {
    Storage::disk('local')->put('evidence/document.pdf', 'fake pdf bytes');

    $file = ReportFile::create([
        'report_id' => $this->report->id,
        'original_name' => 'document.pdf',
        'stored_path' => 'evidence/document.pdf',
        'file_type' => 'application/pdf',
        'file_size' => 4096,
    ]);

    $response = $this->actingAs($this->admin)->get('/admin/evidence/'.$file->id.'/preview');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('Content-Disposition', 'inline; filename="document.pdf"');
});

test('authenticated admin can force download evidence file', function () {
    Storage::disk('local')->put('evidence/statement.docx', 'fake docx bytes');

    $file = ReportFile::create([
        'report_id' => $this->report->id,
        'original_name' => 'statement.docx',
        'stored_path' => 'evidence/statement.docx',
        'file_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'file_size' => 8192,
    ]);

    $response = $this->actingAs($this->admin)->get('/admin/evidence/'.$file->id.'/download');

    $response->assertStatus(200);
    $response->assertHeader('Content-Disposition', 'attachment; filename=statement.docx');
});

test('preview returns 404 if file does not exist on disk', function () {
    $file = ReportFile::create([
        'report_id' => $this->report->id,
        'original_name' => 'missing.png',
        'stored_path' => 'evidence/missing.png',
        'file_type' => 'image/png',
        'file_size' => 2048,
    ]);

    $this->actingAs($this->admin)
        ->get('/admin/evidence/'.$file->id.'/preview')
        ->assertStatus(404);
});
