<?php

namespace App\Services;

use App\Enums\AuthorType;
use App\Enums\ReportStatus;
use App\Models\ActivityLog;
use App\Models\Report;
use App\Models\ReportFile;
use App\Models\ReportUpdate;
use App\Models\User;
use App\Notifications\NewReporterMessageNotification;
use App\Notifications\NewReportSubmittedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ReportService
{
    public function __construct(
        protected TrackingCodeGenerator $trackingCodeGenerator
    ) {}

    /**
     * Create an anonymous report within a database transaction.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile|TemporaryUploadedFile>  $files
     * @return array{report: Report, tracking_code: string}
     */
    public function createReport(array $data, array $files = []): array
    {
        return DB::transaction(function () use ($data, $files) {
            $tracking = $this->trackingCodeGenerator->generate();

            $report = Report::create([
                'category_id' => $data['category_id'],
                'tracking_code' => $tracking['code'],
                'tracking_hash' => $tracking['hash'],
                'title' => $data['title'],
                'description' => $data['description'],
                'incident_date' => $data['incident_date'] ?? null,
                'incident_location' => $data['incident_location'] ?? null,
                'severity' => $data['severity'],
                'status' => ReportStatus::PENDING,
                'assigned_admin_id' => null,
                'internal_notes' => null,
            ]);

            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $this->storeEvidenceFile($report, $file);
                }
            }

            // Create initial public status update
            ReportUpdate::create([
                'report_id' => $report->id,
                'author_type' => AuthorType::ADMIN,
                'user_id' => null,
                'message' => 'Report submitted successfully. Investigation queue pending.',
                'is_public' => true,
            ]);

            // Create activity log entry with zero PII
            ActivityLog::create([
                'user_id' => null,
                'report_id' => $report->id,
                'action' => 'report.submitted',
                'description' => 'Anonymous report submitted.',
                'ip_address' => null,
                'user_agent' => null,
                'properties' => [
                    'severity' => $report->severity->value,
                    'has_files' => count($files) > 0,
                ],
            ]);

            // Dispatch notification to admins
            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewReportSubmittedNotification($report));
            }

            return [
                'report' => $report,
                'tracking_code' => $tracking['code'],
            ];
        });
    }

    /**
     * Store evidence file safely in private storage.
     */
    protected function storeEvidenceFile(Report $report, UploadedFile $file): ReportFile
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $storedName = Str::uuid()->toString().($extension ? '.'.$extension : '');

        $storedPath = $file->storeAs('evidence', $storedName, 'local');

        return ReportFile::create([
            'report_id' => $report->id,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'file_type' => $file->getClientMimeType() ?: 'application/octet-stream',
            'file_size' => $file->getSize(),
        ]);
    }

    /**
     * Find a report by tracking code string.
     */
    public function getReportByTrackingCode(string $trackingCode): ?Report
    {
        $hash = $this->trackingCodeGenerator->hash($trackingCode);

        return Report::with(['category', 'files', 'publicUpdates'])
            ->where('tracking_hash', $hash)
            ->first();
    }

    /**
     * Post a reply message from the anonymous reporter.
     */
    public function addReporterMessage(Report $report, string $message): ReportUpdate
    {
        return DB::transaction(function () use ($report, $message) {
            $update = ReportUpdate::create([
                'report_id' => $report->id,
                'author_type' => AuthorType::REPORTER,
                'user_id' => null,
                'message' => $message,
                'is_public' => true,
            ]);

            ActivityLog::create([
                'user_id' => null,
                'report_id' => $report->id,
                'action' => 'report.reporter_reply',
                'description' => 'Anonymous reporter posted a message.',
                'ip_address' => null,
                'user_agent' => null,
            ]);

            // Notify assigned investigator or admins
            if ($report->assignedAdmin) {
                $report->assignedAdmin->notify(new NewReporterMessageNotification($report, $message));
            } else {
                $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
                if ($admins->isNotEmpty()) {
                    Notification::send($admins, new NewReporterMessageNotification($report, $message));
                }
            }

            return $update;
        });
    }
}
