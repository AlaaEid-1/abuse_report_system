<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInvestigator();
    }

    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin() || $user->isInvestigator();
    }

    public function updateStatus(User $user, Report $report): bool
    {
        return $user->isAdmin() || $user->isInvestigator();
    }

    public function assign(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }

    public function downloadEvidence(User $user, Report $report): bool
    {
        return $user->isAdmin() || $user->isInvestigator();
    }
}
