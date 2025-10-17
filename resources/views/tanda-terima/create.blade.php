@extends('layouts.app')

@section('title', 'Tambah Tanda Terima Manual')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Tanda Terima Manual</h1>
                <p class="text-gray-600 mt-1">Input tanda terima kontainer tanpa surat jalan</p>
            </div>
            <a href="{{ route('tanda-terima.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Form Tanda Terima</h2>
            <p class="text-sm text-gray-500 mt-1">Isi semua field yang diperlukan</p>
        </div>

        <form action="{{ route('tanda-terima.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Info Alert -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            <strong>Catatan:</strong> Tanda terima yang dibuat manual tidak terhubung dengan surat jalan.
                            Gunakan fitur ini hanya untuk tanda terima khusus.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informasi Dasar Surat Jalan -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                    Informasi Surat Jalan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- No. Surat Jalan -->
                    <div>
                        <label for="no_surat_jalan" class="block text-sm font-medium text-gray-700 mb-2">
                            No. Surat Jalan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="no_surat_jalan"
                               id="no_surat_jalan"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('no_surat_jalan') border-red-500 @enderror"
                               value="{{ old('no_surat_jalan') }}"
                               placeholder="Contoh: SJ-MANUAL-001"
                               required>
                        @error('no_surat_jalan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Surat Jalan -->
                    <div>
                        <label for="tanggal_surat_jalan" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Surat Jalan <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_surat_jalan"
                               id="tanggal_surat_jalan"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_surat_jalan') border-red-500 @enderror"
                               value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}"
                               required>
                        @error('tanggal_surat_jalan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supir -->
                    <div>
                        <label for="supir" class="block text-sm font-medium text-gray-700 mb-2">
                            Supir
                        </label>
                        <input type="text"
                               name="supir"
                               id="supir"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('supir') border-red-500 @enderror"
                               value="{{ old('supir') }}"
                               placeholder="Nama supir">
                        @error('supir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kegiatan -->
                    <div>
                        <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Kegiatan
                        </label>
                        <select name="kegiatan"
                                id="kegiatan"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kegiatan') border-red-500 @enderror">
                            <option value="">-- Pilih Kegiatan --</option>
                            @foreach($masterKegiatans as $kegiatan)
                                <option value="{{ $kegiatan->kode_kegiatan }}" {{ old('kegiatan') == $kegiatan->kode_kegiatan ? 'selected' : '' }}>
                                    {{ $kegiatan->nama_kegiatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('kegiatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Kontainer -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-box text-green-600 mr-2"></i>
                    Informasi Kontainer
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- No. Kontainer -->
                    <div>
                        <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                            No. Kontainer
                        </label>
                        <textarea name="no_kontainer"
                                  id="no_kontainer"
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('no_kontainer') border-red-500 @enderror"
                                  placeholder="Pisahkan dengan koma jika lebih dari 1">{{ old('no_kontainer') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Contoh: AYPU0033890, AYPU0033891</p>
                        @error('no_kontainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No. Seal -->
                    <div>
                        <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-2">
                            No. Seal
                        </label>
                        <input type="text"
                               name="no_seal"
                               id="no_seal"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('no_seal') border-red-500 @enderror"
                               value="{{ old('no_seal') }}"
                               placeholder="Nomor seal kontainer">
                        @error('no_seal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-2">
                            Size
                        </label>
                        <select name="size"
                                id="size"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size') border-red-500 @enderror">
                            <option value="">-- Pilih Size --</option>
                            <option value="20" {{ old('size') == '20' ? 'selected' : '' }}>20 Feet</option>
                            <option value="40" {{ old('size') == '40' ? 'selected' : '' }}>40 Feet</option>
                            <option value="45" {{ old('size') == '45' ? 'selected' : '' }}>45 Feet</option>
                        </select>
                        @error('size')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Kontainer -->
                    <div>
                        <label for="jumlah_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Kontainer
                        </label>
                        <input type="number"
                               name="jumlah_kontainer"
                               id="jumlah_kontainer"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jumlah_kontainer') border-red-500 @enderror"
                               value="{{ old('jumlah_kontainer', 1) }}"
                               min="1">
                        @error('jumlah_kontainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Pengiriman -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-shipping-fast text-purple-600 mr-2"></i>
                    Informasi Pengiriman
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tujuan Pengiriman -->
                    <div>
                        <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-2">
                            Tujuan Pengiriman
                        </label>
                        <input type="text"
                               name="tujuan_pengiriman"
                               id="tujuan_pengiriman"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tujuan_pengiriman') border-red-500 @enderror"
                               value="{{ old('tujuan_pengiriman') }}"
                               placeholder="Tujuan pengiriman kontainer">
                        @error('tujuan_pengiriman')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pengirim -->
                    <div>
                        <label for="pengirim" class="block text-sm font-medium text-gray-700 mb-2">
                            Pengirim
                        </label>
                        <input type="text"
                               name="pengirim"
                               id="pengirim"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pengirim') border-red-500 @enderror"
                               value="{{ old('pengirim') }}"
                               placeholder="Nama pengirim">
                        @error('pengirim')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Kapal & Jadwal -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-ship text-indigo-600 mr-2"></i>
                    Informasi Kapal & Jadwal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kapal -->
                    <div>
                        <label for="estimasi_nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                            Estimasi Nama Kapal
                        </label>
                        <select name="estimasi_nama_kapal"
                                id="estimasi_nama_kapal"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-kapal @error('estimasi_nama_kapal') border-red-500 @enderror">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($masterKapals as $kapal)
                                <option value="{{ $kapal->nama_kapal }}" {{ old('estimasi_nama_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}
                                    @if($kapal->nickname) ({{ $kapal->nickname }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('estimasi_nama_kapal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-search mr-1"></i>Ketik untuk mencari nama kapal
                        </p>
                    </div>

                    <!-- Tanggal Ambil Kontainer -->
                    <div>
                        <label for="tanggal_ambil_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Ambil Kontainer
                        </label>
                        <input type="date"
                               name="tanggal_ambil_kontainer"
                               id="tanggal_ambil_kontainer"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_ambil_kontainer') border-red-500 @enderror"
                               value="{{ old('tanggal_ambil_kontainer') }}">
                        @error('tanggal_ambil_kontainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Terima Pelabuhan -->
                    <div>
                        <label for="tanggal_terima_pelabuhan" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Terima Pelabuhan
                        </label>
                        <input type="date"
                               name="tanggal_terima_pelabuhan"
                               id="tanggal_terima_pelabuhan"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_terima_pelabuhan') border-red-500 @enderror"
                               value="{{ old('tanggal_terima_pelabuhan') }}">
                        @error('tanggal_terima_pelabuhan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Garasi -->
                    <div>
                        <label for="tanggal_garasi" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Garasi
                        </label>
                        <input type="date"
                               name="tanggal_garasi"
                               id="tanggal_garasi"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_garasi') border-red-500 @enderror"
                               value="{{ old('tanggal_garasi') }}">
                        @error('tanggal_garasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Muatan -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-weight text-orange-600 mr-2"></i>
                    Informasi Muatan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jumlah -->
                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah
                        </label>
                        <input type="number"
                               name="jumlah"
                               id="jumlah"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jumlah') border-red-500 @enderror"
                               value="{{ old('jumlah') }}"
                               min="0"
                               step="0.01">
                        @error('jumlah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Satuan -->
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                            Satuan
                        </label>
                        <input type="text"
                               name="satuan"
                               id="satuan"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('satuan') border-red-500 @enderror"
                               value="{{ old('satuan') }}"
                               placeholder="Contoh: Kg, Ton, Unit">
                        @error('satuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Berat Kotor -->
                    <div>
                        <label for="berat_kotor" class="block text-sm font-medium text-gray-700 mb-2">
                            Berat Kotor (Kg)
                        </label>
                        <input type="number"
                               name="berat_kotor"
                               id="berat_kotor"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('berat_kotor') border-red-500 @enderror"
                               value="{{ old('berat_kotor') }}"
                               min="0"
                               step="0.01">
                        @error('berat_kotor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dimensi -->
                    <div>
                        <label for="dimensi" class="block text-sm font-medium text-gray-700 mb-2">
                            Dimensi
                        </label>
                        <input type="text"
                               name="dimensi"
                               id="dimensi"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dimensi') border-red-500 @enderror"
                               value="{{ old('dimensi') }}"
                               placeholder="Contoh: 20x10x5 cm">
                        @error('dimensi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Gambar Checkpoint -->
            <div class="mb-8">
                <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-camera text-red-600 mr-2"></i>
                    Gambar Checkpoint
                </h3>
                <div>
                    <label for="gambar_checkpoint" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Gambar
                    </label>
                    <input type="file"
                           name="gambar_checkpoint"
                           id="gambar_checkpoint"
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gambar_checkpoint') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, GIF (Max: 2MB)</p>
                    @error('gambar_checkpoint')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('tanda-terima.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 shadow-sm">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Tanda Terima
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-calculate jumlah_kontainer from no_kontainer
    document.getElementById('no_kontainer').addEventListener('input', function() {
        const value = this.value.trim();
        if (value) {
            const containers = value.split(',').filter(item => item.trim() !== '');
            document.getElementById('jumlah_kontainer').value = containers.length;
        } else {
            document.getElementById('jumlah_kontainer').value = 1;
        }
    });

    // Initialize Select2 for kapal dropdown
    $('.select2-kapal').select2({
        placeholder: '-- Pilih Kapal --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Kapal tidak ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });
</script>
@endpush

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 styling to match Tailwind */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
        color: #111827;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }

    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }

    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #dbeafe;
        color: #1e40af;
    }
</style>
@endpush

@endsection
