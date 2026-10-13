@php
    $pixelSettings = $pixelSettings ?? \App\Models\SiteSetting::getAll();
    $gtmId = trim((string) ($pixelSettings['gtm_container_id'] ?? ''));
@endphp

@if (!empty($gtmId) && $gtmId !== config('services.ga4.gtm_id'))
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif

@if (!empty($pixelSettings['custom_body_scripts']))
{!! $pixelSettings['custom_body_scripts'] !!}
@endif
