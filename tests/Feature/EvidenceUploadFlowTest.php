<?php

use App\Livewire\AnonymousReportForm;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    $this->category = Category::factory()->create([
        'name' => 'Workplace Misconduct',
        'is_active' => true,
    ]);
});

test('full pipeline: livewire file upload creates report_files record and stores file on disk', function () {
    $pdfFile = UploadedFile::fake()->create('incident_document.pdf', 512, 'application/pdf');
    $txtFile = UploadedFile::fake()->create('statement.txt', 10, 'text/plain');

    Livewire::test(AnonymousReportForm::class)
        ->set('category_id', $this->category->id)
        ->set('title', 'Evidence Upload Pipeline Test')
        ->set('description', 'Detailed description for testing the evidence upload pipeline flow.')
        ->call('validateStep1')
        ->assertSet('currentStep', 2)
        ->set('severity', 'high')
        ->set('files', [$pdfFile, $txtFile])
        ->call('submit')
        ->assertSet('currentStep', 3);

    // 1. Verify a Report was created in database
    $report = Report::where('title', 'Evidence Upload Pipeline Test')->first();
    expect($report)->not->toBeNull();

    // 2. Verify report_files has 2 records pointing to this report_id
    expect(ReportFile::where('report_id', $report->id)->count())->toBe(2);

    $files = $report->files()->orderBy('id')->get();
    expect($files[0]->original_name)->toBe('incident_document.pdf');
    expect($files[0]->file_type)->not->toBeEmpty();
    expect($files[0]->file_size)->toBeGreaterThan(0);
    expect($files[0]->report_id)->toBe($report->id);

    expect($files[1]->original_name)->toBe('statement.txt');
    expect($files[1]->report_id)->toBe($report->id);

    // 3. Verify stored_path does NOT contain doubled 'private/private' prefix
    foreach ($files as $file) {
        expect($file->stored_path)->not->toContain('private/evidence');
        // Correct path relative to the local disk root (storage/app/private)
        expect($file->stored_path)->toStartWith('evidence/');
    }

    // 4. Verify files actually exist on disk via Storage facade
    foreach ($files as $file) {
        expect(Storage::disk('local')->exists($file->stored_path))->toBeTrue();
    }
});

test('reports with no files attached show zero count correctly', function () {
    Livewire::test(AnonymousReportForm::class)
        ->set('category_id', $this->category->id)
        ->set('title', 'No Files Report Test')
        ->set('description', 'This report is submitted without any evidence files attached.')
        ->call('validateStep1')
        ->assertSet('currentStep', 2)
        ->set('severity', 'low')
        ->call('submit')
        ->assertSet('currentStep', 3);

    $report = Report::where('title', 'No Files Report Test')->first();
    expect($report)->not->toBeNull();
    expect($report->files()->count())->toBe(0);
});
