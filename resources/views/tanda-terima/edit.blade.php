@extends('layouts.app')

@section('title', 'Edit Tanda Terima')

@section('content')
@php
    // Parse container and seal data
    $nomorKontainerArray = [];
    $noSealArray = [];
    
    if (!empty($tandaTerima->no_kontainer)) {
        $nomorKontainerArray = array_map('trim', explode(',', $tandaTerima->no_kontainer));
    }
    
    if (!empty($tandaTerima->no_seal)) {
        $noSealArray = array_map('trim', explode(',', $tandaTerima->no_seal));
    }
    
    $jumlahKontainer = $tandaTerima->jumlah_kontainer ?: 1;
@endphp

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('tanda-terima.index') }}" class="hover:text-blue-600 transition">Tanda Terima</a>
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
                <p class="text-gray-600 mt-1">No. Surat Jalan: <span class="font-semibold">{{ $tandaTerima->no_surat_jalan }}</span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section (Left - 2/3) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Data Tambahan</h2>
                    <p class="text-sm text-gray-600 mt-1">Lengkapi informasi tambahan untuk tanda terima</p>
                </div>

                <form action="{{ route('tanda-terima.update', $tandaTerima->id) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Update Kontainer Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Update Data Kontainer
                                </label>
                                <span class="text-xs text-gray-500">
                                    {{ $jumlahKontainer }} Kontainer - {{ $tandaTerima->size }}ft
                                </span>
                            </div>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kontainer
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No. Kontainer
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No. Seal
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @for($i = 1; $i <= $jumlahKontainer; $i++)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <span class="text-sm font-medium text-blue-600">#{{ $i }}</span>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-gray-900">
                                                                @if(isset($nomorKontainerArray[$i-1]) && !empty($nomorKontainerArray[$i-1]))
                                                                    {{ $nomorKontainerArray[$i-1] }}
                                                                @else
                                                                    Kontainer {{ $i }}
                                                                @endif
                                                            </p>
                                                            <p class="text-xs text-gray-500">{{ $tandaTerima->size }}ft - {{ strtoupper($tandaTerima->tipe_kontainer ?: 'FCL') }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if(isset($nomorKontainerArray[$i-1]) && !empty($nomorKontainerArray[$i-1]))
                                                        <!-- Nomor kontainer sudah ada, tidak bisa diedit -->
                                                        <div class="flex items-center">
                                                            <input type="text"
                                                                   class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-gray-700 text-sm font-mono cursor-not-allowed"
                                                                   value="{{ $nomorKontainerArray[$i-1] }}"
                                                                   readonly>
                                                            <span class="ml-2 text-xs text-gray-500">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        </div>
                                                        <!-- Hidden field untuk mengirim data yang sama -->
                                                        <input type="hidden" name="nomor_kontainer[]" value="{{ $nomorKontainerArray[$i-1] }}">
                                                    @else
                                                        <!-- Nomor kontainer kosong, bisa diedit -->
                                                        <input type="text"
                                                               name="nomor_kontainer[]"
                                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono @error('nomor_kontainer.'.$i) border-red-500 @enderror"
                                                               placeholder="Nomor kontainer #{{ $i }}"
                                                               value="{{ old('nomor_kontainer.'.$i, '') }}">
                                                        @error('nomor_kontainer.'.$i)
                                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if(isset($noSealArray[$i-1]) && !empty($noSealArray[$i-1]))
                                                        <!-- Nomor seal sudah ada, tidak bisa diedit -->
                                                        <div class="flex items-center">
                                                            <input type="text"
                                                                   class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-gray-700 text-sm font-mono cursor-not-allowed"
                                                                   value="{{ $noSealArray[$i-1] }}"
                                                                   readonly>
                                                            <span class="ml-2 text-xs text-gray-500">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        </div>
                                                        <!-- Hidden field untuk mengirim data yang sama -->
                                                        <input type="hidden" name="no_seal[]" value="{{ $noSealArray[$i-1] }}">
                                                    @else
                                                        <!-- Nomor seal kosong, bisa diedit -->
                                                        <input type="text"
                                                               name="no_seal[]"
                                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono @error('no_seal.'.$i) border-red-500 @enderror"
                                                               placeholder="Nomor seal #{{ $i }}"
                                                               value="{{ old('no_seal.'.$i, '') }}">
                                                        @error('no_seal.'.$i)
                                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-xs text-gray-500 mt-2 space-y-1">
                                <p>
                                    <i class="fas fa-lock mr-1"></i>
                                    <strong>Nomor kontainer</strong> tidak dapat diedit setelah tersimpan
                                </p>
                                <p>
                                    <i class="fas fa-lock mr-1"></i>
                                    <strong>Nomor seal</strong> tidak dapat diedit jika sudah diisi sebelumnya
                                </p>
                                <p>
                                    <i class="fas fa-edit mr-1"></i>
                                    Anda hanya dapat mengisi field yang masih kosong
                                </p>
                            </div>
                        </div>

                        <!-- Estimasi Nama Kapal -->
                        <div>
                            <label for="estimasi_nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                                Estimasi Nama Kapal <span class="text-red-500">*</span>
                            </label>
                            <select name="estimasi_nama_kapal"
                                    id="estimasi_nama_kapal"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-kapal @error('estimasi_nama_kapal') border-red-500 @enderror"
                                    required>
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
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-search mr-1"></i>Ketik untuk mencari nama kapal
                            </p>
                        </div>

                        <!-- Tanggal Section Table -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tanggal
                            </label>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Ambil Kontainer
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Terima Pelabuhan
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Garasi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="date"
                                                       name="tanggal_ambil_kontainer"
                                                       id="tanggal_ambil_kontainer"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_ambil_kontainer') border-red-500 @enderror"
                                                       value="{{ old('tanggal_ambil_kontainer', $tandaTerima->tanggal_ambil_kontainer?->format('Y-m-d')) }}">
                                                @error('tanggal_ambil_kontainer')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="date"
                                                       name="tanggal_terima_pelabuhan"
                                                       id="tanggal_terima_pelabuhan"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_terima_pelabuhan') border-red-500 @enderror"
                                                       value="{{ old('tanggal_terima_pelabuhan', $tandaTerima->tanggal_terima_pelabuhan?->format('Y-m-d')) }}">
                                                @error('tanggal_terima_pelabuhan')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="date"
                                                       name="tanggal_garasi"
                                                       id="tanggal_garasi"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_garasi') border-red-500 @enderror"
                                                       value="{{ old('tanggal_garasi', $tandaTerima->tanggal_garasi?->format('Y-m-d')) }}">
                                                @error('tanggal_garasi')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Informasi Kuantitas per Kontainer -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Informasi Kuantitas per Kontainer
                                </label>
                                <span class="text-xs text-gray-500">
                                    {{ $jumlahKontainer }} Kontainer
                                </span>
                            </div>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kontainer
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jumlah
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Satuan
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            $jumlahArray = [];
                                            $satuanArray = [];
                                            
                                            if (!empty($tandaTerima->jumlah)) {
                                                $jumlahArray = array_map('trim', explode(',', $tandaTerima->jumlah));
                                            }
                                            
                                            if (!empty($tandaTerima->satuan)) {
                                                $satuanArray = array_map('trim', explode(',', $tandaTerima->satuan));
                                            }
                                        @endphp

                                        @for($i = 1; $i <= $jumlahKontainer; $i++)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                                            <span class="text-sm font-medium text-green-600">#{{ $i }}</span>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-gray-900">
                                                                @if(isset($nomorKontainerArray[$i-1]) && !empty($nomorKontainerArray[$i-1]))
                                                                    {{ $nomorKontainerArray[$i-1] }}
                                                                @else
                                                                    Kontainer {{ $i }}
                                                                @endif
                                                            </p>
                                                            <p class="text-xs text-gray-500">{{ $tandaTerima->size }}ft - {{ strtoupper($tandaTerima->tipe_kontainer ?: 'FCL') }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number"
                                                           name="jumlah_kontainer[]"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('jumlah_kontainer.'.$i) border-red-500 @enderror"
                                                           placeholder="Jumlah untuk {{ isset($nomorKontainerArray[$i-1]) && !empty($nomorKontainerArray[$i-1]) ? $nomorKontainerArray[$i-1] : 'kontainer #'.$i }}"
                                                           value="{{ old('jumlah_kontainer.'.$i, isset($jumlahArray[$i-1]) ? $jumlahArray[$i-1] : '') }}"
                                                           min="0"
                                                           step="1">
                                                    @error('jumlah_kontainer.'.$i)
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="text"
                                                           name="satuan_kontainer[]"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('satuan_kontainer.'.$i) border-red-500 @enderror"
                                                           placeholder="Satuan untuk {{ isset($nomorKontainerArray[$i-1]) && !empty($nomorKontainerArray[$i-1]) ? $nomorKontainerArray[$i-1] : 'kontainer #'.$i }}"
                                                           value="{{ old('satuan_kontainer.'.$i, isset($satuanArray[$i-1]) ? $satuanArray[$i-1] : '') }}">
                                                    @error('satuan_kontainer.'.$i)
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Hidden fields for backward compatibility -->
                            <input type="hidden" name="jumlah" id="hiddenJumlah">
                            <input type="hidden" name="satuan" id="hiddenSatuan">
                            
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Masukkan jumlah dan satuan untuk setiap kontainer secara terpisah
                            </p>
                        </div>

                        <!-- Dimensi & Volume -->
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Dimensi dan Volume
                            </h3>

                            @php
                                $panjangArray = [];
                                $lebarArray = [];
                                $tinggiArray = [];
                                $meterKubikArray = [];
                                $tonaseArray = [];
                                
                                if (!empty($tandaTerima->panjang)) {
                                    $panjangArray = array_map('trim', explode(',', $tandaTerima->panjang));
                                }
                                
                                if (!empty($tandaTerima->lebar)) {
                                    $lebarArray = array_map('trim', explode(',', $tandaTerima->lebar));
                                }
                                
                                if (!empty($tandaTerima->tinggi)) {
                                    $tinggiArray = array_map('trim', explode(',', $tandaTerima->tinggi));
                                }
                                
                                if (!empty($tandaTerima->meter_kubik)) {
                                    $meterKubikArray = array_map('trim', explode(',', $tandaTerima->meter_kubik));
                                }
                                
                                if (!empty($tandaTerima->tonase)) {
                                    $tonaseArray = array_map('trim', explode(',', $tandaTerima->tonase));
                                }
                            @endphp

                            <!-- Dimensi Table -->
                            <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Panjang (m)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Lebar (m)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tinggi (m)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Volume (m³)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tonase (Ton)
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="dimensiTableBody" class="bg-white divide-y divide-gray-200">
                                        <!-- Dimensi items will be added here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Add Dimensi Button & Summary -->
                            <div class="flex justify-between items-center">
                                <button type="button" id="addDimensiItem"
                                        class="inline-flex items-center px-4 py-2 border border-purple-300 rounded-md shadow-sm text-sm font-medium text-purple-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Item Dimensi
                                </button>

                                <div class="text-right">
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Total Volume:</span> <span id="totalVolume" class="text-purple-600 font-semibold">0.000 m³</span>
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Total Tonase:</span> <span id="totalTonase" class="text-purple-600 font-semibold">0.000 Ton</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for backward compatibility -->
                            <input type="hidden" name="panjang" id="hiddenPanjang">
                            <input type="hidden" name="lebar" id="hiddenLebar">
                            <input type="hidden" name="tinggi" id="hiddenTinggi">
                            <input type="hidden" name="meter_kubik" id="hiddenMeterKubik">
                            <input type="hidden" name="tonase" id="hiddenTonase">
                        </div>

                        <!-- Informasi Tambahan Table -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tambahan
                            </label>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tujuan Pengiriman
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Catatan
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 align-top">
                                                <input type="text"
                                                       name="tujuan_pengiriman"
                                                       id="tujuan_pengiriman"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tujuan_pengiriman') border-red-500 @enderror"
                                                       placeholder="Masukkan tujuan pengiriman"
                                                       value="{{ old('tujuan_pengiriman', $tandaTerima->tujuan_pengiriman) }}">
                                                @error('tujuan_pengiriman')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 align-top">
                                                <textarea name="catatan"
                                                          id="catatan"
                                                          rows="3"
                                                          class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('catatan') border-red-500 @enderror"
                                                          placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan', $tandaTerima->catatan) }}</textarea>
                                                @error('catatan')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('tanda-terima.index') }}"
                           class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Section (Right - 1/3) -->
        <div class="lg:col-span-1">
            <!-- Surat Jalan Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Surat Jalan</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">No. Surat Jalan</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $tandaTerima->no_surat_jalan }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->tanggal_surat_jalan?->format('d F Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Supir</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->supir ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Jenis Barang</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $tandaTerima->jenis_barang ?: '-' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Kegiatan</dt>
                        <dd class="mt-1">
                            @php
                                $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $tandaTerima->kegiatan)
                                                ->value('nama_kegiatan') ?? $tandaTerima->kegiatan;
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $kegiatanName }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Kontainer Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Kontainer</h3>
                <dl class="space-y-3">

                    @for($i = 1; $i <= $jumlahKontainer; $i++)
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Kontainer #{{ $i }}</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase">No. Kontainer</dt>
                                    <dd class="mt-1">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                                            {{ isset($nomorKontainerArray[$i-1]) ? $nomorKontainerArray[$i-1] : '-' }}
                                        </code>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase">No. Seal</dt>
                                    <dd class="mt-1">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                                            {{ isset($noSealArray[$i-1]) ? $noSealArray[$i-1] : '-' }}
                                        </code>
                                    </dd>
                                </div>
                            </div>
                        </div>
                    @endfor

                    <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-gray-200">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Size</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->size ?: '-' }} ft</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Jumlah Kontainer</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $jumlahKontainer }} Kontainer
                                </span>
                            </dd>
                        </div>
                    </div>
                </dl>
            </div>

            <!-- Location Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Lokasi</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tujuan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->tujuan_pengiriman ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Pengirim</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->pengirim ?: '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

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

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let dimensiItemIndex = 1;

    $(document).ready(function() {
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

        // Calculate initial volumes and totals
        calculateAllVolumesAndTotals();

        // Add new dimensi item
        document.getElementById('addDimensiItem').addEventListener('click', function() {
            addNewDimensiItem();
        });

        // Remove dimensi item
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-dimensi-item')) {
                e.target.closest('.dimensi-item').remove();
                updateItemNumbers();
                calculateAllVolumesAndTotals();
                updateRemoveButtons();
            }
        });

        // Format input values on blur with smart decimal handling
        document.addEventListener('blur', function(e) {
            if (e.target.matches('.dimensi-panjang, .dimensi-lebar, .dimensi-tinggi, .dimensi-tonase')) {
                const value = parseFloat(e.target.value);
                if (!isNaN(value) && value > 0) {
                    if (e.target.matches('.dimensi-tonase')) {
                        e.target.value = formatWeight(value);
                    } else {
                        e.target.value = formatVolume(value);
                    }
                }
            }
        }, true);

        // Initialize existing data
        initializeExistingData();

        // Update hidden fields when inputs change
        $(document).on('input', 'input[name="jumlah_kontainer[]"]', function() {
            updateHiddenQuantityFields();
        });

        $(document).on('input', 'input[name="satuan_kontainer[]"]', function() {
            updateHiddenSatuan();
        });

        // Initialize hidden quantity fields
        updateHiddenQuantityFields();

        // Format all numeric inputs before form submission
        $('form').on('submit', function(e) {
            // Format all dimensi inputs to clean values
            $('.dimensi-panjang, .dimensi-lebar, .dimensi-tinggi').each(function() {
                const value = parseFloat($(this).val());
                if (!isNaN(value) && value > 0) {
                    $(this).val(formatVolumeForDatabase(value));
                }
            });

            $('.dimensi-tonase').each(function() {
                const value = parseFloat($(this).val());
                if (!isNaN(value) && value > 0) {
                    $(this).val(formatWeightForDatabase(value));
                }
            });

            // Format volume fields
            $('.item-meter-kubik').each(function() {
                const value = parseFloat($(this).val());
                if (!isNaN(value) && value > 0) {
                    $(this).val(formatVolumeForDatabase(value));
                }
            });
        });
    });



    // Formatting functions for input fields (clean format, same as database)
    function formatVolume(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Show as "1000" not "1000.000"
        }
        
        // Remove trailing zeros from decimals
        return parseFloat(rounded.toFixed(3)).toString();
    }

    function formatWeight(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Show as "5" not "5.000"
        }
        
        // Remove trailing zeros from decimals
        return parseFloat(rounded.toFixed(3)).toString();
    }

    // Formatting functions for display totals (with thousand separator)
    function formatVolumeDisplay(value) {
        if (value === 0) return '0';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (rounded % 1 === 0) {
            // It's a whole number, format without decimals
            return rounded.toLocaleString('id-ID');
        } else {
            // It has decimals, show with 3 decimal places
            return rounded.toLocaleString('id-ID', {
                minimumFractionDigits: 3,
                maximumFractionDigits: 3
            });
        }
    }

    function formatWeightDisplay(value) {
        if (value === 0) return '0';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (rounded % 1 === 0) {
            // It's a whole number, format without decimals
            return rounded.toLocaleString('id-ID');
        } else {
            // It has decimals, show with 3 decimal places
            return rounded.toLocaleString('id-ID', {
                minimumFractionDigits: 3,
                maximumFractionDigits: 3
            });
        }
    }

    // Formatting functions for database (clean values, no excessive decimals)
    function formatVolumeForDatabase(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Send as "8000" not "8000.000"
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
            return rounded.toString(); // Send as "50" not "50.000"
        }
        
        // Remove trailing zeros from decimals
        return parseFloat(rounded.toFixed(3)).toString();
    }

    function initializeExistingData() {
        @php
            $maxItems = max(
                count($panjangArray),
                count($lebarArray), 
                count($tinggiArray),
                count($meterKubikArray),
                count($tonaseArray),
                1 // At least 1 item
            );
        @endphp

        @for($i = 0; $i < $maxItems; $i++)
            addNewDimensiItem(
                '{{ isset($panjangArray[$i]) ? $panjangArray[$i] : '' }}',
                '{{ isset($lebarArray[$i]) ? $lebarArray[$i] : '' }}',
                '{{ isset($tinggiArray[$i]) ? $tinggiArray[$i] : '' }}',
                '{{ isset($meterKubikArray[$i]) ? $meterKubikArray[$i] : '' }}',
                '{{ isset($tonaseArray[$i]) ? $tonaseArray[$i] : '' }}'
            );
        @endfor

        calculateAllVolumesAndTotals();
        updateRemoveButtons();
    }

    function addNewDimensiItem(panjang = '', lebar = '', tinggi = '', volume = '', tonase = '') {
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
                       class="dimensi-panjang w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="0.000"
                       min="0"
                       step="0.001"
                       value="${panjang}"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][lebar]"
                       class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="0.000"
                       min="0"
                       step="0.001"
                       value="${lebar}"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][tinggi]"
                       class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="0.000"
                       min="0"
                       step="0.001"
                       value="${tinggi}"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="text"
                       name="dimensi_items[${dimensiItemIndex}][meter_kubik]"
                       class="item-meter-kubik w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm focus:outline-none"
                       placeholder="0"
                       readonly
                       value="${volume}">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][tonase]"
                       class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="0.000"
                       min="0"
                       step="0.001"
                       value="${tonase}"
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

        document.getElementById('dimensiTableBody').appendChild(newRow);
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
        const panjang = parseFloat(row.querySelector('.dimensi-panjang').value) || 0;
        const lebar = parseFloat(row.querySelector('.dimensi-lebar').value) || 0;
        const tinggi = parseFloat(row.querySelector('.dimensi-tinggi').value) || 0;

        let volume = 0;
        if (panjang > 0 && lebar > 0 && tinggi > 0) {
            // Kalkulasi langsung dalam meter kubik (m × m × m = m³)
            volume = panjang * lebar * tinggi;
        }

        const volumeInput = row.querySelector('.item-meter-kubik');
        if (volume > 0) {
            volumeInput.value = formatVolumeForDatabase(volume);
        } else {
            volumeInput.value = '';
        }
        calculateTotals();
    }

    function calculateAllVolumesAndTotals() {
        const rows = document.querySelectorAll('#dimensiTableBody .dimensi-item');
        rows.forEach(row => {
            const panjang = parseFloat(row.querySelector('.dimensi-panjang').value) || 0;
            const lebar = parseFloat(row.querySelector('.dimensi-lebar').value) || 0;
            const tinggi = parseFloat(row.querySelector('.dimensi-tinggi').value) || 0;

            let volume = 0;
            if (panjang > 0 && lebar > 0 && tinggi > 0) {
                // Kalkulasi langsung dalam meter kubik (m × m × m = m³)
                volume = panjang * lebar * tinggi;
            }

            const volumeInput = row.querySelector('.item-meter-kubik');
            if (volume > 0) {
                volumeInput.value = formatVolumeForDatabase(volume);
            } else {
                volumeInput.value = '';
            }
        });
        calculateTotals();
        updateRemoveButtons();
    }

    function calculateTotals() {
        let totalVolume = 0;
        let totalTonase = 0;

        const rows = document.querySelectorAll('#dimensiTableBody .dimensi-item');
        rows.forEach(row => {
            const volume = parseFloat(row.querySelector('.item-meter-kubik').value) || 0;
            const tonase = parseFloat(row.querySelector('.dimensi-tonase').value) || 0;

            totalVolume += volume;
            totalTonase += tonase;
        });

        // Update summary display - with thousand separator
        document.getElementById('totalVolume').textContent = formatVolumeDisplay(totalVolume) + ' m³';
        document.getElementById('totalTonase').textContent = formatWeightDisplay(totalTonase) + ' Ton';

        // Update hidden fields for backward compatibility - use smart formatting
        const firstRow = document.querySelector('#dimensiTableBody .dimensi-item');
        if (firstRow) {
            const panjang = parseFloat(firstRow.querySelector('.dimensi-panjang').value) || 0;
            const lebar = parseFloat(firstRow.querySelector('.dimensi-lebar').value) || 0;
            const tinggi = parseFloat(firstRow.querySelector('.dimensi-tinggi').value) || 0;
            
            document.getElementById('hiddenPanjang').value = panjang > 0 ? formatVolumeForDatabase(panjang) : '';
            document.getElementById('hiddenLebar').value = lebar > 0 ? formatVolumeForDatabase(lebar) : '';
            document.getElementById('hiddenTinggi').value = tinggi > 0 ? formatVolumeForDatabase(tinggi) : '';
        }
        document.getElementById('hiddenMeterKubik').value = totalVolume > 0 ? formatVolumeForDatabase(totalVolume) : '';
        document.getElementById('hiddenTonase').value = totalTonase > 0 ? formatWeightForDatabase(totalTonase) : '';
    }

    // Update hidden quantity fields
    function updateHiddenQuantityFields() {
        let jumlahArray = [];

        $('input[name="jumlah_kontainer[]"]').each(function() {
            const value = parseFloat($(this).val()) || 0;
            if (value > 0) {
                jumlahArray.push(value.toString());
            }
        });

        // Update hidden field for backward compatibility
        $('#hiddenJumlah').val(jumlahArray.join(','));
    }

    // Update hidden satuan field
    function updateHiddenSatuan() {
        let satuanArray = [];

        $('input[name="satuan_kontainer[]"]').each(function() {
            const value = $(this).val().trim();
            if (value) {
                satuanArray.push(value);
            }
        });

        // Update hidden field for backward compatibility  
        $('#hiddenSatuan').val(satuanArray.join(','));
    }
</script>
@endpush
