<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ReportFile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvidenceDownloadController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle secure file download for report evidence.
     */
    public function download(Request $request, ReportFile $file): StreamedResponse
    {
        $report = $file->report;

        $this->authorize('downloadEvidence', $report);

        if (! Storage::disk('local')->exists($file->stored_path)) {
            abort(404, 'Evidence file not found on private storage server.');
        }

        // Log evidence download action
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'report_id' => $report->id,
            'action' => 'evidence.downloaded',
            'description' => sprintf('Downloaded file %s by %s.', $file->original_name, $request->user()->name),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'properties' => [
                'file_id' => $file->id,
                'file_name' => $file->original_name,
                'file_size' => $file->file_size,
            ],
        ]);

        return Storage::disk('local')->download($file->stored_path, $file->original_name);
    }
}
