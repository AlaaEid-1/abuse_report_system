<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'original_name',
        'stored_path',
        'file_type',
        'file_size',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function isImage(): bool
    {
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) ||
            str_starts_with($this->file_type, 'image/');
    }

    public function isPdf(): bool
    {
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

        return $extension === 'pdf' || $this->file_type === 'application/pdf';
    }

    public function previewUrl(): string
    {
        return route('admin.evidence.preview', $this);
    }

    public function downloadUrl(): string
    {
        return route('admin.evidence.download', $this);
    }
}
