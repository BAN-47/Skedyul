@php($mobileApkAvailable = file_exists(public_path('downloads/skedyul-mobile.apk')))
<section class="sidebar-mobile-card" aria-label="SKEDYUL mobile app">
    <div class="sidebar-mobile-brand" aria-label="SKEDYUL">
        <span>SKED</span><span class="sidebar-mobile-brand-accent">YUL</span>
    </div>
    <div class="sidebar-mobile-phone-wrap" aria-hidden="true">
        <div class="sidebar-mobile-phone">
            <div class="sidebar-mobile-speaker"></div>
            <div class="sidebar-mobile-screen">
                <div class="sidebar-mobile-status"><span>09:41</span><span class="sidebar-mobile-signal"></span></div>
                <div class="sidebar-mobile-now">
                    <span>NOW ACTIVE</span>
                    <strong>CHEM 301 Lab</strong>
                    <small>Lab 4B · 10:30 AM</small>
                </div>
                <div class="sidebar-mobile-next"><span>NEXT CLASS</span><strong>SCI 101 Hall</strong></div>
                <div class="sidebar-mobile-sync"><i></i> Timetable Synced</div>
            </div>
        </div>
    </div>
    <p class="sidebar-mobile-copy">Your schedule, wherever you are.</p>
    @if ($mobileApkAvailable)
        <a href="{{ asset('downloads/skedyul-mobile.apk') }}?v={{ filemtime(public_path('downloads/skedyul-mobile.apk')) }}"
            download="Skedyul.apk" class="sidebar-mobile-download">↓ <span>Download APK</span></a>
    @else
        <span class="sidebar-mobile-download sidebar-mobile-download-disabled">Coming soon</span>
    @endif
</section>
