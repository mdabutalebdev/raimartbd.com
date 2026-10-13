<x-admin-layout title="Edit Page - Raimart Admin">
    <div class="mb-4">
        <a href="{{ route('admin.pages.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-orange transition cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back to Pages
        </a>
    </div>
    <h1 class="font-serif text-2xl font-bold">Edit Page</h1>

    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="mt-6 max-w-4xl">
        @method('PUT')
        @include('admin.pages._form', ['submitLabel' => 'Update Page'])
    </form>
</x-admin-layout>
