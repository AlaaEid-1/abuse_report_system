<?php

namespace App\Livewire;

use App\Models\Report;
use App\Services\ReportService;
use Livewire\Component;

class ReportTracking extends Component
{
    public string $tracking_code = '';

    public ?int $reportId = null;

    public string $message_body = '';

    public bool $searched = false;

    public ?string $errorMessage = null;

    protected $queryString = [
        'tracking_code' => ['except' => '', 'as' => 'code'],
    ];

    public function mount(ReportService $reportService): void
    {
        if (! empty($this->tracking_code)) {
            $this->track($reportService);
        }
    }

    public function track(ReportService $reportService): void
    {
        $this->validate([
            'tracking_code' => 'required|string|min:10',
        ], [
            'tracking_code.required' => 'Please enter your tracking code.',
            'tracking_code.min' => 'Tracking code must be at least 10 characters.',
        ]);

        $this->searched = true;
        $this->errorMessage = null;

        $report = $reportService->getReportByTrackingCode($this->tracking_code);

        if (! $report) {
            $this->reportId = null;
            $this->errorMessage = 'No report found matching this tracking code. Please verify the code and try again.';

            return;
        }

        $this->reportId = $report->id;
    }

    public function sendReply(ReportService $reportService): void
    {
        $this->validate([
            'message_body' => 'required|string|min:3|max:2000',
        ], [
            'message_body.required' => 'Please enter a message to send.',
            'message_body.min' => 'Message must be at least 3 characters.',
        ]);

        if (! $this->reportId) {
            return;
        }

        $report = Report::find($this->reportId);

        if ($report) {
            $reportService->addReporterMessage($report, $this->message_body);
            $this->message_body = '';
            session()->flash('status', 'Your message has been posted to the investigator team.');
        }
    }

    public function render()
    {
        $report = $this->reportId ? Report::with(['category', 'files', 'publicUpdates'])->find($this->reportId) : null;

        return view('livewire.report-tracking', [
            'report' => $report,
        ])->layout('layouts.public');
    }
}
