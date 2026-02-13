<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings?->system_name ?? config('app.name', 'Neonet') }} - Admin</title>
    <meta name="description" content="Admin Dashboard for {{ $settings?->system_name ?? config('app.name', 'Neonet') }}">
    <link rel="icon" type="image/png" href="{{ (isset($settings) && $settings->logo) ? asset('storage/' . $settings->logo) : asset('images/logo.png') }}">
    <!-- Include Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css"
        integrity="sha512-QKC1UZ/ZHNgFzVKSAhV5v5j73eeL9EEN289eKAEFaAjgAiobVAnVv/AGuPbXsKl1dNoel3kNr6PYnSiTzVVBCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 dark:bg-gray-900">
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>
                    <a href="{{ Auth::user()->hasRole('Agent') ? route('agent.dashboard') : route('admin.dashboard') }}" class="flex ms-2 md:me-24">
                        @if(Auth::user()->site && Auth::user()->site->logo)
                            <img src="{{ asset('storage/' . Auth::user()->site->logo) }}" class="h-8 me-3" alt="{{ Auth::user()->site->name }}">
                            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">{{ Auth::user()->site->name }}</span>
                        @elseif(isset($settings) && $settings->logo)
                            <img src="{{ asset('storage/' . $settings->logo) }}" class="h-8 me-3" alt="{{ $settings->system_name }}">
                            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">{{ $settings->system_name }}</span>
                        @else
                            <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 me-3" alt="FlowBite Logo" />
                            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">{{ config('app.name', 'Neonet') }}</span>
                        @endif
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div>
                            <button type="button"
                                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full"
                                    src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                    alt="user photo">
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                            id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="{{ Auth::user()->hasRole('Agent') ? route('agent.dashboard') : route('admin.dashboard') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                        role="menuitem">Dashboard</a>
                                </li>
                                @can('manage_settings')
                                <li>
                                    <a href="{{ route('admin.settings') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                        role="menuitem">Settings</a>
                                </li>
                                @endcan
                                <li>
                                    <a href="{{ route('logout') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                        role="menuitem"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign
                                        out</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
        aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="{{ Auth::user()->hasRole('Agent') ? route('agent.dashboard') : route('admin.dashboard') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.dashboard') || request()->routeIs('agent.dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <i
                            class="icon-home w-5 h-5 transition duration-75 {{ request()->routeIs('admin.dashboard') || request()->routeIs('agent.dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="ms-3 {{ request()->routeIs('admin.dashboard') || request()->routeIs('agent.dashboard') ? 'font-bold' : '' }}">Dashboard</span>
                    </a>
                </li>

                @role('Agent')
                <li>
                    <a href="{{ route('admin.transactions') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.transactions') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <i
                            class="icon-list w-5 h-5 transition duration-75 {{ request()->routeIs('admin.transactions') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap {{ request()->routeIs('admin.transactions') ? 'font-bold' : '' }}">My Sales</span>
                    </a>
                </li>
                @endrole

                @canany(['manage_sites', 'view_sites','create_sites','edit_sites','delete_sites'])
                <li>
                    <a href="{{ route('admin.sites') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.sites*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <i
                            class="icon-location-pin w-5 h-5 transition duration-75 {{ request()->routeIs('admin.sites*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap {{ request()->routeIs('admin.sites*') ? 'font-bold' : '' }}">Sites</span>
                    </a>
                </li>
                @endcanany
                @can('view_packages')
                <li>
                    <a href="{{ route('admin.packages') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.packages*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <i
                            class="icon-basket w-5 h-5 transition duration-75 {{ request()->routeIs('admin.packages*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap {{ request()->routeIs('admin.packages*') ? 'font-bold' : '' }}">Packages</span>
                    </a>
                </li>
                @endcan
                @can('view_vouchers')
                <li>
                    <a href="{{ route('admin.vouchers') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.vouchers*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <i
                            class="icon-tag w-5 h-5 transition duration-75 {{ request()->routeIs('admin.vouchers*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap {{ request()->routeIs('admin.vouchers*') ? 'font-bold' : '' }}">Vouchers</span>
                    </a>
                </li>
                @endcan
                @can('view_users')
                <li>
                    <a href="{{ route('admin.users') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.users*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <i
                            class="icon-user w-5 h-5 transition duration-75 {{ request()->routeIs('admin.users*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap {{ request()->routeIs('admin.users*') ? 'font-bold' : '' }}">Users</span>
                    </a>
                </li>
                @endcan
                @canany(['view_reports', 'view_transactions'])
                <li>
                    <button type="button"
                        class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('admin.reports*') || request()->routeIs('admin.transactions*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}"
                        aria-controls="dropdown-reports" data-collapse-toggle="dropdown-reports">
                        <i
                            class="icon-chart w-5 h-5 transition duration-75 {{ request()->routeIs('admin.reports*') || request()->routeIs('admin.transactions*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="flex-1 ms-3 text-left whitespace-nowrap {{ request()->routeIs('admin.reports*') || request()->routeIs('admin.transactions*') ? 'font-bold' : '' }}">Reports & Analytics</span>
                        <svg class="w-3 h-3 text-gray-500 dark:text-gray-400 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-reports" class="{{ request()->routeIs('admin.reports*') || request()->routeIs('admin.transactions*') ? '' : 'hidden' }} py-2 space-y-2">
                        @can('view_reports')
                        <li>
                            <a href="{{ route('admin.reports') }}"
                                class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('admin.reports*') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-900' }}">Sales
                                Overview</a>
                        </li>
                        @endcan
                        @can('view_transactions')
                        <li>
                            <a href="{{ route('admin.transactions') }}"
                                class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('admin.transactions*') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-900' }}">Transaction
                                History</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @role('Owner')
                <li>
                    <a href="{{ route('admin.roles.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.roles*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <i
                            class="icon-shield w-5 h-5 transition duration-75 {{ request()->routeIs('admin.roles*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap {{ request()->routeIs('admin.roles*') ? 'font-bold' : '' }}">Roles & Permissions</span>
                    </a>
                </li>
                @endrole

                @can('manage_settings')
                <li>
                    <a href="{{ route('admin.settings') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.settings*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <i
                            class="icon-settings w-5 h-5 transition duration-75 {{ request()->routeIs('admin.settings*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap {{ request()->routeIs('admin.settings*') ? 'font-bold' : '' }}">Settings</span>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </aside>

    <div class="px-4 sm:ml-64">
        <div class="py-4 mt-14">
            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>

</html>
