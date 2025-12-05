@extends('layouts.app')

@section('title', 'Edit Surat Jalan Bongkaran')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Edit Surat Jalan Bongkaran</h1>
                <p class="text-xs text-gray-600 mt-1">Edit data surat jalan bongkaran</p>
            </div>
            <a href="{{ route('surat-jalan-bongkaran.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('surat-jalan-bongkaran.update', $suratJalanBongkaran) }}" method="POST" class="p-3">
            @csrf
            @method('PUT')

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex">
                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="font-medium">Terdapat kesalahan pada form:</h4>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Basic Information -->
                <div class="md:col-span-3">
                    <h3 class="text-base font-medium text-gray-900 mb-2">Informasi Dasar</h3>
                </div>



                <!-- Nomor Surat Jalan -->
                <div>
                    <label for="nomor_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Surat Jalan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomor_surat_jalan" id="nomor_surat_jalan" required
                           value="{{ old('nomor_surat_jalan', $suratJalanBongkaran->nomor_surat_jalan) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_surat_jalan') border-red-300 @enderror"
                           placeholder="Masukkan nomor surat jalan">
                    @error('nomor_surat_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Surat Jalan -->
                <div>
                    <label for="tanggal_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Surat Jalan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_surat_jalan" id="tanggal_surat_jalan" required
                           value="{{ old('tanggal_surat_jalan', $suratJalanBongkaran->tanggal_surat_jalan ? $suratJalanBongkaran->tanggal_surat_jalan->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_surat_jalan') border-red-300 @enderror">
                    @error('tanggal_surat_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Term -->
                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select name="term" id="term"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('term') border-red-300 @enderror">
                        <option value="">Pilih Term</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->kode }}" {{ old('term', $suratJalanBongkaran->term) == $term->kode ? 'selected' : '' }}>
                                {{ $term->kode }} - {{ $term->nama_status }}
                            </option>
                        @endforeach
                    </select>
                    @error('term')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Aktifitas -->
                <div>
                    <label for="aktifitas" class="block text-sm font-medium text-gray-700 mb-1">Aktifitas</label>
                    <select name="aktifitas" id="aktifitas"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('aktifitas') border-red-300 @enderror">
                        <option value="">Pilih aktifitas</option>
                        @foreach($masterKegiatans as $kegiatan)
                            <option value="{{ $kegiatan->nama_kegiatan }}" {{ old('aktifitas', $suratJalanBongkaran->aktifitas) == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                {{ $kegiatan->nama_kegiatan }}
                            </option>
                        @endforeach
                    </select>
                    @error('aktifitas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Pengiriman -->
                <div class="md:col-span-3 mt-3">
                    <h3 class="text-base font-medium text-gray-900 mb-2">Informasi Pengiriman</h3>
                </div>

                <!-- Pengirim -->
                <div>
                    <label for="pengirim" class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                    <input type="text" name="pengirim" id="pengirim"
                           value="{{ old('pengirim', $suratJalanBongkaran->pengirim) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pengirim') border-red-300 @enderror"
                           placeholder="Masukkan nama pengirim">
                    @error('pengirim')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Barang -->
                <div>
                    <label for="jenis_barang" class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <input type="text" name="jenis_barang" id="jenis_barang"
                           value="{{ old('jenis_barang', $suratJalanBongkaran->jenis_barang) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_barang') border-red-300 @enderror"
                           placeholder="Masukkan jenis barang">
                    @error('jenis_barang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tujuan Alamat -->
                <div>
                    <label for="tujuan_alamat" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Alamat</label>
                    <input type="text" name="tujuan_alamat" id="tujuan_alamat"
                           value="{{ old('tujuan_alamat', $suratJalanBongkaran->tujuan_alamat) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tujuan_alamat') border-red-300 @enderror"
                           placeholder="Masukkan tujuan alamat">
                    @error('tujuan_alamat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tujuan Pengambilan -->
                <div>
                    <label for="tujuan_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <select name="tujuan_pengambilan" id="tujuan_pengambilan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tujuan_pengambilan') border-red-300 @enderror">
                        <option value="">Pilih tujuan pengambilan</option>
                        @foreach($tujuanKegiatanUtamas as $tujuan)
                            <option value="{{ $tujuan->ke }}" {{ old('tujuan_pengambilan', $suratJalanBongkaran->tujuan_pengambilan) == $tujuan->ke ? 'selected' : '' }}>
                                {{ $tujuan->ke }}
                            </option>
                        @endforeach
                    </select>
                    @error('tujuan_pengambilan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tujuan Pengiriman -->
                <div>
                    <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text" name="tujuan_pengiriman" id="tujuan_pengiriman"
                           value="{{ old('tujuan_pengiriman', $suratJalanBongkaran->tujuan_pengiriman) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tujuan_pengiriman') border-red-300 @enderror"
                           placeholder="Masukkan tujuan pengiriman">
                    @error('tujuan_pengiriman')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Pengiriman -->
                <div>
                    <label for="jenis_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengiriman</label>
                    <select name="jenis_pengiriman" id="jenis_pengiriman"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_pengiriman') border-red-300 @enderror">
                        <option value="">Pilih jenis pengiriman</option>
                        <option value="FCL" {{ old('jenis_pengiriman', $suratJalanBongkaran->jenis_pengiriman) == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ old('jenis_pengiriman', $suratJalanBongkaran->jenis_pengiriman) == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="Cargo" {{ old('jenis_pengiriman', $suratJalanBongkaran->jenis_pengiriman) == 'Cargo' ? 'selected' : '' }}>Cargo</option>
                    </select>
                    @error('jenis_pengiriman')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Ambil Barang -->
                <div>
                    <label for="tanggal_ambil_barang" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ambil Barang</label>
                    <input type="date" name="tanggal_ambil_barang" id="tanggal_ambil_barang"
                           value="{{ old('tanggal_ambil_barang', $suratJalanBongkaran->tanggal_ambil_barang ? $suratJalanBongkaran->tanggal_ambil_barang->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_ambil_barang') border-red-300 @enderror">
                    @error('tanggal_ambil_barang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Personal -->
                <div class="md:col-span-3 mt-3">
                    <h3 class="text-base font-medium text-gray-900 mb-2">Informasi Personal</h3>
                </div>

                <!-- Supir -->
                <div>
                    <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                    <select name="supir" id="supir"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('supir') border-red-300 @enderror">
                        <option value="">Pilih Supir</option>
                        @foreach($karyawanSupirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" 
                                    data-plat="{{ $supir->plat }}"
                                    {{ old('supir', $suratJalanBongkaran->supir) == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_panggilan ?? $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-blue-600">
                        Ketik nama supir untuk mencari dengan cepat
                    </p>
                    @error('supir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Plat -->
                <div>
                    <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-1">No Plat</label>
                    <input type="text" name="no_plat" id="no_plat"
                           value="{{ old('no_plat', $suratJalanBongkaran->no_plat) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_plat') border-red-300 @enderror"
                           placeholder="Masukkan nomor plat">
                    @error('no_plat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kenek -->
                <div>
                    <label for="kenek" class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <select name="kenek" id="kenek"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kenek') border-red-300 @enderror">
                        <option value="">Pilih Kenek</option>
                        @foreach($karyawanKranis as $krani)
                            <option value="{{ $krani->nama_lengkap }}" {{ old('kenek', $suratJalanBongkaran->kenek) == $krani->nama_lengkap ? 'selected' : '' }}>
                                {{ $krani->nama_panggilan ?? $krani->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-blue-600">
                        Ketik nama kenek untuk mencari dengan cepat
                    </p>
                    @error('kenek')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Krani -->
                <div>
                    <label for="krani" class="block text-sm font-medium text-gray-700 mb-1">Krani</label>
                    <select name="krani" id="krani"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('krani') border-red-300 @enderror">
                        <option value="">Pilih Krani</option>
                        @foreach($karyawanKranis as $krani)
                            <option value="{{ $krani->nama_lengkap }}" {{ old('krani', $suratJalanBongkaran->krani) == $krani->nama_lengkap ? 'selected' : '' }}>
                                {{ $krani->nama_panggilan ?? $krani->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-blue-600">
                        Ketik nama krani untuk mencari dengan cepat
                    </p>
                    @error('krani')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Container -->
                <div class="md:col-span-3 mt-3">
                    <h3 class="text-base font-medium text-gray-900 mb-2">Informasi Container</h3>
                </div>

                <!-- No Kontainer -->
                <div>
                    <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">No Kontainer</label>
                    <input type="text" name="no_kontainer" id="no_kontainer"
                           value="{{ old('no_kontainer', $suratJalanBongkaran->no_kontainer) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_kontainer') border-red-300 @enderror"
                           placeholder="Masukkan nomor kontainer">
                    @error('no_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Seal -->
                <div>
                    <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-1">No Seal</label>
                    <input type="text" name="no_seal" id="no_seal"
                           value="{{ old('no_seal', $suratJalanBongkaran->no_seal) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_seal') border-red-300 @enderror"
                           placeholder="Masukkan nomor seal">
                    @error('no_seal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor BL -->
                <div>
                    <label for="no_bl" class="block text-sm font-medium text-gray-700 mb-1">Nomor BL</label>
                    <input type="text" name="no_bl" id="no_bl"
                           value="{{ old('no_bl', $suratJalanBongkaran->no_bl) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_bl') border-red-300 @enderror"
                           placeholder="Masukkan nomor BL">
                    @error('no_bl')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Size Kontainer -->
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer</label>
                    <input type="text" name="size" id="size"
                           value="{{ old('size', $suratJalanBongkaran->size) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('size') border-red-300 @enderror"
                           placeholder="Masukkan ukuran kontainer">
                    @error('size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi Packaging -->
                <div class="md:col-span-3 mt-3">
                    <h3 class="text-base font-medium text-gray-900 mb-2">Informasi Packaging</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Karton -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Karton</label>
                            <div class="flex space-x-3">
                                <label class="flex items-center">
                                    <input type="radio" name="karton" value="ya" {{ old('karton', $suratJalanBongkaran->karton) == 'ya' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-1 text-sm text-gray-700">Ya</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="karton" value="tidak" {{ old('karton', $suratJalanBongkaran->karton ?? 'tidak') == 'tidak' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-1 text-sm text-gray-700">Tidak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Plastik -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Plastik</label>
                            <div class="flex space-x-3">
                                <label class="flex items-center">
                                    <input type="radio" name="plastik" value="ya" {{ old('plastik', $suratJalanBongkaran->plastik) == 'ya' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-1 text-sm text-gray-700">Ya</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="plastik" value="tidak" {{ old('plastik', $suratJalanBongkaran->plastik ?? 'tidak') == 'tidak' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-1 text-sm text-gray-700">Tidak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Terpal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terpal</label>
                            <div class="flex space-x-3">
                                <label class="flex items-center">
                                    <input type="radio" name="terpal" value="ya" {{ old('terpal', $suratJalanBongkaran->terpal) == 'ya' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-1 text-sm text-gray-700">Ya</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="terpal" value="tidak" {{ old('terpal', $suratJalanBongkaran->terpal ?? 'tidak') == 'tidak' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-1 text-sm text-gray-700">Tidak</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Keuangan & Tagihan -->
                <div class="md:col-span-3 mt-3">
                    <h3 class="text-base font-medium text-gray-900 mb-2">Informasi Keuangan & Tagihan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div class="space-y-3">
                            <!-- RIT -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">RIT</label>
                                <div class="flex space-x-3">
                                    <label class="flex items-center">
                                        <input type="radio" name="rit" value="menggunakan_rit" {{ old('rit', $suratJalanBongkaran->rit) == 'menggunakan_rit' ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-1 text-sm text-gray-700">Ya</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="rit" value="tidak_menggunakan_rit" {{ old('rit', $suratJalanBongkaran->rit) == 'tidak_menggunakan_rit' ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-1 text-sm text-gray-700">Tidak</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Uang Jalan Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                                <div class="flex space-x-3">
                                    <label class="flex items-center">
                                        <input type="radio" name="uang_jalan_type" value="full" {{ old('uang_jalan_type', $suratJalanBongkaran->uang_jalan_type) == 'full' ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-1 text-sm text-gray-700">Full</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="uang_jalan_type" value="setengah" {{ old('uang_jalan_type', $suratJalanBongkaran->uang_jalan_type) == 'setengah' ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-1 text-sm text-gray-700">Setengah</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-3">
                            <!-- Uang Jalan Nominal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal</label>
                                <input type="number" name="uang_jalan_nominal" id="uang_jalan_nominal"
                                       value="{{ old('uang_jalan_nominal', $suratJalanBongkaran->uang_jalan_nominal) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('uang_jalan_nominal') border-red-300 @enderror"
                                       placeholder="Nominal uang jalan" min="0" step="1000">
                            </div>

                            <!-- Tagihan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tagihan</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tagihan_ayp" value="1" {{ old('tagihan_ayp', $suratJalanBongkaran->tagihan_ayp) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="ml-1 text-sm text-gray-700">AYP</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tagihan_atb" value="1" {{ old('tagihan_atb', $suratJalanBongkaran->tagihan_atb) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="ml-1 text-sm text-gray-700">ATB</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tagihan_pb" value="1" {{ old('tagihan_pb', $suratJalanBongkaran->tagihan_pb) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="ml-1 text-sm text-gray-700">PB</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-4 pt-3 border-t border-gray-200">
                <a href="{{ route('surat-jalan-bongkaran.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Custom styling for select elements */
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tujuan kegiatan utama data for uang jalan calculation
    const tujuanKegiatanData = @json($tujuanKegiatanUtamas->keyBy('ke'));
    
    // Auto-fill plat nomor when supir is selected
    const supirSelect = document.getElementById('supir');
    const noPlatInput = document.getElementById('no_plat');
    
    if (supirSelect && noPlatInput) {
        supirSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const platNumber = selectedOption.getAttribute('data-plat');
            
            if (platNumber && platNumber.trim() !== '' && !noPlatInput.value) {
                noPlatInput.value = platNumber;
            }
        });
    }
    
    // Auto-calculate uang jalan based on tujuan pengambilan
    const tujuanPengambilanSelect = document.getElementById('tujuan_pengambilan');
    const uangJalanNominalInput = document.getElementById('uang_jalan_nominal');
    const sizeInput = document.querySelector('input[name="size"]');
    const uangJalanTypeRadios = document.querySelectorAll('input[name="uang_jalan_type"]');
    
    function calculateUangJalan() {
        const selectedTujuan = tujuanPengambilanSelect.value;
        const containerSize = sizeInput ? sizeInput.value : '';
        const uangJalanType = document.querySelector('input[name="uang_jalan_type"]:checked');
        
        if (selectedTujuan && tujuanKegiatanData[selectedTujuan]) {
            const tujuanData = tujuanKegiatanData[selectedTujuan];
            let uangJalan = 0;
            
            // Determine uang jalan based on container size
            if (containerSize === '20' || containerSize === '20ft') {
                uangJalan = tujuanData.uang_jalan_20ft || 0;
            } else if (containerSize === '40' || containerSize === '40ft' || containerSize === '40hc' || containerSize === '40 hc') {
                uangJalan = tujuanData.uang_jalan_40ft || 0;
            } else {
                // Default to 20ft if size is not clear
                uangJalan = tujuanData.uang_jalan_20ft || 0;
            }
            
            // Apply half calculation if "setengah" is selected
            if (uangJalanType && uangJalanType.value === 'setengah') {
                uangJalan = uangJalan / 2;
            }
            
            if (uangJalan > 0) {
                uangJalanNominalInput.value = Math.round(uangJalan);
            }
        }
    }
    
    if (tujuanPengambilanSelect && uangJalanNominalInput) {
        tujuanPengambilanSelect.addEventListener('change', calculateUangJalan);
        
        // Add event listeners to uang jalan type radio buttons
        uangJalanTypeRadios.forEach(radio => {
            radio.addEventListener('change', calculateUangJalan);
        });
        
        // Also trigger calculation on page load if values are pre-selected
        if (tujuanPengambilanSelect.value) {
            calculateUangJalan();
        }
    }
});
</script>
@endpush