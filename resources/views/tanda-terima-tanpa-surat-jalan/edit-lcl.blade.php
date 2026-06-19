@extends('layouts.app')

@section('title', 'Edit Tanda Terima LCL')
@section('page_title', 'Edit Tanda Terima LCL')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- FontAwesome for CamScanner Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* CamScanner Styles */
    .scanner-filter-btn.active {
        background-color: rgb(79 70 229) !important; /* bg-indigo-600 */
        color: #ffffff !important;
        border-color: rgb(99 102 241) !important; /* border-indigo-500 */
    }
    .scanner-filter-btn.active i {
        color: #ffffff !important;
    }

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
                                   value="{{ old('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima ?? '') }}"
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

                        <!-- Surat Jalan Pabrik -->
                        <div>
                            <label for="surat_jalan_pabrik" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Surat Jalan Pabrik
                            </label>
                            <input type="text" name="surat_jalan_pabrik" id="surat_jalan_pabrik"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('surat_jalan_pabrik', $tandaTerima->surat_jalan_pabrik) }}"
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
                                   value="{{ old('tanggal_surat_jalan_pabrik', $tandaTerima->tanggal_surat_jalan_pabrik ? $tandaTerima->tanggal_surat_jalan_pabrik->format('Y-m-d') : '') }}">
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
                                            <select name="nama_penerima" id="nama_penerima" required
                                                    class="select2-penerima flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                <option value="">-- Pilih Penerima --</option>
                                                @if(isset($masterPengirimPenerima))
                                                    @foreach($masterPengirimPenerima as $item)
                                                        <option value="{{ $item->nama }}" 
                                                                data-alamat="{{ $item->alamat }}"
                                                                data-id="{{ $item->id ?? '' }}"
                                                                data-type="{{ $item->type ?? '' }}"
                                                                {{ old('nama_penerima', $tandaTerima->nama_penerima) == $item->nama ? 'selected' : '' }}>
                                                            {{ $item->nama }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <button type="button" 
                                                    id="edit_penerima_btn"
                                                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors flex items-center hidden"
                                                    title="Edit Penerima">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
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
                                                            data-alamat="{{ $item->alamat }}"
                                                            {{ old('notify_party', $tandaTerima->notify_party) == $item->nama ? 'selected' : '' }}>
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
                                                  placeholder="Alamat lengkap Notify Party...">{{ old('alamat_notify_party', $tandaTerima->alamat_notify_party) }}</textarea>
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
                                            <select name="nama_pengirim" id="nama_pengirim" required
                                                    class="select2-pengirim flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                <option value="">-- Pilih Pengirim --</option>
                                                @if(isset($masterPengirimPenerima))
                                                    @foreach($masterPengirimPenerima as $item)
                                                        <option value="{{ $item->nama }}"
                                                                data-alamat="{{ $item->alamat }}"
                                                                data-id="{{ $item->id ?? '' }}"
                                                                data-type="{{ $item->type ?? '' }}"
                                                                {{ old('nama_pengirim', $tandaTerima->nama_pengirim) == $item->nama ? 'selected' : '' }}>
                                                            {{ $item->nama }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <button type="button" 
                                                    id="edit_pengirim_btn"
                                                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors flex items-center hidden"
                                                    title="Edit Pengirim">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
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
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
                                        <div>
                                            <label for="nama_barang_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Nama Barang
                                            </label>
                                            <input type="text"
                                                   name="nama_barang[]" 
                                                   id="nama_barang_{{ $index }}"
                                                   class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="Nama barang"
                                                   value="{{ old('nama_barang.'.$index, $item->nama_barang) }}"
                                                   oninput="toggleUkuranField(this)">
                                        </div>
                                        <div class="ukuran-container hidden">
                                            <label for="ukuran_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Ukuran
                                            </label>
                                            <input type="text"
                                                   name="ukuran[]"
                                                   id="ukuran_{{ $index }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="Contoh: 40x40"
                                                   value="{{ old('ukuran.'.$index, $item->ukuran) }}">
                                        </div>
                                        <div>
                                            <label for="jumlah_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                Jumlah
                                            </label>
                                            <input type="number"
                                                   name="jumlah[]"
                                                   id="jumlah_{{ $index }}"
                                                   class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                                   placeholder="0"
                                                   value="{{ old('jumlah.'.$index, $item->jumlah) }}"
                                                   min="0"
                                                   step="1"
                                                   onchange="calculateVolume(this.closest('.dimensi-row'))">
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
                                        Volume akan dihitung otomatis dari panjang × lebar × tinggi × jumlah
                                    </p>
                                </div>
                            @endforeach
                        @else
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
                                   placeholder="Cari tujuan pengiriman..." autocomplete="off" required
                                   value="{{ old('tujuan_pengiriman') ? App\Models\MasterTujuanKirim::find(old('tujuan_pengiriman'))->nama_tujuan ?? '' : ($tandaTerima->tujuanPengiriman ? $tandaTerima->tujuanPengiriman->nama_tujuan : '') }}">
                            <div id="tujuanPengirimanDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                @foreach(App\Models\MasterTujuanKirim::all() as $tujuan)
                                    <div class="tujuan-pengiriman-option px-3 py-2 hover:bg-indigo-50 cursor-pointer text-sm border-b border-gray-100"
                                         data-value="{{ $tujuan->id }}" data-text="{{ $tujuan->nama_tujuan }}">
                                        {{ $tujuan->nama_tujuan }}
                                    </div>
                                @endforeach
                            </div>
                            <select name="tujuan_pengiriman" id="tujuan_pengiriman" class="hidden">
                                <option value="">Pilih Tujuan Pengiriman</option>
                                @foreach(App\Models\MasterTujuanKirim::all() as $tujuan)
                                    <option value="{{ $tujuan->id }}" {{ old('tujuan_pengiriman', $tandaTerima->tujuan_pengiriman_id) == $tujuan->id ? 'selected' : '' }}>
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

                        @php
                            $existingImages = $tandaTerima->gambar_surat_jalan;
                            if (is_string($existingImages)) {
                                $existingImages = json_decode($existingImages, true) ?? [];
                            }
                            if (!is_array($existingImages)) {
                                $existingImages = [];
                            }
                        @endphp

                        <!-- Preview Area for Images -->
                        <div id="image-preview-container" class="mt-4 @if(empty($existingImages)) hidden @endif">
                            <label class="block text-xs font-medium text-gray-500 mb-2">
                                <i class="fas fa-eye mr-1 text-orange-600"></i>
                                Preview Gambar
                            </label>
                            <div id="image-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($existingImages as $index => $imagePath)
                                    @php $imgUrl = asset('storage/' . ltrim($imagePath, '/')); @endphp
                                    <div class="relative bg-white border border-gray-200 rounded-xl p-3 image-preview-item" data-is-existing="1" data-path="{{ $imagePath }}">
                                        <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-gray-50 h-28 flex items-center justify-center">
                                            <img src="{{ $imgUrl }}" alt="Gambar {{ $index + 1 }}" class="w-full h-full object-cover">
                                        </div>
                                        <p class="text-[11px] font-semibold text-gray-700 mt-2 truncate" title="Gambar {{ $index + 1 }}">Gambar {{ $index + 1 }}</p>
                                        <p class="text-[10px] text-gray-500">Tersimpan di server</p>
                                        <div class="flex gap-1.5 mt-2">
                                            <a href="{{ $imgUrl }}" target="_blank" rel="noopener noreferrer" class="flex-1 flex items-center justify-center gap-1 py-1 px-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-700 hover:text-black text-[10px] font-semibold transition cursor-pointer" download>
                                                <i class="fas fa-download"></i> Unduh
                                            </a>
                                            <button type="button" onclick="removeImageItem({{ $index }})" class="flex-1 flex items-center justify-center gap-1 py-1 px-2 rounded-lg bg-red-50 border border-red-200 text-red-600 hover:text-red-700 hover:bg-red-100 text-[10px] font-semibold transition cursor-pointer">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <input type="hidden" name="existing_images[]" value="{{ $imagePath }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
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

<!-- CamScanner Scanner Modal -->
<div id="camscanner-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                                <div class="absolute -bottom-1.5 -left-1.5 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-nwse-resize shadow-md" data-handle="sw"></div>
                                <div class="absolute -bottom-1.5 -right-1.5 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-nesw-resize shadow-md" data-handle="se"></div>
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
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-950 hover:bg-slate-850 border border-slate-850 text-xs font-semibold text-slate-300 hover:text-white transition duration-150 cursor-pointer">
                                    <i class="fas fa-undo"></i>
                                    <span>Putar Kiri</span>
                                </button>
                                <button type="button" onclick="rotateScanner(90)"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-950 hover:bg-slate-850 border border-slate-850 text-xs font-semibold text-slate-300 hover:text-white transition duration-150 cursor-pointer">
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
                                class="flex-1 px-4 py-2.5 bg-slate-950 hover:bg-slate-850 border border-slate-850 text-slate-300 text-xs font-bold rounded-xl transition duration-150 cursor-pointer">
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

<script>
function calculateVolume(rowElement) {
    if (!rowElement) return;
    
    // Try to find inputs - handle both array format (panjang[]) and object format (items[x][panjang])
    const panjangInput = rowElement.querySelector('input[name*="panjang"]');
    const lebarInput = rowElement.querySelector('input[name*="lebar"]');
    const tinggiInput = rowElement.querySelector('input[name*="tinggi"]');
    const jumlahInput = rowElement.querySelector('input[name*="jumlah"]');
    const volumeInput = rowElement.querySelector('input[name*="meter_kubik"]');
    
    // Check if all required inputs exist
    if (!panjangInput || !lebarInput || !tinggiInput || !volumeInput) {
        console.warn('Cannot calculate volume: missing input elements');
        return;
    }
    
    const panjang = parseFloat(panjangInput.value) || 0;
    const lebar = parseFloat(lebarInput.value) || 0;
    const tinggi = parseFloat(tinggiInput.value) || 0;
    const jumlah = jumlahInput ? (parseInt(jumlahInput.value, 10) || 1) : 1;
    
    // Calculate volume in cubic meters including jumlah
    const volume = panjang * tinggi * lebar * jumlah;
    
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang</label>
                <input type="text" name="nama_barang[]" 
                       class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                       placeholder="Nama barang"
                       oninput="toggleUkuranField(this)">
            </div>
            <div class="ukuran-container hidden">
                <label class="block text-xs font-medium text-gray-500 mb-2">Ukuran</label>
                <input type="text" name="ukuran[]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                       placeholder="Contoh: 40x40">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah</label>
                <input type="number" name="jumlah[]" 
                       class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                       placeholder="0" min="0" step="1"
                       onchange="calculateVolume(this.closest('.dimensi-row'))">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-2">Satuan</label>
                <input type="text" name="satuan[]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                       placeholder="Pcs, Kg, Box">
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                <input type="number" name="panjang[]" step="0.001" min="0"
                       class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                       placeholder="0.000"
                       onchange="calculateVolume(this.closest('.dimensi-row'))">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                <input type="number" name="lebar[]" step="0.001" min="0"
                       class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                       placeholder="0.000"
                       onchange="calculateVolume(this.closest('.dimensi-row'))">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                <input type="number" name="tinggi[]" step="0.001" min="0"
                       class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                       placeholder="0.000"
                       onchange="calculateVolume(this.closest('.dimensi-row'))">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                <input type="number" name="meter_kubik[]" step="0.001" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm"
                       placeholder="0.000" readonly>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                <input type="number" name="tonase[]" step="0.001" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                       placeholder="0.000">
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            <i class="fas fa-info-circle mr-1"></i>
            Volume akan dihitung otomatis dari panjang × lebar × tinggi × jumlah
        </p>
    `;
    
    container.appendChild(newRow);
    itemIndex++;
}

function removeDimensiRow(button) {
    button.closest('.dimensi-row').remove();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for "Tambah Dimensi" button
    const addDimensiBtn = document.getElementById('add-dimensi-btn');
    if (addDimensiBtn) {
        addDimensiBtn.addEventListener('click', addDimensiRow);
    }

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

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Initialize existing images from DOM
function initializeExistingImages() {
    const existingItems = document.querySelectorAll('#image-preview-grid .image-preview-item[data-is-existing="1"]');
    existingItems.forEach((el, index) => {
        const path = el.getAttribute('data-path');
        const img = el.querySelector('img');
        const imgUrl = img ? img.src : '';
        processedImages.push({
            isExisting: true,
            path: path,
            name: 'Gambar ' + (index + 1),
            dataUrl: imgUrl,
            size: 0,
            type: 'image/jpeg'
        });
    });
    // Re-render using our standard template so they are consistent
    renderImagePreviews();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize existing images on page load
    initializeExistingImages();
});

function previewImages(input) {
    if (input.files && input.files.length > 0) {
        const existingCount = processedImages.length;
        const maxAllowed = 5 - existingCount;
        if (maxAllowed <= 0) {
            alert('Maksimal 5 gambar. Hapus beberapa gambar terlebih dahulu jika ingin menambahkan.');
            input.value = '';
            return;
        }

        const filesToProcess = Array.from(input.files).slice(0, maxAllowed);
        let loadedCount = 0;
        const startIndex = processedImages.length;

        filesToProcess.forEach((file, fileIdx) => {
            const isPdf = file.type === 'application/pdf';
            
            if (isPdf) {
                processedImages.push({
                    file: file,
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    isPdf: true,
                    isExisting: false
                });
                loadedCount++;
                checkFinish();
            } else if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    processedImages.push({
                        file: file,
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        isPdf: false,
                        isExisting: false,
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
                    });
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
                renderImagePreviews();
                syncFileInput();
            }
        }

        if (input.files.length > maxAllowed) {
            alert(`Hanya ${maxAllowed} gambar pertama yang akan diproses karena batas maksimal 5 gambar.`);
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
            previewDiv.className = 'relative bg-white border border-gray-200 rounded-xl p-3 hover:shadow-lg hover:border-blue-300 transition duration-200 image-preview-item';
            
            if (item.isExisting) {
                previewDiv.innerHTML = `
                    <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-gray-50 h-28 flex items-center justify-center">
                        <img src="${item.dataUrl}" alt="${item.name}" class="w-full h-full object-cover">
                    </div>
                    <p class="text-[11px] font-semibold text-gray-700 mt-2 truncate" title="${item.name}">${item.name}</p>
                    <p class="text-[10px] text-gray-500">Tersimpan di server</p>
                    <div class="flex gap-1.5 mt-2">
                        <a href="${item.dataUrl}" target="_blank" rel="noopener noreferrer" class="flex-1 flex items-center justify-center gap-1 py-1 px-1 rounded-lg bg-gray-100 border border-gray-200 text-gray-700 hover:text-black text-[10px] font-semibold transition cursor-pointer" download title="Unduh">
                            <i class="fas fa-download"></i>
                        </a>
                        <button type="button" onclick="openScannerModal(${index})" class="flex-1 flex items-center justify-center gap-1 py-1 px-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-100 text-[10px] font-semibold transition cursor-pointer" title="Scan Dokumen">
                            <i class="fas fa-magic"></i>
                        </button>
                        <button type="button" onclick="removeImageItem(${index})" class="flex-1 flex items-center justify-center gap-1 py-1 px-1 rounded-lg bg-red-50 border border-red-200 text-red-600 hover:text-red-700 hover:bg-red-100 text-[10px] font-semibold transition cursor-pointer" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <input type="hidden" name="existing_images[]" value="${item.path}">
                `;
            } else if (item.isPdf) {
                previewDiv.innerHTML = `
                    <div class="relative">
                        <div class="w-full h-28 flex flex-col items-center justify-center bg-red-50 rounded-lg border border-red-200">
                            <i class="fas fa-file-pdf text-red-500 text-3xl mb-1.5 animate-pulse"></i>
                            <span class="text-[9px] font-bold text-red-600 uppercase tracking-wider">PDF DOCUMENT</span>
                        </div>
                        <button type="button" 
                                onclick="removeImageItem(${index})"
                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs transition shadow-md remove-preview-btn border border-red-500 z-10 cursor-pointer font-bold"
                                title="Hapus file">
                            ×
                        </button>
                    </div>
                    <p class="text-[11px] font-semibold text-gray-700 mt-2 truncate" title="${item.name}">${item.name}</p>
                    <p class="text-[10px] text-gray-500">${formatFileSize(item.size)}</p>
                `;
            } else {
                previewDiv.innerHTML = `
                    <div class="relative group overflow-hidden rounded-lg border border-gray-200 bg-gray-50 h-28 flex items-center justify-center">
                        <img src="${item.dataUrl}" 
                             alt="Preview ${index + 1}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/60 transition-all flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100">
                            <button type="button" onclick="openScannerModal(${index})"
                                    class="p-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-[11px] font-medium flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-magic"></i> Scan
                            </button>
                        </div>
                    </div>
                    <p class="text-[11px] font-semibold text-gray-700 mt-2 truncate" title="${item.name}">${item.name}</p>
                    <p class="text-[10px] text-gray-500">${formatFileSize(item.size)}</p>
                    <div class="flex gap-1.5 mt-2">
                        <button type="button" onclick="openScannerModal(${index})"
                                class="flex-1 flex items-center justify-center gap-1 py-1 px-2 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-100 text-[10px] font-semibold transition cursor-pointer">
                            <i class="fas fa-magic"></i> Scan Dokumen
                        </button>
                        <button type="button" onclick="removeImageItem(${index})"
                                class="flex items-center justify-center gap-1 py-1 px-2 rounded-lg bg-red-50 border border-red-200 text-red-600 hover:text-red-700 hover:bg-red-100 text-[10px] font-semibold transition cursor-pointer"
                                title="Hapus file">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            }
            
            previewGrid.appendChild(previewDiv);
        });
    } else {
        previewContainer.classList.add('hidden');
    }
}

