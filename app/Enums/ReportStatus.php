<?php

namespace App\Enums;

enum ReportStatus: string
{
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case INVESTIGATING = 'investigating';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::UNDER_REVIEW => 'Under Review',
            self::INVESTIGATING => 'Investigating',
            self::RESOLVED => 'Resolved',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::UNDER_REVIEW => 'blue',
            self::INVESTIGATING => 'purple',
            self::RESOLVED => 'green',
            self::REJECTED => 'red',
        };
    }
}
