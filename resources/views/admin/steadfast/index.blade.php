<x-admin-layout title="Steadfast Order Tracker - Raimart Admin">
    <div class="flex items-center justify-between">
        <h1 class="font-serif text-2xl font-bold">Steadfast Order Tracker</h1>
    </div>

    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <!-- Settings Form -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">API Settings</h2>
            <form action="{{ route('admin.steadfast.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-sm font-medium">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key', $steadfast->api_key ?? '') }}"
                        class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none" placeholder="Enter API Key">
                </div>

                <div>
                    <label class="text-sm font-medium">Secret Key</label>
                    <input type="text" name="secret_key" value="{{ old('secret_key', $steadfast->secret_key ?? '') }}"
                        class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none" placeholder="Enter Secret Key">
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $steadfast->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-orange focus:ring-brand-orange">
                        <span class="text-sm font-medium">Enable Integration</span>
                    </label>
                </div>

                <button type="submit" class="w-full rounded-lg bg-brand-orange px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-navy transition">Save Settings</button>
            </form>
        </div>

        <!-- Tracker Form -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Track Consignment</h2>
            <form id="trackerForm" class="space-y-5">
                <div>
                    <label class="text-sm font-medium">Consignment ID / Tracking Code</label>
                    <input type="text" id="tracking_code" class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none" placeholder="e.g. 1524...">
                </div>
                
                <button type="button" onclick="trackOrder()" class="w-full flex items-center justify-center gap-2 rounded-lg bg-brand-navy px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-orange transition">
                    <i class="fa-solid fa-magnifying-glass"></i> Track Order
                </button>
            </form>

            <div id="trackingResult" class="mt-6 hidden rounded-lg border border-gray-200 bg-gray-50 p-4">
                <!-- Result will be populated here via JS -->
            </div>
        </div>
    </div>

    <script>
        function trackOrder() {
            const code = document.getElementById('tracking_code').value;
            const resultDiv = document.getElementById('trackingResult');
            
            if (!code) {
                alert('Please enter a tracking code');
                return;
            }

            resultDiv.innerHTML = '<div class="text-center py-4"><i class="fa-solid fa-spinner fa-spin text-brand-orange text-2xl"></i><p class="mt-2 text-sm text-gray-500">Fetching status...</p></div>';
            resultDiv.classList.remove('hidden');

            fetch(`/admin/steadfast/track?code=${code}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.tracking) {
                        resultDiv.innerHTML = `
                            <h3 class="font-bold text-gray-800 mb-2">Tracking Result</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Status:</span> <span class="font-bold text-brand-orange">${data.tracking.status}</span></div>
                                <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Consignment:</span> <span>${data.tracking.consignment_id}</span></div>
                                <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Customer:</span> <span>${data.tracking.recipient_name}</span></div>
                                <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Phone:</span> <span>${data.tracking.recipient_phone}</span></div>
                                <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Amount to collect:</span> <span>৳${data.tracking.cod_amount}</span></div>
                                <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Updated:</span> <span>${data.tracking.updated_at || '-'}</span></div>
                            </div>
                        `;
                    } else {
                        resultDiv.innerHTML = `<div class="text-red-500 text-sm py-2"><i class="fa-solid fa-triangle-exclamation"></i> ${data.message || 'Could not fetch tracking data. Please check your API keys or tracking code.'}</div>`;
                    }
                })
                .catch(err => {
                    resultDiv.innerHTML = `<div class="text-red-500 text-sm py-2"><i class="fa-solid fa-triangle-exclamation"></i> Error communicating with server.</div>`;
                });
        }
    </script>
</x-admin-layout>
