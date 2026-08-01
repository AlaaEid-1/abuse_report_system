<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Component;

class CategoryManagement extends Component
{
    use AuthorizesRequests;

    public bool $showModal = false;

    public ?int $editingCategoryId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public string $icon = 'shield-exclamation';

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:150|unique:categories,name,'.($this->editingCategoryId ?? 'NULL'),
            'slug' => 'required|string|max:150|unique:categories,slug,'.($this->editingCategoryId ?? 'NULL'),
            'description' => 'nullable|string|max:1000',
            'icon' => 'required|string|max:50',
            'is_active' => 'boolean',
        ];
    }

    public function updatedName(): void
    {
        if (! $this->editingCategoryId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openCreateModal(): void
    {
        $this->authorize('manage', Category::class);
        $this->reset(['editingCategoryId', 'name', 'slug', 'description', 'icon', 'is_active']);
        $this->icon = 'shield-exclamation';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(Category $category): void
    {
        $this->authorize('manage', Category::class);
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->icon = $category->icon;
        $this->is_active = $category->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize('manage', Category::class);
        $this->validate();

        $isUpdate = (bool) $this->editingCategoryId;

        Category::updateOrCreate(
            ['id' => $this->editingCategoryId],
            [
                'name' => $this->name,
                'slug' => Str::slug($this->slug),
                'description' => $this->description,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ]
        );

        $this->showModal = false;
        $this->reset(['editingCategoryId', 'name', 'slug', 'description', 'icon', 'is_active']);
        $this->dispatch('toast', message: $isUpdate ? 'Category updated successfully.' : 'Category created successfully.', type: 'success');
    }

    public function toggleActive(Category $category): void
    {
        $this->authorize('manage', Category::class);
        $category->is_active = ! $category->is_active;
        $category->save();
        $this->dispatch('toast', message: $category->is_active ? 'Category activated.' : 'Category deactivated.', type: 'success');
    }

    public function deleteCategory(Category $category): void
    {
        $this->authorize('manage', Category::class);

        if ($category->reports()->count() > 0) {
            $this->dispatch('toast', message: 'Cannot delete a category that has associated reports.', type: 'error');

            return;
        }

        $category->delete();
        $this->dispatch('toast', message: 'Category deleted successfully.', type: 'success');
    }

    public function render()
    {
        $categories = Category::withCount('reports')->orderBy('name')->get();

        return view('livewire.admin.category-management', [
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}
