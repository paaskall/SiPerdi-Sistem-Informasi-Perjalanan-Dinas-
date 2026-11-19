<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIPERDI') }} - Super Admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-lanri.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])
    @livewireStyles

    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        
        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 16rem;
            background-color: #0f172a;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }
        .sidebar.open {
            transform: translateX(0);
        }

        /* OVERLAY */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 40;
            display: none;
        }
        .sidebar-overlay.open {
            display: block;
        }

        /* LAPTOP */
        @media (min-width: 1024px) {
            .sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                transform: translateX(0) !important;
                z-index: auto;
            }
            .sidebar-overlay {
                display: none !important;
            }
        }

        /* SCROLL DI TENGAH SIDEBAR */
        .sidebar-nav {
            flex: 1 1 0%;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.3);
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-700 antialiased">

    <!-- OVERLAY -->
    <div id="sidebarOverlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

    <div class="min-h-screen flex">
        
        <!-- SIDEBAR -->
        <aside id="sidebar" class="sidebar">
            
            <!-- BAGIAN ATAS: Logo + Tombol X -->
            <div class="h-16 flex items-center justify-between px-6 border-b shrink-0" style="border-color: #1e293b;">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('img/lan.png') }}" alt="Logo" class="h-8 w-auto brightness-0 invert">
                    <span class="text-lg font-extrabold tracking-tight text-white">SIPERDI</span>
                </a>
                <button onclick="closeSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- BAGIAN TENGAH: Menu -->
            <nav class="sidebar-nav px-4 py-4 space-y-1">
                @php
                    $menus = [
                        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label' => 'Buat UMK', 'route' => 'perjalanan.buat', 'icon' => 'M12 5v14M5 12h14'],
                        ['label' => 'Riwayat UMK', 'route' => 'perjalanan.riwayat', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['label' => 'Verifikasi UMK', 'route' => 'perjalanan.verifikasi-umk', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Verifikasi Laporan', 'route' => 'perjalanan.verifikasi-laporan', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['label' => 'Audit Trail', 'route' => 'audit.index', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Manajemen User', 'route' => 'master.users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ];
                    $masterMenus = [
                        ['label' => 'Divisi', 'route' => 'master.divisi'],
                        ['label' => 'Golongan', 'route' => 'master.golongan'],
                        ['label' => 'Pangkat', 'route' => 'master.pangkat'],
                        ['label' => 'COA', 'route' => 'master.coa'],
                        ['label' => 'Akun', 'route' => 'master.akun'],
                        ['label' => 'Kota', 'route' => 'master.kota'],
                        ['label' => 'Tarif', 'route' => 'master.tarif'],
                    ];
                @endphp

                @foreach ($menus as $menu)
                    @php $isActive = request()->routeIs($menu['route']); @endphp
                    <a href="{{ route($menu['route']) }}" 
                       onclick="closeSidebar()"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $isActive ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}"/></svg>
                        {{ $menu['label'] }}
                    </a>
                @endforeach

                <!-- Dropdown Master Data -->
                <div>
                    <button onclick="toggleMaster()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                            Master Data
                        </div>
                        <svg id="masterIcon" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="masterMenu" class="pl-11 pr-3 mt-1 space-y-1 hidden">
                        @foreach ($masterMenus as $m)
                            @php $isActive = request()->routeIs($m['route']); @endphp
                            <a href="{{ route($m['route']) }}" onclick="closeSidebar()" class="block px-3 py-2 rounded-md text-sm {{ $isActive ? 'bg-slate-800 text-blue-400' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">{{ $m['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            </nav>

            <!-- BAGIAN BAWAH: Profil + Logout -->
            <div class="p-4 border-t shrink-0" style="border-color: #1e293b;">
                <a href="{{ route('profile.edit') }}" onclick="closeSidebar(); openProfileModal(event)" class="flex items-center gap-3 mb-3 rounded-lg p-2 -m-2 hover:bg-slate-800 transition-colors group">
                    <div class="flex-shrink-0 bg-white flex items-center justify-center text-sm font-extrabold text-blue-900 shadow-sm leading-none" 
                         style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; min-height: 2.25rem; border-radius: 9999px; line-height: 1;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-white truncate group-hover:text-blue-300 transition-colors">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-xs text-slate-400 truncate">Super Admin</div>
                    </div>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-blue-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <button type="button" onclick="confirmLogout(event)" class="w-full flex items-center justify-center gap-2 rounded-lg bg-red-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header Mobile -->
            <header class="lg:hidden bg-white border-b border-slate-200 p-4 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button onclick="openSidebar()" class="text-slate-600 hover:text-slate-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="text-lg font-extrabold tracking-tight text-slate-900">SIPERDI</span>
                </div>
            </header>

            <!-- ✅ MAIN CONTENT (Mendukung Livewire & Blade biasa) -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    <!-- Form Logout -->
    <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    @livewireScripts

    @include('layouts.profile-modal', ['force' => true])
    @include('layouts.flash')

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('open');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
        }

        function toggleMaster() {
            const menu = document.getElementById('masterMenu');
            const icon = document.getElementById('masterIcon');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                menu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function confirmLogout(event) {
            if (event) event.preventDefault();

            if (typeof Swal === 'undefined') {
                if (confirm('Yakin ingin logout?')) {
                    document.getElementById('logout-form').submit();
                }
                return;
            }

            Swal.fire({
                title: 'Keluar Aplikasi?',
                text: 'Anda akan keluar dari sistem. Yakin ingin melanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'font-sans rounded-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang logout...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    document.getElementById('logout-form').submit();
                }
            });
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (data) => {
                const config = data[0] || data;
                Swal.fire({ title: config.title, text: config.text, icon: config.icon || 'info', confirmButtonColor: '#1e3a8a', ...config });
            });
            Livewire.on('swal:confirm', (data) => {
                const config = data[0] || data;
                Swal.fire({
                    title: config.title, text: config.text, icon: config.icon || 'warning', showCancelButton: true,
                    confirmButtonColor: '#dc2626', cancelButtonColor: '#64748b',
                    confirmButtonText: config.confirmButtonText || 'Ya', cancelButtonText: config.cancelButtonText || 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(config.method, config.params);
                    }
                });
            });
        });
    </script>
</body>
</html>