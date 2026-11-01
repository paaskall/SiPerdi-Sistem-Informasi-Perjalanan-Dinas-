<div class="py-8 sm:py-10">
    @php
        $inp = fn ($field) => 'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 '
            . ($errors->has($field) ? 'border-red-400' : 'border-slate-300');
    @endphp

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <!-- Header -->
            <div class="flex items-center gap-4 border-b border-slate-200 bg-slate-50 p-4 sm:p-6">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg></div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Laporan Reimbursement</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Perjalanan: {{ $perjalanan->nomor_pengajuan }} ke {{ $perjalanan->kotaTujuan->nama }}</p>
                </div>
            </div>

            <form wire:submit="simpan">
                <div class="p-4 sm:p-6">
                    <h3 class="flex items-center gap-2.5 text-sm font-bold text-slate-900"><span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">1</span>Rincian Realisasi Biaya & Struk</h3>

                    <div class="mt-4 space-y-4 md:space-y-3">
                        @foreach($rincian as $index => $item)
                            <div wire:key="rincian-{{ $index }}" class="grid grid-cols-1 gap-4 rounded-xl border border-slate-200 p-4 md:grid-cols-12 md:items-start md:rounded-none md:border-0 md:p-0">
                                
                                <div class="md:col-span-3">
                                    <label for="rincian-coa-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Kategori</label>
                                    <select wire:model="rincian.{{ $index }}.coa_id" id="rincian-coa-{{ $index }}" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.coa_id') }}">
                                        <option value="">-- Pilih --</option>
                                        @foreach($coas as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('rincian.'.$index.'.coa_id')" class="mt-1" />
                                </div>

                                <div class="md:col-span-3">
                                    <label for="rincian-ket-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Keterangan</label>
                                    <input wire:model="rincian.{{ $index }}.keterangan" id="rincian-ket-{{ $index }}" type="text" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.keterangan') }}">
                                </div>

                                <div class="md:col-span-3">
                                    <label for="rincian-nom-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Nominal (Rp)</label>
                                    <input wire:model.live="rincian.{{ $index }}.nominal" id="rincian-nom-{{ $index }}" type="number" min="0" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.nominal') }}">
                                    <x-input-error :messages="$errors->get('rincian.'.$index.'.nominal')" class="mt-1" />
                                </div>

                                <div class="md:col-span-3">
                                    <label for="rincian-struk-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Upload Struk</label>
                                    
                                    <div class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} flex flex-col gap-2">
                                        <input type="file" wire:model="rincian.{{ $index }}.file_struk" id="rincian-struk-{{ $index }}" accept="image/*" capture="environment" class="block w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-3 file:cursor-pointer file:rounded-l-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2.5 file:text-xs file:font-semibold file:text-blue-900 hover:file:bg-blue-100">
                                        
                                        <div class="flex gap-2">
                                            <button type="button" onclick="openCamera({{ $index }})" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-md bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-slate-900">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Kamera
                                            </button>
                                            <button type="button" onclick="scanStruk({{ $index }})" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-indigo-700">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Scan OCR
                                            </button>
                                        </div>
                                    </div>

                                    <div wire:loading wire:target="rincian.{{ $index }}.file_struk" class="mt-1 text-xs font-medium text-blue-700">Mengunggah...</div>
                                    @if(isset($item['file_struk']) && is_object($item['file_struk']))
                                        <p wire:loading.remove wire:target="rincian.{{ $index }}.file_struk" class="mt-1 truncate text-xs font-medium text-emerald-700">&#10003; {{ $item['file_struk']->getClientOriginalName() }}</p>
                                    @endif
                                    <x-input-error :messages="$errors->get('rincian.'.$index.'.file_struk')" class="mt-1" />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="tambahBaris" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 max-sm:w-full max-sm:justify-center">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
                        Tambah Baris Biaya
                    </button>
                </div>

                <!-- Total & Kirim -->
                <div class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Total Realisasi</div>
                        <div class="mt-0.5 text-2xl font-extrabold tracking-tight text-slate-900">Rp {{ number_format($total_realisasi, 0, ',', '.') }}</div>
                    </div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="simpan"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-7 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-800 disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                        Kirim Laporan Reimbursement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KAMERA -->
    <div id="cameraModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-80">
        <div class="bg-white rounded-xl overflow-hidden w-full max-w-md">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="font-bold text-slate-900">Ambil Foto Struk</h3>
                <button onclick="closeCamera()" class="text-slate-500 hover:text-red-600 text-2xl">&times;</button>
            </div>
            <div id="reader" class="w-full bg-black"></div>
            <div class="p-4 text-center">
                <button onclick="capturePhoto()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold">Ambil Foto</button>
            </div>
        </div>
    </div>

    <!-- SCRIPT OCR & KAMERA -->
    <script>
        let html5QrCode;
        let currentIndex = null;

        async function openCamera(index) {
            currentIndex = index;
            document.getElementById('cameraModal').classList.remove('hidden');
            document.getElementById('cameraModal').classList.add('flex');

            html5QrCode = new Html5Qrcode("reader");
            try {
                await html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 300, height: 400 } },
                    () => {}, // callback sukses (abaikan)
                    () => {}  // callback error (abaikan)
                );
            } catch (err) {
                alert("Tidak dapat mengakses kamera: " + err);
                closeCamera();
            }
        }

        function closeCamera() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('cameraModal').classList.add('hidden');
                    document.getElementById('cameraModal').classList.remove('flex');
                }).catch(err => console.error(err));
            }
        }

        async function capturePhoto() {
            if (!html5QrCode) return;
            
            const video = document.querySelector('#reader video');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            const base64Image = canvas.toDataURL('image/jpeg', 0.8);
            closeCamera();
            processImageWithGemini(base64Image, currentIndex);
        }

        async function scanStruk(index) {
            const inputFile = document.querySelector(`input[wire\\:model="rincian.${index}.file_struk"]`);
            
            if (!inputFile || !inputFile.files || inputFile.files.length === 0) {
                alert('Pilih gambar struk terlebih dahulu, atau gunakan tombol Kamera!');
                return;
            }

            const file = inputFile.files[0];
            const reader = new FileReader();
            
            reader.onload = async (e) => {
                processImageWithGemini(e.target.result, index);
            };
            reader.readAsDataURL(file);
        }

        async function processImageWithGemini(base64Image, index) {
            Swal.fire({
                title: 'Memindai Struk...',
                text: 'Gemini AI sedang membaca nominal struk Anda.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch("{{ route('api.ocr') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ image: base64Image })
                });

                const result = await response.json();
                Swal.close();

                if (result.success && result.data && result.data.total > 0) {
                    const nominalInput = document.querySelector(`input[wire\\:model\\.live="rincian.${index}.nominal"]`);
                    if (nominalInput) {
                        nominalInput.value = result.data.total;
                        nominalInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `Total terdeteksi: Rp ${result.data.total.toLocaleString('id-ID')}`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Membaca',
                        text: 'Tidak dapat mendeteksi nominal pada struk. Silakan input manual.'
                    });
                }
            } catch (error) {
                Swal.close();
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem saat memindai struk.'
                });
            }
        }
    </script>
</div>