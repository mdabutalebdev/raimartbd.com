<x-admin-layout title="Homepage Category Sections - Raimart Admin">
    <div x-data="categorySectionsManager()" @keydown.escape.window="settingsModalOpen = false">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-sans">Homepage Category Sections</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Manage which categories (main & subcategories) appear as dedicated product sections on your homepage, their display order, and layouts.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.categories.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs cursor-pointer">
                    <i class="fa-regular fa-folder-open text-gray-500"></i>
                    Manage All Categories
                </a>
                <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    View Homepage
                </a>
            </div>
        </div>

        {{-- Info Alert Box (Ariomart Style) --}}
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-blue-100 bg-blue-50/60 p-4 text-sm text-blue-900">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                <i class="fa-solid fa-info text-xs"></i>
            </span>
            <div>
                <h4 class="font-bold text-blue-950">Main Categories & Subcategories Reordering</h4>
                <p class="mt-0.5 text-xs text-blue-800 leading-relaxed">
                    Both main categories and subcategories can be showcased as homepage sections! Click the <span class="font-bold">Up (▲)</span> or <span class="font-bold">Down (▼)</span> arrows to reorder. The new order will be <span class="font-bold text-blue-900 underline">saved automatically</span>. Sections with "Show on Homepage" turned ON appear in this exact order.
                </p>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-3.5 rounded-2xl border border-gray-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.homepage.category-sections.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                {{-- Search --}}
                <div class="relative min-w-[240px] flex-1 max-w-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 py-2 pl-9 pr-4 text-xs font-medium focus:bg-white focus:border-brand-orange focus:outline-none transition">
                </div>

                {{-- Homepage Status Filter --}}
                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-gray-50/50 py-2 px-3 text-xs font-medium text-gray-700 focus:bg-white focus:border-brand-orange focus:outline-none cursor-pointer">
                    <option value="">All Homepage Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active on Homepage</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                {{-- Level Filter --}}
                <select name="level" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-gray-50/50 py-2 px-3 text-xs font-medium text-gray-700 focus:bg-white focus:border-brand-orange focus:outline-none cursor-pointer">
                    <option value="">All Levels</option>
                    <option value="main" {{ request('level') === 'main' ? 'selected' : '' }}>Main Categories</option>
                    <option value="sub" {{ request('level') === 'sub' ? 'selected' : '' }}>Subcategories</option>
                </select>

                @if(request('search') || request('status') || request('level'))
                    <a href="{{ route('admin.homepage.category-sections.index') }}" class="text-xs text-red-500 hover:underline font-semibold ml-1">Reset</a>
                @endif
            </form>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span x-text="activeCount"></span> Active on Homepage
                </span>
                <span class="text-xs text-gray-400">| Total: {{ $totalCount }} categories</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/80 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            <th class="py-3.5 pl-6 pr-3 text-left">Reorder</th>
                            <th class="px-4 py-3.5 text-left">Category</th>
                            <th class="px-4 py-3.5 text-left">Hierarchy & Products</th>
                            <th class="px-4 py-3.5 text-left">Layout Style</th>
                            <th class="px-4 py-3.5 text-center">Show on Homepage</th>
                            <th class="py-3.5 pl-3 pr-6 text-right">Settings</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse ($categories as $index => $category)
                            <tr class="hover:bg-gray-50/50 transition-colors" x-data="{ cat: {{ json_encode($category) }} }">
                                {{-- Reorder --}}
                                <td class="py-3.5 pl-6 pr-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-400 text-xs w-6">#{{ $index + 1 }}</span>
                                        <div class="flex flex-col gap-0.5">
                                            <button type="button" @click="reorder({{ $category->id }}, 'up')"
                                                @if($loop->first) disabled class="flex h-5 w-5 items-center justify-center rounded bg-gray-50 text-gray-300 cursor-not-allowed opacity-40" @else class="flex h-5 w-5 items-center justify-center rounded bg-gray-100 text-gray-600 hover:bg-brand-orange hover:text-white transition cursor-pointer" @endif
                                                title="Move Up">
                                                <i class="fa-solid fa-chevron-up text-[9px]"></i>
                                            </button>
                                            <button type="button" @click="reorder({{ $category->id }}, 'down')"
                                                @if($loop->last) disabled class="flex h-5 w-5 items-center justify-center rounded bg-gray-50 text-gray-300 cursor-not-allowed opacity-40" @else class="flex h-5 w-5 items-center justify-center rounded bg-gray-100 text-gray-600 hover:bg-brand-orange hover:text-white transition cursor-pointer" @endif
                                                title="Move Down">
                                                <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ image_url($category->image) }}" class="h-10 w-10 rounded-xl object-cover border border-gray-100 shadow-2xs" alt="{{ $category->name }}">
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $category->name }}</h4>
                                            @if ($category->parent_id)
                                                <span class="mt-1 inline-flex items-center gap-1 rounded bg-amber-50 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-200/60">
                                                    <i class="fa-solid fa-arrow-turn-down-right text-[8px]"></i> Sub of: {{ $category->parent?->name }} <span class="text-amber-500">/{{ $category->slug }}</span>
                                                </span>
                                            @else
                                                <span class="mt-1 inline-flex items-center gap-1 rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold text-gray-600">
                                                    MAIN CATEGORY <span class="text-gray-400">/{{ $category->slug }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Hierarchy & Products --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if ($category->children_count > 0)
                                            <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-700">
                                                {{ $category->children_count }} subcategories
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-[11px] font-medium text-blue-700 border border-blue-100">
                                            {{ $category->products_count }} products
                                        </span>
                                    </div>
                                </td>

                                {{-- Layout Style --}}
                                <td class="px-4 py-3.5">
                                    @if ($category->home_layout === 'grid')
                                        <div class="inline-flex flex-col">
                                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-100 w-fit">
                                                <i class="fa-solid fa-table-cells text-[10px]"></i> Grid
                                            </span>
                                            <span class="mt-1 text-[10px] text-gray-400">Max: {{ $category->home_max_products ?? 100 }} | Per load: {{ $category->home_per_page ?? 20 }}</span>
                                        </div>
                                    @else
                                        <div class="inline-flex flex-col">
                                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-50 px-2.5 py-1 text-xs font-bold text-purple-700 border border-purple-100 w-fit">
                                                <i class="fa-solid fa-arrows-left-right text-[10px]"></i> Slider
                                            </span>
                                            <span class="mt-1 text-[10px] text-gray-400">Max: {{ $category->home_max_products ?? 100 }}</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Show on Homepage Toggle --}}
                                <td class="px-4 py-3.5 text-center">
                                    <button type="button" @click="toggleHome(cat, $event)"
                                        :class="cat.show_on_home ? 'bg-emerald-500' : 'bg-gray-200'"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                        <span :class="cat.show_on_home ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                </td>

                                {{-- Settings Gear Button --}}
                                <td class="py-3.5 pl-3 pr-6 text-right">
                                    <button type="button" @click="openSettings(cat)"
                                        class="flex h-8 w-8 ml-auto items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-brand-orange hover:border-brand-orange transition cursor-pointer shadow-2xs"
                                        title="Configure section settings">
                                        <i class="fa-solid fa-gear text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <i class="fa-regular fa-folder-open text-3xl mb-2 text-gray-300"></i>
                                    <p class="text-sm">No categories found matching your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section Settings Modal (Ariomart Style - Image 4) --}}
        <div x-show="settingsModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                {{-- Backdrop --}}
                <div x-show="settingsModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" @click="settingsModalOpen = false"></div>

                {{-- Modal Panel --}}
                <div x-show="settingsModalOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 sm:p-7 border border-gray-100">
                    
                    {{-- Header --}}
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <i class="fa-solid fa-sliders text-base"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 font-sans leading-tight">
                                    Section Settings: <span class="text-emerald-600" x-text="form.name"></span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Configure layout style and product display limits for this section.</p>
                            </div>
                        </div>
                        <button type="button" @click="settingsModalOpen = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    {{-- Form Body --}}
                    <div class="mt-6 space-y-5">
                        {{-- Layout Style Radios --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Display Layout</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Grid Layout Option --}}
                                <label class="relative flex items-center gap-3 rounded-xl border-2 p-3.5 cursor-pointer transition select-none"
                                    :class="form.home_layout === 'grid' ? 'border-emerald-500 bg-emerald-50/20' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="modal_layout" value="grid" x-model="form.home_layout" class="text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Grid Layout</p>
                                        <p class="text-[11px] text-gray-500">With Load More button</p>
                                    </div>
                                </label>

                                {{-- Slider Layout Option --}}
                                <label class="relative flex items-center gap-3 rounded-xl border-2 p-3.5 cursor-pointer transition select-none"
                                    :class="form.home_layout === 'slider' ? 'border-emerald-500 bg-emerald-50/20' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="modal_layout" value="slider" x-model="form.home_layout" class="text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Slider Layout</p>
                                        <p class="text-[11px] text-gray-500">Horizontal swipe carousel</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Limits Inputs --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Max Products</label>
                                <input type="number" min="1" max="500" x-model="form.home_max_products"
                                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm font-semibold text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                                <p class="text-[11px] text-gray-400 mt-1">Total max products to show</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Per Page (Load More)</label>
                                <input type="number" min="1" max="100" x-model="form.home_per_page"
                                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm font-semibold text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                                <p class="text-[11px] text-gray-400 mt-1">Products loaded per click</p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="mt-7 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="settingsModalOpen = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="button" @click="saveSettings()"
                            class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast Notification --}}
        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-3 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-3 scale-95" style="display: none;"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl bg-gray-900/95 backdrop-blur-sm text-white px-5 py-3 shadow-2xl border border-gray-800">
            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white text-xs">
                <i class="fa-solid fa-check"></i>
            </span>
            <span class="text-xs font-semibold" x-text="toast.message"></span>
        </div>

    </div>

    @push('scripts')
    <script>
        function categorySectionsManager() {
            return {
                search: '{{ request('search', '') }}',
                status: '{{ request('status', '') }}',
                level: '{{ request('level', '') }}',
                settingsModalOpen: false,
                activeCategory: null,
                activeCount: {{ $activeOnHomeCount }},
                form: {
                    id: null,
                    name: '',
                    home_layout: 'slider',
                    home_max_products: 100,
                    home_per_page: 20
                },
                toast: { show: false, message: '' },
                showToast(msg) {
                    this.toast.message = msg;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 2500);
                },
                openSettings(cat) {
                    this.activeCategory = cat;
                    this.form.id = cat.id;
                    this.form.name = cat.name;
                    this.form.home_layout = cat.home_layout || 'slider';
                    this.form.home_max_products = cat.home_max_products || 100;
                    this.form.home_per_page = cat.home_per_page || 20;
                    this.settingsModalOpen = true;
                },
                saveSettings() {
                    if (!this.form.id) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    fetch('/admin/homepage/category-sections/' + this.form.id + '/settings', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            home_layout: this.form.home_layout,
                            home_max_products: this.form.home_max_products,
                            home_per_page: this.form.home_per_page
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(() => alert('Failed to save settings.'));
                },
                toggleHome(cat, event) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    fetch('/admin/homepage/category-sections/' + cat.id + '/toggle', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            cat.show_on_home = data.show_on_home;
                            this.activeCount = data.active_count;
                            this.showToast(data.show_on_home 
                                ? '"' + cat.name + '" section is now visible on Homepage' 
                                : '"' + cat.name + '" section is now hidden from Homepage');
                        }
                    })
                    .catch(() => alert('Failed to toggle category section.'));
                },
                reorder(id, direction) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    fetch('/admin/homepage/category-sections/reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: id, direction: direction })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(() => alert('Failed to reorder category section.'));
                }
            };
        }
    </script>
    @endpush
</x-admin-layout>
