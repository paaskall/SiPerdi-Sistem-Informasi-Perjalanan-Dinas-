<x-app-layout>
    @php
        $account  = Auth::user();
        $initials = strtoupper(collect(preg_split('/\s+/', trim($account->name)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));
        $roles    = $account->getRoleNames();
    @endphp

    <div class="scroll-smooth py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Judul halaman -->
            <div class="mb-6 flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Pengaturan Akun</h1>
                    <p class="mt-0.5 text-sm text-slate-500">Kelola informasi profil, keamanan, dan akun Anda.</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-12">

                <!-- Ringkasan akun -->
                <aside class="lg:col-span-4 xl:col-span-3">
                    <div class="space-y-4 lg:sticky lg:top-28">
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-blue-900 to-blue-600 text-2xl font-extrabold text-white shadow-lg shadow-blue-900/20">{{ $initials }}</div>
                            <h2 class="mt-4 truncate text-lg font-bold text-slate-900">{{ $account->name }}</h2>
                            <p class="truncate text-sm text-slate-500">{{ $account->email }}</p>

                            @if ($roles->isNotEmpty())
                                <div class="mt-4 flex flex-wrap justify-center gap-1.5">
                                    @foreach ($roles as $r)
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold capitalize text-blue-800 ring-1 ring-inset ring-blue-200">{{ $r }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($account->hasRole('pimpinan'))
                                <p class="mt-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $account->ttd_path ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                                    {{ $account->ttd_path ? 'Tanda tangan tersimpan' : 'Tanda tangan belum diunggah' }}
                                </p>
                            @endif
                        </div>

                        <nav class="rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Bagian pengaturan">
                            <a href="#profil" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 hover:text-blue-900">Informasi Profil</a>
                            <a href="#sandi" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 hover:text-blue-900">Kata Sandi</a>
                            <a href="#hapus" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50">Hapus Akun</a>
                        </nav>
                    </div>
                </aside>

                <!-- Form -->
                <div class="space-y-6 lg:col-span-8 xl:col-span-9">
                    <div id="profil" class="scroll-mt-28 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div id="sandi" class="scroll-mt-28 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div id="hapus" class="scroll-mt-28 rounded-2xl border border-red-200 bg-white p-5 shadow-sm sm:p-8">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>