function removeImageItem(index) {
    const item = processedImages[index];
    if (item && item.isExisting) {
        const form = document.querySelector('form');
        if (form) {
            const rem = document.createElement('input');
            rem.type = 'hidden'; 
            rem.name = 'hapus_gambar[]'; 
            rem.value = item.path; 
            form.appendChild(rem);
        }
    }
    processedImages.splice(index, 1);
    renderImagePreviews();
    syncFileInput();
}

function syncFileInput() {
    const input = document.getElementById('gambar_surat_jalan');
    if (!input) return;
    
    try {
        const dataTransfer = new DataTransfer();
        processedImages.forEach(item => {
            if (item.isExisting) return; // do not upload existing files
            if (item.file) {
                dataTransfer.items.add(item.file);
            } else if (item.dataUrl) {
                const file = dataURLtoFile(item.dataUrl, item.name, item.type);
                dataTransfer.items.add(file);
            }
        });
        input.files = dataTransfer.files;
    } catch (err) {
        console.error("Gagal sinkronisasi file input:", err);
    }
}

function dataURLtoFile(dataurl, filename, mime) {
    var arr = dataurl.split(','),
        bstr = atob(arr[1]), 
        n = bstr.length, 
        u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, {type: mime || 'image/jpeg'});
}

// CamScanner Engine
function openScannerModal(index) {
    activeImageIndex = index;
    const item = processedImages[index];
    if (!item || item.isPdf) return;

    document.getElementById('camscanner-modal').classList.remove('hidden');
    document.getElementById('scanner-loader').classList.remove('hidden');

    if (!item.settings) {
        item.settings = {
            filter: 'original',
            rotation: 0,
            brightness: 0,
            contrast: 0,
            threshold: 120
        };
    }

    currentSettings = JSON.parse(JSON.stringify(item.settings));
    
    // Set slider values
    document.getElementById('adjust-brightness').value = currentSettings.brightness;
    document.getElementById('val-brightness').textContent = (currentSettings.brightness > 0 ? '+' : '') + currentSettings.brightness + '%';
    document.getElementById('adjust-contrast').value = currentSettings.contrast;
    document.getElementById('val-contrast').textContent = (currentSettings.contrast > 0 ? '+' : '') + currentSettings.contrast + '%';
    document.getElementById('adjust-threshold').value = currentSettings.threshold;
    document.getElementById('val-threshold').textContent = currentSettings.threshold;

    if (currentSettings.filter === 'bw') {
        document.getElementById('threshold-slider-group').classList.remove('hidden');
    } else {
        document.getElementById('threshold-slider-group').classList.add('hidden');
    }

    // Update active preset button state
    document.querySelectorAll('.scanner-filter-btn').forEach(btn => btn.classList.remove('active'));
    const activeBtn = document.getElementById('filter-' + currentSettings.filter);
    if (activeBtn) activeBtn.classList.add('active');

    originalImgElement = new Image();
    originalImgElement.crossOrigin = "anonymous";
    originalImgElement.onload = function() {
        // Initialize manual crop boundary if not set
        if (!item.crop) {
            cropBoxPercent = { x: 10, y: 10, w: 80, h: 80 };
        } else {
            cropBoxPercent = JSON.parse(JSON.stringify(item.crop));
        }
        
        isCropperActive = false;
        document.getElementById('crop-overlay').classList.add('hidden');
        document.getElementById('cropper-btn-text').textContent = "Pangkas Manual";
        document.getElementById('cropper-toggle-btn').className = "flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-slate-950 hover:bg-slate-850 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-indigo-400 transition duration-150 cursor-pointer";

        applyFilters();
        document.getElementById('scanner-loader').classList.add('hidden');
    };
    originalImgElement.onerror = function() {
        alert('Gagal memuat gambar untuk di-scan.');
        document.getElementById('scanner-loader').classList.add('hidden');
        closeScannerModal();
    };
    // Use originalDataUrl if available, otherwise fallback to dataUrl (for existing images)
    originalImgElement.src = item.originalDataUrl || item.dataUrl;
}

