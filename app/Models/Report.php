<?php

namespace App\Models;

use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'tracking_code',
        'tracking_hash',
        'title',
        'description',
        'incident_date',
        'incident_location',
        'severity',
        'status',
        'assigned_admin_id',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'severity' => ReportSeverity::class,
            'incident_date' => 'date',
            'internal_notes' => 'encrypted',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ReportFile::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ReportUpdate::class);
    }

    public function publicUpdates(): HasMany
    {
        return $this->hasMany(ReportUpdate::class)->where('is_public', true);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
