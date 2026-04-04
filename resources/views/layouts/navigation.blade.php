<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:ms-10 space-x-16">

                    {{-- Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- Master Data Dropdown --}}
                    <div class="relative" x-data="{ openMaster: false }" @mouseenter="openMaster = true" @mouseleave="openMaster = false">
                        <button
                            @click="openMaster = !openMaster"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition duration-150 ease-in-out border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none"
                        >
                            {{ __('Master Data') }}
                            <svg class="ms-1 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': openMaster }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            x-show="openMaster"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 z-50 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 py-1"
                            style="display: none;"
                        >
                            <x-dropdown-link href="#">
                                {{ __('Produk') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                {{ __('Sales') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                {{ __('Warung') }}
                            </x-dropdown-link>
                            @role('superadmin|admin')
                            <x-dropdown-link :href="route('users.index')" 
                                            :active="request()->routeIs('users.*')">
                                {{ __('Manajemen User') }}
                            </x-dropdown-link>
                            @endrole 
                        </div>
                    </div>

                    {{-- Operasional Dropdown --}}
                    <div class="relative" x-data="{ openOps: false }" @mouseenter="openOps = true" @mouseleave="openOps = false">
                        <button
                            @click="openOps = !openOps"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition duration-150 ease-in-out border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none"
                        >
                            {{ __('Operasional') }}
                            <svg class="ms-1 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': openOps }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            x-show="openOps"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 z-50 mt-1 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 py-1"
                            style="display: none;"
                        >
                            <x-dropdown-link href="#">
                                {{ __('Titipan Barang') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                {{ __('Pengelolaan Stok Sales') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                {{ __('Reminder Restock') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('maps.index')">
                                {{ __('Map View') }}
                            </x-dropdown-link>
                        </div>
                    </div>

                    {{-- Laporan Dropdown --}}
                    <div class="relative" x-data="{ openLaporan: false }" @mouseenter="openLaporan = true" @mouseleave="openLaporan = false">
                        <button
                            @click="openLaporan = !openLaporan"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition duration-150 ease-in-out border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none"
                        >
                            {{ __('Laporan') }}
                            <svg class="ms-1 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': openLaporan }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            x-show="openLaporan"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 z-50 mt-1 w-52 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 py-1"
                            style="display: none;"
                        >
                            <x-dropdown-link href="#">
                                {{ __('Laporan Titipan') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                {{ __('Laporan Penjualan') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="#">
                                {{ __('Laporan Profit') }}
                            </x-dropdown-link>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Settings Dropdown (User) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- Master Data Mobile --}}
            <div x-data="{ openMasterMobile: false }">
                <button @click="openMasterMobile = !openMasterMobile"
                    class="w-full flex justify-between items-center ps-3 pe-4 py-2 text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Master Data') }}
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': openMasterMobile }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openMasterMobile" class="ps-6 space-y-1" style="display:none;">
                    <x-responsive-nav-link href="#">{{ __('Produk') }}</x-responsive-nav-link>
                    <x-responsive-nav-link href="#">{{ __('Sales') }}</x-responsive-nav-link>
                    <x-responsive-nav-link href="#">{{ __('Warung') }}</x-responsive-nav-link>
                    @role('superadmin|admin')
                    <x-responsive-nav-link :href="route('users.index')"
                                        :active="request()->routeIs('users.*')">
                        {{ __('Manajemen User') }}
                    </x-responsive-nav-link>
                    @endrole
                </div>
            </div>

            {{-- Operasional Mobile --}}
            <div x-data="{ openOpsMobile: false }">
                <button @click="openOpsMobile = !openOpsMobile"
                    class="w-full flex justify-between items-center ps-3 pe-4 py-2 text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Operasional') }}
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': openOpsMobile }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openOpsMobile" class="ps-6 space-y-1" style="display:none;">
                    <x-responsive-nav-link href="#">{{ __('Titipan Barang') }}</x-responsive-nav-link>
                    <x-responsive-nav-link href="#">{{ __('Pengelolaan Stok Sales') }}</x-responsive-nav-link>
                    <x-responsive-nav-link href="#">{{ __('Reminder Restock') }}</x-responsive-nav-link>
                    <x-responsive-nav-link href="#">{{ __('Map View') }}</x-responsive-nav-link>
                </div>
            </div>

            {{-- Laporan Mobile --}}
            <div x-data="{ openLaporanMobile: false }">
                <button @click="openLaporanMobile = !openLaporanMobile"
                    class="w-full flex justify-between items-center ps-3 pe-4 py-2 text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Laporan') }}
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': openLaporanMobile }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openLaporanMobile" class="ps-6 space-y-1" style="display:none;">
                    <x-responsive-nav-link href="#">{{ __('Laporan Titipan') }}</x-responsive-nav-link>
                    <x-responsive-nav-link href="#">{{ __('Laporan Penjualan') }}</x-responsive-nav-link>
                    <x-responsive-nav-link href="#">{{ __('Laporan Profit') }}</x-responsive-nav-link>
                </div>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>