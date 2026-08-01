<?php

namespace App\Models;

use App\Enums\AuthorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'author_type',
        'user_id',
        'message',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'author_type' => AuthorType::class,
            'is_public' => 'boolean',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
