<x-admin-layout title="Categories - Raimart Admin">
    <div x-data="{ 
            deleteModalOpen: false, 
            deleteFormAction: '', 
            categoryName: '',
            openDeleteModal(action, name) {
                this.deleteFormAction = action;
                this.categoryName = name;
                this.deleteModalOpen = true;
            }
        }" 
        @keydown.escape.window="deleteModalOpen = false"
    >
        <div class="flex items-center justify-between">
            <h1 class="font-serif text-2xl font-bold">All Categories</h1>
            <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-brand-orange px-4 py-2 text-sm font-semibold text-white hover:bg-brand-navy">
                <i class="fa-solid fa-plus mr-1"></i> New Category
            </a>
        </div>
        <p class="text-sm text-gray-500 mt-1">Manage product categories and subcategories.</p>

        <!-- Table -->
        <div class="mt-6 bg-white rounded-xl shadow-sm ring-1 ring-brand-navy/5 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3 text-left">Image</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Parent</th>
                        <th class="px-4 py-3 text-left">Order</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($categories as $category)
                        <!-- Parent Row -->
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <img src="{{ image_url($category->image) }}" class="h-10 w-10 rounded-lg object-cover border border-gray-100" alt="{{ $category->name }}">
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-brand-navy text-sm">{{ $category->name }}</p>
                                @if($category->children->count())
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $category->children->count() }} subcategories</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">None (Root)</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $category->sort_order }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($category->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 px-2.5 py-0.5 text-xs font-medium border border-green-100">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 text-red-600 px-2.5 py-0.5 text-xs font-medium border border-red-100">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-brand-orange hover:text-brand-navy" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" @click="openDeleteModal('{{ route('admin.categories.destroy', $category) }}', '{{ addslashes($category->name) }}')" class="text-gray-400 hover:text-red-500" title="Delete">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Children Rows -->
                        @foreach ($category->children as $child)
                            <tr class="hover:bg-gray-50/50 transition-colors bg-gray-50/30">
                                <td class="px-4 py-2.5 pl-8">
                                    <img src="{{ image_url($child->image) }}" class="h-8 w-8 rounded-md object-cover border border-gray-100" alt="{{ $child->name }}">
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-corner-down-right text-gray-300 text-xs"></i>
                                        <p class="text-sm text-gray-700">{{ $child->name }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-xs text-gray-400">{{ $category->name }}</td>
                                <td class="px-4 py-2.5 text-xs text-gray-400">{{ $child->sort_order }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($child->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 px-2.5 py-0.5 text-xs font-medium border border-green-100">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-50 text-red-600 px-2.5 py-0.5 text-xs font-medium border border-red-100">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('admin.categories.edit', $child) }}" class="text-brand-orange hover:text-brand-navy" title="Edit">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                        <button type="button" @click="openDeleteModal('{{ route('admin.categories.destroy', $child) }}', '{{ addslashes($child->name) }}')" class="text-gray-400 hover:text-red-500" title="Delete">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Alpine Delete Confirmation Modal -->
        <div x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-brand-navy/60 backdrop-blur-sm"></div>
        <div x-show="deleteModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
             
            <div @click.away="deleteModalOpen = false" class="relative w-full max-w-sm overflow-hidden rounded-2xl bg-white text-left shadow-2xl ring-1 ring-black/5 sm:my-8">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-triangle-exclamation text-lg text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Delete Category</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Are you sure you want to delete <span class="font-bold text-gray-900" x-text="categoryName"></span>? All subcategories will also be removed. This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="deleteFormAction" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">Delete</button>
                    </form>
                    <button @click="deleteModalOpen = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
