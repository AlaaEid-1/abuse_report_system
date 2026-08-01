<?php

namespace App\Livewire\Admin;

use App\Enums\AuthorType;
use App\Enums\ReportStatus;
use App\Models\ActivityLog;
use App\Models\Report;
use App\Models\ReportUpdate;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ReportDetails extends Component
{
    use AuthorizesRequests;

    public Report $report;

    public string $new_status = '';

    public ?string $assigned_admin_id = null;

    public string $internal_note = '';

    public string $public_message = '';

    public function mount(Report $report): void
    {
        $this->authorize('view', $report);
        $this->report = $report->load(['category', 'assignedAdmin', 'files', 'updates.user', 'activityLogs.user']);
        $this->new_status = $report->status->value;
        $this->assigned_admin_id = $report->assigned_admin_id ? (string) $report->assigned_admin_id : null;
        $this->internal_note = $report->internal_notes ?? '';
    }

    public function updateStatus(): void
    {
        $this->authorize('updateStatus', $this->report);

        $this->validate([
            'new_status' => 'required|in:pending,under_review,investigating,resolved,rejected',
        ]);

        $oldStatus = $this->report->status->value;

        if ($oldStatus !== $this->new_status) {
            $this->report->status = ReportStatus::from($this->new_status);
            $this->report->save();

            // Create automatic public update
            ReportUpdate::create([
                'report_id' => $this->report->id,
                'author_type' => AuthorType::ADMIN,
                'user_id' => auth()->id(),
                'message' => 'Status updated to: '.$this->report->status->label(),
                'is_public' => true,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'report_id' => $this->report->id,
                'action' => 'report.status_changed',
                'description' => sprintf('Status changed from %s to %s by %s.', $oldStatus, $this->new_status, auth()->user()->name),
                'properties' => ['old_status' => $oldStatus, 'new_status' => $this->new_status],
            ]);

            $this->dispatch('toast', message: 'Report status updated to: '.$this->report->status->label().'.', type: 'success');
            $this->report->refresh();
        }
    }

    public function assignInvestigator(): void
    {
        $this->authorize('assign', $this->report);

        $assignedId = ! empty($this->assigned_admin_id) ? (int) $this->assigned_admin_id : null;
        $this->report->assigned_admin_id = $assignedId;
        $this->report->save();

        $assigneeName = $assignedId ? User::find($assignedId)?->name : 'Unassigned';

        ActivityLog::create([
            'user_id' => auth()->id(),
            'report_id' => $this->report->id,
            'action' => 'report.assigned',
            'description' => sprintf('Assigned to %s by %s.', $assigneeName, auth()->user()->name),
        ]);

        $this->dispatch('toast', message: 'Investigator assignment updated successfully.', type: 'success');
        $this->report->refresh();
    }

    public function saveInternalNotes(): void
    {
        $this->authorize('updateStatus', $this->report);

        $this->validate([
            'internal_note' => 'nullable|string|max:5000',
        ]);

        $this->report->internal_notes = $this->internal_note;
        $this->report->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'report_id' => $this->report->id,
            'action' => 'report.internal_notes_updated',
            'description' => sprintf('Internal notes updated by %s.', auth()->user()->name),
        ]);

        $this->dispatch('toast', message: 'Internal notes saved securely.', type: 'success');
        $this->report->refresh();
    }

    public function postPublicMessage(): void
    {
        $this->authorize('updateStatus', $this->report);

        $this->validate([
            'public_message' => 'required|string|min:3|max:2000',
        ]);

        ReportUpdate::create([
            'report_id' => $this->report->id,
            'author_type' => AuthorType::ADMIN,
            'user_id' => auth()->id(),
            'message' => $this->public_message,
            'is_public' => true,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'report_id' => $this->report->id,
            'action' => 'report.public_message_posted',
            'description' => sprintf('Public message posted by %s.', auth()->user()->name),
        ]);

        $this->public_message = '';
        $this->dispatch('toast', message: 'Public message posted to reporter timeline.', type: 'success');
        $this->report->refresh();
    }

    public function render()
    {
        $investigators = User::whereIn('role', ['admin', 'super_admin', 'investigator'])->orderBy('name')->get();

        return view('livewire.admin.report-details', [
            'investigators' => $investigators,
            'statuses' => ReportStatus::cases(),
        ])->layout('layouts.admin');
    }
}
