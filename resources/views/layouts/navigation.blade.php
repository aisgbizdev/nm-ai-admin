<nav class="relative z-50" x-cloak>
    @php
        $user = Auth::user();

        $menu = [
            [
                'title' => 'Dashboard',
                'items' => [
                    [
                        'href' => route('dashboard'),
                        'icon' => '<i class="fas fa-home"></i>',
                        'text' => __('Dashboard'),
                        'active' => 'dashboard',
                    ],
                ],
            ],
            [
                'title' => 'Menu',
                'items' => [
                    [
                        'href' => route('knowledge.index'),
                        'icon' => '<i class="fa-solid fa-brain"></i>',
                        'text' => __('Knowladge'),
                        'active' => 'knowledge.*',
                    ],
                    [
                        'href' => route('history.index'),
                        'icon' => '<i class="fa-solid fa-comments"></i>',
                        'text' => __('History Chat'),
                        'active' => 'history.*',
                    ],
                ],
            ],
        ];

        // Tambah menu khusus Superadmin
        if ($user && $user->role === 'Superadmin') {
            $menu[] = [
                'title' => 'Manajemen',
                'items' => [
                    [
                        'href' => route('admin.index'),
                        'icon' => '<i class="fa-solid fa-users"></i>',
                        'text' => __('Admin'),
                        'active' => 'admin.*',
                    ],
                ],
            ];
        }
    @endphp


    {{-- =========================================================
        =============== [ SIDEBAR AREA ] ========================
        ========================================================= --}}

    {{-- SIDEBAR MOBILE (off-canvas) --}}
    <div x-show="sidebarOpen && !isDesktop" x-transition:enter="transition transform ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-blue-50 to-indigo-100 border-r border-zinc-300 sm:hidden z-40">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between h-16 px-4 py-2 border-b border-blue-200">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('Icon/favicon-96x96.png') }}" alt="Logo Newsmaker23"
                        class="h-auto w-[50px] object-contain max-w-full select-none">

                    <span class="font-bold text-gray-800 text-xl tracking-tight text-center select-none"
                        x-show="!sidebarCollapsed">
                        Gwen Stacy
                    </span>
                </a>

                <button @click="sidebarOpen = false"
                    class="p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col justify-between h-full px-4 py-4 space-y-5 overflow-y-auto">
                <div>
                    @foreach ($menu as $group)
                        <nav>
                            <ul class="flex flex-col gap-2"
                                :class="{
                                    'mb-7': !sidebarCollapsed,
                                    'mb-2': sidebarCollapsed
                                }">
                                <li class="text-gray-500 uppercase tracking-wide px-1" x-show="!sidebarCollapsed"
                                    x-transition.opacity>
                                    <div class="flex items-center gap-2">
                                        <hr class="w-full border border-blue-200 rounded-full">
                                        <span class="font-semibold text-xs text-blue-400 select-none">
                                            {{ $group['title'] }}
                                        </span>
                                        <hr class="w-full border border-blue-200 rounded-full">
                                    </div>
                                </li>

                                @foreach ($group['items'] as $item)
                                    <li>
                                        <a href="{{ $item['href'] }}"
                                            class="flex items-center px-3 py-2 rounded transition duration-200
                                    {{ request()->routeIs($item['active'])
                                        ? 'bg-blue-300 text-gray-900 font-semibold'
                                        : 'text-gray-700 bg-white hover:bg-blue-300' }} select-none"
                                            :class="sidebarCollapsed ? 'justify-center' : 'space-x-3'">
                                            <span class="text-sm">{!! $item['icon'] !!}</span>

                                            <span class="text-sm" x-show="!sidebarCollapsed"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 translate-x-2"
                                                x-transition:enter-end="opacity-100 translate-x-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 translate-x-0"
                                                x-transition:leave-end="opacity-0 translate-x-2">
                                                {{ $item['text'] }}
                                            </span>
                                        </a>
                                    </li>
                                @endforeach

                                <li class="text-xs text-gray-500 uppercase tracking-wide px-1" x-show="sidebarCollapsed"
                                    x-transition.opacity>
                                    <div class="flex items-center gap-2">
                                        <hr class="w-full border border-blue-200 rounded-full">
                                    </div>
                                </li>
                            </ul>
                        </nav>
                    @endforeach
                </div>

                <div>
                    <nav>
                        <ul class="flex flex-col items-center gap-2">
                            <li class="w-full">
                                <div class="flex items-center gap-2">
                                    <hr class="w-full border border-blue-200 rounded-full">
                                    <span class="font-semibold text-xs text-blue-400 uppercase select-none">
                                        Settings
                                    </span>
                                    <hr class="w-full border border-blue-200 rounded-full">
                                </div>
                            </li>
                            <li class="w-full" x-data="{ isActive: @js(request()->routeIs('profile.*')) }">
                                <a href="{{ route('profile.index') }}"
                                    class="flex w-full items-center rounded transition duration-200 select-none"
                                    :class="{
                                        /* NORMAL */
                                        'space-x-3 px-3 py-2': !sidebarCollapsed,
                                    
                                        /* COLLAPSED */
                                        'justify-center': sidebarCollapsed,
                                    
                                        /* ACTIVE – NORMAL */
                                        'bg-blue-300 text-gray-900 font-semibold':
                                            !sidebarCollapsed && isActive,
                                    
                                        /* ACTIVE – COLLAPSED */
                                        'bg-blue-100': sidebarCollapsed && isActive,
                                    
                                        /* INACTIVE – NORMAL */
                                        'text-gray-700 bg-white hover:bg-blue-50':
                                            !sidebarCollapsed && !isActive,
                                    }">
                                    <!-- AVATAR -->
                                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold shadow-sm transition"
                                        :class="{
                                            /* ACTIVE */
                                            'bg-white text-blue-700': isActive,
                                        
                                            /* INACTIVE */
                                            'bg-blue-400 text-gray-200 hover:bg-blue-500': !isActive,
                                        }">
                                        <span class="uppercase">
                                            {{ strtoupper(substr($user?->username ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>

                                    <!-- TEXT (HIDDEN SAAT COLLAPSED) -->
                                    <div x-show="!sidebarCollapsed" class="flex flex-col">
                                        <span class="font-semibold text-gray-800 truncate">
                                            {{ $user?->username ?? 'User' }}
                                        </span>
                                        <span class="text-xs text-gray-500 truncate">
                                            {{ $user?->role ?? 'User' }}
                                        </span>
                                    </div>
                                </a>
                            </li>

                            <!-- LOGOUT -->
                            <li class="w-full">
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center w-full rounded transition duration-200 text-gray-100 bg-red-500 hover:bg-red-600 select-none"
                                        :class="sidebarCollapsed ? 'justify-center py-2' : 'justify-center gap-3 px-3 py-2'">
                                        <i class="fa-solid fa-power-off text-sm" x-show="sidebarCollapsed"></i>

                                        <span class="text-sm font-semibold" x-show="!sidebarCollapsed" x-transition>
                                            LOG OUT
                                        </span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- SIDEBAR DESKTOP (SELALU ADA, CUMA BISA COLLAPSE) --}}
    <div class="hidden sm:flex sm:flex-col fixed inset-y-0 left-0 bg-gradient-to-b from-blue-50 to-indigo-100 border-r border-zinc-300 z-40 transition-all duration-300"
        :class="sidebarCollapsed ? 'w-20' : 'w-64'">
        <div class="flex items-center justify-between h-16 px-4 py-2 border-b border-blue-  00">
            <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center gap-3 text-center">
                <img src="{{ asset('Icon/favicon-96x96.png') }}" alt="Logo Newsmaker23"
                    class="h-auto w-[50px] object-contain max-w-full select-none">

                <span class="font-bold text-gray-800 text-xl tracking-tight text-center select-none"
                    x-show="!sidebarCollapsed">
                    Gwen Stacy
                </span>
            </a>
        </div>

        <div class="flex flex-col justify-between h-full px-4 py-4 transition-all duration-300 overflow-y-auto">
            <div>
                @foreach ($menu as $group)
                    <nav>
                        <ul class="flex flex-col gap-2"
                            :class="{
                                'mb-7': !sidebarCollapsed,
                                'mb-2': sidebarCollapsed
                            }">
                            <li class="text-gray-500 uppercase tracking-wide px-1" x-show="!sidebarCollapsed"
                                x-transition.opacity>
                                <div class="flex items-center gap-2">
                                    <hr class="w-full border border-blue-200 rounded-full">
                                    <span class="font-semibold text-blue-400 text-xs select-none">
                                        {{ $group['title'] }}
                                    </span>
                                    <hr class="w-full border border-blue-200 rounded-full">
                                </div>
                            </li>

                            @foreach ($group['items'] as $item)
                                <li>
                                    <a href="{{ $item['href'] }}"
                                        class="flex items-center px-3 py-2 rounded transition duration-200
                                    {{ request()->routeIs($item['active'])
                                        ? 'bg-blue-300 text-gray-900 font-semibold'
                                        : 'text-gray-700 bg-white hover:bg-blue-300' }} select-none"
                                        :class="sidebarCollapsed ? 'justify-center' : 'space-x-3'">
                                        <span class="text-sm">{!! $item['icon'] !!}</span>

                                        <span class="text-sm" x-show="!sidebarCollapsed"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-x-2"
                                            x-transition:enter-end="opacity-100 translate-x-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-x-0"
                                            x-transition:leave-end="opacity-0 translate-x-2">
                                            {{ $item['text'] }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach

                            <li class="text-xs text-gray-500 uppercase tracking-wide px-1" x-show="sidebarCollapsed"
                                x-transition.opacity>
                                <div class="flex items-center gap-2">
                                    <hr class="w-full border border-blue-200 rounded-full">
                                </div>
                            </li>
                        </ul>
                    </nav>
                @endforeach
            </div>
            <div>
                <nav>
                    <ul class="flex flex-col items-center gap-2">
                        <li class="w-full" x-show="!sidebarCollapsed">
                            <div class="flex items-center gap-2">
                                <hr class="w-full border border-blue-200 rounded-full">
                                <span class="font-semibold text-sm text-blue-400 select-none">
                                    Settings
                                </span>
                                <hr class="w-full border border-blue-200 rounded-full">
                            </div>
                        </li>
                        <li class="w-full" x-data="{ isActive: @js(request()->routeIs('profile.*')) }">
                            <a href="{{ route('profile.index') }}"
                                class="flex w-full items-center rounded transition duration-200 select-none"
                                :class="{
                                    /* NORMAL */
                                    'space-x-3 px-3 py-2': !sidebarCollapsed,
                                
                                    /* COLLAPSED */
                                    'justify-center': sidebarCollapsed,
                                
                                    /* ACTIVE – NORMAL */
                                    'bg-blue-300 text-gray-900 font-semibold':
                                        !sidebarCollapsed && isActive,
                                
                                    /* ACTIVE – COLLAPSED */
                                    'bg-blue-100': sidebarCollapsed && isActive,
                                
                                    /* INACTIVE – NORMAL */
                                    'text-gray-700 bg-white hover:bg-blue-50':
                                        !sidebarCollapsed && !isActive,
                                }">
                                <!-- AVATAR -->
                                <div class="inline-flex items-center justify-center min-w-10 min-h-10 rounded-full text-sm font-semibold shadow-sm transition"
                                    :class="{
                                        /* ACTIVE */
                                        'bg-white text-blue-700': isActive,
                                    
                                        /* INACTIVE */
                                        'bg-blue-400 text-gray-200 hover:bg-blue-500': !isActive,
                                    }">
                                    <span class="uppercase">
                                        {{ strtoupper(substr($user?->username ?? 'U', 0, 1)) }}
                                    </span>
                                </div>

                                <!-- TEXT (HIDDEN SAAT COLLAPSED) -->
                                <div x-show="!sidebarCollapsed" class="flex flex-col w-full">
                                    <span class="font-semibold text-gray-800 truncate mr-5">
                                        {{ $user?->username ?? 'User' }}
                                    </span>
                                    <span class="text-xs text-gray-500 truncate">
                                        {{ $user?->role ?? 'User' }}
                                    </span>
                                </div>
                            </a>
                        </li>

                        <!-- LOGOUT -->
                        <li class="w-full">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full rounded transition duration-200 text-gray-100 bg-red-500 hover:bg-red-600 select-none"
                                    :class="sidebarCollapsed ? 'justify-center py-2' : 'justify-center gap-3 px-3 py-2'">
                                    <i class="fa-solid fa-power-off text-sm" x-show="sidebarCollapsed"></i>

                                    <span class="text-sm font-semibold" x-show="!sidebarCollapsed" x-transition>
                                        LOG OUT
                                    </span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    {{-- OVERLAY MOBILE --}}
    <div x-show="sidebarOpen && !isDesktop" x-transition.opacity class="fixed inset-0 bg-black/40 z-30 sm:hidden"
        @click="sidebarOpen = false"></div>

    {{-- =========================================================
        =============== [ TOPBAR AREA ] =========================
        ========================================================= --}}

    {{-- TOPBAR: margin-left ikut desktop sidebar (mini/full) --}}
    <header class="fixed top-0 left-0 right-0 z-30 transition-all duration-300"
        :class="isDesktop ? (sidebarCollapsed ? 'sm:ml-20' : 'sm:ml-64') : 'sm:ml-0'">
        <div
            class="h-16 px-6 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-100 shadow-lg transition-all duration-300">

            {{-- KIRI: Buttons --}}
            <div class="flex items-center gap-3">
                {{-- Mobile hamburger --}}
                <button @click="sidebarOpen = !sidebarOpen"
                    class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': sidebarOpen, 'inline-flex': !sidebarOpen }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Desktop collapse (mini/full) --}}
                <button @click="toggleSidebarCollapse()"
                    class="hidden sm:inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 focus:outline-none transition duration-150 ease-in-out">
                    <span x-show="!sidebarCollapsed"><i class="fa-solid fa-angles-left"></i></span>
                    <span x-show="sidebarCollapsed"><i class="fa-solid fa-angles-right"></i></span>
                </button>

                <h1 class="text-lg font-bold select-none">@yield('header')</p>
            </div>
        </div>
    </header>
</nav>
