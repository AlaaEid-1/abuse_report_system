<?php

use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Livewire\Admin\ReportDetails;
use App\Livewire\ReportTracking;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create(['name' => 'Financial Fraud']);
    $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $this->report = Report::create([
        'category_id' => $this->category->id,
        'tracking_code' => 'SV-NOTE-1234',
        'tracking_hash' => hash('sha256', 'SV-NOTE-1234'),
        'title' => 'Financial Discrepancy Report',
        'description' => 'Discovered inconsistent bookkeeping records.',
        'severity' => ReportSeverity::HIGH,
        'status' => ReportStatus::PENDING,
    ]);
});

test('authenticated admin can save internal notes and see active note display', function () {
    Livewire::actingAs($this->admin)
        ->test(ReportDetails::class, ['report' => $this->report])
        ->set('internal_note', 'Confidential: Financial audit initiated with forensic accounting team.')
        ->call('saveInternalNotes')
        ->assertDispatched('toast')
        ->assertSee('Saved Note')
        ->assertSee('Confidential: Financial audit initiated with forensic accounting team.');

    $this->report->refresh();
    expect($this->report->internal_notes)->toBe('Confidential: Financial audit initiated with forensic accounting team.');
});

test('public report tracking page never exposes internal notes', function () {
    // Set an internal note on the report
    $this->report->internal_notes = 'TOP SECRET INTERNAL INVESTIGATION DETAILS';
    $this->report->save();

    // 1. HTTP Response check
    $response = $this->get('/track?code=SV-NOTE-1234');
    $response->assertStatus(200);
    $response->assertDontSee('TOP SECRET INTERNAL INVESTIGATION DETAILS');
    $response->assertDontSee('Saved Note');

    // 2. Livewire component instance state check
    Livewire::test(ReportTracking::class, ['tracking_code' => 'SV-NOTE-1234'])
        ->call('track')
        ->assertDontSee('TOP SECRET INTERNAL INVESTIGATION DETAILS');
});

test('unauthenticated users cannot view admin report details or internal notes', function () {
    $this->get('/admin/reports/'.$this->report->id)->assertRedirect('/login');
});
