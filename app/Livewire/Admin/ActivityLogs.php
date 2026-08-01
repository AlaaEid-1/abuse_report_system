<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogs extends Component
{
    use WithPagination;

    public string $user_id = '';

    public string $action = '';

    public string $date_from = '';

    public string $date_to = '';

    protected array $queryString = [
        'user_id' => ['except' => ''],
        'action' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
    ];

    public function updatingUserId(): void
    {
        $this->resetPage();
    }

    public function updatingAction(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['user_id', 'action', 'date_from', 'date_to']);
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityLog::with(['user', 'report'])->latest();

        if (! empty($this->user_id)) {
            $query->where('user_id', $this->user_id);
        }

        if (! empty($this->action)) {
            $query->where('action', 'like', '%'.trim($this->action).'%');
        }

        if (! empty($this->date_from)) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if (! empty($this->date_to)) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        $logs = $query->paginate(15);
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('livewire.admin.activity-logs', [
            'logs' => $logs,
            'users' => $users,
            'distinctActions' => $actions,
        ])->layout('layouts.admin');
    }
}
