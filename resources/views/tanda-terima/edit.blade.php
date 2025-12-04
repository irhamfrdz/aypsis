@extends('layouts.app')

@section('title', 'Edit Tanda Terima')

@section('content')
@php
    // Parse container data from tanda terima
    $nomorKontainerArray = [];
    
    if (!empty($tandaTerima->no_kontainer)) {
        $nomorKontainerArray = array_map('trim', explode(',', $tandaTerima->no_kontainer));
    }
    
    $jumlahKontainer = $tandaTerima->jumlah_kontainer ?: 1;
    
    // Parse dimensi items if exists - check both dimensi_items and dimensi_details
    $dimensiItems = [];
    
    // First try dimensi_items (from update)
    if ($tandaTerima->dimensi_items) {
        if (is_string($tandaTerima->dimensi_items)) {
            $dimensiItems = json_decode($tandaTerima->dimensi_items, true) ?? [];
        } elseif (is_array($tandaTerima->dimensi_items)) {
            $dimensiItems = $tandaTerima->dimensi_items;
        }
    }
    
    // If dimensi_items is empty, try dimensi_details (from create)
    if (empty($dimensiItems) && $tandaTerima->dimensi_details) {
        if (is_string($tandaTerima->dimensi_details)) {
            $dimensiItems = json_decode($tandaTerima->dimensi_details, true) ?? [];
        } elseif (is_array($tandaTerima->dimensi_details)) {
            $dimensiItems = $tandaTerima->dimensi_details;
        }
    }
