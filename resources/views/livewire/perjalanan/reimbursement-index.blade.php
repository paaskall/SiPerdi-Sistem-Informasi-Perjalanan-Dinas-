<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        @if (session()->has('message'))
            <div class="mb-5 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

            <!-- Header -->
            <div class="border-b border-slate-200 p-4 sm:p-6">
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Daftar Laporan Reimbursement</h2>
                <p class="mt-1 text-sm text-slate-500">Tinjau laporan realisasi biaya perjalanan dinas.</p>
            </div>

            <!-- Tabel (tablet & desktop) -->
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">No. Reimb</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Pegawai</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tujuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Realisasi</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Selisih</th>
                            <th scope="col" class="px-6 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reimbursements as $r)
                            <tr wire:key="row-{{ $r->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $r->nomor_reimb }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $r->perjalanan->pegawai->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $r->perjalanan->kotaTujuan->nama }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">Rp {{ number_format($r->total_realisasi, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 font-semibold {{ $r->selisih > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format($r->selisih, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" wire:click="openModal({{ $r->id }})" class="rounded-md bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Review</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada laporan reimbursement.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Daftar kartu (mobile) -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($reimbursements as $r)
                    <div wire:key="card-{{ $r->id }}" class="p-4">
                        <div class="font-semibold text-slate-900">{{ $r->nomor_reimb }}</div>
                        <dl class="mt-3 space-y-1.5">
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Pegawai</dt><dd class="text-right text-slate-800">{{ $r->perjalanan->pegawai->name }}</dd></div>
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Tujuan</dt><dd class="text-right text-slate-800">{{ $r->perjalanan->kotaTujuan->nama }}</dd></div>
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Realisasi</dt><dd class="text-right font-semibold text-slate-900">Rp {{ number_format($r->total_realisasi, 0, ',', '.') }}</dd></div>
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Selisih</dt><dd class="text-right font-semibold {{ $r->selisih > 0 ? 'text-red-600' : 'text-emerald-600' }}">Rp {{ number_format($r->selisih, 0, ',', '.') }}</dd></div>
                        </dl>
                        <button type="button" wire:click="openModal({{ $r->id }})" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Review</button>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Tidak ada laporan reimbursement.</div>
                @endforelse
            </div>

            @if ($reimbursements->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $reimbursements->links() }}</div>
            @endif
        </div>
    </div>

    <!-- MODAL REVIEW -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 sm:items-center sm:p-4" wire:keydown.escape.window="closeModal" role="dialog" aria-modal="true">
        <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-t-2xl bg-white sm:rounded-2xl">

            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="text-base font-bold text-slate-900">Review Reimbursement</h3>
                <button type="button" wire:click="closeModal" aria-label="Tutup" class="rounded-md p-1 text-slate-400 transition-colors hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 sm:p-6">
                @php $reimb = \App\Models\Reimbursement::with('items.coa')->find($reimbId); @endphp
                @if($reimb)
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Kategori</th>
                                    <th class="px-4 py-3 font-semibold">Keterangan</th>
                                    <th class="px-4 py-3 font-semibold">Nominal</th>
                                    <th class="px-4 py-3 font-semibold text-center">Struk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($reimb->items as $item)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-slate-800">{{ $item->coa->nama }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $item->keterangan }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-800">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ asset('storage/' . $item->file_struk) }}" target="_blank" class="font-semibold text-blue-900 underline decoration-blue-300 underline-offset-2 hover:text-blue-800">Lihat Struk</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if(auth()->user()->hasRole('keuangan'))
                    <div class="mt-5">
                        <label for="catatan_keuangan" class="block text-sm font-semibold text-slate-800">Catatan Keuangan</label>
                        <p class="mt-0.5 text-xs text-slate-500">Wajib diisi jika menolak laporan.</p>
                        <textarea wire:model="catatan_keuangan" id="catatan_keuangan" rows="3" placeholder="Tambahkan catatan..."
                                  class="mt-2 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('catatan_keuangan') ? 'border-red-400' : 'border-slate-300' }}"></textarea>
                        <x-input-error :messages="$errors->get('catatan_keuangan')" class="mt-2" />
                    </div>
                @endif
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                @if(auth()->user()->hasRole('keuangan'))
                    <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Batal</button>
                    <button type="button" wire:click="tolakKeuangan" class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-5 py-2.5 text-sm font-semibold text-red-700 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">Tolak</button>
                    <button type="button" wire:click="setujuiKeuangan" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Setujui</button>
                @elseif(auth()->user()->hasRole('pimpinan'))
                    <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Batal</button>
                    <button type="button" wire:click="setujuiPimpinan" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Tanda Tangani</button>
                @endif
            </div>

        </div>
    </div>
    @endif
</div>