function closeScannerModal() {
    document.getElementById('camscanner-modal').classList.add('hidden');
    activeImageIndex = null;
    originalImgElement = null;
}

function setScannerFilter(filterName) {
    currentSettings.filter = filterName;
    document.querySelectorAll('.scanner-filter-btn').forEach(btn => btn.classList.remove('active'));
    const activeBtn = document.getElementById('filter-' + filterName);
    if (activeBtn) activeBtn.classList.add('active');

    if (filterName === 'bw') {
        document.getElementById('threshold-slider-group').classList.remove('hidden');
    } else {
        document.getElementById('threshold-slider-group').classList.add('hidden');
    }

    applyFilters();
}

function adjustScannerManual(type, value) {
    currentSettings[type] = parseInt(value, 10);
    if (type === 'brightness') {
        document.getElementById('val-brightness').textContent = (value > 0 ? '+' : '') + value + '%';
    } else if (type === 'contrast') {
        document.getElementById('val-contrast').textContent = (value > 0 ? '+' : '') + value + '%';
    } else if (type === 'threshold') {
        document.getElementById('val-threshold').textContent = value;
    }
    applyFilters();
}

function rotateScanner(deg) {
    currentSettings.rotation = (currentSettings.rotation + deg) % 360;
    if (currentSettings.rotation < 0) currentSettings.rotation += 360;
    applyFilters();
}