@endphp

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('tanda-terima.index') }}" class="hover:text-blue-600 transition">Tanda Terima</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li>
                <a href="{{ route('tanda-terima.show', $tandaTerima) }}" class="hover:text-blue-600 transition">Detail</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Edit</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Tanda Terima</h1>
                <p class="text-gray-600 mt-1">No. Tanda Terima: <span class="font-semibold">{{ $tandaTerima->nomor_tanda_terima ?? '-' }}</span></p>
                @if($tandaTerima->suratJalan)
                    <p class="text-gray-600 text-sm">No. Surat Jalan: <span class="font-semibold">{{ $tandaTerima->suratJalan->no_surat_jalan }}</span></p>
                @endif
            </div>
            @if($sudahMasukBl)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2">
                <div class="flex items-center text-yellow-800">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-sm font-medium">Sudah Masuk BL - Edit Terbatas</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Data Tanda Terima</h2>
            <p class="text-sm text-gray-600 mt-1">Update informasi tanda terima</p>
        </div>

        <form action="{{ route('tanda-terima.update', $tandaTerima) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            {{-- General error alert --}}
            @if(session('error'))
                <div class="server-error mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-800">
                    <div class="font-semibold">Gagal mengupdate Tanda Terima</div>
                    <p class="mt-1">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="validation-errors mb-4 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-sm text-yellow-800">
                    <div class="font-semibold">Validasi gagal. Silakan periksa field berikut:</div>
                    <ul class="mt-2 list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">
                <!-- Nomor Tanda Terima -->
                <div>
                    <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Tanda Terima <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nomor_tanda_terima"
                           id="nomor_tanda_terima"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono @error('nomor_tanda_terima') border-red-500 @enderror"
                           placeholder="Masukkan nomor tanda terima"
                           value="{{ old('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima) }}"
                           required>
                    @error('nomor_tanda_terima')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-hash mr-1"></i>Format: Misal TT-2025-001
                    </p>
                </div>

                <!-- Informasi Surat Jalan Section (Read-only) -->
                @if($tandaTerima->suratJalan)
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informasi Surat Jalan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Tanggal Surat Jalan</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm cursor-not-allowed"
                                   value="{{ $tandaTerima->suratJalan->tanggal_surat_jalan?->format('Y-m-d') }}" readonly disabled>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Nomor Surat Jalan</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm font-mono cursor-not-allowed"
                                   value="{{ $tandaTerima->suratJalan->no_surat_jalan }}" readonly disabled>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Data Kontainer Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Data Kontainer</label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nomor_kontainer" class="block text-xs font-medium text-gray-500 mb-2">No. Kontainer</label>
                            <select name="nomor_kontainer[]" id="nomor_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 text-sm font-mono select2-kontainer @error('nomor_kontainer.0') border-red-500 @enderror"
                                    {{ $sudahMasukBl ? 'disabled' : '' }}>
                                <option value="">-- Pilih atau Ketik Nomor Kontainer --</option>
                                @foreach($stockKontainers as $stock)
                                    <option value="{{ $stock->nomor_seri_gabungan }}"
                                            {{ old('nomor_kontainer.0', $tandaTerima->no_kontainer) == $stock->nomor_seri_gabungan ? 'selected' : '' }}>
                                        {{ $stock->nomor_seri_gabungan }} ({{ $stock->ukuran }}ft - {{ $stock->tipe_kontainer }})
                                    </option>
                                @endforeach
                                @if(old('nomor_kontainer.0', $tandaTerima->no_kontainer))
                                    <option value="{{ old('nomor_kontainer.0', $tandaTerima->no_kontainer) }}" selected>
                                        {{ old('nomor_kontainer.0', $tandaTerima->no_kontainer) }}
                                    </option>
                                @endif
                            </select>
                            @error('nomor_kontainer.0')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if($sudahMasukBl)
                                <p class="mt-1 text-xs text-yellow-600"><i class="fas fa-lock mr-1"></i>Tidak dapat diubah (sudah masuk BL)</p>
                            @endif
                        </div>
                        <div>
                            <label for="no_seal" class="block text-xs font-medium text-gray-500 mb-2">No. Seal</label>
                            <input type="text" name="no_seal[]" id="no_seal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 text-sm font-mono @error('no_seal.0') border-red-500 @enderror"
                                   placeholder="Nomor seal"
                                   value="{{ old('no_seal.0', $tandaTerima->no_seal) }}"
                                   {{ $sudahMasukBl ? 'readonly' : '' }}>
                            @error('no_seal.0')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if($sudahMasukBl)
                                <p class="mt-1 text-xs text-yellow-600"><i class="fas fa-lock mr-1"></i>Tidak dapat diubah (sudah masuk BL)</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Data Supir & Kendaraan (if applicable) -->
                @if($tandaTerima->suratJalan)
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Data Supir & Kendaraan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Supir</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm cursor-not-allowed"
                                   value="{{ $tandaTerima->suratJalan->supir ?? '-' }}" readonly disabled>
                        </div>
                        <div>
                            <label for="supir_pengganti" class="block text-xs font-medium text-gray-500 mb-2">Supir Pengganti</label>
                            <select name="supir_pengganti" id="supir_pengganti"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 text-sm select2-supir-pengganti @error('supir_pengganti') border-red-500 @enderror"
                                    {{ $sudahMasukBl ? 'disabled' : '' }}>
                                <option value="">-- Pilih Supir Pengganti --</option>
                                @foreach($supirs as $supir)
                                    <option value="{{ $supir->nama_lengkap }}" data-plat="{{ $supir->plat ?? '' }}"
                                            {{ old('supir_pengganti', $tandaTerima->supir_pengganti) == $supir->nama_lengkap ? 'selected' : '' }}>
                                        {{ $supir->nama_lengkap }}{{ $supir->plat ? ' (' . $supir->plat . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supir_pengganti')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if($sudahMasukBl)
                                <p class="mt-1 text-xs text-yellow-600"><i class="fas fa-lock mr-1"></i>Tidak dapat diubah (sudah masuk BL)</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Kenek</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm cursor-not-allowed"
                                   value="{{ $tandaTerima->suratJalan->kenek ?? '-' }}" readonly disabled>
                        </div>
                        <div>
                            <label for="kenek_pengganti" class="block text-xs font-medium text-gray-500 mb-2">Kenek Pengganti</label>
                            <select name="kenek_pengganti" id="kenek_pengganti"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 text-sm select2-kenek-pengganti @error('kenek_pengganti') border-red-500 @enderror">
                                <option value="">-- Pilih Kenek Pengganti --</option>
                                @foreach($keneks as $kenek)
                                    <option value="{{ $kenek->nama_lengkap }}"
                                            {{ old('kenek_pengganti', $tandaTerima->kenek_pengganti) == $kenek->nama_lengkap ? 'selected' : '' }}>
                                        {{ $kenek->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kenek_pengganti')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- Data Pengirim & Penerima Section -->
                @if($tandaTerima->suratJalan)
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Data Pengirim & Penerima
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="pengirim" class="block text-xs font-medium text-gray-500 mb-2">
                                Pengirim
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="pengirim" id="pengirim" class="hidden @error('pengirim') border-red-500 @enderror" disabled>
                                    <option value="">Pilih Pengirim</option>
                                    @if(isset($pengirims))
                                        @foreach($pengirims as $pengirimItem)
                                            <option value="{{ $pengirimItem->nama_pengirim ?? $pengirimItem }}" 
                                                {{ old('pengirim', $tandaTerima->pengirim ?? ($tandaTerima->suratJalan->order->pengirim->nama_pengirim ?? '')) == ($pengirimItem->nama_pengirim ?? $pengirimItem) ? 'selected' : '' }}>
                                                {{ $pengirimItem->nama_pengirim ?? $pengirimItem }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                                <!-- Search input (disabled) -->
                                <input type="text" id="pengirimSearch"
                                       placeholder="Pengirim dari order"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 cursor-not-allowed @error('pengirim') border-red-500 @enderror"
                                       disabled>
                            </div>
                            @error('pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="penerima" class="block text-xs font-medium text-gray-500 mb-2">
                                Penerima <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="penerima" id="penerima" class="hidden @error('penerima') border-red-500 @enderror" required>
                                    <option value="">Pilih Penerima</option>
                                    @if(isset($masterPenerimaList))
                                        @foreach($masterPenerimaList as $penerimaItem)
                                            <option value="{{ $penerimaItem->nama }}" 
                                                data-alamat="{{ $penerimaItem->alamat ?? '' }}"
                                                {{ old('penerima', $tandaTerima->penerima) == $penerimaItem->nama ? 'selected' : '' }}>
                                                {{ $penerimaItem->nama }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                                <!-- Search input -->
                                <input type="text" id="penerimaSearch"
                                       placeholder="Cari atau pilih penerima..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('penerima') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="penerimaDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <input type="text" id="penerimaFilterInput" placeholder="Filter penerima..." class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                    </div>
                                    @if(isset($masterPenerimaList))
                                        @foreach($masterPenerimaList as $penerimaItem)
                                            <div class="penerima-option px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm"
                                                 data-value="{{ $penerimaItem->nama }}"
                                                 data-text="{{ $penerimaItem->nama }}"
                                                 data-alamat="{{ $penerimaItem->alamat ?? '' }}">
                                                {{ $penerimaItem->nama }}
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @error('penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-search mr-1"></i>Ketik untuk mencari penerima
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat_penerima" class="block text-xs font-medium text-gray-500 mb-2">
                                Alamat Penerima <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alamat_penerima" id="alamat_penerima" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 text-sm @error('alamat_penerima') border-red-500 @enderror"
                                      placeholder="Alamat lengkap penerima"
                                      required>{{ old('alamat_penerima', $tandaTerima->alamat_penerima) }}</textarea>
                            @error('alamat_penerima')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                <i class="fas fa-info-circle mr-1"></i>Alamat akan terisi otomatis saat memilih penerima, namun dapat diubah sesuai kebutuhan
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Separator -->
                <div class="relative py-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t-2 border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-6 py-2 bg-white text-sm font-semibold text-gray-700 border-2 border-gray-300 rounded-full shadow-sm">
                            <i class="fas fa-arrow-down mr-2 text-blue-600"></i>
                            Data Tanda Terima
                            <i class="fas fa-arrow-down ml-2 text-blue-600"></i>
                        </span>
                    </div>
                </div>

                <!-- Estimasi Nama Kapal, Nomor RO & Expired Date -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="estimasi_nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Estimasi Nama Kapal</label>
                        <select name="estimasi_nama_kapal" id="estimasi_nama_kapal"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 select2-kapal @error('estimasi_nama_kapal') border-red-500 @enderror">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($masterKapals as $kapal)
                                <option value="{{ $kapal->nama_kapal }}"
                                        {{ old('estimasi_nama_kapal', $tandaTerima->estimasi_nama_kapal) == $kapal->nama_kapal ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}{{ $kapal->nickname ? ' (' . $kapal->nickname . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('estimasi_nama_kapal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="nomor_ro" class="block text-sm font-medium text-gray-700 mb-2">Nomor RO</label>
                        <input type="text" name="nomor_ro" id="nomor_ro"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-mono @error('nomor_ro') border-red-500 @enderror"
                               placeholder="Masukkan nomor RO"
                               value="{{ old('nomor_ro', $tandaTerima->nomor_ro) }}">
                        @error('nomor_ro')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="expired_date" class="block text-sm font-medium text-gray-700 mb-2">Expired Date</label>
                        <input type="date" name="expired_date" id="expired_date"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm @error('expired_date') border-red-500 @enderror"
                               value="{{ old('expired_date', $tandaTerima->expired_date ? \Carbon\Carbon::parse($tandaTerima->expired_date)->format('Y-m-d') : '') }}">
                        @error('expired_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tanggal Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Informasi Tanggal</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_checkpoint_supir" class="block text-xs font-medium text-gray-500 mb-2">Tanggal Checkpoint Supir</label>
                            <input type="date" name="tanggal_checkpoint_supir" id="tanggal_checkpoint_supir"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 text-sm @error('tanggal_checkpoint_supir') border-red-500 @enderror"
                                   value="{{ old('tanggal_checkpoint_supir', $tandaTerima->tanggal_checkpoint_supir ? \Carbon\Carbon::parse($tandaTerima->tanggal_checkpoint_supir)->format('Y-m-d') : '') }}">
                            @error('tanggal_checkpoint_supir')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_terima_pelabuhan" class="block text-xs font-medium text-gray-500 mb-2">Tanggal Terima Pelabuhan</label>
                            <input type="date" name="tanggal_terima_pelabuhan" id="tanggal_terima_pelabuhan"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 text-sm @error('tanggal_terima_pelabuhan') border-red-500 @enderror"
                                   value="{{ old('tanggal_terima_pelabuhan', $tandaTerima->tanggal_terima_pelabuhan ? \Carbon\Carbon::parse($tandaTerima->tanggal_terima_pelabuhan)->format('Y-m-d') : '') }}">
                            @error('tanggal_terima_pelabuhan')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Dimensi & Volume -->
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Dimensi dan Volume
                        </h3>
                        <button type="button" id="add-dimensi-btn"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Dimensi
                        </button>
                    </div>

                    <div id="dimensi-container">
                        @if(count($dimensiItems) > 0)
                            @foreach($dimensiItems as $index => $item)
                            <div class="dimensi-row mb-4 pb-4 border-b border-purple-200 {{ $index > 0 ? 'relative' : '' }}">
                                @if($index > 0)
                                <button type="button" class="remove-dimensi-btn absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                @endif
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang</label>
                                        <input type="text" name="nama_barang[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="Nama barang"
                                               value="{{ old('nama_barang.' . $index, $item['nama_barang'] ?? '') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah</label>
                                        <input type="number" name="jumlah[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0" min="0" step="1"
                                               value="{{ old('jumlah.' . $index, $item['jumlah'] ?? '') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Satuan</label>
                                        <input type="text" name="satuan[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="Pcs, Kg, Box"
                                               value="{{ old('satuan.' . $index, $item['satuan'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                                        <input type="number" name="panjang[]"
                                               class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0.000" min="0" step="0.001"
                                               value="{{ old('panjang.' . $index, $item['panjang'] ?? '') }}"
                                               onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                                        <input type="number" name="lebar[]"
                                               class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0.000" min="0" step="0.001"
                                               value="{{ old('lebar.' . $index, $item['lebar'] ?? '') }}"
                                               onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                                        <input type="number" name="tinggi[]"
                                               class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0.000" min="0" step="0.001"
                                               value="{{ old('tinggi.' . $index, $item['tinggi'] ?? '') }}"
                                               onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                                        <input type="number" name="meter_kubik[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm"
                                               placeholder="0.000" min="0" step="0.001"
                                               value="{{ old('meter_kubik.' . $index, $item['meter_kubik'] ?? '') }}"
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                                        <input type="number" name="tonase[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0.000" min="0" step="0.001"
                                               value="{{ old('tonase.' . $index, $item['tonase'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            {{-- Default empty row if no dimensi items exist --}}
                            <div class="dimensi-row mb-4 pb-4 border-b border-purple-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang</label>
                                        <input type="text" name="nama_barang[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="Nama barang" value="{{ old('nama_barang.0') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah</label>
                                        <input type="number" name="jumlah[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0" min="0" step="1" value="{{ old('jumlah.0') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Satuan</label>
                                        <input type="text" name="satuan[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="Pcs, Kg, Box" value="{{ old('satuan.0') }}">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                                        <input type="number" name="panjang[]"
                                               class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0.000" min="0" step="0.001" value="{{ old('panjang.0') }}"
                                               onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                                        <input type="number" name="lebar[]"
                                               class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0.000" min="0" step="0.001" value="{{ old('lebar.0') }}"
                                               onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                                        <input type="number" name="tinggi[]"
                                               class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0.000" min="0" step="0.001" value="{{ old('tinggi.0') }}"
                                               onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                                        <input type="number" name="meter_kubik[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm"
                                               placeholder="0.000" min="0" step="0.001" value="{{ old('meter_kubik.0') }}"
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                                        <input type="number" name="tonase[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm"
                                               placeholder="0.000" min="0" step="0.001" value="{{ old('tonase.0') }}">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Volume akan dihitung otomatis dari panjang × lebar × tinggi
                    </p>
                </div>

                <!-- Informasi Tambahan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Informasi Tambahan</label>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="tujuan_pengiriman" class="block text-xs font-medium text-gray-500 mb-2">Tujuan Pengiriman</label>
                            <select name="tujuan_pengiriman" id="tujuan_pengiriman"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 text-sm select2-tujuan-kirim @error('tujuan_pengiriman') border-red-500 @enderror">
                                <option value="">-- Pilih Tujuan Pengiriman --</option>
                                @foreach($masterTujuanKirims as $tujuan)
                                    <option value="{{ $tujuan->nama_tujuan }}"
                                            {{ old('tujuan_pengiriman', $tandaTerima->tujuan_pengiriman) == $tujuan->nama_tujuan ? 'selected' : '' }}>
                                        {{ $tujuan->nama_tujuan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tujuan_pengiriman')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="catatan" class="block text-xs font-medium text-gray-500 mb-2">Catatan</label>
                            <textarea name="catatan" id="catatan" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 text-sm @error('catatan') border-red-500 @enderror"
                                      placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan', $tandaTerima->catatan) }}</textarea>
                            @error('catatan')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                <a href="{{ route('tanda-terima.show', $tandaTerima) }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i> Update Tanda Terima
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
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
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    (function() {
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded!');
            return;
        }
        
        jQuery(document).ready(function($) {
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2-kapal, .select2-tujuan-kirim, .select2-kontainer, .select2-supir-pengganti, .select2-kenek-pengganti').select2({
                    placeholder: function() {
                        return $(this).data('placeholder') || '-- Pilih --';
                    },
                    allowClear: true,
                    width: '100%',
                    tags: $(this).hasClass('select2-kontainer')
                });
            }
        });
    })();

    function calculateVolume(rowElement) {
        const panjangInput = rowElement.querySelector('[name^="panjang"]');
        const lebarInput = rowElement.querySelector('[name^="lebar"]');
        const tinggiInput = rowElement.querySelector('[name^="tinggi"]');
        const volumeInput = rowElement.querySelector('[name^="meter_kubik"]');

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

    function initializePenerimaDropdown() {
        const searchInput = document.getElementById('penerimaSearch');
        const dropdown = document.getElementById('penerimaDropdown');
        const hiddenSelect = document.getElementById('penerima');
        const filterInput = document.getElementById('penerimaFilterInput');
        const options = document.querySelectorAll('.penerima-option');
        const alamatTextarea = document.getElementById('alamat_penerima');

        if (!searchInput || !dropdown || !hiddenSelect) return;

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

        // Filter from the filter input inside dropdown
        if (filterInput) {
            filterInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                options.forEach(option => {
                    const text = option.getAttribute('data-text').toLowerCase();
                    if (text.includes(searchTerm)) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');
                const alamat = this.getAttribute('data-alamat');

                // Set the hidden select value
                hiddenSelect.value = value;

                // Update search input
                searchInput.value = text;

                // Auto-fill alamat if available
                if (alamat && alamatTextarea) {
                    alamatTextarea.value = alamat;
                }

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#penerimaSearch') && !e.target.closest('#penerimaDropdown')) {
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
            searchInput.value = selectedOption.textContent.trim();
        } else if (hiddenSelect.value) {
            // Handle custom penerima value that might not be in the dropdown options
            const customPenerima = hiddenSelect.value;
            // Check if this value exists in any option
            const existingOption = Array.from(hiddenSelect.options).find(opt => opt.value === customPenerima);
            if (!existingOption) {
                // This is a custom value, display it in the search input
                searchInput.value = customPenerima;
            }
        }
    }

    function initializePengirimDropdown() {
        const searchInput = document.getElementById('pengirimSearch');
        const hiddenSelect = document.getElementById('pengirim');

        if (!searchInput || !hiddenSelect) return;

        // Set initial value if exists
        const selectedOption = hiddenSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent.trim();
        } else if (hiddenSelect.value) {
            searchInput.value = hiddenSelect.value;
        }
    }

    function calculateVolume(rowElement) {
        const panjangInput = rowElement.querySelector('[name^="panjang"]');
        const lebarInput = rowElement.querySelector('[name^="lebar"]');
        const tinggiInput = rowElement.querySelector('[name^="tinggi"]');
        const volumeInput = rowElement.querySelector('[name^="meter_kubik"]');

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

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize penerima dropdown
        initializePenerimaDropdown();
        
        // Initialize pengirim dropdown (disabled)
        initializePengirimDropdown();

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
                            <input type="text" name="nama_barang[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm" placeholder="Nama barang">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah</label>
                            <input type="number" name="jumlah[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm" placeholder="0" min="0" step="1">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Satuan</label>
                            <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm" placeholder="Pcs, Kg, Box">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                            <input type="number" name="panjang[]" class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm" placeholder="0.000" min="0" step="0.001">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                            <input type="number" name="lebar[]" class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm" placeholder="0.000" min="0" step="0.001">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                            <input type="number" name="tinggi[]" class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm" placeholder="0.000" min="0" step="0.001">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                            <input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="0.000" min="0" step="0.001" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                            <input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 text-sm" placeholder="0.000" min="0" step="0.001">
                        </div>
                    </div>
                `;

                container.appendChild(newRow);

                const removeBtn = newRow.querySelector('.remove-dimensi-btn');
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });

                const dimensiInputs = newRow.querySelectorAll('.dimensi-input');
                dimensiInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateVolume(newRow);
                    });
                });
            });
        }

        // Attach listeners to existing rows
        const existingDimensiInputs = document.querySelectorAll('.dimensi-input');
        existingDimensiInputs.forEach(input => {
            input.addEventListener('input', function() {
                const row = input.closest('.dimensi-row');
                calculateVolume(row);
            });
        });

        // Calculate initial volumes
        const existingRows = document.querySelectorAll('#dimensi-container .dimensi-row');
        existingRows.forEach(row => calculateVolume(row));

        // Remove button handlers for existing rows
        document.querySelectorAll('.remove-dimensi-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.dimensi-row').remove();
            });
        });
    });
</script>
@endpush
