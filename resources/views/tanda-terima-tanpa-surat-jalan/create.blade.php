@extends('layouts.app')

@section('title', 'Buat Tanda Terima Tanpa Surat Jalan')
@section('page_title', 'Buat Tanda Terima Tanpa Surat Jalan')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Buat Tanda Terima Tanpa Surat Jalan</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-600">Isi form di bawah untuk membuat tanda terima baru</p>
                    @if(isset($tipe))
                        @php
                            $tipeLabel = strtoupper($tipe);
                            $tipeColor = $tipe === 'fcl' ? 'blue' : ($tipe === 'lcl' ? 'green' : 'orange');
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $tipeColor }}-100 text-{{ $tipeColor }}-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Tipe: {{ $tipeLabel }}
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-4">
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

            <form action="{{ route('tanda-terima-tanpa-surat-jalan.store') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- Hidden input untuk tipe kontainer --}}
                @if(isset($tipe))
                    <input type="hidden" name="tipe_kontainer_selected" value="{{ $tipe }}">
                @endif

                <!-- Informasi Dasar -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_tanda_terima" id="nomor_tanda_terima"
                                   value="{{ old('nomor_tanda_terima') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_tanda_terima') border-red-500 @enderror"
                                   placeholder="TTR-001">
                            @error('nomor_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_tanda_terima" id="tanggal_tanda_terima"
                                   value="{{ old('tanggal_tanda_terima', date('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_tanda_terima') border-red-500 @enderror">
                            @error('tanggal_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nomor_surat_jalan_customer" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Surat Jalan Customer
                            </label>
                            <input type="text" name="nomor_surat_jalan_customer" id="nomor_surat_jalan_customer"
                                   value="{{ old('nomor_surat_jalan_customer') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_surat_jalan_customer') border-red-500 @enderror"
                                   placeholder="SJ-CUSTOMER-001">
                            @error('nomor_surat_jalan_customer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="estimasi_naik_kapal" class="block text-sm font-medium text-gray-700 mb-1">
                                Estimasi Naik Kapal
                            </label>
                            <select name="estimasi_naik_kapal" id="estimasi_naik_kapal"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('estimasi_naik_kapal') border-red-500 @enderror">
                                <option value="">-- Pilih Kapal --</option>
                                @foreach($master_kapals as $kapal)
                                    <option value="{{ $kapal->nama_kapal }}" {{ old('estimasi_naik_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estimasi_naik_kapal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Term Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Term
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="term_id" id="term_id" class="hidden @error('term_id') border-red-500 @enderror">
                                    <option value="">Pilih Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                            {{ $term->nama_status }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Search input -->
                                <input type="text" id="termSearch"
                                       placeholder="Cari atau pilih term..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('term_id') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="termDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('term.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Term Baru
                                        </a>
                                    </div>
                                    @foreach($terms as $term)
                                        <div class="term-option px-3 py-2 hover:bg-gray-50 cursor-pointer"
                                             data-value="{{ $term->id }}"
                                             data-text="{{ $term->nama_status }}">
                                            {{ $term->nama_status }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('term_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Penerima dan Pengirim -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Penerima dan Pengirim</h3>
                    <!-- Baris 1: Nama Penerima dan Pengirim -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                Penerima <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="penerima" id="penerima" value="{{ old('penerima') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('penerima') border-red-500 @enderror"
                                   placeholder="Nama penerima">
                            @error('penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                Pengirim <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('pengirim') border-red-500 @enderror"
                                   placeholder="Nama pengirim">
                            @error('pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 2: PIC dan Telepon -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="pic" class="block text-sm font-medium text-gray-700 mb-1">
                                PIC (Person In Charge)
                            </label>
                            <input type="text" name="pic" id="pic" value="{{ old('pic') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('pic') border-red-500 @enderror"
                                   placeholder="Nama PIC">
                            @error('pic')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">
                                Telepon
                            </label>
                            <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('telepon') border-red-500 @enderror"
                                   placeholder="Nomor telepon">
                            @error('telepon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 3: Alamat -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Penerima
                            </label>
                            <textarea name="alamat_penerima" id="alamat_penerima" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat_penerima') border-red-500 @enderror"
                                      placeholder="Alamat lengkap penerima">{{ old('alamat_penerima') }}</textarea>
                            @error('alamat_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Pengirim
                            </label>
                            <textarea name="alamat_pengirim" id="alamat_pengirim" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat_pengirim') border-red-500 @enderror"
                                      placeholder="Alamat lengkap pengirim">{{ old('alamat_pengirim') }}</textarea>
                            @error('alamat_pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Barang -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Barang</h3>

                    <!-- Baris 1: Informasi Dasar Barang -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="jenis_barang" class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="jenis_barang" id="jenis_barang" value="{{ old('jenis_barang') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('jenis_barang') border-red-500 @enderror"
                                   placeholder="Jenis/nama barang">
                            @error('jenis_barang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nama_barang') border-red-500 @enderror"
                                   placeholder="Nama spesifik barang">
                            @error('nama_barang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="aktifitas" class="block text-sm font-medium text-gray-700 mb-1">
                                Aktifitas
                            </label>
                            <select name="aktifitas" id="aktifitas"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('aktifitas') border-red-500 @enderror">
                                <option value="">-- Pilih Aktifitas --</option>
                                <option value="bongkar" {{ old('aktifitas') == 'bongkar' ? 'selected' : '' }}>Bongkar</option>
                                <option value="muat" {{ old('aktifitas') == 'muat' ? 'selected' : '' }}>Muat</option>
                                <option value="pindah" {{ old('aktifitas') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                                <option value="sortir" {{ old('aktifitas') == 'sortir' ? 'selected' : '' }}>Sortir</option>
                                <option value="lainnya" {{ old('aktifitas') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('aktifitas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 2: Kuantitas dan Berat -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label for="jumlah_barang" class="block text-sm font-medium text-gray-700 mb-1">
                                Jumlah <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="jumlah_barang" id="jumlah_barang" value="{{ old('jumlah_barang', 1) }}" required min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="satuan_barang" class="block text-sm font-medium text-gray-700 mb-1">
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="satuan_barang" id="satuan_barang" value="{{ old('satuan_barang', 'unit') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="pcs, kg, box, dll">
                        </div>
                        <div>
                            <label for="berat" class="block text-sm font-medium text-gray-700 mb-1">
                                Berat
                            </label>
                            <input type="number" name="berat" id="berat" value="{{ old('berat') }}" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="satuan_berat" class="block text-sm font-medium text-gray-700 mb-1">
                                Satuan Berat
                            </label>
                            <input type="text" name="satuan_berat" id="satuan_berat" value="{{ old('satuan_berat', 'kg') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="kg, ton, gram">
                        </div>
                    </div>

                    <!-- Dimensi & Volume Items -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Dimensi & Volume Items
                            </label>
                            <button type="button" id="addDimensiItem" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Item
                            </button>
                        </div>

                        <!-- Table Container -->
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                            No.
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Panjang (cm)
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lebar (cm)
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tinggi (cm)
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Volume (m³)
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tonase (Ton)
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="dimensiTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Default first item -->
                                    <tr class="dimensi-item hover:bg-gray-50" data-index="0">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="item-number text-sm font-medium text-gray-900">1</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][panjang]"
                                                   class="dimensi-panjang w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                   placeholder="0"
                                                   min="0"
                                                   step="0.01"
                                                   value="{{ old('dimensi_items.0.panjang', old('panjang')) }}"
                                                   onchange="calculateItemVolume(this)">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][lebar]"
                                                   class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                   placeholder="0"
                                                   min="0"
                                                   step="0.01"
                                                   value="{{ old('dimensi_items.0.lebar', old('lebar')) }}"
                                                   onchange="calculateItemVolume(this)">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][tinggi]"
                                                   class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                   placeholder="0"
                                                   min="0"
                                                   step="0.01"
                                                   value="{{ old('dimensi_items.0.tinggi', old('tinggi')) }}"
                                                   onchange="calculateItemVolume(this)">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][meter_kubik]"
                                                   class="item-meter-kubik w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm focus:outline-none"
                                                   placeholder="0.000000"
                                                   readonly
                                                   step="0.000001"
                                                   value="{{ old('dimensi_items.0.meter_kubik', old('meter_kubik')) }}">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number"
                                                   name="dimensi_items[0][tonase]"
                                                   class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                   placeholder="0.00"
                                                   min="0"
                                                   step="0.01"
                                                   value="{{ old('dimensi_items.0.tonase', old('tonase')) }}"
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
                                <!-- Table Footer with Totals -->
                                <tfoot class="bg-indigo-50">
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-900">
                                            Total:
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span id="totalVolume" class="text-sm font-semibold text-indigo-900">0.000000 m³</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span id="totalTonase" class="text-sm font-semibold text-indigo-900">0.00 Ton</span>
                                        </td>
                                        <td class="px-4 py-3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Hidden fields for backward compatibility -->
                        <input type="hidden" name="panjang" id="hiddenPanjang">
                        <input type="hidden" name="lebar" id="hiddenLebar">
                        <input type="hidden" name="tinggi" id="hiddenTinggi">
                        <input type="hidden" name="meter_kubik" id="hiddenMeterKubik">
                        <input type="hidden" name="tonase" id="hiddenTonase">

                        <p class="mt-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Volume otomatis dihitung dari Panjang × Lebar × Tinggi. Klik "Tambah Item" untuk menambah dimensi barang yang berbeda.
                        </p>
                    </div>

                    <!-- Baris 4: Keterangan -->
                    <div>
                        <label for="keterangan_barang" class="block text-sm font-medium text-gray-700 mb-1">
                            Keterangan Barang
                        </label>
                        <textarea name="keterangan_barang" id="keterangan_barang" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Deskripsi detail barang (opsional)">{{ old('keterangan_barang') }}</textarea>
                    </div>
                </div>

                <!-- Informasi Supir dan Kenek -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Supir dan Kenek</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">
                                Supir <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <!-- Hidden input for actual value -->
                                <input type="hidden" name="supir" id="supir" value="{{ old('supir', 'Supir Customer') }}">

                                <!-- Search input -->
                                <input type="text" id="supirSearch"
                                       placeholder="Cari supir atau ketik manual..."
                                       value="{{ old('supir', 'Supir Customer') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="supirDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <div class="text-xs text-gray-500 mb-2">Pilih dari Master Karyawan:</div>
                                    </div>
                                    @foreach($supirs as $supir_data)
                                        @php
                                            $nama_display = $supir_data->nama_lengkap ?: $supir_data->nama_panggilan ?: 'Nama tidak tersedia';
                                        @endphp
                                        <div class="supir-option px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                                             data-value="{{ $nama_display }}"
                                             data-text="{{ $nama_display }}"
                                             data-plat="{{ $supir_data->plat ?? '' }}">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-sm">{{ $nama_display }}</span>
                                                @if($supir_data->plat)
                                                    <span class="text-xs text-gray-500">Plat: {{ $supir_data->plat }}</span>
                                                @else
                                                    <span class="text-xs text-gray-500">ID: {{ $supir_data->id }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="p-2 border-t border-gray-200">
                                        <div class="text-xs text-gray-500">Atau ketik manual untuk supir customer</div>
                                    </div>
                                </div>
                            </div>
                            @error('supir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kenek" class="block text-sm font-medium text-gray-700 mb-1">
                                Kenek
                            </label>
                            <div class="relative">
                                <!-- Hidden input for actual value -->
                                <input type="hidden" name="kenek" id="kenek" value="{{ old('kenek', 'Kenek Customer') }}">

                                <!-- Search input -->
                                <input type="text" id="kenekSearch"
                                       placeholder="Cari kenek atau ketik manual..."
                                       value="{{ old('kenek', 'Kenek Customer') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('kenek') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="kenekDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <div class="text-xs text-gray-500 mb-2">Pilih dari Master Karyawan:</div>
                                    </div>
                                    @foreach($kranis as $kenek_data)
                                        @php
                                            $nama_display = $kenek_data->nama_lengkap ?: $kenek_data->nama_panggilan ?: 'Nama tidak tersedia';
                                        @endphp
                                        <div class="kenek-option px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                                             data-value="{{ $nama_display }}"
                                             data-text="{{ $nama_display }}">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-sm">{{ $nama_display }}</span>
                                                <span class="text-xs text-gray-500">Divisi: {{ $kenek_data->divisi }} | ID: {{ $kenek_data->id }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="p-2 border-t border-gray-200">
                                        <div class="text-xs text-gray-500">Atau ketik manual untuk kenek customer</div>
                                    </div>
                                </div>
                            </div>
                            @error('kenek')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Plat Kendaraan
                            </label>
                            <input type="text" name="no_plat" id="no_plat" value="{{ old('no_plat') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror"
                                   placeholder="Nomor plat kendaraan">
                            @error('no_plat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Otomatis terisi jika memilih supir dari master karyawan
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tujuan dan Transportasi -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tujuan dan Transportasi</h3>
                    
                    <!-- Baris 1: Tujuan Pengiriman -->
                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <div>
                            <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Pengiriman <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="tujuan_pengiriman" id="tujuan_pengiriman" class="hidden @error('tujuan_pengiriman') border-red-500 @enderror" required>
                                    <option value="">Pilih Tujuan Pengiriman</option>
                                    @foreach($tujuan_kirims as $tujuan)
                                        <option value="{{ $tujuan->nama_tujuan }}" {{ old('tujuan_pengiriman') == $tujuan->nama_tujuan ? 'selected' : '' }}>
                                            {{ $tujuan->nama_tujuan }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Search input -->
                                <input type="text" id="tujuanPengirimanSearch"
                                       placeholder="Cari atau pilih tujuan pengiriman..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengiriman') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="tujuanPengirimanDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('master.tujuan-kirim.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Tujuan Baru
                                        </a>
                                    </div>
                                    @if(isset($tujuan_kirims) && $tujuan_kirims->count() > 0)
                                        @foreach($tujuan_kirims as $tujuan)
                                            <div class="tujuan-pengiriman-option px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100"
                                                 data-value="{{ $tujuan->nama_tujuan }}"
                                                 data-text="{{ $tujuan->nama_tujuan }}">
                                                <div class="flex flex-col">
                                                    <span class="font-medium">{{ $tujuan->nama_tujuan }}</span>
                                                    @if(isset($tujuan->catatan) && $tujuan->catatan)
                                                        <span class="text-xs text-gray-500">{{ $tujuan->catatan }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="px-3 py-2 text-sm text-gray-500">
                                            Tidak ada data tujuan pengiriman
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('tujuan_pengiriman')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 2: Informasi Kontainer -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe Kontainer
                            </label>
                            <select name="tipe_kontainer" id="tipe_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tipe_kontainer') border-red-500 @enderror"
                                    onchange="handleTipeKontainerChange()">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="fcl" {{ (old('tipe_kontainer', $tipe ?? '') == 'fcl') ? 'selected' : '' }}>FCL</option>
                                <option value="lcl" {{ (old('tipe_kontainer', $tipe ?? '') == 'lcl') ? 'selected' : '' }}>LCL</option>
                                <option value="cargo" {{ (old('tipe_kontainer', $tipe ?? '') == 'cargo') ? 'selected' : '' }}>Cargo</option>
                            </select>
                            @error('tipe_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="size_kontainer_field">
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Size Kontainer
                            </label>
                            <select name="size_kontainer" id="size_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('size_kontainer') border-red-500 @enderror">
                                <option value="">-- Pilih Size --</option>
                                <option value="20 ft" {{ old('size_kontainer') == '20 ft' ? 'selected' : '' }}>20 ft</option>
                                <option value="40 ft" {{ old('size_kontainer') == '40 ft' ? 'selected' : '' }}>40 ft</option>
                                <option value="40 HC" {{ old('size_kontainer') == '40 HC' ? 'selected' : '' }}>40 HC (High Cube)</option>
                                <option value="45 ft" {{ old('size_kontainer') == '45 ft' ? 'selected' : '' }}>45 ft</option>
                                <option value="53 ft" {{ old('size_kontainer') == '53 ft' ? 'selected' : '' }}>53 ft</option>
                                <option value="other" {{ old('size_kontainer') == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="no_kontainer_field">
                            <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Kontainer
                            </label>
                            <input type="text" name="no_kontainer" id="no_kontainer" value="{{ old('no_kontainer') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_kontainer') border-red-500 @enderror"
                                   placeholder="Nomor kontainer">
                            @error('no_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="no_seal_field">
                            <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Seal
                            </label>
                            <input type="text" name="no_seal" id="no_seal" value="{{ old('no_seal') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_seal') border-red-500 @enderror"
                                   placeholder="Nomor seal">
                            @error('no_seal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 3: Tanggal Seal -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div id="tanggal_seal_field">
                            <label for="tanggal_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Seal
                            </label>
                            <input type="date" name="tanggal_seal" id="tanggal_seal" value="{{ old('tanggal_seal') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_seal') border-red-500 @enderror">
                            @error('tanggal_seal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan</h3>
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan Tambahan
                        </label>
                        <textarea name="catatan" id="catatan" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Catatan atau informasi tambahan (opsional)">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Tanda Terima
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

        // Initialize term dropdown
        initializeTermDropdown();

        // Initialize tujuan pengiriman dropdown
        initializeTujuanPengirimanDropdown();

        // Initialize supir dropdown
        initializeSupirDropdown();

        // Initialize kenek dropdown
        initializeKenekDropdown();

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
                       class="dimensi-panjang w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="0"
                       min="0"
                       step="0.01"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][lebar]"
                       class="dimensi-lebar w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="0"
                       min="0"
                       step="0.01"
                       onchange="calculateItemVolume(this)">
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <input type="number"
                       name="dimensi_items[${dimensiItemIndex}][tinggi]"
                       class="dimensi-tinggi w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
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
                       class="dimensi-tonase w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
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
        // Use first item's values or totals
        const firstRow = document.querySelector('#dimensiTableBody .dimensi-item');
        if (firstRow) {
            document.getElementById('hiddenPanjang').value = firstRow.querySelector('.dimensi-panjang').value || '';
            document.getElementById('hiddenLebar').value = firstRow.querySelector('.dimensi-lebar').value || '';
            document.getElementById('hiddenTinggi').value = firstRow.querySelector('.dimensi-tinggi').value || '';
        }
        document.getElementById('hiddenMeterKubik').value = totalVolume > 0 ? totalVolume.toFixed(6) : '';
        document.getElementById('hiddenTonase').value = totalTonase > 0 ? totalTonase.toFixed(2) : '';
    }

    // Legacy function for backward compatibility
    function calculateMeterKubik() {
        calculateAllVolumesAndTotals();
    }

    function handleTipeKontainerChange() {
        const tipeKontainer = document.getElementById('tipe_kontainer').value;
        const sizeKontainerField = document.getElementById('size_kontainer_field');
        const noKontainerField = document.getElementById('no_kontainer_field');
        const noSealField = document.getElementById('no_seal_field');
        const tanggalSealField = document.getElementById('tanggal_seal_field');
        
        if (tipeKontainer === 'cargo') {
            // Hide kontainer fields for cargo
            sizeKontainerField.style.display = 'none';
            noKontainerField.style.display = 'none';
            noSealField.style.display = 'none';
            tanggalSealField.style.display = 'none';
            // Clear kontainer fields when cargo is selected
            document.getElementById('no_kontainer').value = '';
            document.getElementById('size_kontainer').value = '';
            document.getElementById('no_seal').value = '';
            document.getElementById('tanggal_seal').value = '';
        } else {
            // Show kontainer fields for FCL and LCL
            sizeKontainerField.style.display = 'block';
            noKontainerField.style.display = 'block';
            noSealField.style.display = 'block';
            tanggalSealField.style.display = 'block';
        }
    }

    // Call on page load to handle old values
    document.addEventListener('DOMContentLoaded', function() {
        handleTipeKontainerChange();
    });

    function initializeTermDropdown() {
        const searchInput = document.getElementById('termSearch');
        const dropdown = document.getElementById('termDropdown');
        const hiddenSelect = document.getElementById('term_id');
        const options = document.querySelectorAll('.term-option');

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

                // Set the hidden select value
                hiddenSelect.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#termSearch') && !e.target.closest('#termDropdown')) {
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
            searchInput.value = selectedOption.textContent;
        }
    }

    function initializeTujuanPengirimanDropdown() {
        const searchInput = document.getElementById('tujuanPengirimanSearch');
        const dropdown = document.getElementById('tujuanPengirimanDropdown');
        const hiddenSelect = document.getElementById('tujuan_pengiriman');
        const options = document.querySelectorAll('.tujuan-pengiriman-option');

        console.log('Initializing Tujuan Pengiriman Dropdown');
        console.log('Search Input:', searchInput);
        console.log('Dropdown:', dropdown);
        console.log('Hidden Select:', hiddenSelect);
        console.log('Options found:', options.length);

        if (!searchInput || !dropdown || !hiddenSelect) {
            console.error('Required elements not found for tujuan pengiriman dropdown');
            return;
        }

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            console.log('Search input focused, showing dropdown');
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
        options.forEach((option, index) => {
            console.log(`Setting up click handler for tujuan option ${index}:`, option.textContent || option.innerHTML);
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                console.log('Tujuan option clicked:', { value, text });

                // Set the hidden select value
                hiddenSelect.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');

                console.log('Tujuan values set:', { hiddenValue: hiddenSelect.value, searchValue: searchInput.value });
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#tujuanPengirimanSearch') && !e.target.closest('#tujuanPengirimanDropdown')) {
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
            searchInput.value = selectedOption.textContent;
        }
    }

    function initializeSupirDropdown() {
        const searchInput = document.getElementById('supirSearch');
        const dropdown = document.getElementById('supirDropdown');
        const hiddenInput = document.getElementById('supir');
        const platInput = document.getElementById('no_plat');
        const options = document.querySelectorAll('.supir-option');

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Update hidden input when typing manually
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            hiddenInput.value = this.value; // Always update hidden input

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
                const plat = this.getAttribute('data-plat');

                // Set the hidden input value
                hiddenInput.value = value;

                // Update search input
                searchInput.value = text;

                // Auto-fill plat if available
                if (plat) {
                    platInput.value = plat;
                }

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#supirSearch') && !e.target.closest('#supirDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });
    }

    function initializeKenekDropdown() {
        const searchInput = document.getElementById('kenekSearch');
        const dropdown = document.getElementById('kenekDropdown');
        const hiddenInput = document.getElementById('kenek');
        const options = document.querySelectorAll('.kenek-option');

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Update hidden input when typing manually
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            hiddenInput.value = this.value; // Always update hidden input

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

                // Set the hidden input value
                hiddenInput.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#kenekSearch') && !e.target.closest('#kenekDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });
    }
</script>
@endpush
