<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            
            <!-- Header -->
            <div class="border-b border-slate-200 p-4 sm:p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-slate-900">Audit Trail</h2>
                    <p class="mt-1 text-sm text-slate-500">Riwayat lengkap aktivitas pengguna dalam sistem.</p>
                </div>
                <div class="relative w-full sm:w-64">
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari user/aksi..." 
                           class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-3 pr-3 text-sm text-slate-900 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20">
                </div>
            </div>

            <!-- Tabel Log -->
            <div class="overflow-x-auto">
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
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $log->causer ? $log->causer->name : 'Sistem' }}
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
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada aktivitas tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="border-t border-slate-200 px-4 py-3 sm:px-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>