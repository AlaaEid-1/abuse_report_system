<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewReporterMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Report $report,
        public string $messageText
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Message on Report: '.$this->report->tracking_code)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('The anonymous reporter has posted a new message on case '.$this->report->tracking_code.'.')
            ->line('Message snippet: "'.Str::limit($this->messageText, 100).'"')
            ->action('View Message in Portal', url('/admin/reports/'.$this->report->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'tracking_code' => $this->report->tracking_code,
            'message' => 'New reply posted by anonymous reporter.',
        ];
    }
}
