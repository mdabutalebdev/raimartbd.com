@php
    $pixelSettings = \App\Models\SiteSetting::getAll();
    $fbPixelEnabled = ($pixelSettings['fb_pixel_enabled'] ?? '1') === '1';
    $fbPixelId = trim((string) ($pixelSettings['fb_pixel_id'] ?? ''));
    $gtmId = trim((string) ($pixelSettings['gtm_container_id'] ?? config('services.ga4.gtm_id', '')));
    $ga4Id = trim((string) ($pixelSettings['ga4_measurement_id'] ?? ''));
    $tiktokId = trim((string) ($pixelSettings['tiktok_pixel_id'] ?? ''));
    $trackPageView = ($pixelSettings['pixel_track_pageview'] ?? '1') === '1';
    $advMatching = ($pixelSettings['pixel_advanced_matching'] ?? '1') === '1';

    $userData = [];
    if ($advMatching && auth()->check()) {
        $u = auth()->user();
        if (!empty($u->email)) $userData['em'] = hash('sha256', strtolower(trim($u->email)));
        if (!empty($u->phone)) $userData['ph'] = hash('sha256', preg_replace('/\D+/', '', $u->phone));
        if (!empty($u->name)) {
            $parts = explode(' ', trim($u->name));
            $userData['fn'] = hash('sha256', strtolower($parts[0]));
            if (count($parts) > 1) {
                $userData['ln'] = hash('sha256', strtolower(end($parts)));
            }
        }
    }
@endphp

{{-- Meta (Facebook) Pixel --}}
@if ($fbPixelEnabled && !empty($fbPixelId))
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ $fbPixelId }}', @json($userData));
@if ($trackPageView)
fbq('track', 'PageView');
@endif
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ $fbPixelId }}&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
@endif

{{-- Google Tag Manager --}}
@if (!empty($gtmId) && $gtmId !== config('services.ga4.gtm_id'))
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
<!-- End Google Tag Manager -->
@endif

{{-- Google Analytics 4 (GA4) --}}
@if (!empty($ga4Id))
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $ga4Id }}');
</script>
<!-- End GA4 -->
@endif

{{-- TikTok Pixel --}}
@if (!empty($tiktokId))
<!-- TikTok Pixel -->
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
  ttq.load('{{ $tiktokId }}');
  ttq.page();
}(window, document, 'ttq');
</script>
<!-- End TikTok Pixel -->
@endif

{{-- Helper for client-side event dispatching --}}
<script>
window.trackPixelEvent = function(eventName, data) {
    data = data || {};
    if (typeof window.fbq === 'function') {
        window.fbq('track', eventName, data);
    }
    if (typeof window.ttq !== 'undefined' && typeof window.ttq.track === 'function') {
        window.ttq.track(eventName, data);
    }
    if (typeof window.dataLayer !== 'undefined') {
        window.dataLayer.push(Object.assign({ event: eventName }, data));
    }
};
</script>

{{-- Custom Head Scripts --}}
@if (!empty($pixelSettings['custom_head_scripts']))
{!! $pixelSettings['custom_head_scripts'] !!}
@endif
