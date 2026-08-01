<?php

namespace App\Livewire;

use App\Enums\ReportSeverity;
use App\Models\Category;
use App\Services\ReportService;
use Livewire\Component;
use Livewire\WithFileUploads;

class AnonymousReportForm extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    public string $category_id = '';

    public string $title = '';

    public string $description = '';

    public ?string $incident_date = null;

    public ?string $incident_location = null;

    public string $severity = 'medium';

    public array $files = [];

    public ?string $generatedTrackingCode = null;

    protected function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20',
            'incident_date' => 'nullable|date|before_or_equal:today',
            'incident_location' => 'nullable|string|max:255',
            'severity' => 'required|in:low,medium,high,critical',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,docx,txt|max:10240',
        ];
    }

    protected array $messages = [
        'category_id.required' => 'Please select an abuse category.',
        'title.required' => 'Report title is required.',
        'title.min' => 'Title must be at least 5 characters.',
        'description.required' => 'Detailed description is required.',
        'description.min' => 'Description must be at least 20 characters.',
        'files.*.mimes' => 'Allowed file formats are PDF, JPG, PNG, WEBP, DOCX, and TXT.',
        'files.*.max' => 'Each evidence file must not exceed 10MB.',
    ];

    public function validateStep1(): void
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20',
        ]);

        $this->currentStep = 2;
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function removeFile(int $index): void
    {
        if (isset($this->files[$index])) {
            unset($this->files[$index]);
            $this->files = array_values($this->files);
        }
    }

    public function submit(ReportService $reportService): void
    {
        $validated = $this->validate();

        $result = $reportService->createReport([
            'category_id' => (int) $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'incident_date' => $this->incident_date,
            'incident_location' => $this->incident_location,
            'severity' => ReportSeverity::from($this->severity),
        ], $this->files);

        $this->generatedTrackingCode = $result['tracking_code'];
        $this->currentStep = 3;
    }

    public function resetForm(): void
    {
        $this->reset([
            'currentStep',
            'category_id',
            'title',
            'description',
            'incident_date',
            'incident_location',
            'severity',
            'files',
            'generatedTrackingCode',
        ]);
    }

    public function render()
    {
        $categories = Category::active()->orderBy('name')->get();

        return view('livewire.anonymous-report-form', [
            'categories' => $categories,
        ])->layout('layouts.public');
    }
}
