<section class="space-y-6" x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
         x-init="$watch('open', v => v && $nextTick(() => $refs.pw && $refs.pw.focus()))"
         @keydown.escape.window="open = false">

    <header class="flex items-start gap-4">
        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
        <div>
            <h2 class="text-lg font-bold tracking-tight text-slate-900">Hapus Akun</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-500">Setelah dihapus, seluruh data dan sumber daya akun ini akan hilang secara permanen. Unduh data yang ingin Anda simpan sebelum melanjutkan.</p>
        </div>
    </header>

    <div class="flex max-w-xl flex-col gap-4 rounded-xl border border-red-200 bg-red-50/60 p-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-red-800">Tindakan ini tidak dapat dibatalkan.</p>
        <button type="button" @click="open = true"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 max-sm:w-full">
            Hapus Akun
        </button>
    </div>

    <!-- Modal konfirmasi -->
    <div x-show="open" x-transition.opacity style="display: none"
         class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4"
         role="dialog" aria-modal="true" aria-labelledby="delete-title" @click.self="open = false">
        <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-2xl bg-white shadow-xl sm:rounded-2xl">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="p-5 sm:p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                        <div>
                            <h3 id="delete-title" class="text-base font-bold text-slate-900">Hapus akun Anda?</h3>
                            <p class="mt-1 text-sm leading-relaxed text-slate-500">Seluruh data akun akan dihapus permanen. Masukkan kata sandi Anda untuk mengonfirmasi.</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="delete_password" class="block text-sm font-semibold text-slate-800">Kata sandi</label>
                        <input id="delete_password" name="password" type="password" x-ref="pw" placeholder="Masukkan kata sandi" autocomplete="current-password"
                               class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 {{ $errors->userDeletion->has('password') ? 'border-red-400' : 'border-slate-300' }}">
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button type="button" @click="open = false" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Batal</button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">Ya, Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>
</section>