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

                    {{-- General error alert for server-side issues or exception messages --}}
                    @if(session('error'))
                        <div class="server-error mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-800">
                            <div class="font-semibold">Gagal membuat Tanda Terima</div>
                            <p class="mt-1">{{ session('error') }}</p>
                            <p class="mt-2 text-xs text-gray-500">Saran perbaikan:
                                <ul class="list-disc ml-5 mt-1">
                                    <li>Periksa kembali field yang wajib diisi (ditandai bintang merah).</li>
                                    <li>Pastikan format tanggal dan nomor kontainer benar.</li>
                                    <li>Jika server mengembalikan error teknis (SQL, constraint), buka file log: <code>storage/logs/laravel.log</code> untuk detail.</li>
                                    <li>Jika masih gagal, hubungi admin dengan melampirkan pesan error di bawah.</li>
                                </ul>
                            </p>
                        </div>
                    @endif

                    {{-- Validation summary for multiple field errors --}}
                    @if ($errors->any())
                        <div class="validation-errors mb-4 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-sm text-yellow-800">
                            <div class="font-semibold">Validasi gagal. Silakan periksa field berikut:</div>
                            <ul class="mt-2 list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <p class="mt-2 text-xs text-gray-500">Tips: Periksa format tanggal, panjang karakter, dan field yang wajib diisi.</p>
                        </div>
                    @endif
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="tanggal_surat_jalan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Surat Jalan
                                    </label>
                                    <input type="date"
                                           name="tanggal_surat_jalan"
                                           id="tanggal_surat_jalan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm cursor-not-allowed"
                                           value="{{ old('tanggal_surat_jalan', $suratJalan->tanggal_surat_jalan?->format('Y-m-d')) }}"
                                           readonly
                                           disabled>
                                </div>
                                <div>
                                    <label for="nomor_surat_jalan" class="block text-xs font-medium text-gray-500 mb-2">
                                        Nomor Surat Jalan
                                    </label>
                                    <input type="text"
                                           name="nomor_surat_jalan"
                                           id="nomor_surat_jalan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm font-mono cursor-not-allowed"
                                           value="{{ old('nomor_surat_jalan', $suratJalan->no_surat_jalan) }}"
                                           placeholder="Nomor surat jalan"
                                           readonly
                                           disabled>
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
                                    <label for="tipe_kontainer" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tipe Kontainer
                                    </label>
                                    <select name="tipe_kontainer[]"
                                            id="tipe_kontainer"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tipe_kontainer.0') border-red-500 @enderror">
                                        <option value="">Pilih Tipe</option>
                                        <option value="HC" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'HC' ? 'selected' : '' }}>HC (High Cube)</option>
                                        <option value="STD" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'STD' ? 'selected' : '' }}>STD (Standard)</option>
                                        <option value="RF" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'RF' ? 'selected' : '' }}>RF (Reefer)</option>
                                        <option value="OT" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'OT' ? 'selected' : '' }}>OT (Open Top)</option>
                                        <option value="FR" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'FR' ? 'selected' : '' }}>FR (Flat Rack)</option>
                                        <option value="Dry Container" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                                        <option value="High Cube" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'High Cube' ? 'selected' : '' }}>High Cube</option>
                                        <option value="Reefer" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'Reefer' ? 'selected' : '' }}>Reefer</option>
                                        <option value="Open Top" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'Open Top' ? 'selected' : '' }}>Open Top</option>
                                        <option value="Flat Rack" {{ old('tipe_kontainer.0', $suratJalan->tipe_kontainer) == 'Flat Rack' ? 'selected' : '' }}>Flat Rack</option>
                                    </select>
                                    @error('tipe_kontainer.0')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
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
                                            class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm select2-pengirim cursor-not-allowed"
                                            readonly
                                            disabled>
                                        <option value="">-- Pilih Pengirim --</option>
                                        @foreach($pengirims as $pengirim)
                                            <option value="{{ $pengirim->nama_pengirim }}"
                                                    {{ old('pengirim', ($suratJalan->order && $suratJalan->order->pengirim ? $suratJalan->order->pengirim->nama_pengirim : '')) == $pengirim->nama_pengirim ? 'selected' : '' }}>
                                                {{ $pengirim->nama_pengirim }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($suratJalan->gambar_checkpoint)
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-2">
                                        Gambar Checkpoint
                                    </label>
                                    @php
                                        // Check if it's a JSON array (multiple images) or single image path
                                        $gambarCheckpoint = $suratJalan->gambar_checkpoint;
                                        $imagePaths = [];
                                        
                                        try {
                                            if (empty($gambarCheckpoint)) {
                                                $imagePaths = [];
                                            } elseif (is_array($gambarCheckpoint)) {
                                                $imagePaths = array_filter($gambarCheckpoint);
                                            } elseif (is_string($gambarCheckpoint)) {
                                                // Try to decode as JSON
                                                $decoded = json_decode($gambarCheckpoint, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $imagePaths = array_filter($decoded);
                                                } else {
                                                    // Single path
                                                    $imagePaths = [$gambarCheckpoint];
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            \Log::error('Error parsing gambar_checkpoint: ' . $e->getMessage());
                                            $imagePaths = [];
                                        }
                                    @endphp
                                    @if(count($imagePaths) > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($imagePaths as $index => $imagePath)
                                        @if(!empty($imagePath) && is_string($imagePath))
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
                                        @endif
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-camera mr-1"></i>
                                        {{ count($imagePaths) }} foto diupload saat checkpoint di lapangan
                                    </p>
                                    @else
                                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500">Tidak ada gambar checkpoint</p>
                                    </div>
                                    @endif
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="supir" class="block text-xs font-medium text-gray-500 mb-2">
                                        Nama Supir
                                    </label>
                                    <input type="text"
                                           name="supir"
                                           id="supir"
                                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm cursor-not-allowed"
                                           value="{{ old('supir', $suratJalan->supir) }}"
                                           placeholder="Nama supir"
                                           readonly
                                           disabled>
                                </div>
                                <div>
                                    <label for="supir_pengganti" class="block text-xs font-medium text-gray-500 mb-2">
                                        Supir Pengganti
                                    </label>
                                    <input type="text"
                                           name="supir_pengganti"
                                           id="supir_pengganti"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                           value="{{ old('supir_pengganti') }}"
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
                                           value="{{ old('no_plat') }}"
                                           placeholder="Nomor plat kendaraan">
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

                        <!-- Estimasi Nama Kapal, Nomor RO & Expired Date -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="estimasi_nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estimasi Nama Kapal
                                </label>
                                <select name="estimasi_nama_kapal"
                                        id="estimasi_nama_kapal"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-kapal @error('estimasi_nama_kapal') border-red-500 @enderror">
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
                            <div>
                                <label for="nomor_ro" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor RO
                                </label>
                                <input type="text"
                                       name="nomor_ro"
                                       id="nomor_ro"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono @error('nomor_ro') border-red-500 @enderror"
                                       placeholder="Masukkan nomor RO"
                                       value="{{ old('nomor_ro') }}">
                                @error('nomor_ro')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="expired_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Expired Date
                                </label>
                                <input type="date"
                                       name="expired_date"
                                       id="expired_date"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('expired_date') border-red-500 @enderror"
                                       value="{{ old('expired_date') }}">
                                @error('expired_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>Tanggal kadaluarsa
                                </p>
                            </div>
                        </div>

                        <!-- Tanggal Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Informasi Tanggal
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="tanggal_checkpoint_supir" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Checkpoint Supir
                                    </label>
                                    <input type="date"
                                           name="tanggal_checkpoint_supir"
                                           id="tanggal_checkpoint_supir"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_checkpoint_supir') border-red-500 @enderror"
                                           value="{{ old('tanggal_checkpoint_supir', $suratJalan->tanggal_checkpoint ? \Carbon\Carbon::parse($suratJalan->tanggal_checkpoint)->format('Y-m-d') : '') }}"
                                           required>
                                    @error('tanggal_checkpoint_supir')
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
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm {{ $errors->has('nama_barang.0') ? 'border-red-500' : '' }}"
                                           placeholder="Nama barang"
                                           value="{{ old('nama_barang.0') }}">
                                    @if($errors->has('nama_barang.0'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('nama_barang.0') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="jumlah_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Jumlah
                                    </label>
                                    <input type="number"
                                           name="jumlah[]"
                                           id="jumlah_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm {{ $errors->has('jumlah.0') ? 'border-red-500' : '' }}"
                                           placeholder="0"
                                           value="{{ old('jumlah.0') }}"
                                           min="0"
                                           step="1">
                                    @if($errors->has('jumlah.0'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('jumlah.0') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="satuan_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Satuan
                                    </label>
                                    <input type="text"
                                           name="satuan[]"
                                           id="satuan_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm {{ $errors->has('satuan.0') ? 'border-red-500' : '' }}"
                                           placeholder="Pcs, Kg, Box"
                                           value="{{ old('satuan.0') }}">
                                    @if($errors->has('satuan.0'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('satuan.0') }}</p>
                                    @endif
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
                                           class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm {{ $errors->has('panjang.0') ? 'border-red-500' : '' }}"
                                           placeholder="0.000"
                                           value="{{ old('panjang.0') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    @if($errors->has('panjang.0'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('panjang.0') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="lebar_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Lebar (m)
                                    </label>
                                    <input type="number"
                                           name="lebar[]"
                                           id="lebar_0"
                                           class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm {{ $errors->has('lebar.0') ? 'border-red-500' : '' }}"
                                           placeholder="0.000"
                                           value="{{ old('lebar.0') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    @if($errors->has('lebar.0'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('lebar.0') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="tinggi_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tinggi (m)
                                    </label>
                                    <input type="number"
                                           name="tinggi[]"
                                           id="tinggi_0"
                                           class="dimensi-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm {{ $errors->has('tinggi.0') ? 'border-red-500' : '' }}"
                                           placeholder="0.000"
                                           value="{{ old('tinggi.0') }}"
                                           min="0"
                                           step="0.001"
                                           onchange="calculateVolume(this.closest('.dimensi-row'))">
                                    @if($errors->has('tinggi.0'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('tinggi.0') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="meter_kubik_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Volume (m³)
                                    </label>
                                    <input type="number"
                                           name="meter_kubik[]"
                                           id="meter_kubik_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm {{ $errors->has('meter_kubik.0') ? 'border-red-500' : '' }}"
                                           placeholder="0.000"
                                           value="{{ old('meter_kubik.0') }}"
                                           min="0"
                                           step="0.001"
                                           readonly>
                                    @if($errors->has('meter_kubik.0'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('meter_kubik.0') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="tonase_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tonase (Ton)
                                    </label>
                                    <input type="number"
                                           name="tonase[]"
                                           id="tonase_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm {{ $errors->has('tonase.0') ? 'border-red-500' : '' }}"
                                           placeholder="0.000"
                                           value="{{ old('tonase.0') }}"
                                           min="0"
                                           step="0.001">
                                    @if($errors->has('tonase.0'))
                                        <p class="mt-1 text-xs text-red-600">{{ $errors->first('tonase.0') }}</p>
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Volume akan dihitung otomatis dari panjang × lebar × tinggi
                            </p>
                                </div>
                            </div>
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
                                    <select name="tujuan_pengiriman"
                                            id="tujuan_pengiriman"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm select2-tujuan-kirim @error('tujuan_pengiriman') border-red-500 @enderror">
                                        <option value="">-- Pilih Tujuan Pengiriman --</option>
                                        @foreach($masterTujuanKirims as $tujuan)
                                            <option value="{{ $tujuan->nama_tujuan }}"
                                                    {{ old('tujuan_pengiriman', $suratJalan->tujuan_pengiriman) == $tujuan->nama_tujuan ? 'selected' : '' }}>
                                                {{ $tujuan->nama_tujuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tujuan_pengiriman')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari tujuan pengiriman
                                    </p>
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

    // Create a mapping of kontainer number to its details (size and tipe)
    var kontainerDetailsMap = {
        @foreach($stockKontainers as $stock)
            "{{ $stock->nomor_seri_gabungan }}": {
                size: "{{ $stock->ukuran }}",
                tipe: "{{ $stock->tipe_kontainer }}"
            },
        @endforeach
    };

    console.log('Kontainer details mapping loaded:', kontainerDetailsMap);

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

            // Initialize Select2 for tujuan kirim dropdown
            $('.select2-tujuan-kirim').select2({
                placeholder: '-- Pilih Tujuan Pengiriman --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Tujuan pengiriman tidak ditemukan";
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

            // Auto-fill nomor kontainer and tipe kontainer when selected from dropdown
            $('#nomor_kontainer').on('select2:select', function(e) {
                var selectedValue = e.params.data.id;
                console.log('Nomor kontainer selected:', selectedValue);
                
                // Extract just the container number (before the opening parenthesis if exists)
                var containerNumber = selectedValue.split(' (')[0].trim();
                console.log('Extracted container number:', containerNumber);
                
                // Auto-fill size and tipe kontainer based on selected kontainer
                if (kontainerDetailsMap[containerNumber]) {
                    var details = kontainerDetailsMap[containerNumber];
                    console.log('Found kontainer details:', details);
                    
                    // Set size dropdown (without triggering filter)
                    if (details.size) {
                        $('#size').val(details.size);
                        console.log('✓ Size auto-filled:', details.size);
                    }
                    
                    // Set tipe kontainer dropdown
                    if (details.tipe) {
                        $('#tipe_kontainer').val(details.tipe);
                        console.log('✓ Tipe kontainer auto-filled:', details.tipe);
                    }
                } else {
                    console.log('No details found for kontainer:', containerNumber);
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
            } else {
                console.error('✗ Select2 plugin not loaded!');
            }
        });
    })();

    // Scroll to alert block if present
    jQuery(document).ready(function($) {
        var $alert = $('.server-error, .validation-errors').first();
        if ($alert && $alert.length) {
            // Smooth scroll to the alert and highlight
            $('html, body').animate({ scrollTop: $alert.offset().top - 80 }, 300);
            $alert.addClass('ring-2 ring-red-200');
            setTimeout(function() { $alert.removeClass('ring-2 ring-red-200'); }, 3000);
        }
    });

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

    // Counter untuk index dimensi baru
    let dimensiCounter = document.querySelectorAll('#dimensi-container .dimensi-row').length || 1;

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
</script>
@endpush
