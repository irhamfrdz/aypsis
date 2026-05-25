@extends('layouts.app')

@section('title', 'Buat Tanda Terima LCL')
@section('page_title', 'Buat Tanda Terima LCL')

@push('styles')
<!-- Select2 CSS -->
<link href="https://unpkg.com/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
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
<style>
    /* CamScanner Editor Styling */
    #camscanner-modal {
        background-color: rgba(15, 23, 42, 0.7);
    }
    #scanner-canvas-wrapper {
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.8);
    }
    .scanner-filter-btn.active {
        background-color: rgb(79, 70, 229);
        border-color: rgb(129, 140, 248);
        color: white !important;
    }
    #crop-box [data-handle] {
        position: absolute;
        transition: transform 0.1s ease;
    }
    #crop-box [data-handle]:hover {
        transform: scale(1.3);
    }
    #crop-overlay {
        background-color: rgba(0, 0, 0, 0.5);
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

                        <!-- Surat Jalan Pabrik -->
                        <div>
                            <label for="surat_jalan_pabrik" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Surat Jalan Pabrik
                            </label>
                            <input type="text" name="surat_jalan_pabrik" id="surat_jalan_pabrik"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('surat_jalan_pabrik') }}"
                                   placeholder="SJ-PABRIK-001">
                            @error('surat_jalan_pabrik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Surat Jalan Pabrik -->
                        <div>
                            <label for="tanggal_surat_jalan_pabrik" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal SJ Pabrik
                            </label>
                            <input type="date" name="tanggal_surat_jalan_pabrik" id="tanggal_surat_jalan_pabrik"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('tanggal_surat_jalan_pabrik') }}">
                            @error('tanggal_surat_jalan_pabrik')
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
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-blue-800 border-b border-blue-200 pb-2 flex-1">Data Penerima</h4>
                            </div>
                            
                            <div id="penerima-container">
                                <div class="penerima-row space-y-3 p-3 bg-white rounded border border-blue-200 mb-3">
                                    <!-- Nama Penerima -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Nama Penerima <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <select name="nama_penerima" required
                                                    class="select2-penerima flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
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
                                                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors flex items-center"
                                                    title="Tambah Penerima Baru">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- PIC Penerima -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            PIC Penerima
                                        </label>
                                        <input type="text" name="pic_penerima"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               value="{{ old('pic_penerima') }}"
                                               placeholder="Nama PIC Penerima">
                                    </div>

                                    <!-- Telepon Penerima -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Telepon Penerima
                                        </label>
                                        <input type="text" name="telepon_penerima"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               value="{{ old('telepon_penerima') }}"
                                               placeholder="08123456789">
                                    </div>

                                    <!-- Alamat Penerima -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Alamat Penerima <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="alamat_penerima" rows="2" required
                                                  class="penerima-alamat w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                  placeholder="Alamat lengkap penerima...">{{ old('alamat_penerima') }}</textarea>
                                    </div>

                                    <!-- Notify Party -->
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="block text-sm font-medium text-gray-700">
                                                Notify Party
                                            </label>
                                            <button type="button" 
                                                    onclick="openNotifyPopup()"
                                                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs transition-colors flex items-center"
                                                    title="Tambah Notify Party Baru">
                                                <i class="fas fa-plus mr-1"></i>
                                                Tambah Notify Party
                                            </button>
                                        </div>
                                        <select name="notify_party"
                                                id="notify_party"
                                                class="select2-notify w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                            <option value="">-- Pilih Notify Party --</option>
                                            @if(isset($masterPengirimPenerima))
                                                @foreach($masterPengirimPenerima as $item)
                                                    <option value="{{ $item->nama }}" 
                                                            data-alamat="{{ $item->alamat }}">
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Alamat Notify Party -->
                                    <div class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Alamat Notify Party
                                        </label>
                                        <textarea name="alamat_notify_party" id="alamat_notify_party" rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                  placeholder="Alamat lengkap Notify Party...">{{ old('alamat_notify_party') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Pengirim -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-blue-800 border-b border-blue-200 pb-2 flex-1">Data Pengirim</h4>
                            </div>
                            
                            <div id="pengirim-container">
                                <div class="pengirim-row space-y-3 p-3 bg-white rounded border border-blue-200 mb-3">
                                    <!-- Nama Pengirim -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Nama Pengirim <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <select name="nama_pengirim" required
                                                    class="select2-pengirim flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
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
                                                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors flex items-center"
                                                    title="Tambah Pengirim Baru">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- PIC Pengirim -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            PIC Pengirim
                                        </label>
                                        <input type="text" name="pic_pengirim"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               value="{{ old('pic_pengirim') }}"
                                               placeholder="Nama PIC Pengirim">
                                    </div>

                                    <!-- Telepon Pengirim -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Telepon Pengirim
                                        </label>
                                        <input type="text" name="telepon_pengirim"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               value="{{ old('telepon_pengirim') }}"
                                               placeholder="08123456789">
                                    </div>

                                    <!-- Alamat Pengirim -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Alamat Pengirim <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="alamat_pengirim" rows="2" required
                                                  class="pengirim-alamat w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                  placeholder="Alamat lengkap pengirim...">{{ old('alamat_pengirim') }}</textarea>
                                    </div>
                                </div>
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
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
                                <div>
                                    <label for="nama_barang_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Nama Barang
                                    </label>
                                    <input type="text"
                                           name="nama_barang[]" 
                                           id="nama_barang_0"
                                           class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="Nama barang"
                                           value="{{ old('nama_barang.0') }}"
                                           oninput="toggleUkuranField(this)">
                                </div>
                                <div class="ukuran-container hidden">
                                    <label for="ukuran_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Ukuran
                                    </label>
                                    <input type="text"
                                           name="ukuran[]"
                                           id="ukuran_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="Contoh: 40x40"
                                           value="{{ old('ukuran.0') }}">
                                </div>
                                <div>
                                    <label for="jumlah_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jumlah
                                    </label>
                                    <input type="number"
                                           name="jumlah[]"
                                           id="jumlah_0"
                                           class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="0"
                                           value="{{ old('jumlah.0') }}"
                                           min="0"
                                           step="1"
                                           onchange="calculateVolume(this.closest('.dimensi-row'))">
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
                                Volume akan dihitung otomatis dari panjang × lebar × tinggi × jumlah
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

                <!-- 6. Tujuan Pengiriman -->
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

<!-- CamScanner Modal -->
<div id="camscanner-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-950 bg-opacity-75 backdrop-blur-sm" aria-hidden="true" onclick="closeScannerModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative z-10 inline-block align-middle bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-5xl sm:w-full border border-slate-800">
            <div class="px-6 py-4 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-magic text-indigo-400"></i>
                    <span>CamScanner Document Enhancer</span>
                </h3>
                <button type="button" onclick="closeScannerModal()" class="text-slate-400 hover:text-white transition-colors cursor-pointer">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
                <div class="lg:col-span-2 p-6 bg-slate-950 flex flex-col items-center justify-center min-h-[400px] lg:min-h-[500px] relative overflow-hidden">
                    <div id="scanner-loader" class="absolute inset-0 bg-slate-950/80 z-10 flex flex-col items-center justify-center gap-3 hidden">
                        <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-xs text-slate-400 font-medium">Memproses gambar...</span>
                    </div>
                    <div id="scanner-canvas-wrapper" class="relative max-w-full max-h-[450px] overflow-hidden flex items-center justify-center bg-slate-900 rounded-xl border border-slate-800 p-2 shadow-inner">
                        <canvas id="scanner-canvas" class="max-w-full max-h-[400px] object-contain rounded"></canvas>
                        <div id="crop-overlay" class="absolute inset-0 hidden select-none pointer-events-none">
                            <div id="crop-box" class="absolute border-2 border-dashed border-indigo-400 bg-indigo-500/10 pointer-events-auto cursor-move">
                                <div class="absolute -top-1.5 -left-1.5 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-nwse-resize shadow-md" data-handle="nw"></div>
                                <div class="absolute -top-1.5 -right-1.5 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-nesw-resize shadow-md" data-handle="ne"></div>
                                <div class="absolute -bottom-1.5 -left-1.5 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-nesw-resize shadow-md" data-handle="sw"></div>
                                <div class="absolute -bottom-1.5 -right-1.5 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-nwse-resize shadow-md" data-handle="se"></div>
                                <div class="absolute top-1/2 -left-1.5 -translate-y-1/2 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-ew-resize shadow-md" data-handle="w"></div>
                                <div class="absolute top-1/2 -right-1.5 -translate-y-1/2 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-ew-resize shadow-md" data-handle="e"></div>
                                <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-ns-resize shadow-md" data-handle="n"></div>
                                <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-ns-resize shadow-md" data-handle="s"></div>
                            </div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-3 flex items-center gap-1.5">
                        <i class="fas fa-info-circle"></i>
                        <span>Gunakan panel kanan untuk meningkatkan kontras dokumen atau merotasi.</span>
                    </p>
                </div>
                <div class="p-6 bg-slate-900 border-t lg:border-t-0 lg:border-l border-slate-800 flex flex-col justify-between">
                    <div class="space-y-6">
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Mode Scan (Preset)</span>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="setScannerFilter('original')" id="filter-original"
                                        class="scanner-filter-btn flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-800 bg-slate-950 text-slate-300 hover:text-white hover:bg-slate-800 hover:border-slate-700 transition duration-150 cursor-pointer">
                                    <i class="fas fa-image text-lg mb-1 text-slate-400"></i>
                                    <span class="text-xs font-medium">Asli</span>
                                </button>
                                <button type="button" onclick="setScannerFilter('magic')" id="filter-magic"
                                        class="scanner-filter-btn flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-800 bg-slate-950 text-slate-300 hover:text-white hover:bg-slate-800 hover:border-slate-700 transition duration-150 cursor-pointer">
                                    <i class="fas fa-magic text-lg mb-1 text-indigo-400"></i>
                                    <span class="text-xs font-medium">Magic Color</span>
                                </button>
                                <button type="button" onclick="setScannerFilter('bw')" id="filter-bw"
                                        class="scanner-filter-btn flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-800 bg-slate-950 text-slate-300 hover:text-white hover:bg-slate-800 hover:border-slate-700 transition duration-150 cursor-pointer">
                                    <i class="fas fa-adjust text-lg mb-1 text-teal-400"></i>
                                    <span class="text-xs font-medium">Hitam Putih</span>
                                </button>
                                <button type="button" onclick="setScannerFilter('grayscale')" id="filter-grayscale"
                                        class="scanner-filter-btn flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-800 bg-slate-950 text-slate-300 hover:text-white hover:bg-slate-800 hover:border-slate-700 transition duration-150 cursor-pointer">
                                    <i class="fas fa-palette text-lg mb-1 text-amber-400"></i>
                                    <span class="text-xs font-medium">Grayscale</span>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-4 pt-4 border-t border-slate-800/60">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Penyesuaian Manual</span>
                            <div>
                                <div class="flex justify-between text-xs font-medium text-slate-400 mb-1">
                                    <span>Kecerahan (Brightness)</span>
                                    <span id="val-brightness">0%</span>
                                </div>
                                <input type="range" id="adjust-brightness" min="-100" max="100" value="0" step="5"
                                       oninput="adjustScannerManual('brightness', this.value)"
                                       class="w-full h-1 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-medium text-slate-400 mb-1">
                                    <span>Kontras (Contrast)</span>
                                    <span id="val-contrast">0%</span>
                                </div>
                                <input type="range" id="adjust-contrast" min="-100" max="100" value="0" step="5"
                                       oninput="adjustScannerManual('contrast', this.value)"
                                       class="w-full h-1 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                            </div>
                            <div id="threshold-slider-group" class="hidden">
                                <div class="flex justify-between text-xs font-medium text-slate-400 mb-1">
                                    <span>Ambang Batas (Threshold)</span>
                                    <span id="val-threshold">120</span>
                                </div>
                                <input type="range" id="adjust-threshold" min="0" max="255" value="120" step="5"
                                       oninput="adjustScannerManual('threshold', this.value)"
                                       class="w-full h-1 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                            </div>
                        </div>
                        <div class="space-y-3 pt-4 border-t border-slate-800/60">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Rotasi & Pangkas</span>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="rotateScanner(-90)"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-950 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition duration-150 cursor-pointer">
                                    <i class="fas fa-undo"></i>
                                    <span>Putar Kiri</span>
                                </button>
                                <button type="button" onclick="rotateScanner(90)"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-950 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition duration-150 cursor-pointer">
                                    <i class="fas fa-redo"></i>
                                    <span>Putar Kanan</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="toggleCropper()" id="cropper-toggle-btn"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-slate-950 hover:bg-slate-850 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-indigo-400 transition duration-150 cursor-pointer">
                                    <i class="fas fa-crop-alt"></i>
                                    <span id="cropper-btn-text">Pangkas Manual</span>
                                </button>
                                <button type="button" onclick="autoCropDocument()"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-indigo-950/65 hover:bg-indigo-900 border border-indigo-850 text-xs font-semibold text-indigo-300 hover:text-white transition duration-150 cursor-pointer"
                                        title="Deteksi tepi kertas otomatis">
                                    <i class="fas fa-magic text-indigo-400 mr-0.5 animate-pulse"></i>
                                    <span>Autocrop</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-6 border-t border-slate-800 mt-6">
                        <button type="button" onclick="closeScannerModal()"
                                class="flex-1 px-4 py-2.5 bg-slate-950 hover:bg-slate-800 border border-slate-850 text-slate-300 text-xs font-bold rounded-xl transition duration-150 cursor-pointer">
                            Batal
                        </button>
                        <button type="button" onclick="saveScannerResult()"
                                class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition duration-150 shadow-lg shadow-indigo-600/20 cursor-pointer">
                            Simpan Scan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Select2 JS - jQuery already loaded in layout -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    // Global flag to track Select2 readiness
    window.select2Ready = false;
    
    // Wrap everything in jQuery ready to ensure DOM and libraries are loaded
    jQuery(document).ready(function($) {
        console.log('✓ jQuery loaded, version:', $.fn.jquery);
        
        // Initialize other dropdowns first (these don't need Select2)
        initializeTermDropdown();
        initializeTujuanPengirimanDropdown();
        initializeSupirDropdown();
        
        // Wait for Select2 to be fully loaded with retry mechanism
        function waitForSelect2(callback, attempts) {
            attempts = attempts || 0;
            if (attempts > 30) {
                console.error('❌ Select2 gagal dimuat setelah 10 detik');
                console.log('💡 Mencoba memuat Select2 dari sumber alternatif...');
                
                // Try loading from alternative CDN
                var script = document.createElement('script');
                script.src = 'https://unpkg.com/select2@4.0.13/dist/js/select2.min.js';
                script.onload = function() {
                    console.log('✓ Select2 berhasil dimuat dari CDN alternatif');
                    window.select2Ready = true;
                    var jqInstance = window.jQuery || jQuery || $;
                    callback(jqInstance);
                };
                script.onerror = function() {
                    console.error('❌ Gagal memuat Select2 dari semua sumber');
                    alert('Terjadi kesalahan memuat komponen halaman. Silakan refresh halaman atau periksa koneksi internet Anda.');
                };
                document.head.appendChild(script);
                return;
            }

            if (typeof $.fn.select2 !== 'undefined') {
                console.log('✓ Select2 berhasil dimuat');
                window.select2Ready = true;
                callback($);
            } else {
                setTimeout(function() {
                    waitForSelect2(callback, attempts + 1);
                }, 100);
            }
        }

        // Wait for Select2, then initialize
        waitForSelect2(function(jqInstance) {
            initializeSelect2Dropdowns(jqInstance);
        });

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
    
    // Initialize Select2 for all penerima and pengirim dropdowns
    function initializeSelect2Dropdowns(jq) {
        // Accept jQuery instance from caller (preferred), else fallback to window.jQuery
        var $ = jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
        
        // Double-check Select2 availability
        if (!$ || typeof $.fn.select2 === 'undefined') {
            console.error('❌ Select2 tidak tersedia saat inisialisasi');
            console.log('Local jQuery (arg) tersedia:', !!jq);
            console.log('window.jQuery tersedia:', !!window.jQuery);
            console.log('Select2 available on provided jQuery:', jq && !!(jq.fn && jq.fn.select2));
            console.log('Select2 available on window.jQuery:', !!(window.jQuery && window.jQuery.fn && window.jQuery.fn.select2));
            return;
        }
        
        console.log('🔧 Menginisialisasi Select2 dropdowns using jQuery instance:', ($ && $.fn && $.fn.jquery) ? $.fn.jquery : 'unknown');
        
        // Mark Select2 as ready
        window.select2Ready = true;
        // Save the jQuery instance Select2 is attached to
        window.select2Jq = $;
        
        // Initialize all penerima Select2 dropdowns
        $('.select2-penerima').each(function() {
            // Destroy existing Select2 instance if exists
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
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

            // Auto-fill alamat when selected
            $(this).off('select2:select').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                var row = $(this).closest('.penerima-row');
                
                if (alamat && row.length) {
                    row.find('.penerima-alamat').val(alamat);
                }
            });

            // Clear alamat when cleared
            $(this).off('select2:clear').on('select2:clear', function(e) {
                var row = $(this).closest('.penerima-row');
                if (row.length) {
                    row.find('.penerima-alamat').val('');
                }
            });
        });

        // Initialize all notify party Select2 dropdowns
        $('.select2-notify').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
                placeholder: '-- Pilih Notify Party --',
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

            // Auto-fill alamat when selected
            $(this).off('select2:select').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                
                if (alamat) {
                    $('#alamat_notify_party').val(alamat);
                } else {
                    $('#alamat_notify_party').val('');
                }
            });

            // Clear alamat when cleared
            $(this).off('select2:clear').on('select2:clear', function(e) {
                $('#alamat_notify_party').val('');
            });
        });

        // Initialize all pengirim Select2 dropdowns
        $('.select2-pengirim').each(function() {
            // Destroy existing Select2 instance if exists
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
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

            // Auto-fill alamat when selected
            $(this).off('select2:select').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                var row = $(this).closest('.pengirim-row');
                
                if (alamat && row.length) {
                    row.find('.pengirim-alamat').val(alamat);
                }
            });

            // Clear alamat when cleared
            $(this).off('select2:clear').on('select2:clear', function(e) {
                var row = $(this).closest('.pengirim-row');
                if (row.length) {
                    row.find('.pengirim-alamat').val('');
                }
            });
        });
        
        const totalInitialized = $('.select2-hidden-accessible').length;
        console.log('✓ Select2 berhasil diinisialisasi pada', totalInitialized, 'dropdown');
    }

    // Add new penerima row
    function addPenerimaRow() {
        // Check if Select2 is ready and use the jQuery instance Select2 is attached to
        var $ = window.select2Jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
        if (!window.select2Ready || !$ || typeof $.fn.select2 === 'undefined') {
            console.warn('⚠️ Select2 belum siap, menunggu...');
            setTimeout(addPenerimaRow, 500);
            return;
        }
        
        const container = document.getElementById('penerima-container');
        const newRow = document.createElement('div');
        newRow.className = 'penerima-row space-y-3 p-3 bg-white rounded border border-blue-200 mb-3 relative';
        
        newRow.innerHTML = `
            <button type="button" onclick="removePenerimaRow(this)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Penerima <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <select name="nama_penerima[]" required class="select2-penerima flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">-- Pilih Penerima --</option>
                        @if(isset($masterPengirimPenerima))
                            @foreach($masterPengirimPenerima as $item)
                                <option value="{{ $item->nama }}" data-alamat="{{ $item->alamat }}">{{ $item->nama }}</option>
                            @endforeach
                        @endif
                    </select>
                    <button type="button" onclick="openPenerimaPopup()" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors flex items-center" title="Tambah Penerima Baru">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">PIC Penerima</label>
                <input type="text" name="pic_penerima[]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="Nama PIC Penerima">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon Penerima</label>
                <input type="text" name="telepon_penerima[]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="08123456789">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat Penerima <span class="text-red-500">*</span>
                </label>
                <textarea name="alamat_penerima[]" rows="2" required class="penerima-alamat w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="Alamat lengkap penerima..."></textarea>
            </div>
        `;
        
        container.appendChild(newRow);
        
        // Initialize Select2 for the new row
        var $ = window.select2Jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
        if ($ && typeof $.fn.select2 !== 'undefined') {
            // Initialize only the new select element
            var newSelect = $(newRow).find('.select2-penerima');
            if (newSelect.length > 0 && !newSelect.hasClass('select2-hidden-accessible')) {
                newSelect.select2({
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
                
                // Auto-fill alamat when selected
                newSelect.on('select2:select', function(e) {
                    var selectedOption = e.params.data.element;
                    var alamat = $(selectedOption).data('alamat');
                    var row = $(this).closest('.penerima-row');
                    
                    if (alamat && row.length) {
                        row.find('.penerima-alamat').val(alamat);
                    }
                });

                // Clear alamat when cleared
                newSelect.on('select2:clear', function(e) {
                    var row = $(this).closest('.penerima-row');
                    if (row.length) {
                        row.find('.penerima-alamat').val('');
                    }
                });
                console.log('✓ Select2 diinisialisasi untuk baris penerima baru');
            }
        } else {
            console.error('❌ jQuery/Select2 tidak tersedia! jQuery:', typeof window.jQuery, 'Select2:', typeof (window.jQuery ? window.jQuery.fn.select2 : 'N/A'));
        }
    }

    // Remove penerima row
    function removePenerimaRow(button) {
        const row = button.closest('.penerima-row');
        const container = document.getElementById('penerima-container');
        
        // Prevent removing the last row
        if (container.querySelectorAll('.penerima-row').length > 1) {
            row.remove();
        } else {
            alert('Minimal harus ada 1 penerima!');
        }
    }

    // Add new pengirim row
    function addPengirimRow() {
        // Check if Select2 is ready and use the jQuery instance Select2 is attached to
        var $ = window.select2Jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
        if (!window.select2Ready || !$ || typeof $.fn.select2 === 'undefined') {
            console.warn('⚠️ Select2 belum siap, menunggu...');
            setTimeout(addPengirimRow, 500);
            return;
        }
        
        const container = document.getElementById('pengirim-container');
        const newRow = document.createElement('div');
        newRow.className = 'pengirim-row space-y-3 p-3 bg-white rounded border border-blue-200 mb-3 relative';
        
        newRow.innerHTML = `
            <button type="button" onclick="removePengirimRow(this)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Pengirim <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <select name="nama_pengirim[]" required class="select2-pengirim flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">-- Pilih Pengirim --</option>
                        @if(isset($masterPengirimPenerima))
                            @foreach($masterPengirimPenerima as $item)
                                <option value="{{ $item->nama }}" data-alamat="{{ $item->alamat }}">{{ $item->nama }}</option>
                            @endforeach
                        @endif
                    </select>
                    <button type="button" onclick="openPengirimPopup()" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors flex items-center" title="Tambah Pengirim Baru">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">PIC Pengirim</label>
                <input type="text" name="pic_pengirim[]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="Nama PIC Pengirim">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon Pengirim</label>
                <input type="text" name="telepon_pengirim[]" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="08123456789">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat Pengirim <span class="text-red-500">*</span>
                </label>
                <textarea name="alamat_pengirim[]" rows="2" required class="pengirim-alamat w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="Alamat lengkap pengirim..."></textarea>
            </div>
        `;
        
        container.appendChild(newRow);
        
        // Initialize Select2 for the new row
        var $ = window.select2Jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
        if ($ && typeof $.fn.select2 !== 'undefined') {
            // Initialize only the new select element
            var newSelect = $(newRow).find('.select2-pengirim');
            if (newSelect.length > 0 && !newSelect.hasClass('select2-hidden-accessible')) {
                newSelect.select2({
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
                
                // Auto-fill alamat when selected
                newSelect.on('select2:select', function(e) {
                    var selectedOption = e.params.data.element;
                    var alamat = $(selectedOption).data('alamat');
                    var row = $(this).closest('.pengirim-row');
                    
                    if (alamat && row.length) {
                        row.find('.pengirim-alamat').val(alamat);
                    }
                });

                // Clear alamat when cleared
                newSelect.on('select2:clear', function(e) {
                    var row = $(this).closest('.pengirim-row');
                    if (row.length) {
                        row.find('.pengirim-alamat').val('');
                    }
                });
                console.log('✓ Select2 diinisialisasi untuk baris pengirim baru');
            }
        } else {
            console.error('❌ jQuery/Select2 tidak tersedia! jQuery:', typeof window.jQuery, 'Select2:', typeof (window.jQuery ? window.jQuery.fn.select2 : 'N/A'));
        }
    }

    // Remove pengirim row
    function removePengirimRow(button) {
        const row = button.closest('.pengirim-row');
        const container = document.getElementById('pengirim-container');
        
        // Prevent removing the last row
        if (container.querySelectorAll('.pengirim-row').length > 1) {
            row.remove();
        } else {
            alert('Minimal harus ada 1 pengirim!');
        }
    }
    
    let lastPopupOpened = '';

    // Function to open popup for adding new penerima
    function openPenerimaPopup() {
        lastPopupOpened = 'penerima';
        const popupWidth = 700;
        const popupHeight = 600;
        const left = (screen.width - popupWidth) / 2;
        const top = (screen.height - popupHeight) / 2;
        
        window.open(
            '{{ route("tanda-terima.penerima.create", [], false) }}',
            'TambahPenerima',
            `width=${popupWidth},height=${popupHeight},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
    }
    
    // Function to open popup for adding new pengirim
    function openPengirimPopup() {
        lastPopupOpened = 'pengirim';
        const popupWidth = 700;
        const popupHeight = 600;
        const left = (screen.width - popupWidth) / 2;
        const top = (screen.height - popupHeight) / 2;
        
        window.open(
            '{{ route("tanda-terima.penerima.create", [], false) }}',
            'TambahPengirim',
            `width=${popupWidth},height=${popupHeight},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
    }

    // Function to open popup for adding new notify party
    function openNotifyPopup() {
        lastPopupOpened = 'notify';
        const popupWidth = 700;
        const popupHeight = 600;
        const left = (screen.width - popupWidth) / 2;
        const top = (screen.height - popupHeight) / 2;
        
        window.open(
            '{{ route("tanda-terima.penerima.create", [], false) }}',
            'TambahNotifyParty',
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
            const $ = window.select2Jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
            console.log('Received penerima data:', penerimaData);
            
            // Determine which one should be selected
            const selectAsPenerima = lastPopupOpened === 'penerima';
            const selectAsPengirim = lastPopupOpened === 'pengirim';
            const selectAsNotify = lastPopupOpened === 'notify';
            
            // Create new option for all penerima dropdowns
            const newOptionPenerima = new Option(
                penerimaData.nama,
                penerimaData.nama,
                selectAsPenerima,
                selectAsPenerima
            );
            $(newOptionPenerima).attr('data-alamat', penerimaData.alamat || '');
            
            // Create new option for all pengirim dropdowns
            const newOptionPengirim = new Option(
                penerimaData.nama,
                penerimaData.nama,
                selectAsPengirim,
                selectAsPengirim
            );
            $(newOptionPengirim).attr('data-alamat', penerimaData.alamat || '');

            // Create new option for all notify dropdowns
            const newOptionNotify = new Option(
                penerimaData.nama,
                penerimaData.nama,
                selectAsNotify,
                selectAsNotify
            );
            $(newOptionNotify).attr('data-alamat', penerimaData.alamat || '');
            
            // Add to all penerima dropdowns
            $('.select2-penerima').each(function() {
                $(this).append($(newOptionPenerima).clone());
                if (selectAsPenerima) {
                    $(this).trigger('change');
                }
            });
            
            // Add to all pengirim dropdowns
            $('.select2-pengirim').each(function() {
                $(this).append($(newOptionPengirim).clone());
                if (selectAsPengirim) {
                    $(this).trigger('change');
                }
            });

            // Add to all notify dropdowns
            $('.select2-notify').each(function() {
                $(this).append($(newOptionNotify).clone());
                if (selectAsNotify) {
                    $(this).trigger('change');
                }
            });

            // Auto-fill alamat
            if (selectAsPenerima) {
                $('#alamat_penerima').val(penerimaData.alamat || '');
            } else if (selectAsPengirim) {
                $('#alamat_pengirim').val(penerimaData.alamat || '');
            } else if (selectAsNotify) {
                $('#alamat_notify_party').val(penerimaData.alamat || '');
            }
            
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
        const jumlahInput = rowElement ? rowElement.querySelector('[name^="jumlah"]') : document.getElementById('jumlah_0');
        const volumeInput = rowElement ? rowElement.querySelector('[name^="meter_kubik"]') : document.getElementById('meter_kubik_0');

        const panjang = parseFloat(panjangInput.value) || 0;
        const lebar = parseFloat(lebarInput.value) || 0;
        const tinggi = parseFloat(tinggiInput.value) || 0;
        const jumlah = parseInt(jumlahInput.value) || 1; // Default ke 1 jika kosong

        if (panjang > 0 && lebar > 0 && tinggi > 0) {
            const volume = panjang * tinggi * lebar * jumlah;
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang</label>
                            <input type="text" name="nama_barang[]" class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" oninput="toggleUkuranField(this)">
                        </div>
                        <div class="ukuran-container hidden">
                            <label class="block text-xs font-medium text-gray-500 mb-2">Ukuran</label>
                            <input type="text" name="ukuran[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 40x40">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah</label>
                            <input type="number" name="jumlah[]" class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="0" step="1">
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





    // CamScanner & Image Upload Functions
    var processedImages = [];
    var activeImageIndex = null;
    var originalImgElement = null;
    var currentSettings = {
        filter: 'original',
        rotation: 0,
        brightness: 0,
        contrast: 0,
        threshold: 120
    };

    let cropBoxPercent = { x: 10, y: 10, w: 80, h: 80 };
    let isCropperActive = false;
    let isDraggingBox = false;
    let isResizingBox = false;
    let activeHandle = null;
    let dragStartCoords = { x: 0, y: 0 };
    let cropBoxStartCoords = { x: 0, y: 0, w: 0, h: 0 };

    function previewImages(input) {
        const previewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('image-preview-grid');
        
        if (input.files && input.files.length > 0) {
            previewContainer.classList.remove('hidden');
            
            // Limit to 5 files maximum
            const filesToProcess = Array.from(input.files).slice(0, 5);
            processedImages = new Array(filesToProcess.length);
            let loadedCount = 0;
            
            filesToProcess.forEach((file, index) => {
                const isPdf = file.type === 'application/pdf';
                
                if (isPdf) {
                    processedImages[index] = {
                        file: file,
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        isPdf: true
                    };
                    loadedCount++;
                    checkFinish();
                } else if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        processedImages[index] = {
                            file: file,
                            name: file.name,
                            size: file.size,
                            type: file.type,
                            isPdf: false,
                            originalDataUrl: e.target.result,
                            dataUrl: e.target.result,
                            isProcessed: false,
                            settings: {
                                filter: 'original',
                                rotation: 0,
                                brightness: 0,
                                contrast: 0,
                                threshold: 120
                            },
                            crop: null
                        };
                        loadedCount++;
                        checkFinish();
                    };
                    reader.readAsDataURL(file);
                } else {
                    loadedCount++;
                    checkFinish();
                }
            });
            
            function checkFinish() {
                if (loadedCount === filesToProcess.length) {
                    processedImages = processedImages.filter(item => item !== undefined);
                    renderImagePreviews();
                    syncFileInput();
                }
            }
            
            if (input.files.length > 5) {
                alert('Maksimal 5 gambar yang dapat diupload. Hanya 5 gambar pertama yang akan diproses.');
            }
        }
    }

    function renderImagePreviews() {
        const previewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('image-preview-grid');
        
        if (processedImages.length > 0) {
            previewContainer.classList.remove('hidden');
            previewGrid.innerHTML = '';
            
            processedImages.forEach((item, index) => {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'relative bg-slate-900 border border-slate-800 rounded-xl p-3 hover:shadow-lg hover:border-slate-700 transition duration-200 image-preview-item';
                
                let mediaHtml = '';
                let actionHtml = '';
                
                if (item.isPdf) {
                    mediaHtml = `
                        <div class="w-full h-24 flex flex-col items-center justify-center bg-red-950/40 rounded-lg border border-red-900/50">
                            <i class="fas fa-file-pdf text-red-500 text-3xl mb-1.5 animate-pulse"></i>
                            <span class="text-[9px] font-bold text-red-400 uppercase tracking-wider">PDF DOCUMENT</span>
                        </div>
                    `;
                } else {
                    mediaHtml = `
                        <div class="relative group overflow-hidden rounded-lg border border-slate-800 bg-slate-950 h-24 flex items-center justify-center">
                            <img src="${item.dataUrl}" 
                                 alt="Preview ${index + 1}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-slate-950/45 group-hover:bg-slate-950/70 transition-all flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100">
                                <button type="button" onclick="openScannerModal(${index})"
                                        class="p-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-[11px] font-medium flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-magic"></i> Scan
                                </button>
                            </div>
                        </div>
                    `;
                    
                    actionHtml = `
                        <button type="button" onclick="openScannerModal(${index})"
                                class="flex-1 flex items-center justify-center gap-1.5 py-1 px-2 rounded-lg bg-indigo-950/60 border border-indigo-900/50 text-indigo-400 hover:text-indigo-300 hover:bg-indigo-900/40 text-[10px] font-semibold transition mt-2 cursor-pointer">
                            <i class="fas fa-magic"></i> Scan Dokumen
                        </button>
                    `;
                }
                
                previewDiv.innerHTML = `
                    <div class="relative">
                        ${mediaHtml}
                        <button type="button" 
                                onclick="removeImageItem(${index})"
                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs transition shadow-md remove-preview-btn border border-red-500 z-10 cursor-pointer font-bold"
                                title="Hapus file">
                            ×
                        </button>
                    </div>
                    <p class="text-[11px] font-medium text-slate-200 mt-2 truncate" title="${item.name}">${item.name}</p>
                    <p class="text-[10px] text-slate-500">${formatFileSize(item.size)}</p>
                    <div class="flex gap-1.5">
                        ${actionHtml}
                    </div>
                `;
                
                previewGrid.appendChild(previewDiv);
            });
        } else {
            previewContainer.classList.add('hidden');
        }
    }

    function removeImageItem(index) {
        processedImages.splice(index, 1);
        renderImagePreviews();
        syncFileInput();
    }

    function syncFileInput() {
        const fileInput = document.getElementById('gambar_surat_jalan');
        const dataTransfer = new DataTransfer();
        processedImages.forEach(item => {
            if (item.isPdf) {
                dataTransfer.items.add(item.file);
            } else if (item.isProcessed) {
                try {
                    const file = dataURLtoFile(item.dataUrl, item.name);
                    dataTransfer.items.add(file);
                } catch (err) {
                    console.error("Gagal convert gambar: ", err);
                    dataTransfer.items.add(item.file);
                }
            } else {
                dataTransfer.items.add(item.file);
            }
        });
        fileInput.files = dataTransfer.files;
    }

    function dataURLtoFile(dataurl, filename) {
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename.replace(/\.[^/.]+$/, "") + ".jpg", {type: 'image/jpeg'});
    }

    // Modal CamScanner Functions
    function openScannerModal(index) {
        activeImageIndex = index;
        const item = processedImages[index];
        if (!item || item.isPdf) return;
        
        const modal = document.getElementById('camscanner-modal');
        modal.classList.remove('hidden');
        
        const savedSettings = (item.isProcessed && item.settings) ? item.settings : null;
        const savedCrop = (item.isProcessed && item.crop) ? item.crop : null;
        
        loadScannerImage(item.originalDataUrl, savedSettings, savedCrop);
    }

    function closeScannerModal() {
        const modal = document.getElementById('camscanner-modal');
        modal.classList.add('hidden');
        activeImageIndex = null;
        originalImgElement = null;
    }

    function setScannerFilter(filterName) {
        currentSettings.filter = filterName;
        updateFilterUI();
        applyFilters();
    }

    function updateFilterUI() {
        document.querySelectorAll('.scanner-filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.getElementById('filter-' + currentSettings.filter);
        if (activeBtn) activeBtn.classList.add('active');
        
        const thresholdGroup = document.getElementById('threshold-slider-group');
        if (currentSettings.filter === 'bw') {
            thresholdGroup.classList.remove('hidden');
        } else {
            thresholdGroup.classList.add('hidden');
        }
    }

    function adjustScannerManual(type, value) {
        currentSettings[type] = value;
        if (type === 'brightness') {
            document.getElementById('val-brightness').innerText = (value > 0 ? '+' : '') + value + '%';
        } else if (type === 'contrast') {
            document.getElementById('val-contrast').innerText = (value > 0 ? '+' : '') + value + '%';
        } else if (type === 'threshold') {
            document.getElementById('val-threshold').innerText = value;
        }
        applyFilters();
    }

    function rotateScanner(degrees) {
        currentSettings.rotation = (currentSettings.rotation + degrees) % 360;
        if (currentSettings.rotation < 0) currentSettings.rotation += 360;
        applyFilters();
    }

    function autoCropDocument(isSilent = false) {
        if (!originalImgElement) return;
        
        try {
            const tempCanvas = document.createElement('canvas');
            const maxAnalysisSize = 300;
            const scale = Math.min(1, maxAnalysisSize / Math.max(originalImgElement.width, originalImgElement.height));
            tempCanvas.width = originalImgElement.width * scale;
            tempCanvas.height = originalImgElement.height * scale;
            
            const tempCtx = tempCanvas.getContext('2d');
            const rotation = currentSettings.rotation;
            const isSwapped = (rotation / 90) % 2 !== 0;
            
            const analysisCanvas = document.createElement('canvas');
            analysisCanvas.width = isSwapped ? tempCanvas.height : tempCanvas.width;
            analysisCanvas.height = isSwapped ? tempCanvas.width : tempCanvas.height;
            const analysisCtx = analysisCanvas.getContext('2d');
            
            analysisCtx.translate(analysisCanvas.width / 2, analysisCanvas.height / 2);
            analysisCtx.rotate((rotation * Math.PI) / 180);
            analysisCtx.drawImage(originalImgElement, -tempCanvas.width / 2, -tempCanvas.height / 2, tempCanvas.width, tempCanvas.height);
            
            const width = analysisCanvas.width;
            const height = analysisCanvas.height;
            const imgData = analysisCtx.getImageData(0, 0, width, height);
            const data = imgData.data;
            
            const grayData = new Uint8Array(width * height);
            let minVal = 255;
            let maxVal = 0;
            
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i+1];
                const b = data[i+2];
                const gray = Math.round(0.299 * r + 0.587 * g + 0.114 * b);
                grayData[i/4] = gray;
                if (gray < minVal) minVal = gray;
                if (gray > maxVal) maxVal = gray;
            }
            
            let finalCrop = { x: 10, y: 10, w: 80, h: 80 };
            
            if (maxVal - minVal > 40) {
                const threshold = minVal + (maxVal - minVal) * 0.38;
                
                const borderX = Math.max(1, Math.floor(width * 0.03));
                const borderY = Math.max(1, Math.floor(height * 0.03));
                
                let minX = width;
                let maxX = 0;
                let minY = height;
                let maxY = 0;
                
                for (let y = borderY; y < height - borderY; y++) {
                    for (let x = borderX; x < width - borderX; x++) {
                        const idx = y * width + x;
                        if (grayData[idx] >= threshold) {
                            if (x < minX) minX = x;
                            if (x > maxX) maxX = x;
                            if (y < minY) minY = y;
                            if (y > maxY) maxY = y;
                        }
                    }
                }
                
                if (maxX > minX && maxY > minY && (maxX - minX) > width * 0.2 && (maxY - minY) > height * 0.2) {
                    const padX = Math.floor((maxX - minX) * 0.02);
                    const padY = Math.floor((maxY - minY) * 0.02);
                    
                    const left = Math.max(0, minX - padX);
                    const right = Math.min(width - 1, maxX + padX);
                    const top = Math.max(0, minY - padY);
                    const bottom = Math.min(height - 1, maxY + padY);
                    
                    finalCrop = {
                        x: Math.round((left / width) * 100),
                        y: Math.round((top / height) * 100),
                        w: Math.round(((right - left) / width) * 100),
                        h: Math.round(((bottom - top) / height) * 100)
                    };
                }
            }
            
            cropBoxPercent = finalCrop;
            
            const cropBox = document.getElementById('crop-box');
            if (cropBox) {
                cropBox.style.left = cropBoxPercent.x + '%';
                cropBox.style.top = cropBoxPercent.y + '%';
                cropBox.style.width = cropBoxPercent.w + '%';
                cropBox.style.height = cropBoxPercent.h + '%';
            }
            
            if (!isCropperActive) {
                toggleCropper();
            } else {
                alignOverlayWithCanvas();
            }
            
        } catch (error) {
            console.error("Gagal melakukan autocrop:", error);
            if (!isSilent) {
                alert("Gagal mendeteksi dokumen secara otomatis.");
            }
        }
    }

    function toggleCropper() {
        isCropperActive = !isCropperActive;
        const btn = document.getElementById('cropper-toggle-btn');
        const text = document.getElementById('cropper-btn-text');
        const overlay = document.getElementById('crop-overlay');
        
        if (isCropperActive) {
            overlay.classList.remove('hidden');
            alignOverlayWithCanvas();
            text.innerText = 'Matikan Pangkas (Batal)';
            btn.classList.add('bg-indigo-950', 'border-indigo-500', 'text-indigo-400');
        } else {
            overlay.classList.add('hidden');
            text.innerText = 'Pangkas Manual';
            btn.classList.remove('bg-indigo-950', 'border-indigo-500', 'text-indigo-400');
        }
    }

    function alignOverlayWithCanvas() {
        const canvas = document.getElementById('scanner-canvas');
        const overlay = document.getElementById('crop-overlay');
        if (!canvas || !overlay) return;
        
        overlay.style.width = canvas.offsetWidth + 'px';
        overlay.style.height = canvas.offsetHeight + 'px';
        overlay.style.top = canvas.offsetTop + 'px';
        overlay.style.left = canvas.offsetLeft + 'px';
    }

    function loadScannerImage(dataUrl, savedSettings = null, savedCrop = null) {
        document.getElementById('scanner-loader').classList.remove('hidden');
        originalImgElement = new Image();
        originalImgElement.onload = function() {
            document.getElementById('scanner-loader').classList.add('hidden');
            
            if (savedSettings) {
                currentSettings = { ...savedSettings };
                
                document.getElementById('adjust-brightness').value = currentSettings.brightness;
                document.getElementById('val-brightness').innerText = (currentSettings.brightness > 0 ? '+' : '') + currentSettings.brightness + '%';
                document.getElementById('adjust-contrast').value = currentSettings.contrast;
                document.getElementById('val-contrast').innerText = (currentSettings.contrast > 0 ? '+' : '') + currentSettings.contrast + '%';
                document.getElementById('adjust-threshold').value = currentSettings.threshold;
                document.getElementById('val-threshold').innerText = currentSettings.threshold;
            } else {
                currentSettings = {
                    filter: 'original',
                    rotation: 0,
                    brightness: 0,
                    contrast: 0,
                    threshold: 120
                };
                
                document.getElementById('adjust-brightness').value = 0;
                document.getElementById('val-brightness').innerText = '0%';
                document.getElementById('adjust-contrast').value = 0;
                document.getElementById('val-contrast').innerText = '0%';
                document.getElementById('adjust-threshold').value = 120;
                document.getElementById('val-threshold').innerText = '120';
            }
            
            if (savedCrop) {
                cropBoxPercent = { ...savedCrop };
                const cropBox = document.getElementById('crop-box');
                cropBox.style.left = cropBoxPercent.x + '%';
                cropBox.style.top = cropBoxPercent.y + '%';
                cropBox.style.width = cropBoxPercent.w + '%';
                cropBox.style.height = cropBoxPercent.h + '%';
                
                if (!isCropperActive) toggleCropper();
            } else {
                cropBoxPercent = { x: 10, y: 10, w: 80, h: 80 };
                const cropBox = document.getElementById('crop-box');
                cropBox.style.left = '10%';
                cropBox.style.top = '10%';
                cropBox.style.width = '80%';
                cropBox.style.height = '80%';
                
                if (isCropperActive) toggleCropper();
                
                setTimeout(function() {
                    autoCropDocument(true);
                }, 100);
            }
            
            updateFilterUI();
            applyFilters();
        };
        originalImgElement.src = dataUrl;
    }

    function applyFilters() {
        if (!originalImgElement) return;
        const canvas = document.getElementById('scanner-canvas');
        const ctx = canvas.getContext('2d');
        
        const rotation = currentSettings.rotation;
        const isSwapped = (rotation / 90) % 2 !== 0;
        const targetWidth = isSwapped ? originalImgElement.height : originalImgElement.width;
        const targetHeight = isSwapped ? originalImgElement.width : originalImgElement.height;
        
        canvas.width = targetWidth;
        canvas.height = targetHeight;
        
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.save();
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate((rotation * Math.PI) / 180);
        ctx.drawImage(originalImgElement, -originalImgElement.width / 2, -originalImgElement.height / 2);
        ctx.restore();
        
        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imgData.data;
        
        const filter = currentSettings.filter;
        const brightness = parseInt(currentSettings.brightness);
        const contrast = parseInt(currentSettings.contrast);
        const threshold = parseInt(currentSettings.threshold);
        const contrastFactor = (259 * (contrast + 255)) / (255 * (259 - contrast));
        
        for (let i = 0; i < data.length; i += 4) {
            let r = data[i];
            let g = data[i+1];
            let b = data[i+2];
            
            if (filter === 'grayscale') {
                const gray = 0.2126 * r + 0.7152 * g + 0.0722 * b;
                r = g = b = gray;
            } else if (filter === 'magic') {
                let gray = 0.2126 * r + 0.7152 * g + 0.0722 * b;
                if (gray > 130) {
                    gray = Math.min(255, gray + (255 - gray) * 0.55);
                } else {
                    gray = Math.max(0, gray - (gray * 0.25));
                }
                gray = (gray - 35) * (255 / 185);
                r = g = b = Math.min(255, Math.max(0, gray));
            } else if (filter === 'bw') {
                const gray = 0.299 * r + 0.587 * g + 0.114 * b;
                const val = gray > threshold ? 255 : 0;
                r = g = b = val;
            }
            
            if (filter !== 'bw') {
                r = contrastFactor * (r - 128) + 128 + brightness;
                g = contrastFactor * (g - 128) + 128 + brightness;
                b = contrastFactor * (b - 128) + 128 + brightness;
            } else {
                r = Math.min(255, Math.max(0, r + brightness));
                g = Math.min(255, Math.max(0, g + brightness));
                b = Math.min(255, Math.max(0, b + brightness));
            }
            
            data[i]   = Math.min(255, Math.max(0, r));
            data[i+1] = Math.min(255, Math.max(0, g));
            data[i+2] = Math.min(255, Math.max(0, b));
        }
        
        ctx.putImageData(imgData, 0, 0);
        setTimeout(alignOverlayWithCanvas, 50);
    }

    function saveScannerResult() {
        if (activeImageIndex === null || !originalImgElement) return;
        const canvas = document.getElementById('scanner-canvas');
        let finalDataUrl = '';
        
        processedImages[activeImageIndex].settings = { ...currentSettings };
        
        if (isCropperActive) {
            const cropX = (cropBoxPercent.x / 100) * canvas.width;
            const cropY = (cropBoxPercent.y / 100) * canvas.height;
            const cropW = (cropBoxPercent.w / 100) * canvas.width;
            const cropH = (cropBoxPercent.h / 100) * canvas.height;
            
            processedImages[activeImageIndex].crop = { ...cropBoxPercent };
            
            const cropCanvas = document.createElement('canvas');
            cropCanvas.width = cropW;
            cropCanvas.height = cropH;
            
            const cropCtx = cropCanvas.getContext('2d');
            cropCtx.drawImage(canvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);
            
            finalDataUrl = cropCanvas.toDataURL('image/jpeg', 0.9);
        } else {
            processedImages[activeImageIndex].crop = null;
            finalDataUrl = canvas.toDataURL('image/jpeg', 0.9);
        }
        
        processedImages[activeImageIndex].dataUrl = finalDataUrl;
        processedImages[activeImageIndex].isProcessed = true;
        
        closeScannerModal();
        renderImagePreviews();
        syncFileInput();
    }

    function initCropperDrag() {
        const cropBox = document.getElementById('crop-box');
        const overlay = document.getElementById('crop-overlay');
        if (!cropBox || !overlay) return;
        
        cropBox.addEventListener('mousedown', function(e) {
            if (e.target.dataset.handle) {
                isResizingBox = true;
                activeHandle = e.target.dataset.handle;
            } else {
                isDraggingBox = true;
            }
            dragStartCoords = { x: e.clientX, y: e.clientY };
            
            const parentRect = overlay.getBoundingClientRect();
            const boxRect = cropBox.getBoundingClientRect();
            cropBoxStartCoords = {
                x: boxRect.left - parentRect.left,
                y: boxRect.top - parentRect.top,
                w: boxRect.width,
                h: boxRect.height
            };
            
            e.stopPropagation();
            e.preventDefault();
        });

        cropBox.addEventListener('touchstart', function(e) {
            const touch = e.touches[0];
            if (e.target.dataset.handle) {
                isResizingBox = true;
                activeHandle = e.target.dataset.handle;
            } else {
                isDraggingBox = true;
            }
            dragStartCoords = { x: touch.clientX, y: touch.clientY };
            
            const parentRect = overlay.getBoundingClientRect();
            const boxRect = cropBox.getBoundingClientRect();
            cropBoxStartCoords = {
                x: boxRect.left - parentRect.left,
                y: boxRect.top - parentRect.top,
                w: boxRect.width,
                h: boxRect.height
            };
            
            e.stopPropagation();
        });

        document.addEventListener('mousemove', handleDragMove);
        document.addEventListener('touchmove', handleDragMove, { passive: false });
        document.addEventListener('mouseup', endDrag);
        document.addEventListener('touchend', endDrag);
        
        function handleDragMove(e) {
            if (!isDraggingBox && !isResizingBox) return;
            
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            
            const dx = clientX - dragStartCoords.x;
            const dy = clientY - dragStartCoords.y;
            
            const parentRect = overlay.getBoundingClientRect();
            const pW = parentRect.width;
            const pH = parentRect.height;
            if (pW === 0 || pH === 0) return;
            
            let newX = cropBoxStartCoords.x;
            let newY = cropBoxStartCoords.y;
            let newW = cropBoxStartCoords.w;
            let newH = cropBoxStartCoords.h;
            
            if (isDraggingBox) {
                newX = cropBoxStartCoords.x + dx;
                newY = cropBoxStartCoords.y + dy;
                
                newX = Math.max(0, Math.min(newX, pW - newW));
                newY = Math.max(0, Math.min(newY, pH - newH));
            } else if (isResizingBox) {
                const minSize = 25;
                
                switch (activeHandle) {
                    case 'nw':
                        newX = cropBoxStartCoords.x + dx;
                        newY = cropBoxStartCoords.y + dy;
                        newW = cropBoxStartCoords.w - dx;
                        newH = cropBoxStartCoords.h - dy;
                        break;
                    case 'ne':
                        newY = cropBoxStartCoords.y + dy;
                        newW = cropBoxStartCoords.w + dx;
                        newH = cropBoxStartCoords.h - dy;
                        break;
                    case 'sw':
                        newX = cropBoxStartCoords.x + dx;
                        newW = cropBoxStartCoords.w - dx;
                        newH = cropBoxStartCoords.h + dy;
                        break;
                    case 'se':
                        newW = cropBoxStartCoords.w + dx;
                        newH = cropBoxStartCoords.h + dy;
                        break;
                    case 'n':
                        newY = cropBoxStartCoords.y + dy;
                        newH = cropBoxStartCoords.h - dy;
                        break;
                    case 's':
                        newH = cropBoxStartCoords.h + dy;
                        break;
                    case 'w':
                        newX = cropBoxStartCoords.x + dx;
                        newW = cropBoxStartCoords.w - dx;
                        break;
                    case 'e':
                        newW = cropBoxStartCoords.w + dx;
                        break;
                }
                
                if (newW < minSize) {
                    if (activeHandle.includes('w')) newX = cropBoxStartCoords.x + cropBoxStartCoords.w - minSize;
                    newW = minSize;
                }
                if (newH < minSize) {
                    if (activeHandle.includes('n')) newY = cropBoxStartCoords.y + cropBoxStartCoords.h - minSize;
                    newH = minSize;
                }
                
                if (newX < 0) { newW += newX; newX = 0; }
                if (newY < 0) { newH += newY; newY = 0; }
                if (newX + newW > pW) newW = pW - newX;
                if (newY + newH > pH) newH = pH - newY;
            }
            
            cropBoxPercent.x = (newX / pW) * 100;
            cropBoxPercent.y = (newY / pH) * 100;
            cropBoxPercent.w = (newW / pW) * 100;
            cropBoxPercent.h = (newH / pH) * 100;
            
            cropBox.style.left = cropBoxPercent.x + '%';
            cropBox.style.top = cropBoxPercent.y + '%';
            cropBox.style.width = cropBoxPercent.w + '%';
            cropBox.style.height = cropBoxPercent.h + '%';
            
            if (e.cancelable) e.preventDefault();
        }
        
        function endDrag() {
            isDraggingBox = false;
            isResizingBox = false;
            activeHandle = null;
        }
    }

    // Format file size for display
    function formatFileSize(bytes) {
        if (bytes === 0 || !bytes) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Drag and drop functionality
    document.addEventListener('DOMContentLoaded', function() {
        initCropperDrag();
        
        // Handle manual nomor kontainer submission (optional)
        const createLclForm = document.querySelector('form');
        if (createLclForm) {
            createLclForm.addEventListener('submit', function(e) {
                const hiddenInput = document.getElementById('nomor_kontainer');
                const manualField = document.getElementById('nomor_kontainer_manual');
                if (hiddenInput && hiddenInput.value === '__manual__') {
                    hiddenInput.value = manualField && manualField.value.trim() ? manualField.value.trim() : '';
                }
            });
        }

        const dropZone = document.querySelector('.upload-dropzone');
        const fileInput = document.getElementById('gambar_surat_jalan');
        
        if (dropZone && fileInput) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });
            
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
                const validFiles = [];
                const maxSize = 10 * 1024 * 1024;
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                
                for (let i = 0; i < Math.min(files.length, 5); i++) {
                    const file = files[i];
                    if (!allowedTypes.includes(file.type)) {
                        alert(`File ${file.name} bukan format gambar yang diizinkan. Gunakan JPG, PNG, GIF, atau WEBP.`);
                        continue;
                    }
                    if (file.size > maxSize) {
                        alert(`File ${file.name} terlalu besar. Maksimal 10MB per file.`);
                        continue;
                    }
                    validFiles.push(file);
                }
                
                if (validFiles.length > 0) {
                    const dataTransfer = new DataTransfer();
                    validFiles.forEach(file => dataTransfer.items.add(file));
                    fileInput.files = dataTransfer.files;
                    previewImages(fileInput);
                }
            }
        }

        // Initialize existing rows for ukuran visibility
        document.querySelectorAll('.nama-barang-input').forEach(input => {
            toggleUkuranField(input);
        });
    });

    /**
     * Toggles the visibility of the "Ukuran" field based on the "Nama Barang" input value.
     * Only shows if the value contains "keramik" (case-insensitive).
     */
    function toggleUkuranField(input) {
        const row = input.closest('.dimensi-row') || input.closest('.dimensi-row-new') || input.closest('.dimensi-row-edit');
        if (!row) return;

        const ukuranContainer = row.querySelector('.ukuran-container');
        const gridContainer = row.querySelector('.dimensi-info-grid');
        const value = input.value.toLowerCase();
        
        if (value.includes('keramik')) {
            if (ukuranContainer) {
                ukuranContainer.classList.remove('hidden');
            }
            if (gridContainer) {
                gridContainer.classList.remove('md:grid-cols-3');
                gridContainer.classList.add('md:grid-cols-4');
            }
        } else {
            if (ukuranContainer) {
                ukuranContainer.classList.add('hidden');
            }
            if (gridContainer) {
                gridContainer.classList.remove('md:grid-cols-4');
                gridContainer.classList.add('md:grid-cols-3');
            }
        }
    }
</script>
@endpush