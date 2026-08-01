<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ReportFile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvidenceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Stream evidence file in-browser for previewing images and PDFs.
     */
    public function preview(Request $request, ReportFile $file): StreamedResponse|BinaryFileResponse
    {
        $report = $file->report;

        $this->authorize('downloadEvidence', $report);

        if (! Storage::disk('local')->exists($file->stored_path)) {
            abort(404, 'Evidence file not found on private storage server.');
        }

        // Log evidence preview action
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'report_id' => $report->id,
            'action' => 'evidence.previewed',
            'description' => sprintf('Previewed file %s (%s).', $file->original_name, $file->file_type),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'properties' => [
                'file_id' => $file->id,
                'file_name' => $file->original_name,
                'mime_type' => $file->file_type,
            ],
        ]);

        $fullPath = Storage::disk('local')->path($file->stored_path);

        return response()->file($fullPath, [
            'Content-Type' => $file->file_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes($file->original_name).'"',
        ]);
    }

    /**
     * Securely force file download.
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
            'description' => sprintf('Downloaded file %s (%d bytes).', $file->original_name, $file->file_size),
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
