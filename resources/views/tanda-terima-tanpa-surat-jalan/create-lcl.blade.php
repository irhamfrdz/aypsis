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

                <!-- 3. Informasi Barang -->
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Informasi Barang
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Nama Barang -->
                        <div>
                            <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_barang" id="nama_barang"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                   value="{{ old('nama_barang') }}" required
                                   placeholder="Contoh: Spare Part Mesin">
                            @error('nama_barang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Barang -->
                        <div>
                            <label for="jenis_barang" class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Barang <span class="text-red-500">*</span>
                            </label>
                            <select name="jenis_barang" id="jenis_barang"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                                <option value="">Pilih Jenis Barang</option>
                                @foreach($jenisBarangs as $jenis)
                                    <option value="{{ $jenis->id }}" {{ old('jenis_barang') == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_barang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kuantitas -->
                        <div>
                            <label for="kuantitas" class="block text-sm font-medium text-gray-700 mb-1">
                                Kuantitas <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="kuantitas" id="kuantitas"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                   value="{{ old('kuantitas', 1) }}" required min="1"
                                   placeholder="1">
                            @error('kuantitas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Keterangan Barang -->
                    <div>
                        <label for="keterangan_barang" class="block text-sm font-medium text-gray-700 mb-1">
                            Keterangan Barang
                        </label>
                        <textarea name="keterangan_barang" id="keterangan_barang" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                  placeholder="Keterangan tambahan tentang barang...">{{ old('keterangan_barang') }}</textarea>
                        @error('keterangan_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 4. Dimensi dan Volume -->
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m10 0h4a1 1 0 011 1v4M4 16v4a1 1 0 001 1h4m10 0h4a1 1 0 01-1 1v-4"></path>
                        </svg>
                        Dimensi dan Volume
                    </h3>

                    <!-- Dimensi Table -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-purple-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Panjang (cm)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Lebar (cm)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Tinggi (cm)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Volume (m³)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Berat (Ton)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-purple-800 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dimensiTableBody" class="bg-white divide-y divide-gray-200">
                                <tr class="dimensi-item hover:bg-gray-50" data-index="0">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="item-number text-sm font-medium text-gray-900">1</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="number"
                                               name="dimensi_items[0][panjang]"
                                               class="dimensi-panjang w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                               placeholder="0"
                                               min="0"
                                               step="0.01"
                                               onchange="calculateItemVolume(this)">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="number"
                                               name="dimensi_items[0][lebar]"
                                               class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                               placeholder="0"
                                               min="0"
                                               step="0.01"
                                               onchange="calculateItemVolume(this)">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="number"
                                               name="dimensi_items[0][tinggi]"
                                               class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                               placeholder="0"
                                               min="0"
                                               step="0.01"
                                               onchange="calculateItemVolume(this)">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="number"
                                               name="dimensi_items[0][meter_kubik]"
                                               class="item-meter-kubik w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm focus:outline-none"
                                               placeholder="0.000000"
                                               readonly
                                               step="0.000001">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="number"
                                               name="dimensi_items[0][tonase]"
                                               class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                               placeholder="0.00"
                                               min="0"
                                               step="0.01"
                                               onchange="calculateTotals()">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <button type="button" class="remove-dimensi-item text-red-600 hover:text-red-800 p-1" style="display: none;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Add Dimensi Button & Summary -->
                    <div class="flex justify-between items-center">
                        <button type="button" id="addDimensiItem"
                                class="inline-flex items-center px-4 py-2 border border-purple-300 rounded-md text-sm font-medium text-purple-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Item
                        </button>

                        <div class="text-right">
                            <div class="text-sm text-gray-600">
                                Total Volume: <span id="totalVolume" class="font-medium text-purple-600">0.000000 m³</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                Total Berat: <span id="totalTonase" class="font-medium text-purple-600">0.00 Ton</span>
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

                <!-- 6. Tujuan Pengiriman -->
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
        // Calculate initial volumes and totals
        calculateAllVolumesAndTotals();

        // Initialize dropdowns
        initializeTermDropdown();
        initializeTujuanPengirimanDropdown();
        initializeSupirDropdown();

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
    });

    let dimensiItemIndex = 1;

    function addNewDimensiItem() {
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
                       placeholder="0"
                       min="0"
                       step="0.01"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][lebar]"
                       class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="0"
                       min="0"
                       step="0.01"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][tinggi]"
                       class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="0"
                       min="0"
                       step="0.01"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][meter_kubik]"
                       class="item-meter-kubik w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm focus:outline-none"
                       placeholder="0.000000"
                       readonly
                       step="0.000001">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][tonase]"
                       class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="0.00"
                       min="0"
                       step="0.01"
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
            volume = (panjang * lebar * tinggi) / 1000000;
        }

        row.querySelector('.item-meter-kubik').value = volume > 0 ? volume.toFixed(6) : '';
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
                volume = (panjang * lebar * tinggi) / 1000000;
            }

            row.querySelector('.item-meter-kubik').value = volume > 0 ? volume.toFixed(6) : '';
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

        // Update summary display
        document.getElementById('totalVolume').textContent = totalVolume.toFixed(6) + ' m³';
        document.getElementById('totalTonase').textContent = totalTonase.toFixed(2) + ' Ton';

        // Update hidden fields for backward compatibility
        const firstRow = document.querySelector('#dimensiTableBody .dimensi-item');
        if (firstRow) {
            document.getElementById('hiddenPanjang').value = firstRow.querySelector('.dimensi-panjang').value || '';
            document.getElementById('hiddenLebar').value = firstRow.querySelector('.dimensi-lebar').value || '';
            document.getElementById('hiddenTinggi').value = firstRow.querySelector('.dimensi-tinggi').value || '';
        }
        document.getElementById('hiddenMeterKubik').value = totalVolume > 0 ? totalVolume.toFixed(6) : '';
        document.getElementById('hiddenTonase').value = totalTonase > 0 ? totalTonase.toFixed(2) : '';
    }

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
</script>
@endpush