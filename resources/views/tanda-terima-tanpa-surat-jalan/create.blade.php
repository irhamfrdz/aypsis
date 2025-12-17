@extends('layouts.app')

@section('title', 'Buat Tanda Terima Tanpa Surat Jalan')
@section('page_title', 'Buat Tanda Terima Tanpa Surat Jalan')

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
        outline: none;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        max-height: 300px !important;
    }

    .select2-results__options {
        max-height: 250px !important;
        overflow-y: auto;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .select2-container--default .select2-results__option {
        padding: 8px 12px;
        font-size: 14px;
    }

    .select2-results__message {
        padding: 8px 12px;
        font-size: 14px;
        color: #6b7280;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Buat Tanda Terima Tanpa Surat Jalan</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-600">Isi form di bawah untuk membuat tanda terima baru</p>
                    @if(isset($tipe))
                        @php
                            $tipeLabel = strtoupper($tipe);
                            $tipeColor = $tipe === 'fcl' ? 'blue' : ($tipe === 'lcl' ? 'green' : 'orange');
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $tipeColor }}-100 text-{{ $tipeColor }}-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Tipe: {{ $tipeLabel }}
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-4">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="font-medium text-sm mb-2">Terdapat kesalahan pada input:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tanda-terima-tanpa-surat-jalan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                {{-- Hidden input untuk tipe kontainer --}}
                @if(isset($tipe))
                    <input type="hidden" name="tipe_kontainer_selected" value="{{ $tipe }}">
                @endif

                <!-- Informasi Dasar -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Tanda Terima
                            </label>
                            <input type="text" name="nomor_tanda_terima" id="nomor_tanda_terima"
                                   value="{{ old('nomor_tanda_terima') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm @error('nomor_tanda_terima') border-red-500 @enderror"
                                   placeholder="Masukkan nomor tanda terima (opsional)">
                            @error('nomor_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Kosongkan jika ingin diisi nanti
                            </p>
                        </div>
                        <div>
                            <label for="tanggal_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_tanda_terima" id="tanggal_tanda_terima"
                                   value="{{ old('tanggal_tanda_terima', date('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_tanda_terima') border-red-500 @enderror">
                            @error('tanggal_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nomor_surat_jalan_customer" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Surat Jalan Customer
                            </label>
                            <input type="text" name="nomor_surat_jalan_customer" id="nomor_surat_jalan_customer"
                                   value="{{ old('nomor_surat_jalan_customer') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_surat_jalan_customer') border-red-500 @enderror"
                                   placeholder="SJ-CUSTOMER-001">
                            @error('nomor_surat_jalan_customer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="estimasi_naik_kapal" class="block text-sm font-medium text-gray-700 mb-1">
                                Estimasi Naik Kapal
                            </label>
                            <select name="estimasi_naik_kapal" id="estimasi_naik_kapal"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('estimasi_naik_kapal') border-red-500 @enderror">
                                <option value="">-- Pilih Kapal --</option>
                                @foreach($master_kapals as $kapal)
                                    <option value="{{ $kapal->nama_kapal }}" {{ old('estimasi_naik_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estimasi_naik_kapal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Term Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Term
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="term_id" id="term_id" class="hidden @error('term_id') border-red-500 @enderror">
                                    <option value="">Pilih Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                            {{ $term->nama_status }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Search input -->
                                <input type="text" id="termSearch"
                                       placeholder="Cari atau pilih term..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('term_id') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="termDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('term.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Term Baru
                                        </a>
                                    </div>
                                    @foreach($terms as $term)
                                        <div class="term-option px-3 py-2 hover:bg-gray-50 cursor-pointer"
                                             data-value="{{ $term->id }}"
                                             data-text="{{ $term->nama_status }}">
                                            {{ $term->nama_status }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('term_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Penerima dan Pengirim -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Penerima dan Pengirim</h3>
                    <!-- Baris 1: Nama Penerima dan Pengirim -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="penerima" class="block text-sm font-medium text-gray-700">
                                    Penerima <span class="text-red-500">*</span>
                                </label>
                                <button type="button" 
                                        onclick="openPenerimaPopup()"
                                        class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                    <i class="fas fa-plus-circle"></i>
                                    Tambah Penerima Baru
                                </button>
                            </div>
                            <select name="penerima" id="penerima" required
                                    class="select2-penerima w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('penerima') border-red-500 @enderror">
                                <option value="">-- Pilih Penerima --</option>
                                @foreach($masterPengirimPenerima as $item)
                                    <option value="{{ $item->nama }}" 
                                            data-alamat="{{ $item->alamat }}"
                                            {{ old('penerima') == $item->nama ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="pengirim" class="block text-sm font-medium text-gray-700">
                                    Pengirim <span class="text-red-500">*</span>
                                </label>
                                <button type="button" 
                                        onclick="openPengirimPopup()"
                                        class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                    <i class="fas fa-plus-circle"></i>
                                    Tambah Pengirim Baru
                                </button>
                            </div>
                            <select name="pengirim" id="pengirim" required
                                    class="select2-pengirim w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('pengirim') border-red-500 @enderror">
                                <option value="">-- Pilih Pengirim --</option>
                                @foreach($masterPengirimPenerima as $item)
                                    <option value="{{ $item->nama }}"
                                            data-alamat="{{ $item->alamat }}"
                                            {{ old('pengirim') == $item->nama ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 2: PIC dan Telepon -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="pic" class="block text-sm font-medium text-gray-700 mb-1">
                                PIC (Person In Charge)
                            </label>
                            <input type="text" name="pic" id="pic" value="{{ old('pic') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('pic') border-red-500 @enderror"
                                   placeholder="Nama PIC">
                            @error('pic')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">
                                Telepon
                            </label>
                            <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('telepon') border-red-500 @enderror"
                                   placeholder="Nomor telepon">
                            @error('telepon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 3: Alamat -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Penerima
                            </label>
                            <textarea name="alamat_penerima" id="alamat_penerima" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat_penerima') border-red-500 @enderror"
                                      placeholder="Alamat lengkap penerima">{{ old('alamat_penerima') }}</textarea>
                            @error('alamat_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Pengirim
                            </label>
                            <textarea name="alamat_pengirim" id="alamat_pengirim" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat_pengirim') border-red-500 @enderror"
                                      placeholder="Alamat lengkap pengirim">{{ old('alamat_pengirim') }}</textarea>
                            @error('alamat_pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Dimensi dan Volume -->
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Dimensi dan Volume
                        </h3>
                        <button type="button"
                                id="add-dimensi-btn-new"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Dimensi
                        </h3>
                    </div>

                    <div id="dimensi-container-new">
                        <div class="dimensi-row-new mb-4 pb-4 border-b border-purple-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label for="nama_barang_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Nama Barang <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="nama_barang[]" 
                                           id="nama_barang_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="Nama barang"
                                           value="{{ old('nama_barang.0') }}"
                                           required>
                                </div>
                                <div>
                                    <label for="jumlah_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jumlah <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="jumlah[]"
                                           id="jumlah_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0"
                                           value="{{ old('jumlah.0', 1) }}"
                                           min="1"
                                           step="1"
                                           required>
                                </div>
                                <div>
                                    <label for="satuan_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="satuan[]"
                                           id="satuan_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="Pcs, Kg, Box"
                                           value="{{ old('satuan.0', 'unit') }}"
                                           required>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                <div>
                                    <label for="panjang_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Panjang (m)
                                    </label>
                                    <input type="number"
                                           name="panjang[]"
                                           id="panjang_0"
                                           class="dimensi-input-new w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0.000"
                                           value="{{ old('panjang.0') }}"
                                           min="0"
                                           step="0.001"
                                           oninput="calculateVolumeNew(this.closest('.dimensi-row-new'))">
                                </div>
                                <div>
                                    <label for="lebar_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Lebar (m)
                                    </label>
                                    <input type="number"
                                           name="lebar[]"
                                           id="lebar_0"
                                           class="dimensi-input-new w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0.000"
                                           value="{{ old('lebar.0') }}"
                                           min="0"
                                           step="0.001"
                                           oninput="calculateVolumeNew(this.closest('.dimensi-row-new'))">
                                </div>
                                <div>
                                    <label for="tinggi_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tinggi (m)
                                    </label>
                                    <input type="number"
                                           name="tinggi[]"
                                           id="tinggi_0"
                                           class="dimensi-input-new w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0.000"
                                           value="{{ old('tinggi.0') }}"
                                           min="0"
                                           step="0.001"
                                           oninput="calculateVolumeNew(this.closest('.dimensi-row-new'))">
                                </div>
                                <div>
                                    <label for="meter_kubik_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Volume (m³)
                                    </label>
                                    <input type="number"
                                           name="meter_kubik[]"
                                           id="meter_kubik_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm"
                                           placeholder="0.000"
                                           value="{{ old('meter_kubik.0') }}"
                                           min="0"
                                           step="0.001"
                                           readonly>
                                </div>
                                <div>
                                    <label for="tonase_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tonase (Ton)
                                    </label>
                                    <input type="number"
                                           name="tonase[]"
                                           id="tonase_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0.000"
                                           value="{{ old('tonase.0') }}"
                                           min="0"
                                           step="0.001">
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Volume akan dihitung otomatis dari panjang × lebar × tinggi × jumlah
                            </p>
                        </div>
                    </div>

                    <!-- Hidden fields untuk backward compatibility -->
                    <input type="hidden" name="jenis_barang" id="jenis_barang" value="">
                    <input type="hidden" name="aktifitas" id="aktifitas" value="">
                    <input type="hidden" name="jumlah_barang" id="jumlah_barang" value="1">
                    <input type="hidden" name="satuan_barang" id="satuan_barang" value="unit">
                    <input type="hidden" name="berat" id="berat" value="">
                    <input type="hidden" name="satuan_berat" id="satuan_berat" value="kg">
                    <input type="hidden" name="keterangan_barang" id="keterangan_barang" value="">
                    <!-- REMOVED: Backward-compatible hidden scalar fields for panjang/lebar/tinggi/meter_kubik/tonase -->
                    <!-- These conflict with array inputs panjang[], lebar[], tinggi[], meter_kubik[], tonase[] -->
                    <!-- The controller now only expects arrays, not scalar values -->
                </div>

                <!-- Informasi Supir dan Kenek -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Supir dan Kenek</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">
                                Supir <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <!-- Hidden input for actual value -->
                                <input type="hidden" name="supir" id="supir" value="{{ old('supir', 'Supir Customer') }}">

                                <!-- Search input -->
                                <input type="text" id="supirSearch"
                                       placeholder="Cari supir atau ketik manual..."
                                       value="{{ old('supir', 'Supir Customer') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="supirDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <div class="text-xs text-gray-500 mb-2">Pilih dari Master Karyawan:</div>
                                    </div>
                                    @foreach($supirs as $supir_data)
                                        @php
                                            $nama_display = $supir_data->nama_lengkap ?: $supir_data->nama_panggilan ?: 'Nama tidak tersedia';
                                        @endphp
                                        <div class="supir-option px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                                             data-value="{{ $nama_display }}"
                                             data-text="{{ $nama_display }}"
                                             data-plat="{{ $supir_data->plat ?? '' }}">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-sm">{{ $nama_display }}</span>
                                                @if($supir_data->plat)
                                                    <span class="text-xs text-gray-500">Plat: {{ $supir_data->plat }}</span>
                                                @else
                                                    <span class="text-xs text-gray-500">ID: {{ $supir_data->id }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="p-2 border-t border-gray-200">
                                        <div class="text-xs text-gray-500">Atau ketik manual untuk supir customer</div>
                                    </div>
                                </div>
                            </div>
                            @error('supir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kenek" class="block text-sm font-medium text-gray-700 mb-1">
                                Kenek
                            </label>
                            <div class="relative">
                                <!-- Hidden input for actual value -->
                                <input type="hidden" name="kenek" id="kenek" value="{{ old('kenek', 'Kenek Customer') }}">

                                <!-- Search input -->
                                <input type="text" id="kenekSearch"
                                       placeholder="Cari kenek atau ketik manual..."
                                       value="{{ old('kenek', 'Kenek Customer') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('kenek') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="kenekDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <div class="text-xs text-gray-500 mb-2">Pilih dari Master Karyawan:</div>
                                    </div>
                                    @foreach($kranis as $kenek_data)
                                        @php
                                            $nama_display = $kenek_data->nama_lengkap ?: $kenek_data->nama_panggilan ?: 'Nama tidak tersedia';
                                        @endphp
                                        <div class="kenek-option px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                                             data-value="{{ $nama_display }}"
                                             data-text="{{ $nama_display }}">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-sm">{{ $nama_display }}</span>
                                                <span class="text-xs text-gray-500">Divisi: {{ $kenek_data->divisi }} | ID: {{ $kenek_data->id }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="p-2 border-t border-gray-200">
                                        <div class="text-xs text-gray-500">Atau ketik manual untuk kenek customer</div>
                                    </div>
                                </div>
                            </div>
                            @error('kenek')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Plat Kendaraan
                            </label>
                            <input type="text" name="no_plat" id="no_plat" value="{{ old('no_plat') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror"
                                   placeholder="Nomor plat kendaraan">
                            @error('no_plat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Otomatis terisi jika memilih supir dari master karyawan
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tujuan dan Transportasi -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tujuan dan Transportasi</h3>
                    
                    <!-- Baris 1: Tujuan Pengiriman -->
                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <div>
                            <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Pengiriman <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="tujuan_pengiriman" id="tujuan_pengiriman" class="hidden @error('tujuan_pengiriman') border-red-500 @enderror">
                                    <option value="">Pilih Tujuan Pengiriman</option>
                                    @foreach($tujuan_kirims as $tujuan)
                                        <option value="{{ $tujuan->nama_tujuan }}" {{ old('tujuan_pengiriman') == $tujuan->nama_tujuan ? 'selected' : '' }}>
                                            {{ $tujuan->nama_tujuan }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Search input -->
                                <input type="text" id="tujuanPengirimanSearch"
                                       placeholder="Cari atau pilih tujuan pengiriman..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengiriman') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="tujuanPengirimanDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('tujuan-kirim.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Tujuan Baru
                                        </a>
                                    </div>
                                    @if(isset($tujuan_kirims) && $tujuan_kirims->count() > 0)
                                        @foreach($tujuan_kirims as $tujuan)
                                            <div class="tujuan-pengiriman-option px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100"
                                                 data-value="{{ $tujuan->nama_tujuan }}"
                                                 data-text="{{ $tujuan->nama_tujuan }}">
                                                <div class="flex flex-col">
                                                    <span class="font-medium">{{ $tujuan->nama_tujuan }}</span>
                                                    @if(isset($tujuan->catatan) && $tujuan->catatan)
                                                        <span class="text-xs text-gray-500">{{ $tujuan->catatan }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="px-3 py-2 text-sm text-gray-500">
                                            Tidak ada data tujuan pengiriman
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('tujuan_pengiriman')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 2: Informasi Kontainer -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe Kontainer
                            </label>
                            <select name="tipe_kontainer" id="tipe_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 cursor-not-allowed @error('tipe_kontainer') border-red-500 @enderror"
                                    onchange="handleTipeKontainerChange()" readonly disabled>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="fcl" {{ (old('tipe_kontainer', $tipe ?? '') == 'fcl') ? 'selected' : '' }}>FCL</option>
                                <option value="lcl" {{ (old('tipe_kontainer', $tipe ?? '') == 'lcl') ? 'selected' : '' }}>LCL</option>
                                <option value="cargo" {{ (old('tipe_kontainer', $tipe ?? '') == 'cargo') ? 'selected' : '' }}>Cargo</option>
                            </select>
                            @if(isset($tipe))
                                <input type="hidden" name="tipe_kontainer" value="{{ $tipe }}">
                            @endif
                            @error('tipe_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="size_kontainer_field">
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Size Kontainer
                            </label>
                            <select name="size_kontainer" id="size_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('size_kontainer') border-red-500 @enderror">
                                <option value="">-- Pilih Size --</option>
                                <option value="20 ft" {{ old('size_kontainer') == '20 ft' ? 'selected' : '' }}>20 ft</option>
                                <option value="40 ft" {{ old('size_kontainer') == '40 ft' ? 'selected' : '' }}>40 ft</option>
                                <option value="40 HC" {{ old('size_kontainer') == '40 HC' ? 'selected' : '' }}>40 HC (High Cube)</option>
                                <option value="45 ft" {{ old('size_kontainer') == '45 ft' ? 'selected' : '' }}>45 ft</option>
                                <option value="53 ft" {{ old('size_kontainer') == '53 ft' ? 'selected' : '' }}>53 ft</option>
                                <option value="other" {{ old('size_kontainer') == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="no_kontainer_field">
                            <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Kontainer <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="noKontainerSearch" placeholder="Cari nomor kontainer..." autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_kontainer') border-red-500 @enderror">
                                <div id="noKontainerDropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto hidden">
                                    @if(isset($containerOptions) && count($containerOptions))
                                        @foreach($containerOptions as $opt)
                                            <div class="no-kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer" data-value="{{ $opt['value'] }}" data-text="{{ $opt['label'] }}@if(!empty($opt['size'])) - {{ $opt['size'] }}@endif" data-size="{{ $opt['size'] }}" data-source="{{ $opt['source'] }}">
                                                {{ $opt['label'] }}@if(!empty($opt['size'])) - {{ $opt['size'] }}@endif
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="no-kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer text-blue-600" data-value="__manual__" data-text="&raquo; Ketik manual / Lainnya">
                                        &raquo; Ketik manual / Lainnya
                                    </div>
                                </div>
                                <input type="hidden" name="no_kontainer" id="no_kontainer" value="{{ old('no_kontainer') }}" required>
                            </div>
                            <input type="text" name="no_kontainer_manual" id="no_kontainer_manual" value="{{ old('no_kontainer_manual') }}" placeholder="Masukkan nomor kontainer jika memilih Lainnya" class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md text-sm hidden" />
                            @error('no_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="no_seal_field">
                            <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Seal <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_seal" id="no_seal" value="{{ old('no_seal') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_seal') border-red-500 @enderror"
                                   placeholder="Nomor seal">
                            @error('no_seal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 3: Tanggal Seal -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div id="tanggal_seal_field">
                            <label for="tanggal_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Seal
                            </label>
                            <input type="date" name="tanggal_seal" id="tanggal_seal" value="{{ old('tanggal_seal') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_seal') border-red-500 @enderror">
                            @error('tanggal_seal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan</h3>
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan Tambahan
                        </label>
                        <textarea name="catatan" id="catatan" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Catatan atau informasi tambahan (opsional)">{{ old('catatan') }}</textarea>
                    </div>

                    <!-- Hidden input untuk auto-save ke prospek -->
                    <input type="hidden" name="simpan_ke_prospek" value="1">
                    
                    <!-- Info notification -->
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-blue-800">
                                <strong>Info:</strong> Data ini akan otomatis tersimpan juga ke tabel prospek untuk keperluan follow-up bisnis.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Upload Gambar -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Gambar</h3>
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors upload-dropzone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="gambar_tanda_terima" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload gambar</span>
                                    <input id="gambar_tanda_terima" 
                                           name="gambar_tanda_terima[]" 
                                           type="file" 
                                           class="sr-only" 
                                           multiple
                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                           onchange="previewImages(this)">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, JPEG, GIF, WEBP sampai 10MB per file (max 5 file)
                            </p>
                        </div>
                    </div>
                    @error('gambar_tanda_terima.*')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <!-- Preview Area for Images -->
                    <div id="image-preview-container" class="mt-4 hidden">
                        <label class="block text-xs font-medium text-gray-500 mb-2">
                            <i class="fas fa-eye mr-1 text-green-600"></i>
                            Preview Gambar yang Akan Diupload
                        </label>
                        <div id="image-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            <!-- Preview images will be inserted here by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Tanda Terima
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Select2 JS - jQuery already loaded in layout -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Wrap everything in jQuery ready to ensure DOM and libraries are loaded
    jQuery(document).ready(function($) {
        console.log('jQuery version:', $.fn.jquery);
        console.log('Select2 available:', typeof $.fn.select2);
        
        // Initialize Select2 for penerima dropdown
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-penerima').select2({
                placeholder: '-- Pilih Penerima --',
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: false,
                language: {
                    noResults: function() {
                        return "Tidak ada hasil ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Initialize Select2 for pengirim dropdown
            $('.select2-pengirim').select2({
                placeholder: '-- Pilih Pengirim --',
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: false,
                language: {
                    noResults: function() {
                        return "Tidak ada hasil ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Auto-fill alamat penerima when penerima is selected
            $('#penerima').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                
                if (alamat) {
                    $('#alamat_penerima').val(alamat);
                }
            });

            // Clear alamat when penerima is cleared
            $('#penerima').on('select2:clear', function(e) {
                $('#alamat_penerima').val('');
            });

            // Auto-fill alamat pengirim when pengirim is selected
            $('#pengirim').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                
                if (alamat) {
                    $('#alamat_pengirim').val(alamat);
                }
            });

            // Clear alamat when pengirim is cleared
            $('#pengirim').on('select2:clear', function(e) {
                $('#alamat_pengirim').val('');
            });
        } else {
            console.error('Select2 is not loaded!');
        }

        // Calculate initial volumes and totals
        calculateAllVolumesAndTotals();

        // Initialize term dropdown
        initializeTermDropdown();

        // Initialize tujuan pengiriman dropdown
        initializeTujuanPengirimanDropdown();

        // Initialize supir dropdown
        initializeSupirDropdown();

        // Initialize kenek dropdown
        initializeKenekDropdown();

        // Add new dimensi item (only if legacy table exists)
        const addDimensiBtnLegacy = document.getElementById('addDimensiItem');
        if (addDimensiBtnLegacy) {
            addDimensiBtnLegacy.addEventListener('click', function() {
                addNewDimensiItem();
            });
        }

        // Remove dimensi item
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-dimensi-item')) {
                e.target.closest('.dimensi-item').remove();
                updateItemNumbers();
                calculateAllVolumesAndTotals();
                updateRemoveButtons();
            }
        });
    }); // End of jQuery ready

    let dimensiItemIndex = 1;

    function addNewDimensiItem() {
        const newRow = document.createElement('tr');
        newRow.className = 'dimensi-item hover:bg-gray-50';
        newRow.setAttribute('data-index', dimensiItemIndex);
        newRow.innerHTML = `
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="item-number text-sm font-medium text-gray-900">${dimensiItemIndex + 1}</span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][panjang]"
                       class="dimensi-panjang w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="0.000"
                       min="0"
                       step="0.001"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][lebar]"
                       class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="0.000"
                       min="0"
                       step="0.001"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][tinggi]"
                       class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="0.000"
                       min="0"
                       step="0.001"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][meter_kubik]"
                       class="item-meter-kubik w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm focus:outline-none"
                       placeholder="0.000"
                       readonly
                       step="0.001">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][tonase]"
                       class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="0.000"
                       min="0"
                       step="0.001"
                       onchange="calculateTotals()">
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-center">
                <button type="button" class="remove-dimensi-item text-red-600 hover:text-red-800 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;

        const dimensiTableBodyEl = document.getElementById('dimensiTableBody');
        if (dimensiTableBodyEl) {
            dimensiTableBodyEl.appendChild(newRow);
        }
        dimensiItemIndex++;
        updateItemNumbers();
        updateRemoveButtons();
    }

    function updateItemNumbers() {
        const rows = document.querySelectorAll('#dimensiTableBody .dimensi-item');
        rows.forEach((row, index) => {
            row.querySelector('.item-number').textContent = index + 1;
            row.setAttribute('data-index', index);
        });
    }

    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-dimensi-item');
        if (removeButtons.length === 1) {
            removeButtons[0].style.display = 'none';
        } else {
            removeButtons.forEach(btn => btn.style.display = 'block');
        }
    }

    function calculateItemVolume(element) {
        const row = element.closest('.dimensi-item');
        if (!row) return;
        
        const panjangEl = row.querySelector('.dimensi-panjang');
        const lebarEl = row.querySelector('.dimensi-lebar');
        const tinggiEl = row.querySelector('.dimensi-tinggi');
        const jumlahEl = row.querySelector('[name^="jumlah"], input[name*="jumlah"]');
        
        const panjang = panjangEl ? parseFloat(panjangEl.value) || 0 : 0;
        const lebar = lebarEl ? parseFloat(lebarEl.value) || 0 : 0;
        const tinggi = tinggiEl ? parseFloat(tinggiEl.value) || 0 : 0;
        const jumlah = jumlahEl ? (parseInt(jumlahEl.value, 10) || 1) : 1;

        let volume = 0;
        if (panjang > 0 && lebar > 0 && tinggi > 0) {
            // Kalkulasi volume termasuk jumlah: panjang × tinggi × lebar × jumlah
            volume = panjang * tinggi * lebar * jumlah;
        }

        const volumeInput = row.querySelector('.item-meter-kubik');
        if (volumeInput) {
            if (volume > 0) {
                volumeInput.value = volume.toFixed(3);
            } else {
                volumeInput.value = '';
            }
        }
        calculateTotals();
    }

    function calculateAllVolumesAndTotals() {
        // Legacy dimensi table
        const legacyRows = document.querySelectorAll('#dimensiTableBody .dimensi-item');
        if (legacyRows.length > 0) {
            legacyRows.forEach(row => {
                const panjangEl = row.querySelector('.dimensi-panjang');
                const lebarEl = row.querySelector('.dimensi-lebar');
                const tinggiEl = row.querySelector('.dimensi-tinggi');
                
                const panjang = panjangEl ? parseFloat(panjangEl.value) || 0 : 0;
                const lebar = lebarEl ? parseFloat(lebarEl.value) || 0 : 0;
                const tinggi = tinggiEl ? parseFloat(tinggiEl.value) || 0 : 0;

                let volume = 0;
                // Try to read jumlah if available; default to 1
                const jumlahEl = row.querySelector('[name^="jumlah"], input[name*="jumlah"]');
                const jumlah = jumlahEl ? (parseInt(jumlahEl.value, 10) || 1) : 1;

                if (panjang > 0 && lebar > 0 && tinggi > 0) {
                    volume = panjang * tinggi * lebar * jumlah;
                }

                const volumeInput = row.querySelector('.item-meter-kubik');
                if (volumeInput) {
                    if (volume > 0) {
                        volumeInput.value = volume.toFixed(3);
                    } else {
                        volumeInput.value = '';
                    }
                }
            });
        }

        // New LCL dimensi rows
        const newRows = document.querySelectorAll('#dimensi-container-new .dimensi-row-new');
        if (newRows.length > 0) {
            newRows.forEach(row => {
                calculateVolumeNew(row);
            });
        }

        calculateTotals();
        updateRemoveButtons();
    }

    function calculateTotals() {
        let totalVolume = 0;
        let totalTonase = 0;

        // Sum from legacy rows
        const legacyRows = document.querySelectorAll('#dimensiTableBody .dimensi-item');
        if (legacyRows.length > 0) {
            legacyRows.forEach(row => {
                const volumeEl = row.querySelector('.item-meter-kubik');
                const tonaseEl = row.querySelector('.dimensi-tonase');
                
                const volume = volumeEl ? parseFloat(volumeEl.value) || 0 : 0;
                const tonase = tonaseEl ? parseFloat(tonaseEl.value) || 0 : 0;

                totalVolume += volume;
                totalTonase += tonase;
            });
        }

        // Sum from new LCL rows
        const newRows = document.querySelectorAll('#dimensi-container-new .dimensi-row-new');
        if (newRows.length > 0) {
            newRows.forEach(row => {
                const volumeEl = row.querySelector('input[name="meter_kubik[]"]');
                const tonaseEl = row.querySelector('input[name="tonase[]"]');
                
                const volume = volumeEl ? parseFloat(volumeEl.value) || 0 : 0;
                const tonase = tonaseEl ? parseFloat(tonaseEl.value) || 0 : 0;

                totalVolume += volume;
                totalTonase += tonase;
            });
        }

        // Update summary display (only if elements exist)
        const totalVolumeElement = document.getElementById('totalVolume');
        const totalTonaseElement = document.getElementById('totalTonase');
        
        if (totalVolumeElement) {
            totalVolumeElement.textContent = formatVolumeDisplay(totalVolume) + ' m³';
        }
        if (totalTonaseElement) {
            totalTonaseElement.textContent = formatWeightDisplay(totalTonase) + ' Ton';
        }

        // REMOVED: Hidden scalar field updates (hiddenPanjang, hiddenLebar, etc.)
        // These fields no longer exist - form only sends arrays now
    }

    // Formatting functions for input fields (clean whole numbers, smart decimal display)
    function formatVolume(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Show as "1000" not "1000.000"
        }
        
        return rounded.toFixed(3); // Show decimals when needed
    }

    function formatWeight(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Show as "5" not "5.000"
        }
        
        return rounded.toFixed(3); // Show decimals when needed
    }

    // Formatting functions for display totals (with thousand separator)
    function formatVolumeDisplay(value) {
        if (!value || value === 0) return '0';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toLocaleString('id-ID'); // Show as "1,000" not "1,000.000"
        }
        
        return rounded.toLocaleString('id-ID', {
            minimumFractionDigits: 3,
            maximumFractionDigits: 3
        }); // Show decimals with thousand separator
    }

    function formatWeightDisplay(value) {
        if (!value || value === 0) return '0';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toLocaleString('id-ID'); // Show as "5" not "5.000"
        }
        
        return rounded.toLocaleString('id-ID', {
            minimumFractionDigits: 3,
            maximumFractionDigits: 3
        }); // Show decimals with thousand separator
    }

    // Formatting functions for database (clean values, no excessive decimals)
    function formatVolumeForDatabase(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Send as "1000" not "1000.000"
        }
        
        // Remove trailing zeros from decimals
        return parseFloat(rounded.toFixed(3)).toString();
    }

    function formatWeightForDatabase(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Send as "5" not "5.000"
        }
        
        // Remove trailing zeros from decimals
        return parseFloat(rounded.toFixed(3)).toString();
    }

    // Legacy function for backward compatibility
    function calculateMeterKubik() {
        calculateAllVolumesAndTotals();
    }

    function handleTipeKontainerChange() {
        const tipeKontainerEl = document.getElementById('tipe_kontainer');
        if (!tipeKontainerEl) return; // nothing to do if element doesn't exist
        const tipeKontainer = tipeKontainerEl.value;
        const sizeKontainerField = document.getElementById('size_kontainer_field');
        const noKontainerField = document.getElementById('no_kontainer_field');
        const noSealField = document.getElementById('no_seal_field');
        const tanggalSealField = document.getElementById('tanggal_seal_field');
        const noKontainerInput = document.getElementById('no_kontainer');
        const noSealInput = document.getElementById('no_seal');
        
        if (tipeKontainer === 'cargo') {
            // Hide kontainer fields for cargo
            sizeKontainerField.style.display = 'none';
            noKontainerField.style.display = 'none';
            noSealField.style.display = 'none';
            tanggalSealField.style.display = 'none';
            // Clear kontainer fields when cargo is selected and remove required attribute
            if (noKontainerInput) noKontainerInput.value = '';
            const sizeKontainerInput = document.getElementById('size_kontainer');
            if (sizeKontainerInput) sizeKontainerInput.value = '';
            if (noSealInput) noSealInput.value = '';
            const tanggalSealInput = document.getElementById('tanggal_seal');
            if (tanggalSealInput) tanggalSealInput.value = '';
            if (noKontainerInput) noKontainerInput.removeAttribute('required');
            if (noSealInput) noSealInput.removeAttribute('required');
        } else {
            // Show kontainer fields for FCL and LCL
            sizeKontainerField.style.display = 'block';
            noKontainerField.style.display = 'block';
            noSealField.style.display = 'block';
            tanggalSealField.style.display = 'block';
            // Add required attribute back for FCL and LCL
            if (noKontainerInput) noKontainerInput.setAttribute('required', 'required');
            if (noSealInput) noSealInput.setAttribute('required', 'required');
        }
    }

    // Initialize no kontainer dropdown
    initializeNoKontainerDropdown();

    function setSizeKontainerValue(size) {
        const sizeSelect = document.getElementById('size_kontainer');
        if (!sizeSelect) return;
        let matched = false;
        for (let i = 0; i < sizeSelect.options.length; i++) {
            const opt = sizeSelect.options[i];
            if (!size) {
                opt.selected = false; // reset
                continue;
            }
            if (opt.value === size || (opt.text && opt.text.toLowerCase().includes(String(size).toLowerCase())) || opt.value.replace(/\s|-/g, '').toLowerCase() === String(size).replace(/\s|-/g, '').toLowerCase()) {
                opt.selected = true;
                matched = true;
                break;
            }
        }
        if (!matched) {
            // fallback to direct assignment (useful when exact values match)
            sizeSelect.value = size;
        }
    }

    // Ensure manual value is submitted if manual option chosen
    const tandaTerimaForm = document.querySelector('form');
    if (tandaTerimaForm) {
        tandaTerimaForm.addEventListener('submit', function (e) {
            // Validate tujuan_pengiriman (custom validation for hidden required field)
            const tujuanPengirimanField = document.getElementById('tujuan_pengiriman');
            const tujuanPengirimanSearch = document.getElementById('tujuanPengirimanSearch');
            if (tujuanPengirimanField && (!tujuanPengirimanField.value || tujuanPengirimanField.value === '')) {
                e.preventDefault();
                alert('Tujuan Pengiriman wajib diisi!');
                if (tujuanPengirimanSearch) {
                    tujuanPengirimanSearch.focus();
                    tujuanPengirimanSearch.classList.add('border-red-500');
                }
                return false;
            }
            
            // Ensure latest volume/totals/calculations are up-to-date
            calculateAllVolumesAndTotals();
            // Update hidden legacy fields from new LCL inputs before submit
            updateHiddenBarangFields();
            // DON'T call serializeLclRowsToDimensiItems() - form already sends panjang[], lebar[], etc arrays correctly!
            // serializeLclRowsToDimensiItems(); // DISABLED - causes data corruption
            const hiddenInput = document.getElementById('no_kontainer');
            const manualField = document.getElementById('no_kontainer_manual');
            if (hiddenInput && hiddenInput.value === '__manual__') {
                if (!manualField || !manualField.value.trim()) {
                    e.preventDefault();
                    alert('Silakan isi nomor kontainer pada input manual.');
                    (manualField || document.getElementById('noKontainerSearch')).focus();
                    return false;
                }
                // Set hidden input to manual value
                hiddenInput.value = manualField.value.trim();
            }
        });
    }

    // Update hidden legacy fields based on LCL inputs
    function updateHiddenBarangFields() {
        try {
            const jenisEl = document.getElementById('jenis_barang');
            const jumlahEl = document.getElementById('jumlah_barang');
            const satuanEl = document.getElementById('satuan_barang');
            const keteranganEl = document.getElementById('keterangan_barang');
            const beratEl = document.getElementById('berat');
            const satuanBeratEl = document.getElementById('satuan_berat');

            const namaInputs = Array.from(document.querySelectorAll('input[name="nama_barang[]"]'));
            const jumlahInputs = Array.from(document.querySelectorAll('input[name="jumlah[]"]'));
            const satuanInputs = Array.from(document.querySelectorAll('input[name="satuan[]"]'));

            const namaVals = namaInputs.map(i => i.value.trim()).filter(v => v !== '');
            const jumlahVals = jumlahInputs.map(i => parseInt(i.value, 10) || 0).filter(v => v >= 0);
            const satuanVals = satuanInputs.map(i => i.value.trim()).filter(v => v !== '');

            if (jenisEl) jenisEl.value = namaVals.length ? namaVals.join(', ') : (jenisEl.value || '');
            if (jumlahEl) {
                // Sum jumlah[] to a single integer for legacy field
                const totalJumlah = jumlahVals.length ? jumlahVals.reduce((a, b) => a + b, 0) : parseInt(jumlahEl.value, 10) || 1;
                jumlahEl.value = totalJumlah;
            }
            if (satuanEl) satuanEl.value = satuanVals.length ? satuanVals.join(',') : (satuanEl.value || 'unit');

            // For description and weight fields we can leave as-is or attempt to copy from available inputs
            if (keteranganEl && !keteranganEl.value && namaVals.length) {
                // Put a short note listing first item as fallback
                keteranganEl.value = keteranganEl.value || '';
            }
            if (beratEl && !beratEl.value) {
                beratEl.value = beratEl.value || '';
            }
            if (satuanBeratEl && !satuanBeratEl.value) {
                satuanBeratEl.value = satuanBeratEl.value || 'kg';
            }
            // REMOVED: Hidden scalar dimension field updates
            // Form now only uses arrays: panjang[], lebar[], tinggi[], meter_kubik[], tonase[]
        } catch (err) {
            // silent
        }
    }

    // NOTE: This function is DISABLED and should NOT be used!
    // The form already sends panjang[], lebar[], tinggi[] arrays correctly.
    // Calling this function creates dimensi_items[][] which conflicts with the direct arrays.
    // Convert LCL input rows (panjang[], lebar[], tinggi[], meter_kubik[], tonase[]) into
    // equivalent legacy dimensi_items format so backend that expects dimensi_items[] receives the data.
    function serializeLclRowsToDimensiItems_DISABLED() {
        try {
            const formElement = document.querySelector('form');
            if (!formElement) return;

            // remove previous serialized inputs if any
            const oldGenerated = formElement.querySelectorAll('.generated-dimensi-item');
            oldGenerated.forEach(el => el.remove());

            const newRows = Array.from(document.querySelectorAll('#dimensi-container-new .dimensi-row-new'));
            newRows.forEach((row, idx) => {
                const panjangRaw = row.querySelector('input[name="panjang[]"]')?.value || '';
                const lebarRaw = row.querySelector('input[name="lebar[]"]')?.value || '';
                const tinggiRaw = row.querySelector('input[name="tinggi[]"]')?.value || '';
                const meterRaw = row.querySelector('input[name="meter_kubik[]"]')?.value || '';
                const tonaseRaw = row.querySelector('input[name="tonase[]"]')?.value || '';
                const nama = row.querySelector('input[name="nama_barang[]"]')?.value || '';
                const jumlahRaw = row.querySelector('input[name="jumlah[]"]')?.value || '';
                const satuanRaw = row.querySelector('input[name="satuan[]"]')?.value || '';

                // Clean numeric inputs: replace comma with dot, strip thousands separators, parse
                const cleanNumber = (val) => {
                    if (!val && val !== 0) return null;
                    if (typeof val !== 'string') val = String(val);
                    // Remove thousand sep like '1.234,56' -> '1234.56' if needed, but simpler: replace commas with dot
                    let cleaned = val.replace(/\s/g, '').replace(/,/g, '.');
                    // Remove non-numeric characters except dot and minus
                    cleaned = cleaned.replace(/[^0-9.\-]/g, '');
                    const parsed = parseFloat(cleaned);
                    return isNaN(parsed) ? null : parsed;
                };

                const panjang = cleanNumber(panjangRaw);
                const lebar = cleanNumber(lebarRaw);
                const tinggi = cleanNumber(tinggiRaw);
                const meter = cleanNumber(meterRaw);
                const tonase = cleanNumber(tonaseRaw);
                const jumlah = parseInt(String(jumlahRaw || '0').replace(/[^0-9-]/g, ''), 10) || 0;
                const satuan = satuanRaw || '';

                // Debug log for each row before creating fields
                console.log(`Row ${idx} - Raw values:`, {
                    panjangRaw, lebarRaw, tinggiRaw, meterRaw, tonaseRaw, nama
                });
                console.log(`Row ${idx} - Parsed values:`, {
                    panjang, lebar, tinggi, meter, tonase
                });

                // Use direct numeric values to avoid formatting issues
                const fields = {
                    'panjang': (panjang !== null ? panjang.toFixed(3) : '0'),
                    'lebar': (lebar !== null ? lebar.toFixed(3) : '0'),
                    'tinggi': (tinggi !== null ? tinggi.toFixed(3) : '0'),
                    'meter_kubik': (meter !== null ? meter.toFixed(3) : '0'),
                    'tonase': (tonase !== null ? tonase.toFixed(3) : '0'),
                    'nama_barang': nama || '',
                    'jumlah': jumlah || 0,
                    'satuan': satuan || ''
                };
                
                console.log(`Row ${idx} - Fields to submit:`, fields);

                Object.keys(fields).forEach(k => {
                    const v = fields[k];
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.className = 'generated-dimensi-item';
                    // name like dimensi_items[0][panjang]
                    input.name = `dimensi_items[${idx}][${k}]`;
                    input.value = v;
                    formElement.appendChild(input);
                });
            });
            // Debug log created hidden fields for developer
            const created = Array.from(formElement.querySelectorAll('.generated-dimensi-item')).map(i => ({name: i.name, value: i.value}));
            console.log('Serialized dimensi_items from LCL rows:', created);
        } catch (err) {
            // ignore
        }
    }

    // Call on page load to handle old values
    document.addEventListener('DOMContentLoaded', function() {
        handleTipeKontainerChange();
    });

    function initializeTermDropdown() {
        const searchInput = document.getElementById('termSearch');
        const dropdown = document.getElementById('termDropdown');
        const hiddenSelect = document.getElementById('term_id');
        const options = document.querySelectorAll('.term-option');

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                // Set the hidden select value
                hiddenSelect.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#termSearch') && !e.target.closest('#termDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });

        // Set initial value if exists
        const selectedOption = hiddenSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent;
        }
    }

    function initializeTujuanPengirimanDropdown() {
        const searchInput = document.getElementById('tujuanPengirimanSearch');
        const dropdown = document.getElementById('tujuanPengirimanDropdown');
        const hiddenSelect = document.getElementById('tujuan_pengiriman');
        const options = document.querySelectorAll('.tujuan-pengiriman-option');

        // Initialization for Tujuan Pengiriman Dropdown

        if (!searchInput || !dropdown || !hiddenSelect) {
            return;
        }

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach((option, index) => {
            // Setup click handler for tujuan option
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');


                // Set the hidden select value
                hiddenSelect.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');

            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#tujuanPengirimanSearch') && !e.target.closest('#tujuanPengirimanDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });

        // Set initial value if exists
        const selectedOption = hiddenSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent;
        }
    }

    function initializeSupirDropdown() {
        const searchInput = document.getElementById('supirSearch');
        const dropdown = document.getElementById('supirDropdown');
        const hiddenInput = document.getElementById('supir');
        const platInput = document.getElementById('no_plat');
        const options = document.querySelectorAll('.supir-option');

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Update hidden input when typing manually
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            hiddenInput.value = this.value; // Always update hidden input

            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');
                const plat = this.getAttribute('data-plat');

                // Set the hidden input value
                hiddenInput.value = value;

                // Update search input
                searchInput.value = text;

                // Auto-fill plat if available
                if (plat) {
                    platInput.value = plat;
                }

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#supirSearch') && !e.target.closest('#supirDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });
    }

    function initializeKenekDropdown() {
        const searchInput = document.getElementById('kenekSearch');
        const dropdown = document.getElementById('kenekDropdown');
        const hiddenInput = document.getElementById('kenek');
        const options = document.querySelectorAll('.kenek-option');

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Update hidden input when typing manually
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            hiddenInput.value = this.value; // Always update hidden input

            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                // Set the hidden input value
                hiddenInput.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#kenekSearch') && !e.target.closest('#kenekDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });
    }

    function initializeNoKontainerDropdown() {
        const searchInput = document.getElementById('noKontainerSearch');
        const dropdown = document.getElementById('noKontainerDropdown');
        const hiddenInput = document.getElementById('no_kontainer');
        const manualField = document.getElementById('no_kontainer_manual');
        const options = document.querySelectorAll('.no-kontainer-option');

        if (!searchInput || !dropdown || !hiddenInput) {
            return;
        }

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');
                const size = this.getAttribute('data-size');

                // Set the hidden input value
                hiddenInput.value = value;

                // Update search input
                searchInput.value = text;

                // Auto-fill size_kontainer
                setSizeKontainerValue(size);

                // Handle manual field
                if (value === '__manual__') {
                    manualField.classList.remove('hidden');
                    manualField.focus();
                } else {
                    manualField.classList.add('hidden');
                }

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#noKontainerSearch') && !e.target.closest('#noKontainerDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });

        // Set initial value if exists
        if (hiddenInput.value) {
            const selectedOption = document.querySelector(`.no-kontainer-option[data-value="${hiddenInput.value}"]`);
            if (selectedOption) {
                searchInput.value = selectedOption.getAttribute('data-text');
                const size = selectedOption.getAttribute('data-size');
                setSizeKontainerValue(size);
            } else if (hiddenInput.value === '__manual__' && manualField.value) {
                searchInput.value = manualField.value;
                manualField.classList.remove('hidden');
            }
        }
    }

    // New functions for LCL-style dimensi input
    let dimensiCounterNew = 1;

    function calculateVolumeNew(rowElement) {
        if (!rowElement) {
            console.warn('⚠️ calculateVolumeNew: rowElement is null');
            return;
        }

        const panjangInput = rowElement.querySelector('input[name="panjang[]"]');
        const lebarInput = rowElement.querySelector('input[name="lebar[]"]');
        const tinggiInput = rowElement.querySelector('input[name="tinggi[]"]');
        const volumeInput = rowElement.querySelector('input[name="meter_kubik[]"]');

        if (!panjangInput || !lebarInput || !tinggiInput || !volumeInput) {
            console.warn('⚠️ calculateVolumeNew: Missing inputs:', {
                panjang: !!panjangInput,
                lebar: !!lebarInput,
                tinggi: !!tinggiInput,
                volume: !!volumeInput
            });
            return;
        }

        const panjang = parseFloat(panjangInput.value) || 0;
        const lebar = parseFloat(lebarInput.value) || 0;
        const tinggi = parseFloat(tinggiInput.value) || 0;
        const jumlahInput = rowElement.querySelector('input[name="jumlah[]"]');
        const jumlah = jumlahInput ? (parseInt(jumlahInput.value, 10) || 1) : 1;

        console.log('📐 calculateVolumeNew input values:', { 
            panjang: panjang, 
            lebar: lebar, 
            tinggi: tinggi,
            jumlah: jumlah,
            panjangRaw: panjangInput.value,
            lebarRaw: lebarInput.value,
            tinggiRaw: tinggiInput.value
        });

        if (panjang > 0 && lebar > 0 && tinggi > 0 && jumlah > 0) {
            // Calculate volume in cubic meters (m³) including jumlah
            const volume = panjang * tinggi * lebar * jumlah;
            
            // Validate: check for unrealistic values (> 100000 m³ likely means user entered cm instead of m)
            if (volume > 100000) {
                alert('⚠️ PERINGATAN: Volume yang dihitung sangat besar (' + volume.toFixed(2) + ' m³).\n\n' +
                      'Pastikan Anda memasukkan dimensi dalam METER, bukan centimeter!\n' +
                      'Contoh: 1.5 meter (bukan 150 cm)');
                panjangInput.value = '';
                lebarInput.value = '';
                tinggiInput.value = '';
                volumeInput.value = '';
                return;
            }
            
            const volumeFormatted = volume.toFixed(3);
            
            console.log('✅ Volume calculation:', {
                formula: `${panjang} × ${lebar} × ${tinggi} × ${jumlah}`,
                result: volume,
                formatted: volumeFormatted,
                previousValue: volumeInput.value
            });
            
            // Store the calculated volume directly without extra formatting
            volumeInput.value = volumeFormatted;
            
            // Verify it was set correctly
            console.log('✅ Volume set to:', volumeInput.value);
            
            // update totals after a single row value change
            calculateTotals();
        } else {
            console.log('⚪ Volume cleared (insufficient values - need all 3 dimensions > 0 and jumlah > 0)');
            volumeInput.value = '';
        }
    }

    // Add new dimensi row function for new format
    document.addEventListener('DOMContentLoaded', function() {
        const addButton = document.getElementById('add-dimensi-btn-new');
        const container = document.getElementById('dimensi-container-new');

        if (addButton && container) {
            addButton.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'dimensi-row-new mb-4 pb-4 border-b border-purple-200 relative';
                newRow.innerHTML = `
                    <button type="button" class="remove-dimensi-btn-new absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_barang[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="1" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="unit" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                            <input type="number" name="panjang[]" class="dimensi-input-new w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeNew(this.closest('.dimensi-row-new'))">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                            <input type="number" name="lebar[]" class="dimensi-input-new w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeNew(this.closest('.dimensi-row-new'))">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                            <input type="number" name="tinggi[]" class="dimensi-input-new w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeNew(this.closest('.dimensi-row-new'))">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                            <input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="0.000" min="0" step="0.001" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                            <input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001">
                        </div>
                    </div>
                `;

                container.appendChild(newRow);
                dimensiCounterNew++;

                // Add remove button event listener
                const removeBtn = newRow.querySelector('.remove-dimensi-btn-new');
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });

                // Add volume calculation event listeners
                const dimensiInputs = newRow.querySelectorAll('.dimensi-input-new');
                dimensiInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateVolumeNew(newRow);
                    });
                });

                // Also attach listeners for tonase, jumlah, satuan to update totals and hidden fields
                const tonaseInput = newRow.querySelector('input[name="tonase[]"]');
                const jumlahInput = newRow.querySelector('input[name="jumlah[]"]');
                const satuanInput = newRow.querySelector('input[name="satuan[]"]');
                const namaInput = newRow.querySelector('input[name="nama_barang[]"]');
                if (tonaseInput) {
                    tonaseInput.addEventListener('input', function() {
                        calculateTotals();
                        updateHiddenBarangFields();
                    });
                }
                if (jumlahInput || satuanInput || namaInput) {
                    [jumlahInput, satuanInput, namaInput].forEach(el => {
                        if (el) el.addEventListener('input', function() {
                            updateHiddenBarangFields();
                        });
                    });
                }

                // Specifically add event listeners for panjang, lebar, tinggi
                const panjangInput = newRow.querySelector('input[name="panjang[]"]');
                const lebarInput = newRow.querySelector('input[name="lebar[]"]');
                const tinggiInput = newRow.querySelector('input[name="tinggi[]"]');
                
                if (panjangInput) {
                    panjangInput.addEventListener('input', function() {
                        calculateVolumeNew(newRow);
                    });
                }
                if (lebarInput) {
                    lebarInput.addEventListener('input', function() {
                        calculateVolumeNew(newRow);
                    });
                }
                if (tinggiInput) {
                    tinggiInput.addEventListener('input', function() {
                        calculateVolumeNew(newRow);
                    });
                }
            });
        }

        // Attach event listeners to existing dimensi-input elements (initial row)
        const existingDimensiInputs = document.querySelectorAll('.dimensi-input-new');
        existingDimensiInputs.forEach(input => {
            input.addEventListener('input', function() {
                const row = input.closest('.dimensi-row-new');
                if (row) {
                    calculateVolumeNew(row);
                }
            });
        });

        // Attach event listeners to existing tonase/jumlah/nama/satuan fields (initial row if present)
        const existingTonases = document.querySelectorAll('input[name="tonase[]"]');
        existingTonases.forEach(input => {
            input.addEventListener('input', function() {
                calculateTotals();
                updateHiddenBarangFields();
            });
        });
        const existingJumlahs = document.querySelectorAll('input[name="jumlah[]"]');
        existingJumlahs.forEach(input => {
            input.addEventListener('input', updateHiddenBarangFields);
        });
        const existingSatuans = document.querySelectorAll('input[name="satuan[]"]');
        existingSatuans.forEach(input => {
            input.addEventListener('input', updateHiddenBarangFields);
        });
        const existingNamas = document.querySelectorAll('input[name="nama_barang[]"]');
        existingNamas.forEach(input => {
            input.addEventListener('input', updateHiddenBarangFields);
        });

        // Also add event listeners specifically for the initial row inputs
        const initialRow = document.querySelector('.dimensi-row-new');
        if (initialRow) {
            const panjangInput = initialRow.querySelector('input[name="panjang[]"]');
            const lebarInput = initialRow.querySelector('input[name="lebar[]"]');
            const tinggiInput = initialRow.querySelector('input[name="tinggi[]"]');
            
            if (panjangInput) {
                panjangInput.addEventListener('input', function() {
                    calculateVolumeNew(initialRow);
                });
            }
            if (lebarInput) {
                lebarInput.addEventListener('input', function() {
                    calculateVolumeNew(initialRow);
                });
            }
            if (tinggiInput) {
                tinggiInput.addEventListener('input', function() {
                    calculateVolumeNew(initialRow);
                });
            }
        }

        // Attach event listener for nama, jumlah, satuan to keep hidden legacy fields in sync
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name="nama_barang[]"], input[name="jumlah[]"], input[name="satuan[]"]')) {
                updateHiddenBarangFields();
            }
        });

        // Run initial calculation for any prefilled dimensi rows
        const existingDimensiRows = document.querySelectorAll('#dimensi-container-new .dimensi-row-new');
        existingDimensiRows.forEach(row => calculateVolumeNew(row));
        // Run initial update of hidden legacy fields
        updateHiddenBarangFields();

        // Add form submission debugging
        const formElement = document.querySelector('form');
        if (formElement) {
            formElement.addEventListener('submit', function(e) {
                // First, ensure all calculations are up to date
                calculateAllVolumesAndTotals();
                updateHiddenBarangFields();
                
                // Validate volumes before submission
                const volumeInputs = document.querySelectorAll('input[name="meter_kubik[]"]');
                let hasInvalidVolume = false;
                let maxVolume = 0;
                
                volumeInputs.forEach(input => {
                    const vol = parseFloat(input.value) || 0;
                    if (vol > maxVolume) maxVolume = vol;
                    if (vol > 100000) {
                        hasInvalidVolume = true;
                    }
                });
                
                if (hasInvalidVolume) {
                    e.preventDefault();
                    alert('❌ ERROR: Volume terlalu besar (' + maxVolume.toFixed(2) + ' m³)!\n\n' +
                          'Pastikan Anda memasukkan dimensi dalam METER, bukan centimeter.\n' +
                          'Contoh: Masukkan 1.5 untuk 1.5 meter (bukan 150).\n\n' +
                          'Silakan perbaiki input dimensi Anda.');
                    return false;
                }
                
                console.log('=== FORM SUBMISSION DEBUG ===');
                
                // Check each dimensi row AFTER calculations
                const dimensiRows = document.querySelectorAll('#dimensi-container-new .dimensi-row-new');
                console.log(`Total rows: ${dimensiRows.length}`);
                
                dimensiRows.forEach((row, index) => {
                    const panjangInput = row.querySelector('input[name="panjang[]"]');
                    const lebarInput = row.querySelector('input[name="lebar[]"]');
                    const tinggiInput = row.querySelector('input[name="tinggi[]"]');
                    const volumeInput = row.querySelector('input[name="meter_kubik[]"]');
                    const tonaseInput = row.querySelector('input[name="tonase[]"]');
                    const namaInput = row.querySelector('input[name="nama_barang[]"]');
                    
                    const panjang = panjangInput?.value || '';
                    const lebar = lebarInput?.value || '';
                    const tinggi = tinggiInput?.value || '';
                    const volume = volumeInput?.value || '';
                    const tonase = tonaseInput?.value || '';
                    const nama = namaInput?.value || '';
                    
                    console.log(`Row ${index + 1}:`, {
                        nama_barang: nama,
                        panjang: panjang,
                        lebar: lebar, 
                        tinggi: tinggi,
                        meter_kubik: volume,
                        tonase: tonase
                    });
                    
                    // Check if inputs exist
                    console.log(`Row ${index + 1} inputs exist:`, {
                        panjangExists: !!panjangInput,
                        lebarExists: !!lebarInput,
                        tinggiExists: !!tinggiInput,
                        volumeExists: !!volumeInput,
                        tonaseExists: !!tonaseInput,
                        namaExists: !!namaInput
                    });
                });
                
                // Show FormData that will be sent
                const formData = new FormData(formElement);
                console.log('=== FORMDATA ARRAYS ===');
                const panjangVals = formData.getAll('panjang[]');
                const lebarVals = formData.getAll('lebar[]');
                const tinggiVals = formData.getAll('tinggi[]');
                const jumlahVals = formData.getAll('jumlah[]');
                const volumeVals = formData.getAll('meter_kubik[]');
                const tonaseVals = formData.getAll('tonase[]');
                const namaVals = formData.getAll('nama_barang[]');
                
                console.log('panjang[]:', panjangVals);
                console.log('lebar[]:', lebarVals);
                console.log('tinggi[]:', tinggiVals);
                console.log('jumlah[]:', jumlahVals);
                console.log('meter_kubik[]:', volumeVals);
                console.log('tonase[]:', tonaseVals);
                console.log('nama_barang[]:', namaVals);
                
                // Manual calculation verification
                console.log('=== MANUAL CALCULATION VERIFICATION ===');
                for (let i = 0; i < panjangVals.length; i++) {
                    const p = parseFloat(panjangVals[i]);
                    const l = parseFloat(lebarVals[i]);
                    const t = parseFloat(tinggiVals[i]);
                    const j = parseInt(jumlahVals[i], 10) || 1;
                    const v = parseFloat(volumeVals[i]);
                    const expectedVolume = p * l * t * j;
                    
                    console.log(`Item ${i + 1}:`, {
                        panjang: p,
                        lebar: l,
                        tinggi: t,
                        jumlah: j,
                        volumeFromForm: v,
                        volumeCalculated: expectedVolume,
                        match: Math.abs(v - expectedVolume) < 0.001 ? '✅ MATCH' : '❌ MISMATCH'
                    });
                }
                
                // DISABLED: serializeLclRowsToDimensiItems() - form already sends arrays correctly
                // serializeLclRowsToDimensiItems();
                // Let form submit normally for now
                // e.preventDefault(); // Remove this to allow submission
            });
        }
    });

    // Image Upload Functions
    function previewImages(input) {
        const previewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('image-preview-grid');
        
        if (input.files && input.files.length > 0) {
            // Show preview container
            previewContainer.classList.remove('hidden');
            
            // Clear previous previews
            previewGrid.innerHTML = '';
            
            // Limit to 5 files maximum
            const filesToProcess = Math.min(input.files.length, 5);
            let validFileCount = 0;
            
            for (let i = 0; i < filesToProcess; i++) {
                const file = input.files[i];
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    console.warn(`File ${file.name} is not an image`);
                    continue;
                }
                
                // Validate file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert(`File ${file.name} terlalu besar. Maksimal 10MB per file.`);
                    continue;
                }
                
                validFileCount++;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative bg-gray-50 rounded-lg border border-gray-200 p-2 hover:shadow-md transition-shadow image-preview-item';
                    previewDiv.dataset.fileIndex = i;
                    
                    previewDiv.innerHTML = `
                        <div class="relative">
                            <img src="${e.target.result}" 
                                 alt="Preview ${validFileCount}" 
                                 class="w-full h-20 object-cover rounded hover:opacity-90 transition-opacity">
                            <button type="button" 
                                    onclick="removePreview(this, ${i})"
                                    class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 transition-colors shadow-sm remove-preview-btn"
                                    title="Hapus gambar">
                                ×
                            </button>
                            <div class="absolute bottom-1 left-1 bg-black bg-opacity-60 text-white text-xs px-1 rounded">
                                ${validFileCount}
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1 truncate" title="${file.name}">${file.name}</p>
                        <p class="text-xs text-gray-400">${formatFileSize(file.size)}</p>
                    `;
                    
                    previewGrid.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            }
            
            // Show warning if more than 5 files selected
            if (input.files.length > 5) {
                alert('Maksimal 5 gambar yang dapat diupload. Hanya 5 gambar pertama yang akan diproses.');
            }
            
            // Show summary
            if (validFileCount > 0) {
                const summaryText = document.createElement('p');
                summaryText.className = 'text-xs text-green-600 mt-2 font-medium';
                summaryText.innerHTML = `<i class="fas fa-check-circle mr-1"></i>${validFileCount} gambar siap diupload`;
                previewContainer.appendChild(summaryText);
            }
        } else {
            // Hide preview container if no files
            previewContainer.classList.add('hidden');
        }
    }

    // Remove preview image
    function removePreview(button, index) {
        const input = document.getElementById('gambar_tanda_terima');
        const previewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('image-preview-grid');
        
        // Remove the preview div
        button.closest('.image-preview-item').remove();
        
        // Hide preview container if no more images
        if (previewGrid.children.length === 0) {
            previewContainer.classList.add('hidden');
            input.value = ''; // Clear file input
        }
    }

    // Format file size for display
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Drag and drop functionality
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.querySelector('.upload-dropzone');
        const fileInput = document.getElementById('gambar_tanda_terima');
        
        if (dropZone && fileInput) {
            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });
            
            // Highlight drop zone when dragging over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });
            
            // Handle dropped files
            dropZone.addEventListener('drop', handleDrop, false);
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            function highlight(e) {
                dropZone.classList.add('dragover');
            }
            
            function unhighlight(e) {
                dropZone.classList.remove('dragover');
            }
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                // Validate files before setting to input
                const validFiles = [];
                const maxSize = 10 * 1024 * 1024; // 10MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                
                for (let i = 0; i < Math.min(files.length, 5); i++) {
                    const file = files[i];
                    
                    if (!allowedTypes.includes(file.type)) {
                        alert(`File ${file.name} bukan format gambar yang diizinkan. Gunakan: JPG, PNG, GIF, atau WEBP.`);
                        continue;
                    }
                    
                    if (file.size > maxSize) {
                        alert(`File ${file.name} terlalu besar (${formatFileSize(file.size)}). Maksimal 10MB per file.`);
                        continue;
                    }
                    
                    validFiles.push(file);
                }
                
                if (validFiles.length > 0) {
                    // Create a new FileList-like object with valid files
                    const dataTransfer = new DataTransfer();
                    validFiles.forEach(file => dataTransfer.items.add(file));
                    
                    // Set files to input
                    fileInput.files = dataTransfer.files;
                    
                    // Trigger preview
                    previewImages(fileInput);
                    
                    if (validFiles.length < files.length) {
                        alert(`${validFiles.length} dari ${files.length} file berhasil dipilih. File lainnya tidak memenuhi kriteria.`);
                    }
                } else {
                    alert('Tidak ada file yang valid untuk diupload.');
                }
            }
        }
    });
    
    // Function to open penerima popup window
    function openPenerimaPopup() {
        const width = 600;
        const height = 500;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        
        const popup = window.open(
            '{{ route("tanda-terima.penerima.create") }}',
            'TambahPenerima',
            `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
        
        if (popup) {
            popup.focus();
        } else {
            alert('Pop-up diblokir! Silakan izinkan pop-up untuk situs ini.');
        }
    }

    // Function to open pengirim popup window
    function openPengirimPopup() {
        const width = 600;
        const height = 500;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        
        const popup = window.open(
            '{{ route("tanda-terima.penerima.create") }}',
            'TambahPengirim',
            `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
        
        if (popup) {
            popup.focus();
        } else {
            alert('Pop-up diblokir! Silakan izinkan pop-up untuk situs ini.');
        }
    }

    // Listen for message from popup when new penerima/pengirim is added
    window.addEventListener('message', function(event) {
        // Verify origin for security
        if (event.origin !== window.location.origin) return;
        
        if (event.data.type === 'penerimaAdded') {
            const newData = event.data.penerima;
            
            // Add to both penerima and pengirim select (same data source)
            const penerimaSelect = $('#penerima');
            const pengirimSelect = $('#pengirim');
            
            // Add new option to penerima
            const penerimaOption = new Option(newData.nama, newData.nama, true, true);
            $(penerimaOption).attr('data-alamat', newData.alamat || '');
            penerimaSelect.append(penerimaOption);
            
            // Add new option to pengirim  
            const pengirimOption = new Option(newData.nama, newData.nama, false, false);
            $(pengirimOption).attr('data-alamat', newData.alamat || '');
            pengirimSelect.append(pengirimOption);
            
            // Trigger select2 change and auto-fill alamat for penerima
            penerimaSelect.trigger('change');
            $('#alamat_penerima').val(newData.alamat || '');
            
            console.log('✓ New penerima/pengirim added:', newData.nama);
        }
    });
</script>

<!-- CSS untuk Image Upload -->
<style>
    /* Upload Area Styling */
    .upload-dropzone {
        transition: all 0.3s ease;
    }
    
    .upload-dropzone.dragover {
        border-color: #3b82f6;
        background-color: #eff6ff;
        transform: scale(1.02);
    }
    
    .upload-dropzone:hover {
        border-color: #60a5fa;
        background-color: #f8fafc;
    }
    
    .image-preview-item {
        transition: all 0.2s ease;
    }
    
    .image-preview-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .remove-preview-btn {
        transition: all 0.2s ease;
    }
    
    .remove-preview-btn:hover {
        transform: scale(1.1);
    }
    
    /* Loading spinner for image preview */
    .image-loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush
