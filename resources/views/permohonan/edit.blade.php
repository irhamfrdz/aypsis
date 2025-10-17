@extends('layouts.app')

@section('title', 'Edit Permohonan')
@section('page_title', 'Edit Memo: ' . $permohonan->nomor_memo)

@push('styles')
    {{-- Tambahkan CSS untuk Choices.js --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        .choices__inner { backgroun                if (tujuanData) {
                    const effectiveUkuran = jumlah === 2 ? '40' : ukuran;

                    if (isAntar) {
                        // Gunakan harga antar berdasarkan ukuran
                        if (effectiveUkuran === '20' || effectiveUkuran === '10') {
                            return parseFloat(tujuanData.antar_20) || 0;
                        } else if (effectiveUkuran === '40') {
                            return parseFloat(tujuanData.antar_40) || 0;
                        }
                    } else {
                        // Gunakan harga uang jalan berdasarkan ukuran
                        if (effectiveUkuran === '20' || effectiveUkuran === '10') {
                            return parseFloat(tujuanData.uang_jalan_20) || 0;
                        } else if (effectiveUkuran === '40') {
                            return parseFloat(tujuanData.uang_jalan_40) || 0;
                        }
                    }
                } else {order-radius: 0.375rem; border: 1px solid #d1d5db; font-size: 1rem; padding: 0.5rem 0.75rem; min-height: 46px; }
        .is-focused .choices__inner, .is-open .choices__inner { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5); }
        .choices__input { background-color: #f3f4f6; font-size: 1rem; }
        .choices__list--dropdown { background-color: #e5e7eb; border-color: #d1d5db; }
        .choices__list--dropdown .choices__item--selectable.is-highlighted { background-color: #c7d2fe; color: #3730a3; }
        .choices[data-type*="select-multiple"] .choices__item { background-color: #d1d5db; border: 1px solid #9ca3af; color: #1f2937; }
    </style>
@endpush

@section('content')
    <div class="bg-white shadow-md rounded-lg">
        <form id="permohonanForm" action="{{ route('permohonan.update', $permohonan->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            @php
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
                $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-300 shadow-sm text-base p-2.5";
            @endphp

            {{-- Bagian 1: Informasi Umum --}}
            <fieldset class="border p-4 rounded-md mb-6">
                <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Umum</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                    <div>
                        <label for="nomor_memo" class="block text-sm font-medium text-gray-700 mb-1">Nomor Memo</label>
                        <input type="text" id="nomor_memo" class="{{ $readonlyInputClasses }}" value="{{ $permohonan->nomor_memo }}" readonly>
                    </div>
                    <div>
                        <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Kegiatan</label>
                        <select name="kegiatan" id="kegiatan" class="{{ $inputClasses }}" required>
                            @foreach($kegiatans as $k)
                                <option value="{{ $k->kode_kegiatan }}" {{ old('kegiatan', $permohonan->kegiatan) == $k->kode_kegiatan ? 'selected' : '' }}>{{ $k->nama_kegiatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="vendor_perusahaan" class="block text-sm font-medium text-gray-700 mb-1">Vendor Perusahaan</label>
                        <select name="vendor_perusahaan" id="vendor_perusahaan" class="{{ $inputClasses }}" required>
                            <option value="">Pilih Vendor</option>
                            @if(isset($vendors) && $vendors->count() > 0)
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->nama_vendor }}" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == $vendor->nama_vendor ? 'selected' : '' }}>
                                        {{ $vendor->nama_vendor }}
                                    </option>
                                @endforeach
                            @else
                                {{-- Fallback jika tidak ada data vendor --}}
                                <option value="AYP" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == 'AYP' ? 'selected' : '' }}>AYP</option>
                                <option value="ZONA" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                                <option value="SOC" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == 'SOC' ? 'selected' : '' }}>SOC</option>
                                <option value="DPE" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == 'DPE' ? 'selected' : '' }}>DPE</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="tanggal_memo" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Memo</label>
                        <input type="date" name="tanggal_memo" id="tanggal_memo" class="{{ $inputClasses }}" value="{{ old('tanggal_memo', $permohonan->tanggal_memo) }}">
                    </div>
                    <div>
                        <label for="supir_id" class="block text-sm font-medium text-gray-700 mb-1">Supir (Nama Panggilan)</label>
                        <select name="supir_id" id="supir_id" required>
                            <option value="">Cari atau pilih supir...</option>
                            @foreach ($supirs as $supir)
                                <option value="{{ $supir->id }}" data-plat="{{ $supir->plat }}" {{ old('supir_id', $permohonan->supir_id) == $supir->id ? 'selected' : '' }}>
                                    {{ $supir->nama_panggilan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="plat_nomor" class="block text-sm font-medium text-gray-700 mb-1">No Plat (Otomatis)</label>
                        <input type="text" id="plat_nomor" class="{{ $readonlyInputClasses }}" value="{{ $permohonan->plat_nomor }}" readonly>
                    </div>
                    <div>
                        <label for="kenek" class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                        <input type="text" name="kenek" id="kenek" class="{{ $inputClasses }}" value="{{ old('kenek', $permohonan->kenek) }}">
                    </div>
                </div>
            </fieldset>

            {{-- Bagian 2: Informasi Kontainer & Tujuan --}}
            <fieldset class="border p-4 rounded-md mb-6">
                <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Kontainer & Tujuan</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                    <div>
                        <label for="jumlah_kontainer" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kontainer</label>
                        <input type="number" name="jumlah_kontainer" id="jumlah_kontainer" class="{{ $inputClasses }}" value="{{ old('jumlah_kontainer', $permohonan->kontainers->count() ?: 1) }}" min="1" required>
                    </div>
                    <div>
                        <label for="ukuran" class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kontainer</label>
                        <select name="ukuran" id="ukuran" class="{{ $inputClasses }}" required>
                            <option value="10" {{ old('ukuran', $permohonan->ukuran) == '10' ? 'selected' : '' }}>10 ft</option>
                            <option value="20" {{ old('ukuran', $permohonan->ukuran) == '20' ? 'selected' : '' }}>20 ft</option>
                            <option value="40" {{ old('ukuran', $permohonan->ukuran) == '40' ? 'selected' : '' }}>40 ft</option>
                        </select>
                    </div>
                    <div>
                        <label for="no_chasis" class="block text-sm font-medium text-gray-700 mb-1">No Chasis</label>
                        <input type="text" name="no_chasis" id="no_chasis" class="{{ $inputClasses }}" value="{{ old('no_chasis', $permohonan->no_chasis) }}">
                    </div>
                    {{-- Rute Tujuan --}}
                    <div class="lg:col-span-3" id="rute_container">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rute Tujuan</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="dari" class="block text-xs font-medium text-gray-600 mb-1">Dari</label>
                                <select name="dari" id="dari" class="{{ $inputClasses }}" required>
                                    <option value="">Pilih Lokasi Asal</option>
                                    @isset($tujuans)
                                        @php
                                            $uniqueDari = collect($tujuans)->pluck('dari')->filter()->unique()->sort();
                                        @endphp
                                        @if($uniqueDari->count() > 0)
                                            @foreach($uniqueDari as $location)
                                                <option value="{{ $location }}" {{ old('dari', $permohonan->dari) == $location ? 'selected' : '' }}>{{ $location }}</option>
                                            @endforeach
                                        @else
                                            {{-- Fallback to hardcoded options only if no database data --}}
                                            <option value="Dermaga" {{ old('dari', $permohonan->dari) == 'Dermaga' ? 'selected' : '' }}>Dermaga</option>
                                            <option value="Merak" {{ old('dari', $permohonan->dari) == 'Merak' ? 'selected' : '' }}>Merak</option>
                                        @endif
                                    @else
                                        {{-- Fallback when tujuans not passed --}}
                                        <option value="Dermaga" {{ old('dari', $permohonan->dari) == 'Dermaga' ? 'selected' : '' }}>Dermaga</option>
                                        <option value="Merak" {{ old('dari', $permohonan->dari) == 'Merak' ? 'selected' : '' }}>Merak</option>
                                    @endisset
                                </select>
                            </div>
                            <div>
                                <label for="ke" class="block text-xs font-medium text-gray-600 mb-1">Ke</label>
                                <select name="ke" id="ke" class="{{ $inputClasses }}" required>
                                    <option value="">Pilih Lokasi Tujuan</option>
                                    @isset($tujuans)
                                        @php
                                            $uniqueKe = collect($tujuans)->pluck('ke')->filter()->unique()->sort();
                                        @endphp
                                        @if($uniqueKe->count() > 0)
                                            @foreach($uniqueKe as $location)
                                                <option value="{{ $location }}" {{ old('ke', $permohonan->ke) == $location ? 'selected' : '' }}>{{ $location }}</option>
                                            @endforeach
                                        @else
                                            {{-- Fallback to hardcoded options only if no database data --}}
                                            <option value="Semut" {{ old('ke', $permohonan->ke) == 'Semut' ? 'selected' : '' }}>Semut</option>
                                            <option value="Jakarta" {{ old('ke', $permohonan->ke) == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                        @endif
                                    @else
                                        {{-- Fallback when tujuans not passed --}}
                                        <option value="Semut" {{ old('ke', $permohonan->ke) == 'Semut' ? 'selected' : '' }}>Semut</option>
                                        <option value="Jakarta" {{ old('ke', $permohonan->ke) == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                    @endisset
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="nomor_kontainer_container" class="lg:col-span-3 space-y-4">
                        {{-- Input akan digenerate oleh JavaScript di sini --}}
                    </div>
                </div>


                <!-- Tombol tambah kontainer -->
                <div class="md:col-span-2">
                    <button type="button" id="add_container" class="mt-2 text-sm text-indigo-600 hover:text-indigo-900 font-semibold">
                        + Tambah Kontainer
                    </button>
                </div>
            </div>

            {{-- Checkbox Antar Lokasi --}}
            <div class="lg:col-span-3 flex items-center pt-2" id="antar_lokasi_container">
                <input id="antar_lokasi_checkbox" name="antar_sewa" value="1" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('antar_sewa', $permohonan->antar_sewa) ? 'checked' : '' }}>
                <label for="antar_lokasi_checkbox" class="ml-2 block text-sm font-medium text-gray-900">Antar Lokasi</label>
            </div>
        </div>
    </fieldset>

    <h3 class="text-lg font-semibold text-gray-800 mb-4">Biaya & Keuangan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="jumlah_uang_jalan" class="block text-sm font-medium text-gray-700">Jumlah Uang Jalan (Otomatis)</label>
                    <input type="number" name="jumlah_uang_jalan" id="jumlah_uang_jalan" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" value="{{ $permohonan->jumlah_uang_jalan }}" readonly>
                </div>

                <div>
                    <label for="jumlah_pelancar" class="block text-sm font-medium text-gray-700">Jumlah Pelancar</label>
                    <input type="number" name="jumlah_pelancar" id="jumlah_pelancar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $permohonan->jumlah_pelancar }}">
                </div>

                <div>
                    <label for="jumlah_mel" class="block text-sm font-medium text-gray-700">Jumlah MEL</label>
                    <input type="number" name="jumlah_mel" id="jumlah_mel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $permohonan->jumlah_mel }}">
                </div>

                <div>
                    <label for="jumlah_kawalan" class="block text-sm font-medium text-gray-700">Jumlah Kawalan</label>
                    <input type="number" name="jumlah_kawalan" id="jumlah_kawalan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $permohonan->jumlah_kawalan }}">
                </div>

                <div>
                    <label for="jumlah_parkir" class="block text-sm font-medium text-gray-700">Jumlah Parkir</label>
                    <input type="number" name="jumlah_parkir" id="jumlah_parkir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $permohonan->jumlah_parkir }}">
                </div>

                <div>
                    <label for="total_biaya_display" class="block text-sm font-medium text-gray-700">Total Biaya (Otomatis)</label>
                    <input type="text" id="total_biaya_display" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" value="{{ $permohonan->total_biaya }}" readonly>
                </div>

                <div>
                    <label for="adjustment" class="block text-sm font-medium text-gray-700">Adjustment</label>
                    <input type="number" name="adjustment" id="adjustment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $permohonan->adjustment }}">
                </div>

                <div>
                    <label for="alasan_adjustment" class="block text-sm font-medium text-gray-700">Alasan Adjustment</label>
                    <input type="text" name="alasan_adjustment" id="alasan_adjustment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $permohonan->alasan_adjustment }}">
                </div>

                <div>
                    <label for="total_harga_setelah_adj_display" class="block text-sm font-medium text-gray-700">Total Harga Setelah Adjustment (Otomatis)</label>
                    <input type="text" id="total_harga_setelah_adj_display" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" value="{{ $permohonan->total_harga_setelah_adj }}" readonly>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tambahan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $permohonan->catatan }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran</label>
                    @if ($permohonan->lampiran)
                        <p class="text-sm text-gray-500 mb-2">Lampiran saat ini: <a href="{{ Storage::url($permohonan->lampiran) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Lampiran</a></p>
                    @endif
                    <input type="file" name="lampiran" id="lampiran" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('permohonan.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Perbarui Permohonan
                </button>
            </div>
        </form>
    </div>

    <script>
        // Variabel global yang akan digunakan di luar DOMContentLoaded
        let kegiatanSelect, updateTotals;

        // Data tujuan dari database untuk filtering dinamis
        const tujuansData = @json($tujuans ?? []);

        document.addEventListener('DOMContentLoaded', function () {
            // Fungsi untuk format angka dalam format Indonesia (dengan titik sebagai pemisah ribuan)
            function formatNumberIndonesia(number) {
                // Pastikan number adalah number, bukan string
                const num = typeof number === 'string' ? parseFloat(number) : number;
                // Jika bukan number valid, return '0'
                if (isNaN(num)) return '0';
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Fungsi untuk parsing angka dari format Indonesia ke number
            function parseNumberIndonesia(formattedNumber) {
                return parseFloat(formattedNumber.replace(/\./g, '')) || 0;
            }

            // elements
            const supirSelect = document.getElementById('supir_id');
            const platInput = document.getElementById('plat_nomor');
            const tujuanSelect = document.getElementById('tujuan');
            kegiatanSelect = document.getElementById('kegiatan');
            const uangJalanInput = document.getElementById('jumlah_uang_jalan');
            const jumlahKontainerInput = document.getElementById('jumlah_kontainer');
            const ukuranKontainerSelect = document.getElementById('ukuran');
            const adjustmentInput = document.getElementById('adjustment');
            const totalHargaDisplay = document.getElementById('total_harga_setelah_adj_display');
            const dariSelect = document.getElementById('dari');
            const keSelect = document.getElementById('ke');

            // embed tujuan options map
            const tujuanOptions = Array.from(tujuanSelect.options).filter(o => o.value).map(o => {
                try { return JSON.parse(o.getAttribute('data-json')); } catch (e) { return null; }
            }).filter(Boolean).reduce((acc, t) => { acc[t.id] = t; return acc; }, {});

            supirSelect.addEventListener('change', function () {
                const opt = supirSelect.options[supirSelect.selectedIndex];
                platInput.value = opt ? (opt.dataset.plat || '') : '';
            });

            // Logika untuk ukuran kontainer berdasarkan kegiatan
            const originalOptions = Array.from(ukuranKontainerSelect.options);

            function updateFormBasedOnJumlah() {
                const jumlah = parseInt(jumlahKontainerInput.value, 10) || 0;
                const currentValue = ukuranKontainerSelect.value;
                const selectedKegiatan = kegiatanSelect.value.toLowerCase();
                const isPerbaikanKontainer = selectedKegiatan.includes('perbaikan kontainer') || selectedKegiatan.includes('perbaikan');

                while (ukuranKontainerSelect.options.length > 0) {
                    ukuranKontainerSelect.remove(0);
                }

                if (isPerbaikanKontainer) {
                    // Untuk perbaikan kontainer, hanya tampilkan 20ft
                    ukuranKontainerSelect.add(new Option('20 ft', '20'));
                    ukuranKontainerSelect.value = '20';
                } else {
                    // Untuk kegiatan normal, tampilkan semua ukuran dengan logika jumlah
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

            function computeUangJalan() {
                const isAntar = !!document.getElementById('antar_lokasi_checkbox') && document.getElementById('antar_lokasi_checkbox').checked;
                const dari = dariSelect.value;
                const ke = keSelect.value;
                const jumlah = parseInt(jumlahKontainerInput.value, 10) || 0;
                const ukuran = ukuranKontainerSelect.value;
                const selectedKegiatan = kegiatanSelect.value.toLowerCase();
                const kegiatanText = kegiatanSelect.options[kegiatanSelect.selectedIndex]?.text.toLowerCase() || '';
                const isPerbaikanKontainer = selectedKegiatan.includes('perbaikan') ||
                                           kegiatanText.includes('perbaikan kontainer') ||
                                           kegiatanText.includes('perbaikan');

                // Khusus untuk perbaikan kontainer, uang jalan selalu 75.000
                if (isPerbaikanKontainer) {
                    return 75000;
                }

                // Cari data tujuan dari database berdasarkan dari dan ke
                const tujuanData = tujuansData.find(tujuan =>
                    tujuan.dari === dari && tujuan.ke === ke
                );

                if (tujuanData) {
                    const effectiveUkuran = jumlah === 2 ? '40' : ukuran;

                    if (isAntar) {
                        // Gunakan harga antar berdasarkan ukuran
                        if (effectiveUkuran === '20' || effectiveUkuran === '10') {
                            return tujuanData.antar_20 || 0;
                        } else if (effectiveUkuran === '40') {
                            return tujuanData.antar_40 || 0;
                        }
                    } else {
                        // Gunakan harga uang jalan berdasarkan ukuran
                        if (effectiveUkuran === '20' || effectiveUkuran === '10') {
                            return tujuanData.uang_jalan_20 || 0;
                        } else if (effectiveUkuran === '40') {
                            return tujuanData.uang_jalan_40 || 0;
                        }
                    }
                } else {
                    // Fallback ke harga default jika tidak ada data tujuan
                    const effectiveUkuran = jumlah === 2 ? '40' : ukuran;
                    if (isAntar) {
                        return effectiveUkuran === '20' || effectiveUkuran === '10' ? 250000 : 350000;
                    } else {
                        return effectiveUkuran === '20' || effectiveUkuran === '10' ? 200000 : 300000;
                    }
                }

                return 0;
            }

            // Fungsi untuk update dropdown 'ke' berdasarkan pilihan 'dari'
            function updateKeOptions() {
                const dariValue = dariSelect.value;

                // Simpan nilai yang sedang dipilih
                const currentKeValue = keSelect.value;

                // Kosongkan dropdown ke
                keSelect.innerHTML = '<option value="">Pilih Lokasi Tujuan</option>';

                if (dariValue) {
                    // Filter tujuan yang tersedia dari lokasi yang dipilih
                    const availableDestinations = tujuansData
                        .filter(tujuan => tujuan.dari === dariValue && tujuan.ke)
                        .map(tujuan => tujuan.ke)
                        .filter((value, index, self) => self.indexOf(value) === index) // Remove duplicates
                        .sort();

                    // Tambahkan opsi yang tersedia
                    availableDestinations.forEach(destination => {
                        const option = document.createElement('option');
                        option.value = destination;
                        option.textContent = destination;
                        // Jika nilai sebelumnya masih tersedia, tetap pilih
                        if (destination === currentKeValue) {
                            option.selected = true;
                        }
                        keSelect.appendChild(option);
                    });

                    // Jika tidak ada tujuan yang tersedia, tambahkan opsi kosong
                    if (availableDestinations.length === 0) {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Tidak ada tujuan tersedia';
                        option.disabled = true;
                        keSelect.appendChild(option);
                    }
                } else {
                    // Jika tidak ada dari yang dipilih, tampilkan semua opsi ke yang tersedia
                    const allDestinations = tujuansData
                        .filter(tujuan => tujuan.ke)
                        .map(tujuan => tujuan.ke)
                        .filter((value, index, self) => self.indexOf(value) === index)
                        .sort();

                    allDestinations.forEach(destination => {
                        const option = document.createElement('option');
                        option.value = destination;
                        option.textContent = destination;
                        keSelect.appendChild(option);
                    });
                }

                // Update perhitungan uang jalan setelah dropdown berubah
                updateTotals();
            }

            // Event listener untuk perubahan dropdown dari
            dariSelect.addEventListener('change', updateKeOptions);

            updateTotals = function() {
                const uangJalan = computeUangJalan();
                uangJalanInput.value = formatNumberIndonesia(uangJalan);
                const pelancar = parseNumberIndonesia(document.getElementById('jumlah_pelancar').value) || 0;
                const mel = parseNumberIndonesia(document.getElementById('jumlah_mel').value) || 0;
                const kawalan = parseNumberIndonesia(document.getElementById('jumlah_kawalan').value) || 0;
                const parkir = parseNumberIndonesia(document.getElementById('jumlah_parkir').value) || 0;
                const totalBiaya = uangJalan + pelancar + mel + kawalan + parkir;
                document.getElementById('total_biaya_display').value = formatNumberIndonesia(totalBiaya);
                const adjustment = parseNumberIndonesia(adjustmentInput.value) || 0;
                totalHargaDisplay.value = formatNumberIndonesia(totalBiaya + adjustment);
            }

            // attach listeners
            dariSelect.addEventListener('change', updateTotals);
            keSelect.addEventListener('change', updateTotals);
            jumlahKontainerInput.addEventListener('input', updateTotals);
            ukuranKontainerSelect.addEventListener('change', updateTotals);

            // Event listener untuk adjustment dengan formatting
            adjustmentInput.addEventListener('input', function(e) {
                // Hapus semua karakter non-digit kecuali titik
                let value = e.target.value.replace(/[^\d.]/g, '');
                // Format ulang nilai
                if (value) {
                    const numericValue = parseFloat(value.replace(/\./g, ''));
                    if (!isNaN(numericValue)) {
                        e.target.value = formatNumberIndonesia(numericValue);
                    }
                }
                updateTotals();
            });

            // Event listener untuk input uang jalan dengan formatting
            uangJalanInput.addEventListener('input', function(e) {
                // Hapus semua karakter non-digit kecuali titik
                let value = e.target.value.replace(/[^\d.]/g, '');
                // Format ulang nilai
                if (value) {
                    const numericValue = parseFloat(value.replace(/\./g, ''));
                    if (!isNaN(numericValue)) {
                        e.target.value = formatNumberIndonesia(numericValue);
                    }
                }
                updateTotals();
            });

            // Event listeners untuk field lainnya dengan formatting
            ['jumlah_pelancar','jumlah_mel','jumlah_kawalan','jumlah_parkir'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', function(e) {
                        // Hapus semua karakter non-digit kecuali titik
                        let value = e.target.value.replace(/[^\d.]/g, '');
                        // Format ulang nilai
                        if (value) {
                            const numericValue = parseFloat(value.replace(/\./g, ''));
                            if (!isNaN(numericValue)) {
                                e.target.value = formatNumberIndonesia(numericValue);
                            }
                        }
                        updateTotals();
                    });
                }
            });

            // Event listener untuk checkbox antar lokasi
            const antarLokasiCheckbox = document.getElementById('antar_lokasi_checkbox');
            if (antarLokasiCheckbox) {
                antarLokasiCheckbox.addEventListener('change', updateTotals);
            }

            // Logika untuk mengubah tampilan form berdasarkan kegiatan
            const ruteContainer = document.getElementById('rute_container');
            const antarLokasiContainer = document.getElementById('antar_lokasi_container');

            function toggleFormDisplay() {
                const selectedKegiatan = kegiatanSelect.value.toLowerCase();
                const kegiatanText = kegiatanSelect.options[kegiatanSelect.selectedIndex]?.text.toLowerCase() || '';

                const isPerbaikanKontainer = selectedKegiatan.includes('perbaikan') ||
                                           kegiatanText.includes('perbaikan kontainer') ||
                                           kegiatanText.includes('perbaikan');

                if (isPerbaikanKontainer) {
                    // Untuk perbaikan kontainer, sembunyikan checkbox antar lokasi
                    antarLokasiContainer.style.display = 'none';
                } else {
                    // Tampilkan checkbox antar lokasi untuk kegiatan normal
                    antarLokasiContainer.style.display = 'flex';
                }

                // Update perhitungan uang jalan
                updateTotals();
                updateFormBasedOnJumlah();
            }

            // Event listener untuk perubahan kegiatan
            kegiatanSelect.addEventListener('change', () => {
                updateFormBasedOnJumlah();
                toggleFormDisplay();
            });

            // Panggil fungsi saat halaman dimuat untuk mengatur tampilan awal
            toggleFormDisplay();
            updateFormBasedOnJumlah();
            updateKeOptions(); // Initialize ke dropdown based on current dari value

            updateTotals();
        });
    </script>
@endsection
