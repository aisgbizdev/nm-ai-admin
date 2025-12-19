<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (config('services.firebase.customToken'))
        <meta name="firebase-custom-token" content="{{ config('services.firebase.customToken') }}">
    @endif

    <title>@yield('title', config('app.name', 'Laravel')) - Gwen Stacy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="icon" type="image/png" href="{{ asset('Icon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('Icon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('Icon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('Icon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="NM23" />
    <link rel="manifest" href="{{ asset('Icon/site.webmanifest') }}" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div x-data="{
        sidebarOpen: false, // MOBILE ONLY (overlay)
        sidebarCollapsed: false, // DESKTOP ONLY (shrink)
        isDesktop: false,
        storageKey: 'nm-sidebar-collapsed',
        loadCollapsed() {
            const saved = localStorage.getItem(this.storageKey);
            return saved === 'true';
        },
        saveCollapsed(value) {
            localStorage.setItem(this.storageKey, value ? 'true' : 'false');
        },
        toggleSidebarCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            this.saveCollapsed(this.sidebarCollapsed);
        },
        init() {
            const mql = window.matchMedia('(min-width: 640px)');
    
            const apply = () => {
                this.isDesktop = mql.matches;
    
                if (this.isDesktop) {
                    // Desktop: sidebar selalu ada, jadi overlay dimatiin
                    this.sidebarOpen = false;
                    this.sidebarCollapsed = this.loadCollapsed();
                } else {
                    // Mobile: collapse ga kepake, reset biar rapi
                    this.sidebarCollapsed = false;
                }
            };
    
            apply();
            if (mql.addEventListener) mql.addEventListener('change', apply);
            else mql.addListener(apply);
    
            // ESC: nutup overlay mobile
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.sidebarOpen = false;
            });
        }
    }" x-init="init()">
        @include('layouts.navigation')

        {{-- CONTENT WRAPPER --}}
        <div class="min-h-screen pt-16 transition-all duration-300 bg-gradient-to-br from-indigo-500/10 via-sky-400/10 to-emerald-400/10"
            :class="isDesktop ? (sidebarCollapsed ? 'sm:ml-20' : 'sm:ml-64') : 'sm:ml-0'">

            <div class="absolute inset-x-0 top-[-6rem] -z-10 transform-gpu overflow-hidden blur-3xl">
                <div
                    class="relative left-1/2 aspect-[1108/632] w-[72rem] -translate-x-1/2 bg-gradient-to-br from-blue-500 via-indigo-400 to-cyan-400 opacity-25">
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1">
                @hasSection('content')
                    @yield('content')
                @elseif (isset($slot))
                    {{ $slot }}
                @endif
            </main>
        </div>
    </div>

    @yield('scripts')
</body>

</html>
