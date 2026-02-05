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

        <form action="{{ route('tanda-terima.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
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

                        @if(strtolower($suratJalan->tipe_kontainer ?? '') != 'cargo')
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
                                    <!-- Input Gudang -->
                                    <div class="mt-3">
                                        <label for="gudang" class="block text-xs font-medium text-gray-500 mb-2">
                                            Gudang <span class="text-red-500">*</span>
                                        </label>
                                        <select name="gudang_id"
                                                id="gudang"
                                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm select2-gudang @error('gudang_id') border-red-500 @enderror">

                                            <option value="">-- Pilih Gudang --</option>
                                            @foreach($gudangs as $gudang)
                                                <option value="{{ $gudang->id }}"
                                                        {{ old('gudang_id') == $gudang->id ? 'selected' : '' }}>
                                                    {{ $gudang->nama_gudang }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('gudang_id')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500">
                                            <i class="fas fa-info-circle mr-1"></i>Pilih gudang untuk kontainer ini
                                        </p>
                                    </div>
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
                        @endif

                        <!-- Data Pengirim & Order Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Data Pengirim & Penerima
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
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="penerima" class="block text-xs font-medium text-gray-500">
                                            Penerima <span class="text-red-500">*</span>
                                        </label>
                                        <button type="button"
                                                onclick="openPenerimaPopup()"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-300 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-plus mr-1"></i>
                                            Tambah Penerima Baru
                                        </button>
                                    </div>
                                    <select name="penerima"
                                            id="penerima"
                                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm select2-penerima @error('penerima') border-red-500 @enderror">
                                        <option value="">-- Pilih Penerima --</option>
                                        @foreach($masterPenerimaList as $penerima)
                                            <option value="{{ $penerima->nama_penerima }}"
                                                    data-alamat="{{ $penerima->alamat }}"
                                                    {{ old('penerima', $suratJalan->order->penerima ?? '') == $penerima->nama_penerima ? 'selected' : '' }}>
                                                {{ $penerima->nama_penerima }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('penerima')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari penerima
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="alamat_penerima" class="block text-xs font-medium text-gray-500 mb-2">
                                        Alamat Penerima
                                    </label>
                                    <textarea name="alamat_penerima"
                                              id="alamat_penerima"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('alamat_penerima') border-red-500 @enderror"
                                              placeholder="Alamat lengkap penerima">{{ old('alamat_penerima', $suratJalan->order->alamat_penerima ?? '') }}</textarea>
                                    @error('alamat_penerima')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                        <i class="fas fa-info-circle mr-1"></i>Alamat akan terisi otomatis saat memilih penerima, namun dapat diubah sesuai kebutuhan
                                    </p>
                                </div>
                                @if($suratJalan->gambar_checkpoint)
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-2">
                                        Gambar Checkpoint Saat Ini
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

                                <!-- Upload Gambar Checkpoint Baru -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-2">
                                        <i class="fas fa-upload mr-1 text-blue-600"></i>
                                        Upload Gambar Checkpoint Baru
                                    </label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors upload-dropzone">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="gambar_checkpoint" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Upload gambar</span>
                                                    <input id="gambar_checkpoint" 
                                                           name="gambar_checkpoint[]" 
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
                                    @error('gambar_checkpoint.*')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Catatan:</strong> Gambar yang diupload akan menggantikan/menambah gambar checkpoint yang sudah ada pada surat jalan ini.
                                    </p>
                                    
                                    <!-- Preview Area for New Images -->
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
                                    <select name="supir_pengganti"
                                            id="supir_pengganti"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm select2-supir-pengganti @error('supir_pengganti') border-red-500 @enderror">
                                        <option value="">-- Pilih Supir Pengganti --</option>
                                        @foreach($karyawanSupirs as $supir)
                                            <option value="{{ $supir->nama_lengkap }}"
                                                    data-plat="{{ $supir->plat ?? 'N/A' }}"
                                                    {{ old('supir_pengganti') == $supir->nama_lengkap ? 'selected' : '' }}>
                                                {{ $supir->nama_lengkap }}{{ $supir->plat ? ' (' . $supir->plat . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supir_pengganti')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari supir pengganti
                                    </p>
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
                                        Nama Kenek
                                    </label>
                                    <input type="text"
                                           name="kenek"
                                           id="kenek"
                                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-sm cursor-not-allowed"
                                           value="{{ old('kenek', $suratJalan->kenek) }}"
                                           placeholder="Nama kenek"
                                           readonly
                                           disabled>
                                </div>
                                <div>
                                    <label for="kenek_pengganti" class="block text-xs font-medium text-gray-500 mb-2">
                                        Kenek Pengganti
                                    </label>
                                    <select name="kenek_pengganti"
                                            id="kenek_pengganti"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm select2-kenek-pengganti @error('kenek_pengganti') border-red-500 @enderror">
                                        <option value="">-- Pilih Kenek Pengganti --</option>
                                        @foreach($kranisKenek as $kenek)
                                            <option value="{{ $kenek->nama_lengkap }}"
                                                    data-plat="{{ $kenek->plat }}"
                                                    {{ old('kenek_pengganti') == $kenek->nama_lengkap ? 'selected' : '' }}>
                                                {{ $kenek->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kenek_pengganti')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari kenek pengganti
                                    </p>
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

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="tanggal" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Tanda Terima <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           name="tanggal"
                                           id="tanggal"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal') border-red-500 @enderror"
                                           value="{{ old('tanggal', date('Y-m-d')) }}"
                                           required>
                                    @error('tanggal')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tanggal_checkpoint_supir" class="block text-xs font-medium text-gray-500 mb-2">
                                        Tanggal Checkpoint Supir <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           name="tanggal_checkpoint_supir"
                                           id="tanggal_checkpoint_supir"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_checkpoint_supir') border-red-500 @enderror"
                                           value="{{ old('tanggal_checkpoint_supir', $suratJalan->tanggal_checkpoint ? \Carbon\Carbon::parse($suratJalan->tanggal_checkpoint)->format('Y-m-d') : date('Y-m-d')) }}"
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
                                           value="{{ old('tanggal_terima_pelabuhan', date('Y-m-d')) }}">
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
                                           value="{{ old('tonase.0', 15) }}"
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

@push('scripts')
<!-- Select2 JS - jQuery already loaded in layout -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    console.log('=== Script Loading Started ===');
    console.log('jQuery available:', typeof jQuery !== 'undefined');
    console.log('$ available:', typeof $ !== 'undefined');
    
    // Create a mapping of supir names to plat numbers
    var supirPlatMap = {};
    @foreach($karyawans as $karyawan)
        supirPlatMap["{{ $karyawan->nama_lengkap }}"] = "{{ $karyawan->plat ?? 'N/A' }}";
    @endforeach

    console.log('Supir-Plat mapping loaded:', supirPlatMap);

    // Create a mapping of kontainer number to its details (size)
    var kontainerDetailsMap = {};
    @foreach($stockKontainers as $stock)
        kontainerDetailsMap["{{ $stock->nomor_seri_gabungan }}"] = {
            size: "{{ $stock->ukuran }}"
        };
    @endforeach

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

            // Initialize Select2 for penerima dropdown
            $('.select2-penerima').select2({
                placeholder: '-- Pilih Penerima --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Penerima tidak ditemukan";
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
                
                console.log('Penerima selected:', e.params.data.id);
                console.log('Alamat:', alamat);
                
                if (alamat) {
                    $('#alamat_penerima').val(alamat);
                    console.log('✓ Alamat penerima auto-filled');
                } else {
                    $('#alamat_penerima').val('');
                }
            });

            // Clear alamat when penerima is cleared
            $('#penerima').on('select2:clear', function(e) {
                $('#alamat_penerima').val('');
                console.log('✓ Alamat penerima cleared');
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

            // Initialize Select2 for supir pengganti dropdown
            $('.select2-supir-pengganti').select2({
                placeholder: '-- Pilih Supir Pengganti --',
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

            // Initialize Select2 for kenek pengganti dropdown
            $('.select2-kenek-pengganti').select2({
                placeholder: '-- Pilih Kenek Pengganti --',
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

            console.log('Select2 initialized for all dropdowns');

            // Auto-fill plat nomor berdasarkan supir default dari surat jalan
            function autoFillPlatFromSupir() {
                var currentSupir = $('#supir').val();
                console.log('=== Auto-filling plat for current supir ===');
                console.log('Current supir:', currentSupir);
                console.log('Available supir-plat mapping:', supirPlatMap);
                
                if (currentSupir && supirPlatMap[currentSupir]) {
                    var platNomor = supirPlatMap[currentSupir];
                    console.log('Found plat in mapping:', platNomor);
                    
                    if (platNomor && platNomor !== '' && platNomor !== 'N/A') {
                        $('#no_plat').val(platNomor);
                        console.log('✓ Default plat auto-filled for supir:', currentSupir, '->', platNomor);
                    } else {
                        console.log('⚠ Plat is empty or N/A for supir:', currentSupir);
                    }
                } else {
                    console.log('⚠ Supir not found in mapping:', currentSupir);
                    // Check if there's an existing plat value from surat jalan
                    var existingPlat = '{{ old("no_plat", $suratJalan->no_plat ?? "") }}';
                    if (existingPlat && existingPlat !== '') {
                        $('#no_plat').val(existingPlat);
                        console.log('✓ Used existing plat from surat jalan:', existingPlat);
                    }
                }
            }

            // Auto-fill plat on page load
            autoFillPlatFromSupir();

            // Auto-fill nomor kontainer when selected from dropdown
            $('#nomor_kontainer').on('select2:select', function(e) {
                var selectedValue = e.params.data.id;
                console.log('Nomor kontainer selected:', selectedValue);
                
                // Extract just the container number (before the opening parenthesis if exists)
                var containerNumber = selectedValue.split(' (')[0].trim();
                console.log('Extracted container number:', containerNumber);
                
                // Auto-fill size based on selected kontainer
                if (kontainerDetailsMap[containerNumber]) {
                    var details = kontainerDetailsMap[containerNumber];
                    console.log('Found kontainer details:', details);
                    
                    // Set size dropdown (without triggering filter)
                    if (details.size) {
                        $('#size').val(details.size);
                        console.log('✓ Size auto-filled:', details.size);
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

            // Auto-fill plat nomor when supir pengganti selected and update original supir field
            $('#supir_pengganti').on('select2:select', function(e) {
                var selectedSupir = e.params.data.id;
                var platNomor = $(e.params.data.element).data('plat');
                
                console.log('Supir pengganti selected:', selectedSupir);
                console.log('Plat nomor from element:', platNomor);
                
                // Try to get plat from mapping if not found in element
                if (!platNomor || platNomor === '' || platNomor === 'N/A') {
                    platNomor = supirPlatMap[selectedSupir];
                    console.log('Plat nomor from mapping:', platNomor);
                }
                
                // Auto-fill plat nomor if available
                if (platNomor && platNomor !== '' && platNomor !== 'N/A') {
                    $('#no_plat').val(platNomor);
                    console.log('✓ Plat nomor auto-filled:', platNomor);
                } else {
                    console.log('⚠ Plat nomor not available for:', selectedSupir);
                }
                
                // Update original supir field with selected supir pengganti
                // This will be sent to controller to update surat jalan
                $('#supir').val(selectedSupir);
                console.log('✓ Original supir field updated with supir pengganti:', selectedSupir);
            });

            // Clear supir field when supir pengganti is cleared
            $('#supir_pengganti').on('select2:clear', function(e) {
                // Reset supir field to original value from surat jalan
                var originalSupir = '{{ old("supir", $suratJalan->supir) }}';
                $('#supir').val(originalSupir);
                console.log('✓ Supir field reset to original:', originalSupir);
                
                // Auto-fill plat for original supir
                autoFillPlatFromSupir();
            });

            // Auto-fill when kenek pengganti selected and update original kenek field
            $('#kenek_pengganti').on('select2:select', function(e) {
                var selectedKenek = e.params.data.id;
                
                console.log('Kenek pengganti selected:', selectedKenek);
                
                // Update original kenek field with selected kenek pengganti
                // This will be sent to controller to update surat jalan
                $('#kenek').val(selectedKenek);
                console.log('✓ Original kenek field updated with kenek pengganti:', selectedKenek);
            });

            // Clear kenek field when kenek pengganti is cleared
            $('#kenek_pengganti').on('select2:clear', function(e) {
                // Reset kenek field to original value from surat jalan
                var originalKenek = '{{ old("kenek", $suratJalan->kenek) }}';
                $('#kenek').val(originalKenek);
                console.log('✓ Kenek field reset to original:', originalKenek);
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

    // Listen for message from popup when new penerima is added
    window.addEventListener('message', function(event) {
        // Verify origin for security
        if (event.origin !== window.location.origin) return;
        
        if (event.data.type === 'penerimaAdded') {
            const newPenerima = event.data.penerima;
            
            // Add new option to select
            const select = $('#penerima');
            const newOption = new Option(newPenerima.nama, newPenerima.nama, true, true);
            $(newOption).attr('data-alamat', newPenerima.alamat || '');
            select.append(newOption);
            
            // Trigger select2 change and auto-fill alamat
            select.trigger('change');
            $('#alamat_penerima').val(newPenerima.alamat || '');
            
            console.log('✓ New penerima added:', newPenerima.nama);
        }
    });

    function calculateVolume(rowElement) {
        const panjangInput = rowElement ? rowElement.querySelector('[name^="panjang"]') : document.getElementById('panjang_0');
        const lebarInput = rowElement ? rowElement.querySelector('[name^="lebar"]') : document.getElementById('lebar_0');
        const tinggiInput = rowElement ? rowElement.querySelector('[name^="tinggi"]') : document.getElementById('tinggi_0');
        const jumlahInput = rowElement ? rowElement.querySelector('[name^="jumlah"]') : document.getElementById('jumlah_0');
        const volumeInput = rowElement ? rowElement.querySelector('[name^="meter_kubik"]') : document.getElementById('meter_kubik_0');

        const panjang = parseFloat(panjangInput.value) || 0;
        const lebar = parseFloat(lebarInput.value) || 0;
        const tinggi = parseFloat(tinggiInput.value) || 0;
        const jumlah = parseFloat(jumlahInput.value) || 0;

        if (panjang > 0 && lebar > 0 && tinggi > 0 && jumlah > 0) {
            const volume = panjang * tinggi * lebar * jumlah;
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

<!-- JavaScript untuk Image Upload dan Preview -->
<script>
    // Preview uploaded images
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
        const input = document.getElementById('gambar_checkpoint');
        const previewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('image-preview-grid');
        
        // Remove the preview div
        button.closest('.relative').parentNode.remove();
        
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
        const dropZone = document.querySelector('.border-dashed');
        const fileInput = document.getElementById('gambar_checkpoint');
        
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
</script>
@endpush
