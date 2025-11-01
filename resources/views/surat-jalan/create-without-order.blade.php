@extends('layouts.app')

@section('title', 'Tambah Surat Jalan Tanpa Order')
@section('page_title', 'Tambah Surat Jalan Tanpa Order')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Tambah Surat Jalan Tanpa Order</h1>
                <p class="text-xs text-gray-600 mt-1">Buat surat jalan baru tanpa menggunakan order yang ada</p>
            </div>
            <a href="{{ route('surat-jalan.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada form:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('surat-jalan.store-without-order') }}" method="POST" enctype="multipart/form-data" class="p-4">
            @csrf

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
                           value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}"
                           required
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_surat_jalan') border-red-500 @enderror">
                    @error('tanggal_surat_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <div class="flex">
                        <input type="text"
                               name="no_surat_jalan"
                               value="{{ old('no_surat_jalan') }}"
                               placeholder="Masukkan nomor surat jalan"
                               required
                               autocomplete="off"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_surat_jalan') border-red-500 @enderror">
                        <button type="button"
                                onclick="generateNomorSuratJalan()"
                                class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-r-lg hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 border border-indigo-600">
                            Generate
                        </button>
                    </div>
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
                        @foreach($kegiatanSuratJalan as $kegiatan)
                            <option value="{{ $kegiatan->nama_kegiatan }}" {{ old('kegiatan') == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                {{ $kegiatan->nama_kegiatan }}
                            </option>
                        @endforeach
                    </select>
                    @error('kegiatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Data kegiatan diambil dari master kegiatan dengan type "kegiatan surat jalan"</p>
                </div>

                <!-- Pengirim Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Pengirim</h3>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="pengirim_id" class="text-sm font-medium text-gray-700">
                            Pengirim <span class="text-red-600">*</span>
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
                                @foreach($pengirimOptions as $pengirim)
                                    <option value="{{ $pengirim->id }}" {{ old('pengirim_id') == $pengirim->id ? 'selected' : '' }}>
                                        {{ $pengirim->nama_pengirim }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="dropdown_options_pengirim" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    @error('pengirim_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengirim <span class="text-red-600">*</span></label>
                    <textarea name="alamat"
                              rows="3"
                              required
                              placeholder="Alamat lengkap pengirim"
                              autocomplete="off"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barang Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Barang</h3>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="jenis_barang_id" class="text-sm font-medium text-gray-700">
                            Jenis Barang <span class="text-red-600">*</span>
                        </label>
                        <a href="{{ route('jenis-barang.create') }}" id="add_jenis_barang_link"
                           class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                           title="Tambah Jenis Barang Baru">
                            Tambah
                        </a>
                    </div>
                    <div class="relative">
                        <div class="dropdown-container-jenis-barang">
                            <input type="text" id="search_jenis_barang" placeholder="Cari jenis barang..." autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <select name="jenis_barang_id" id="jenis_barang_id" required
                                    class="hidden w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jenis_barang_id') border-red-500 @enderror">
                                <option value="">Pilih jenis barang</option>
                                @foreach($jenisBarangOptions as $jenisBarang)
                                    <option value="{{ $jenisBarang->id }}" {{ old('jenis_barang_id') == $jenisBarang->id ? 'selected' : '' }}>
                                        {{ $jenisBarang->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="dropdown_options_jenis_barang" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
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
                        <label class="block text-sm font-medium text-gray-700">Tujuan Pengambilan <span class="text-red-600">*</span></label>
                        <a href="#" id="add_tujuan_pengambilan_link" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            + Tambah
                        </a>
                    </div>
                    <div class="relative dropdown-container-tujuan-pengambilan">
                        <input type="text" id="search_tujuan_pengambilan" placeholder="Cari tujuan pengambilan..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengambilan_id') border-red-500 @enderror">
                        <select name="tujuan_pengambilan_id" id="tujuan_pengambilan_id" required class="hidden" onchange="updateUangJalan()">
                            <option value="">Pilih tujuan pengambilan...</option>
                            @foreach($tujuanKirimOptions as $tujuan)
                                <option value="{{ $tujuan->id }}" {{ old('tujuan_pengambilan_id') == $tujuan->id ? 'selected' : '' }}>
                                    {{ $tujuan->nama_tujuan }}
                                </option>
                            @endforeach
                        </select>
                        <div id="dropdown_options_tujuan_pengambilan" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-lg hidden"></div>
                    </div>
                    @error('tujuan_pengambilan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Tujuan Pengiriman <span class="text-red-600">*</span></label>
                        <a href="#" id="add_tujuan_pengiriman_link" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            + Tambah
                        </a>
                    </div>
                    <div class="relative dropdown-container-tujuan-pengiriman">
                        <input type="text" id="search_tujuan_pengiriman" placeholder="Cari tujuan pengiriman..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengiriman_id') border-red-500 @enderror">
                        <select name="tujuan_pengiriman_id" id="tujuan_pengiriman_id" required class="hidden">
                            <option value="">Pilih tujuan pengiriman...</option>
                            @foreach($tujuanKirimOptions as $tujuan)
                                <option value="{{ $tujuan->id }}" {{ old('tujuan_pengiriman_id') == $tujuan->id ? 'selected' : '' }}>
                                    {{ $tujuan->nama_tujuan }}
                                </option>
                            @endforeach
                        </select>
                        <div id="dropdown_options_tujuan_pengiriman" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-lg hidden"></div>
                    </div>
                    @error('tujuan_pengiriman_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Retur Barang</label>
                    <input type="text"
                           name="retur_barang"
                           value="{{ old('retur_barang') }}"
                           placeholder="Jenis barang retur (opsional)"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('retur_barang') border-red-500 @enderror">
                    @error('retur_barang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Retur</label>
                    <input type="number"
                           name="jumlah_retur"
                           value="{{ old('jumlah_retur') }}"
                           placeholder="0"
                           min="0"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_retur') border-red-500 @enderror">
                    @error('jumlah_retur')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kontainer Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kontainer</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kontainer <span class="text-red-600">*</span></label>
                    <input type="text"
                           name="tipe_kontainer"
                           value="{{ old('tipe_kontainer') }}"
                           placeholder="Masukkan tipe kontainer"
                           required
                           onchange="handleTipeKontainerVisibility()"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tipe_kontainer') border-red-500 @enderror">
                    @error('tipe_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="size_container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                    <select name="size"
                            id="size-select"
                            onchange="updateKontainerRules()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('size') border-red-500 @enderror">
                        <option value="">Pilih Size</option>
                        <option value="20" {{ old('size') == '20' ? 'selected' : '' }}>20 ft</option>
                        <option value="40" {{ old('size') == '40' ? 'selected' : '' }}>40 ft</option>
                        <option value="45" {{ old('size') == '45' ? 'selected' : '' }}>45 ft</option>
                    </select>
                    @error('size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Uang jalan akan diperbarui berdasarkan size kontainer</p>
                </div>

                <div id="jumlah_kontainer_container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kontainer</label>
                    <input type="number"
                           name="jumlah_kontainer"
                           id="jumlah_kontainer_input"
                           value="{{ old('jumlah_kontainer', '1') }}"
                           min="1"
                           onchange="updateKontainerRules()"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_kontainer') border-red-500 @enderror">
                    @error('jumlah_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1" id="jumlah_kontainer_note">Untuk size 40ft dan 45ft, hanya bisa 1 kontainer per surat jalan</p>
                    
                    <!-- Pricelist notification -->
                    <div id="pricelist-info" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-blue-700" id="pricelist-info-text">
                                Menggunakan pricelist 40ft untuk 2 kontainer 20ft
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Packaging Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kemasan</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Karton</label>
                    <select name="karton"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('karton') border-red-500 @enderror">
                        <option value="">Pilih Karton</option>
                        <option value="ya" {{ old('karton') == 'ya' ? 'selected' : '' }}>Ya</option>
                        <option value="tidak" {{ old('karton') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                    </select>
                    @error('karton')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plastik</label>
                    <select name="plastik"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('plastik') border-red-500 @enderror">
                        <option value="">Pilih Plastik</option>
                        <option value="ya" {{ old('plastik') == 'ya' ? 'selected' : '' }}>Ya</option>
                        <option value="tidak" {{ old('plastik') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                    </select>
                    @error('plastik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Terpal</label>
                    <select name="terpal"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('terpal') border-red-500 @enderror">
                        <option value="">Pilih Terpal</option>
                        <option value="ya" {{ old('terpal') == 'ya' ? 'selected' : '' }}>Ya</option>
                        <option value="tidak" {{ old('terpal') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                    </select>
                    @error('terpal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transport Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Transport</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir <span class="text-red-600">*</span></label>
                    <select name="supir"
                            id="supir-select"
                            required
                            onchange="updateNoPlat()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">
                        <option value="">Pilih Supir</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" 
                                    data-plat="{{ $supir->plat }}"
                                    {{ old('supir') == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    @error('supir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <select name="kenek"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('kenek') border-red-500 @enderror">
                        <option value="">Pilih Kenek (Opsional)</option>
                        @foreach($keneks as $kenek)
                            <option value="{{ $kenek->nama_lengkap }}"
                                    {{ old('kenek') == $kenek->nama_lengkap ? 'selected' : '' }}>
                                {{ $kenek->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    @error('kenek')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Data kenek diambil dari master karyawan divisi krani</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat <span class="text-red-600">*</span></label>
                    <input type="text"
                           name="no_plat"
                           id="no-plat-input"
                           value="{{ old('no_plat') }}"
                           placeholder="Nomor plat kendaraan"
                           required
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror">
                    @error('no_plat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">No. plat akan otomatis terisi berdasarkan supir yang dipilih</p>
                </div>

                <!-- Schedule Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Jadwal</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Muat</label>
                    <input type="date"
                           name="tanggal_muat"
                           value="{{ old('tanggal_muat') }}"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_muat') border-red-500 @enderror">
                    @error('tanggal_muat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berangkat</label>
                    <input type="date"
                           name="tanggal_berangkat"
                           value="{{ old('tanggal_berangkat') }}"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_berangkat') border-red-500 @enderror">
                    @error('tanggal_berangkat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Tambahan</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <input type="text"
                           name="term"
                           value="{{ old('term') }}"
                           placeholder="Syarat pembayaran"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('term') border-red-500 @enderror">
                    @error('term')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rit</label>
                    <select name="rit"
                            autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('rit') border-red-500 @enderror">
                        <option value="">Pilih Rit</option>
                        <option value="1" {{ old('rit') == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ old('rit') == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ old('rit') == '3' ? 'selected' : '' }}>3</option>
                    </select>
                    @error('rit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <input type="number"
                           name="uang_jalan"
                           id="uang-jalan-input"
                           value="{{ old('uang_jalan', '0') }}"
                           min="0"
                           step="1000"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none @error('uang_jalan') border-red-500 @enderror">
                    @error('uang_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Uang jalan otomatis berdasarkan tujuan pengambilan dan size kontainer. Untuk 2 kontainer 20ft akan menggunakan pricelist 40ft.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Pemesanan</label>
                    <input type="text"
                           name="no_pemesanan"
                           value="{{ old('no_pemesanan') }}"
                           placeholder="Nomor pemesanan"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_pemesanan') border-red-500 @enderror">
                    @error('no_pemesanan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar/Dokumen</label>
                    <input type="file"
                           name="gambar"
                           accept="image/*"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('gambar') border-red-500 @enderror">
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Max: 2MB</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan"
                              rows="3"
                              placeholder="Catatan tambahan (opsional)"
                              autocomplete="off"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function generateNomorSuratJalan() {
    fetch('{{ route("surat-jalan.generate-nomor") }}')
        .then(response => response.json())
        .then(data => {
            document.querySelector('input[name="no_surat_jalan"]').value = data.no_surat_jalan;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal generate nomor surat jalan');
        });
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

function updateUangJalan() {
    const tujuanPengambilanSelect = document.querySelector('select[name="tujuan_pengambilan_id"]');
    const sizeSelect = document.querySelector('select[name="size"]');
    const uangJalanInput = document.getElementById('uang-jalan-input');
    const jumlahKontainer = parseInt(document.querySelector('input[name="jumlah_kontainer"]').value) || 1;

    // Get selected tujuan pengambilan name
    let tujuanPengambilan = '';
    if (tujuanPengambilanSelect && tujuanPengambilanSelect.selectedIndex > 0) {
        tujuanPengambilan = tujuanPengambilanSelect.options[tujuanPengambilanSelect.selectedIndex].text;
    }

    if (tujuanPengambilan) {
        let size = sizeSelect ? sizeSelect.value : '';

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

                // Add hidden field with formatted display value
                let displayField = document.getElementById('uang-jalan-display');
                if (!displayField) {
                    displayField = document.createElement('input');
                    displayField.type = 'hidden';
                    displayField.name = 'uang_jalan_display';
                    displayField.id = 'uang-jalan-display';
                    uangJalanInput.parentNode.appendChild(displayField);
                }
                displayField.value = data.uang_jalan; // Keep formatted version for display

                console.log(`Uang Jalan Updated - Original Size: ${size}, Jumlah: ${jumlahKontainer}, Calculation Size: ${calculationSize}, Raw: ${rawValue}, Formatted: ${data.uang_jalan}`);
            } else {
                uangJalanInput.value = '0';
                console.log('Uang jalan tidak ditemukan untuk tujuan:', tujuanPengambilan);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            uangJalanInput.value = '0';
        });
    } else {
        uangJalanInput.value = '0';
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
        
        // Update uang jalan setelah rules diterapkan
        updateUangJalan();
    }
}

function handleTipeKontainerVisibility() {
    const tipeKontainer = document.querySelector('input[name="tipe_kontainer"]').value.toLowerCase();
    const sizeContainer = document.getElementById('size_container');
    const jumlahKontainerContainer = document.getElementById('jumlah_kontainer_container');
    const sizeSelect = document.querySelector('select[name="size"]');
    const jumlahKontainerInput = document.querySelector('input[name="jumlah_kontainer"]');

    if (tipeKontainer === 'cargo') {
        // Hide size and jumlah kontainer fields for cargo
        sizeContainer.style.display = 'none';
        jumlahKontainerContainer.style.display = 'none';
        
        // Remove required attributes and clear values
        sizeSelect.removeAttribute('required');
        jumlahKontainerInput.removeAttribute('required');
        sizeSelect.value = '';
        jumlahKontainerInput.value = '';
        
        console.log('Cargo type detected - hiding size and jumlah kontainer fields');
    } else {
        // Show size and jumlah kontainer fields for other types
        sizeContainer.style.display = 'block';
        jumlahKontainerContainer.style.display = 'block';
        
        // Set default values if empty
        if (!jumlahKontainerInput.value) {
            jumlahKontainerInput.value = '1';
        }
        
        console.log('Non-cargo type detected - showing size and jumlah kontainer fields');
    }
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
    } else if (config.selectId === 'jenis_barang_id') {
        window.refreshJenisBarangOptions = refreshOriginalOptions;
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
                    } else if (config.selectId === 'jenis_barang_id') {
                        searchInput.placeholder = 'Cari jenis barang...';
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

// Handle form submission for cargo type
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const tipeKontainer = document.querySelector('input[name="tipe_kontainer"]').value.toLowerCase();
            
            if (tipeKontainer === 'cargo') {
                // For cargo, remove size and jumlah_kontainer from submission if they're hidden
                const sizeSelect = document.querySelector('select[name="size"]');
                const jumlahKontainerInput = document.querySelector('input[name="jumlah_kontainer"]');
                
                if (sizeSelect && sizeSelect.parentElement.style.display === 'none') {
                    sizeSelect.removeAttribute('name');
                }
                
                if (jumlahKontainerInput && jumlahKontainerInput.parentElement.style.display === 'none') {
                    jumlahKontainerInput.removeAttribute('name');
                }
                
                console.log('Cargo form submission - removed hidden fields');
            }
        });
    }

    // Initialize Pengirim dropdown
    createSearchableDropdown({
        selectId: 'pengirim_id',
        searchId: 'search_pengirim',
        dropdownId: 'dropdown_options_pengirim',
        containerClass: 'dropdown-container-pengirim'
    });

    // Handle Pengirim "Tambah" link
    const addPengirimLink = document.getElementById('add_pengirim_link');
    const searchPengirimInput = document.getElementById('search_pengirim');
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

    // Handle message from popup for new items added
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

                // Refresh the original options for the searchable dropdown
                if (window.refreshPengirimOptions) {
                    window.refreshPengirimOptions();
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
        } else if (event.data.type === 'jenis-barang-added') {
            // Handle Jenis Barang added
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

                // Refresh the original options for the searchable dropdown
                if (window.refreshJenisBarangOptions) {
                    window.refreshJenisBarangOptions();
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
        } else if (event.data.type === 'tujuan-kirim-added') {
            // Handle Tujuan Kirim added for both pengambilan and pengiriman
            const windowName = event.data.windowName;

            if (windowName === 'addTujuanPengambilan') {
                // Handle Tujuan Pengambilan
                const tujuanPengambilanSelect = document.getElementById('tujuan_pengambilan_id');
                const searchTujuanPengambilanInput = document.getElementById('search_tujuan_pengambilan');
                const dropdownOptionsTujuanPengambilan = document.getElementById('dropdown_options_tujuan_pengambilan');

                if (tujuanPengambilanSelect && event.data.data) {
                    // Add new option to select
                    const newOption = document.createElement('option');
                    newOption.value = event.data.data.id;
                    newOption.textContent = event.data.data.nama_tujuan;
                    tujuanPengambilanSelect.appendChild(newOption);

                    // Select the new option
                    tujuanPengambilanSelect.value = event.data.data.id;

                    // Update search input to show selected value
                    if (searchTujuanPengambilanInput) {
                        searchTujuanPengambilanInput.value = event.data.data.nama_tujuan;
                    }

                    // Update the dropdown options in the searchable dropdown
                    if (dropdownOptionsTujuanPengambilan) {
                        const newOptionDiv = document.createElement('div');
                        newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                        newOptionDiv.textContent = event.data.data.nama_tujuan;
                        newOptionDiv.setAttribute('data-value', event.data.data.id);

                        newOptionDiv.addEventListener('click', function() {
                            tujuanPengambilanSelect.value = event.data.data.id;
                            searchTujuanPengambilanInput.value = event.data.data.nama_tujuan;
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
                    showNotification('Tujuan Pengambilan "' + event.data.data.nama_tujuan + '" berhasil ditambahkan dan dipilih!', 'success');
                }
            } else if (windowName === 'addTujuanPengiriman') {
                // Handle Tujuan Pengiriman
                const tujuanPengirimanSelect = document.getElementById('tujuan_pengiriman_id');
                const searchTujuanPengirimanInput = document.getElementById('search_tujuan_pengiriman');
                const dropdownOptionsTujuanPengiriman = document.getElementById('dropdown_options_tujuan_pengiriman');

                if (tujuanPengirimanSelect && event.data.data) {
                    // Add new option to select
                    const newOption = document.createElement('option');
                    newOption.value = event.data.data.id;
                    newOption.textContent = event.data.data.nama_tujuan;
                    tujuanPengirimanSelect.appendChild(newOption);

                    // Select the new option
                    tujuanPengirimanSelect.value = event.data.data.id;

                    // Update search input to show selected value
                    if (searchTujuanPengirimanInput) {
                        searchTujuanPengirimanInput.value = event.data.data.nama_tujuan;
                    }

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

                    // Hide dropdown
                    if (dropdownOptionsTujuanPengiriman) {
                        dropdownOptionsTujuanPengiriman.classList.add('hidden');
                    }

                    // Trigger change event
                    tujuanPengirimanSelect.dispatchEvent(new Event('change'));

                    // Show success notification
                    showNotification('Tujuan Pengiriman "' + event.data.data.nama_tujuan + '" berhasil ditambahkan dan dipilih!', 'success');
                }
            }
        }
    });

    // Initialize Jenis Barang dropdown
    createSearchableDropdown({
        selectId: 'jenis_barang_id',
        searchId: 'search_jenis_barang',
        dropdownId: 'dropdown_options_jenis_barang',
        containerClass: 'dropdown-container-jenis-barang'
    });

    // Handle Jenis Barang "Tambah" link
    const addJenisBarangLink = document.getElementById('add_jenis_barang_link');
    const searchJenisBarangInput = document.getElementById('search_jenis_barang');
    if (addJenisBarangLink && searchJenisBarangInput) {
        addJenisBarangLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchJenisBarangInput.value.trim();
            let url = "{{ route('jenis-barang.create') }}";

            // Add popup parameter and nama_barang if available
            const params = new URLSearchParams();
            params.append('popup', '1');

            if (searchValue) {
                params.append('search', searchValue);
            }

            url += '?' + params.toString();

            // Open as popup window with specific dimensions
            const popup = window.open(
                url,
                'addJenisBarang',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }

    // Initialize Tujuan Pengambilan dropdown
    createSearchableDropdown({
        selectId: 'tujuan_pengambilan_id',
        searchId: 'search_tujuan_pengambilan',
        dropdownId: 'dropdown_options_tujuan_pengambilan',
        containerClass: 'dropdown-container-tujuan-pengambilan'
    });

    // Handle Tujuan Pengambilan "Tambah" link
    const addTujuanPengambilanLink = document.getElementById('add_tujuan_pengambilan_link');
    const searchTujuanPengambilanInput = document.getElementById('search_tujuan_pengambilan');
    if (addTujuanPengambilanLink && searchTujuanPengambilanInput) {
        addTujuanPengambilanLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTujuanPengambilanInput.value.trim();
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
                'addTujuanPengambilan',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }

    // Initialize Tujuan Pengiriman dropdown
    createSearchableDropdown({
        selectId: 'tujuan_pengiriman_id',
        searchId: 'search_tujuan_pengiriman',
        dropdownId: 'dropdown_options_tujuan_pengiriman',
        containerClass: 'dropdown-container-tujuan-pengiriman'
    });

    // Handle Tujuan Pengiriman "Tambah" link
    const addTujuanPengirimanLink = document.getElementById('add_tujuan_pengiriman_link');
    const searchTujuanPengirimanInput = document.getElementById('search_tujuan_pengiriman');
    if (addTujuanPengirimanLink && searchTujuanPengirimanInput) {
        addTujuanPengirimanLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTujuanPengirimanInput.value.trim();
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

    // Initialize visibility and rules on page load
    handleTipeKontainerVisibility();
    updateKontainerRules();
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
</script>
@endsection