@php
    $user = Auth::user();

    $mainLinks = [
        ['label' => 'Dashboard',         'route' => 'dashboard',              'role' => null],
        ['label' => 'Buat Pengajuan',    'route' => 'perjalanan.buat',        'role' => 'pegawai'],
        ['label' => 'Riwayat Saya',      'route' => 'perjalanan.riwayat',     'role' => 'pegawai'],
        ['label' => 'Persetujuan',       'route' => 'perjalanan.persetujuan', 'role' => 'keuangan'],
        ['label' => 'Reimbursement',     'route' => 'reimbursement.index',    'role' => 'keuangan'],
        ['label' => 'SPPD',              'route' => 'perjalanan.pimpinan',    'role' => 'pimpinan'],
        ['label' => 'Reimburse',         'route' => 'reimbursement.index',    'role' => 'pimpinan'],
    ];
    
    $mainLinks = array_values(array_filter($mainLinks, fn ($l) => is_null($l['role']) || $user->hasRole($l['role'])));

    $masterLinks = [
        ['label' => 'Divisi',   'route' => 'master.divisi'],
        ['label' => 'Golongan', 'route' => 'master.golongan'],
        ['label' => 'COA',      'route' => 'master.coa'],
        ['label' => 'Kota',     'route' => 'master.kota'],
        ['label' => 'Tarif',    'route' => 'master.tarif'],
    ];
    $showMaster   = $user->hasRole('keuangan');
    $masterActive = request()->routeIs('master.*');

    $linkBase   = 'inline-flex h-full items-center border-b-2 px-1 text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2';
    $linkOn     = 'border-blue-900 text-slate-900';
    $linkOff    = 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-800';
    $mobBase    = 'block border-l-4 py-2.5 pl-4 pr-4 text-base font-medium transition-colors';
    $mobOn      = 'border-blue-900 bg-blue-50 text-blue-900';
    $mobOff     = 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    $initial    = strtoupper(mb_substr($user->name, 0, 1));
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white">
    <!-- Menu utama -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between sm:h-20">
            <div class="flex">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-3 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 sm:gap-4">
                    <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-9 w-auto object-contain sm:h-11">
                    <span class="hidden h-8 w-px bg-slate-200 sm:block"></span>
                    <span class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">SIPERDI</span>
                </a>

                <!-- Tautan (desktop) -->
                <div class="hidden gap-7 lg:ml-10 lg:flex">
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

            <!-- Menu pengguna (desktop) -->
            <div class="hidden lg:flex lg:items-center">
                <div class="relative" x-data="{ o: false }" @click.outside="o = false" @keydown.escape.window="o = false">
                    <button type="button" @click="o = ! o" :aria-expanded="o"
                            class="inline-flex items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">{{ $initial }}</span>
                        <span class="max-w-[10rem] truncate">{{ $user->name }}</span>
                        <svg class="h-4 w-4 text-slate-400 transition-transform" :class="o && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="o" x-transition.opacity style="display: none"
                         class="absolute right-0 z-50 mt-2 w-52 rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Profil</a>
                        
                        {{-- LOGOUT VIA FORM (TANPA JAVASCRIPT) --}}
                        <form method="GET" action="http://127.0.0.1:8002/sso-logout">
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm font-medium text-red-700 hover:bg-red-50 transition-colors">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center lg:hidden">
                <button type="button" @click="open = ! open" :aria-expanded="open" aria-label="Buka menu"
                        class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu mobile -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200 lg:hidden">
        <div class="space-y-1 py-3">
            @foreach ($mainLinks as $l)
                @php $on = request()->routeIs($l['route']); @endphp
                <a href="{{ route($l['route']) }}" @if($on) aria-current="page" @endif
                   class="{{ $mobBase }} {{ $on ? $mobOn : $mobOff }}">{{ $l['label'] }}</a>
            @endforeach

            @if ($showMaster)
                <p class="px-5 pb-1 pt-4 text-xs font-semibold text-slate-400">Master Data</p>
                @foreach ($masterLinks as $m)
                    @php $on = request()->routeIs($m['route']); @endphp
                    <a href="{{ route($m['route']) }}" @if($on) aria-current="page" @endif
                       class="{{ $mobBase }} {{ $on ? $mobOn : $mobOff }}">{{ $m['label'] }}</a>
                @endforeach
            @endif
        </div>

        <!-- Pengguna (mobile) -->
        <div class="border-t border-slate-200 pb-3 pt-4">
            <div class="flex items-center gap-3 px-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-900 text-sm font-bold text-white">{{ $initial }}</span>
                <div class="min-w-0">
                    <div class="truncate text-base font-semibold text-slate-900">{{ $user->name }}</div>
                    <div class="truncate text-sm text-slate-500">{{ $user->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="{{ $mobBase }} {{ request()->routeIs('profile.edit') ? $mobOn : $mobOff }}">Profil</a>
                
                {{-- LOGOUT VIA FORM MOBILE --}}
                <form method="GET" action="http://127.0.0.1:8002/sso-logout">
                    <button type="submit" class="{{ $mobBase }} {{ $mobOff }} w-full text-left text-red-700">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.logoutSso = function() {
            Swal.fire({
                title: 'Keluar Aplikasi?',
                text: 'Anda akan keluar dari semua aplikasi (SSO).',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'http://127.0.0.1:8002/sso-logout';
                }
            });
        };
    </script>
@endpush