<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Abuse Categories Management</h1>
            <p class="mt-1 text-sm text-slate-400">Manage categories available for public report submissions.</p>
        </div>

        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Category</span>
        </button>
    </div>


    <!-- Categories Data Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Category Name</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Active Reports</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-white">{{ $cat->name }}</div>
                                <div class="text-xs text-slate-500 max-w-xs truncate">{{ $cat->description }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-indigo-400">
                                {{ $cat->slug }}
                            </td>
                            <td class="px-6 py-4 font-bold text-white">
                                {{ $cat->reports_count }}
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" wire:click="toggleActive({{ $cat->id }})" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $cat->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                    <span class="w-2 h-2 rounded-full {{ $cat->is_active ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                                    {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button type="button" wire:click="openEditModal({{ $cat->id }})" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition-all">
                                    Edit
                                </button>
                                <button type="button" wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Are you sure you want to delete this category?" class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-xs font-semibold text-rose-400 transition-all">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                No categories defined yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form for Create / Edit -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <h2 class="text-lg font-bold text-white">{{ $editingCategoryId ? 'Edit Category' : 'Create New Category' }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Category Name</label>
                        <input type="text" wire:model="name" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Slug</label>
                        <input type="text" wire:model="slug" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                        @error('slug') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        @error('description') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-sm font-medium text-slate-300">Active (Visible on reporting form)</label>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow-md shadow-indigo-600/30">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
