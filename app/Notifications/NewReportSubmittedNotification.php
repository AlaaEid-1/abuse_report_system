<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReportSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Report $report
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New SafeVoice Abuse Report: '.$this->report->tracking_code)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A new anonymous abuse report has been submitted to the compliance portal.')
            ->line('Tracking Code: '.$this->report->tracking_code)
            ->line('Category: '.$this->report->category?->name)
            ->line('Severity: '.strtoupper($this->report->severity->value))
            ->line('Title: '.$this->report->title)
            ->action('Review Report in Dashboard', url('/admin/reports/'.$this->report->id))
            ->line('Identity protection protocols remain active.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'tracking_code' => $this->report->tracking_code,
            'title' => $this->report->title,
            'category' => $this->report->category?->name,
            'severity' => $this->report->severity->value,
            'message' => 'New report submitted for triage.',
        ];
    }
}
