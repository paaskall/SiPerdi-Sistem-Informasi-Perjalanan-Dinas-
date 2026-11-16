<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- HEADER HALAMAN -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Audit Trail</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Riwayat lengkap aktivitas pengguna dalam sistem.</p>
                </div>
            </div>
        </div>

        <!-- CARD TABEL -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            
            <!-- Toolbar: Pencarian -->
            <div class="border-b border-slate-200 p-4 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <p class="text-sm text-slate-500">
                    Menampilkan <span class="font-semibold text-slate-700">{{ $logs->firstItem() ?? 0 }}&ndash;{{ $logs->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $logs->total() }}</span> log
                </p>
                <div class="relative w-full sm:w-72">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari user, aksi, atau objek..." 
                           class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20">
                </div>
            </div>

            <!-- Tabel Log (Desktop) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-semibold w-48">Waktu</th>
                            <th class="px-6 py-3 font-semibold">Pengguna</th>
                            <th class="px-6 py-3 font-semibold">Aktivitas</th>
                            <th class="px-6 py-3 font-semibold">Objek Terkait</th>
                            <th class="px-6 py-3 font-semibold">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $log->causer ? $log->causer->name : 'Sistem' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                        {{ $log->description }}
                                    </span>
                                    @if($log->properties->has('alasan_penolakan'))
                                        <p class="text-xs text-red-600 mt-1">Alasan: "{{ $log->properties['alasan_penolakan'] }}"</p>
                                    @endif
                                    @if($log->properties->has('nomor_pengajuan'))
                                        <p class="text-xs text-slate-500 mt-1">No: {{ $log->properties['nomor_pengajuan'] }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    @if($log->subject)
                                        {{ class_basename($log->subject_type) }} #{{ $log->subject->nomor_pengajuan ?? $log->subject->nomor_reimb ?? $log->subject_id }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                                    {{ $log->properties['ip'] ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="px-6 py-14 text-center">
                                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <p class="font-semibold text-slate-800">Belum ada aktivitas</p>
                                        <p class="mt-1 text-sm text-slate-500">Log aktivitas akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tampilan Mobile (Card) -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($logs as $log)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="font-semibold text-slate-900">{{ $log->causer ? $log->causer->name : 'Sistem' }}</div>
                            <div class="text-xs text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="mb-2">
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                {{ $log->description }}
                            </span>
                        </div>
                        @if($log->subject)
                            <div class="text-xs text-slate-500 mb-1">
                                Objek: <span class="font-medium text-slate-700">{{ class_basename($log->subject_type) }} #{{ $log->subject->nomor_pengajuan ?? $log->subject->nomor_reimb ?? $log->subject_id }}</span>
                            </div>
                        @endif
                        <div class="text-xs text-slate-400 font-mono">
                            IP: {{ $log->properties['ip'] ?? '-' }}
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center">
                        <p class="font-semibold text-slate-800">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if ($logs->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>