function applyFilters() {
    if (!originalImgElement) return;

    const canvas = document.getElementById('scanner-canvas');
    const ctx = canvas.getContext('2d');

    // Calculate dimensions based on rotation
    const is90or270 = (currentSettings.rotation === 90 || currentSettings.rotation === 270);
    let targetW = is90or270 ? originalImgElement.height : originalImgElement.width;
    let targetH = is90or270 ? originalImgElement.width : originalImgElement.height;

    // Downscale for canvas editor limits to prevent browser crash
    const maxDimension = 1200;
    let scale = 1;
    if (targetW > maxDimension || targetH > maxDimension) {
        scale = maxDimension / Math.max(targetW, targetH);
        targetW = Math.round(targetW * scale);
        targetH = Math.round(targetH * scale);
    }

    canvas.width = targetW;
    canvas.height = targetH;

    ctx.clearRect(0, 0, targetW, targetH);
    ctx.save();

    // Translate and rotate
    ctx.translate(targetW / 2, targetH / 2);
    ctx.rotate((currentSettings.rotation * Math.PI) / 180);

    // Draw image with translation offsets
    const drawW = (is90or270 ? targetH : targetW);
    const drawH = (is90or270 ? targetW : targetH);
    ctx.drawImage(originalImgElement, -drawW / 2, -drawH / 2, drawW, drawH);
    ctx.restore();

    // 1. If Crop/Pangkas is active or stored, crop the canvas
    if (cropBoxPercent && (isCropperActive || processedImages[activeImageIndex].crop)) {
        // Get cropped pixel boundaries
        const cx = Math.round((cropBoxPercent.x / 100) * targetW);
        const cy = Math.round((cropBoxPercent.y / 100) * targetH);
        const cw = Math.round((cropBoxPercent.w / 100) * targetW);
        const ch = Math.round((cropBoxPercent.h / 100) * targetH);

        if (cw > 5 && ch > 5) {
            const cropImageData = ctx.getImageData(cx, cy, cw, ch);
            canvas.width = cw;
            canvas.height = ch;
            ctx.putImageData(cropImageData, 0, 0);
        }
    }

    // Apply Pixel Manipulations
    const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imgData.data;

    // Apply Brightness & Contrast
    const b = currentSettings.brightness; // -100 to 100
    const c = currentSettings.contrast;   // -100 to 100
    const factor = (259 * (c + 255)) / (255 * (259 - c));

    for (let i = 0; i < data.length; i += 4) {
        // Brightness
        let r = data[i] + b * 2.55;
        let g = data[i + 1] + b * 2.55;
        let bVal = data[i + 2] + b * 2.55;

        // Contrast
        r = factor * (r - 128) + 128;
        g = factor * (g - 128) + 128;
        bVal = factor * (bVal - 128) + 128;

        data[i] = Math.max(0, Math.min(255, r));
        data[i + 1] = Math.max(0, Math.min(255, g));
        data[i + 2] = Math.max(0, Math.min(255, bVal));
    }

    // Apply Presets/Filters
    if (currentSettings.filter === 'grayscale') {
        for (let i = 0; i < data.length; i += 4) {
            const gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
            data[i] = gray;
            data[i + 1] = gray;
            data[i + 2] = gray;
        }
    } else if (currentSettings.filter === 'bw') {
        const thresh = currentSettings.threshold;
        for (let i = 0; i < data.length; i += 4) {
            const gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
            const bw = gray >= thresh ? 255 : 0;
            data[i] = bw;
            data[i + 1] = bw;
            data[i + 2] = bw;
        }
    } else if (currentSettings.filter === 'magic') {
        // Adaptive Contrast / White balance simulation for paper document clarity
        for (let i = 0; i < data.length; i += 4) {
            let r = data[i];
            let g = data[i+1];
            let bVal = data[i+2];

            // Increase contrast dynamically and lighten background
            r = Math.min(255, r * 1.15);
            g = Math.min(255, g * 1.15);
            bVal = Math.min(255, bVal * 1.15);

            // If it's close to white, make it pure white (enhances paper contrast)
            const brightness = 0.299 * r + 0.587 * g + 0.114 * bVal;
            if (brightness > 165) {
                r = Math.min(255, r * 1.1);
                g = Math.min(255, g * 1.1);
                bVal = Math.min(255, bVal * 1.1);
            } else {
                // Darken text slightly
                r = r * 0.9;
                g = g * 0.9;
                bVal = bVal * 0.9;
            }

            data[i] = Math.max(0, Math.min(255, r));
            data[i + 1] = Math.max(0, Math.min(255, g));
            data[i + 2] = Math.max(0, Math.min(255, bVal));
        }
    }

    ctx.putImageData(imgData, 0, 0);

    // Render/position crop box in UI if active
    if (isCropperActive) {
        updateCropBoxUI();
    }
}

