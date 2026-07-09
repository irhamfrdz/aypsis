@extends('layouts.app')

@section('title', 'Edit Tanda Terima - ' . $tandaTerimaTanpaSuratJalan->no_tanda_terima)
@section('page_title', 'Edit Tanda Terima - ' . $tandaTerimaTanpaSuratJalan->no_tanda_terima)

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
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Edit Tanda Terima</h1>
                <p class="text-xs text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->no_tanda_terima }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tanda-terima-tanpa-surat-jalan.show', $tandaTerimaTanpaSuratJalan) }}"
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

            <form action="{{ route('tanda-terima-tanpa-surat-jalan.update', $tandaTerimaTanpaSuratJalan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Informasi Dasar -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_tanda_terima" id="nomor_tanda_terima"
                                   value="{{ old('nomor_tanda_terima', $tandaTerimaTanpaSuratJalan->nomor_tanda_terima) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_tanda_terima') border-red-500 @enderror"
                                   placeholder="TTR-001">
                            @error('nomor_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nomor_surat_jalan_customer" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Surat Jalan Customer
                            </label>
                            <input type="text" name="nomor_surat_jalan_customer" id="nomor_surat_jalan_customer"
                                   value="{{ old('nomor_surat_jalan_customer', $tandaTerimaTanpaSuratJalan->nomor_surat_jalan_customer) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_surat_jalan_customer') border-red-500 @enderror"
                                   placeholder="SJ-CUSTOMER-001">
                            @error('nomor_surat_jalan_customer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="surat_jalan_pabrik" class="block text-sm font-medium text-gray-700 mb-1">
                                Surat Jalan Pabrik
                            </label>
                            <input type="text" name="surat_jalan_pabrik" id="surat_jalan_pabrik"
                                   value="{{ old('surat_jalan_pabrik', $tandaTerimaTanpaSuratJalan->surat_jalan_pabrik) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('surat_jalan_pabrik') border-red-500 @enderror"
                                   placeholder="SJ-PABRIK-001">
                            @error('surat_jalan_pabrik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_surat_jalan_pabrik" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Surat Jalan Pabrik
                            </label>
                            <input type="date" name="tanggal_surat_jalan_pabrik" id="tanggal_surat_jalan_pabrik"
                                   value="{{ old('tanggal_surat_jalan_pabrik', $tandaTerimaTanpaSuratJalan->tanggal_surat_jalan_pabrik ? $tandaTerimaTanpaSuratJalan->tanggal_surat_jalan_pabrik->format('Y-m-d') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_surat_jalan_pabrik') border-red-500 @enderror">
                            @error('tanggal_surat_jalan_pabrik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_tanda_terima" id="tanggal_tanda_terima"
                                   value="{{ old('tanggal_tanda_terima', $tandaTerimaTanpaSuratJalan->tanggal_tanda_terima->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_tanda_terima') border-red-500 @enderror">
                            @error('tanggal_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Term
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="term_id" id="term_id" class="hidden @error('term_id') border-red-500 @enderror">
                                    <option value="">Pilih Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id', $tandaTerimaTanpaSuratJalan->term_id) == $term->id ? 'selected' : '' }}>
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
                            <label for="penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Penerima <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select name="nama_penerima" id="penerima" class="select2-penerima flex-1" required>
                                    <option value="">-- Pilih Penerima --</option>
                                    @foreach($masterPengirimPenerima as $item)
                                        <option value="{{ $item->nama }}" 
                                                data-alamat="{{ $item->alamat ?? '' }}"
                                                {{ old('nama_penerima', $tandaTerimaTanpaSuratJalan->nama_penerima ?? $tandaTerimaTanpaSuratJalan->penerima) == $item->nama ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openPenerimaPopup()" 
                                        class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @error('nama_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Pengirim <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select name="nama_pengirim" id="pengirim" class="select2-pengirim flex-1" required>
                                    <option value="">-- Pilih Pengirim --</option>
                                    @foreach($masterPengirimPenerima as $item)
                                        <option value="{{ $item->nama }}" 
                                                data-alamat="{{ $item->alamat ?? '' }}"
                                                {{ old('nama_pengirim', $tandaTerimaTanpaSuratJalan->nama_pengirim ?? $tandaTerimaTanpaSuratJalan->pengirim) == $item->nama ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openPengirimPopup()" 
                                        class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @error('nama_pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 2: PIC dan Telepon -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="pic_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                PIC Penerima
                            </label>
                            <input type="text" name="pic_penerima" id="pic_penerima" 
                                   value="{{ old('pic_penerima', $tandaTerimaTanpaSuratJalan->pic_penerima ?? $tandaTerimaTanpaSuratJalan->pic) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Nama PIC Penerima">
                        </div>
                        <div>
                            <label for="pic_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                PIC Pengirim
                            </label>
                            <input type="text" name="pic_pengirim" id="pic_pengirim" 
                                   value="{{ old('pic_pengirim', $tandaTerimaTanpaSuratJalan->pic_pengirim) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Nama PIC Pengirim">
                        </div>
                    </div>

                    <!-- Baris 3: Telepon -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="telepon_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                Telepon Penerima
                            </label>
                            <input type="text" name="telepon_penerima" id="telepon_penerima" 
                                   value="{{ old('telepon_penerima', $tandaTerimaTanpaSuratJalan->telepon_penerima ?? $tandaTerimaTanpaSuratJalan->telepon) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Nomor telepon penerima">
                        </div>
                        <div>
                            <label for="telepon_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                Telepon Pengirim
                            </label>
                            <input type="text" name="telepon_pengirim" id="telepon_pengirim" 
                                   value="{{ old('telepon_pengirim', $tandaTerimaTanpaSuratJalan->telepon_pengirim) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Nomor telepon pengirim">
                        </div>
                    </div>

                    <!-- Baris 4: Alamat -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Penerima
                            </label>
                            <textarea name="alamat_penerima" id="alamat_penerima" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Alamat lengkap penerima">{{ old('alamat_penerima', $tandaTerimaTanpaSuratJalan->alamat_penerima) }}</textarea>
                        </div>
                        <div>
                            <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Pengirim
                            </label>
                            <textarea name="alamat_pengirim" id="alamat_pengirim" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Alamat lengkap pengirim">{{ old('alamat_pengirim', $tandaTerimaTanpaSuratJalan->alamat_pengirim) }}</textarea>
                        </div>
                    </div>

                    <!-- Baris 5: Notify Party -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="notify_party" class="block text-sm font-medium text-gray-700 mb-1">
                                Notify Party
                            </label>
                            <div class="flex gap-2">
                                <select name="notify_party" id="notify_party"
                                        class="select2-notify flex-1">
                                    <option value="">-- Pilih Notify Party --</option>
                                    @php
                                        $notifyInMaster = false;
                                        $currentNotify = old('notify_party', $tandaTerimaTanpaSuratJalan->notify_party);
                                        if ($currentNotify) {
                                            foreach($masterPengirimPenerima as $item) {
                                                if ($item->nama === $currentNotify) {
                                                    $notifyInMaster = true;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($currentNotify && !$notifyInMaster)
                                        <option value="{{ $currentNotify }}" selected>{{ $currentNotify }}</option>
                                    @endif
                                    @foreach($masterPengirimPenerima as $item)
                                        <option value="{{ $item->nama }}" 
                                                data-alamat="{{ $item->alamat }}"
                                                {{ $currentNotify == $item->nama ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openNotifyPopup()" 
                                        class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @error('notify_party')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="alamat_notify_party" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Notify Party
                            </label>
                            <textarea name="alamat_notify_party" id="alamat_notify_party" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat_notify_party') border-red-500 @enderror"
                                      placeholder="Alamat lengkap Notify Party">{{ old('alamat_notify_party', $tandaTerimaTanpaSuratJalan->alamat_notify_party) }}</textarea>
                            @error('alamat_notify_party')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Barang (LCL-friendly: array-based, same as create.blade.php) -->
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Dimensi dan Volume
                        </h3>
                        <button type="button"
                                id="add-dimensi-btn-edit"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Dimensi
                        </button>
                    </div>

                    <div id="dimensi-container-edit">
                        {{-- Existing LCL rows (populate from model dimensiItems) --}}
                        @php
                            $dimensiItems = $tandaTerimaTanpaSuratJalan->dimensiItems ?? [];
                            if ($dimensiItems instanceof \Illuminate\Database\Eloquent\Collection) {
                                $dimensiItems = $dimensiItems->toArray();
                            }
                            $initialDimensiRows = old('nama_barang') ? array_map(null, old('nama_barang'), old('jumlah'), old('satuan'), old('panjang'), old('lebar'), old('tinggi'), old('meter_kubik'), old('tonase')) : null;
                        @endphp

                        @if(!empty($initialDimensiRows))
                            @foreach(old('nama_barang') as $idx => $nm)
                                <div class="dimensi-row-edit mb-4 pb-4 border-b border-purple-200 relative">
                                    <button type="button" class="remove-dimensi-btn-edit absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                                            <input type="text" name="nama_barang[]" class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required value="{{ old('nama_barang.'.$idx) }}" oninput="toggleUkuranField(this)">
                                        </div>
                                        <div class="ukuran-container hidden">
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Ukuran</label>
                                            <input type="text" name="ukuran[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 40x40" value="{{ old('ukuran.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label>
                                            <input type="number" name="jumlah[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="{{ old('jumlah.'.$idx, 1) }}" required oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label>
                                            <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="{{ old('satuan.'.$idx, 'unit') }}" required>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                            <input type="number" name="panjang[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 1.5" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('panjang.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                            <input type="number" name="lebar[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 1.2" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('lebar.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                            <input type="number" name="tinggi[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 2.0" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('tinggi.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                                            <input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="Auto" min="0" step="0.001" readonly value="{{ old('meter_kubik.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                                            <input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" value="{{ old('tonase.'.$idx) }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @elseif(!empty($dimensiItems))
                            @foreach($dimensiItems as $item)
                                <div class="dimensi-row-edit mb-4 pb-4 border-b border-purple-200 relative">
                                    <button type="button" class="remove-dimensi-btn-edit absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                                            <input type="text" name="nama_barang[]" class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required value="{{ old('nama_barang', $item['nama_barang'] ?? $item->nama_barang ?? '') }}" oninput="toggleUkuranField(this)">
                                        </div>
                                        <div class="ukuran-container hidden">
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Ukuran</label>
                                            <input type="text" name="ukuran[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 40x40" value="{{ old('ukuran', $item['ukuran'] ?? $item->ukuran ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label>
                                            <input type="number" name="jumlah[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="{{ old('jumlah', $item['jumlah'] ?? $item->jumlah ?? 1) }}" required oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label>
                                            <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="{{ old('satuan', $item['satuan'] ?? $item->satuan ?? 'unit') }}" required>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                            <input type="number" name="panjang[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 1.5" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('panjang', $item['panjang'] ?? $item->panjang ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                            <input type="number" name="lebar[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 1.2" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('lebar', $item['lebar'] ?? $item->lebar ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                            <input type="number" name="tinggi[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 2.0" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('tinggi', $item['tinggi'] ?? $item->tinggi ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                                            <input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="Auto" min="0" step="0.001" readonly value="{{ old('meter_kubik', $item['meter_kubik'] ?? $item->meter_kubik ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                                            <input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" value="{{ old('tonase', $item['tonase'] ?? $item->tonase ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Keep an empty single initial row to match create behavior -->
                            <div class="dimensi-row-edit mb-4 pb-4 border-b border-purple-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama_barang[]" class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required oninput="toggleUkuranField(this)">
                                    </div>
                                    <div class="ukuran-container hidden">
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Ukuran</label>
                                        <input type="text" name="ukuran[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 40x40">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label>
                                        <input type="number" name="jumlah[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="1" required oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label>
                                        <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="unit" required>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                        <input type="number" name="panjang[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 1.5" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                        <input type="number" name="lebar[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 1.2" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m) <span class="text-xs text-gray-400">*dalam meter</span></label>
                                        <input type="number" name="tinggi[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 2.0" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                                        <input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="Auto" min="0" step="0.001" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                                        <input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Hidden fields for backward compatibility (scalar) -->
                    <input type="hidden" name="jenis_barang" id="jenis_barang" value="{{ old('jenis_barang', $tandaTerimaTanpaSuratJalan->jenis_barang) }}">
                    <input type="hidden" name="jumlah_barang" id="jumlah_barang" value="{{ old('jumlah_barang', $tandaTerimaTanpaSuratJalan->jumlah_barang) }}">
                    <input type="hidden" name="satuan_barang" id="satuan_barang" value="{{ old('satuan_barang', $tandaTerimaTanpaSuratJalan->satuan_barang) }}">
                    <input type="hidden" name="berat" id="berat" value="{{ old('berat', $tandaTerimaTanpaSuratJalan->berat) }}">
                    <input type="hidden" name="satuan_berat" id="satuan_berat" value="{{ old('satuan_berat', $tandaTerimaTanpaSuratJalan->satuan_berat) }}">
                    <input type="hidden" name="keterangan_barang" id="keterangan_barang" value="{{ old('keterangan_barang', $tandaTerimaTanpaSuratJalan->keterangan_barang) }}">
                    <!-- End LCL-style Informasi Barang -->
                </div>

                <!-- Informasi Tujuan -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tujuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="tujuan_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Pengambilan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tujuan_pengambilan" id="tujuan_pengambilan" value="{{ old('tujuan_pengambilan', $tandaTerimaTanpaSuratJalan->tujuan_pengambilan) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengambilan') border-red-500 @enderror"
                                   placeholder="Lokasi pengambilan barang">
                            @error('tujuan_pengambilan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Pengiriman <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tujuan_pengiriman" id="tujuan_pengiriman" value="{{ old('tujuan_pengiriman', $tandaTerimaTanpaSuratJalan->tujuan_pengiriman) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengiriman') border-red-500 @enderror"
                                   placeholder="Lokasi tujuan pengiriman">
                            @error('tujuan_pengiriman')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Transportasi -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Transportasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Supir <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="supir" id="supir" class="hidden @error('supir') border-red-500 @enderror" required>
                                    <option value="">Pilih Supir</option>
                                    @foreach($supirs as $supir)
                                        <option value="{{ $supir->nama_lengkap }}" {{ old('supir', $tandaTerimaTanpaSuratJalan->supir) == $supir->nama_lengkap ? 'selected' : '' }}>
                                            {{ $supir->nama_panggilan ?? $supir->nama_lengkap }}
                                        </option>
                                    @endforeach
                                    @if(old('supir', $tandaTerimaTanpaSuratJalan->supir) && !in_array(old('supir', $tandaTerimaTanpaSuratJalan->supir), $supirs->pluck('nama_lengkap')->toArray()))
                                        <option value="{{ old('supir', $tandaTerimaTanpaSuratJalan->supir) }}" selected>{{ old('supir', $tandaTerimaTanpaSuratJalan->supir) }}</option>
                                    @endif
                                </select>

                                <!-- Search input -->
                                <input type="text" id="supirSearch"
                                       placeholder="Cari atau pilih supir..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="supirDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('karyawan.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Supir Baru
                                        </a>
                                    </div>
                                    @foreach($supirs as $supir)
                                        <div class="supir-option px-3 py-2 hover:bg-gray-50 cursor-pointer"
                                             data-value="{{ $supir->nama_lengkap }}"
                                             data-text="{{ $supir->nama_lengkap }}">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $supir->nama_lengkap }}</span>
                                                @if($supir->nik)
                                                    <span class="text-xs text-gray-500">NIK: {{ $supir->nik }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
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
                                <!-- Hidden select for form submission -->
                                <select name="kenek" id="kenek" class="hidden @error('kenek') border-red-500 @enderror">
                                    <option value="">Pilih Kenek</option>
                                    @foreach($kranis as $krani)
                                        <option value="{{ $krani->nama_lengkap }}" {{ old('kenek', $tandaTerimaTanpaSuratJalan->kenek) == $krani->nama_lengkap ? 'selected' : '' }}>
                                            {{ $krani->nama_lengkap }}
                                        </option>
                                    @endforeach
                                    @if(old('kenek', $tandaTerimaTanpaSuratJalan->kenek) && !in_array(old('kenek', $tandaTerimaTanpaSuratJalan->kenek), $kranis->pluck('nama_lengkap')->toArray()))
                                        <option value="{{ old('kenek', $tandaTerimaTanpaSuratJalan->kenek) }}" selected>{{ old('kenek', $tandaTerimaTanpaSuratJalan->kenek) }}</option>
                                    @endif
                                </select>

                                <!-- Search input -->
                                <input type="text" id="kenekSearch"
                                       placeholder="Cari atau pilih kenek..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('kenek') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="kenekDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('karyawan.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Kenek Baru
                                        </a>
                                    </div>
                                    @foreach($kranis as $krani)
                                        <div class="kenek-option px-3 py-2 hover:bg-gray-50 cursor-pointer"
                                             data-value="{{ $krani->nama_lengkap }}"
                                             data-text="{{ $krani->nama_lengkap }}">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $krani->nama_lengkap }}</span>
                                                @if($krani->nik)
                                                    <span class="text-xs text-gray-500">NIK: {{ $krani->nik }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('kenek')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Plat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_plat" id="no_plat" value="{{ old('no_plat', $tandaTerimaTanpaSuratJalan->no_plat) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror"
                                   placeholder="Nomor plat kendaraan">
                            @error('no_plat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe Kontainer
                            </label>
                                <select name="_tipe_kontainer_disabled" id="tipe_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 cursor-not-allowed @error('tipe_kontainer') border-red-500 @enderror"
                                    disabled onchange="handleTipeKontainerChange()">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="fcl" {{ old('tipe_kontainer', $tandaTerimaTanpaSuratJalan->tipe_kontainer) == 'fcl' ? 'selected' : '' }}>FCL</option>
                                <option value="lcl" {{ old('tipe_kontainer', $tandaTerimaTanpaSuratJalan->tipe_kontainer) == 'lcl' ? 'selected' : '' }}>LCL</option>
                                <option value="cargo" {{ old('tipe_kontainer', $tandaTerimaTanpaSuratJalan->tipe_kontainer) == 'cargo' ? 'selected' : '' }}>Cargo</option>
                            </select>
                            {{-- Keep a hidden field to submit tipe_kontainer value since disabled selects are not submitted --}}
                            <input type="hidden" name="tipe_kontainer" value="{{ old('tipe_kontainer', $tandaTerimaTanpaSuratJalan->tipe_kontainer) }}">
                            @error('tipe_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Kontainer Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div id="size_kontainer_field">
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Size Kontainer
                            </label>
                            <select name="size_kontainer" id="size_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('size_kontainer') border-red-500 @enderror">
                                <option value="">-- Pilih Size --</option>
                                <option value="20 ft" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '20 ft' ? 'selected' : '' }}>20 ft</option>
                                <option value="40 ft" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '40 ft' ? 'selected' : '' }}>40 ft</option>
                                <option value="40 HC" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '40 HC' ? 'selected' : '' }}>40 HC (High Cube)</option>
                                <option value="45 ft" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '45 ft' ? 'selected' : '' }}>45 ft</option>
                                <option value="53 ft" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '53 ft' ? 'selected' : '' }}>53 ft</option>
                                <option value="other" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == 'other' ? 'selected' : '' }}>Lainnya</option>
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
                                <input type="hidden" name="no_kontainer" id="no_kontainer" value="{{ old('no_kontainer', $tandaTerimaTanpaSuratJalan->no_kontainer) }}" required>
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
                            <input type="text" name="no_seal" id="no_seal" value="{{ old('no_seal', $tandaTerimaTanpaSuratJalan->no_seal) }}" required
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
                            <input type="date" name="tanggal_seal" id="tanggal_seal" value="{{ old('tanggal_seal', $tandaTerimaTanpaSuratJalan->tanggal_seal ? $tandaTerimaTanpaSuratJalan->tanggal_seal->format('Y-m-d') : '') }}"
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
                                  placeholder="Catatan atau informasi tambahan (opsional)">{{ old('catatan', $tandaTerimaTanpaSuratJalan->catatan) }}</textarea>
                    </div>
                </div>

                <!-- Upload Gambar -->
                @php
                    $__gambarArray = $tandaTerimaTanpaSuratJalan->gambar_tanda_terima;
                    if (is_string($__gambarArray)) {
                        $__decoded = json_decode($__gambarArray, true);
                        $__gambarArray = is_array($__decoded) ? $__decoded : [];
                    }
                    if (!is_array($__gambarArray)) {
                        $__gambarArray = [];
                    }
                @endphp
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gambar Tanda Terima</h3>

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
                    <div id="image-preview-container" class="mt-4 @if(empty($__gambarArray)) hidden @endif">
                        <label class="block text-xs font-medium text-gray-500 mb-2">
                            <i class="fas fa-eye mr-1 text-green-600"></i>
                            Preview Gambar
                        </label>
                        <div id="image-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            {{-- Existing images previewed here --}}
                            @foreach($__gambarArray as $index => $imagePath)
                                @php $imgUrl = asset('storage/' . ltrim($imagePath, '/')); @endphp
                                <div class="relative bg-gray-50 rounded-lg border border-gray-200 p-2 image-preview-item" data-is-existing="1" data-path="{{ $imagePath }}">
                                    <img src="{{ $imgUrl }}" alt="Gambar {{ $index + 1 }}" class="object-cover w-full h-28 rounded"/>
                                    <div class="flex justify-between items-center mt-2">
                                        <div class="text-xs text-gray-600 truncate">Gambar {{ $index + 1 }}</div>
                                        <div class="flex gap-2 items-center">
                                            <a href="{{ $imgUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-2 py-1 text-xs bg-white border rounded text-gray-700 hover:bg-gray-50" download>
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M21 12v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8"></path></svg>
                                                Unduh
                                            </a>
                                            <button type="button" onclick="removeExistingImage(this, '{{ $imagePath }}')" class="inline-flex items-center px-2 py-1 text-xs bg-red-50 border rounded text-red-700 hover:bg-red-100">Hapus</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="existing_images[]" value="{{ $imagePath }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.show', $tandaTerimaTanpaSuratJalan) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Tanda Terima
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
                                <div class="absolute -bottom-1.5 -left-1.5 w-3 h-3 bg-indigo-500 border border-white rounded-full cursor-nwse-resize shadow-md" data-handle="sw"></div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Wrap everything in jQuery ready to ensure jQuery and Select2 are loaded
    jQuery(document).ready(function($) {
        console.log('jQuery loaded:', typeof $ !== 'undefined');
        console.log('Select2 loaded:', typeof $.fn.select2 !== 'undefined');
        
        // Initialize Select2 only if library is available
        if (typeof $.fn.select2 !== 'undefined') {
            // Initialize Select2 for penerima and pengirim
            $('.select2-penerima').select2({
                placeholder: '-- Pilih Penerima --',
                allowClear: true,
                width: '100%'
            });

            $('.select2-pengirim').select2({
                placeholder: '-- Pilih Pengirim --',
                allowClear: true,
                width: '100%'
            });

            // Auto-fill alamat when penerima is selected
            $('.select2-penerima').on('select2:select', function (e) {
                const alamat = $(this).find(':selected').data('alamat');
                if (alamat) {
                    $('#alamat_penerima').val(alamat);
                }
            });

            // Auto-fill alamat when pengirim is selected
            $('.select2-pengirim').on('select2:select', function (e) {
                const alamat = $(this).find(':selected').data('alamat');
                if (alamat) {
                    $('#alamat_pengirim').val(alamat);
                }
            });

            $('.select2-notify').select2({
                placeholder: '-- Pilih Notify Party --',
                allowClear: true,
                width: '100%',
                tags: true
            });

            // Auto-fill alamat when notify party is selected
            $('.select2-notify').on('select2:select', function (e) {
                const alamat = $(this).find(':selected').data('alamat');
                if (alamat) {
                    $('#alamat_notify_party').val(alamat);
                }
            });

            // Clear alamat when notify party is cleared
            $('.select2-notify').on('select2:clear', function (e) {
                $('#alamat_notify_party').val('');
            });
        } else {
            console.error('Select2 library not loaded!');
        }

        // Calculate meter kubik on page load if values exist
        calculateMeterKubik();

        // Initialize term dropdown
        initializeTermDropdown();

        // Initialize supir dropdown
        initializeSupirDropdown();

        // Initialize kenek dropdown
        initializeKenekDropdown();

        // Handle tipe kontainer on page load
        handleTipeKontainerChange();
        // Initialize per-row volume calculation for existing dimensi rows
        const initialDimensiRows = document.querySelectorAll('#dimensi-container-edit .dimensi-row-edit');
        initialDimensiRows.forEach(row => calculateVolumeEdit(row));
    }); // End of jQuery ready

    function handleTipeKontainerChange() {
        const tipeKontainerEl = document.getElementById('tipe_kontainer');
        if (!tipeKontainerEl) return; // nothing to do if element doesn't exist
        const tipeKontainer = tipeKontainerEl.value;
        
        console.log('Tipe Kontainer:', tipeKontainer);
        
        const sizeKontainerField = document.getElementById('size_kontainer_field');
        const noKontainerField = document.getElementById('no_kontainer_field');
        const noSealField = document.getElementById('no_seal_field');
        const tanggalSealField = document.getElementById('tanggal_seal_field');
        const noKontainerInput = document.getElementById('no_kontainer');
        const noSealInput = document.getElementById('no_seal');
        
        console.log('Elements found:', {
            sizeKontainerField: !!sizeKontainerField,
            noKontainerField: !!noKontainerField,
            noSealField: !!noSealField,
            tanggalSealField: !!tanggalSealField
        });
        
        if (tipeKontainer === 'cargo') {
            console.log('Hiding kontainer fields for cargo');
            // Hide kontainer fields for cargo
            if (sizeKontainerField) sizeKontainerField.style.display = 'none';
            if (noKontainerField) noKontainerField.style.display = 'none';
            if (noSealField) noSealField.style.display = 'none';
            if (tanggalSealField) tanggalSealField.style.display = 'none';
            // Clear kontainer fields when cargo is selected and remove required attribute
            if (noKontainerInput) noKontainerInput.value = 'CARGO';
            const sizeKontainerInput = document.getElementById('size_kontainer');
            if (sizeKontainerInput) sizeKontainerInput.value = '';
            if (noSealInput) noSealInput.value = '';
            const tanggalSealInput = document.getElementById('tanggal_seal');
            if (tanggalSealInput) tanggalSealInput.value = '';
            if (noKontainerInput) noKontainerInput.removeAttribute('required');
            if (noSealInput) noSealInput.removeAttribute('required');
        } else {
            console.log('Showing kontainer fields for FCL/LCL');
            // Show kontainer fields for FCL and LCL
            if (sizeKontainerField) sizeKontainerField.style.display = 'block';
            if (noKontainerField) noKontainerField.style.display = 'block';
            if (noSealField) noSealField.style.display = 'block';
            if (tanggalSealField) tanggalSealField.style.display = 'block';
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
    // Ensure manual value is submitted if manual option chosen (edit)
    const editForm = document.querySelector('form');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
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
            // Update hidden legacy fields from LCL rows
            try { updateHiddenBarangFields(); } catch (err) { /* ignore */ }
        });
    }

    function calculateMeterKubik() {
        // This function is not needed for edit page since we use array-based inputs
        // Volume calculation is handled by calculateVolumeEdit() for each row
        return;
    }

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

    function initializeSupirDropdown() {
        const searchInput = document.getElementById('supirSearch');
        const dropdown = document.getElementById('supirDropdown');
        const hiddenSelect = document.getElementById('supir');
        const options = document.querySelectorAll('.supir-option');

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

            // Update hidden select with current input value for custom entries
            hiddenSelect.value = this.value;

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

        // Set initial value if exists
        const selectedOption = hiddenSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent;
        } else if (hiddenSelect.value) {
            // Handle custom supir value that might not be in the dropdown options
            const customSupir = hiddenSelect.value;
            // Check if this value exists in any option
            const existingOption = Array.from(hiddenSelect.options).find(opt => opt.value === customSupir);
            if (!existingOption) {
                // This is a custom value, display it in the search input
                searchInput.value = customSupir;
            }
        }
    }

    function initializeKenekDropdown() {
        const searchInput = document.getElementById('kenekSearch');
        const dropdown = document.getElementById('kenekDropdown');
        const hiddenSelect = document.getElementById('kenek');
        const options = document.querySelectorAll('.kenek-option');

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

            // Update hidden select with current input value for custom entries
            hiddenSelect.value = this.value;

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

        // Set initial value if exists
        const selectedOption = hiddenSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent;
        } else if (hiddenSelect.value) {
            // Handle custom kenek value that might not be in the dropdown options
            const customKenek = hiddenSelect.value;
            // Check if this value exists in any option
            const existingOption = Array.from(hiddenSelect.options).find(opt => opt.value === customKenek);
            if (!existingOption) {
                // This is a custom value, display it in the search input
                searchInput.value = customKenek;
            }
        }
    }

    let lastPopupOpened = '';

    // Function to open penerima popup window
    function openPenerimaPopup() {
        lastPopupOpened = 'penerima';
        const width = 600;
        const height = 500;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        
        const popup = window.open(
            '{{ route("tanda-terima.penerima.create", [], false) }}',
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
        lastPopupOpened = 'pengirim';
        const width = 600;
        const height = 500;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        
        const popup = window.open(
            '{{ route("tanda-terima.penerima.create", [], false) }}',
            'TambahPengirim',
            `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
        
        if (popup) {
            popup.focus();
        } else {
            alert('Pop-up diblokir! Silakan izinkan pop-up untuk situs ini.');
        }
    }

    // Function to open notify party popup window
    function openNotifyPopup() {
        lastPopupOpened = 'notify';
        const width = 600;
        const height = 500;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        
        const popup = window.open(
            '{{ route("tanda-terima.penerima.create", [], false) }}',
            'TambahNotifyParty',
            `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
        
        if (popup) {
            popup.focus();
        } else {
            alert('Pop-up diblokir! Silakan izinkan pop-up untuk situs ini.');
        }
    }

    // Listen for message from popup when new penerima/pengirim/notify is added
    window.addEventListener('message', function(event) {
        // Verify origin for security
        if (event.origin !== window.location.origin) return;
        
        if (event.data.type === 'penerimaAdded') {
            const newData = event.data.penerima;
            
            // Add to penerima, pengirim, and notify select (same data source)
            const penerimaSelect = jQuery('.select2-penerima');
            const pengirimSelect = jQuery('.select2-pengirim');
            const notifySelect = jQuery('.select2-notify');
            
            // Determine which one should be selected
            const selectAsPenerima = lastPopupOpened === 'penerima';
            const selectAsPengirim = lastPopupOpened === 'pengirim';
            const selectAsNotify = lastPopupOpened === 'notify';
            
            // Add new option to penerima
            const penerimaOption = new Option(newData.nama, newData.nama, selectAsPenerima, selectAsPenerima);
            jQuery(penerimaOption).attr('data-alamat', newData.alamat || '');
            penerimaSelect.append(penerimaOption);
            
            // Add new option to pengirim  
            const pengirimOption = new Option(newData.nama, newData.nama, selectAsPengirim, selectAsPengirim);
            jQuery(pengirimOption).attr('data-alamat', newData.alamat || '');
            pengirimSelect.append(pengirimOption);

            // Add new option to notify
            const notifyOption = new Option(newData.nama, newData.nama, selectAsNotify, selectAsNotify);
            jQuery(notifyOption).attr('data-alamat', newData.alamat || '');
            notifySelect.append(notifyOption);
            
            // Trigger select2 change and auto-fill alamat for the active one
            if (selectAsPenerima) {
                penerimaSelect.trigger('change');
                jQuery('#alamat_penerima').val(newData.alamat || '');
            } else if (selectAsPengirim) {
                pengirimSelect.trigger('change');
                jQuery('#alamat_pengirim').val(newData.alamat || '');
            } else if (selectAsNotify) {
                notifySelect.trigger('change');
                jQuery('#alamat_notify_party').val(newData.alamat || '');
            }
            
            console.log('✓ New ' + lastPopupOpened + ' added:', newData.nama);
        }
    });

    function initializeNoKontainerDropdown() {
        const searchInput = document.getElementById('noKontainerSearch');
        const dropdown = document.getElementById('noKontainerDropdown');
        const hiddenInput = document.getElementById('no_kontainer');
        const manualField = document.getElementById('no_kontainer_manual');
        const options = document.querySelectorAll('.no-kontainer-option');

        if (!searchInput || !dropdown || !hiddenInput) {
            console.error('Required elements not found for no kontainer dropdown');
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
                const form = document.querySelector('form[enctype="multipart/form-data"]');
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
            const input = document.getElementById('gambar_tanda_terima');
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
                const form = document.querySelector('form[enctype="multipart/form-data"]');
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

        // Dimensi (LCL-like) functions for edit
        function calculateVolumeEdit(rowElement) {
            if (!rowElement) return;
            const panjangInput = rowElement.querySelector('input[name="panjang[]"]');
            const lebarInput = rowElement.querySelector('input[name="lebar[]"]');
            const tinggiInput = rowElement.querySelector('input[name="tinggi[]"]');
            const jumlahInput = rowElement.querySelector('input[name="jumlah[]"]');
            const volumeInput = rowElement.querySelector('input[name="meter_kubik[]"]');

            const panjang = parseFloat(panjangInput?.value) || 0;
            const lebar = parseFloat(lebarInput?.value) || 0;
            const tinggi = parseFloat(tinggiInput?.value) || 0;
            const jumlah = jumlahInput ? (parseInt(jumlahInput.value, 10) || 1) : 1;

            if (panjang > 0 && lebar > 0 && tinggi > 0) {
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
                
                volumeInput.value = volume.toFixed(3);
            } else {
                volumeInput.value = '';
            }
            updateHiddenBarangFields();
        }

        // Add and remove dimensi row handlers
        document.addEventListener('click', function(e) {
            if (e.target && e.target.closest('#add-dimensi-btn-edit')) {
                const container = document.getElementById('dimensi-container-edit');
                if (!container) return;
                const newRow = document.createElement('div');
                newRow.className = 'dimensi-row-edit mb-4 pb-4 border-b border-purple-200 relative';
                newRow.innerHTML = `
                    <button type="button" class="remove-dimensi-btn-edit absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_barang[]" class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required oninput="toggleUkuranField(this)">
                        </div>
                        <div class="ukuran-container hidden">
                            <label class="block text-xs font-medium text-gray-500 mb-2">Ukuran</label>
                            <input type="text" name="ukuran[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 40x40">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="1" required oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="unit" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m) <span class="text-xs text-gray-400">*dalam meter</span></label><input type="number" name="panjang[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 1.5" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))"></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m) <span class="text-xs text-gray-400">*dalam meter</span></label><input type="number" name="lebar[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 1.2" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))"></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m) <span class="text-xs text-gray-400">*dalam meter</span></label><input type="number" name="tinggi[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: 2.0" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))"></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label><input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="Auto" min="0" step="0.001" readonly></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label><input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001"></div>
                    </div>
                `;
                container.appendChild(newRow);
            }
            if (e.target && e.target.closest('.remove-dimensi-btn-edit')) {
                const row = e.target.closest('.dimensi-row-edit');
                if (row) row.remove();
                updateHiddenBarangFields();
            }
        });

        // Update hidden legacy fields (jenis_barang, jumlah_barang, satuan_barang, keterangan_barang)
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
                    const totalJumlah = jumlahVals.length ? jumlahVals.reduce((a, b) => a + b, 0) : parseInt(jumlahEl.value, 10) || 1;
                    jumlahEl.value = totalJumlah;
                }
                if (satuanEl) satuanEl.value = satuanVals.length ? satuanVals.join(',') : (satuanEl.value || 'unit');
                if (keteranganEl && !keteranganEl.value && namaVals.length) {
                    keteranganEl.value = keteranganEl.value || '';
                }
                if (beratEl && !beratEl.value) {
                    beratEl.value = beratEl.value || '';
                }
                if (satuanBeratEl && !satuanBeratEl.value) {
                    satuanBeratEl.value = satuanBeratEl.value || 'kg';
                }
            } catch (err) {
                // ignore errors
            }
        }

        // Attach event to initial dimensi inputs
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize existing images on page load
            initializeExistingImages();

            const container = document.getElementById('dimensi-container-edit');
            if (container) {
                container.querySelectorAll('.dimensi-input-edit').forEach(inp => {
                    inp.addEventListener('input', function() {
                        const row = this.closest('.dimensi-row-edit');
                        if (row) calculateVolumeEdit(row);
                    });
                });
                // When tonase/jumlah/satuan/nama input change update hidden fields
                container.querySelectorAll('input[name="tonase[]"], input[name="jumlah[]"], input[name="satuan[]"], input[name="nama_barang[]"]').forEach(inp => {
                    inp.addEventListener('input', function() {
                        updateHiddenBarangFields();
                        if (this.name === 'jumlah[]') {
                            const row = this.closest('.dimensi-row-edit');
                            if (row) calculateVolumeEdit(row);
                        }
                    });
                });
            }
        });

        // Handle updates from dynamically added fields (delegated)
        document.addEventListener('input', function(e) {
            if (e.target && (e.target.matches('input[name="nama_barang[]"]') || e.target.matches('input[name="jumlah[]"]') || e.target.matches('input[name="satuan[]"]') || e.target.matches('input[name="tonase[]"]'))) {
                updateHiddenBarangFields();
            }
            // If jumlah changed, recalculate volume for that row (handles dynamically added rows)
            if (e.target && e.target.matches && e.target.matches('input[name="jumlah[]"]')) {
                const row = e.target.closest('.dimensi-row-edit');
                if (row) calculateVolumeEdit(row);
            }
        });

        // Initialize existing rows for ukuran visibility
        document.querySelectorAll('.nama-barang-input').forEach(input => {
            toggleUkuranField(input);
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
