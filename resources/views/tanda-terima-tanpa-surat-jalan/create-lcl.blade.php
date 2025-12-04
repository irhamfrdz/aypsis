@extends('layouts.app')

@section('title', 'Buat Tanda Terima LCL')
@section('page_title', 'Buat Tanda Terima LCL')

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
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
        outline: none;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
        background-color: #10b981;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #d1fae5;
        color: #065f46;
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
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Buat Tanda Terima LCL</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-600">Less Container Load - Kontainer dibagi dengan beberapa pengirim</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Tipe: LCL
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('tanda-terima-tanpa-surat-jalan.pilih-tipe') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
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

            <form action="{{ route('tanda-terima-lcl.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- 1. Informasi Dasar -->
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Nomor Tanda Terima -->
                        <div>
                            <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Tanda Terima
                            </label>
                            <input type="text" name="nomor_tanda_terima" id="nomor_tanda_terima"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('nomor_tanda_terima') }}"
                                   placeholder="TTR-LCL-001 (boleh dikosongkan)">
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Input manual, boleh dikosongkan jika belum ada nomor
                            </p>
                            @error('nomor_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Tanda Terima -->
                        <div>
                            <label for="tanggal_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_tanda_terima" id="tanggal_tanda_terima"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('tanggal_tanda_terima', date('Y-m-d')) }}" required>
                            @error('tanggal_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor Surat Jalan Customer -->
                        <div>
                            <label for="no_surat_jalan_customer" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Surat Jalan Customer
                            </label>
                            <input type="text" name="no_surat_jalan_customer" id="no_surat_jalan_customer"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('no_surat_jalan_customer') }}"
                                   placeholder="SJ-CUS-001">
                            @error('no_surat_jalan_customer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Term -->
                        <div>
                            <label for="termSearch" class="block text-sm font-medium text-gray-700 mb-1">
                                Term <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="termSearch" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="Cari term..." autocomplete="off" required>
                                <div id="termDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    @foreach($terms as $term)
                                        <div class="term-option px-3 py-2 hover:bg-green-50 cursor-pointer text-sm border-b border-gray-100"
                                             data-value="{{ $term->id }}" data-text="{{ $term->nama_status }}">
                                            {{ $term->nama_status }}
                                        </div>
                                    @endforeach
                                </div>
                                <select name="term_id" id="term_id" class="hidden">
                                    <option value="">Pilih Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                            {{ $term->nama_status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('term_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 2. Informasi Penerima dan Pengirim -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Informasi Penerima dan Pengirim
                    </h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Kolom Penerima -->
                        <div class="space-y-4">
                            <h4 class="font-medium text-blue-800 border-b border-blue-200 pb-2">Data Penerima</h4>
                            
                            <!-- Nama Penerima -->
                            <div>
                                <label for="nama_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Penerima <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <select name="nama_penerima" id="nama_penerima" required
                                            class="select2-penerima flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_penerima') border-red-500 @enderror">
                                        <option value="">-- Pilih Penerima --</option>
                                        @if(isset($masterPengirimPenerima))
                                            @foreach($masterPengirimPenerima as $item)
                                                <option value="{{ $item->nama }}" 
                                                        data-alamat="{{ $item->alamat }}"
                                                        {{ old('nama_penerima') == $item->nama ? 'selected' : '' }}>
                                                    {{ $item->nama }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="button" 
                                            onclick="openPenerimaPopup()"
                                            class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition-colors flex items-center"
                                            title="Tambah Penerima Baru">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('nama_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- PIC Penerima -->
                            <div>
                                <label for="pic_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                    PIC Penerima
                                </label>
                                <input type="text" name="pic_penerima" id="pic_penerima"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('pic_penerima') }}"
                                       placeholder="Nama PIC Penerima">
                                @error('pic_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Telepon Penerima -->
                            <div>
                                <label for="telepon_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                    Telepon Penerima
                                </label>
                                <input type="text" name="telepon_penerima" id="telepon_penerima"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('telepon_penerima') }}"
                                       placeholder="08123456789">
                                @error('telepon_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat Penerima -->
                            <div>
                                <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Penerima <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat_penerima" id="alamat_penerima" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          required placeholder="Alamat lengkap penerima...">{{ old('alamat_penerima') }}</textarea>
                                @error('alamat_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Pengirim -->
                        <div class="space-y-4">
                            <h4 class="font-medium text-blue-800 border-b border-blue-200 pb-2">Data Pengirim</h4>
                            
                            <!-- Nama Pengirim -->
                            <div>
                                <label for="nama_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Pengirim <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <select name="nama_pengirim" id="nama_pengirim" required
                                            class="select2-pengirim flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_pengirim') border-red-500 @enderror">
                                        <option value="">-- Pilih Pengirim --</option>
                                        @if(isset($masterPengirimPenerima))
                                            @foreach($masterPengirimPenerima as $item)
                                                <option value="{{ $item->nama }}"
                                                        data-alamat="{{ $item->alamat }}"
                                                        {{ old('nama_pengirim') == $item->nama ? 'selected' : '' }}>
                                                    {{ $item->nama }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="button" 
                                            onclick="openPengirimPopup()"
                                            class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition-colors flex items-center"
                                            title="Tambah Pengirim Baru">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('nama_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- PIC Pengirim -->
                            <div>
                                <label for="pic_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                    PIC Pengirim
                                </label>
                                <input type="text" name="pic_pengirim" id="pic_pengirim"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('pic_pengirim') }}"
                                       placeholder="Nama PIC Pengirim">
                                @error('pic_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Telepon Pengirim -->
                            <div>
                                <label for="telepon_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                    Telepon Pengirim
                                </label>
                                <input type="text" name="telepon_pengirim" id="telepon_pengirim"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('telepon_pengirim') }}"
                                       placeholder="08123456789">
                                @error('telepon_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat Pengirim -->
                            <div>
                                <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Pengirim <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat_pengirim" id="alamat_pengirim" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          required placeholder="Alamat lengkap pengirim...">{{ old('alamat_pengirim') }}</textarea>
                                @error('alamat_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Dimensi dan Volume -->
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Dimensi dan Volume
                        </h3>
                        <button type="button"
                                id="add-dimensi-btn"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Dimensi
                        </button>
                    </div>

                    <div id="dimensi-container">
                        <div class="dimensi-row mb-4 pb-4 border-b border-purple-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label for="nama_barang_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Nama Barang
                                    </label>
                                    <input type="text"
                                           name="nama_barang[]" 
                                           id="nama_barang_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="Nama barang"
                                           value="{{ old('nama_barang.0') }}">
                                </div>
                                <div>
                                    <label for="jumlah_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jumlah
                                    </label>
                                    <input type="number"
                                           name="jumlah[]"
                                           id="jumlah_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0"
                                           value="{{ old('jumlah.0') }}"
                                           min="0"
                                           step="1">
                                </div>
                                <div>
                                    <label for="satuan_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Satuan
                                    </label>
                                    <input type="text"
                                           name="satuan[]"
                                           id="satuan_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="Pcs, Kg, Box"
                                           value="{{ old('satuan.0') }}">
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
                                           class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0.000"
                                           value="{{ old('panjang.0') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume(this.closest('.dimensi-row'))">
                                </div>
                                <div>
                                    <label for="lebar_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Lebar (m)
                                    </label>
                                    <input type="number"
                                           name="lebar[]"
                                           id="lebar_0"
                                           class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0.000"
                                           value="{{ old('lebar.0') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume(this.closest('.dimensi-row'))">
                                </div>
                                <div>
                                    <label for="tinggi_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tinggi (m)
                                    </label>
                                    <input type="number"
                                           name="tinggi[]"
                                           id="tinggi_0"
                                           class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0.000"
                                           value="{{ old('tinggi.0') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume(this.closest('.dimensi-row'))">
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
                                Volume akan dihitung otomatis dari panjang × lebar × tinggi
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 5. Informasi Supir -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi Supir
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Supir -->
                        <div>
                            <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Supir <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="supirSearch" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                       placeholder="Cari atau ketik nama supir..." autocomplete="off" required 
                                       value="{{ old('supir', 'Supir Customer') }}">
                                <div id="supirDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    @foreach($supirs as $supir)
                                        <div class="supir-option px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100"
                                             data-value="{{ $supir->nama_supir }}" 
                                             data-text="{{ $supir->nama_supir }}"
                                             data-plat="{{ $supir->no_plat }}">
                                            <div class="font-medium">{{ $supir->nama_supir }}</div>
                                            <div class="text-xs text-gray-500">{{ $supir->no_plat }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="supir" id="supir" required value="{{ old('supir', 'Supir Customer') }}">
                            </div>
                            @error('supir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No Plat -->
                        <div>
                            <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Plat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_plat" id="no_plat"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                   value="{{ old('no_plat', 'Plat Customer') }}" required
                                   placeholder="B 1234 XYZ">
                            @error('no_plat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 6. Informasi Kontainer -->
                <div class="bg-teal-50 p-4 rounded-lg border border-teal-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Informasi Kontainer
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Nomor Kontainer -->
                        <div>
                            <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Kontainer
                            </label>
                            <div class="relative">
                                <input type="text" id="nomorKontainerSearch" placeholder="Cari nomor kontainer..." autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <div id="nomorKontainerDropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto hidden">
                                    @if(isset($containerOptions) && count($containerOptions))
                                        @foreach($containerOptions as $opt)
                                            <div class="nomor-kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer" data-value="{{ $opt['value'] }}" data-text="{{ $opt['label'] }}@if(!empty($opt['size'])) - {{ $opt['size'] }}@endif" data-size="{{ $opt['size'] }}" data-source="{{ $opt['source'] }}">
                                                {{ $opt['label'] }}@if(!empty($opt['size'])) - {{ $opt['size'] }}@endif
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="nomor-kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer text-blue-600" data-value="__manual__" data-text="&raquo; Ketik manual / Lainnya">
                                        &raquo; Ketik manual / Lainnya
                                    </div>
                                </div>
                                <input type="hidden" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer') }}">
                            </div>
                            <input type="text" name="nomor_kontainer_manual" id="nomor_kontainer_manual" value="{{ old('nomor_kontainer_manual') }}" placeholder="Masukkan nomor kontainer jika memilih Lainnya" class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md text-sm hidden" />
                            <p class="mt-1 text-xs text-gray-500">Isi jika sudah ditentukan kontainernya, kosongkan jika belum</p>
                            @error('nomor_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Size Kontainer -->
                        <div>
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Size Kontainer
                            </label>
                            <select name="size_kontainer" id="size_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Pilih Size Kontainer</option>
                                <option value="20ft" {{ old('size_kontainer') == '20ft' ? 'selected' : '' }}>20 Feet</option>
                                <option value="40ft" {{ old('size_kontainer') == '40ft' ? 'selected' : '' }}>40 Feet</option>
                                <option value="40hc" {{ old('size_kontainer') == '40hc' ? 'selected' : '' }}>40 Feet High Cube</option>
                                <option value="45ft" {{ old('size_kontainer') == '45ft' ? 'selected' : '' }}>45 Feet</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Ukuran kontainer yang akan digunakan</p>
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nomor Seal -->
                        <div>
                            <label for="nomor_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Seal
                            </label>
                            <input type="text" name="nomor_seal" id="nomor_seal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                   value="{{ old('nomor_seal') }}"
                                   placeholder="Masukkan nomor seal">
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jika diisi, data akan langsung bisa masuk ke prospek
                            </p>
                            @error('nomor_seal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipe Kontainer -->
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe Kontainer
                            </label>
                            <select name="tipe_kontainer" id="tipe_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Pilih Tipe Kontainer</option>
                                <option value="HC" {{ old('tipe_kontainer') == 'HC' ? 'selected' : '' }}>HC (High Cube)</option>
                                <option value="STD" {{ old('tipe_kontainer') == 'STD' ? 'selected' : '' }}>STD (Standard)</option>
                                <option value="RF" {{ old('tipe_kontainer') == 'RF' ? 'selected' : '' }}>RF (Reefer)</option>
                                <option value="OT" {{ old('tipe_kontainer') == 'OT' ? 'selected' : '' }}>OT (Open Top)</option>
                                <option value="FR" {{ old('tipe_kontainer') == 'FR' ? 'selected' : '' }}>FR (Flat Rack)</option>
                                <option value="Dry Container" {{ old('tipe_kontainer') == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Jenis kontainer yang akan digunakan</p>
                            @error('tipe_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>


                </div>

                <!-- 7. Tujuan Pengiriman -->
                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Tujuan Pengiriman
                    </h3>
                    <div>
                        <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">
                            Tujuan Pengiriman <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="tujuanPengirimanSearch" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Cari tujuan pengiriman..." autocomplete="off" required>
                            <div id="tujuanPengirimanDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                @foreach($masterTujuanKirims as $tujuan)
                                    <div class="tujuan-pengiriman-option px-3 py-2 hover:bg-indigo-50 cursor-pointer text-sm border-b border-gray-100"
                                         data-value="{{ $tujuan->id }}" data-text="{{ $tujuan->nama_tujuan }}">
                                        {{ $tujuan->nama_tujuan }}
                                    </div>
                                @endforeach
                            </div>
                            <select name="tujuan_pengiriman" id="tujuan_pengiriman" class="hidden">
                                <option value="">Pilih Tujuan Pengiriman</option>
                                @foreach($masterTujuanKirims as $tujuan)
                                    <option value="{{ $tujuan->id }}" {{ old('tujuan_pengiriman') == $tujuan->id ? 'selected' : '' }}>
                                        {{ $tujuan->nama_tujuan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('tujuan_pengiriman')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 8. Upload Gambar Surat Jalan -->
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Upload Gambar Surat Jalan
                    </h3>

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-orange-400 transition-colors upload-dropzone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="gambar_surat_jalan" class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500">
                                    <span>Upload gambar</span>
                                    <input id="gambar_surat_jalan" name="gambar_surat_jalan[]" type="file" class="sr-only" accept="image/*" multiple onchange="previewImages(this)">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, JPEG, GIF, WEBP sampai 10MB per file (max 5 file)
                            </p>
                        </div>
                    </div>
                    @error('gambar_surat_jalan.*')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Preview Area for Images -->
                    <div id="image-preview-container" class="mt-4 hidden">
                        <label class="block text-xs font-medium text-gray-500 mb-2">
                            <i class="fas fa-eye mr-1 text-orange-600"></i>
                            Preview Gambar
                        </label>
                        <div id="image-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            {{-- Preview images will be inserted here by JavaScript --}}
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.pilih-tipe') }}"
                       class="px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Tanda Terima LCL
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
            $('#nama_penerima').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                
                if (alamat) {
                    $('#alamat_penerima').val(alamat);
                }
            });

            // Clear alamat when penerima is cleared
            $('#nama_penerima').on('select2:clear', function(e) {
                $('#alamat_penerima').val('');
            });

            // Auto-fill alamat pengirim when pengirim is selected
            $('#nama_pengirim').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                
                if (alamat) {
                    $('#alamat_pengirim').val(alamat);
                }
            });

            // Clear alamat when pengirim is cleared
            $('#nama_pengirim').on('select2:clear', function(e) {
                $('#alamat_pengirim').val('');
            });
        } else {
            console.error('Select2 is not loaded!');
        }

        // Initialize other dropdowns
        initializeTermDropdown();
        initializeTujuanPengirimanDropdown();
        initializeSupirDropdown();

        // Add form validation for custom dropdowns
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Validate term selection
                const termSearch = document.getElementById('termSearch');
                const termHidden = document.getElementById('term_id');
                
                if (termSearch && termHidden) {
                    if (!termHidden.value) {
                        e.preventDefault();
                        termSearch.setCustomValidity('Silakan pilih salah satu term dari daftar.');
                        termSearch.reportValidity();
                        return false;
                    }
                }

                // Validate tujuan pengiriman
                const tujuanSearch = document.getElementById('tujuanPengirimanSearch');
                const tujuanHidden = document.getElementById('tujuan_pengiriman');
                
                if (tujuanSearch && tujuanHidden) {
                    if (!tujuanHidden.value) {
                        e.preventDefault();
                        tujuanSearch.setCustomValidity('Silakan pilih salah satu tujuan pengiriman dari daftar.');
                        tujuanSearch.reportValidity();
                        return false;
                    }
                }
            });
        }
    }); // End of jQuery ready
    
    // Function to open popup for adding new penerima
    function openPenerimaPopup() {
        const popupWidth = 700;
        const popupHeight = 600;
        const left = (screen.width - popupWidth) / 2;
        const top = (screen.height - popupHeight) / 2;
        
        window.open(
            '{{ route("tanda-terima.penerima.create") }}',
            'TambahPenerima',
            `width=${popupWidth},height=${popupHeight},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
    }
    
    // Function to open popup for adding new pengirim
    function openPengirimPopup() {
        const popupWidth = 700;
        const popupHeight = 600;
        const left = (screen.width - popupWidth) / 2;
        const top = (screen.height - popupHeight) / 2;
        
        window.open(
            '{{ route("tanda-terima.penerima.create") }}',
            'TambahPengirim',
            `width=${popupWidth},height=${popupHeight},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
    }
    
    // Listen for messages from popup window
    window.addEventListener('message', function(event) {
        // Verify origin for security
        if (event.origin !== window.location.origin) {
            return;
        }
        
        if (event.data && event.data.type === 'penerimaAdded') {
            const penerimaData = event.data.penerima;
            console.log('Received penerima data:', penerimaData);
            
            // Get Select2 instances
            const $penerimaSel = $('#nama_penerima');
            const $pengirimSel = $('#nama_pengirim');
            
            // Create new option for penerima
            const newOptionPenerima = new Option(
                penerimaData.nama,
                penerimaData.nama,
                false,
                false
            );
            $(newOptionPenerima).attr('data-alamat', penerimaData.alamat || '');
            
            // Create new option for pengirim
            const newOptionPengirim = new Option(
                penerimaData.nama,
                penerimaData.nama,
                false,
                false
            );
            $(newOptionPengirim).attr('data-alamat', penerimaData.alamat || '');
            
            // Add to both dropdowns
            $penerimaSel.append(newOptionPenerima);
            $pengirimSel.append(newOptionPengirim);
            
            // Set the new value and trigger change
            $penerimaSel.val(penerimaData.nama).trigger('change');
            
            // Auto-fill alamat penerima
            $('#alamat_penerima').val(penerimaData.alamat || '');
            
            // Show success notification
            const successMsg = document.createElement('div');
            successMsg.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50';
            successMsg.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>${penerimaData.nama}</strong> berhasil ditambahkan!</span>
                </div>
            `;
            document.body.appendChild(successMsg);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                successMsg.remove();
            }, 3000);
        }
    });
    
    // Counter untuk index dimensi baru
    let dimensiCounter = 1;

    function calculateVolume(rowElement) {
        const panjangInput = rowElement ? rowElement.querySelector('[name^="panjang"]') : document.getElementById('panjang_0');
        const lebarInput = rowElement ? rowElement.querySelector('[name^="lebar"]') : document.getElementById('lebar_0');
        const tinggiInput = rowElement ? rowElement.querySelector('[name^="tinggi"]') : document.getElementById('tinggi_0');
        const volumeInput = rowElement ? rowElement.querySelector('[name^="meter_kubik"]') : document.getElementById('meter_kubik_0');

        const panjang = parseFloat(panjangInput.value) || 0;
        const lebar = parseFloat(lebarInput.value) || 0;
        const tinggi = parseFloat(tinggiInput.value) || 0;

        if (panjang > 0 && lebar > 0 && tinggi > 0) {
            const volume = panjang * lebar * tinggi;
            volumeInput.value = volume.toFixed(3);
        } else {
            volumeInput.value = '';
        }
    }

    // Fungsi untuk menambah baris dimensi baru
    document.addEventListener('DOMContentLoaded', function() {
        const addButton = document.getElementById('add-dimensi-btn');
        const container = document.getElementById('dimensi-container');

        if (addButton && container) {
            addButton.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'dimensi-row mb-4 pb-4 border-b border-purple-200 relative';
                newRow.innerHTML = `
                    <button type="button" class="remove-dimensi-btn absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang</label>
                            <input type="text" name="nama_barang[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah</label>
                            <input type="number" name="jumlah[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="0" step="1">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Satuan</label>
                            <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                            <input type="number" name="panjang[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm dimensi-input" placeholder="0.000" min="0" step="0.001">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                            <input type="number" name="lebar[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm dimensi-input" placeholder="0.000" min="0" step="0.001">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                            <input type="number" name="tinggi[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm dimensi-input" placeholder="0.000" min="0" step="0.001">
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
                dimensiCounter++;

                // Tambahkan event listener untuk tombol hapus
                const removeBtn = newRow.querySelector('.remove-dimensi-btn');
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });

                // Tambahkan event listener untuk kalkulasi volume
                const dimensiInputs = newRow.querySelectorAll('.dimensi-input');
                dimensiInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateVolume(newRow);
                    });
                });
                // Also trigger initial volume calculation for new row
                const firstDimensiInput = newRow.querySelector('.dimensi-input');
                if (firstDimensiInput) firstDimensiInput.dispatchEvent(new Event('input'));
            });
        }
        // Attach event listeners to existing dimensi-input elements (initial row)
        const existingDimensiInputs = document.querySelectorAll('.dimensi-input');
        existingDimensiInputs.forEach(input => {
            input.addEventListener('input', function() {
                const row = input.closest('.dimensi-row');
                calculateVolume(row);
            });
        });
        // Run initial calculation for any prefilled dimensi rows
        const existingDimensiRows = document.querySelectorAll('#dimensi-container .dimensi-row');
        existingDimensiRows.forEach(row => calculateVolume(row));
    });

    function initializeTermDropdown() {
        const searchInput = document.getElementById('termSearch');
        const dropdown = document.getElementById('termDropdown');
        const hiddenSelect = document.getElementById('term_id');
        const options = document.querySelectorAll('.term-option');

        // Remove required attribute from hidden select to prevent focus issues
        if (hiddenSelect) {
            hiddenSelect.removeAttribute('required');
        }

        // Add custom validation to search input instead
        if (searchInput) {
            searchInput.setAttribute('required', 'required');
            searchInput.setAttribute('data-term-validation', 'true');
        }

        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            // Clear validation state when user types
            this.setCustomValidity('');
            
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

        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                hiddenSelect.value = value;
                searchInput.value = text;
                searchInput.setCustomValidity(''); // Clear any validation errors
                dropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#termSearch') && !e.target.closest('#termDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Custom validation for form submission
        searchInput.addEventListener('invalid', function() {
            if (!hiddenSelect.value) {
                this.setCustomValidity('Silakan pilih salah satu term dari daftar.');
            }
        });

        // Validate on blur
        searchInput.addEventListener('blur', function() {
            if (this.value && !hiddenSelect.value) {
                this.setCustomValidity('Silakan pilih salah satu term dari daftar yang tersedia.');
            } else if (!this.value) {
                this.setCustomValidity('Term wajib dipilih.');
            }
        });
    }

    function initializeTujuanPengirimanDropdown() {
        const searchInput = document.getElementById('tujuanPengirimanSearch');
        const dropdown = document.getElementById('tujuanPengirimanDropdown');
        const hiddenSelect = document.getElementById('tujuan_pengiriman');
        const options = document.querySelectorAll('.tujuan-pengiriman-option');

        // Remove required from hidden select and add to search input
        if (hiddenSelect) {
            hiddenSelect.removeAttribute('required');
        }
        if (searchInput) {
            searchInput.setAttribute('required', 'required');
        }

        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            // Clear validation state when user types
            this.setCustomValidity('');

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

        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                hiddenSelect.value = value;
                searchInput.value = text;
                searchInput.setCustomValidity(''); // Clear any validation errors
                dropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#tujuanPengirimanSearch') && !e.target.closest('#tujuanPengirimanDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Custom validation
        searchInput.addEventListener('invalid', function() {
            if (!hiddenSelect.value) {
                this.setCustomValidity('Silakan pilih salah satu tujuan pengiriman dari daftar.');
            }
        });

        searchInput.addEventListener('blur', function() {
            if (this.value && !hiddenSelect.value) {
                this.setCustomValidity('Silakan pilih salah satu tujuan pengiriman dari daftar yang tersedia.');
            } else if (!this.value) {
                this.setCustomValidity('Tujuan pengiriman wajib dipilih.');
            }
        });
    }

    function initializeSupirDropdown() {
        const searchInput = document.getElementById('supirSearch');
        const dropdown = document.getElementById('supirDropdown');
        const hiddenInput = document.getElementById('supir');
        const platInput = document.getElementById('no_plat');
        const options = document.querySelectorAll('.supir-option');

        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            hiddenInput.value = this.value;

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

        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');
                const plat = this.getAttribute('data-plat');

                hiddenInput.value = value;
                searchInput.value = text;

                if (plat) {
                    platInput.value = plat;
                }

                dropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#supirSearch') && !e.target.closest('#supirDropdown')) {
                dropdown.classList.add('hidden');
            }
        });
    }

    // Initialize nomor kontainer dropdown
    function initializeNomorKontainerDropdown() {
        const searchInput = document.getElementById('nomorKontainerSearch');
        const dropdown = document.getElementById('nomorKontainerDropdown');
        const hiddenInput = document.getElementById('nomor_kontainer');
        const manualField = document.getElementById('nomor_kontainer_manual');
        const options = document.querySelectorAll('.nomor-kontainer-option');

        if (!searchInput || !dropdown || !hiddenInput) {
            console.error('Required elements not found for nomor kontainer dropdown');
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
            if (!e.target.closest('#nomorKontainerSearch') && !e.target.closest('#nomorKontainerDropdown')) {
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
            const selectedOption = document.querySelector(`.nomor-kontainer-option[data-value="${hiddenInput.value}"]`);
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

    function setSizeKontainerValue(size) {
        const sizeSelect = document.getElementById('size_kontainer');
        if (!sizeSelect) return;
        
        // Normalize for LCL formats if needed
        function normalizeLclSize(s) {
            if (!s) return '';
            s = String(s).toLowerCase();
            if (s.match(/40hc|40 hc/)) return '40hc';
            if (s.match(/40/)) return '40ft';
            if (s.match(/20/)) return '20ft';
            if (s.match(/45/)) return '45ft';
            return s;
        }
        
        size = normalizeLclSize(size);
        let matched = false;
        for (let i = 0; i < sizeSelect.options.length; i++) {
            const opt = sizeSelect.options[i];
            if (!size) {
                opt.selected = false;
                continue;
            }
            if (opt.value === size || (opt.text && opt.text.toLowerCase().includes(String(size).toLowerCase())) || opt.value.replace(/\s|-/g, '').toLowerCase() === String(size).replace(/\s|-/g, '').toLowerCase()) {
                opt.selected = true;
                matched = true;
                break;
            }
        }
        if (!matched) {
            sizeSelect.value = size;
        }
    }

    // Image Upload Functions
    function previewImages(input) {
        const previewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('image-preview-grid');
        
        if (input.files && input.files.length > 0) {
            previewContainer.classList.remove('hidden');
            
            // Clear previous previews
            previewGrid.innerHTML = '';
            
            const maxFiles = 5;
            const filesToProcess = Math.min(input.files.length, maxFiles);
            
            for (let i = 0; i < filesToProcess; i++) {
                const file = input.files[i];
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    continue;
                }
                
                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Salah satu file terlalu besar. Maksimal 10MB per file.');
                    continue;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative bg-gray-50 rounded-lg border border-gray-200 p-2 image-preview-item';
                    previewDiv.dataset.fileIndex = i;
                    
                    previewDiv.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${i + 1}" class="object-cover w-full h-28 rounded"/>
                        <div class="flex justify-between items-center mt-2">
                            <div class="text-xs text-gray-600 truncate">${file.name}</div>
                            <button type="button" onclick="removePreview(this, ${i})" class="text-red-500 hover:text-red-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                    
                    previewGrid.appendChild(previewDiv);
                };
                
                reader.readAsDataURL(file);
            }
            
            if (input.files.length > maxFiles) {
                alert(`Maksimal ${maxFiles} gambar. File yang dipilih: ${input.files.length}. Hanya ${maxFiles} file pertama yang akan diupload.`);
            }
        } else {
            previewContainer.classList.add('hidden');
        }
    }

    function removePreview(button, index) {
        const input = document.getElementById('gambar_surat_jalan');
        const previewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('image-preview-grid');
        
        // Remove preview element
        const previewItem = button.closest('.image-preview-item');
        if (previewItem) {
            previewItem.remove();
        }
        
        // Hide container if no more previews
        if (previewGrid.children.length === 0) {
            previewContainer.classList.add('hidden');
        }
        
        // Remove file from input (create new FileList without the removed file)
        try {
            const files = Array.from(input.files || []);
            const newFiles = files.filter((_, idx) => idx !== index);
            
            const dataTransfer = new DataTransfer();
            newFiles.forEach(f => dataTransfer.items.add(f));
            input.files = dataTransfer.files;
        } catch (err) {
            // If browser doesn't support DataTransfer, just clear the input
            console.warn('Could not remove specific file, browser limitation');
        }
    }

    // DOM Ready initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize nomor kontainer dropdown
        initializeNomorKontainerDropdown();
        
        // Ensure manual value is submitted if manual option chosen
        const createLclForm = document.querySelector('form');
        if (createLclForm) {
            createLclForm.addEventListener('submit', function(e) {
                const hiddenInput = document.getElementById('nomor_kontainer');
                const manualField = document.getElementById('nomor_kontainer_manual');
                if (hiddenInput && hiddenInput.value === '__manual__') {
                    if (!manualField || !manualField.value.trim()) {
                        e.preventDefault();
                        alert('Silakan isi nomor kontainer pada input manual.');
                        (manualField || document.getElementById('nomorKontainerSearch')).focus();
                        return false;
                    }
                    // Set hidden input to manual value
                    hiddenInput.value = manualField.value.trim();
                }
            });
        }
        
        // Drag and drop support
        const dropZone = document.querySelector('.upload-dropzone');
        const fileInput = document.getElementById('gambar_surat_jalan');
        
        if (dropZone && fileInput) {
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.add('border-orange-500', 'bg-orange-50');
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.remove('border-orange-500', 'bg-orange-50');
                }, false);
            });
            
            dropZone.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                fileInput.files = files;
                previewImages(fileInput);
            }, false);
        }
    });
</script>
@endpush