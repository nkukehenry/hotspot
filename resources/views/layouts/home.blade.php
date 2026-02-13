<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!isset($site))
        <meta name="branding-mode" content="global">
    @endif

    <title>{{ isset($site) ? $site->name : ($settings?->system_name ?? config('app.name', 'Neonet')) }} - Wifi</title>
    <meta name="description" content="{{ isset($site) ? 'Connect to ' . $site->name . ' internet access.' : 'Welcome to ' . ($settings?->system_name ?? config('app.name', 'Neonet')) . ' - High Speed Wi-Fi Hotspot.' }}">
    <link rel="icon" type="image/png" href="{{ (isset($settings) && $settings->logo) ? asset('storage/' . $settings->logo) : asset('images/logo.png') }}">

    <!-- Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <script>
        // Global Site Context Manager
        (function() {
            const siteData = @json($site ?? null);
            if (siteData) {
                // Update cache with current site
                localStorage.setItem('last_site', JSON.stringify({
                    name: siteData.name,
                    logo: siteData.logo,
                    slug: siteData.slug || siteData.site_code,
                    timestamp: Date.now()
                }));
            }

            // Client-side hydration of branding if site is missing
            document.addEventListener('DOMContentLoaded', () => {
                const cachedSite = JSON.parse(localStorage.getItem('last_site'));
                const hasSiteData = @json(isset($site));
                const isSitesPage = @json(request()->routeIs('customer.sites'));
                const brandingMode = document.querySelector('meta[name="branding-mode"]')?.content;
                const isGlobalMode = brandingMode === 'global';
                
                // Only restore cached site if we are NOT in global mode and NOT on sites page
                if (!hasSiteData && !isSitesPage && !isGlobalMode && cachedSite) {
                    // Update header/footer logos and names from cache
                    const dynamicLogos = document.querySelectorAll('.dynamic-site-logo');
                    const dynamicNames = document.querySelectorAll('.dynamic-site-name');
                    const dynamicLinks = document.querySelectorAll('.dynamic-site-link');

                    dynamicLogos.forEach(img => {
                        if (cachedSite.logo) {
                            img.src = `{{ asset('storage') }}/${cachedSite.logo}`;
                        }
                    });

                    dynamicNames.forEach(span => {
                        span.textContent = cachedSite.name;
                    });

                    dynamicLinks.forEach(a => {
                        a.href = `{{ url('/wifi') }}/${cachedSite.slug}`;
                    });
                }
            });
        })();
    </script>
</head>

<body class="bg-gray-200 dark:bg-gray-900"
    style="background-image:url({{ asset('images/bg.webp') }})!important; background-size:cover!important; background-repeat:no-repeat; min-height:100vh; margin:0px; padding:0px; overflow-y:auto; background-position:center; background-attachment: fixed;">
    <nav class="bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="{{ isset($site) ? route('customer.packages', $site->slug ?? $site->site_code) : url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse dynamic-site-link">
                @if(isset($site) && $site->logo)
                    <img src="{{ asset('storage/' . $site->logo) }}" class="h-10 dynamic-site-logo" alt="{{ $site->name }}">
                @elseif(isset($settings) && $settings->logo)
                    <img src="{{ asset('storage/' . $settings->logo) }}" class="h-10 dynamic-site-logo" alt="{{ $settings->system_name }}">
                @else
                    <img src="{{ asset('images/logo.png') }}" class="h-10 dynamic-site-logo" alt="Logo">
                @endif
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white dynamic-site-name">{{ isset($site) ? $site->name : ($settings->system_name ?? 'Neonet WiFi') }}</span>
            </a>
            <button data-collapse-toggle="navbar-default" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul
                    class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li>
                        <a href="{{ isset($site) ? route('customer.packages', $site->slug ?? $site->site_code) : url('/') }}"
                            class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-blue-500 dynamic-site-link"
                            aria-current="page">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('customer.transactions') }}"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white dark:hover:text-blue-500 dark:hover:bg-gray-700">My
                            Transactions</a>
                    </li>
                    {{-- <li>
                        <a href="{{ url('/contact') }}"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white dark:hover:text-blue-500 dark:hover:bg-gray-700">
                            {{ isset($site) && $site->contact_phone ? 'Call: ' . $site->contact_phone : 'Contact Us' }}
                        </a>
                    </li> --}}
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <footer
        class="fixed bottom-0 left-0 z-20 w-full p-2 bg-white/80 border-t border-gray-200 shadow md:flex md:items-center md:justify-between md:px-6 dark:bg-gray-800/80 dark:border-gray-600 backdrop-blur-sm">
        <span class="text-[11px] text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }} <a
                href="{{ isset($site) ? route('customer.packages', $site->slug ?? $site->site_code) : url('/') }}" class="hover:underline font-bold dynamic-site-link dynamic-site-name">{{ isset($site) ? $site->name : ($settings->system_name ?? 'Neonet') }}</a>. All Rights Reserved.
        </span>
        <ul class="flex flex-wrap items-center mt-3 text-[11px] font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
            <li>
                <a href="{{ isset($site) ? route('customer.packages', $site->slug ?? $site->site_code) : url('/') }}" class="hover:underline me-4 md:me-6 dynamic-site-link">Home</a>
            </li>
            <li>
                <a href="{{ route('customer.sites') }}" class="hover:underline me-4 md:me-6">Sites</a>
            </li>
            <li>
                <a href="{{ route('customer.transactions') }}" class="hover:underline me-4 md:me-6">My Transactions</a>
            </li>
            @guest
            <li>
                <a href="{{ url('/admin') }}" class="hover:underline">Login</a>
            </li>
            @endguest
            @auth
            <li>
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
            </li>
            @endauth
        </ul>
    </footer>

    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
</body>

</html>
