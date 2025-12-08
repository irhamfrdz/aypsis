@extends('layouts.app')

@section('content')
<style>
    /* Custom styling for select elements */
    .kontainer-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
    
    /* Searchable dropdown styling */
    .dropdown-container-pengirim {
        position: relative;
    }
    
    .dropdown-container-nomor-kontainer {
        position: relative;
    }
</style>

<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Edit Surat Jalan</h1>
                <p class="text-xs text-gray-600 mt-1">Edit surat jalan: {{ $suratJalan->no_surat_jalan }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('surat-jalan.show', $suratJalan->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Lihat
                </a>
                <a href="{{ route('surat-jalan.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('surat-jalan.update', $suratJalan->id) }}" method="POST" enctype="multipart/form-data" class="p-4">
            @csrf
            @method('PUT')

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <div class="font-medium">Terdapat kesalahan pada form:</div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Dasar</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date"
                           name="tanggal_surat_jalan"
                           value="{{ old('tanggal_surat_jalan', $suratJalan->tanggal_surat_jalan?->format('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_surat_jalan') border-red-500 @enderror">
                    @error('tanggal_surat_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="text"
                           name="no_surat_jalan"
                           value="{{ old('no_surat_jalan', $suratJalan->no_surat_jalan) }}"
                           required
                           placeholder="Contoh: SJ/2025/10/0001"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_surat_jalan') border-red-500 @enderror">
                    @error('no_surat_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan <span class="text-red-600">*</span></label>
                    <select name="kegiatan"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('kegiatan') border-red-500 @enderror">
                        <option value="">Pilih Kegiatan</option>
                        @if(isset($kegiatanSuratJalan))
                            @foreach($kegiatanSuratJalan as $kegiatan)
                                <option value="{{ $kegiatan->nama_kegiatan }}" {{ old('kegiatan', $suratJalan->kegiatan) == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                    {{ $kegiatan->nama_kegiatan }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('kegiatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Data kegiatan diambil dari master kegiatan dengan type "kegiatan surat jalan"</p>
                </div>

                <!-- Rest of form fields similar to create.blade.php but with old values from $suratJalan -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="pengirim_id" class="text-sm font-medium text-gray-700">
                            Pengirim
                        </label>
                        <a href="{{ route('pengirim.create') }}" id="add_pengirim_link"
                           class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                           title="Tambah Pengirim Baru">
                            Tambah
                        </a>
                    </div>
                    <div class="relative">
                        <div class="dropdown-container-pengirim">
                            <input type="text" id="search_pengirim" placeholder="Cari pengirim..." autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <select name="pengirim_id" id="pengirim_id" required
                                    class="hidden w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('pengirim_id') border-red-500 @enderror">
                                <option value="">Pilih pengirim</option>
                                @if(isset($pengirims))
                                    @foreach($pengirims as $pengirim)
                                        @php
                                            $isSelected = false;
                                            if(old('pengirim_id')) {
                                                $isSelected = old('pengirim_id') == $pengirim->id;
                                            } elseif($suratJalan->pengirim) {
                                                $isSelected = $suratJalan->pengirim == $pengirim->nama_pengirim;
                                            }
                                        @endphp
                                        <option value="{{ $pengirim->id }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $pengirim->nama_pengirim }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div id="dropdown_options_pengirim" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    @error('pengirim_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Cari dan pilih pengirim dari daftar atau tambah pengirim baru</p>
                </div>

                <!-- Barang Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Barang</h3>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="jenis_barang_id" class="text-sm font-medium text-gray-700">
                            Jenis Barang
                        </label>
                        <button type="button" onclick="openJenisBarangPopup()" 
                                class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                title="Tambah Jenis Barang Baru">
                            Tambah Jenis Barang Baru
                        </button>
                    </div>
                    <div class="relative">
                        <div class="dropdown-container-jenis-barang">
                            <input type="text" id="search_jenis_barang" placeholder="Search jenis barang..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <select name="jenis_barang_id" id="jenis_barang_id"
                                    class="hidden w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jenis_barang_id') border-red-500 @enderror">
                                <option value="">Select an option</option>
                                @foreach($jenisBarangOptions as $jenisBarang)
                                    @php
                                        $isSelected = false;
                                        if (old('jenis_barang_id')) {
                                            $isSelected = old('jenis_barang_id') == $jenisBarang->id;
                                        } else {
                                            // Check if this jenis barang matches the current surat jalan's jenis_barang
                                            $isSelected = $suratJalan->jenis_barang == $jenisBarang->nama_barang;
                                        }
                                    @endphp
                                    <option value="{{ $jenisBarang->id }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $jenisBarang->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="dropdown_options_jenis_barang" class="absolute z-50 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    @error('jenis_barang_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="tujuan_pengambilan_id" class="text-sm font-medium text-gray-700">
                            Tujuan Pengambilan
                        </label>
                        <button type="button" onclick="openTujuanKegiatanUtamaPopup()" 
                                class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                title="Tambah Tujuan Pengambilan Baru">
                            Tambah Tujuan Pengambilan Baru
                        </button>
                    </div>
                    <div class="relative">
                        <div class="dropdown-container-tujuan-pengambilan">
                            <input type="text" id="search_tujuan_pengambilan" placeholder="Search tujuan pengambilan..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <select name="tujuan_pengambilan_id" id="tujuan_pengambilan_id"
                                    class="hidden w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengambilan_id') border-red-500 @enderror">
                                <option value="">Select an option</option>
                                @foreach($tujuanKegiatanUtamas as $tujuanKegiatanUtama)
                                    @php
                                        $isSelected = false;
                                        if (old('tujuan_pengambilan_id')) {
                                            $isSelected = old('tujuan_pengambilan_id') == $tujuanKegiatanUtama->id;
                                        } else {
                                            // Check if this tujuan matches the current surat jalan's tujuan_pengambilan
                                            $isSelected = $suratJalan->tujuan_pengambilan == $tujuanKegiatanUtama->ke;
                                        }
                                    @endphp
                                    <option value="{{ $tujuanKegiatanUtama->id }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $tujuanKegiatanUtama->ke }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="dropdown_options_tujuan_pengambilan" class="absolute z-50 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    @error('tujuan_pengambilan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="tujuan_pengiriman_id" class="text-sm font-medium text-gray-700">
                            Tujuan Pengiriman
                        </label>
                        <a href="{{ route('tujuan-kirim.create') }}" id="add_tujuan_pengiriman_link" target="_blank"
                           class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                           title="Tambah Tujuan Pengiriman">
                            Tambah
                        </a>
                    </div>
                    <div class="relative">
                        <div class="dropdown-container-tujuan-pengiriman">
                            <input type="text" id="search_tujuan_pengiriman" placeholder="Cari tujuan pengiriman..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <select name="tujuan_pengiriman_id" id="tujuan_pengiriman_id"
                                    class="hidden w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengiriman_id') border-red-500 @enderror">
                                <option value="">Pilih Tujuan Pengiriman</option>
                                @foreach($tujuanKirimOptions ?? [] as $tujuanKirim)
                                    @php
                                        $isSelected = false;
                                        if (old('tujuan_pengiriman_id')) {
                                            $isSelected = old('tujuan_pengiriman_id') == $tujuanKirim->id;
                                        } else {
                                            // Check if this tujuan kirim matches the current surat jalan's tujuan_pengiriman
                                            $isSelected = ($suratJalan->tujuan_pengiriman == $tujuanKirim->nama_tujuan);
                                        }
                                    @endphp
                                    <option value="{{ $tujuanKirim->id }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $tujuanKirim->nama_tujuan }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="dropdown_options_tujuan_pengiriman" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    @error('tujuan_pengiriman_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Perubahan tujuan pengiriman akan mengupdate data order terkait</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Retur Barang</label>
                    <input type="text"
                           name="retur_barang"
                           value="{{ old('retur_barang', $suratJalan->retur_barang) }}"
                           placeholder="Retur barang"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('retur_barang') border-red-500 @enderror">
                    @error('retur_barang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Retur</label>
                    <input type="number"
                           name="jumlah_retur"
                           value="{{ old('jumlah_retur', $suratJalan->jumlah_retur) }}"
                           min="0"
                           placeholder="Jumlah retur"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_retur') border-red-500 @enderror">
                    @error('jumlah_retur')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Container Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kontainer</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kontainer</label>
                    <input type="text"
                           name="tipe_kontainer"
                           value="{{ old('tipe_kontainer', $suratJalan->tipe_kontainer) }}"
                           placeholder="Tipe kontainer (FCL/LCL)"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 @error('tipe_kontainer') border-red-500 @enderror">
                    @error('tipe_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                    <select name="size"
                            id="size-select"
                            onchange="updateKontainerRules(); filterNomorKontainerBySize();"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('size') border-red-500 @enderror">
                        <option value="">Pilih Size</option>
                        <option value="20" {{ old('size', $suratJalan->size) == '20' ? 'selected' : '' }}>20 ft</option>
                        <option value="40" {{ old('size', $suratJalan->size) == '40' ? 'selected' : '' }}>40 ft</option>
                        <option value="45" {{ old('size', $suratJalan->size) == '45' ? 'selected' : '' }}>45 ft</option>
                    </select>
                    @error('size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Uang jalan akan diperbarui berdasarkan size kontainer</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontainer</label>
                    <div class="relative">
                        <div class="dropdown-container-nomor-kontainer">
                            <input type="text" id="search_nomor_kontainer" placeholder="Cari nomor kontainer..." autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <select name="nomor_kontainer"
                                    id="nomor-kontainer-select"
                                    onchange="autoFillKontainerDetails(); updateJumlahKontainer();"
                                    class="hidden">
                                <option value="">Pilih Nomor Kontainer</option>
                                @if(isset($stockKontainers) && $stockKontainers->isNotEmpty())
                                    @foreach($stockKontainers as $stock)
                                        @php
                                            // Extract numeric ID from prefixed ID
                                            $numericId = '';
                                            if (isset($stock->kontainer_id)) {
                                                $numericId = $stock->kontainer_id;
                                            } else {
                                                // Extract numeric part from ID (remove stock_ or kontainer_ prefix)
                                                $numericId = preg_replace('/^(stock_|kontainer_)/', '', $stock->id);
                                            }
                                        @endphp
                                        <option value="{{ $stock->nomor_seri_gabungan }}" 
                                                data-kontainer-id="{{ $numericId }}"
                                                data-ukuran="{{ $stock->ukuran }}"
                                                data-tipe="{{ $stock->tipe_kontainer }}"
                                                data-source="{{ $stock->source ?? 'stock_kontainers' }}"
                                                {{ old('nomor_kontainer', $suratJalan->no_kontainer) == $stock->nomor_seri_gabungan ? 'selected' : '' }}>
                                            {{ $stock->nomor_seri_gabungan }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div id="dropdown_options_nomor_kontainer" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    @error('nomor_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div id="stok-info" style="display:none; margin-top:5px; padding:8px; background:#e6f3ff; border-radius:4px;">
                        <small class="text-blue-600">
                            <strong>Informasi Stok:</strong>
                            <br>Size: <span id="stok-size">-</span>
                            <br>Type: <span id="stok-type">-</span>
                            <br>Sumber: <span id="stok-source">-</span>
                        </small>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pilih nomor kontainer dari stock yang tersedia (status: available/tersedia) dan kontainer sewa (status: tersedia). Data berasal dari table stock_kontainers dan kontainers. Filter otomatis berdasarkan size kontainer.</p>
                    <!-- Hidden field untuk kontainer_id -->
                    <input type="hidden" name="kontainer_id" id="kontainer-id" value="{{ old('kontainer_id', $suratJalan->kontainer_id) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kontainer</label>
                    <input type="number"
                           name="jumlah_kontainer"
                           value="{{ old('jumlah_kontainer', $suratJalan->jumlah_kontainer ?? 1) }}"
                           min="1"
                           placeholder="Jumlah kontainer"
                           onchange="updateKontainerNote()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_kontainer') border-red-500 @enderror">
                    @error('jumlah_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Seal</label>
                    <input type="text"
                           name="no_seal"
                           value="{{ old('no_seal', $suratJalan->no_seal) }}"
                           placeholder="Nomor seal"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_seal') border-red-500 @enderror">
                    @error('no_seal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transportasi Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Transportasi</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                    <select name="supir"
                            id="supir-select"
                            onchange="updateNoPlat()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">
                        <option value="">Pilih Supir</option>
                        @if(isset($supirs))
                            @foreach($supirs as $supir)
                                <option value="{{ $supir->nama_panggilan ?? $supir->nama_lengkap }}"
                                        data-plat="{{ $supir->plat }}"
                                        {{ old('supir', $suratJalan->supir) == ($supir->nama_panggilan ?? $supir->nama_lengkap) ? 'selected' : '' }}>
                                    {{ $supir->nama_panggilan ?? $supir->nama_lengkap }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('supir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir 2</label>
                    <input type="text"
                           name="supir2"
                           value="{{ old('supir2', $suratJalan->supir2) }}"
                           placeholder="Nama supir kedua (opsional)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('supir2') border-red-500 @enderror">
                    @error('supir2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <input type="text"
                           name="kenek"
                           value="{{ old('kenek', $suratJalan->kenek) }}"
                           placeholder="Nama kenek"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('kenek') border-red-500 @enderror">
                    @error('kenek')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat</label>
                    <input type="text"
                           name="no_plat"
                           id="no-plat-input"
                           value="{{ old('no_plat', $suratJalan->no_plat) }}"
                           placeholder="Nomor plat kendaraan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror">
                    @error('no_plat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">No. plat akan otomatis terisi berdasarkan supir yang dipilih</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <div class="flex">
                        <input type="number"
                               name="uang_jalan"
                               id="uang-jalan"
                               value="{{ old('uang_jalan', $suratJalan->uang_jalan) }}"
                               placeholder="0"
                               min="0"
                               step="1"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('uang_jalan') border-red-500 @enderror">
                        <button type="button"
                                onclick="updateUangJalan()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-r-lg text-sm">
                            Auto
                        </button>
                    </div>
                    @error('uang_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Bisa diisi manual atau klik "Auto" untuk mengisi otomatis berdasarkan tujuan pengambilan dan size kontainer.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select name="status"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status', $suratJalan->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $suratJalan->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="belum masuk checkpoint" {{ old('status', $suratJalan->status) == 'belum masuk checkpoint' ? 'selected' : '' }}>Belum Masuk Checkpoint</option>
                        <option value="sudah_checkpoint" {{ old('status', $suratJalan->status) == 'sudah_checkpoint' ? 'selected' : '' }}>Sudah Checkpoint</option>
                        <option value="approved" {{ old('status', $suratJalan->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="fully_approved" {{ old('status', $suratJalan->status) == 'fully_approved' ? 'selected' : '' }}>Fully Approved</option>
                        <option value="rejected" {{ old('status', $suratJalan->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ old('status', $suratJalan->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $suratJalan->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aktifitas</label>
                    <textarea name="aktifitas"
                              rows="3"
                              placeholder="Deskripsi aktifitas atau catatan tambahan"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('aktifitas') border-red-500 @enderror">{{ old('aktifitas', $suratJalan->aktifitas) }}</textarea>
                    @error('aktifitas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar/Dokumen</label>
                    @if($suratJalan->gambar)
                        <div class="mb-2 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                            <img src="{{ asset('storage/' . $suratJalan->gambar) }}"
                                 alt="Current Image"
                                 class="max-w-xs h-20 object-cover rounded border">
                        </div>
                    @endif
                    <input type="file"
                           name="gambar"
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('gambar') border-red-500 @enderror">
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Max: 2MB. Kosongkan jika tidak ingin mengubah.</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('surat-jalan.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-150">
                    Batal
                </a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors duration-150">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Initialize kontainer filtering on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeKontainerFiltering();
    
    // Auto-fill kontainer details if edit mode has existing data
    const nomorKontainerSelect = document.getElementById('nomor-kontainer-select');
    if (nomorKontainerSelect && nomorKontainerSelect.value) {
        autoFillKontainerDetails();
    }
});

function initializeKontainerFiltering() {
    // Store original options for filtering
    window.allKontainerOptions = [];
    const kontainerSelect = document.getElementById('nomor-kontainer-select');
    
    if (kontainerSelect) {
        const options = kontainerSelect.querySelectorAll('option');
        options.forEach(function(option) {
            if (option.value !== '') {
                window.allKontainerOptions.push({
                    value: option.value,
                    text: option.value, // Hanya nomor kontainer
                    ukuran: option.getAttribute('data-ukuran'),
                    tipe: option.getAttribute('data-tipe'),
                    source: option.getAttribute('data-source') || 'stock_kontainers',
                    kontainerId: option.getAttribute('data-kontainer-id')
                });
            }
        });
        
        console.log('Kontainer filtering initialized');
        console.log('Total kontainer options stored:', window.allKontainerOptions.length);
        
        // Filter kontainer on page load if size is already selected
        const sizeSelect = document.getElementById('size-select');
        if (sizeSelect && sizeSelect.value) {
            filterNomorKontainerBySize();
        }
    }
}

function autoFillKontainerDetails() {
    const nomorKontainerSelect = document.getElementById('nomor-kontainer-select');
    const kontainerIdInput = document.getElementById('kontainer-id');
    const stokInfo = document.getElementById('stok-info');
    const searchNomorKontainerInput = document.getElementById('search_nomor_kontainer');
    
    if (!nomorKontainerSelect || !stokInfo) return;
    
    const selectedOption = nomorKontainerSelect.options[nomorKontainerSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const kontainerId = selectedOption.getAttribute('data-kontainer-id');
        const size = selectedOption.getAttribute('data-ukuran');
        const type = selectedOption.getAttribute('data-tipe');
        const source = selectedOption.getAttribute('data-source');
        
        // Update search input to show selected value
        if (searchNomorKontainerInput) {
            searchNomorKontainerInput.value = selectedOption.textContent;
        }
        
        // Set kontainer_id
        if (kontainerIdInput) {
            let kontainerIdValue = '';
            if (kontainerId && kontainerId !== '' && !isNaN(kontainerId)) {
                kontainerIdValue = parseInt(kontainerId);
            }
            kontainerIdInput.value = kontainerIdValue;
        }
        
        // Show stock info
        document.getElementById('stok-size').textContent = size || '-';
        document.getElementById('stok-type').textContent = type || '-';
        document.getElementById('stok-source').textContent = source === 'stock_kontainers' ? 'Stock Kontainers' : 'Kontainers';
        stokInfo.style.display = 'block';
        
        // Auto-fill size if empty
        const sizeSelect = document.getElementById('size-select');
        if (sizeSelect && !sizeSelect.value && size) {
            sizeSelect.value = size;
            updateKontainerRules();
        }
    } else {
        // Clear kontainer_id and search input
        if (kontainerIdInput) {
            kontainerIdInput.value = '';
        }
        if (searchNomorKontainerInput) {
            searchNomorKontainerInput.value = '';
            searchNomorKontainerInput.placeholder = 'Cari nomor kontainer...';
        }
        
        // Hide stock info
        stokInfo.style.display = 'none';
    }
}

function updateJumlahKontainer() {
    const jumlahInput = document.getElementById('jumlah_kontainer_input');
    const nomorKontainerSelect = document.getElementById('nomor-kontainer-select');
    
    if (jumlahInput && nomorKontainerSelect && nomorKontainerSelect.value) {
        if (!jumlahInput.value) {
            jumlahInput.value = '1';
        }
    }
}

function updateUangJalan() {
    const tujuanPengambilan = document.querySelector('input[name="tujuan_pengambilan"]')?.value || "{{ $suratJalan->tujuan_pengambilan }}";
    const sizeSelect = document.querySelector('select[name="size"]');
    const uangJalanInput = document.getElementById('uang-jalan');
    const jumlahKontainer = parseInt(document.querySelector('input[name="jumlah_kontainer"]')?.value) || 1;

    if (tujuanPengambilan && sizeSelect && uangJalanInput) {
        let size = sizeSelect.value;

        // Untuk size 20ft dengan 2 kontainer, gunakan tarif 40ft
        let calculationSize = size;
        if (jumlahKontainer === 2 && size === '20') {
            calculationSize = '40';
            console.log('Menggunakan pricelist 40ft untuk 2 kontainer 20ft');
        } else {
            console.log(`Menggunakan pricelist ${size}ft untuk ${jumlahKontainer} kontainer ${size}ft`);
        }

        // Fetch uang jalan based on tujuan pengambilan and container size
        fetch('/api/get-uang-jalan-by-tujuan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                tujuan: tujuanPengambilan,
                size: calculationSize
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove formatting and store as raw number for database
                const rawValue = data.uang_jalan.replace(/[^\d]/g, ''); // Remove non-digits
                uangJalanInput.value = rawValue;

                console.log(`Uang Jalan Updated - Original Size: ${size}, Jumlah: ${jumlahKontainer}, Calculation Size: ${calculationSize}, Raw: ${rawValue}, Formatted: ${data.uang_jalan}`);
            } else {
                // Set default uang jalan based on size if API fails
                if (!uangJalanInput.value) {
                    if (size === '20') {
                        uangJalanInput.value = '500000';
                    } else if (size === '40') {
                        uangJalanInput.value = '750000';
                    } else if (size === '45') {
                        uangJalanInput.value = '850000';
                    }
                }
                console.log('Uang jalan tidak ditemukan untuk tujuan:', tujuanPengambilan, '- menggunakan default berdasarkan size');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Set default uang jalan based on size if error occurs
            if (!uangJalanInput.value) {
                if (size === '20') {
                    uangJalanInput.value = '500000';
                } else if (size === '40') {
                    uangJalanInput.value = '750000';
                } else if (size === '45') {
                    uangJalanInput.value = '850000';
                }
            }
        });
    } else {
        // Fallback to simple size-based calculation if no tujuan pengambilan
        if (sizeSelect && uangJalanInput && !uangJalanInput.value) {
            const selectedSize = sizeSelect.value;
            
            if (selectedSize === '20') {
                uangJalanInput.value = '500000';
            } else if (selectedSize === '40') {
                uangJalanInput.value = '750000';
            } else if (selectedSize === '45') {
                uangJalanInput.value = '850000';
            }
        }
    }
}

function filterNomorKontainerBySize() {
    const sizeSelect = document.getElementById('size-select');
    const kontainerSelect = document.getElementById('nomor-kontainer-select');
    const searchNomorKontainerInput = document.getElementById('search_nomor_kontainer');
    const selectedSize = sizeSelect ? sizeSelect.value : '';
    
    if (!kontainerSelect) return;
    
    // Clear current selection
    kontainerSelect.selectedIndex = 0;
    if (searchNomorKontainerInput) {
        searchNomorKontainerInput.value = '';
        searchNomorKontainerInput.placeholder = 'Cari nomor kontainer...';
    }
    
    // Remove all options except placeholder
    const options = kontainerSelect.querySelectorAll('option');
    options.forEach(function(option, index) {
        if (index > 0) { // Keep first option (placeholder)
            option.remove();
        }
    });
    
    if (!selectedSize) {
        // Show all options if no size selected
        window.allKontainerOptions.forEach(function(optionData) {
            const newOption = document.createElement('option');
            newOption.value = optionData.value;
            newOption.textContent = optionData.value; // Hanya nomor kontainer
            newOption.setAttribute('data-ukuran', optionData.ukuran);
            newOption.setAttribute('data-tipe', optionData.tipe);
            newOption.setAttribute('data-source', optionData.source);
            // Pastikan kontainer ID adalah numeric atau kosong
            const numericId = optionData.kontainerId && !isNaN(optionData.kontainerId) ? optionData.kontainerId : '';
            newOption.setAttribute('data-kontainer-id', numericId);
            kontainerSelect.appendChild(newOption);
        });
        console.log('No size selected - showing all kontainers:', window.allKontainerOptions.length);
    } else {
        // Filter options based on size
        let filteredCount = 0;
        window.allKontainerOptions.forEach(function(optionData) {
            const optionUkuran = optionData.ukuran;
            
            // Normalize both values for comparison
            const normalizedUkuran = optionUkuran ? String(optionUkuran).toLowerCase().replace(/\s+/g, '') : '';
            const normalizedSize = selectedSize.toLowerCase().replace(/\s+/g, '');
            
            // Check multiple possible formats
            const sizeMatches = 
                normalizedUkuran === normalizedSize + 'ft' || 
                normalizedUkuran === normalizedSize ||
                normalizedUkuran.startsWith(normalizedSize);
            
            if (sizeMatches) {
                const newOption = document.createElement('option');
                newOption.value = optionData.value;
                newOption.textContent = optionData.value; // Hanya nomor kontainer
                newOption.setAttribute('data-ukuran', optionData.ukuran);
                newOption.setAttribute('data-tipe', optionData.tipe);
                newOption.setAttribute('data-source', optionData.source);
                // Pastikan kontainer ID adalah numeric atau kosong
                const numericId = optionData.kontainerId && !isNaN(optionData.kontainerId) ? optionData.kontainerId : '';
                newOption.setAttribute('data-kontainer-id', numericId);
                kontainerSelect.appendChild(newOption);
                filteredCount++;
            }
        });
        
        console.log(`Filtered kontainers for size ${selectedSize}ft: ${filteredCount} items found`);
    }
    
    // Refresh the searchable dropdown options after filtering
    if (window.refreshNomorKontainerOptions) {
        window.refreshNomorKontainerOptions();
    }
}

function updateNoPlat() {
    const supirSelect = document.getElementById('supir-select');
    const noPlatInput = document.getElementById('no-plat-input');

    if (supirSelect.selectedIndex > 0) {
        const selectedOption = supirSelect.options[supirSelect.selectedIndex];
        const platNumber = selectedOption.getAttribute('data-plat');

        if (platNumber) {
            noPlatInput.value = platNumber;
        } else {
            noPlatInput.value = '';
        }
    } else {
        noPlatInput.value = '';
    }
}

function updateKontainerNote() {
    const jumlahKontainer = parseInt(document.querySelector('input[name="jumlah_kontainer"]').value) || 1;
    const jumlahKontainerInput = document.querySelector('input[name="jumlah_kontainer"]');

    // Hapus note yang ada
    const existingNote = document.getElementById('kontainer-rule-note-edit');
    if (existingNote) {
        existingNote.remove();
    }

    if (jumlahKontainer === 2) {
        // Tambahkan keterangan
        const note = document.createElement('p');
        note.id = 'kontainer-rule-note-edit';
        note.className = 'text-xs text-blue-600 mt-1';
        note.innerHTML = '<strong>Catatan:</strong> Untuk 2 kontainer, akan menggunakan tarif 40ft meskipun size 20ft';
        jumlahKontainerInput.parentNode.appendChild(note);
    }
}

function updateKontainerRules() {
    const sizeSelect = document.querySelector('select[name="size"]');
    const jumlahKontainerInput = document.getElementById('jumlah_kontainer_input');
    const jumlahKontainerNote = document.getElementById('jumlah_kontainer_note');
    const pricelistInfo = document.getElementById('pricelist-info');
    const pricelistInfoText = document.getElementById('pricelist-info-text');
    
    if (sizeSelect && jumlahKontainerInput) {
        const selectedSize = sizeSelect.value;
        const jumlahKontainer = parseInt(jumlahKontainerInput.value) || 1;
        
        // Hide pricelist info by default
        if (pricelistInfo) {
            pricelistInfo.classList.add('hidden');
        }
        
        if (selectedSize === '40' || selectedSize === '45') {
            // Untuk size 40ft dan 45ft, hanya bisa 1 kontainer
            jumlahKontainerInput.value = '1';
            jumlahKontainerInput.max = '1';
            jumlahKontainerInput.disabled = true;
            jumlahKontainerInput.style.backgroundColor = '#F3F4F6';
            jumlahKontainerInput.style.color = '#6B7280';
            
            if (jumlahKontainerNote) {
                jumlahKontainerNote.textContent = `Untuk size ${selectedSize}ft, hanya bisa 1 kontainer per surat jalan`;
                jumlahKontainerNote.className = 'text-xs text-orange-600 mt-1 font-medium';
            }
        } else if (selectedSize === '20') {
            // Untuk size 20ft, bisa lebih dari 1 kontainer
            jumlahKontainerInput.disabled = false;
            jumlahKontainerInput.removeAttribute('max');
            jumlahKontainerInput.style.backgroundColor = '';
            jumlahKontainerInput.style.color = '';
            
            if (jumlahKontainerNote) {
                jumlahKontainerNote.textContent = 'Untuk size 20ft, bisa menggunakan multiple kontainer per surat jalan';
                jumlahKontainerNote.className = 'text-xs text-green-600 mt-1';
            }
            
            // Show pricelist info for 2 kontainer 20ft
            if (jumlahKontainer === 2) {
                if (pricelistInfo && pricelistInfoText) {
                    pricelistInfo.classList.remove('hidden');
                    pricelistInfoText.textContent = 'Menggunakan pricelist 40ft untuk 2 kontainer 20ft';
                }
            }
        } else {
            // Jika belum pilih size, reset
            jumlahKontainerInput.disabled = false;
            jumlahKontainerInput.removeAttribute('max');
            jumlahKontainerInput.style.backgroundColor = '';
            jumlahKontainerInput.style.color = '';
            
            if (jumlahKontainerNote) {
                jumlahKontainerNote.textContent = 'Pilih size kontainer terlebih dahulu';
                jumlahKontainerNote.className = 'text-xs text-gray-500 mt-1';
            }
        }
    }
    
    // Update uang jalan berdasarkan size
    updateUangJalan();
}

// Function to create searchable dropdown
function createSearchableDropdown(config) {
    const selectElement = document.getElementById(config.selectId);
    const searchInput = document.getElementById(config.searchId);
    const dropdownOptions = document.getElementById(config.dropdownId);
    let originalOptions = Array.from(selectElement.options);

    // Function to refresh original options (when new items are added)
    function refreshOriginalOptions() {
        originalOptions = Array.from(selectElement.options);
    }

    // Make refreshOriginalOptions available globally for this dropdown
    if (config.selectId === 'pengirim_id') {
        window.refreshPengirimOptions = refreshOriginalOptions;
    }
    if (config.selectId === 'nomor-kontainer-select') {
        window.refreshNomorKontainerOptions = refreshOriginalOptions;
    }

    // Initially populate dropdown options
    populateDropdown(originalOptions);

    // Show dropdown when search input is focused or clicked
    searchInput.addEventListener('focus', function() {
        dropdownOptions.classList.remove('hidden');
    });

    searchInput.addEventListener('click', function() {
        dropdownOptions.classList.remove('hidden');
    });

    // Filter options based on search
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredOptions = originalOptions.filter(option => {
            if (option.value === '') return true;
            return option.text.toLowerCase().includes(searchTerm);
        });
        populateDropdown(filteredOptions);
        dropdownOptions.classList.remove('hidden');
    });

    // Populate dropdown with options
    function populateDropdown(options) {
        dropdownOptions.innerHTML = '';
        options.forEach(option => {
            const div = document.createElement('div');
            div.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
            div.textContent = option.text;
            div.setAttribute('data-value', option.value);

            div.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.textContent;

                // Set the select value
                selectElement.value = value;

                // Update search input
                if (value === '') {
                    searchInput.value = '';
                    if (config.selectId === 'pengirim_id') {
                        searchInput.placeholder = 'Cari pengirim...';
                    }
                } else {
                    searchInput.value = text;
                    searchInput.placeholder = '';
                }

                // Hide dropdown
                dropdownOptions.classList.add('hidden');

                // Trigger change event
                selectElement.dispatchEvent(new Event('change'));
            });

            dropdownOptions.appendChild(div);
        });
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.' + config.containerClass)) {
            dropdownOptions.classList.add('hidden');
        }
    });

    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdownOptions.classList.add('hidden');
        }
    });
}

// Check kontainer rules on page load
document.addEventListener('DOMContentLoaded', function() {
    updateKontainerNote();
    updateKontainerRules();
    
    // Initialize Pengirim dropdown
    createSearchableDropdown({
        selectId: 'pengirim_id',
        searchId: 'search_pengirim',
        dropdownId: 'dropdown_options_pengirim',
        containerClass: 'dropdown-container-pengirim'
    });

    // Initialize Jenis Barang dropdown
    createSearchableDropdown({
        selectId: 'jenis_barang_id',
        searchId: 'search_jenis_barang',
        dropdownId: 'dropdown_options_jenis_barang',
        containerClass: 'dropdown-container-jenis-barang'
    });

    // Initialize Tujuan Pengambilan dropdown
    createSearchableDropdown({
        selectId: 'tujuan_pengambilan_id',
        searchId: 'search_tujuan_pengambilan',
        dropdownId: 'dropdown_options_tujuan_pengambilan',
        containerClass: 'dropdown-container-tujuan-pengambilan'
    });

    // Initialize Nomor Kontainer dropdown
    createSearchableDropdown({
        selectId: 'nomor-kontainer-select',
        searchId: 'search_nomor_kontainer',
        dropdownId: 'dropdown_options_nomor_kontainer',
        containerClass: 'dropdown-container-nomor-kontainer'
    });

    // Initialize Tujuan Pengiriman dropdown
    createSearchableDropdown({
        selectId: 'tujuan_pengiriman_id',
        searchId: 'search_tujuan_pengiriman',
        dropdownId: 'dropdown_options_tujuan_pengiriman',
        containerClass: 'dropdown-container-tujuan-pengiriman'
    });
    
    // Set initial value for pengirim search input if edit mode has existing data
    const pengirimSelect = document.getElementById('pengirim_id');
    const searchPengirimInput = document.getElementById('search_pengirim');
    if (pengirimSelect && pengirimSelect.value && searchPengirimInput) {
        const selectedOption = pengirimSelect.options[pengirimSelect.selectedIndex];
        if (selectedOption && selectedOption.text) {
            searchPengirimInput.value = selectedOption.text;
        }
    }

    // Set initial value for jenis barang search input if edit mode has existing data
    const jenisBarangSelect = document.getElementById('jenis_barang_id');
    const searchJenisBarangInput = document.getElementById('search_jenis_barang');
    
    console.log('Debug Jenis Barang Init:');
    console.log('Select element exists:', !!jenisBarangSelect);
    console.log('Search input exists:', !!searchJenisBarangInput);
    console.log('Select value:', jenisBarangSelect ? jenisBarangSelect.value : 'N/A');
    console.log('Selected index:', jenisBarangSelect ? jenisBarangSelect.selectedIndex : 'N/A');
    
    if (jenisBarangSelect && searchJenisBarangInput) {
        // Try to find selected option or match with stored value
        const storedValue = "{{ $suratJalan->jenis_barang }}";
        console.log('Stored value from database:', storedValue);
        
        if (jenisBarangSelect.value && jenisBarangSelect.selectedIndex > 0) {
            // Option already selected
            const selectedOption = jenisBarangSelect.options[jenisBarangSelect.selectedIndex];
            if (selectedOption && selectedOption.text) {
                searchJenisBarangInput.value = selectedOption.text;
                console.log('Set search input from selected option:', selectedOption.text);
            }
        } else if (storedValue && storedValue !== '') {
            // No option selected but we have stored value, try to match and select
            console.log('No option selected, searching for match with:', storedValue);
            
            for (let i = 0; i < jenisBarangSelect.options.length; i++) {
                const option = jenisBarangSelect.options[i];
                console.log('Checking option:', option.text, 'against stored:', storedValue);
                
                if (option.text.trim() === storedValue.trim()) {
                    console.log('Found exact match! Selecting option:', option.text);
                    jenisBarangSelect.selectedIndex = i;
                    jenisBarangSelect.value = option.value;
                    searchJenisBarangInput.value = option.text;
                    break;
                }
            }
            
            // If still no match, just put the stored value in search input
            if (!jenisBarangSelect.value && searchJenisBarangInput.value === '') {
                console.log('No match found, setting search input to stored value');
                searchJenisBarangInput.value = storedValue;
            }
        }
    }

    // Set initial value for tujuan pengambilan search input if edit mode has existing data
    const tujuanPengambilanSelect = document.getElementById('tujuan_pengambilan_id');
    const searchTujuanPengambilanInput = document.getElementById('search_tujuan_pengambilan');
    
    console.log('Debug Tujuan Pengambilan Init:');
    console.log('Select element exists:', !!tujuanPengambilanSelect);
    console.log('Search input exists:', !!searchTujuanPengambilanInput);
    console.log('Select value:', tujuanPengambilanSelect ? tujuanPengambilanSelect.value : 'N/A');
    console.log('Selected index:', tujuanPengambilanSelect ? tujuanPengambilanSelect.selectedIndex : 'N/A');
    
    if (tujuanPengambilanSelect && searchTujuanPengambilanInput) {
        // Try to find selected option or match with stored value
        const storedValue = "{{ $suratJalan->tujuan_pengambilan }}";
        console.log('Stored value from database:', storedValue);
        
        if (tujuanPengambilanSelect.value && tujuanPengambilanSelect.selectedIndex > 0) {
            // Option already selected
            const selectedOption = tujuanPengambilanSelect.options[tujuanPengambilanSelect.selectedIndex];
            if (selectedOption && selectedOption.text) {
                searchTujuanPengambilanInput.value = selectedOption.text;
                console.log('Set search input from selected option:', selectedOption.text);
            }
        } else if (storedValue && storedValue !== '') {
            // No option selected but we have stored value, try to match and select
            console.log('No option selected, searching for match with:', storedValue);
            
            for (let i = 0; i < tujuanPengambilanSelect.options.length; i++) {
                const option = tujuanPengambilanSelect.options[i];
                console.log('Checking option:', option.text, 'against stored:', storedValue);
                
                if (option.text.trim() === storedValue.trim()) {
                    console.log('Found exact match! Selecting option:', option.text);
                    tujuanPengambilanSelect.selectedIndex = i;
                    tujuanPengambilanSelect.value = option.value;
                    searchTujuanPengambilanInput.value = option.text;
                    break;
                }
            }
            
            // If still no match, just put the stored value in search input
            if (!tujuanPengambilanSelect.value && searchTujuanPengambilanInput.value === '') {
                console.log('No match found, setting search input to stored value');
                searchTujuanPengambilanInput.value = storedValue;
            }
        }
    }

    // Set initial value for tujuan pengiriman search input if edit mode has existing data
    const tujuanPengirimanSelect = document.getElementById('tujuan_pengiriman_id');
    const searchTujuanPengirimanInput = document.getElementById('search_tujuan_pengiriman');
    
    console.log('Debug Tujuan Pengiriman Init:');
    console.log('Select element exists:', !!tujuanPengirimanSelect);
    console.log('Search input exists:', !!searchTujuanPengirimanInput);
    console.log('Select value:', tujuanPengirimanSelect ? tujuanPengirimanSelect.value : 'N/A');
    console.log('Selected index:', tujuanPengirimanSelect ? tujuanPengirimanSelect.selectedIndex : 'N/A');
    
    if (tujuanPengirimanSelect && searchTujuanPengirimanInput) {
        // Try to find selected option or match with stored value
        const storedValue = "{{ $suratJalan->tujuan_pengiriman }}";
        console.log('Stored value from database:', storedValue);
        
        if (tujuanPengirimanSelect.value && tujuanPengirimanSelect.selectedIndex > 0) {
            // Option already selected
            const selectedOption = tujuanPengirimanSelect.options[tujuanPengirimanSelect.selectedIndex];
            if (selectedOption && selectedOption.text) {
                searchTujuanPengirimanInput.value = selectedOption.text;
                console.log('Set search input from selected option:', selectedOption.text);
            }
        } else if (storedValue && storedValue !== '') {
            // No option selected but we have stored value, try to match and select
            console.log('No option selected, searching for match with:', storedValue);
            
            for (let i = 0; i < tujuanPengirimanSelect.options.length; i++) {
                const option = tujuanPengirimanSelect.options[i];
                console.log('Checking option:', option.text, 'against stored:', storedValue);
                
                if (option.text.trim() === storedValue.trim()) {
                    console.log('Found exact match! Selecting option:', option.text);
                    tujuanPengirimanSelect.selectedIndex = i;
                    tujuanPengirimanSelect.value = option.value;
                    searchTujuanPengirimanInput.value = option.text;
                    break;
                }
            }
            
            // If still no match, just put the stored value in search input
            if (!tujuanPengirimanSelect.value && searchTujuanPengirimanInput.value === '') {
                console.log('No match found, setting search input to stored value');
                searchTujuanPengirimanInput.value = storedValue;
            }
        }
    }
    
    // Set initial value for nomor kontainer search input if edit mode has existing data
    const nomorKontainerSelect = document.getElementById('nomor-kontainer-select');
    const searchNomorKontainerInput = document.getElementById('search_nomor_kontainer');
    
    console.log('Debug Nomor Kontainer Init:');
    console.log('Select element exists:', !!nomorKontainerSelect);
    console.log('Search input exists:', !!searchNomorKontainerInput);
    console.log('Select value:', nomorKontainerSelect ? nomorKontainerSelect.value : 'N/A');
    console.log('Selected index:', nomorKontainerSelect ? nomorKontainerSelect.selectedIndex : 'N/A');
    
    if (nomorKontainerSelect && searchNomorKontainerInput) {
        // Try to find selected option or match with stored value
        const storedValue = "{{ $suratJalan->no_kontainer }}";
        console.log('Stored value from database:', storedValue);
        
        if (nomorKontainerSelect.value && nomorKontainerSelect.selectedIndex > 0) {
            // Option already selected
            const selectedOption = nomorKontainerSelect.options[nomorKontainerSelect.selectedIndex];
            if (selectedOption && selectedOption.text) {
                searchNomorKontainerInput.value = selectedOption.text;
                console.log('Set search input from selected option:', selectedOption.text);
            }
        } else if (storedValue && storedValue !== '') {
            // No option selected but we have stored value, try to match and select
            console.log('No option selected, searching for match with:', storedValue);
            
            for (let i = 0; i < nomorKontainerSelect.options.length; i++) {
                const option = nomorKontainerSelect.options[i];
                console.log('Checking option:', option.text, 'against stored:', storedValue);
                
                if (option.text.trim() === storedValue.trim() || option.value.trim() === storedValue.trim()) {
                    console.log('Found exact match! Selecting option:', option.text);
                    nomorKontainerSelect.selectedIndex = i;
                    nomorKontainerSelect.value = option.value;
                    searchNomorKontainerInput.value = option.text;
                    // Trigger the autofill function
                    autoFillKontainerDetails();
                    break;
                }
            }
            
            // If still no match, just put the stored value in search input
            if (!nomorKontainerSelect.value && searchNomorKontainerInput.value === '') {
                console.log('No match found, setting search input to stored value');
                searchNomorKontainerInput.value = storedValue;
            }
        }
    }
    
    // Handle Pengirim "Tambah" link
    const addPengirimLink = document.getElementById('add_pengirim_link');
    if (addPengirimLink && searchPengirimInput) {
        addPengirimLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchPengirimInput.value.trim();
            let url = "{{ route('pengirim.create') }}";

            // Add popup parameter and nama_pengirim if available
            const params = new URLSearchParams();
            params.append('popup', '1');

            if (searchValue) {
                params.append('search', searchValue);
            }

            url += '?' + params.toString();

            // Open as popup window with specific dimensions
            const popup = window.open(
                url,
                'addPengirim',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }
    
    // Handle Tujuan Pengiriman "Tambah" link
    const addTujuanPengirimanLink = document.getElementById('add_tujuan_pengiriman_link');
    if (addTujuanPengirimanLink) {
        addTujuanPengirimanLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = document.getElementById('search_tujuan_pengiriman')?.value.trim() || '';
            let url = "{{ route('tujuan-kirim.create') }}";

            // Add popup parameter and nama_tujuan if available
            const params = new URLSearchParams();
            params.append('popup', '1');

            if (searchValue) {
                params.append('search', searchValue);
            }

            url += '?' + params.toString();

            // Open as popup window with specific dimensions
            const popup = window.open(
                url,
                'addTujuanPengiriman',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }
    
    // Handle message from popup for new records added
    window.addEventListener('message', function(event) {
        if (event.data.type === 'pengirim-added') {
            const pengirimSelect = document.getElementById('pengirim_id');
            const searchPengirimInput = document.getElementById('search_pengirim');
            const dropdownOptionsPengirim = document.getElementById('dropdown_options_pengirim');

            if (pengirimSelect && event.data.data) {
                // Add new option to select
                const newOption = document.createElement('option');
                newOption.value = event.data.data.id;
                newOption.textContent = event.data.data.nama_pengirim;
                pengirimSelect.appendChild(newOption);

                // Select the new option
                pengirimSelect.value = event.data.data.id;

                // Update search input to show selected value
                if (searchPengirimInput) {
                    searchPengirimInput.value = event.data.data.nama_pengirim;
                }

                // Update the dropdown options in the searchable dropdown
                if (dropdownOptionsPengirim) {
                    const newOptionDiv = document.createElement('div');
                    newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                    newOptionDiv.textContent = event.data.data.nama_pengirim;
                    newOptionDiv.setAttribute('data-value', event.data.data.id);

                    newOptionDiv.addEventListener('click', function() {
                        pengirimSelect.value = event.data.data.id;
                        searchPengirimInput.value = event.data.data.nama_pengirim;
                        dropdownOptionsPengirim.classList.add('hidden');
                        pengirimSelect.dispatchEvent(new Event('change'));
                    });

                    if (dropdownOptionsPengirim.children.length > 1) {
                        dropdownOptionsPengirim.insertBefore(newOptionDiv, dropdownOptionsPengirim.children[1]);
                    } else {
                        dropdownOptionsPengirim.appendChild(newOptionDiv);
                    }
                }

                // Hide dropdown
                if (dropdownOptionsPengirim) {
                    dropdownOptionsPengirim.classList.add('hidden');
                }

                // Trigger change event
                pengirimSelect.dispatchEvent(new Event('change'));

                // Show success notification
                showNotification('Pengirim "' + event.data.data.nama_pengirim + '" berhasil ditambahkan dan dipilih!', 'success');
            }
        }

        // Handle jenis barang added
        if (event.data.type === 'jenis-barang-added') {
            const jenisBarangSelect = document.getElementById('jenis_barang_id');
            const searchJenisBarangInput = document.getElementById('search_jenis_barang');
            const dropdownOptionsJenisBarang = document.getElementById('dropdown_options_jenis_barang');

            if (jenisBarangSelect && event.data.data) {
                // Add new option to select
                const newOption = document.createElement('option');
                newOption.value = event.data.data.id;
                newOption.textContent = event.data.data.nama_barang;
                jenisBarangSelect.appendChild(newOption);

                // Select the new option
                jenisBarangSelect.value = event.data.data.id;

                // Update search input to show selected value
                if (searchJenisBarangInput) {
                    searchJenisBarangInput.value = event.data.data.nama_barang;
                }

                // Update the dropdown options in the searchable dropdown
                if (dropdownOptionsJenisBarang) {
                    const newOptionDiv = document.createElement('div');
                    newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                    newOptionDiv.textContent = event.data.data.nama_barang;
                    newOptionDiv.setAttribute('data-value', event.data.data.id);

                    newOptionDiv.addEventListener('click', function() {
                        jenisBarangSelect.value = event.data.data.id;
                        searchJenisBarangInput.value = event.data.data.nama_barang;
                        dropdownOptionsJenisBarang.classList.add('hidden');
                        jenisBarangSelect.dispatchEvent(new Event('change'));
                    });

                    if (dropdownOptionsJenisBarang.children.length > 1) {
                        dropdownOptionsJenisBarang.insertBefore(newOptionDiv, dropdownOptionsJenisBarang.children[1]);
                    } else {
                        dropdownOptionsJenisBarang.appendChild(newOptionDiv);
                    }
                }

                // Hide dropdown
                if (dropdownOptionsJenisBarang) {
                    dropdownOptionsJenisBarang.classList.add('hidden');
                }

                // Trigger change event
                jenisBarangSelect.dispatchEvent(new Event('change'));

                // Show success notification
                showNotification('Jenis Barang "' + event.data.data.nama_barang + '" berhasil ditambahkan dan dipilih!', 'success');
            }
        }

        // Handle tujuan kegiatan utama added (for tujuan pengambilan)
        if (event.data.type === 'tujuan-kegiatan-utama-added') {
            // Update tujuan pengambilan dropdown
            const tujuanPengambilanSelect = document.getElementById('tujuan_pengambilan_id');
            const searchTujuanPengambilanInput = document.getElementById('search_tujuan_pengambilan');
            const dropdownOptionsTujuanPengambilan = document.getElementById('dropdown_options_tujuan_pengambilan');

            if (tujuanPengambilanSelect && event.data.data) {
                // Add new option to select
                const newOption = document.createElement('option');
                newOption.value = event.data.data.id;
                newOption.textContent = event.data.data.ke;
                tujuanPengambilanSelect.appendChild(newOption);

                // Select the new option
                tujuanPengambilanSelect.value = event.data.data.id;

                // Update search input to show selected value
                if (searchTujuanPengambilanInput) {
                    searchTujuanPengambilanInput.value = event.data.data.ke;
                }

                // Update the dropdown options in the searchable dropdown
                if (dropdownOptionsTujuanPengambilan) {
                    const newOptionDiv = document.createElement('div');
                    newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                    newOptionDiv.textContent = event.data.data.ke;
                    newOptionDiv.setAttribute('data-value', event.data.data.id);

                    newOptionDiv.addEventListener('click', function() {
                        tujuanPengambilanSelect.value = event.data.data.id;
                        searchTujuanPengambilanInput.value = event.data.data.ke;
                        dropdownOptionsTujuanPengambilan.classList.add('hidden');
                        tujuanPengambilanSelect.dispatchEvent(new Event('change'));
                    });

                    if (dropdownOptionsTujuanPengambilan.children.length > 1) {
                        dropdownOptionsTujuanPengambilan.insertBefore(newOptionDiv, dropdownOptionsTujuanPengambilan.children[1]);
                    } else {
                        dropdownOptionsTujuanPengambilan.appendChild(newOptionDiv);
                    }
                }

                // Hide dropdown
                if (dropdownOptionsTujuanPengambilan) {
                    dropdownOptionsTujuanPengambilan.classList.add('hidden');
                }

                // Trigger change event
                tujuanPengambilanSelect.dispatchEvent(new Event('change'));

                // Show success notification
                showNotification('Tujuan Pengambilan "' + event.data.data.ke + '" berhasil ditambahkan dan dipilih!', 'success');
            }
        }

        // Handle tujuan kirim added (for tujuan pengiriman only)
        if (event.data.type === 'tujuan-kirim-added') {
            // Update tujuan pengiriman dropdown only
            const tujuanPengirimanSelect = document.getElementById('tujuan_pengiriman_id');
            const searchTujuanPengirimanInput = document.getElementById('search_tujuan_pengiriman');
            const dropdownOptionsTujuanPengiriman = document.getElementById('dropdown_options_tujuan_pengiriman');

            if (tujuanPengirimanSelect && event.data.data) {
                // Add new option to select
                const newOption2 = document.createElement('option');
                newOption2.value = event.data.data.id;
                newOption2.textContent = event.data.data.nama_tujuan;
                tujuanPengirimanSelect.appendChild(newOption2);

                // Update the dropdown options in the searchable dropdown
                if (dropdownOptionsTujuanPengiriman) {
                    const newOptionDiv = document.createElement('div');
                    newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                    newOptionDiv.textContent = event.data.data.nama_tujuan;
                    newOptionDiv.setAttribute('data-value', event.data.data.id);

                    newOptionDiv.addEventListener('click', function() {
                        tujuanPengirimanSelect.value = event.data.data.id;
                        searchTujuanPengirimanInput.value = event.data.data.nama_tujuan;
                        dropdownOptionsTujuanPengiriman.classList.add('hidden');
                        tujuanPengirimanSelect.dispatchEvent(new Event('change'));
                    });

                    if (dropdownOptionsTujuanPengiriman.children.length > 1) {
                        dropdownOptionsTujuanPengiriman.insertBefore(newOptionDiv, dropdownOptionsTujuanPengiriman.children[1]);
                    } else {
                        dropdownOptionsTujuanPengiriman.appendChild(newOptionDiv);
                    }
                }
            }

            // Show success notification
            if (event.data.data) {
                showNotification('Tujuan "' + event.data.data.nama_tujuan + '" berhasil ditambahkan!', 'success');
            }
        }
    });
    
    // Function to show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Popup functions for adding new records
    window.openPengirimPopup = function() {
        window.open('/pengirim/create', 'pengirimPopup', 'width=800,height=600,scrollbars=yes');
    }

    window.openJenisBarangPopup = function() {
        window.open('/jenis-barang/create', 'jenisBarangPopup', 'width=800,height=600,scrollbars=yes');
    }

    window.openTujuanKegiatanUtamaPopup = function() {
        window.open('/master/tujuan-kegiatan-utama/create', 'tujuanKegiatanUtamaPopup', 'width=800,height=600,scrollbars=yes');
    }

    window.openTujuanKirimPengirimanPopup = function() {
        window.open('/tujuan-kirim/create', 'tujuanKirimPengirimanPopup', 'width=800,height=600,scrollbars=yes');
    }
});
</script>
@endsection
