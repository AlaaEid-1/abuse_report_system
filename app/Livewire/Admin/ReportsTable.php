<?php

namespace App\Livewire\Admin;

use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use App\Models\Category;
use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;

class ReportsTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $category_id = '';

    public string $severity = '';

    protected array $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'category_id' => ['except' => ''],
        'severity' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingSeverity(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'category_id', 'severity']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Report::with(['category', 'assignedAdmin'])->latest();

        if (! empty($this->search)) {
            $searchTerm = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('tracking_code', 'like', $searchTerm);
            });
        }

        if (! empty($this->status)) {
            $query->where('status', $this->status);
        }

        if (! empty($this->category_id)) {
            $query->where('category_id', $this->category_id);
        }

        if (! empty($this->severity)) {
            $query->where('severity', $this->severity);
        }

        $reports = $query->paginate(10);
        $categories = Category::active()->orderBy('name')->get();

        return view('livewire.admin.reports-table', [
            'reports' => $reports,
            'categories' => $categories,
            'statuses' => ReportStatus::cases(),
            'severities' => ReportSeverity::cases(),
        ])->layout('layouts.admin');
    }
}
