@php
    $user = Auth::user();
    $isSuperAdmin = $user->hasRole('super-admin');

    $mainLinks = [
        ['label' => 'Dashboard',          'route' => 'dashboard',                        'roles' => [], 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'Buat UMK',           'route' => 'perjalanan.buat',                  'roles' => ['pegawai', 'super-admin'], 'icon' => 'M12 5v14M5 12h14'],
        ['label' => 'Riwayat',            'route' => 'perjalanan.riwayat',               'roles' => ['pegawai', 'super-admin'], 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['label' => 'Laporan',            'route' => 'laporan.riwayat',                  'roles' => ['pegawai', 'super-admin'], 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label' => 'Verifikasi',         'route' => 'perjalanan.verifikasi-umk',        'roles' => ['keuangan', 'super-admin'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];

    $mainLinks = array_values(array_filter($mainLinks, function ($l) use ($user) {
        if (empty($l['roles'])) return true;
        foreach ($l['roles'] as $role) {
            if ($user->hasRole($role)) return true;
        }
        return false;
    }));

    $masterLinks = [
        ['label' => 'Divisi',   'route' => 'master.divisi'],
        ['label' => 'Golongan', 'route' => 'master.golongan'],
        ['label' => 'Pangkat',  'route' => 'master.pangkat'],
        ['label' => 'COA',      'route' => 'master.coa'],
        ['label' => 'Akun',     'route' => 'master.akun'],
        ['label' => 'Kota',     'route' => 'master.kota'],
        ['label' => 'Tarif',    'route' => 'master.tarif'],
    ];

    // Master data untuk keuangan & super-admin (sesuai route)
    $showMaster   = $user->hasRole('keuangan') || $isSuperAdmin;
    $masterActive = request()->routeIs('master.*');

    $linkBase   = 'inline-flex h-full items-center border-b-2 px-1 text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2';
    $linkOn     = 'border-blue-900 text-slate-900';
    $linkOff    = 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-800';
    $initial    = strtoupper(mb_substr($user->name, 0, 1));

    // Bottom bar mobile: 4 menu utama + tombol "Lainnya".
    // Menu sisa (mis. Verifikasi untuk super-admin) dipindah ke "Lainnya".
    $bottomLinks   = array_slice($mainLinks, 0, 4);
    $overflowLinks = array_slice($mainLinks, 4);
    $moreActive    = $masterActive
        || request()->routeIs('profile.*')
        || collect($overflowLinks)->contains(fn ($l) => request()->routeIs($l['route']));
@endphp

<!-- NAVBAR DESKTOP (Hanya Muncul di Layar Lebar) -->
<style>
    .dn-root { display: none; }
    @media (min-width: 1024px) { .dn-root { display: block; } }
    .dn-links { display: flex; gap: 24px; margin-left: 40px; }
    .dn-user  { display: flex; align-items: center; }
</style>
<nav class="dn-root sticky top-0 z-40 border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between sm:h-20">
            <div class="flex">
                <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-3 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 sm:gap-4">
                    <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-9 w-auto object-contain sm:h-11">
                    <span class="hidden h-8 w-px bg-slate-200 sm:block"></span>
                    <span class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">SIPERDI</span>
                </a>

                <div class="dn-links">
                    @foreach ($mainLinks as $l)
                        @php $on = request()->routeIs($l['route']); @endphp
                        <a href="{{ route($l['route']) }}" @if($on) aria-current="page" @endif
                           class="{{ $linkBase }} {{ $on ? $linkOn : $linkOff }}">{{ $l['label'] }}</a>
                    @endforeach

                    @if ($showMaster)
                        <div class="relative flex h-full items-center" x-data="{ o: false }" @click.outside="o = false" @keydown.escape.window="o = false">
                            <button type="button" @click="o = ! o" :aria-expanded="o"
                                    class="{{ $linkBase }} gap-1 {{ $masterActive ? $linkOn : $linkOff }}">
                                Master Data
                                <svg class="h-4 w-4 transition-transform" :class="o && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="o" x-transition.opacity style="display: none"
                                 class="absolute left-0 top-full z-50 -mt-px w-48 rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg">
                                @foreach ($masterLinks as $m)
                                    @php $on = request()->routeIs($m['route']); @endphp
                                    <a href="{{ route($m['route']) }}" @if($on) aria-current="page" @endif
                                       class="block px-4 py-2 text-sm font-medium {{ $on ? 'bg-blue-50 text-blue-900' : 'text-slate-700 hover:bg-slate-50' }}">{{ $m['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="dn-user">
                <div class="relative" x-data="{ o: false }" @click.outside="o = false" @keydown.escape.window="o = false">
                    <button type="button" @click="o = ! o" :aria-expanded="o"
                            class="inline-flex items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">{{ $initial }}</span>
                        <span class="max-w-[10rem] truncate">{{ $user->name }}</span>
                        <svg class="h-4 w-4 text-slate-400 transition-transform" :class="o && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="o" x-transition.opacity style="display: none"
                         class="absolute right-0 z-50 mt-2 w-52 rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg">
                        <a href="{{ route('profile.edit') }}" @if ($isSuperAdmin) onclick="openProfileModal(event)" @click="o = false" @endif class="block px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Profil</a>
                        <button type="button" onclick="confirmLogout(event)"
                                class="block w-full px-4 py-2 text-left text-sm font-medium text-red-700 hover:bg-red-50 transition-colors">
                            Keluar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- HEADER MOBILE (Hanya Muncul di HP, Logo & Nama Saja) -->
<header class="lg:hidden sticky top-0 z-40 flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3"
        style="padding-top: calc(0.75rem + env(safe-area-inset-top, 0px));">
    <div class="flex items-center gap-3">
        <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-8 w-auto">
        <span class="text-lg font-extrabold tracking-tight text-slate-900">SIPERDI</span>
    </div>
    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">
        {{ $initial }}
    </div>
</header>

<!-- BOTTOM NAVIGATION (Hanya HP) - CSS biasa, tidak butuh build Tailwind -->
<style>
    .bn-root { display: none; }
    @media (max-width: 1023.98px) { .bn-root { display: block; } }

    .bn-backdrop { position: fixed; inset: 0; z-index: 40; background: rgba(15, 23, 42, .4); }

    .bn-bar {
        position: fixed; left: 0; right: 0; bottom: 0; z-index: 50;
        background: #fff; border-top: 1px solid #e2e8f0;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, .05);
        padding-bottom: env(safe-area-inset-bottom, 0px);
    }
    .bn-row { display: flex; align-items: stretch; height: 64px; }

    .bn-item {
        position: relative; flex: 1 1 0%; min-width: 0;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;
        color: #94a3b8; background: none; border: 0; padding: 0; margin: 0;
        font: inherit; text-decoration: none; cursor: pointer;
        -webkit-tap-highlight-color: transparent; transition: color .15s;
    }
    .bn-item:active { color: #475569; }
    .bn-item.is-on { color: #1e3a8a; }
    .bn-item svg { width: 24px; height: 24px; display: block; flex: none; }
    .bn-label { font-size: 10px; font-weight: 600; line-height: 1; }
    .bn-mark { position: absolute; top: 0; left: 20px; right: 20px; height: 2px; border-radius: 0 0 4px 4px; background: #1e3a8a; }

    .bn-sheet {
        position: fixed; left: 0; right: 0; bottom: 0; z-index: 45;
        max-height: 75vh; overflow-y: auto;
        background: #fff; border-top: 1px solid #e2e8f0;
        border-radius: 16px 16px 0 0;
        box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, .15);
        padding-bottom: calc(64px + env(safe-area-inset-bottom, 0px));
    }
    .bn-grip { width: 40px; height: 4px; border-radius: 9999px; background: #e2e8f0; margin: 8px auto 0; }
    .bn-body { padding: 12px 8px; }

    .bn-link {
        display: flex; align-items: center; gap: 12px; width: 100%;
        padding: 12px 16px; border: 0; border-radius: 8px; background: none;
        font: inherit; font-size: 14px; font-weight: 500; text-align: left;
        color: #334155; text-decoration: none; cursor: pointer;
    }
    .bn-link:active { background: #f1f5f9; }
    .bn-link.is-on { background: #eff6ff; color: #1e3a8a; font-weight: 600; }
    .bn-link.danger { color: #b91c1c; }
    .bn-link.danger:active { background: #fef2f2; }
    .bn-link svg { width: 20px; height: 20px; flex: none; }

    .bn-title { padding: 8px 16px 4px; font-size: 12px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; }
    .bn-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; }
    .bn-grid .bn-link { padding: 10px 16px; }
    .bn-sep { height: 1px; background: #f1f5f9; margin: 8px 0; }

    .bn-user { display: flex; align-items: center; gap: 12px; padding: 8px 16px; }
    .bn-avatar {
        width: 36px; height: 36px; flex: none; border-radius: 9999px;
        display: flex; align-items: center; justify-content: center;
        background: #1e3a8a; color: #fff; font-size: 14px; font-weight: 700;
    }
    .bn-name { font-size: 14px; font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .bn-in   { transition: transform .2s ease-out; }
    .bn-out  { transition: transform .15s ease-in; }
    .bn-down { transform: translateY(100%); }
    .bn-up   { transform: translateY(0); }
</style>

<div class="bn-root" x-data="{ more: false }" @keydown.escape.window="more = false">

    {{-- Backdrop --}}
    <div class="bn-backdrop" x-show="more" x-transition.opacity @click="more = false" style="display: none"></div>

    {{-- Bottom sheet "Lainnya" --}}
    <div class="bn-sheet" x-show="more" style="display: none"
         x-transition:enter="bn-in" x-transition:enter-start="bn-down" x-transition:enter-end="bn-up"
         x-transition:leave="bn-out" x-transition:leave-start="bn-up" x-transition:leave-end="bn-down">
        <div class="bn-grip"></div>

        <div class="bn-body">
            @if (count($overflowLinks))
                @foreach ($overflowLinks as $l)
                    @php $on = request()->routeIs($l['route']); @endphp
                    <a href="{{ route($l['route']) }}" class="bn-link {{ $on ? 'is-on' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $l['icon'] }}"/>
                        </svg>
                        {{ $l['label'] }}
                    </a>
                @endforeach
                <div class="bn-sep"></div>
            @endif

            @if ($showMaster)
                <p class="bn-title">Master Data</p>
                <div class="bn-grid">
                    @foreach ($masterLinks as $m)
                        @php $on = request()->routeIs($m['route']); @endphp
                        <a href="{{ route($m['route']) }}" class="bn-link {{ $on ? 'is-on' : '' }}">{{ $m['label'] }}</a>
                    @endforeach
                </div>
                <div class="bn-sep"></div>
            @endif

            <div class="bn-user">
                <span class="bn-avatar">{{ $initial }}</span>
                <span class="bn-name">{{ $user->name }}</span>
            </div>
            <a href="{{ route('profile.edit') }}" @if ($isSuperAdmin) onclick="openProfileModal(event)" @click="more = false" @endif class="bn-link {{ request()->routeIs('profile.*') ? 'is-on' : '' }}">Profil Saya</a>
            <button type="button" class="bn-link danger" onclick="confirmLogout(event)">Keluar</button>
        </div>
    </div>

    {{-- Bar utama --}}
    <nav class="bn-bar" aria-label="Navigasi utama">
        <div class="bn-row">
            @foreach ($bottomLinks as $l)
                @php $on = request()->routeIs($l['route']); @endphp
                <a href="{{ route($l['route']) }}" @if($on) aria-current="page" @endif
                   @click="more = false" class="bn-item {{ $on ? 'is-on' : '' }}">
                    @if ($on) <span class="bn-mark"></span> @endif
                    <svg fill="none" stroke="currentColor" stroke-width="{{ $on ? 2 : 1.75 }}" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $l['icon'] }}"/>
                    </svg>
                    <span class="bn-label">{{ $l['label'] }}</span>
                </a>
            @endforeach

            <button type="button" @click="more = ! more" :aria-expanded="more"
                    class="bn-item {{ $moreActive ? 'is-on' : '' }}" :class="more && 'is-on'">
                @if ($moreActive) <span class="bn-mark"></span> @endif
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="bn-label">Lainnya</span>
            </button>
        </div>
    </nav>
</div>

<script>
window.confirmLogout = function (event) {
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
};
</script>