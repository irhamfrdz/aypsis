@extends('layouts.app')

@section('title', 'Edit Tanda Terima LCL')
@section('page_title', 'Edit Tanda Terima LCL')

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
                <h1 class="text-xl font-semibold text-gray-900">Edit Tanda Terima LCL</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-600">{{ $tandaTerima->nomor_tanda_terima }}</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Tipe: LCL
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('tanda-terima-lcl.show', $tandaTerima) }}"
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

            <form action="{{ route('tanda-terima-lcl.update', $tandaTerima) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <!-- Hidden field for tipe_kontainer -->
                <input type="hidden" name="tipe_kontainer" value="lcl">
                
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
                                   value="{{ old('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima) }}"
                                   placeholder="TTR-LCL-001">
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Nomor tanda terima (opsional)
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
                                   value="{{ old('tanggal_tanda_terima', $tandaTerima->tanggal_tanda_terima->format('Y-m-d')) }}" required>
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
                                   value="{{ old('no_surat_jalan_customer', $tandaTerima->no_surat_jalan_customer) }}"
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
                                       placeholder="Cari term..." autocomplete="off" required
                                       value="{{ old('term_id') ? App\Models\Term::find(old('term_id'))->nama_status ?? '' : ($tandaTerima->term ? $tandaTerima->term->nama_status : '') }}">
                                <div id="termDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    @foreach(App\Models\Term::all() as $term)
                                        <div class="term-option px-3 py-2 hover:bg-green-50 cursor-pointer text-sm border-b border-gray-100"
                                             data-value="{{ $term->id }}" data-text="{{ $term->nama_status }}">
                                            {{ $term->nama_status }}
                                        </div>
                                    @endforeach
                                </div>
                                <select name="term_id" id="term_id" class="hidden">
                                    <option value="">Pilih Term</option>
                                    @foreach(App\Models\Term::all() as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id', $tandaTerima->term_id) == $term->id ? 'selected' : '' }}>
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
                                                                {{ old('nama_penerima', $tandaTerima->nama_penerima) == $item->nama ? 'selected' : '' }}>
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
                                               value="{{ old('pic_penerima', $tandaTerima->pic_penerima) }}"
                                               placeholder="Nama PIC Penerima">
                                    </div>

                                    <!-- Telepon Penerima -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Telepon Penerima
                                        </label>
                                        <input type="text" name="telepon_penerima"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               value="{{ old('telepon_penerima', $tandaTerima->telepon_penerima) }}"
                                               placeholder="08123456789">
                                    </div>

                                    <!-- Alamat Penerima -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Alamat Penerima <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="alamat_penerima" rows="2" required
                                                  class="penerima-alamat w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                  placeholder="Alamat lengkap penerima...">{{ old('alamat_penerima', $tandaTerima->alamat_penerima) }}</textarea>
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
                                                                {{ old('nama_pengirim', $tandaTerima->nama_pengirim) == $item->nama ? 'selected' : '' }}>
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
                                               value="{{ old('pic_pengirim', $tandaTerima->pic_pengirim) }}"
                                               placeholder="Nama PIC Pengirim">
                                    </div>

                                    <!-- Telepon Pengirim -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Telepon Pengirim
                                        </label>
                                        <input type="text" name="telepon_pengirim"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               value="{{ old('telepon_pengirim', $tandaTerima->telepon_pengirim) }}"
                                               placeholder="08123456789">
                                    </div>

                                    <!-- Alamat Pengirim -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Alamat Pengirim <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="alamat_pengirim" rows="2" required
                                                  class="pengirim-alamat w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                  placeholder="Alamat lengkap pengirim...">{{ old('alamat_pengirim', $tandaTerima->alamat_pengirim) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Barang Section -->
                    <div class="mt-4">
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_barang" id="nama_barang" 
                               value="{{ old('nama_barang', $tandaTerima->nama_barang) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Masukkan nama barang" required>
                        @error('nama_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        @if($tandaTerima->items && $tandaTerima->items->count() > 0)
                            @foreach($tandaTerima->items as $index => $item)
                                <div class="dimensi-row mb-4 pb-4 border-b border-purple-200">
                                    <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label for="nama_barang_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Nama Barang
                                            </label>
                                            <input type="text"
                                                   name="nama_barang[]" 
                                                   id="nama_barang_{{ $index }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="Nama barang"
                                                   value="{{ old('nama_barang.'.$index, $item->nama_barang) }}">
                                        </div>
                                        <div>
                                            <label for="jumlah_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Jumlah
                                            </label>
                                            <input type="number"
                                                   name="jumlah[]"
                                                   id="jumlah_{{ $index }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="0"
                                                   value="{{ old('jumlah.'.$index, $item->jumlah) }}"
                                                   min="0"
                                                   step="1">
                                        </div>
                                        <div>
                                            <label for="satuan_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Satuan
                                            </label>
                                            <input type="text"
                                                   name="satuan[]"
                                                   id="satuan_{{ $index }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="Pcs, Kg, Box"
                                                   value="{{ old('satuan.'.$index, $item->satuan) }}">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div>
                                            <label for="panjang_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Panjang (m)
                                            </label>
                                            <input type="number"
                                                   name="panjang[]"
                                                   id="panjang_{{ $index }}"
                                                   class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="0.000"
                                                   value="{{ old('panjang.'.$index, $item->panjang) }}"
                                                   min="0"
                                                   step="0.001"
                                                   onchange="calculateVolume(this.closest('.dimensi-row'))">
                                        </div>
                                        <div>
                                            <label for="lebar_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Lebar (m)
                                            </label>
                                            <input type="number"
                                                   name="lebar[]"
                                                   id="lebar_{{ $index }}"
                                                   class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="0.000"
                                                   value="{{ old('lebar.'.$index, $item->lebar) }}"
                                                   min="0"
                                                   step="0.001"
                                                   onchange="calculateVolume(this.closest('.dimensi-row'))">
                                        </div>
                                        <div>
                                            <label for="tinggi_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Tinggi (m)
                                            </label>
                                            <input type="number"
                                                   name="tinggi[]"
                                                   id="tinggi_{{ $index }}"
                                                   class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="0.000"
                                                   value="{{ old('tinggi.'.$index, $item->tinggi) }}"
                                                   min="0"
                                                   step="0.001"
                                                   onchange="calculateVolume(this.closest('.dimensi-row'))">
                                        </div>
                                        <div>
                                            <label for="meter_kubik_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Volume (m³)
                                            </label>
                                            <input type="number"
                                                   name="meter_kubik[]"
                                                   id="meter_kubik_{{ $index }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm"
                                                   placeholder="0.000"
                                                   value="{{ old('meter_kubik.'.$index, $item->meter_kubik) }}"
                                                   min="0"
                                                   step="0.001"
                                                   readonly>
                                        </div>
                                        <div>
                                            <label for="tonase_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Tonase (Ton)
                                            </label>
                                            <input type="number"
                                                   name="tonase[]"
                                                   id="tonase_{{ $index }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="0.000"
                                                   value="{{ old('tonase.'.$index, $item->tonase) }}"
                                                   min="0"
                                                   step="0.001">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Volume akan dihitung otomatis dari panjang × lebar × tinggi
                                    </p>
                                </div>
                            @endforeach
                        @else
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
                        @endif
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
                                       value="{{ old('supir', $tandaTerima->supir ?? 'Supir Customer') }}">
                                <div id="supirDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    @if(isset($supirs) && count($supirs) > 0)
                                        @foreach($supirs as $supir)
                                            <div class="supir-option px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100"
                                                 data-value="{{ $supir->nama_supir }}" 
                                                 data-text="{{ $supir->nama_supir }}"
                                                 data-plat="{{ $supir->no_plat }}">
                                                <div class="font-medium">{{ $supir->nama_supir }}</div>
                                                <div class="text-xs text-gray-500">{{ $supir->no_plat }}</div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="hidden" name="supir" id="supir" required value="{{ old('supir', $tandaTerima->supir ?? 'Supir Customer') }}">
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
                                   value="{{ old('no_plat', $tandaTerima->no_plat ?? 'Plat Customer') }}" required
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
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                       value="{{ old('nomor_kontainer', $tandaTerima->nomor_kontainer) != '__manual__' ? old('nomor_kontainer', $tandaTerima->nomor_kontainer) : '' }}">
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
                                <input type="hidden" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer', $tandaTerima->nomor_kontainer) }}">
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
                                <option value="20ft" {{ old('size_kontainer', $tandaTerima->size_kontainer) == '20ft' ? 'selected' : '' }}>20 Feet</option>
                                <option value="40ft" {{ old('size_kontainer', $tandaTerima->size_kontainer) == '40ft' ? 'selected' : '' }}>40 Feet</option>
                                <option value="40hc" {{ old('size_kontainer', $tandaTerima->size_kontainer) == '40hc' ? 'selected' : '' }}>40 Feet High Cube</option>
                                <option value="45ft" {{ old('size_kontainer', $tandaTerima->size_kontainer) == '45ft' ? 'selected' : '' }}>45 Feet</option>
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
                                   value="{{ old('nomor_seal', $tandaTerima->nomor_seal) }}"
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
                                <option value="HC" {{ old('tipe_kontainer', $tandaTerima->tipe_kontainer) == 'HC' ? 'selected' : '' }}>HC (High Cube)</option>
                                <option value="STD" {{ old('tipe_kontainer', $tandaTerima->tipe_kontainer) == 'STD' ? 'selected' : '' }}>STD (Standard)</option>
                                <option value="RF" {{ old('tipe_kontainer', $tandaTerima->tipe_kontainer) == 'RF' ? 'selected' : '' }}>RF (Reefer)</option>
                                <option value="OT" {{ old('tipe_kontainer', $tandaTerima->tipe_kontainer) == 'OT' ? 'selected' : '' }}>OT (Open Top)</option>
                                <option value="FR" {{ old('tipe_kontainer', $tandaTerima->tipe_kontainer) == 'FR' ? 'selected' : '' }}>FR (Flat Rack)</option>
                                <option value="Dry Container" {{ old('tipe_kontainer', $tandaTerima->tipe_kontainer) == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
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
                        <label for="master_tujuan_kirim_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Pilih Tujuan <span class="text-red-500">*</span>
                        </label>
                        <select name="master_tujuan_kirim_id" id="master_tujuan_kirim_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Pilih Tujuan Pengiriman</option>
                            @foreach(App\Models\MasterTujuanKirim::all() as $tujuan)
                                <option value="{{ $tujuan->id }}" {{ old('master_tujuan_kirim_id', $tandaTerima->tujuan_pengiriman_id) == $tujuan->id ? 'selected' : '' }}>
                                    {{ $tujuan->nama_tujuan }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih tujuan pengiriman barang</p>
                        @error('master_tujuan_kirim_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dimensi dan Volume -->
                @if($tandaTerima->items && $tandaTerima->items->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Dimensi dan Volume</h2>
                        </div>
                        <div class="p-6">
                            <div id="dimensi-container">
                                @foreach($tandaTerima->items as $index => $item)
                                    <div class="dimensi-row border border-gray-200 rounded-lg p-4 mb-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-md font-medium text-gray-700">Item {{ $index + 1 }}</h3>
                                            @if($loop->count > 1)
                                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeDimensiRow(this)">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Panjang (m)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][panjang]" 
                                                       value="{{ old('items.'.$index.'.panjang', $item->panjang) }}"
                                                       step="0.01" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                                       onchange="calculateVolume(this)">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (m)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][lebar]" 
                                                       value="{{ old('items.'.$index.'.lebar', $item->lebar) }}"
                                                       step="0.01" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                                       onchange="calculateVolume(this)">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (m)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][tinggi]" 
                                                       value="{{ old('items.'.$index.'.tinggi', $item->tinggi) }}"
                                                       step="0.01" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                                       onchange="calculateVolume(this)">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Volume (m³)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][meter_kubik]" 
                                                       value="{{ old('items.'.$index.'.meter_kubik', $item->meter_kubik) }}"
                                                       step="0.001" 
                                                       class="volume-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                                       readonly>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Berat (Ton)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][tonase]" 
                                                       value="{{ old('items.'.$index.'.tonase', $item->tonase) }}"
                                                       step="0.01" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" 
                                    onclick="addDimensiRow()" 
                                    class="inline-flex items-center px-4 py-2 border border-green-300 rounded-md text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Item
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Upload Gambar Surat Jalan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Upload Gambar Surat Jalan
                        </h2>
                    </div>
                    <div class="p-6">
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

                        <!-- Preview Area for New Images -->
                        <div id="image-preview-container" class="mt-4 hidden">
                            <label class="block text-xs font-medium text-gray-500 mb-2">
                                <i class="fas fa-eye mr-1 text-orange-600"></i>
                                Preview Gambar Baru
                            </label>
                            <div id="image-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                {{-- Preview images will be inserted here by JavaScript --}}
                            </div>
                        </div>

                        <!-- Existing Images -->
                        @php
                            $existingImages = $tandaTerima->gambar_surat_jalan;
                            if (is_string($existingImages)) {
                                $existingImages = json_decode($existingImages, true) ?? [];
                            }
                            if (!is_array($existingImages)) {
                                $existingImages = [];
                            }
                        @endphp

                        @if(!empty($existingImages))
                            <div class="mt-4">
                                <label class="block text-xs font-medium text-gray-500 mb-2">
                                    <i class="fas fa-images mr-1 text-green-600"></i>
                                    Gambar Yang Sudah Ada
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach($existingImages as $index => $imagePath)
                                        @php $imgUrl = asset('storage/' . ltrim($imagePath, '/')); @endphp
                                        <div class="relative bg-gray-50 rounded-lg border border-gray-200 p-2 existing-image-item" data-path="{{ $imagePath }}">
                                            <img src="{{ $imgUrl }}" alt="Gambar {{ $index + 1 }}" class="object-cover w-full h-28 rounded" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect fill=%27%23ddd%27 width=%27100%27 height=%27100%27/%3E%3Ctext fill=%27%23999%27 x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27%3EGambar tidak ditemukan%3C/text%3E%3C/svg%3E';"/>
                                            <div class="flex justify-between items-center mt-2">
                                                <div class="text-xs text-gray-600 truncate">Gambar {{ $index + 1 }}</div>
                                                <button type="button" onclick="removeExistingImage(this, '{{ $imagePath }}')" class="text-red-500 hover:text-red-700 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <input type="hidden" name="existing_images[]" value="{{ $imagePath }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('tanda-terima-lcl.show', $tandaTerima) }}" 
                               class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleKontainerFields() {
    const cargo = document.querySelector('input[name="tipe_kontainer"]:checked').value === 'cargo';
    const kontainerFields = document.getElementById('kontainer-fields');
    
    if (cargo) {
        kontainerFields.style.display = 'none';
        // Clear kontainer fields when cargo is selected
        document.getElementById('nomor_kontainer').value = '';
        document.getElementById('size_kontainer').value = '';
    } else {
        kontainerFields.style.display = 'grid';
    }
}
    // Initialize nomor kontainer dropdown
    initializeNomorKontainerDropdown();

function setSizeKontainerValue(size) {
    const sizeSelect = document.getElementById('size_kontainer');
    if (!sizeSelect) return;
    // Normalize for LCL formats
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
        if (!size) { opt.selected = false; continue; }
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
// Ensure manual value is submitted if manual option chosen (edit-lcl)
const editLclForm = document.querySelector('form');
if (editLclForm) {
    editLclForm.addEventListener('submit', function(e) {
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

function calculateVolume(element) {
    const row = element.closest('.dimensi-row');
    const panjang = parseFloat(row.querySelector('input[name*="[panjang]"]').value) || 0;
    const lebar = parseFloat(row.querySelector('input[name*="[lebar]"]').value) || 0;
    const tinggi = parseFloat(row.querySelector('input[name*="[tinggi]"]').value) || 0;
    
    // Calculate volume in cubic meters (m × m × m = m³)
    const volume = panjang * lebar * tinggi;
    
    const volumeInput = row.querySelector('.volume-input');
    volumeInput.value = formatVolumeForDatabase(volume);
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
    }let itemIndex = {{ $tandaTerima->items ? $tandaTerima->items->count() : 0 }};

function addDimensiRow() {
    const container = document.getElementById('dimensi-container');
    const newRow = document.createElement('div');
    newRow.className = 'dimensi-row border border-gray-200 rounded-lg p-4 mb-4';
    newRow.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-md font-medium text-gray-700">Item ${itemIndex + 1}</h3>
            <button type="button" class="text-red-600 hover:text-red-800" onclick="removeDimensiRow(this)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Panjang (m)</label>
                <input type="number" name="items[${itemIndex}][panjang]" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                       onchange="calculateVolume(this)">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (m)</label>
                <input type="number" name="items[${itemIndex}][lebar]" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                       onchange="calculateVolume(this)">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (m)</label>
                <input type="number" name="items[${itemIndex}][tinggi]" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                       onchange="calculateVolume(this)">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Volume (m³)</label>
                <input type="text" name="items[${itemIndex}][meter_kubik]" 
                       class="volume-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                       readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berat (Ton)</label>
                <input type="number" name="items[${itemIndex}][tonase]" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
    itemIndex++;
}

function removeDimensiRow(button) {
    button.closest('.dimensi-row').remove();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleKontainerFields();
    
    // Format input values on blur for smart decimal display
    document.addEventListener('blur', function(e) {
        if (e.target.matches('input[name*="[panjang]"], input[name*="[lebar]"], input[name*="[tinggi]"], input[name*="[tonase]"]')) {
            const value = parseFloat(e.target.value);
            if (!isNaN(value) && value > 0) {
                if (e.target.matches('input[name*="[tonase]"]')) {
                    e.target.value = formatWeight(value);
                } else {
                    e.target.value = formatVolume(value);
                }
            }
        }
    }, true);

    // Drag and drop support
    const dropZone = document.querySelector('.upload-dropzone');
    const fileInput = document.getElementById('gambar_surat_jalan');
    
    if (dropZone && fileInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
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

// Image Upload Functions
function previewImages(input) {
    const previewContainer = document.getElementById('image-preview-container');
    const previewGrid = document.getElementById('image-preview-grid');
    
    if (input.files && input.files.length > 0) {
        previewContainer.classList.remove('hidden');
        
        // Clear previous previews
        previewGrid.innerHTML = '';
        
        const maxFiles = 5;
        const existingCount = document.querySelectorAll('.existing-image-item').length;
        const availableSlots = maxFiles - existingCount;
        
        if (availableSlots <= 0) {
            alert('Maksimal 5 gambar. Hapus beberapa gambar yang sudah ada terlebih dahulu.');
            input.value = '';
            previewContainer.classList.add('hidden');
            return;
        }
        
        const filesToProcess = Math.min(input.files.length, availableSlots);
        
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
        
        if (input.files.length > availableSlots) {
            alert(`Maksimal ${maxFiles} gambar total. Tersisa ${availableSlots} slot. Hanya ${availableSlots} file pertama yang akan diupload.`);
        }
    } else {
        if (previewGrid.children.length === 0) {
            previewContainer.classList.add('hidden');
        }
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

function removeExistingImage(button, path) {
    const imageItem = button.closest('.existing-image-item');
    if (imageItem) {
        imageItem.remove();
    }
    
    // Remove from existing_images array by removing the hidden input
    const hiddenInputs = document.querySelectorAll('input[name="existing_images[]"]');
    hiddenInputs.forEach(input => {
        if (input.value === path) {
            input.remove();
        }
    });
    
    // Add to removal list
    const form = document.querySelector('form');
    if (form) {
        const removeInput = document.createElement('input');
        removeInput.type = 'hidden';
        removeInput.name = 'hapus_gambar[]';
        removeInput.value = path;
        form.appendChild(removeInput);
    }
}

</script>

@push('scripts')
<!-- Select2 JS - jQuery already loaded in layout -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Global flag to track Select2 readiness
    window.select2Ready = false;
    
    // Wrap everything in jQuery ready to ensure DOM and libraries are loaded
    jQuery(document).ready(function($) {
        console.log('✓ jQuery loaded, version:', $.fn.jquery);
        
        // Initialize supir dropdown (non-Select2)
        initializeSupirDropdown();
        
        // Wait for Select2 to be fully loaded with retry mechanism
        function waitForSelect2(callback, attempts) {
            attempts = attempts || 0;
            if (attempts > 30) {
                console.error('❌ Select2 gagal dimuat setelah 30 percobaan');
                return;
            }

            if (typeof $.fn.select2 !== 'undefined') {
                console.log('✓ Select2 tersedia');
                window.select2Ready = true;
                callback($);
            } else {
                console.log('⏳ Menunggu Select2... percobaan', attempts + 1);
                setTimeout(function() {
                    waitForSelect2(callback, attempts + 1);
                }, 100);
            }
        }

        // Wait for Select2, then initialize
        waitForSelect2(function(jqInstance) {
            initializeSelect2Dropdowns(jqInstance);
        });
    }); // End of jQuery ready
    
    // Initialize Select2 for all penerima and pengirim dropdowns
    function initializeSelect2Dropdowns(jq) {
        // Accept jQuery instance from caller (preferred), else fallback to window.jQuery
        var $ = jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
        
        // Double-check Select2 availability
        if (!$ || typeof $.fn.select2 === 'undefined') {
            console.error('❌ Select2 tidak tersedia saat inisialisasi');
            return;
        }
        
        console.log('🔧 Menginisialisasi Select2 dropdowns');
        
        // Mark Select2 as ready
        window.select2Ready = true;
        window.select2Jq = $;
        
        // Initialize all penerima Select2 dropdowns
        $('.select2-penerima').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
                placeholder: '-- Pilih Penerima --',
                allowClear: true,
                width: '100%'
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

        // Initialize all pengirim Select2 dropdowns
        $('.select2-pengirim').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
                placeholder: '-- Pilih Pengirim --',
                allowClear: true,
                width: '100%'
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
    
    // Initialize supir dropdown
    function initializeSupirDropdown() {
        const searchInput = document.getElementById('supirSearch');
        const dropdown = document.getElementById('supirDropdown');
        const hiddenInput = document.getElementById('supir');
        const platInput = document.getElementById('no_plat');
        const options = document.querySelectorAll('.supir-option');

        if (!searchInput || !dropdown || !hiddenInput || !platInput) {
            console.error('Required elements not found for supir dropdown');
            return;
        }

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
            const $ = window.select2Jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
            console.log('Received penerima data:', penerimaData);
            
            // Create new option for all penerima and pengirim dropdowns
            const newOptionPenerima = new Option(
                penerimaData.nama,
                penerimaData.nama,
                true,
                true
            );
            $(newOptionPenerima).attr('data-alamat', penerimaData.alamat || '');
            
            const newOptionPengirim = new Option(
                penerimaData.nama,
                penerimaData.nama,
                false,
                false
            );
            $(newOptionPengirim).attr('data-alamat', penerimaData.alamat || '');
            
            // Add to all penerima dropdowns
            $('.select2-penerima').each(function() {
                $(this).append($(newOptionPenerima).clone());
            });
            
            // Add to all pengirim dropdowns
            $('.select2-pengirim').each(function() {
                $(this).append($(newOptionPengirim).clone());
            });
            
            // Show success notification
            const successMsg = document.createElement('div');
            successMsg.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50';
            successMsg.innerHTML = `
                <strong>Berhasil!</strong> ${penerimaData.nama} telah ditambahkan.
            `;
            document.body.appendChild(successMsg);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                successMsg.remove();
            }, 3000);
        }
    });
</script>
@endpush
@endsection