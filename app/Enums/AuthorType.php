<?php

namespace App\Enums;

enum AuthorType: string
{
    case ADMIN = 'admin';
    case REPORTER = 'reporter';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::REPORTER => 'Anonymous Reporter',
        };
    }
}