// Cropper manual methods
function toggleCropper() {
    isCropperActive = !isCropperActive;
    const overlay = document.getElementById('crop-overlay');
    const btnText = document.getElementById('cropper-btn-text');
    const btn = document.getElementById('cropper-toggle-btn');
    
    if (isCropperActive) {
        overlay.classList.remove('hidden');
        btnText.textContent = "Matikan Pangkas";
        btn.className = "flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-indigo-600 text-white border border-indigo-500 text-xs font-semibold hover:bg-indigo-700 transition duration-150 cursor-pointer";
        applyFilters(); // Re-render canvas in full size before cropping
    } else {
        overlay.classList.add('hidden');
        btnText.textContent = "Pangkas Manual";
        btn.className = "flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-slate-950 hover:bg-slate-850 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-indigo-400 transition duration-150 cursor-pointer";
        
        // Discard changes to crop
        processedImages[activeImageIndex].crop = null;
        applyFilters();
    }
}

function updateCropBoxUI() {
    const canvas = document.getElementById('scanner-canvas');
    const box = document.getElementById('crop-box');
    
    const canvasW = canvas.offsetWidth;
    const canvasH = canvas.offsetHeight;
    const canvasLeft = canvas.offsetLeft;
    const canvasTop = canvas.offsetTop;

    box.style.left = canvasLeft + (cropBoxPercent.x / 100) * canvasW + 'px';
    box.style.top = canvasTop + (cropBoxPercent.y / 100) * canvasH + 'px';
    box.style.width = (cropBoxPercent.w / 100) * canvasW + 'px';
    box.style.height = (cropBoxPercent.h / 100) * canvasH + 'px';
}

