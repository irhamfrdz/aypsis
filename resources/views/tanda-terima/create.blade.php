@extends('layouts.app')

@section('title', 'Buat Tanda Terima')

@section('content')
@php
    // Parse container data from surat jalan
    $nomorKontainerArray = [];
    if (!empty($suratJalan->no_kontainer)) {
        $nomorKontainerArray = array_map('trim', explode(',', $suratJalan->no_kontainer));
    }
    
    $sizeArray = [];
    if (!empty($suratJalan->size)) {
        $sizeArray = array_map('trim', explode(',', $suratJalan->size));
    }
    
    $noSealArray = [];
    if (!empty($suratJalan->no_seal)) {
        $noSealArray = array_map('trim', explode(',', $suratJalan->no_seal));
    }
    
    $jumlahKontainer = old('jumlah_kontainer', ($suratJalan->jumlah_kontainer ?: (count($nomorKontainerArray) ?: 1)));

    $dimensiItems = [];
    if ($suratJalan->order && $suratJalan->order->dimensi_items) {
        $dimensiItems = $suratJalan->order->dimensi_items;
        if (is_string($dimensiItems)) {
            $dimensiItems = json_decode($dimensiItems, true) ?? [];
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
                        <div id="section-kontainer">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Data Kontainer
                                </label>
                                <div class="flex items-center space-x-2">
                                    <label for="jumlah_kontainer" class="text-xs font-medium text-gray-500">Jumlah:</label>
                                    <input type="number"
                                           name="jumlah_kontainer"
                                           id="jumlah_kontainer"
                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ $jumlahKontainer }}"
                                           min="1"
                                           onchange="updateKontainerRows(this.value)">
                                </div>
                            </div>

                            <div id="kontainer-rows-wrapper" class="space-y-4">
                                @for($i = 0; $i < $jumlahKontainer; $i++)
                                <div class="kontainer-row bg-gray-50 p-4 rounded-lg border border-gray-200 relative pt-6" data-index="{{ $i }}">
                                    <div class="absolute top-0 left-0 px-3 py-1 bg-blue-600 text-white text-[10px] uppercase font-bold rounded-br-lg rounded-tl-lg shadow-sm">
                                        Container #{{ $i + 1 }}
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">No. Kontainer</label>
                                            <select name="nomor_kontainer[]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm font-mono select2-kontainer">
                                                <option value="">-- Pilih atau Ketik No. Kontainer --</option>
                                                @foreach($stockKontainers as $stock)
                                                    <option value="{{ $stock->nomor_seri_gabungan }}"
                                                            {{ old("nomor_kontainer.$i", $nomorKontainerArray[$i] ?? '') == $stock->nomor_seri_gabungan ? 'selected' : '' }}>
                                                        {{ $stock->nomor_seri_gabungan }} ({{ $stock->ukuran }}ft)
                                                    </option>
                                                @endforeach
                                                @if(old("nomor_kontainer.$i", $nomorKontainerArray[$i] ?? ''))
                                                    @php $currentNo = old("nomor_kontainer.$i", $nomorKontainerArray[$i] ?? ''); @endphp
                                                    @if(!$stockKontainers->contains('nomor_seri_gabungan', $currentNo))
                                                        <option value="{{ $currentNo }}" selected>{{ $currentNo }}</option>
                                                    @endif
                                                @endif
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">Size</label>
                                            <select name="size[]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm kontainer-size">
                                                <option value="">Pilih Size</option>
                                                <option value="20" {{ old("size.$i", ($sizeArray[$i] ?? $suratJalan->size)) == '20' ? 'selected' : '' }}>20</option>
                                                <option value="40" {{ old("size.$i", ($sizeArray[$i] ?? $suratJalan->size)) == '40' ? 'selected' : '' }}>40</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">No. Seal</label>
                                            <input type="text"
                                                   name="no_seal[]"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm font-mono"
                                                   placeholder="Nomor seal"
                                                   value="{{ old("no_seal.$i", $noSealArray[$i] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>

                            <!-- Input Gudang Grouped -->
                            <div class="mt-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <label for="gudang" class="block text-xs font-medium text-gray-500 mb-2">
                                    Gudang Tujuan <span class="text-red-500">*</span>
                                </label>
                                <select name="gudang_id"
                                        id="gudang"
                                        class="w-full px-3 py-2 border border-gray-300 rounded text-sm select2-gudang @error('gudang_id') border-red-500 @enderror">
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
                                <p class="mt-1 text-[10px] text-gray-500 italic">
                                    <i class="fas fa-info-circle mr-1"></i>Pilih gudang untuk menyimpan kontainer-kontainer ini dalam sistem
                                </p>
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
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="pengirim" class="block text-xs font-medium text-gray-500">
                                            Pengirim
                                        </label>
                                        <button type="button"
                                                onclick="openPengirimPopup()"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-300 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-plus mr-1"></i>
                                            Tambah Pengirim Baru
                                        </button>
                                    </div>
                                    <select name="pengirim"
                                            id="pengirim"
                                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm select2-pengirim">
                                        <option value="">-- Pilih Pengirim --</option>
                                        @foreach($pengirims as $pengirim)
                                            <option value="{{ $pengirim->nama_pengirim }}"
                                                    data-alamat="{{ $pengirim->alamat }}"
                                                    {{ old('pengirim', ($suratJalan->order && $suratJalan->order->pengirim ? $suratJalan->order->pengirim->nama_pengirim : '')) == $pengirim->nama_pengirim ? 'selected' : '' }}>
                                                {{ $pengirim->nama_pengirim }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="pic_pengirim" class="block text-xs font-medium text-gray-500 mb-2">
                                        PIC Pengirim
                                    </label>
                                    <input type="text"
                                           name="pic_pengirim"
                                           id="pic_pengirim"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('pic_pengirim') border-red-500 @enderror"
                                           placeholder="Nama PIC pengirim"
                                           value="{{ old('pic_pengirim') }}">
                                    @error('pic_pengirim')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label for="alamat_pengirim" class="block text-xs font-medium text-gray-500 mb-2">
                                        Alamat Pengirim
                                    </label>
                                    <textarea name="alamat_pengirim"
                                              id="alamat_pengirim"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('alamat_pengirim') border-red-500 @enderror"
                                              placeholder="Alamat lengkap pengirim">{{ old('alamat_pengirim', ($suratJalan->order ? $suratJalan->order->alamat_pengirim : '')) }}</textarea>
                                    @error('alamat_pengirim')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                        <i class="fas fa-info-circle mr-1"></i>Alamat pengirim akan terisi otomatis saat memilih pengirim, namun dapat diubah sesuai kebutuhan
                                    </p>
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
                                            @php
                                                // Prioritize notify_party if available, otherwise use penerima
                                                $selectedPenerima = null;
                                                if ($suratJalan->order && $suratJalan->order->notify_party_id) {
                                                    // Use notify party if available
                                                    $notifyParty = \App\Models\Penerima::find($suratJalan->order->notify_party_id);
                                                    if ($notifyParty) {
                                                        $selectedPenerima = $notifyParty->nama_penerima;
                                                    }
                                                } elseif ($suratJalan->order && $suratJalan->order->penerima_id) {
                                                    // Use regular penerima if no notify party
                                                    $regularPenerima = \App\Models\Penerima::find($suratJalan->order->penerima_id);
                                                    if ($regularPenerima) {
                                                        $selectedPenerima = $regularPenerima->nama_penerima;
                                                    }
                                                } elseif ($suratJalan->order) {
                                                    // Fallback to penerima string field
                                                    $selectedPenerima = $suratJalan->order->penerima;
                                                }
                                                
                                                $isSelected = old('penerima', $selectedPenerima) == $penerima->nama_penerima;
                                            @endphp
                                            <option value="{{ $penerima->nama_penerima }}"
                                                    data-alamat="{{ $penerima->alamat }}"
                                                    {{ $isSelected ? 'selected' : '' }}>
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
                                <div>
                                    <label for="pic_penerima" class="block text-xs font-medium text-gray-500 mb-2">
                                        PIC Penerima
                                    </label>
                                    <input type="text"
                                           name="pic_penerima"
                                           id="pic_penerima"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('pic_penerima') border-red-500 @enderror"
                                           placeholder="Nama PIC penerima"
                                           value="{{ old('pic_penerima') }}">
                                    @error('pic_penerima')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label for="alamat_penerima" class="block text-xs font-medium text-gray-500 mb-2">
                                        Alamat Penerima
                                    </label>
                                    @php
                                        // Prioritize notify_party address if available
                                        $alamatPenerima = '';
                                        if ($suratJalan->order && $suratJalan->order->notify_party_id) {
                                            $notifyParty = \App\Models\Penerima::find($suratJalan->order->notify_party_id);
                                            if ($notifyParty) {
                                                $alamatPenerima = $notifyParty->alamat;
                                            }
                                        } elseif ($suratJalan->order && $suratJalan->order->penerima_id) {
                                            $regularPenerima = \App\Models\Penerima::find($suratJalan->order->penerima_id);
                                            if ($regularPenerima) {
                                                $alamatPenerima = $regularPenerima->alamat;
                                            }
                                        } elseif ($suratJalan->order) {
                                            $alamatPenerima = $suratJalan->order->alamat_penerima;
                                        }
                                    @endphp
                                    <textarea name="alamat_penerima"
                                              id="alamat_penerima"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('alamat_penerima') border-red-500 @enderror"
                                              placeholder="Alamat lengkap penerima">{{ old('alamat_penerima', $alamatPenerima) }}</textarea>
                                    @error('alamat_penerima')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                        <i class="fas fa-info-circle mr-1"></i>Alamat akan terisi otomatis saat memilih penerima, namun dapat diubah sesuai kebutuhan
                                    </p>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="notify_party" class="block text-xs font-medium text-gray-500">
                                            Notify Party
                                        </label>
                                        <button type="button"
                                                onclick="openNotifyPopup()"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-300 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-plus mr-1"></i>
                                            Tambah Notify Party Baru
                                        </button>
                                    </div>
                                    <select name="notify_party"
                                            id="notify_party"
                                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm select2-notify @error('notify_party') border-red-500 @enderror">
                                        <option value="">-- Pilih Notify Party --</option>
                                        @foreach($masterPenerimaList as $penerima)
                                            @php
                                                $selectedNotify = null;
                                                if ($suratJalan->order && $suratJalan->order->notify_party_id) {
                                                    $notifyParty = \App\Models\Penerima::find($suratJalan->order->notify_party_id);
                                                    if ($notifyParty) {
                                                        $selectedNotify = $notifyParty->nama_penerima;
                                                    }
                                                }
                                                $isSelected = old('notify_party', $selectedNotify) == $penerima->nama_penerima;
                                            @endphp
                                            <option value="{{ $penerima->nama_penerima }}"
                                                    data-alamat="{{ $penerima->alamat }}"
                                                    {{ $isSelected ? 'selected' : '' }}>
                                                {{ $penerima->nama_penerima }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('notify_party')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-search mr-1"></i>Ketik untuk mencari notify party
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="alamat_notify_party" class="block text-xs font-medium text-gray-500 mb-2">
                                        Alamat Notify Party
                                    </label>
                                    @php
                                        $alamatNotify = '';
                                        if ($suratJalan->order && $suratJalan->order->notify_party_id) {
                                            $notifyParty = \App\Models\Penerima::find($suratJalan->order->notify_party_id);
                                            if ($notifyParty) {
                                                $alamatNotify = $notifyParty->alamat;
                                            }
                                        }
                                    @endphp
                                    <textarea name="alamat_notify_party"
                                              id="alamat_notify_party"
                                              rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('alamat_notify_party') border-red-500 @enderror"
                                              placeholder="Alamat lengkap Notify Party">{{ old('alamat_notify_party', $alamatNotify) }}</textarea>
                                    @error('alamat_notify_party')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
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
                                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,application/pdf"
                                                           onchange="previewImages(this)">
                                                </label>
                                                <p class="pl-1">atau drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PNG, JPG, JPEG, GIF, WEBP, PDF sampai 10MB per file (max 5 file)
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
                                    <select name="kenek" id="kenek"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm select2-kenek @error('kenek') border-red-500 @enderror">
                                        <option value="">-- Pilih Kenek --</option>
                                        @foreach($kranisKenek as $k)
                                            <option value="{{ $k->nama_lengkap }}"
                                                    {{ old('kenek', $suratJalan->kenek) == $k->nama_lengkap ? 'selected' : '' }}>
                                                {{ $k->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kenek')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
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
                                
                                <!-- Checkbox Lembur & Nginap -->
                                <div class="col-span-1 md:col-span-2 flex space-x-6 mt-2 items-center bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               name="lembur"
                                               id="lembur"
                                               value="1"
                                               class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               {{ old('lembur', $suratJalan->lembur) ? 'checked' : '' }}>
                                        <label for="lembur" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                                            Lembur
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               name="nginap"
                                               id="nginap"
                                               value="1"
                                               class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               {{ old('nginap', $suratJalan->nginap) ? 'checked' : '' }}>
                                        <label for="nginap" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                                            Nginap
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               name="tidak_lembur_nginap"
                                               id="tidak_lembur_nginap"
                                               value="1"
                                               class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               {{ old('tidak_lembur_nginap', $suratJalan->tidak_lembur_nginap) ? 'checked' : '' }}>
                                        <label for="tidak_lembur_nginap" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer select-none">
                                            Tidak Lembur & Nginap
                                        </label>
                                    </div>
                                    <div class="ml-auto text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-info-circle mr-1 text-yellow-600"></i>
                                        Pilih minimal satu
                                    </div>
                                </div>
                                @if($errors->has('lembur') || $errors->has('nginap') || $errors->has('tidak_lembur_nginap'))
                                    <p class="mt-1 text-xs text-red-600">
                                        Harap pilih minimal satu opsi (Lembur, Nginap, atau Tidak Lembur & Nginap).
                                    </p>
                                @endif
                                
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const lembur = document.getElementById('lembur');
                                        const nginap = document.getElementById('nginap');
                                        const tidakLemburNginap = document.getElementById('tidak_lembur_nginap');
                                        
                                        function updateCheckboxes(event) {
                                            // Identify which checkbox triggered the change
                                            const target = event ? event.target : null;

                                            if (target === tidakLemburNginap && tidakLemburNginap.checked) {
                                                // If 'Tidak Lembur & Nginap' is checked, uncheck others
                                                lembur.checked = false;
                                                nginap.checked = false;
                                            } else if ((target === lembur || target === nginap) && (lembur.checked || nginap.checked)) {
                                                // If 'Lembur' or 'Nginap' is checked, uncheck 'Tidak Lembur & Nginap'
                                                tidakLemburNginap.checked = false;
                                            }
                                        }
                                        
                                        lembur.addEventListener('change', updateCheckboxes);
                                        nginap.addEventListener('change', updateCheckboxes);
                                        tidakLemburNginap.addEventListener('change', updateCheckboxes);
                                        
                                        // Initial run logic check
                                        if (tidakLemburNginap.checked) {
                                            lembur.checked = false;
                                            nginap.checked = false;
                                        } else if (lembur.checked || nginap.checked) {
                                            tidakLemburNginap.checked = false;
                                        }
                                    });
                                </script>
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
                                <label for="surat_jalan_pabrik" class="block text-sm font-medium text-gray-700 mb-2">
                                    Surat Jalan Pabrik
                                </label>
                                <input type="text"
                                       name="surat_jalan_pabrik"
                                       id="surat_jalan_pabrik"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono @error('surat_jalan_pabrik') border-red-500 @enderror"
                                       placeholder="Masukkan nomor surat jalan pabrik"
                                       value="{{ old('surat_jalan_pabrik') }}">
                                @error('surat_jalan_pabrik')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="no_dn" class="block text-sm font-medium text-gray-700 mb-2">
                                    No. Dn
                                </label>
                                <input type="text"
                                       name="no_dn"
                                       id="no_dn"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono @error('no_dn') border-red-500 @enderror"
                                       placeholder="Masukkan nomor DN"
                                       value="{{ old('no_dn') }}">
                                @error('no_dn')
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
                                @if(count($dimensiItems) > 0)
                                    @foreach($dimensiItems as $index => $item)
                                    <div class="dimensi-row mb-4 pb-4 border-b border-purple-200 relative">
                                        @if($index > 0)
                                        <button type="button" class="remove-dimensi-btn absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        @endif
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 dimensi-info-grid">
                                            <div>
                                                <label for="nama_barang_{{ $index }}" class="block text-xs font-medium text-gray-500 mb-2">
                                                    Nama Barang
                                                </label>
                                                <input type="text"
                                                       name="nama_barang[]"
                                                       id="nama_barang_{{ $index }}"
                                                       class="nama-barang-input w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm {{ $errors->has('nama_barang.' . $index) ? 'border-red-500' : '' }}"
                                                       placeholder="Nama barang"
                                                       value="{{ old('nama_barang.' . $index, $item['nama_barang'] ?? '') }}"
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
                                                       value="{{ old('ukuran.' . $index, $item['ukuran'] ?? '') }}">
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
                                                       value="{{ old('jumlah.' . $index, $item['jumlah'] ?? '') }}"
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
                                                       value="{{ old('satuan.' . $index, $item['satuan'] ?? '') }}">
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
                                                       value="{{ old('panjang.' . $index, $item['panjang'] ?? '') }}"
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
                                                       value="{{ old('lebar.' . $index, $item['lebar'] ?? '') }}"
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
                                                       value="{{ old('tinggi.' . $index, $item['tinggi'] ?? '') }}"
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
                                                       value="{{ old('meter_kubik.' . $index, $item['meter_kubik'] ?? '') }}"
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
                                                       value="{{ old('tonase.' . $index, $item['tonase'] ?? (count($dimensiItems) == 0 ? 15 : '')) }}"
                                                       min="0"
                                                       step="0.001">
                                            </div>
                                        </div>
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
                                                       placeholder="Contoh: 40x40">
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
                                                       placeholder="Pcs, Kg, Box">
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
                                                       value="15"
                                                       min="0"
                                                       step="0.001">
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="estimasi_nama_kapal" class="block text-xs font-medium text-gray-500 mb-2">
                                            Estimasi Nama Kapal
                                        </label>
                                        <input type="text"
                                               name="estimasi_nama_kapal"
                                               id="estimasi_nama_kapal"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('estimasi_nama_kapal') border-red-500 @enderror"
                                               placeholder="Contoh: Meratus"
                                               value="{{ old('estimasi_nama_kapal') }}">
                                        @error('estimasi_nama_kapal')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="nomor_ro" class="block text-xs font-medium text-gray-500 mb-2">
                                            Nomor RO
                                        </label>
                                        <input type="text"
                                               name="nomor_ro"
                                               id="nomor_ro"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('nomor_ro') border-red-500 @enderror"
                                               placeholder="Masukkan nomor RO"
                                               value="{{ old('nomor_ro') }}">
                                        @error('nomor_ro')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="surat_jalan_pabrik" class="block text-xs font-medium text-gray-500 mb-2">
                                            Surat Jalan Pabrik
                                        </label>
                                        <input type="text"
                                               name="surat_jalan_pabrik"
                                               id="surat_jalan_pabrik"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('surat_jalan_pabrik') border-red-500 @enderror"
                                               placeholder="SJ-PABRIK-001"
                                               value="{{ old('surat_jalan_pabrik') }}">
                                        @error('surat_jalan_pabrik')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="tanggal_surat_jalan_pabrik" class="block text-xs font-medium text-gray-500 mb-2">
                                            Tanggal Surat Jalan Pabrik
                                        </label>
                                        <input type="date"
                                               name="tanggal_surat_jalan_pabrik"
                                               id="tanggal_surat_jalan_pabrik"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('tanggal_surat_jalan_pabrik') border-red-500 @enderror"
                                               value="{{ old('tanggal_surat_jalan_pabrik') }}">
                                        @error('tanggal_surat_jalan_pabrik')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
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

            // Auto-fill alamat pengirim when pengirim is selected
            $('#pengirim').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                
                console.log('Pengirim selected:', e.params.data.id);
                console.log('Alamat Pengirim:', alamat);
                
                if (alamat) {
                    $('#alamat_pengirim').val(alamat);
                    console.log('✓ Alamat pengirim auto-filled');
                } else {
                    $('#alamat_pengirim').val('');
                }
            });

            // Clear alamat when pengirim is cleared
            $('#pengirim').on('select2:clear', function(e) {
                $('#alamat_pengirim').val('');
                console.log('✓ Alamat pengirim cleared');
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

            // Initialize Select2 for notify party dropdown
            $('.select2-notify').select2({
                placeholder: '-- Pilih Notify Party --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Notify Party tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Auto-fill alamat notify party when notify party is selected
            $('#notify_party').on('select2:select', function(e) {
                var selectedOption = e.params.data.element;
                var alamat = $(selectedOption).data('alamat');
                
                if (alamat) {
                    $('#alamat_notify_party').val(alamat);
                } else {
                    $('#alamat_notify_party').val('');
                }
            });

            // Clear alamat when notify party is cleared
            $('#notify_party').on('select2:clear', function(e) {
                $('#alamat_notify_party').val('');
            });

            // Initialize Select2 for gudang dropdown
            $('.select2-gudang').each(function() {
                $(this).select2({
                    placeholder: '-- Pilih Gudang --',
                    allowClear: true,
                    width: '100%'
                });
            });

            // Re-usable Select2 initialization for kontainer
            function initKontainerSelect2(element) {
                $(element).select2({
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
            }

            // Initialize existing kontainer selects
            $('.select2-kontainer').each(function() {
                initKontainerSelect2(this);
            });

            // Function to update kontainer rows based on count
            window.updateKontainerRows = function(count) {
                count = parseInt(count) || 1;
                console.log('Updating kontainer rows to:', count);
                
                var $wrapper = $('#kontainer-rows-wrapper');
                var currentCount = $wrapper.find('.kontainer-row').length;
                
                if (count > currentCount) {
                    // Add rows
                    for (var i = currentCount; i < count; i++) {
                        var newRow = `
                            <div class="kontainer-row bg-gray-50 p-4 rounded-lg border border-gray-200 relative pt-6" data-index="${i}">
                                <div class="absolute top-0 left-0 px-3 py-1 bg-blue-600 text-white text-[10px] uppercase font-bold rounded-br-lg rounded-tl-lg shadow-sm">
                                    Container ${i + 1}
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">No. Kontainer</label>
                                        <select name="nomor_kontainer[]" class="w-full px-3 py-2 border border-gray-300 rounded text-sm font-mono select2-kontainer">
                                            <option value="">-- Pilih atau Ketik No. Kontainer --</option>
                                            @foreach($stockKontainers as $stock)
                                                <option value="{{ $stock->nomor_seri_gabungan }}">{{ $stock->nomor_seri_gabungan }} ({{ $stock->ukuran }}ft)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">Size</label>
                                        <select name="size[]" class="w-full px-3 py-2 border border-gray-300 rounded text-sm kontainer-size">
                                            <option value="">Pilih Size</option>
                                            <option value="20" {{ $suratJalan->size == "20" ? "selected" : "" }}>20</option>
                                            <option value="40" {{ $suratJalan->size == "40" ? "selected" : "" }}>40</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">No. Seal</label>
                                        <input type="text" name="no_seal[]" class="w-full px-3 py-2 border border-gray-300 rounded text-sm font-mono" placeholder="Nomor seal">
                                    </div>
                                </div>
                            </div>
                        `;
                        $wrapper.append(newRow);
                        initKontainerSelect2($wrapper.find('.kontainer-row').last().find('.select2-kontainer'));
                    }
                } else if (count < currentCount) {
                    // Remove rows
                    for (var i = currentCount; i > count; i--) {
                        $wrapper.find('.kontainer-row').last().remove();
                    }
                }
            };

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
            $(document).on('select2:select', '.select2-kontainer', function(e) {
                var selectedValue = e.params.data.id;
                var $row = $(this).closest('.kontainer-row');
                var $sizeSelect = $row.find('.kontainer-size');
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
                        $sizeSelect.val(details.size);
                        console.log('✓ Size auto-filled:', details.size);
                    }
                    

                } else {
                    console.log('No details found for kontainer:', containerNumber);
                }
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

    // Track which popup was opened to handle the message response correctly
    let lastPopupOpened = null;

    // Function to open pengirim popup window
    function openPengirimPopup() {
        lastPopupOpened = 'pengirim';
        const width = 600;
        const height = 500;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        
        const popup = window.open(
            '{{ route("order.pengirim.create", ["popup" => "true"], false) }}',
            'TambahPengirim',
            `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );
        
        if (popup) {
            popup.focus();
        } else {
            alert('Pop-up diblokir! Silakan izinkan pop-up untuk situs ini.');
        }
    }

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

    // Listen for message from popup when new penerima or pengirim is added
    window.addEventListener('message', function(event) {
        // Verify origin for security
        if (event.origin !== window.location.origin) return;
        
        console.log('Message received from popup:', event.data);

        // Handle based on event type and tracker
        if (event.data.type === 'penerimaAdded' || lastPopupOpened === 'penerima') {
            const data = event.data.penerima || event.data.data || event.data;
            if (!data || !data.nama) return;

            const newName = data.nama;
            const newAlamat = data.alamat || '';
            
            // Add new option to select
            const select = jQuery('#penerima');
            if (select.length) {
                // Check if option already exists
                if (select.find("option[value='" + newName + "']").length === 0) {
                    const newOption = new Option(newName, newName, true, true);
                    jQuery(newOption).attr('data-alamat', newAlamat);
                    select.append(newOption);
                } else {
                    select.val(newName);
                }
                
                // Trigger select2 change and auto-fill alamat
                select.trigger('change');
                jQuery('#alamat_penerima').val(newAlamat);
                
                console.log('✓ New penerima selected:', newName);
            }
        } else if (lastPopupOpened === 'notify') {
            const data = event.data.penerima || event.data.data || event.data;
            if (!data || !data.nama) return;

            const newName = data.nama;
            const newAlamat = data.alamat || '';
            
            // Add new option to select
            const select = jQuery('#notify_party');
            if (select.length) {
                // Check if option already exists
                if (select.find("option[value='" + newName + "']").length === 0) {
                    const newOption = new Option(newName, newName, true, true);
                    jQuery(newOption).attr('data-alamat', newAlamat);
                    select.append(newOption);
                } else {
                    select.val(newName);
                }
                
                // Trigger select2 change and auto-fill alamat
                select.trigger('change');
                jQuery('#alamat_notify_party').val(newAlamat);
                
                console.log('✓ New notify party selected:', newName);
            }
        } else if (event.data.type === 'pengirim-added' || lastPopupOpened === 'pengirim') {
            const data = event.data.data || event.data.pengirim || event.data;
            if (!data) return;
            
            const newName = data.nama_pengirim || data.nama || '';
            if (!newName) return;
            
            // Add new option to select
            const select = jQuery('#pengirim');
            if (select.length) {
                // Check if option already exists
                if (select.find("option[value='" + newName + "']").length === 0) {
                    const newOption = new Option(newName, newName, true, true);
                    select.append(newOption);
                } else {
                    select.val(newName);
                }
                
                // Trigger select2 change
                select.trigger('change');
                
                console.log('✓ New pengirim selected:', newName);
            }
        }
        
        // Reset tracker
        lastPopupOpened = null;
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
    let processedImages = [];
    let originalImgElement = null;
    let activeImageIndex = null;

    // Current settings for active edit session
    let currentSettings = {
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

    // Preview uploaded images
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

    function formatFileSize(bytes) {
        if (bytes === 0 || !bytes) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function syncFileInput() {
        const fileInput = document.getElementById('gambar_checkpoint');
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
            // Gunakan canvas sementara untuk analisis cepat dengan resolusi rendah
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
            
            // Gambar dengan rotasi aktif
            analysisCtx.translate(analysisCanvas.width / 2, analysisCanvas.height / 2);
            analysisCtx.rotate((rotation * Math.PI) / 180);
            analysisCtx.drawImage(originalImgElement, -tempCanvas.width / 2, -tempCanvas.height / 2, tempCanvas.width, tempCanvas.height);
            
            const width = analysisCanvas.width;
            const height = analysisCanvas.height;
            const imgData = analysisCtx.getImageData(0, 0, width, height);
            const data = imgData.data;
            
            // Konversi ke grayscale dan cari intensitas piksel
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
            
            // Default bounding box (80% di tengah)
            let finalCrop = { x: 10, y: 10, w: 80, h: 80 };
            
            // Jika kontras cukup memadai untuk segmentasi
            if (maxVal - minVal > 40) {
                // Gunakan threshold adaptif sederhana antara min dan max
                const threshold = minVal + (maxVal - minVal) * 0.38;
                
                // Cari bounding box piksel di atas threshold (area kertas putih/terang)
                // Beri margin 3% dari tepi luar agar tidak terganggu bayangan bingkai foto
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
                
                // Pastikan bounding box valid dan tidak terlalu kecil (minimal 20% lebar/tinggi)
                if (maxX > minX && maxY > minY && (maxX - minX) > width * 0.2 && (maxY - minY) > height * 0.2) {
                    // Tambahkan padding 2% untuk memastikan konten tidak terpotong
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
            
            // Update cropBoxPercent
            cropBoxPercent = finalCrop;
            
            // Sinkronkan ke DOM
            const cropBox = document.getElementById('crop-box');
            if (cropBox) {
                cropBox.style.left = cropBoxPercent.x + '%';
                cropBox.style.top = cropBoxPercent.y + '%';
                cropBox.style.width = cropBoxPercent.w + '%';
                cropBox.style.height = cropBoxPercent.h + '%';
            }
            
            // Aktifkan area pangkas (cropper overlay) jika belum aktif
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
                
                // Jalankan autocrop secara otomatis saat gambar pertama kali dimuat
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

    document.addEventListener('DOMContentLoaded', function() {
        initCropperDrag();
        
        const dropZone = document.querySelector('.border-dashed');
        const fileInput = document.getElementById('gambar_checkpoint');
        
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
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
                
                for (let i = 0; i < Math.min(files.length, 5); i++) {
                    const file = files[i];
                    if (!allowedTypes.includes(file.type)) {
                        alert(`File ${file.name} bukan format yang diizinkan. Gunakan JPG, PNG, GIF, WEBP atau PDF.`);
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
                // Optional: Clear the value when hidden? 
                // Better not to clear if the user typed something then accidentally removed "keramik"
            }
            if (gridContainer) {
                gridContainer.classList.remove('md:grid-cols-4');
                gridContainer.classList.add('md:grid-cols-3');
            }
        }
    }
</script>
@endpush
