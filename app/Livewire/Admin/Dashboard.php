<?php

namespace App\Livewire\Admin;

use App\Enums\ReportStatus;
use App\Models\Report;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $totalReports = Report::count();
        $pendingCount = Report::where('status', ReportStatus::PENDING)->count();
        $underReviewCount = Report::where('status', ReportStatus::UNDER_REVIEW)->count();
        $investigatingCount = Report::where('status', ReportStatus::INVESTIGATING)->count();
        $resolvedCount = Report::where('status', ReportStatus::RESOLVED)->count();

        $resolvedPercentage = $totalReports > 0
            ? round(($resolvedCount / $totalReports) * 100, 1)
            : 0;

        $recentReports = Report::with(['category', 'assignedAdmin'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalReports' => $totalReports,
            'pendingCount' => $pendingCount,
            'underReviewCount' => $underReviewCount,
            'investigatingCount' => $investigatingCount,
            'resolvedCount' => $resolvedCount,
            'resolvedPercentage' => $resolvedPercentage,
            'recentReports' => $recentReports,
        ])->layout('layouts.admin');
    }
}
