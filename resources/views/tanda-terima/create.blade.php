@extends('layouts.app')

@section('title', 'Buat Tanda Terima')

@section('content')
@php
    // Parse container data from surat jalan
    $nomorKontainerArray = [];
    
    if (!empty($suratJalan->no_kontainer)) {
        $nomorKontainerArray = array_map('trim', explode(',', $suratJalan->no_kontainer));
    }
    
    $jumlahKontainer = $suratJalan->jumlah_kontainer ?: 1;
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
                <a href="{{ route('tanda-terima.select-surat-jalan') }}" class="hover:text-blue-600 transition">Pilih Surat Jalan</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Buat</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Buat Tanda Terima</h1>
                <p class="text-gray-600 mt-1">No. Surat Jalan: <span class="font-semibold">{{ $suratJalan->no_surat_jalan }}</span></p>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Data Tanda Terima</h2>
            <p class="text-sm text-gray-600 mt-1">Lengkapi informasi untuk tanda terima baru</p>
        </div>

        <form action="{{ route('tanda-terima.store') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="surat_jalan_id" value="{{ $suratJalan->id }}">

                    <div class="space-y-6">
                        <!-- Informasi Surat Jalan Section -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Informasi Surat Jalan
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="tanggal_surat_jalan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Surat Jalan
                                    </label>
                                    <input type="date"
                                           name="tanggal_surat_jalan"
                                           id="tanggal_surat_jalan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           value="{{ old('tanggal_surat_jalan', $suratJalan->tanggal_surat_jalan?->format('Y-m-d')) }}">
                                </div>
                                <div>
                                    <label for="nomor_surat_jalan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Nomor Surat Jalan
                                    </label>
                                    <input type="text"
                                           name="nomor_surat_jalan"
                                           id="nomor_surat_jalan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono"
                                           value="{{ old('nomor_surat_jalan', $suratJalan->no_surat_jalan) }}"
                                           placeholder="Nomor surat jalan">
                                </div>
                                <div>
                                    <label for="rit" class="block text-xs font-medium text-gray-500 mb-2">
                                        Rit
                                    </label>
                                    <select name="rit"
                                            id="rit"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Status Rit</option>
                                        <option value="menggunakan_rit" {{ old('rit', $suratJalan->rit) == 'menggunakan_rit' ? 'selected' : '' }}>Menggunakan Rit</option>
                                        <option value="tidak_menggunakan_rit" {{ old('rit', $suratJalan->rit) == 'tidak_menggunakan_rit' ? 'selected' : '' }}>Tidak Menggunakan Rit</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Data Pengirim & Order Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Data Pengirim & Order
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="pengirim" class="block text-xs font-medium text-gray-500 mb-2">
                                        Pengirim
                                    </label>
                                    <select name="pengirim"
                                            id="pengirim"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm select2-pengirim">
                                        <option value="">-- Pilih Pengirim --</option>
                                        @foreach($pengirims as $pengirim)
                                            <option value="{{ $pengirim->nama_pengirim }}"
                                                    {{ old('pengirim', ($suratJalan->order && $suratJalan->order->pengirim ? $suratJalan->order->pengirim->nama_pengirim : '')) == $pengirim->nama_pengirim ? 'selected' : '' }}>
                                                {{ $pengirim->nama_pengirim }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari pengirim
                                    </p>
                                </div>
                                <div>
                                    <label for="term" class="block text-xs font-medium text-gray-500 mb-2">
                                        Term
                                    </label>
                                    <select name="term"
                                            id="term"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm select2-term">
                                        <option value="">-- Pilih Term --</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->nama_status }}"
                                                    {{ old('term', $suratJalan->term) == $term->nama_status ? 'selected' : '' }}>
                                                {{ $term->nama_status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari term
                                    </p>
                                </div>
                                <div>
                                    <label for="aktifitas" class="block text-xs font-medium text-gray-500 mb-2">
                                        Aktifitas/Kegiatan
                                    </label>
                                    <select name="aktifitas"
                                            id="aktifitas"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm select2-kegiatan">
                                        <option value="">-- Pilih Kegiatan --</option>
                                        @foreach($masterKegiatans as $kegiatan)
                                            <option value="{{ $kegiatan->nama_kegiatan }}"
                                                    {{ old('aktifitas', $suratJalan->kegiatan) == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                                {{ $kegiatan->nama_kegiatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari kegiatan
                                    </p>
                                </div>
                                <div>
                                    <label for="jenis_barang" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jenis Barang
                                    </label>
                                    <select name="jenis_barang"
                                            id="jenis_barang"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm select2-jenis-barang">
                                        <option value="">-- Pilih Jenis Barang --</option>
                                        @foreach($jenisBarangs as $jenisBarang)
                                            <option value="{{ $jenisBarang->nama_barang }}"
                                                    {{ old('jenis_barang', $suratJalan->jenis_barang) == $jenisBarang->nama_barang ? 'selected' : '' }}>
                                                {{ $jenisBarang->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari jenis barang
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="alamat" class="block text-xs font-medium text-gray-500 mb-2">
                                        Alamat
                                    </label>
                                    <textarea name="alamat"
                                              id="alamat"
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                              placeholder="Alamat lengkap">{{ old('alamat', $suratJalan->alamat) }}</textarea>
                                </div>
                                <div>
                                    <label for="telepon" class="block text-xs font-medium text-gray-500 mb-2">
                                        Telepon
                                    </label>
                                    <input type="text"
                                           name="telepon"
                                           id="telepon"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           value="{{ old('telepon', $suratJalan->telepon) }}"
                                           placeholder="Nomor telepon">
                                </div>
                                <div>
                                    <label for="jumlah_retur" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jumlah Retur
                                    </label>
                                    <input type="number"
                                           name="jumlah_retur"
                                           id="jumlah_retur"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           value="{{ old('jumlah_retur', $suratJalan->jumlah_retur) }}"
                                           placeholder="0"
                                           min="0">
                                </div>
                                @if($suratJalan->gambar_checkpoint)
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-2">
                                        Gambar Checkpoint
                                    </label>
                                    @php
                                        // Check if it's a JSON array (multiple images) or single image path
                                        $gambarCheckpoint = $suratJalan->gambar_checkpoint;
                                        $isJson = is_string($gambarCheckpoint) && (str_starts_with($gambarCheckpoint, '[') || str_starts_with($gambarCheckpoint, '{'));
                                        $imagePaths = $isJson ? json_decode($gambarCheckpoint, true) : [$gambarCheckpoint];
                                        $imagePaths = is_array($imagePaths) ? array_filter($imagePaths) : [$gambarCheckpoint];
                                    @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($imagePaths as $index => $imagePath)
                                        <div class="flex items-start gap-2 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                            <a href="{{ asset('storage/' . $imagePath) }}" 
                                               target="_blank" 
                                               class="group relative block overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-400 transition-all flex-shrink-0">
                                                @php
                                                    $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                @endphp
                                                @if($isImage)
                                                    <img src="{{ asset('storage/' . $imagePath) }}" 
                                                         alt="Gambar Checkpoint {{ $index + 1 }}" 
                                                         class="w-24 h-24 object-cover group-hover:scale-105 transition-transform">
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 flex items-center justify-center transition-all">
                                                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-24 h-24 flex items-center justify-center bg-gray-100">
                                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </a>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-700 mb-1">Foto {{ $index + 1 }}</p>
                                                <div class="flex flex-col gap-1">
                                                    <a href="{{ asset('storage/' . $imagePath) }}" 
                                                       target="_blank" 
                                                       class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Lihat
                                                    </a>
                                                    <a href="{{ asset('storage/' . $imagePath) }}" 
                                                       download 
                                                       class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded transition">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                        </svg>
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-camera mr-1"></i>
                                        {{ count($imagePaths) }} foto diupload saat checkpoint di lapangan
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Data Supir & Kendaraan Section -->
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Data Supir & Kendaraan
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label for="supir" class="block text-xs font-medium text-gray-500 mb-2">
                                        Supir
                                    </label>
                                    <select name="supir"
                                            id="supir"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm select2-supir">
                                        <option value="">-- Pilih Supir --</option>
                                        @foreach($karyawans as $karyawan)
                                            <option value="{{ $karyawan->nama_lengkap }}"
                                                    data-plat="{{ $karyawan->plat }}"
                                                    {{ old('supir', $suratJalan->supir) == $karyawan->nama_lengkap ? 'selected' : '' }}>
                                                {{ $karyawan->nama_lengkap }}{{ $karyawan->nama_panggilan ? ' (' . $karyawan->nama_panggilan . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari supir
                                    </p>
                                </div>
                                <div>
                                    <label for="supir_pengganti" class="block text-xs font-medium text-gray-500 mb-2">
                                        Supir Pengganti
                                    </label>
                                    <input type="text"
                                           name="supir_pengganti"
                                           id="supir_pengganti"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                           value="{{ old('supir_pengganti', $suratJalan->supir_pengganti) }}"
                                           placeholder="Nama supir pengganti">
                                </div>
                                <div>
                                    <label for="no_plat" class="block text-xs font-medium text-gray-500 mb-2">
                                        No. Plat
                                    </label>
                                    <input type="text"
                                           name="no_plat"
                                           id="no_plat"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-mono"
                                           value="{{ old('no_plat', $suratJalan->no_plat) }}"
                                           placeholder="Nomor plat kendaraan">
                                </div>
                                <div>
                                    <label for="kenek" class="block text-xs font-medium text-gray-500 mb-2">
                                        Kenek
                                    </label>
                                    <select name="kenek"
                                            id="kenek"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm select2-kenek">
                                        <option value="">-- Pilih Kenek --</option>
                                        @foreach($kranisKenek as $krani)
                                            <option value="{{ $krani->nama_lengkap }}"
                                                    {{ old('kenek', $suratJalan->kenek) == $krani->nama_lengkap ? 'selected' : '' }}>
                                                {{ $krani->nama_lengkap }}{{ $krani->nama_panggilan ? ' (' . $krani->nama_panggilan . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari kenek
                                    </p>
                                </div>
                                <div>
                                    <label for="krani" class="block text-xs font-medium text-gray-500 mb-2">
                                        Krani
                                    </label>
                                    <select name="krani"
                                            id="krani"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm select2-krani">
                                        <option value="">-- Pilih Krani --</option>
                                        @foreach($kranisKenek as $krani)
                                            <option value="{{ $krani->nama_lengkap }}"
                                                    {{ old('krani', $suratJalan->krani) == $krani->nama_lengkap ? 'selected' : '' }}>
                                                {{ $krani->nama_lengkap }}{{ $krani->nama_panggilan ? ' (' . $krani->nama_panggilan . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari krani
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Data Kontainer Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Data Kontainer
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="tipe_kontainer" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tipe Kontainer
                                    </label>
                                    <select name="tipe_kontainer[]"
                                            id="tipe_kontainer"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Tipe</option>
                                        <option value="fcl" {{ old('tipe_kontainer.0', strtolower($suratJalan->tipe_kontainer ?: 'fcl')) === 'fcl' ? 'selected' : '' }}>FCL</option>
                                        <option value="lcl" {{ old('tipe_kontainer.0', strtolower($suratJalan->tipe_kontainer ?: 'fcl')) === 'lcl' ? 'selected' : '' }}>LCL</option>
                                        <option value="cargo" {{ old('tipe_kontainer.0', strtolower($suratJalan->tipe_kontainer ?: 'fcl')) === 'cargo' ? 'selected' : '' }}>Cargo</option>
                                        <option value="bb" {{ old('tipe_kontainer.0', strtolower($suratJalan->tipe_kontainer ?: 'fcl')) === 'bb' ? 'selected' : '' }}>BB</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="size" class="block text-xs font-medium text-gray-500 mb-2">
                                        Size Kontainer
                                    </label>
                                    <select name="size[]"
                                            id="size"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Size</option>
                                        <option value="20" {{ old('size.0', $suratJalan->size) == '20' ? 'selected' : '' }}>20</option>
                                        <option value="40" {{ old('size.0', $suratJalan->size) == '40' ? 'selected' : '' }}>40</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="jumlah_kontainer" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jumlah Kontainer
                                    </label>
                                    <input type="number"
                                           name="jumlah_kontainer"
                                           id="jumlah_kontainer"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           value="{{ old('jumlah_kontainer', $suratJalan->jumlah_kontainer) }}"
                                           placeholder="Jumlah kontainer"
                                           min="0">
                                </div>
                                <div>
                                    <label for="nomor_kontainer" class="block text-xs font-medium text-gray-500 mb-2">
                                        No. Kontainer
                                    </label>
                                    <select name="nomor_kontainer[]"
                                            id="nomor_kontainer"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono select2-kontainer @error('nomor_kontainer.0') border-red-500 @enderror">
                                        <option value="">-- Pilih atau Ketik Nomor Kontainer --</option>
                                        @foreach($stockKontainers as $stock)
                                            <option value="{{ $stock->nomor_seri_gabungan }}"
                                                    {{ old('nomor_kontainer.0', $suratJalan->no_kontainer) == $stock->nomor_seri_gabungan ? 'selected' : '' }}>
                                                {{ $stock->nomor_seri_gabungan }} ({{ $stock->ukuran }}ft - {{ $stock->tipe_kontainer }})
                                            </option>
                                        @endforeach
                                        @if(old('nomor_kontainer.0', $suratJalan->no_kontainer))
                                            <option value="{{ old('nomor_kontainer.0', $suratJalan->no_kontainer) }}" selected>
                                                {{ old('nomor_kontainer.0', $suratJalan->no_kontainer) }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('nomor_kontainer.0')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari atau input nomor kontainer baru
                                    </p>
                                </div>
                                <div>
                                    <label for="no_seal" class="block text-xs font-medium text-gray-500 mb-2">
                                        No. Seal
                                    </label>
                                    <input type="text"
                                           name="no_seal[]"
                                           id="no_seal"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono @error('no_seal.0') border-red-500 @enderror"
                                           placeholder="Nomor seal"
                                           value="{{ old('no_seal.0', $suratJalan->no_seal) }}">
                                    @error('no_seal.0')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Data Packing/Pengamanan -->
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="karton" class="block text-xs font-medium text-gray-500 mb-2">
                                        Karton
                                    </label>
                                    <select name="karton"
                                            id="karton"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Status Karton</option>
                                        <option value="pakai" {{ old('karton', $suratJalan->karton) == 'pakai' ? 'selected' : '' }}>Pakai</option>
                                        <option value="tidak_pakai" {{ old('karton', $suratJalan->karton) == 'tidak_pakai' ? 'selected' : '' }}>Tidak Pakai</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="plastik" class="block text-xs font-medium text-gray-500 mb-2">
                                        Plastik
                                    </label>
                                    <select name="plastik"
                                            id="plastik"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Status Plastik</option>
                                        <option value="pakai" {{ old('plastik', $suratJalan->plastik) == 'pakai' ? 'selected' : '' }}>Pakai</option>
                                        <option value="tidak_pakai" {{ old('plastik', $suratJalan->plastik) == 'tidak_pakai' ? 'selected' : '' }}>Tidak Pakai</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="terpal" class="block text-xs font-medium text-gray-500 mb-2">
                                        Terpal
                                    </label>
                                    <select name="terpal"
                                            id="terpal"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Status Terpal</option>
                                        <option value="pakai" {{ old('terpal', $suratJalan->terpal) == 'pakai' ? 'selected' : '' }}>Pakai</option>
                                        <option value="tidak_pakai" {{ old('terpal', $suratJalan->terpal) == 'tidak_pakai' ? 'selected' : '' }}>Tidak Pakai</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Separator: Form Surat Jalan End -->
                        <div class="relative py-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t-2 border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="px-6 py-2 bg-white text-sm font-semibold text-gray-700 border-2 border-gray-300 rounded-full shadow-sm">
                                    <i class="fas fa-arrow-down mr-2 text-blue-600"></i>
                                    Form Tanda Terima
                                    <i class="fas fa-arrow-down ml-2 text-blue-600"></i>
                                </span>
                            </div>
                        </div>

                        <!-- FORM TANDA TERIMA START -->

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
                                            {{ old('estimasi_nama_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
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

                        <!-- Tanggal Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tanggal
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="tanggal_ambil_kontainer" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Ambil Kontainer
                                    </label>
                                    <input type="date"
                                           name="tanggal_ambil_kontainer"
                                           id="tanggal_ambil_kontainer"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_ambil_kontainer') border-red-500 @enderror"
                                           value="{{ old('tanggal_ambil_kontainer') }}">
                                    @error('tanggal_ambil_kontainer')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tanggal_terima_pelabuhan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Terima Pelabuhan
                                    </label>
                                    <input type="date"
                                           name="tanggal_terima_pelabuhan"
                                           id="tanggal_terima_pelabuhan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_terima_pelabuhan') border-red-500 @enderror"
                                           value="{{ old('tanggal_terima_pelabuhan') }}">
                                    @error('tanggal_terima_pelabuhan')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tanggal_garasi" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Garasi
                                    </label>
                                    <input type="date"
                                           name="tanggal_garasi"
                                           id="tanggal_garasi"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_garasi') border-red-500 @enderror"
                                           value="{{ old('tanggal_garasi') }}">
                                    @error('tanggal_garasi')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kuantitas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Kuantitas
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="jumlah" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jumlah
                                    </label>
                                    <input type="number"
                                           name="jumlah"
                                           id="jumlah"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('jumlah') border-red-500 @enderror"
                                           placeholder="0"
                                           value="{{ old('jumlah') }}"
                                           min="0"
                                           step="1">
                                    @error('jumlah')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="satuan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Satuan
                                    </label>
                                    <input type="text"
                                           name="satuan"
                                           id="satuan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('satuan') border-red-500 @enderror"
                                           placeholder="contoh: Pcs, Kg, Box"
                                           value="{{ old('satuan') }}">
                                    @error('satuan')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dimensi & Volume -->
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Dimensi dan Volume
                            </h3>

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                <div>
                                    <label for="panjang" class="block text-xs font-medium text-gray-500 mb-2">
                                        Panjang (m)
                                    </label>
                                    <input type="number"
                                           name="panjang"
                                           id="panjang"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('panjang') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('panjang') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume()">
                                    @error('panjang')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="lebar" class="block text-xs font-medium text-gray-500 mb-2">
                                        Lebar (m)
                                    </label>
                                    <input type="number"
                                           name="lebar"
                                           id="lebar"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('lebar') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('lebar') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume()">
                                    @error('lebar')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tinggi" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tinggi (m)
                                    </label>
                                    <input type="number"
                                           name="tinggi"
                                           id="tinggi"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('tinggi') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('tinggi') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume()">
                                    @error('tinggi')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="meter_kubik" class="block text-xs font-medium text-gray-500 mb-2">
                                        Volume (m³)
                                    </label>
                                    <input type="number"
                                           name="meter_kubik"
                                           id="meter_kubik"
                                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm @error('meter_kubik') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('meter_kubik') }}"
                                           min="0"
                                           step="0.001"
                                           readonly>
                                    @error('meter_kubik')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tonase" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tonase (Ton)
                                    </label>
                                    <input type="number"
                                           name="tonase"
                                           id="tonase"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('tonase') border-red-500 @enderror"
                                           placeholder="0.000"
                                           value="{{ old('tonase') }}"
                                           min="0"
                                           step="0.001">
                                    @error('tonase')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Volume akan dihitung otomatis dari panjang × lebar × tinggi
                            </p>
                        </div>

                        <!-- Informasi Tambahan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tambahan
                            </label>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="tujuan_pengiriman" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tujuan Pengiriman
                                    </label>
                                    <input type="text"
                                           name="tujuan_pengiriman"
                                           id="tujuan_pengiriman"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tujuan_pengiriman') border-red-500 @enderror"
                                           placeholder="Masukkan tujuan pengiriman"
                                           value="{{ old('tujuan_pengiriman', $suratJalan->tujuan_pengiriman) }}">
                                    @error('tujuan_pengiriman')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="catatan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Catatan
                                    </label>
                                    <textarea name="catatan"
                                              id="catatan"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('catatan') border-red-500 @enderror"
                                              placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan') }}</textarea>
                                    @error('catatan')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('tanda-terima.select-surat-jalan') }}"
                           class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition duration-200">
                            <i class="fas fa-save mr-2"></i> Simpan Tanda Terima
                        </button>
                    </div>
                </form>
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
<!-- Select2 JS - jQuery already loaded in layout -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    console.log('=== Script Loading Started ===');
    console.log('jQuery available:', typeof jQuery !== 'undefined');
    console.log('$ available:', typeof $ !== 'undefined');
    
    // Create a mapping of supir names to plat numbers
    var supirPlatMap = {
        @foreach($karyawans as $karyawan)
            "{{ $karyawan->nama_lengkap }}": "{{ $karyawan->plat ?? '' }}",
        @endforeach
    };

    console.log('Supir-Plat mapping loaded:', supirPlatMap);

    // Use jQuery ready with better checking
    (function() {
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded!');
            return;
        }
        
        jQuery(document).ready(function($) {
            console.log('✓ Document ready - initializing Select2');
            
            if (typeof $.fn.select2 !== 'undefined') {
                console.log('✓ Select2 plugin is available');
                
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

            // Initialize Select2 for pengirim dropdown
            $('.select2-pengirim').select2({
                placeholder: '-- Pilih Pengirim --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Pengirim tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Initialize Select2 for term dropdown
            $('.select2-term').select2({
                placeholder: '-- Pilih Term --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Term tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Initialize Select2 for jenis barang dropdown
            $('.select2-jenis-barang').select2({
                placeholder: '-- Pilih Jenis Barang --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Jenis barang tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Initialize Select2 for kegiatan dropdown
            $('.select2-kegiatan').select2({
                placeholder: '-- Pilih Kegiatan --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Kegiatan tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Initialize Select2 for supir dropdown
            $('.select2-supir').select2({
                placeholder: '-- Pilih Supir --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Supir tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Initialize Select2 for kenek dropdown
            $('.select2-kenek').select2({
                placeholder: '-- Pilih Kenek --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Kenek tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Initialize Select2 for krani dropdown
            $('.select2-krani').select2({
                placeholder: '-- Pilih Krani --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Krani tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Initialize Select2 for nomor kontainer with tags (allow free input)
            $('.select2-kontainer').select2({
                placeholder: '-- Pilih atau Ketik Nomor Kontainer --',
                allowClear: true,
                width: '100%',
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    }
                },
                language: {
                    noResults: function() {
                        return "Ketik nomor kontainer baru atau pilih dari daftar";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            console.log('Select2 initialized for all dropdowns');

            // Auto-fill no_plat when supir is selected - using change event
            $('#supir').on('change', function() {
                console.log('=== Supir Change Event Triggered ===');
                var supirName = $(this).val();
                console.log('Selected supir name:', supirName);
                
                if (supirName && supirName !== '') {
                    var plat = supirPlatMap[supirName];
                    console.log('Looking up plat for:', supirName);
                    console.log('Found plat:', plat);
                    
                    if (plat && plat !== '' && plat !== 'null') {
                        $('#no_plat').val(plat);
                        console.log('✓ Plat field updated to:', plat);
                    } else {
                        console.log('✗ No plat found or plat is empty');
                        $('#no_plat').val('');
                    }
                } else {
                    console.log('Supir cleared, clearing plat field');
                    $('#no_plat').val('');
                }
            });

            // Filter nomor kontainer berdasarkan size yang dipilih
            $('#size').on('change', function() {
                var selectedSize = $(this).val();
                console.log('=== Size Changed ===');
                console.log('Selected size:', selectedSize);
                
                // Clear current selection first
                $('#nomor_kontainer').val(null);
                
                // Destroy existing Select2 instance
                $('#nomor_kontainer').select2('destroy');
                
                // Remove all options except the placeholder
                var $select = $('#nomor_kontainer');
                var placeholderOption = $select.find('option[value=""]').clone();
                $select.empty().append(placeholderOption);
                
                // Re-add filtered options
                @foreach($stockKontainers as $stock)
                    var optionText = '{{ $stock->nomor_seri_gabungan }} ({{ $stock->ukuran }}ft - {{ $stock->tipe_kontainer }})';
                    var optionValue = '{{ $stock->nomor_seri_gabungan }}';
                    var stockSize = '{{ $stock->ukuran }}';
                    
                    // Only add option if no size selected OR size matches
                    if (!selectedSize || stockSize === selectedSize) {
                        var newOption = new Option(optionText, optionValue, false, false);
                        $select.append(newOption);
                        console.log('✓ Added option:', optionText);
                    } else {
                        console.log('✗ Skipped option:', optionText);
                    }
                @endforeach
                
                // Re-initialize Select2 with filtered data
                $('#nomor_kontainer').select2({
                    placeholder: '-- Pilih atau Ketik Nomor Kontainer --',
                    allowClear: true,
                    width: '100%',
                    tags: true,
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: term,
                            text: term,
                            newTag: true
                        }
                    },
                    language: {
                        noResults: function() {
                            return "Ketik nomor kontainer baru atau pilih dari daftar";
                        },
                        searching: function() {
                            return "Mencari...";
                        }
                    }
                });
                
                console.log('✓ Kontainer options filtered by size:', selectedSize);
            });

            // Also listen to Select2 specific events as backup
            $('#supir').on('select2:select', function(e) {
                console.log('=== Select2:select Event Triggered ===');
                $('#supir').trigger('change');
            });

            $('#supir').on('select2:clear', function() {
                console.log('=== Select2:clear Event Triggered ===');
                $('#no_plat').val('');
            });
            } else {
                console.error('✗ Select2 plugin not loaded!');
            }
        });
    })();

    function calculateVolume() {
        const panjang = parseFloat(document.getElementById('panjang').value) || 0;
        const lebar = parseFloat(document.getElementById('lebar').value) || 0;
        const tinggi = parseFloat(document.getElementById('tinggi').value) || 0;

        if (panjang > 0 && lebar > 0 && tinggi > 0) {
            const volume = panjang * lebar * tinggi;
            document.getElementById('meter_kubik').value = volume.toFixed(3);
        } else {
            document.getElementById('meter_kubik').value = '';
        }
    }
</script>
@endpush