// Event listener setup for crop box dragging/resizing
document.addEventListener('mousedown', function(e) {
    if (!isCropperActive) return;
    const handle = e.target.getAttribute('data-handle');
    const isBox = e.target === document.getElementById('crop-box');
    
    if (handle || isBox) {
        e.preventDefault();
        dragStartCoords = { x: e.clientX, y: e.clientY };
        cropBoxStartCoords = { ...cropBoxPercent };
        
        if (handle) {
            isResizingBox = true;
            activeHandle = handle;
        } else {
            isDraggingBox = true;
        }
    }
});

document.addEventListener('mousemove', function(e) {
    if (!isCropperActive || (!isDraggingBox && !isResizingBox)) return;

    const canvas = document.getElementById('scanner-canvas');
    const canvasW = canvas.offsetWidth;
    const canvasH = canvas.offsetHeight;

    const dxPercent = ((e.clientX - dragStartCoords.x) / canvasW) * 100;
    const dyPercent = ((e.clientY - dragStartCoords.y) / canvasH) * 100;

    if (isDraggingBox) {
        let newX = cropBoxStartCoords.x + dxPercent;
        let newY = cropBoxStartCoords.y + dyPercent;

        // Clamp within bounds
        newX = Math.max(0, Math.min(100 - cropBoxStartCoords.w, newX));
        newY = Math.max(0, Math.min(100 - cropBoxStartCoords.h, newY));

        cropBoxPercent.x = newX;
        cropBoxPercent.y = newY;
    } else if (isResizingBox) {
        let newX = cropBoxStartCoords.x;
        let newY = cropBoxStartCoords.y;
        let newW = cropBoxStartCoords.w;
        let newH = cropBoxStartCoords.h;

        if (activeHandle.includes('e')) {
            newW = Math.max(5, Math.min(100 - newX, cropBoxStartCoords.w + dxPercent));
        }
        if (activeHandle.includes('w')) {
            const possibleW = cropBoxStartCoords.w - dxPercent;
            if (possibleW >= 5) {
                newX = Math.max(0, cropBoxStartCoords.x + dxPercent);
                newW = cropBoxStartCoords.w + (cropBoxStartCoords.x - newX);
            }
        }
        if (activeHandle.includes('s')) {
            newH = Math.max(5, Math.min(100 - newY, cropBoxStartCoords.h + dyPercent));
        }
        if (activeHandle.includes('n')) {
            const possibleH = cropBoxStartCoords.h - dyPercent;
            if (possibleH >= 5) {
                newY = Math.max(0, cropBoxStartCoords.y + dyPercent);
                newH = cropBoxStartCoords.h + (cropBoxStartCoords.y - newY);
            }
        }

        cropBoxPercent.x = newX;
        cropBoxPercent.y = newY;
        cropBoxPercent.w = newW;
        cropBoxPercent.h = newH;
    }

    updateCropBoxUI();
});

document.addEventListener('mouseup', function() {
    isDraggingBox = false;
    isResizingBox = false;
    activeHandle = null;
});

// Autocrop edge-detection algorithm
function autoCropDocument() {
    if (!originalImgElement) return;

    document.getElementById('scanner-loader').classList.remove('hidden');

    setTimeout(() => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Downscale for faster analysis
        const scaleWidth = 300;
        const scaleHeight = Math.round((originalImgElement.height / originalImgElement.width) * scaleWidth);
        canvas.width = scaleWidth;
        canvas.height = scaleHeight;

        // Handle rotation in detector
        ctx.translate(scaleWidth / 2, scaleHeight / 2);
        ctx.rotate((currentSettings.rotation * Math.PI) / 180);
        const is90or270 = (currentSettings.rotation === 90 || currentSettings.rotation === 270);
        const drawW = is90or270 ? scaleHeight : scaleWidth;
        const drawH = is90or270 ? scaleWidth : scaleHeight;
        ctx.drawImage(originalImgElement, -drawW / 2, -drawH / 2, drawW, drawH);
        ctx.restore();

        const imgData = ctx.getImageData(0, 0, scaleWidth, scaleHeight);
        const data = imgData.data;

        // 1. Convert to Grayscale & Calculate mean intensity
        const gray = new Uint8Array(scaleWidth * scaleHeight);
        let totalIntensity = 0;
        for (let i = 0; i < data.length; i += 4) {
            const val = Math.round(0.299 * data[i] + 0.587 * data[i+1] + 0.114 * data[i+2]);
            gray[i / 4] = val;
            totalIntensity += val;
        }
        const meanIntensity = totalIntensity / (scaleWidth * scaleHeight);

        // 2. Sobel Edge Detection
        const edge = new Uint8Array(scaleWidth * scaleHeight);
        for (let y = 1; y < scaleHeight - 1; y++) {
            for (let x = 1; x < scaleWidth - 1; x++) {
                const idx = y * scaleWidth + x;
                
                // Sobel kernels
                const gx = 
                    -1 * gray[idx - scaleWidth - 1] + 1 * gray[idx - scaleWidth + 1] +
                    -2 * gray[idx - 1]               + 2 * gray[idx + 1] +
                    -1 * gray[idx + scaleWidth - 1] + 1 * gray[idx + scaleWidth + 1];

                const gy = 
                    -1 * gray[idx - scaleWidth - 1] - 2 * gray[idx - scaleWidth] - 1 * gray[idx - scaleWidth + 1] +
                    1 * gray[idx + scaleWidth - 1] + 2 * gray[idx + scaleWidth] + 1 * gray[idx + scaleWidth + 1];

                const mag = Math.sqrt(gx * gx + gy * gy);
                edge[idx] = mag > 45 ? 255 : 0;
            }
        }

        // 3. Document bounding box detection (horizontal & vertical projections)
        let minX = scaleWidth, maxX = 0, minY = scaleHeight, maxY = 0;
        let foundEdges = false;

        // Thresholds based on lighting
        const projThreshold = Math.max(2, Math.round(scaleWidth * 0.015));

        for (let y = 4; y < scaleHeight - 4; y++) {
            let rowCount = 0;
            for (let x = 4; x < scaleWidth - 4; x++) {
                if (edge[y * scaleWidth + x] === 255) {
                    rowCount++;
                }
            }
            if (rowCount > projThreshold) {
                minY = Math.min(minY, y);
                maxY = Math.max(maxY, y);
                foundEdges = true;
            }
        }

        for (let x = 4; x < scaleWidth - 4; x++) {
            let colCount = 0;
            for (let y = 4; y < scaleHeight - 4; y++) {
                if (edge[y * scaleWidth + x] === 255) {
                    colCount++;
                }
            }
            if (colCount > projThreshold) {
                minX = Math.min(minX, x);
                maxX = Math.max(maxX, x);
                foundEdges = true;
            }
        }

        // 4. Update crop coordinates or fallback
        if (foundEdges && (maxX - minX > scaleWidth * 0.25) && (maxY - minY > scaleHeight * 0.25)) {
            // Success: Add minor margins
            const marginX = Math.round(scaleWidth * 0.02);
            const marginY = Math.round(scaleHeight * 0.02);

            cropBoxPercent.x = Math.max(0, Math.min(100, ((minX - marginX) / scaleWidth) * 100));
            cropBoxPercent.y = Math.max(0, Math.min(100, ((minY - marginY) / scaleHeight) * 100));
            cropBoxPercent.w = Math.max(10, Math.min(100 - cropBoxPercent.x, ((maxX - minX + 2 * marginX) / scaleWidth) * 100));
            cropBoxPercent.h = Math.max(10, Math.min(100 - cropBoxPercent.y, ((maxY - minY + 2 * marginY) / scaleHeight) * 100));
        } else {
            // Fallback to center-focused 85% area
            cropBoxPercent = { x: 7.5, y: 7.5, w: 85, h: 85 };
        }

        // Enforce crop box view
        isCropperActive = true;
        const overlay = document.getElementById('crop-overlay');
        const btnText = document.getElementById('cropper-btn-text');
        const btn = document.getElementById('cropper-toggle-btn');
        overlay.classList.remove('hidden');
        btnText.textContent = "Matikan Pangkas";
        btn.className = "flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-indigo-600 text-white border border-indigo-500 text-xs font-semibold hover:bg-indigo-700 transition duration-150 cursor-pointer";

        applyFilters();
        document.getElementById('scanner-loader').classList.add('hidden');
    }, 100);
}

