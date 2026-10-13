<x-admin-layout title="Laravel Logs - Raimart Admin">
    <div x-data="laravelLogManager({
        currentFile: '{{ $currentFile }}',
        lineCount: {{ $lineCount }},
        totalEntries: {{ $stats['total_entries'] }}
    })" class="space-y-6">

        {{-- Top Navigation & Title --}}
        <div>
            <div class="mb-3">
                <a href="{{ route('admin.settings.index') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-orange transition cursor-pointer">
                    <i class="fa-solid fa-arrow-left"></i> Back to Settings
                </a>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="font-serif text-2xl font-bold text-gray-900">Laravel Logs</h1>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ app()->environment('production') ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ app()->environment('production') ? 'bg-amber-500' : 'bg-emerald-500 animate-pulse' }}"></span>
                            {{ strtoupper(app()->environment()) }}
                        </span>
                        @if ($isWritable)
                            <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                <i class="fa-solid fa-check text-[10px]"></i> Writable
                            </span>
                        @else
                            <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200" title="File permissions do not allow writing">
                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Read-Only
                            </span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">
                        View, live-edit, filter, download, or clear Laravel application log files in real time.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Refresh Button --}}
                    <a href="{{ route('admin.logs.index', ['file' => $currentFile]) }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-2xs hover:bg-gray-50 hover:text-brand-navy transition cursor-pointer"
                        title="Reload log file">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span class="hidden md:inline">Refresh</span>
                    </a>

                    {{-- Download Button --}}
                    @if ($fileExists && $fileSize > 0)
                        <a href="{{ route('admin.logs.download', ['file' => $currentFile]) }}"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-2xs hover:bg-gray-50 hover:text-brand-orange transition cursor-pointer"
                            title="Download this log file">
                            <i class="fa-solid fa-download"></i>
                            <span class="hidden md:inline">Download</span>
                        </a>
                    @endif

                    {{-- Test Log Entry Modal Trigger --}}
                    <button type="button" @click="testModalOpen = true"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-purple-200 bg-purple-50 px-3 py-2 text-xs font-semibold text-purple-700 shadow-2xs hover:bg-purple-100 transition cursor-pointer">
                        <i class="fa-solid fa-flask"></i>
                        <span>Test Entry</span>
                    </button>

                    {{-- Clear Log Button --}}
                    @if ($fileExists && $fileSize > 0)
                        <button type="button" @click="clearModalOpen = true"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 shadow-2xs hover:bg-rose-100 transition cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Clear Log</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Alerts & Notifications --}}
        @if (session('status'))
            <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-2xs">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('status') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow-2xs">
                <div class="flex items-center gap-2 font-semibold">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Notice:</span>
                </div>
                <ul class="mt-1 list-inside list-disc text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($isTruncated)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800">
                <div class="flex items-center gap-2 font-semibold text-sm">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                    <span>Large Log File Detected</span>
                </div>
                <p class="mt-1">
                    This file is large (<strong>{{ $formattedSize }}</strong>). To protect browser performance and memory, only the most recent 10 MB is loaded for editing.
                </p>
            </div>
        @endif

        {{-- Stats and Selector Header Bar --}}
        <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-2xs">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                {{-- File Selector --}}
                <div class="flex flex-wrap items-center gap-3">
                    <label for="log_file_selector" class="text-xs font-bold uppercase tracking-wider text-gray-500">
                        <i class="fa-solid fa-folder-open text-brand-orange mr-1"></i> Log File:
                    </label>
                    <div class="relative min-w-[240px]">
                        <select id="log_file_selector"
                            @change="switchFile($event.target.value)"
                            class="w-full appearance-none rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2 pr-9 text-xs font-semibold text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none cursor-pointer">
                            @forelse ($files as $file)
                                <option value="{{ $file['name'] }}" {{ $file['name'] === $currentFile ? 'selected' : '' }}>
                                    {{ $file['name'] }} ({{ $file['formatted_size'] }})
                                </option>
                            @empty
                                <option value="laravel.log">laravel.log (0 B)</option>
                            @endforelse
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
                    </div>

                    <span class="text-xs text-gray-400">
                        @if ($lastModified)
                            Last updated {{ \Carbon\Carbon::createFromTimestamp($lastModified)->diffForHumans() }}
                        @else
                            No logs recorded
                        @endif
                    </span>
                </div>

                {{-- Mode Switcher Tabs --}}
                <div class="flex items-center rounded-xl bg-gray-100 p-1">
                    <button type="button"
                        @click="activeTab = 'editor'"
                        :class="activeTab === 'editor' ? 'bg-white text-gray-900 shadow-2xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-xs transition cursor-pointer">
                        <i class="fa-solid fa-code"></i>
                        <span>Editor & Save</span>
                    </button>
                    <button type="button"
                        @click="activeTab = 'viewer'"
                        :class="activeTab === 'viewer' ? 'bg-white text-gray-900 shadow-2xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-xs transition cursor-pointer">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Log Stream ({{ count($entries) }})</span>
                    </button>
                </div>
            </div>

            {{-- Metric Chips --}}
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6 border-t border-gray-100 pt-4">
                <div class="rounded-xl bg-gray-50/80 p-2.5 text-center">
                    <span class="block text-[11px] font-medium text-gray-500">File Size</span>
                    <span class="mt-0.5 block text-sm font-bold text-gray-900">{{ $formattedSize }}</span>
                </div>
                <div class="rounded-xl bg-gray-50/80 p-2.5 text-center">
                    <span class="block text-[11px] font-medium text-gray-500">Total Lines</span>
                    <span class="mt-0.5 block text-sm font-bold text-gray-900" x-text="editorLines">{{ number_format($lineCount) }}</span>
                </div>
                <div class="rounded-xl bg-rose-50/60 p-2.5 text-center">
                    <span class="block text-[11px] font-medium text-rose-700">Errors</span>
                    <span class="mt-0.5 block text-sm font-bold text-rose-700">{{ $stats['total_errors'] }}</span>
                </div>
                <div class="rounded-xl bg-amber-50/60 p-2.5 text-center">
                    <span class="block text-[11px] font-medium text-amber-700">Warnings</span>
                    <span class="mt-0.5 block text-sm font-bold text-amber-700">{{ $stats['total_warnings'] }}</span>
                </div>
                <div class="rounded-xl bg-sky-50/60 p-2.5 text-center">
                    <span class="block text-[11px] font-medium text-sky-700">Info</span>
                    <span class="mt-0.5 block text-sm font-bold text-sky-700">{{ $stats['info'] }}</span>
                </div>
                <div class="rounded-xl bg-gray-50/80 p-2.5 text-center">
                    <span class="block text-[11px] font-medium text-gray-500">Entries</span>
                    <span class="mt-0.5 block text-sm font-bold text-gray-900">{{ count($entries) }}</span>
                </div>
            </div>
        </div>

        {{-- TAB 1: CODE EDITOR & LIVE UPDATE --}}
        <div x-show="activeTab === 'editor'" x-cloak class="space-y-3">
            <form id="logUpdateForm" action="{{ route('admin.logs.update') }}" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="file" value="{{ $currentFile }}">

                {{-- Editor Toolbar --}}
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-2.5 shadow-2xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-mono font-semibold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-md">
                            <i class="fa-regular fa-file-code text-brand-orange"></i>
                            storage/logs/{{ $currentFile }}
                        </span>

                        <span x-show="isDirty" x-cloak class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Unsaved Changes
                        </span>

                        {{-- Line wrapping toggle --}}
                        <button type="button" @click="wordWrap = !wordWrap"
                            :class="wordWrap ? 'bg-gray-200 text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-700'"
                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs transition cursor-pointer"
                            title="Toggle Word Wrap">
                            <i class="fa-solid fa-arrows-left-right-to-line text-[11px]"></i>
                            <span class="hidden sm:inline">Wrap</span>
                        </button>

                        {{-- Jump to Top / Bottom --}}
                        <button type="button" @click="scrollToTop()"
                            class="rounded-lg px-2 py-1 text-xs text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition cursor-pointer"
                            title="Scroll to start of logs">
                            <i class="fa-solid fa-arrow-up"></i> Top
                        </button>
                        <button type="button" @click="scrollToBottom()"
                            class="rounded-lg px-2 py-1 text-xs text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition cursor-pointer"
                            title="Scroll to newest logs (bottom)">
                            <i class="fa-solid fa-arrow-down"></i> Bottom
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="hidden md:inline text-[11px] text-gray-400">
                            Press <kbd class="rounded border border-gray-300 bg-gray-50 px-1 py-0.5 text-[10px] font-semibold text-gray-600">Ctrl</kbd> + <kbd class="rounded border border-gray-300 bg-gray-50 px-1 py-0.5 text-[10px] font-semibold text-gray-600">S</kbd> to save
                        </span>

                        {{-- Save Changes Button --}}
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-brand-orange px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-navy transition cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </div>

                {{-- Interactive Log Editor Textarea --}}
                <div class="relative rounded-2xl border border-gray-800 bg-[#0d1117] shadow-xl overflow-hidden">
                    <div class="flex items-center justify-between border-b border-gray-800/80 bg-[#161b22] px-4 py-2 text-xs text-gray-400">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-red-500/80"></span>
                            <span class="h-3 w-3 rounded-full bg-yellow-500/80"></span>
                            <span class="h-3 w-3 rounded-full bg-green-500/80"></span>
                            <span class="ml-2 font-mono text-[11px] text-gray-400">Log File Editor</span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px]">
                            <span class="text-gray-400 font-mono">Lines: <strong class="text-gray-200" x-text="editorLines">{{ $lineCount }}</strong></span>
                            <span class="text-gray-400 font-mono">Chars: <strong class="text-gray-200" x-text="editorChars">{{ strlen($rawContent) }}</strong></span>
                        </div>
                    </div>

                    <textarea
                        x-ref="editor"
                        name="content"
                        id="rawLogEditor"
                        x-model="content"
                        @input="handleInput()"
                        @keydown="handleKeydown($event)"
                        :wrap="wordWrap ? 'soft' : 'off'"
                        spellcheck="false"
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        placeholder="Log file is empty..."
                        class="w-full min-h-[560px] max-h-[75vh] resize-y bg-[#0d1117] p-4 font-mono text-xs sm:text-[13px] leading-relaxed text-[#e6edf3] focus:outline-none focus:ring-0 selection:bg-brand-orange/30 whitespace-pre font-normal"
                    >{{ $rawContent }}</textarea>
                </div>

                {{-- Bottom Action Floating / Footer Bar --}}
                <div class="flex items-center justify-between rounded-xl bg-white p-4 border border-gray-200 shadow-2xs">
                    <p class="text-xs text-gray-500">
                        Editing will immediately rewrite <code class="font-bold text-gray-800">storage/logs/{{ $currentFile }}</code>.
                    </p>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-brand-orange px-6 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-navy transition cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- TAB 2: FORMATTED LOG STREAM VIEWER --}}
        <div x-show="activeTab === 'viewer'" x-cloak class="space-y-4">
            {{-- Stream Filter Bar --}}
            <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-2xs sm:flex-row sm:items-center sm:justify-between">
                {{-- Search Box --}}
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text"
                        x-model="searchQuery"
                        placeholder="Search log messages, exceptions, files, timestamps..."
                        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-9 pr-4 py-2 text-xs font-medium text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none">
                </div>

                {{-- Severity Level Pills --}}
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" @click="filterLevel = 'ALL'"
                        :class="filterLevel === 'ALL' ? 'bg-gray-900 text-white font-bold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer">
                        All ({{ count($entries) }})
                    </button>
                    <button type="button" @click="filterLevel = 'ERROR'"
                        :class="filterLevel === 'ERROR' ? 'bg-rose-600 text-white font-bold' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'"
                        class="rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer">
                        Errors ({{ $stats['total_errors'] }})
                    </button>
                    <button type="button" @click="filterLevel = 'WARNING'"
                        :class="filterLevel === 'WARNING' ? 'bg-amber-600 text-white font-bold' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                        class="rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer">
                        Warnings ({{ $stats['total_warnings'] }})
                    </button>
                    <button type="button" @click="filterLevel = 'INFO'"
                        :class="filterLevel === 'INFO' ? 'bg-sky-600 text-white font-bold' : 'bg-sky-50 text-sky-700 hover:bg-sky-100'"
                        class="rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer">
                        Info ({{ $stats['info'] }})
                    </button>
                    <button type="button" @click="filterLevel = 'DEBUG'"
                        :class="filterLevel === 'DEBUG' ? 'bg-gray-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer">
                        Debug ({{ $stats['debug'] }})
                    </button>
                </div>
            </div>

            {{-- Entries List --}}
            <div class="space-y-3">
                @forelse ($entries as $index => $entry)
                    @php
                        $lvl = strtoupper($entry['level']);
                        $isError = in_array($lvl, ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR']);
                        $isWarning = in_array($lvl, ['WARNING', 'NOTICE']);
                        $isInfo = $lvl === 'INFO';
                        
                        $badgeBg = $isError ? 'bg-rose-100 text-rose-800 border-rose-200' :
                                  ($isWarning ? 'bg-amber-100 text-amber-800 border-amber-200' :
                                  ($isInfo ? 'bg-sky-100 text-sky-800 border-sky-200' : 'bg-gray-100 text-gray-700 border-gray-200'));

                        $cardBorder = $isError ? 'hover:border-rose-300' :
                                      ($isWarning ? 'hover:border-amber-300' :
                                      ($isInfo ? 'hover:border-sky-300' : 'hover:border-gray-300'));
                    @endphp

                    <div x-data="{ expanded: false }"
                        x-show="matchesFilter('{{ addslashes($entry['level']) }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $entry['message'])) }}')"
                        class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-2xs transition-all {{ $cardBorder }}">

                        {{-- Entry Header --}}
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-2.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-md border px-2 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $badgeBg }}">
                                    {{ $entry['level'] }}
                                </span>
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">
                                    {{ $entry['env'] }}
                                </span>
                                <span class="font-mono text-xs font-semibold text-gray-500">
                                    <i class="fa-regular fa-clock mr-1 text-[11px]"></i>{{ $entry['timestamp'] }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                    @click="copyToClipboard(@js($entry['raw']))"
                                    class="rounded-lg p-1.5 text-xs text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition cursor-pointer"
                                    title="Copy raw entry">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                                @if (!empty($entry['stack_trace']))
                                    <button type="button" @click="expanded = !expanded"
                                        class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-200 transition cursor-pointer">
                                        <span x-text="expanded ? 'Hide Trace' : 'Stack Trace'"></span>
                                        <i class="fa-solid text-[9px]" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Entry Message --}}
                        <div class="mt-3">
                            <pre class="font-mono text-xs leading-relaxed text-gray-800 whitespace-pre-wrap break-all">{{ $entry['message'] }}</pre>
                        </div>

                        {{-- Collapsible Stack Trace --}}
                        @if (!empty($entry['stack_trace']))
                            <div x-show="expanded" x-collapse class="mt-3 rounded-xl border border-gray-800 bg-[#0d1117] p-3 shadow-inner">
                                <div class="mb-2 flex items-center justify-between text-[11px] text-gray-400">
                                    <span class="font-mono font-semibold">Stack Trace</span>
                                    <button type="button" @click="copyToClipboard(@js($entry['stack_trace']))"
                                        class="text-xs text-gray-400 hover:text-white cursor-pointer">
                                        <i class="fa-regular fa-copy mr-1"></i> Copy Trace
                                    </button>
                                </div>
                                <pre class="max-h-96 overflow-auto font-mono text-[11px] leading-relaxed text-gray-300 whitespace-pre-wrap">{{ $entry['stack_trace'] }}</pre>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <i class="fa-solid fa-file-circle-check text-2xl"></i>
                        </div>
                        <h3 class="mt-4 text-base font-bold text-gray-900">No Log Entries</h3>
                        <p class="mt-1 text-xs text-gray-500">The log file is clean or empty.</p>
                        <button type="button" @click="testModalOpen = true"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-orange px-4 py-2 text-xs font-semibold text-white hover:bg-brand-navy transition cursor-pointer">
                            <i class="fa-solid fa-plus"></i> Generate Test Entry
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- MODAL: TEST ENTRY GENERATOR --}}
        <div x-show="testModalOpen" style="display: none;"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
            <div @click.away="testModalOpen = false"
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-serif text-lg font-bold text-gray-900">
                        <i class="fa-solid fa-flask text-purple-600 mr-1.5"></i> Add Test Log Entry
                    </h3>
                    <button type="button" @click="testModalOpen = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form action="{{ route('admin.logs.test-entry') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="file" value="{{ $currentFile }}">

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Severity Level</label>
                        <select name="level" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-900 focus:border-brand-orange focus:outline-none">
                            <option value="info">INFO (General system information)</option>
                            <option value="warning">WARNING (Cautionary alert)</option>
                            <option value="error">ERROR (Exception or runtime failure)</option>
                            <option value="critical">CRITICAL (System urgent notice)</option>
                            <option value="debug">DEBUG (Detailed debug diagnostics)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Log Message</label>
                        <textarea name="message" rows="3"
                            placeholder="Enter a test error or log note to verify real-time logging..."
                            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs focus:border-brand-orange focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="testModalOpen = false"
                            class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-purple-600 px-5 py-2 text-xs font-bold text-white hover:bg-purple-700 transition cursor-pointer">
                            Write Log Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL: CLEAR LOG CONFIRMATION --}}
        <div x-show="clearModalOpen" style="display: none;"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
            <div @click.away="clearModalOpen = false"
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3 text-rose-600">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-serif text-lg font-bold text-gray-900">Clear Log File?</h3>
                        <p class="text-xs text-gray-500">This will erase all content in <code class="font-bold text-gray-700">{{ $currentFile }}</code>.</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Are you sure you want to empty this log file? This action cannot be undone. Consider downloading a copy first if you need it for historical debugging.
                </p>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="clearModalOpen = false"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">
                        Cancel
                    </button>
                    <form action="{{ route('admin.logs.clear') }}" method="POST">
                        @csrf
                        <input type="hidden" name="file" value="{{ $currentFile }}">
                        <button type="submit"
                            class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700 transition cursor-pointer">
                            Yes, Clear Log
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Script for Log Manager --}}
    <script>
        function laravelLogManager(config) {
            return {
                activeTab: 'editor',
                currentFile: config.currentFile,
                content: '',
                initialContent: '',
                isDirty: false,
                wordWrap: true,
                searchQuery: '',
                filterLevel: 'ALL',
                testModalOpen: false,
                clearModalOpen: false,

                init() {
                    const el = document.getElementById('rawLogEditor');
                    if (el) {
                        this.content = el.value;
                        this.initialContent = el.value;
                    }
                },

                get editorLines() {
                    if (!this.content) return 0;
                    return (this.content.match(/\n/g) || []).length + 1;
                },

                get editorChars() {
                    return this.content ? this.content.length : 0;
                },

                handleInput() {
                    this.isDirty = this.content !== this.initialContent;
                },

                handleKeydown(e) {
                    // Ctrl + S or Cmd + S to save
                    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                        e.preventDefault();
                        const form = document.getElementById('logUpdateForm');
                        if (form) {
                            form.submit();
                        }
                        return;
                    }

                    // Tab key indentation handling
                    if (e.key === 'Tab') {
                        e.preventDefault();
                        const textarea = this.$refs.editor;
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;

                        this.content = this.content.substring(0, start) + '    ' + this.content.substring(end);
                        this.handleInput();

                        this.$nextTick(() => {
                            textarea.selectionStart = textarea.selectionEnd = start + 4;
                        });
                    }
                },

                switchFile(filename) {
                    window.location.href = "{{ route('admin.logs.index') }}?file=" + encodeURIComponent(filename);
                },

                scrollToTop() {
                    const textarea = this.$refs.editor;
                    if (textarea) textarea.scrollTop = 0;
                },

                scrollToBottom() {
                    const textarea = this.$refs.editor;
                    if (textarea) textarea.scrollTop = textarea.scrollHeight;
                },

                copyToClipboard(text) {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text).then(() => {
                            alert('Copied to clipboard!');
                        }).catch(() => {
                            this.fallbackCopy(text);
                        });
                    } else {
                        this.fallbackCopy(text);
                    }
                },

                fallbackCopy(text) {
                    const textArea = document.createElement("textarea");
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        alert('Copied to clipboard!');
                    } catch (err) {}
                    document.body.removeChild(textArea);
                },

                matchesFilter(level, message) {
                    level = (level || '').toUpperCase();
                    message = (message || '').toLowerCase();
                    const query = this.searchQuery.toLowerCase().trim();

                    // Level filter
                    if (this.filterLevel !== 'ALL') {
                        if (this.filterLevel === 'ERROR' && !['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'].includes(level)) {
                            return false;
                        }
                        if (this.filterLevel === 'WARNING' && !['WARNING', 'NOTICE'].includes(level)) {
                            return false;
                        }
                        if (this.filterLevel === 'INFO' && level !== 'INFO') {
                            return false;
                        }
                        if (this.filterLevel === 'DEBUG' && level !== 'DEBUG') {
                            return false;
                        }
                    }

                    // Text search
                    if (query !== '' && !message.includes(query) && !level.includes(query)) {
                        return false;
                    }

                    return true;
                }
            }
        }
    </script>
</x-admin-layout>
