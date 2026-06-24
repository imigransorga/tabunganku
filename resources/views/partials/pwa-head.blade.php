{{-- Tag PWA: manifest, ikon, theme, dan service worker --}}
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#4f46e5">
<link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32.png">

{{-- iOS "Add to Home Screen" --}}
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Tabungan KIA">

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
</script>
