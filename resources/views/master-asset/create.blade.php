@extends('layouts.app')

@section('title', 'Tambah Asset Baru')
@section('page_title', 'Tambah Asset')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-color: #d1d5db;
        border-radius: 0.75rem;
        height: 38px;
        padding: 4px 6px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #374151;
        padding-left: 4px;
    }
    .select2-dropdown {
        border-radius: 0.75rem;
        border-color: #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        font-size: 0.75rem;
        overflow: hidden;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 0.5rem;
        border-color: #d1d5db;
        padding: 4px 8px;
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('asset.index') }}" class="p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">Tambah Asset Baru</h1>
                <p class="text-xs text-gray-500">Lengkapi formulir berikut untuk mendaftarkan asset baru perusahaan.</p>
            </div>
        </div>
        <a href="{{ route('asset.index') }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <p class="font-semibold text-sm mb-1">Periksa kembali formulir Anda:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('asset.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Section 1: Informasi Utama -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">1</div>
                <h2 class="text-sm font-bold text-gray-800">Informasi Pokok Asset</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Asset <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_asset" value="{{ old('kode_asset', $nextKode) }}" required class="w-full px-3 py-2 text-xs font-mono border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Asset <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_asset" value="{{ old('nama_asset') }}" required placeholder="Contoh: Laptop Lenovo ThinkPad T14 Gen 3" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="nama_merk" class="block text-xs font-semibold text-gray-700 mb-1">Nama Merk</label>
                    <input type="text" name="nama_merk" id="nama_merk" value="{{ old('nama_merk') }}" maxlength="150" placeholder="Contoh: ASUS, Lenovo, Samsung" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Seri / Nomor Mesin</label>
                    <input type="text" name="nomor_seri" value="{{ old('nomor_seri') }}" maxlength="100" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lokasi</label>
                    <select name="lokasi" id="lokasi" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Lokasi</option>
                        @foreach($lokasis as $lokasi)
                            <option value="{{ $lokasi }}" {{ old('lokasi') === $lokasi ? 'selected' : '' }}>{{ $lokasi }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="lokasi-asset-field">
                    <label for="lokasi_asset" class="block text-xs font-semibold text-gray-700 mb-1">Lokasi Asset</label>
                    <select name="lokasi_asset" id="lokasi_asset" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Lokasi Asset</option>
                        @foreach(\App\Models\Asset::LOKASI_ASSET_OPTIONS as $lokasiAsset)
                            <option value="{{ $lokasiAsset }}" data-kapal="0" @selected(old('lokasi_asset') === $lokasiAsset)>{{ $lokasiAsset }}</option>
                        @endforeach
                        @foreach(\App\Models\Asset::LOKASI_ASSET_KAPAL_OPTIONS as $lokasiAsset)
                            <option value="{{ $lokasiAsset }}" data-kapal="1" @selected(old('lokasi_asset') === $lokasiAsset)>{{ $lokasiAsset }}</option>
                        @endforeach
                    </select>
                    @error('lokasi_asset')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="kapal-field" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kapal <span class="text-rose-500">*</span></label>
                    <select name="kapal_id" id="kapal_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Kapal</option>
                        @foreach($kapals as $kapal)
                            <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>{{ $kapal->nama_kapal }}{{ $kapal->status !== 'aktif' ? ' (Nonaktif)' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori Asset <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="kategori" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat }}" data-kapal="{{ in_array($kat, \App\Models\Asset::KATEGORI_KAPAL_OPTIONS, true) ? '1' : '0' }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ old('status', 'Tersedia') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kondisi Fisik <span class="text-rose-500">*</span></label>
                    <select name="kondisi" id="kondisi" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Kondisi Fisik</option>
                        @foreach($kondisis as $kd)
                            <option value="{{ $kd }}" data-kapal="{{ in_array($kd, \App\Models\Asset::KONDISI_KAPAL_OPTIONS, true) ? '1' : '0' }}" {{ old('kondisi', 'Baik') == $kd ? 'selected' : '' }}>{{ $kd }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="karyawan-field" class="md:col-span-2 hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Karyawan Pembawa Asset <span class="text-rose-500">*</span></label>
                    <select name="karyawan_id" id="karyawan_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-karyawan">
                        <option value="">-- Pilih Karyawan Pembawa Asset --</option>
                        @foreach($karyawans as $kry)
                            <option value="{{ $kry->id }}" {{ old('karyawan_id') == $kry->id ? 'selected' : '' }}>
                                {{ $kry->nama_lengkap }} ({{ $kry->nik ?? '-' }})@if($kry->divisi) - {{ $kry->divisi }}@endif
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-gray-400 mt-1 block">Pilih karyawan yang saat ini memegang / bertanggung jawab atas asset ini</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Tanda Terima</label>
                    <input type="date" name="tanggal_tanda_terima" value="{{ old('tanggal_tanda_terima') }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <span class="text-[10px] text-gray-400 mt-1 block">Tanggal penyerahan / serah terima asset</span>
                </div>
            </div>
        </div>

        <!-- Section 2: Informasi Pengadaan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">2</div>
                <h2 class="text-sm font-bold text-gray-800">Informasi Pengadaan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label id="tanggal-perolehan-label" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Perolehan</label>
                    <input type="date" name="tanggal_perolehan" value="{{ old('tanggal_perolehan', date('Y-m-d')) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="kuantitas" class="block text-xs font-semibold text-gray-700 mb-1">Kuantitas</label>
                    <input type="number" name="kuantitas" id="kuantitas" min="0" step="0.01" value="{{ old('kuantitas') }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="satuan" class="block text-xs font-semibold text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" id="satuan" maxlength="50" value="{{ old('satuan') }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div id="kapal-detail-fields" class="hidden md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="spesifikasi" class="block text-xs font-semibold text-gray-700 mb-1">Spesifikasi</label>
                        <textarea name="spesifikasi" id="spesifikasi" rows="2" maxlength="2000" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('spesifikasi') }}</textarea>
                    </div>
                    <div>
                        <label for="tanggal_kadaluarsa" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Kadaluwarsa</label>
                        <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa') }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Vendor / Supplier</label>
                    <input type="text" name="vendor" value="{{ old('vendor') }}" placeholder="Nama toko / rekanan pengadaan" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">No. Faktur / Invoice / PO</label>
                    <input type="text" name="nomor_faktur" value="{{ old('nomor_faktur') }}" placeholder="Nomor kuitansi / invoice pembelian" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Section 3: Dokumen & Keterangan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">3</div>
                <h2 class="text-sm font-bold text-gray-800">Dokumen & Keterangan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Foto Asset</label>
                    <input type="file" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-xl cursor-pointer">
                    <span class="text-[10px] text-gray-400 mt-0.5 block">Format: JPG, PNG, WEBP (Maks 5MB)</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lampiran Dokumen (Sertifikat / Manual / Invoice)</label>
                    <input type="file" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-300 rounded-xl cursor-pointer">
                    <span class="text-[10px] text-gray-400 mt-0.5 block">Format: PDF, Word, Image, ZIP (Maks 10MB)</span>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan / Catatan Tambahan</label>
                    <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan kelengkapan, riwayat, atau instruksi khusus..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('asset.index') }}" class="px-5 py-2.5 text-xs font-semibold rounded-xl text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150">
                Simpan Data Asset
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lokasi = document.getElementById('lokasi');
        const lokasiAsset = document.getElementById('lokasi_asset');
        const kategori = document.getElementById('kategori');
        const kondisi = document.getElementById('kondisi');
        const toggleLocationFields = function() {
            const showKapal = lokasi.value === 'Di kapal';
            const showKaryawan = lokasi.value === 'Di bawa oleh karyawan';
            const kapalDetailFields = document.getElementById('kapal-detail-fields');
            kapalDetailFields.classList.toggle('hidden', !showKapal);
            kapalDetailFields.querySelectorAll('input, textarea').forEach(function(field) {
                field.disabled = !showKapal;
            });
            document.getElementById('tanggal-perolehan-label').textContent = showKapal ? 'Tanggal Beli' : 'Tanggal Perolehan';
            Array.from(kategori.options).forEach(function(option) {
                if (option.value) {
                    const available = (option.dataset.kapal === '1') === showKapal;
                    option.hidden = !available;
                    option.disabled = !available;
                }
            });
            if (kategori.selectedOptions[0]?.disabled) {
                kategori.value = '';
            }
            Array.from(kondisi.options).forEach(function(option) {
                if (option.value) {
                    const available = (option.dataset.kapal === '1') === showKapal;
                    option.hidden = !available;
                    option.disabled = !available;
                }
            });
            if (kondisi.selectedOptions[0]?.disabled) {
                kondisi.value = '';
            }
            Array.from(lokasiAsset.options).forEach(function(option) {
                if (option.value) {
                    const available = (option.dataset.kapal === '1') === showKapal;
                    option.hidden = !available;
                    option.disabled = !available;
                }
            });
            if (lokasiAsset.selectedOptions[0]?.disabled) {
                lokasiAsset.value = '';
            }
            document.getElementById('kapal-field').classList.toggle('hidden', !showKapal);
            document.getElementById('kapal_id').required = showKapal;
            document.getElementById('karyawan-field').classList.toggle('hidden', !showKaryawan);
            document.getElementById('karyawan_id').required = showKaryawan;
        };
        lokasi.addEventListener('change', toggleLocationFields);
        toggleLocationFields();

        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
            $('#karyawan_id').select2({
                placeholder: '-- Pilih Karyawan Pembawa Asset --',
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>
@endpush
