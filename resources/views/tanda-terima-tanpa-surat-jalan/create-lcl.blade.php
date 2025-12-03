@extends('layouts.app')

@section('title', 'Buat Tanda Terima LCL')
@section('page_title', 'Buat Tanda Terima LCL')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Buat Tanda Terima LCL</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-600">Less Container Load - Kontainer dibagi dengan beberapa pengirim</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Tipe: LCL
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('tanda-terima-tanpa-surat-jalan.pilih-tipe') }}"
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

            <form action="{{ route('tanda-terima-lcl.store') }}" method="POST" class="space-y-6">
                @csrf

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
                                Nomor Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_tanda_terima" id="nomor_tanda_terima"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('nomor_tanda_terima') }}" required
                                   placeholder="TTR-LCL-001">
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
                                   value="{{ old('tanggal_tanda_terima', date('Y-m-d')) }}" required>
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
                                   value="{{ old('no_surat_jalan_customer') }}"
                                   placeholder="SJ-CUS-001">
                            @error('no_surat_jalan_customer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Term -->
                        <div>
                            <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Term <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="termSearch" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="Cari term..." autocomplete="off">
                                <div id="termDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    @foreach($terms as $term)
                                        <div class="term-option px-3 py-2 hover:bg-green-50 cursor-pointer text-sm border-b border-gray-100"
                                             data-value="{{ $term->id }}" data-text="{{ $term->nama_status }}">
                                            {{ $term->nama_status }}
                                        </div>
                                    @endforeach
                                </div>
                                <select name="term_id" id="term_id" class="hidden" required>
                                    <option value="">Pilih Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
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
                            <h4 class="font-medium text-blue-800 border-b border-blue-200 pb-2">Data Penerima</h4>
                            
                            <!-- Nama Penerima -->
                            <div>
                                <label for="nama_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Penerima <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_penerima" id="nama_penerima"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('nama_penerima') }}" required
                                       placeholder="PT. Nama Perusahaan Penerima">
                                @error('nama_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- PIC Penerima -->
                            <div>
                                <label for="pic_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                    PIC Penerima
                                </label>
                                <input type="text" name="pic_penerima" id="pic_penerima"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('pic_penerima') }}"
                                       placeholder="Nama PIC Penerima">
                                @error('pic_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Telepon Penerima -->
                            <div>
                                <label for="telepon_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                    Telepon Penerima
                                </label>
                                <input type="text" name="telepon_penerima" id="telepon_penerima"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('telepon_penerima') }}"
                                       placeholder="08123456789">
                                @error('telepon_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat Penerima -->
                            <div>
                                <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Penerima <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat_penerima" id="alamat_penerima" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          required placeholder="Alamat lengkap penerima...">{{ old('alamat_penerima') }}</textarea>
                                @error('alamat_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Pengirim -->
                        <div class="space-y-4">
                            <h4 class="font-medium text-blue-800 border-b border-blue-200 pb-2">Data Pengirim</h4>
                            
                            <!-- Nama Pengirim -->
                            <div>
                                <label for="nama_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Pengirim <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_pengirim" id="nama_pengirim"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('nama_pengirim') }}" required
                                       placeholder="PT. Nama Perusahaan Pengirim">
                                @error('nama_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- PIC Pengirim -->
                            <div>
                                <label for="pic_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                    PIC Pengirim
                                </label>
                                <input type="text" name="pic_pengirim" id="pic_pengirim"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('pic_pengirim') }}"
                                       placeholder="Nama PIC Pengirim">
                                @error('pic_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Telepon Pengirim -->
                            <div>
                                <label for="telepon_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                    Telepon Pengirim
                                </label>
                                <input type="text" name="telepon_pengirim" id="telepon_pengirim"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('telepon_pengirim') }}"
                                       placeholder="08123456789">
                                @error('telepon_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat Pengirim -->
                            <div>
                                <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Pengirim <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat_pengirim" id="alamat_pengirim" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          required placeholder="Alamat lengkap pengirim...">{{ old('alamat_pengirim') }}</textarea>
                                @error('alamat_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                        <div class="dimensi-row mb-4 pb-4 border-b border-purple-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label for="nama_barang_0" class="block text-xs font-medium text-gray-500 mb-2">
                                        Nama Barang
                                    </label>
                                    <input type="text"
                                           name="nama_barang[]" 
                                           id="nama_barang_0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                           placeholder="Nama barang"
                                           value="{{ old('nama_barang.0') }}">
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
                                           value="{{ old('jumlah.0') }}"
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
                                Volume akan dihitung otomatis dari panjang × lebar × tinggi
                            </p>
                        </div>
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
                                       value="{{ old('supir', 'Supir Customer') }}">
                                <div id="supirDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    @foreach($supirs as $supir)
                                        <div class="supir-option px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100"
                                             data-value="{{ $supir->nama_supir }}" 
                                             data-text="{{ $supir->nama_supir }}"
                                             data-plat="{{ $supir->no_plat }}">
                                            <div class="font-medium">{{ $supir->nama_supir }}</div>
                                            <div class="text-xs text-gray-500">{{ $supir->no_plat }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="supir" id="supir" required value="{{ old('supir', 'Supir Customer') }}">
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
                                   value="{{ old('no_plat', 'Plat Customer') }}" required
                                   placeholder="B 1234 XYZ">
                            @error('no_plat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 6. Informasi Kontainer -->
                <div class="bg-teal-50 p-4 rounded-lg border border-teal-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Informasi Kontainer
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Nomor Kontainer -->
                        <div>
                            <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Kontainer
                            </label>
                            <div class="relative">
                                <input type="text" id="nomorKontainerSearch" placeholder="Cari nomor kontainer..." autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <div id="nomorKontainerDropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto hidden">
                                    @if(isset($containerOptions) && count($containerOptions))
                                        @foreach($containerOptions as $opt)
                                            <div class="nomor-kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer" data-value="{{ $opt['value'] }}" data-text="{{ $opt['label'] }}@if(!empty($opt['size'])) - {{ $opt['size'] }}@endif" data-size="{{ $opt['size'] }}" data-source="{{ $opt['source'] }}">
                                                {{ $opt['label'] }}@if(!empty($opt['size'])) - {{ $opt['size'] }}@endif
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="nomor-kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer text-blue-600" data-value="__manual__" data-text="&raquo; Ketik manual / Lainnya">
                                        &raquo; Ketik manual / Lainnya
                                    </div>
                                </div>
                                <input type="hidden" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer') }}">
                            </div>
                            <input type="text" name="nomor_kontainer_manual" id="nomor_kontainer_manual" value="{{ old('nomor_kontainer_manual') }}" placeholder="Masukkan nomor kontainer jika memilih Lainnya" class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md text-sm hidden" />
                            <p class="mt-1 text-xs text-gray-500">Isi jika sudah ditentukan kontainernya, kosongkan jika belum</p>
                            @error('nomor_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Size Kontainer -->
                        <div>
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Size Kontainer
                            </label>
                            <select name="size_kontainer" id="size_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Pilih Size Kontainer</option>
                                <option value="20ft" {{ old('size_kontainer') == '20ft' ? 'selected' : '' }}>20 Feet</option>
                                <option value="40ft" {{ old('size_kontainer') == '40ft' ? 'selected' : '' }}>40 Feet</option>
                                <option value="40hc" {{ old('size_kontainer') == '40hc' ? 'selected' : '' }}>40 Feet High Cube</option>
                                <option value="45ft" {{ old('size_kontainer') == '45ft' ? 'selected' : '' }}>45 Feet</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Ukuran kontainer yang akan digunakan</p>
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nomor Seal -->
                        <div>
                            <label for="nomor_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Seal
                            </label>
                            <input type="text" name="nomor_seal" id="nomor_seal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                   value="{{ old('nomor_seal') }}"
                                   placeholder="Masukkan nomor seal">
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jika diisi, data akan langsung bisa masuk ke prospek
                            </p>
                            @error('nomor_seal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipe Kontainer -->
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe Kontainer
                            </label>
                            <select name="tipe_kontainer" id="tipe_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Pilih Tipe Kontainer</option>
                                <option value="HC" {{ old('tipe_kontainer') == 'HC' ? 'selected' : '' }}>HC (High Cube)</option>
                                <option value="STD" {{ old('tipe_kontainer') == 'STD' ? 'selected' : '' }}>STD (Standard)</option>
                                <option value="RF" {{ old('tipe_kontainer') == 'RF' ? 'selected' : '' }}>RF (Reefer)</option>
                                <option value="OT" {{ old('tipe_kontainer') == 'OT' ? 'selected' : '' }}>OT (Open Top)</option>
                                <option value="FR" {{ old('tipe_kontainer') == 'FR' ? 'selected' : '' }}>FR (Flat Rack)</option>
                                <option value="Dry Container" {{ old('tipe_kontainer') == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Jenis kontainer yang akan digunakan</p>
                            @error('tipe_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>


                </div>

                <!-- 7. Tujuan Pengiriman -->
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
                                   placeholder="Cari tujuan pengiriman..." autocomplete="off" required>
                            <div id="tujuanPengirimanDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                @foreach($masterTujuanKirims as $tujuan)
                                    <div class="tujuan-pengiriman-option px-3 py-2 hover:bg-indigo-50 cursor-pointer text-sm border-b border-gray-100"
                                         data-value="{{ $tujuan->id }}" data-text="{{ $tujuan->nama_tujuan }}">
                                        {{ $tujuan->nama_tujuan }}
                                    </div>
                                @endforeach
                            </div>
                            <select name="tujuan_pengiriman" id="tujuan_pengiriman" class="hidden" required>
                                <option value="">Pilih Tujuan Pengiriman</option>
                                @foreach($masterTujuanKirims as $tujuan)
                                    <option value="{{ $tujuan->id }}" {{ old('tujuan_pengiriman') == $tujuan->id ? 'selected' : '' }}>
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

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.pilih-tipe') }}"
                       class="px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Tanda Terima LCL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize dropdowns
        initializeTermDropdown();
        initializeTujuanPengirimanDropdown();
        initializeSupirDropdown();
    });

    // Counter untuk index dimensi baru
    let dimensiCounter = document.querySelectorAll('#dimensi-container .dimensi-row').length || 1;

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

    function initializeTermDropdown() {
        const searchInput = document.getElementById('termSearch');
        const dropdown = document.getElementById('termDropdown');
        const hiddenSelect = document.getElementById('term_id');
        const options = document.querySelectorAll('.term-option');

        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

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

        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                hiddenSelect.value = value;
                searchInput.value = text;
                dropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#termSearch') && !e.target.closest('#termDropdown')) {
                dropdown.classList.add('hidden');
            }
        });
    }

    function initializeTujuanPengirimanDropdown() {
        const searchInput = document.getElementById('tujuanPengirimanSearch');
        const dropdown = document.getElementById('tujuanPengirimanDropdown');
        const hiddenSelect = document.getElementById('tujuan_pengiriman');
        const options = document.querySelectorAll('.tujuan-pengiriman-option');

        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

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

        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                hiddenSelect.value = value;
                searchInput.value = text;
                dropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#tujuanPengirimanSearch') && !e.target.closest('#tujuanPengirimanDropdown')) {
                dropdown.classList.add('hidden');
            }
        });
    }

    function initializeSupirDropdown() {
        const searchInput = document.getElementById('supirSearch');
        const dropdown = document.getElementById('supirDropdown');
        const hiddenInput = document.getElementById('supir');
        const platInput = document.getElementById('no_plat');
        const options = document.querySelectorAll('.supir-option');

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

    // Initialize nomor kontainer dropdown
    initializeNomorKontainerDropdown();

    function setSizeKontainerValue(size) {
        const sizeSelect = document.getElementById('size_kontainer');
        if (!sizeSelect) return;
        // Normalize for LCL formats if needed
        function normalizeLclSize(s) {
            if (!s) return '';
            s = String(s).toLowerCase();
            if (s.match(/40hc|40 hc/)) return '40hc';
            if (s.match(/40/)) return '40ft';
            if (s.match(/20/)) return '20ft';
            if (s.match(/45/)) return '45ft';
            return s;
        }

    // Ensure manual value is submitted if manual option chosen (create-lcl)
    const createLclForm = document.querySelector('form');
    if (createLclForm) {
        createLclForm.addEventListener('submit', function(e) {
            const hiddenInput = document.getElementById('nomor_kontainer');
            const manualField = document.getElementById('nomor_kontainer_manual');
            if (hiddenInput && hiddenInput.value === '__manual__') {
                if (!manualField || !manualField.value.trim()) {
                    e.preventDefault();
                    alert('Silakan isi nomor kontainer pada input manual.');
                    (manualField || document.getElementById('nomorKontainerSearch')).focus();
                    return false;
                }
                // Set hidden input to manual value
                hiddenInput.value = manualField.value.trim();
            }
        });
    }
        size = normalizeLclSize(size);
        let matched = false;
        for (let i = 0; i < sizeSelect.options.length; i++) {
            const opt = sizeSelect.options[i];
            if (!size) {
                opt.selected = false;
                continue;
            }
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

    function initializeNomorKontainerDropdown() {
        const searchInput = document.getElementById('nomorKontainerSearch');
        const dropdown = document.getElementById('nomorKontainerDropdown');
        const hiddenInput = document.getElementById('nomor_kontainer');
        const manualField = document.getElementById('nomor_kontainer_manual');
        const options = document.querySelectorAll('.nomor-kontainer-option');

        if (!searchInput || !dropdown || !hiddenInput) {
            console.error('Required elements not found for nomor kontainer dropdown');
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
            if (!e.target.closest('#nomorKontainerSearch') && !e.target.closest('#nomorKontainerDropdown')) {
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
            const selectedOption = document.querySelector(`.nomor-kontainer-option[data-value="${hiddenInput.value}"]`);
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
</script>
@endpush