function saveScannerResult() {
    if (activeImageIndex === null) return;
    const canvas = document.getElementById('scanner-canvas');
    const dataUrl = canvas.toDataURL('image/jpeg', 0.92);

    const item = processedImages[activeImageIndex];

    // If it was an existing image from the server, treat it as a newly uploaded scanned file
    // so we add its old path to hapus_gambar[] and mark it as isExisting = false
    if (item.isExisting) {
        const form = document.querySelector('form');
        if (form && item.path) {
            const rem = document.createElement('input');
            rem.type = 'hidden'; 
            rem.name = 'hapus_gambar[]'; 
            rem.value = item.path; 
            form.appendChild(rem);
        }
        item.isExisting = false;
        if (!item.name) item.name = 'scanned_image.jpg';
        if (!item.type) item.type = 'image/jpeg';
    }

    item.dataUrl = dataUrl;
    item.settings = { ...currentSettings };
    item.crop = isCropperActive ? { ...cropBoxPercent } : null;
    item.isProcessed = true;

    renderImagePreviews();
    syncFileInput();
    closeScannerModal();
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
        
        // Initialize term dropdown
        initializeTermDropdown();
        
        // Initialize tujuan pengiriman dropdown
        initializeTujuanPengirimanDropdown();

        // Toggle edit button when select2 changes
        $('#nama_penerima').on('change', function() {
            const val = $(this).val();
            if (val) {
                $('#edit_penerima_btn').removeClass('hidden');
            } else {
                $('#edit_penerima_btn').addClass('hidden');
            }
        });
        $('#nama_pengirim').on('change', function() {
            const val = $(this).val();
            if (val) {
                $('#edit_pengirim_btn').removeClass('hidden');
            } else {
                $('#edit_pengirim_btn').addClass('hidden');
            }
        });
        
        // Trigger initial check
        setTimeout(function() {
            $('#nama_penerima, #nama_pengirim').trigger('change');
        }, 500);

        $('#edit_penerima_btn').on('click', function(e) {
            e.preventDefault();
            openPenerimaEditPopup();
        });
        $('#edit_pengirim_btn').on('click', function(e) {
            e.preventDefault();
            openPengirimEditPopup();
        });
        
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

        // Initialize all notify party Select2 dropdowns
        $('.select2-notify').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
                placeholder: '-- Pilih Notify Party --',
                allowClear: true,
                width: '100%'
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
    
    // Initialize tujuan pengiriman dropdown
    function initializeTujuanPengirimanDropdown() {
        const searchInput = document.getElementById('tujuanPengirimanSearch');
        const dropdown = document.getElementById('tujuanPengirimanDropdown');
        const hiddenSelect = document.getElementById('tujuan_pengiriman');
        const options = document.querySelectorAll('.tujuan-pengiriman-option');

        if (!searchInput || !dropdown || !hiddenSelect) {
            console.error('Required elements not found for tujuan pengiriman dropdown');
            return;
        }

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
                searchInput.setCustomValidity('');

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
                this.setCustomValidity('Silakan pilih salah satu tujuan pengiriman dari daftar yang tersedia.');
            }
        });

        searchInput.addEventListener('blur', function() {
            if (this.value && !hiddenSelect.value) {
                this.setCustomValidity('Silakan pilih salah satu tujuan pengiriman dari daftar yang tersedia.');
            } else if (!this.value) {
                this.setCustomValidity('Tujuan pengiriman wajib diisi.');
            }
        });
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

    // Function to open popup for editing selected penerima
    function openPenerimaEditPopup() {
        lastPopupOpened = 'penerima-edit';
        const select = document.getElementById('nama_penerima');
        if (!select) return;
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;
        
        const id = selectedOption.getAttribute('data-id');
        const type = selectedOption.getAttribute('data-type');
        if (!id) {
            alert('Tidak dapat mengedit data ini.');
            return;
        }
        
        let url = '';
        if (type === 'penerima') {
            url = "{{ route('tanda-terima.penerima.edit', ':id', false) }}".replace(':id', id);
        } else if (type === 'pengirim') {
            url = "{{ route('tanda-terima.pengirim.edit', ':id', false) }}".replace(':id', id);
        } else if (type === 'master') {
            url = "{{ route('master-pengirim-penerima.edit', ':id', false) }}".replace(':id', id);
        }
        
        if (!url) return;
        url += (url.includes('?') ? '&' : '?') + 'popup=1';
        
        const popupWidth = 700;
        const popupHeight = 600;
        const left = (screen.width - popupWidth) / 2;
        const top = (screen.height - popupHeight) / 2;
        
        window.open(
            url,
            'EditPenerima',
            `width=${popupWidth},height=${popupHeight},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
    }

    // Function to open popup for editing selected pengirim
    function openPengirimEditPopup() {
        lastPopupOpened = 'pengirim-edit';
        const select = document.getElementById('nama_pengirim');
        if (!select) return;
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;
        
        const id = selectedOption.getAttribute('data-id');
        const type = selectedOption.getAttribute('data-type');
        if (!id) {
            alert('Tidak dapat mengedit data ini.');
            return;
        }
        
        let url = '';
        if (type === 'penerima') {
            url = "{{ route('tanda-terima.penerima.edit', ':id', false) }}".replace(':id', id);
        } else if (type === 'pengirim') {
            url = "{{ route('tanda-terima.pengirim.edit', ':id', false) }}".replace(':id', id);
        } else if (type === 'master') {
            url = "{{ route('master-pengirim-penerima.edit', ':id', false) }}".replace(':id', id);
        }
        
        if (!url) return;
        url += (url.includes('?') ? '&' : '?') + 'popup=1';
        
        const popupWidth = 700;
        const popupHeight = 600;
        const left = (screen.width - popupWidth) / 2;
        const top = (screen.height - popupHeight) / 2;
        
        window.open(
            url,
            'EditPengirim',
            `width=${popupWidth},height=${popupHeight},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
    }
    
    // Listen for messages from popup window
    window.addEventListener('message', function(event) {
        // Verify origin for security
        if (event.origin !== window.location.origin) {
            return;
        }
        
        if (event.data && (event.data.type === 'penerimaAdded' || event.data.type === 'penerima-added')) {
            const isEdit = lastPopupOpened === 'penerima-edit' || lastPopupOpened === 'pengirim-edit';
            const data = event.data.type === 'penerima-added' ? event.data.data : event.data.penerima;
            const $ = window.select2Jq || window.jQuery || (typeof jQuery !== 'undefined' ? jQuery : null);
            console.log('Received data:', data, 'isEdit:', isEdit);
            
            if (isEdit) {
                // Update the selected option in the active dropdown
                const selectId = lastPopupOpened === 'penerima-edit' ? 'nama_penerima' : 'nama_pengirim';
                const select = document.getElementById(selectId);
                if (select) {
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption) {
                        selectedOption.text = data.nama;
                        selectedOption.value = data.nama;
                        selectedOption.setAttribute('data-alamat', data.alamat || '');
                        
                        // Trigger change so Select2 updates
                        $(select).trigger('change');
                    }
                }
                
                // Update addresses
                if (lastPopupOpened === 'penerima-edit') {
                    $('textarea[name="alamat_penerima"], textarea[name="alamat_penerima[]"]').val(data.alamat || '');
                } else {
                    $('textarea[name="alamat_pengirim"], textarea[name="alamat_pengirim[]"]').val(data.alamat || '');
                }
            } else {
                // Determine which one should be selected
                const selectAsPenerima = lastPopupOpened === 'penerima';
                const selectAsPengirim = lastPopupOpened === 'pengirim';
                const selectAsNotify = lastPopupOpened === 'notify';
                
                // Create new option for all penerima dropdowns
                const newOptionPenerima = new Option(
                    data.nama,
                    data.nama,
                    selectAsPenerima,
                    selectAsPenerima
                );
                $(newOptionPenerima).attr('data-alamat', data.alamat || '');
                $(newOptionPenerima).attr('data-id', data.id || '');
                $(newOptionPenerima).attr('data-type', data.type || (lastPopupOpened === 'penerima' ? 'penerima' : 'master'));
                
                // Create new option for all pengirim dropdowns
                const newOptionPengirim = new Option(
                    data.nama,
                    data.nama,
                    selectAsPengirim,
                    selectAsPengirim
                );
                $(newOptionPengirim).attr('data-alamat', data.alamat || '');
                $(newOptionPengirim).attr('data-id', data.id || '');
                $(newOptionPengirim).attr('data-type', data.type || (lastPopupOpened === 'pengirim' ? 'pengirim' : 'master'));

                // Create new option for all notify dropdowns
                const newOptionNotify = new Option(
                    data.nama,
                    data.nama,
                    selectAsNotify,
                    selectAsNotify
                );
                $(newOptionNotify).attr('data-alamat', data.alamat || '');
                $(newOptionNotify).attr('data-id', data.id || '');
                $(newOptionNotify).attr('data-type', data.type || 'master');
                
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
                    $('textarea[name="alamat_penerima"], textarea[name="alamat_penerima[]"]').val(data.alamat || '');
                } else if (selectAsPengirim) {
                    $('textarea[name="alamat_pengirim"], textarea[name="alamat_pengirim[]"]').val(data.alamat || '');
                } else if (selectAsNotify) {
                    $('#alamat_notify_party').val(data.alamat || '');
                }
            }
            
            // Show success notification
            const successMsg = document.createElement('div');
            successMsg.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50';
            successMsg.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>${data.nama}</strong> berhasil ${isEdit ? 'diperbarui' : 'ditambahkan'}!</span>
                </div>
            `;
            document.body.appendChild(successMsg);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                successMsg.remove();
            }, 3000);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
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
@endsection