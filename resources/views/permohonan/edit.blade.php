@extends('layouts.app')

@section('title', 'Edit Permohonan')
@section('page_title', 'Edit Memo: ' . $permohonan->nomor_memo)

@push('styles')
    {{-- Tambahkan CSS untuk Choices.js --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        .choices__inner { background-color: #f3f4f6; border-radius: 0.375rem; border: 1px solid #d1d5db; font-size: 1rem; padding: 0.5rem 0.75rem; min-height: 46px; }
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
                            <option value="pengiriman" {{ old('kegiatan', $permohonan->kegiatan) == 'pengiriman' ? 'selected' : '' }}>Pengiriman</option>
                            <option value="pengambilan" {{ old('kegiatan', $permohonan->kegiatan) == 'pengambilan' ? 'selected' : '' }}>Pengambilan</option>
                        </select>
                    </div>
                    <div>
                        <label for="vendor_perusahaan" class="block text-sm font-medium text-gray-700 mb-1">Vendor Perusahaan</label>
                        <select name="vendor_perusahaan" id="vendor_perusahaan" class="{{ $inputClasses }}" required>
                            <option value="AYP" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == 'AYP' ? 'selected' : '' }}>AYP</option>
                            <option value="ZONA" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                            <option value="SOC" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == 'SOC' ? 'selected' : '' }}>SOC</option>
                            <option value="DPE" {{ old('vendor_perusahaan', $permohonan->vendor_perusahaan) == 'DPE' ? 'selected' : '' }}>DPE</option>
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
                    <div class="lg:col-span-3">
                        <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                        <select name="tujuan_id" id="tujuan" class="{{ $inputClasses }}" required>
                            <option value="">Pilih Tujuan</option>
                            @foreach($tujuans as $t)
                                @php $label = trim((($t->wilayah ?? '') ? $t->wilayah : '') . ' ' . (($t->rute ?? '') ? '- '.$t->rute : '')); @endphp
                                <option value="{{ $t->id }}" data-json='@json($t)' {{ $permohonan->tujuan === $label ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
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
        document.addEventListener('DOMContentLoaded', function () {
            // elements
            const supirSelect = document.getElementById('supir_id');
            const platInput = document.getElementById('plat_nomor');
            const tujuanSelect = document.getElementById('tujuan');
            const uangJalanInput = document.getElementById('jumlah_uang_jalan');
            const jumlahKontainerInput = document.getElementById('jumlah_kontainer');
            const ukuranKontainerSelect = document.getElementById('ukuran');
            const adjustmentInput = document.getElementById('adjustment');
            const totalHargaDisplay = document.getElementById('total_harga_setelah_adj_display');

            // embed tujuan options map
            const tujuanOptions = Array.from(tujuanSelect.options).filter(o => o.value).map(o => {
                try { return JSON.parse(o.getAttribute('data-json')); } catch (e) { return null; }
            }).filter(Boolean).reduce((acc, t) => { acc[t.id] = t; return acc; }, {});

            supirSelect.addEventListener('change', function () {
                const opt = supirSelect.options[supirSelect.selectedIndex];
                platInput.value = opt ? (opt.dataset.plat || '') : '';
            });

            function computeUangJalan() {
                const isAntar = !!document.getElementById('antar_sewa_checkbox') && document.getElementById('antar_sewa_checkbox').checked;
                const tujuanId = tujuanSelect.value;
                const jumlah = parseInt(jumlahKontainerInput.value, 10) || 0;
                const ukuran = ukuranKontainerSelect.value;
                const effectiveUkuran = jumlah === 2 ? '40' : ukuran;

                const tujuanObj = tujuanOptions[tujuanId] || null;
                if (tujuanObj) {
                    if (isAntar) return (effectiveUkuran === '20' || effectiveUkuran === '10') ? parseFloat(tujuanObj.antar_20) : parseFloat(tujuanObj.antar_40);
                    return (effectiveUkuran === '20' || effectiveUkuran === '10') ? parseFloat(tujuanObj.uang_jalan_20) : parseFloat(tujuanObj.uang_jalan_40);
                }
                // fallback default
                if (isAntar) return (effectiveUkuran === '20' || effectiveUkuran === '10') ? 50000 : 100000;
                return (effectiveUkuran === '20' || effectiveUkuran === '10') ? 200000 : 300000;
            }

            function updateTotals() {
                const uangJalan = computeUangJalan();
                uangJalanInput.value = uangJalan;
                const pelancar = parseFloat(document.getElementById('jumlah_pelancar').value) || 0;
                const mel = parseFloat(document.getElementById('jumlah_mel').value) || 0;
                const kawalan = parseFloat(document.getElementById('jumlah_kawalan').value) || 0;
                const parkir = parseFloat(document.getElementById('jumlah_parkir').value) || 0;
                const totalBiaya = uangJalan + pelancar + mel + kawalan + parkir;
                document.getElementById('total_biaya_display').value = totalBiaya;
                const adjustment = parseFloat(adjustmentInput.value) || 0;
                totalHargaDisplay.value = totalBiaya + adjustment;
            }

            // attach listeners
            tujuanSelect.addEventListener('change', updateTotals);
            jumlahKontainerInput.addEventListener('input', updateTotals);
            ukuranKontainerSelect.addEventListener('change', updateTotals);
            adjustmentInput.addEventListener('input', updateTotals);
            ['jumlah_pelancar','jumlah_mel','jumlah_kawalan','jumlah_parkir'].forEach(id => {
                const el = document.getElementById(id); if (el) el.addEventListener('input', updateTotals);
            });

            updateTotals();
        });
    </script>
@endsection
