@extends('layouts.app')

@section('title', 'Memo Permohonan Supir')
@section('page_title', 'Memo Permohonan Supir')

@push('styles')
    {{-- Tambahkan CSS untuk Choices.js --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        /* Custom styles for Choices.js to make it larger and clearer */
        .choices__inner {
            background-color: #f3f4f6; /* bg-gray-100 */
            border-radius: 0.375rem; /* rounded-md */
            border: 1px solid #d1d5db; /* border-gray-300 */
            font-size: 1rem; /* text-base */
            padding: 0.5rem 0.75rem; /* p-2.5 equivalent */
            min-height: 46px; /* Ensure consistent height */
            width: 100%; /* Make it full width for consistent layout */
        }
        .is-focused .choices__inner,
        .is-open .choices__inner {
            border-color: #6366f1; /* border-indigo-500 */
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5); /* ring-indigo-500 */
        }
        .choices__input {
            background-color: #f3f4f6; /* bg-gray-100 */
            font-size: 1rem;
        }
        .choices__list--dropdown {
            background-color: #e5e7eb; /* bg-gray-200 */
            border-color: #d1d5db; /* border-gray-300 */
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); /* shadow-md */
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #c7d2fe; /* bg-indigo-200 */
            color: #3730a3; /* text-indigo-800 */
        }
        .choices[data-type*="select-multiple"] .choices__item {
            background-color: #d1d5db; /* bg-gray-300 */
            border: 1px solid #9ca3af; /* border-gray-400 */
            color: #1f2937; /* text-gray-800 */
        }
        .choices__item--choice {
            font-size: 1rem; /* text-base */
            padding: 0.5rem 0.75rem; /* p-2.5 equivalent */
        }
        /* New custom classes for better layout */
        .form-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem; /* Equivalent to space-y-6 */
        }
    </style>
@endpush

