<x-admin-layout title="Steadfast API Settings & Order Tracker - Raimart Admin">
    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-brand-orange transition cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back to Settings
        </a>
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold text-sky-700 bg-sky-50 border border-sky-200/80 shadow-xs self-start sm:self-auto">
            <i class="fa-solid fa-shield-halved text-sky-500"></i>
            <span>Official Steadfast API v1</span>
        </div>
    </div>

    <!-- Status Messages -->
    @if (isset($errors) && $errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-xs">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-inside list-disc space-y-1 text-red-600 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-medium">{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Top 3 Metric / Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <!-- Server Status Card -->
        <div class="rounded-2xl bg-white p-5 border border-gray-100 shadow-xs flex items-center justify-between transition hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                    <i class="fa-solid fa-tower-broadcast text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">Steadfast Server</p>
                    <p id="top-server-status" class="text-base font-bold text-gray-800 mt-0.5">Ready to Test</p>
                </div>
            </div>
            <button type="button" onclick="runPing()" id="top-ping-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-gray-700 bg-gray-50 border border-gray-200 hover:bg-gray-100 transition cursor-pointer">
                <i class="fa-solid fa-wifi text-[11px] text-gray-500"></i>
                <span>Ping</span>
            </button>
        </div>

        <!-- Courier Balance Card -->
        <div class="rounded-2xl bg-white p-5 border border-gray-100 shadow-xs flex items-center justify-between transition hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">Courier Balance</p>
                    <p id="top-balance-amount" class="text-base font-bold text-gray-800 mt-0.5">—</p>
                    <p class="text-[11px] text-gray-400">Delivered COD payout ready</p>
                </div>
            </div>
            <button type="button" onclick="fetchBalance()" id="top-balance-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-emerald-700 bg-emerald-50/80 border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer">
                <i class="fa-solid fa-arrows-rotate text-[11px]"></i>
                <span>Check Balance</span>
            </button>
        </div>

        <!-- Credential Status Card -->
        <div class="rounded-2xl bg-white p-5 border border-gray-100 shadow-xs flex items-center justify-between transition hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                    <i class="fa-solid fa-key text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">Credential Status</p>
                    @if (!empty($steadfast->api_key) && !empty($steadfast->secret_key))
                        <p class="text-base font-bold text-gray-800 mt-0.5">Keys Configured</p>
                        <p class="text-[11px] text-gray-400">Ready for auto booking</p>
                    @else
                        <p class="text-base font-bold text-amber-600 mt-0.5">Keys Not Set</p>
                        <p class="text-[11px] text-gray-400">Enter API &amp; Secret Key</p>
                    @endif
                </div>
            </div>
            <div>
                @if ($steadfast->is_active && !empty($steadfast->api_key) && !empty($steadfast->secret_key))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> Active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                        <span class="h-2 w-2 rounded-full bg-gray-400"></span> Inactive
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Grid: Left Settings Form, Right Webhook & Diagnostics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: API Credentials & Webhook Settings Card (2 Cols) -->
        <div class="lg:col-span-2 rounded-2xl bg-white p-6 md:p-8 border border-gray-100 shadow-xs">
            <div class="flex items-start gap-3 pb-6 border-b border-gray-100">
                <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 mt-0.5">
                    <i class="fa-solid fa-sliders text-base"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">API Credentials &amp; Webhook Settings</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Collect your Api-Key and Secret-Key from your Steadfast merchant portal.</p>
                </div>
            </div>

            <form action="{{ route('admin.steadfast.update') }}" method="POST" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- API Key -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Steadfast API Key <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </div>
                        <input type="text" name="api_key" value="{{ old('api_key', $steadfast->api_key ?? '') }}"
                            placeholder="e.g. qdrng9p340hmzaiyeoxw@z4ovzf1giqh"
                            class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-4 text-sm text-gray-800 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">Provided in your Steadfast Portal &gt; Settings &gt; API Information.</p>
                </div>

                <!-- Secret Key -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Steadfast Secret Key <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" id="secret_key_input" name="secret_key" value="{{ old('secret_key', $steadfast->secret_key ?? '') }}"
                            placeholder="Enter Secret Key"
                            class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-11 text-sm text-gray-800 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition">
                        <button type="button" onclick="toggleSecretKeyVisibility()" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 cursor-pointer">
                            <i id="secret_key_icon" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">Keep this strictly secret. Used to sign and authorize parcel bookings.</p>
                </div>

                <!-- Webhook Auth Token -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Webhook Auth Token (Optional / Recommended)
                    </label>
                    <div class="relative flex items-center">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </div>
                        <input type="text" id="webhook_token_input" name="webhook_token" value="{{ old('webhook_token', $steadfast->webhook_token ?? '') }}"
                            placeholder="e.g. Np6IJ-NnIvnFaeDAA-tukMwr9fqboqBm"
                            class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-28 text-sm text-gray-800 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition">
                        <button type="button" onclick="generateWebhookToken()" class="absolute right-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200/80 transition flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-wand-magic-sparkles text-[11px] text-gray-500"></i> Generate
                        </button>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">Passed in Steadfast portal webhook settings. Steadfast will use this token to sign x-signature (HMAC-SHA256).</p>
                </div>

                <!-- Base API URL -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Base API URL
                        </label>
                        <button type="button" onclick="resetBaseUrl()" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 cursor-pointer">
                            Reset to default
                        </button>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-solid fa-globe text-sm"></i>
                        </div>
                        <input type="text" id="base_url_input" name="base_url" value="{{ old('base_url', $steadfast->base_url ?? 'https://portal.packzy.com/api/v1') }}"
                            class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-4 text-sm text-gray-800 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition font-mono">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">Default: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">https://portal.packzy.com/api/v1</code></p>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-3 pt-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $steadfast->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                    <span class="text-sm font-semibold text-gray-700">Enable Steadfast Automatic Booking &amp; Tracking</span>
                </div>

                <!-- Save Button -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm transition cursor-pointer">
                        <i class="fa-solid fa-floppy-disk text-base"></i>
                        <span>Save Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Callback URL & Live Diagnostics (1 Col) -->
        <div class="space-y-6">
            <!-- Webhook Callback URL Card -->
            <div class="rounded-2xl bg-white p-6 border border-gray-100 shadow-xs">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                        <i class="fa-solid fa-code-fork text-sm"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Webhook Callback URL</h3>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed mb-4">
                    Copy this callback URL and paste it into the Steadfast merchant portal Webhook settings to receive automatic parcel tracking &amp; status updates.
                </p>

                <div class="bg-gray-50 rounded-xl p-3 border border-gray-200/80 mb-4">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase mb-1.5">Your Callback URL</p>
                    <div class="flex items-center gap-2">
                        <input type="text" id="webhook_url_copy" readonly value="{{ url('/api/steadfast/webhook') }}"
                            class="w-full text-xs font-mono text-gray-700 bg-transparent border-none p-0 focus:outline-none select-all truncate">
                        <button type="button" onclick="copyWebhookUrl()" id="copy_webhook_btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-black text-white text-xs font-semibold shrink-0 transition cursor-pointer">
                            <i class="fa-regular fa-copy text-[11px]"></i> Copy
                        </button>
                    </div>
                </div>

                <div class="space-y-2 text-xs text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                        <span><strong>Method:</strong> POST (JSON)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                        <span><strong>Signature:</strong> X-Signature (HMAC-SHA256)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                        <span><strong>Events:</strong> delivery_status, tracking_update, etc.</span>
                    </div>
                </div>
            </div>

            <!-- Live Diagnostics Card -->
            <div class="rounded-2xl bg-white p-6 border border-gray-100 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-heart-pulse text-sm"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Live Diagnostics</h3>
                    </div>
                    <span class="text-xs text-gray-400">Real-time test</span>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button type="button" onclick="runPing()" id="diag_ping_btn" class="flex items-center justify-center gap-2 px-3 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition cursor-pointer">
                        <i class="fa-solid fa-tower-broadcast text-blue-500"></i>
                        <span>Test Ping</span>
                    </button>
                    <button type="button" onclick="fetchBalance()" id="diag_balance_btn" class="flex items-center justify-center gap-2 px-3 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs transition cursor-pointer">
                        <i class="fa-solid fa-bangladeshi-taka-sign text-emerald-600"></i>
                        <span>Get Balance</span>
                    </button>
                </div>

                <!-- Diagnostics Output Box -->
                <div id="diagnostics_output" class="hidden rounded-xl border border-gray-200 bg-gray-50 p-4 text-xs font-mono">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-200 mb-2">
                        <span class="font-bold text-gray-700 uppercase" id="diag_title">Result</span>
                        <span id="diag_time" class="text-gray-400 text-[10px]"></span>
                    </div>
                    <div id="diag_content" class="text-gray-700 break-words leading-relaxed"></div>
                </div>
            </div>

            <!-- Quick Consignment Tracker Card -->
            <div class="rounded-2xl bg-white p-6 border border-gray-100 shadow-xs">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="h-8 w-8 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Track Consignment</h3>
                </div>
                <div class="space-y-3">
                    <div class="relative">
                        <input type="text" id="tracking_code_input" placeholder="CID / Tracking / Invoice..."
                            class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3.5 text-xs text-gray-800 placeholder-gray-400 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                    </div>
                    <button type="button" onclick="trackManualConsignment()" id="track_btn" class="w-full flex items-center justify-center gap-2 rounded-xl bg-brand-navy hover:bg-brand-orange py-2.5 px-4 text-xs font-semibold text-white transition cursor-pointer">
                        <i class="fa-solid fa-magnifying-glass"></i> Track Order
                    </button>
                </div>
                <div id="quick_track_output" class="hidden mt-3 rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs"></div>
            </div>
        </div>
    </div>

    <!-- JavaScript Interactions -->
    <script>
        function toggleSecretKeyVisibility() {
            const input = document.getElementById('secret_key_input');
            const icon = document.getElementById('secret_key_icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function generateWebhookToken() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let token = 'SF-';
            for (let i = 0; i < 32; i++) {
                token += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('webhook_token_input').value = token;
        }

        function resetBaseUrl() {
            document.getElementById('base_url_input').value = 'https://portal.packzy.com/api/v1';
        }

        function copyWebhookUrl() {
            const copyInput = document.getElementById('webhook_url_copy');
            copyInput.select();
            copyInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyInput.value).then(() => {
                const btn = document.getElementById('copy_webhook_btn');
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-[11px] text-emerald-400"></i> Copied!';
                btn.classList.add('bg-emerald-800');
                setTimeout(() => {
                    btn.innerHTML = orig;
                    btn.classList.remove('bg-emerald-800');
                }, 2000);
            });
        }

        function showDiag(title, content, isSuccess) {
            const box = document.getElementById('diagnostics_output');
            const titleEl = document.getElementById('diag_title');
            const timeEl = document.getElementById('diag_time');
            const contentEl = document.getElementById('diag_content');

            box.classList.remove('hidden');
            titleEl.textContent = title;
            titleEl.className = 'font-bold uppercase ' + (isSuccess ? 'text-emerald-700' : 'text-red-600');
            timeEl.textContent = new Date().toLocaleTimeString();
            contentEl.innerHTML = content;
        }

        function runPing() {
            const topStatus = document.getElementById('top-server-status');
            const pingBtn = document.getElementById('top-ping-btn');
            const diagBtn = document.getElementById('diag_ping_btn');

            topStatus.innerHTML = '<span class="text-xs text-gray-500 font-normal"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Pinging...</span>';
            pingBtn.disabled = true;
            diagBtn.disabled = true;

            fetch("{{ route('admin.steadfast.ping') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                pingBtn.disabled = false;
                diagBtn.disabled = false;

                if (data.success) {
                    topStatus.innerHTML = `<span class="text-emerald-600 font-bold">${data.status}</span> <span class="text-xs text-gray-400 font-normal">(${data.latency}ms)</span>`;
                    showDiag('Ping Successful', `<strong>Status:</strong> ${data.status}<br><strong>Latency:</strong> ${data.latency}ms<br><strong>Message:</strong> ${data.message}`, true);
                    if (data.balance !== null && data.balance !== undefined) {
                        document.getElementById('top-balance-amount').textContent = '৳' + parseFloat(data.balance).toFixed(2);
                    }
                } else {
                    topStatus.innerHTML = `<span class="text-red-500 font-bold">${data.status || 'Failed'}</span>`;
                    showDiag('Ping Failed', `<strong>Error:</strong> ${data.message}`, false);
                }
            })
            .catch(err => {
                pingBtn.disabled = false;
                diagBtn.disabled = false;
                topStatus.innerHTML = '<span class="text-red-500 font-bold">Error</span>';
                showDiag('Network Error', err.message, false);
            });
        }

        function fetchBalance() {
            const balanceAmount = document.getElementById('top-balance-amount');
            const balanceBtn = document.getElementById('top-balance-btn');
            const diagBtn = document.getElementById('diag_balance_btn');

            balanceAmount.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm text-gray-400"></i>';
            balanceBtn.disabled = true;
            diagBtn.disabled = true;

            fetch("{{ route('admin.steadfast.balance') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                balanceBtn.disabled = false;
                diagBtn.disabled = false;

                if (data.success) {
                    balanceAmount.textContent = '৳' + data.balance;
                    showDiag('Balance Retrieved', `<strong>Current Withdrawable Balance:</strong> ৳${data.balance}<br><span class="text-gray-500">Ready for merchant payout.</span>`, true);
                } else {
                    balanceAmount.textContent = 'Error';
                    showDiag('Balance Check Failed', data.message, false);
                }
            })
            .catch(err => {
                balanceBtn.disabled = false;
                diagBtn.disabled = false;
                balanceAmount.textContent = 'Error';
                showDiag('Network Error', err.message, false);
            });
        }

        function trackManualConsignment() {
            const code = document.getElementById('tracking_code_input').value.trim();
            const output = document.getElementById('quick_track_output');

            if (!code) {
                alert('Please enter a Consignment ID or Tracking Code');
                return;
            }

            output.innerHTML = '<div class="py-2 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Checking Steadfast...</div>';
            output.classList.remove('hidden');

            fetch(`/admin/steadfast/track?code=${encodeURIComponent(code)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.tracking) {
                        output.innerHTML = `
                            <div class="space-y-1.5 font-sans">
                                <div class="flex justify-between pb-1 border-b border-gray-200">
                                    <span class="text-gray-500">Status:</span>
                                    <span class="font-bold text-emerald-600">${data.tracking.status}</span>
                                </div>
                                <div class="flex justify-between pb-1 border-b border-gray-200">
                                    <span class="text-gray-500">Consignment:</span>
                                    <span class="font-semibold text-gray-800">#${data.tracking.consignment_id}</span>
                                </div>
                                <div class="flex justify-between pb-1 border-b border-gray-200">
                                    <span class="text-gray-500">Tracking Code:</span>
                                    <span class="font-mono text-gray-800">${data.tracking.tracking_code}</span>
                                </div>
                                <div class="flex justify-between pb-1 border-b border-gray-200">
                                    <span class="text-gray-500">Recipient:</span>
                                    <span class="text-gray-800">${data.tracking.recipient_name} (${data.tracking.recipient_phone})</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">COD Amount:</span>
                                    <span class="font-bold text-gray-800">৳${data.tracking.cod_amount}</span>
                                </div>
                            </div>
                        `;
                    } else {
                        output.innerHTML = `<p class="text-red-600"><i class="fa-solid fa-circle-exclamation mr-1"></i> ${data.message || 'Consignment not found.'}</p>`;
                    }
                })
                .catch(err => {
                    output.innerHTML = `<p class="text-red-600"><i class="fa-solid fa-circle-exclamation mr-1"></i> Error: ${err.message}</p>`;
                });
        }
    </script>
</x-admin-layout>
