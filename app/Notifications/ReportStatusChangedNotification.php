<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Report $report,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'tracking_code' => $this->report->tracking_code,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => sprintf('Report status updated to %s.', $this->newStatus),
        ];
    }
}