@section('content')
<div class="space-y-8 max-w-3xl mx-auto">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form id="permohonanForm" action="{{ route('permohonan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @php
                // Definisikan kelas Tailwind untuk input yang lebih besar dan jelas
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
                $autoInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-200 shadow-sm text-base p-2.5";
            @endphp

            {{-- Bagian 1: Informasi Umum --}}
            <fieldset class="border p-4 rounded-md mb-6">
                <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Umum</legend>
                <div class="form-section pt-4">
                    {{-- Memo Number (Always at the top for visibility) --}}
                    <div>
                        <label for="nomor_memo" class="block text-sm font-medium text-gray-700">Nomor Memo (Otomatis)</label>
                        <input type="text" name="nomor_memo" id="nomor_memo" class="{{ $autoInputClasses }}" readonly>
                        <input type="hidden" id="kode_cetak" value="1">
                        <p class="mt-1 text-xs text-gray-500">
                            <span id="memo_format_info">
                                Format: MS (2 digit) + cetakan (1 digit) + tahun (2 digit) + bulan (2 digit) + running number (7 digit)<br>
                                <span class="font-mono text-blue-600"></span>
                            </span>
                        </p>
                    </div>

                    {{-- Activity & Vendor --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="kegiatan" class="block text-sm font-medium text-gray-700">Kegiatan</label>
                            <select name="kegiatan" id="kegiatan" class="{{ $inputClasses }}" required>
                                <option value="">Pilih Kegiatan</option>
                                @isset($kegiatans)
                                    @foreach($kegiatans as $kg)
                                        <option value="{{ $kg->kode_kegiatan }}" {{ old('kegiatan') == $kg->kode_kegiatan ? 'selected' : '' }}>{{ $kg->nama_kegiatan }}</option>
                                    @endforeach
                                @else
                                    <option value="pengiriman">Pengiriman Kontainer</option>
                                    <option value="pengambilan">Pengambilan Kontainer</option>
                                @endisset
                            </select>
                        </div>
                        <div>
                            <label for="vendor_perusahaan" class="block text-sm font-medium text-gray-700">Vendor Perusahaan</label>
                            <select name="vendor_perusahaan" id="vendor_perusahaan" class="{{ $inputClasses }}" required>
                                <option value="">Pilih Vendor</option>
                                <option value="AYP">AYP</option>
                                <option value="ZONA">ZONA</option>
                                <option value="SOC">SOC</option>
                                <option value="DPE">DPE</option>
                            </select>
                        </div>
                    </div>

                    {{-- Date & Driver --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tanggal_memo" class="block text-sm font-medium text-gray-700">Tanggal Memo</label>
                            <input type="date" name="tanggal_memo" id="tanggal_memo" class="{{ $inputClasses }}" value="{{ now()->toDateString() }}">
                        </div>
                        <div>
                            <label for="supir_id" class="block text-sm font-medium text-gray-700">Supir (Nama Panggilan)</label>
                            <select name="supir_id" id="supir_id" class="{{ $inputClasses }}" required>
                                <option value="">Cari atau pilih supir...</option>
                                @foreach ($supirs as $supir)
                                    <option value="{{ $supir->id }}" data-plat="{{ $supir->plat }}">{{ $supir->nama_panggilan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Automatic Plate & Optional Krani --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="plat_nomor" class="block text-sm font-medium text-gray-700">No Plat (Otomatis)</label>
                            <input type="text" name="plat_nomor" id="plat_nomor" class="{{ $autoInputClasses }}" readonly>
                        </div>
                        <div>
                            <label for="krani_id" class="block text-sm font-medium text-gray-700">Krani (Opsional)</label>
                            <select name="krani_id" id="krani_id" class="{{ $inputClasses }}">
                                <option value="">Cari atau pilih krani...</option>
                                @foreach ($kranis as $krani)
                                    <option value="{{ $krani->id }}">{{ $krani->nama_panggilan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Bagian 2: Informasi Kontainer & Tujuan --}}
            <fieldset class="border p-4 rounded-md mb-6">
                <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Kontainer & Tujuan</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="jumlah_kontainer" class="block text-sm font-medium text-gray-700">Jumlah Kontainer</label>
                            <input type="number" name="jumlah_kontainer" id="jumlah_kontainer" class="{{ $inputClasses }}" value="1" min="1" required>
                        </div>
                        <div>
                            <label for="ukuran" class="block text-sm font-medium text-gray-700">Ukuran Kontainer</label>
                            <select name="ukuran" id="ukuran" class="{{ $inputClasses }}" required>
                                <option value="">Pilih Ukuran</option>
                                <option value="10">10 ft</option>
                                <option value="20">20 ft</option>
                                <option value="40">40 ft</option>
                            </select>
                        </div>
                        <div>
                            <label for="no_chasis" class="block text-sm font-medium text-gray-700">No Chasis</label>
                            <input type="text" name="no_chasis" id="no_chasis" class="{{ $inputClasses }}">
                        </div>
                    </div>

                    <div>
                        <label for="tujuan" class="block text-sm font-medium text-gray-700">Tujuan</label>
                        <select name="tujuan_id" id="tujuan" class="{{ $inputClasses }}" required>
                            <option value="">Pilih Tujuan</option>
                            @foreach($tujuans as $t)
                                @php $label = trim((($t->wilayah ?? '') ? $t->wilayah : '') . ' ' . (($t->rute ?? '') ? '- '.$t->rute : '')); @endphp
                                <option value="{{ $t->id }}" data-json='@json($t)'>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rute untuk Perbaikan Kontainer --}}
                    <div id="rute_container" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700">Rute Perbaikan</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="dari" class="block text-xs font-medium text-gray-600">Dari</label>
                                <input type="text" name="dari" id="dari" class="{{ $inputClasses }}" placeholder="Lokasi asal">
                            </div>
                            <div>
                                <label for="ke" class="block text-xs font-medium text-gray-600">Ke</label>
                                <input type="text" name="ke" id="ke" class="{{ $inputClasses }}" placeholder="Lokasi tujuan">
                            </div>
                        </div>
                    </div>

                    <div id="antar_lokasi_container" class="flex items-center pt-2">
                        <input id="antar_lokasi_checkbox" name="antar_sewa" value="1" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="antar_lokasi_checkbox" class="ml-2 block text-sm font-medium text-gray-900">Antar Lokasi</label>
                    </div>
                </div>
            </fieldset>

            {{-- Bagian 3: Biaya & Keuangan --}}
            <fieldset class="border p-4 rounded-md mb-6">
                <legend class="text-lg font-semibold text-gray-800 px-2">Biaya & Keuangan</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="jumlah_uang_jalan" class="block text-sm font-medium text-gray-700">Jumlah Uang Jalan</label>
                            <input type="number" name="jumlah_uang_jalan" id="jumlah_uang_jalan" class="{{ $inputClasses }}" value="0" required>
                        </div>
                        <div>
                            <label for="adjustment" class="block text-sm font-medium text-gray-700">Adjustment</label>
                            <input type="number" name="adjustment" id="adjustment" class="{{ $inputClasses }}" value="0" required>
                        </div>
                        <div>
                            <label for="total_setelah_adjustment" class="block text-sm font-medium text-gray-700">Total Biaya (Setelah Adj)</label>
                            <input type="number" id="total_setelah_adjustment" class="{{ $autoInputClasses }}" readonly>
                        </div>
                    </div>
                    <div>
                        <label for="alasan_adjustment" class="block text-sm font-medium text-gray-700">Alasan Adjustment</label>
                        <input type="text" name="alasan_adjustment" id="alasan_adjustment" class="{{ $inputClasses }}">
                    </div>
                </div>
            </fieldset>

            {{-- Bagian 4: Informasi Tambahan --}}
            <fieldset class="border p-4 rounded-md mb-6">
                <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Tambahan</legend>
                <div class="form-section pt-4">
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5"></textarea>
                    </div>
                    <div>
                        <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran</label>
                        <input type="file" name="lampiran" id="lampiran" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>
            </fieldset>

            <div class="flex justify-end mt-8">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan Permohonan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
@push('scripts')
    {{-- Tambahkan JS untuk Choices.js --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        // Variabel global yang akan digunakan di luar DOMContentLoaded
        let kegiatanSelect, tujuanSelect, updateUangJalan, updateFormBasedOnJumlah;

        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Choices.js untuk Supir
            const supirElement = document.getElementById('supir_id');
            const supirChoices = new Choices(supirElement, {
                searchEnabled: true,
                itemSelectText: 'Tekan untuk memilih',
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Cari atau pilih supir...'
            });

            // Inisialisasi Choices.js untuk Krani
            const kraniElement = document.getElementById('krani_id');
            const kraniChoices = new Choices(kraniElement, {
                searchEnabled: true,
                itemSelectText: 'Tekan untuk memilih',
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Cari atau pilih krani...'
            });

            const platInput = document.getElementById('plat_nomor');

            // Event listener untuk mengisi plat nomor otomatis
            supirElement.addEventListener('change', function(event) {
                const supirId = supirElement.value;
                if (supirId) {
                    const selectedOption = supirElement.querySelector(`option[value="${supirId}"]`);
                    if (selectedOption) {
                        const plat = selectedOption.dataset.plat || '';
                        platInput.value = plat;
                    }
                }
                // Jika tidak ada supir, jangan kosongkan platInput agar tetap bisa diedit manual
            });

            // Logika untuk ukuran kontainer dan input nomor kontainer dinamis
            const jumlahKontainerInput = document.getElementById('jumlah_kontainer');
            const ukuranKontainerSelect = document.getElementById('ukuran');
            kegiatanSelect = document.getElementById('kegiatan');
            const nomorKontainerContainer = document.getElementById('nomor_kontainer_container');
            const originalOptions = Array.from(ukuranKontainerSelect.options);
            const inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";

            updateFormBasedOnJumlah = function() {
                const jumlah = parseInt(jumlahKontainerInput.value, 10) || 0;
                const currentValue = ukuranKontainerSelect.value;
                const selectedKegiatan = kegiatanSelect.value.toLowerCase();
                const isTarikAntarPerbaikan = (selectedKegiatan.includes('tarik') || selectedKegiatan.includes('antar')) && selectedKegiatan.includes('perbaikan');
                const isPerbaikanKontainer = (selectedKegiatan.includes('perbaikan kontainer') || selectedKegiatan.includes('perbaikan')) && !isTarikAntarPerbaikan;

                while (ukuranKontainerSelect.options.length > 0) {
                    ukuranKontainerSelect.remove(0);
                }

                if (isPerbaikanKontainer) {
                    // Untuk perbaikan kontainer (kecuali tarik/antar perbaikan), hanya tampilkan 20ft
                    ukuranKontainerSelect.add(new Option('20 ft', '20'));
                    ukuranKontainerSelect.value = '20';
                } else {
                    // Untuk kegiatan normal atau tarik/antar perbaikan, tampilkan semua ukuran dengan logika jumlah
                    if (jumlah > 1) {
                        originalOptions.forEach(option => {
                            if (option.value === '10' || option.value === '20' || option.value === '') {
                                ukuranKontainerSelect.add(new Option(option.text, option.value));
                            }
                        });
                        if (currentValue === '40') {
                            ukuranKontainerSelect.value = '';
                        } else {
                            ukuranKontainerSelect.value = currentValue;
                        }
                    } else {
                        originalOptions.forEach(option => {
                            ukuranKontainerSelect.add(new Option(option.text, option.value));
                        });
                        ukuranKontainerSelect.value = currentValue;
                    }
                }
            }

            jumlahKontainerInput.addEventListener('input', () => {
                updateFormBasedOnJumlah();
                updateUangJalan();
            });

            // Event listener untuk perubahan kegiatan
            kegiatanSelect.addEventListener('change', () => {
                updateFormBasedOnJumlah();
                toggleTujuanDisplay();
                generateMemoNumber(); // Update nomor memo saat kegiatan berubah
            });

            // Logika untuk Uang Jalan dan Total Biaya Otomatis
            // checkbox id was renamed to "antar_lokasi_checkbox" in the template; support both
            const antarSewaCheckbox = document.getElementById('antar_sewa_checkbox') || document.getElementById('antar_lokasi_checkbox');
            tujuanSelect = document.getElementById('tujuan');
            const uangJalanInput = document.getElementById('jumlah_uang_jalan');
            const adjustmentInput = document.getElementById('adjustment');
            const totalSetelahAdjInput = document.getElementById('total_setelah_adjustment');

            function updateTotalBiaya() {
                const uangJalan = parseFloat(uangJalanInput.value) || 0;
                const adjustment = parseFloat(adjustmentInput.value) || 0;
                totalSetelahAdjInput.value = uangJalan + adjustment;
            }

            // Prepare a map of tujuan by id using embedded option data
            const tujuanOptions = Array.from(tujuanSelect.options).filter(o => o.value).map(o => {
                try { return JSON.parse(o.getAttribute('data-json')); } catch (e) { return null; }
            }).filter(Boolean).reduce((acc, t) => { acc[t.id] = t; return acc; }, {});

            updateUangJalan = function() {
                const isAntarSewa = antarSewaCheckbox ? antarSewaCheckbox.checked : false;
                const tujuanId = tujuanSelect.value;
                const ukuran = ukuranKontainerSelect.value;
                const jumlah = parseInt(jumlahKontainerInput.value, 10) || 0;
                const selectedKegiatan = kegiatanSelect.value.toLowerCase();
                const isTarikAntarPerbaikan = (selectedKegiatan.includes('tarik') || selectedKegiatan.includes('antar')) && selectedKegiatan.includes('perbaikan');
                const isPerbaikanKontainer = (selectedKegiatan.includes('perbaikan kontainer') || selectedKegiatan.includes('perbaikan')) && !isTarikAntarPerbaikan;

                let uangJalan = 0;

                // Khusus untuk perbaikan kontainer (kecuali tarik/antar perbaikan), uang jalan selalu 75.000
                if (isPerbaikanKontainer) {
                    uangJalan = 75000;
                } else {
                    // Untuk kegiatan normal atau tarik/antar perbaikan, gunakan logika normal berdasarkan tujuan
                    // Tentukan ukuran efektif untuk perhitungan harga.
                    const effectiveUkuran = jumlah === 2 ? '40' : ukuran;

                    const tujuanObj = tujuanOptions[tujuanId] || null;
                    if (tujuanObj) {
                        if (isAntarSewa) {
                            uangJalan = (effectiveUkuran === '20' || effectiveUkuran === '10') ? parseFloat(tujuanObj.antar_20) : parseFloat(tujuanObj.antar_40);
                        } else {
                            uangJalan = (effectiveUkuran === '20' || effectiveUkuran === '10') ? parseFloat(tujuanObj.uang_jalan_20) : parseFloat(tujuanObj.uang_jalan_40);
                        }
                    } else {
                        // fallback: gunakan rule lama jika tujuan belum dipilih atau tidak ada di master
                        if (isAntarSewa) {
                            if (effectiveUkuran === '20' || effectiveUkuran === '10') uangJalan = 50000;
                            else uangJalan = 100000;
                        } else {
                            if (effectiveUkuran === '20' || effectiveUkuran === '10') uangJalan = 200000;
                            else uangJalan = 300000;
                        }
                    }
                }

                uangJalanInput.value = uangJalan;
                updateTotalBiaya(); // Panggil update total setiap kali uang jalan berubah
            }

            if (antarSewaCheckbox) {
                antarSewaCheckbox.addEventListener('change', updateUangJalan);
            }
            tujuanSelect.addEventListener('change', updateUangJalan);
            ukuranKontainerSelect.addEventListener('change', updateUangJalan);
            uangJalanInput.addEventListener('input', updateTotalBiaya);
            adjustmentInput.addEventListener('input', updateTotalBiaya);

            updateFormBasedOnJumlah();
            // Panggil fungsi saat halaman dimuat untuk mengatur nilai awal
            updateUangJalan();

            // Fungsi untuk generate Nomor Memo
            function generateMemoNumber() {
                const memoInput = document.getElementById('nomor_memo');
                const selectedKegiatan = kegiatanSelect.value.toLowerCase();
                const isTarikAntarPerbaikan = (selectedKegiatan.includes('tarik') || selectedKegiatan.includes('antar')) && selectedKegiatan.includes('perbaikan');
                const isPerbaikanKontainer = (selectedKegiatan.includes('perbaikan kontainer') || selectedKegiatan.includes('perbaikan')) && !isTarikAntarPerbaikan;

                let prefix = 'MS'; // Default untuk Memo Supir
                let kodeCetak = document.getElementById('kode_cetak').value;
                let formatInfo = 'Format: MS (2 digit) + cetakan (1 digit) + tahun (2 digit) + bulan (2 digit) + running number (7 digit)';

                // Jika kegiatan perbaikan kontainer (termasuk tarik/antar perbaikan), gunakan prefix MP dan kode cetak 1
                if (isPerbaikanKontainer || isTarikAntarPerbaikan) {
                    prefix = 'MP';
                    kodeCetak = '1'; // Selalu 1 untuk semua jenis perbaikan kontainer
                    formatInfo = 'Format: MP (2 digit) + cetakan (1 digit) + tahun (2 digit) + bulan (2 digit) + running number (7 digit)';
                }

                const now = new Date();
                const year = now.getFullYear().toString().slice(-2);
                const month = (now.getMonth() + 1).toString().padStart(2, '0');

                const runningNumber = Date.now().toString().slice(-7);

                const nomorMemo = `${prefix}${kodeCetak}${year}${month}${runningNumber}`;
                memoInput.value = nomorMemo;

                // Update informasi format dan preview
                const formatInfoElement = document.getElementById('memo_format_info');
                const previewElement = document.getElementById('memo_format_preview');

                if (formatInfoElement) {
                    formatInfoElement.innerHTML = `${formatInfo}<br><span class="font-mono text-blue-600">${nomorMemo}</span>`;
                }
            }

            generateMemoNumber();
        });

        // Logika untuk mengubah tampilan tujuan berdasarkan kegiatan
        const tujuanContainer = document.getElementById('tujuan').closest('.lg\\:col-span-3');
        const ruteContainer = document.getElementById('rute_container');
        const antarLokasiContainer = document.getElementById('antar_lokasi_container');
        const dariInput = document.getElementById('dari');
        const keInput = document.getElementById('ke');

        function toggleTujuanDisplay() {
            const selectedKegiatan = kegiatanSelect.value.toLowerCase();

            // Jangan sembunyikan dropdown tujuan untuk kegiatan tarik/antar yang berkaitan dengan perbaikan
            const isTarikAntarPerbaikan = (selectedKegiatan.includes('tarik') || selectedKegiatan.includes('antar')) && selectedKegiatan.includes('perbaikan');
            const isPerbaikanKontainer = (selectedKegiatan.includes('perbaikan kontainer') || selectedKegiatan.includes('perbaikan')) && !isTarikAntarPerbaikan;

            if (isPerbaikanKontainer) {
                // Tampilkan input rute perbaikan, sembunyikan dropdown tujuan
                tujuanContainer.style.display = 'none';
                ruteContainer.style.display = 'block';
                antarLokasiContainer.style.display = 'none'; // Sembunyikan checkbox antar lokasi

                // Hapus required dari dropdown tujuan dan tambahkan ke input rute
                tujuanSelect.removeAttribute('required');
                dariInput.setAttribute('required', 'required');
                keInput.setAttribute('required', 'required');

                // Reset nilai dropdown tujuan
                tujuanSelect.value = '';
            } else if (isTarikAntarPerbaikan) {
                // Untuk tarik/antar kontainer perbaikan, tampilkan kedua: dropdown tujuan DAN input rute
                tujuanContainer.style.display = 'block';
                ruteContainer.style.display = 'block';
                antarLokasiContainer.style.display = 'flex'; // Tampilkan checkbox antar lokasi

                // Kedua field wajib diisi
                tujuanSelect.setAttribute('required', 'required');
                dariInput.setAttribute('required', 'required');
                keInput.setAttribute('required', 'required');
            } else {
                // Tampilkan dropdown tujuan, sembunyikan input rute perbaikan
                tujuanContainer.style.display = 'block';
                ruteContainer.style.display = 'none';
                antarLokasiContainer.style.display = 'flex'; // Tampilkan checkbox antar lokasi

                // Tambahkan required ke dropdown tujuan dan hapus dari input rute
                tujuanSelect.setAttribute('required', 'required');
                dariInput.removeAttribute('required');
                keInput.removeAttribute('required');

                // Reset nilai input rute
                dariInput.value = '';
                keInput.value = '';
            }

            // Update perhitungan uang jalan
            updateUangJalan();
            updateFormBasedOnJumlah();
        }

        // Panggil fungsi saat halaman dimuat untuk mengatur tampilan awal
        toggleTujuanDisplay();
    </script>
@endpush
