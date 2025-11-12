@extends('layouts.app')

@section('content')
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Order Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Order</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order ID</label>
                    <input type="number"
                           name="order_id"
                           value="{{ old('order_id', $suratJalan->order_id) }}"
                           placeholder="ID Order"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('order_id') border-red-500 @enderror">
                    @error('order_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Basic Information -->
                <div class="md:col-span-2 mt-4">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan</label>
                    <select name="kegiatan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('kegiatan') border-red-500 @enderror">
                        <option value="">Pilih Kegiatan</option>
                        <option value="ANTAR ISI" {{ old('kegiatan', $suratJalan->kegiatan) == 'ANTAR ISI' ? 'selected' : '' }}>ANTAR ISI</option>
                        <option value="ANTAR KOSONG" {{ old('kegiatan', $suratJalan->kegiatan) == 'ANTAR KOSONG' ? 'selected' : '' }}>ANTAR KOSONG</option>
                        <option value="AMBIL ISI" {{ old('kegiatan', $suratJalan->kegiatan) == 'AMBIL ISI' ? 'selected' : '' }}>AMBIL ISI</option>
                        <option value="AMBIL KOSONG" {{ old('kegiatan', $suratJalan->kegiatan) == 'AMBIL KOSONG' ? 'selected' : '' }}>AMBIL KOSONG</option>
                    </select>
                    @error('kegiatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rest of form fields similar to create.blade.php but with old values from $suratJalan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                    <input type="text"
                           name="pengirim"
                           value="{{ old('pengirim', $suratJalan->pengirim) }}"
                           placeholder="Nama pengirim"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('pengirim') border-red-500 @enderror">
                    @error('pengirim')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input type="text"
                           name="telp"
                           value="{{ old('telp', $suratJalan->telp) }}"
                           placeholder="Nomor telepon"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('telp') border-red-500 @enderror">
                    @error('telp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat"
                              rows="3"
                              placeholder="Alamat lengkap pengirim"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $suratJalan->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barang Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Barang</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <input type="text"
                           name="jenis_barang"
                           value="{{ old('jenis_barang', $suratJalan->jenis_barang) }}"
                           placeholder="Jenis/nama barang"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jenis_barang') border-red-500 @enderror">
                    @error('jenis_barang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <input type="text"
                           name="tujuan_pengambilan"
                           value="{{ old('tujuan_pengambilan', $suratJalan->tujuan_pengambilan) }}"
                           placeholder="Tujuan pengambilan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengambilan') border-red-500 @enderror">
                    @error('tujuan_pengambilan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text"
                           name="tujuan_pengiriman"
                           value="{{ old('tujuan_pengiriman', $suratJalan->tujuan_pengiriman) }}"
                           placeholder="Tujuan pengiriman"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengiriman') border-red-500 @enderror">
                    @error('tujuan_pengiriman')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tipe_kontainer') border-red-500 @enderror">
                    @error('tipe_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                    <select name="size"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('size') border-red-500 @enderror">
                        <option value="">Pilih Size</option>
                        <option value="20" {{ old('size', $suratJalan->size) == '20' ? 'selected' : '' }}>20ft</option>
                        <option value="40" {{ old('size', $suratJalan->size) == '40' ? 'selected' : '' }}>40ft</option>
                    </select>
                    @error('size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    <input type="text"
                           name="no_kontainer"
                           value="{{ old('no_kontainer', $suratJalan->no_kontainer) }}"
                           placeholder="Nomor kontainer"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_kontainer') border-red-500 @enderror">
                    @error('no_kontainer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

                <!-- Kemasan Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kemasan</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Karton</label>
                    <select name="karton"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('karton') border-red-500 @enderror">
                        <option value="">Pilih Karton</option>
                        <option value="1" {{ old('karton', $suratJalan->karton) == '1' ? 'selected' : '' }}>Ada</option>
                        <option value="0" {{ old('karton', $suratJalan->karton) == '0' ? 'selected' : '' }}>Tidak Ada</option>
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
                        <option value="1" {{ old('plastik', $suratJalan->plastik) == '1' ? 'selected' : '' }}>Ada</option>
                        <option value="0" {{ old('plastik', $suratJalan->plastik) == '0' ? 'selected' : '' }}>Tidak Ada</option>
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
                        <option value="1" {{ old('terpal', $suratJalan->terpal) == '1' ? 'selected' : '' }}>Ada</option>
                        <option value="0" {{ old('terpal', $suratJalan->terpal) == '0' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                    @error('terpal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transportasi Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Transportasi</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                    <input type="text"
                           name="supir"
                           value="{{ old('supir', $suratJalan->supir) }}"
                           placeholder="Nama supir utama"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">
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
                           value="{{ old('no_plat', $suratJalan->no_plat) }}"
                           placeholder="Nomor plat kendaraan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror">
                    @error('no_plat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <input type="number"
                           name="uang_jalan"
                           value="{{ old('uang_jalan', $suratJalan->uang_jalan) }}"
                           step="1000"
                           min="0"
                           placeholder="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('uang_jalan') border-red-500 @enderror">
                    @error('uang_jalan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Uang jalan untuk supir dan kenek</p>
                </div>



                <!-- Schedule Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Waktu</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Muat</label>
                    <input type="date"
                           name="tanggal_muat"
                           value="{{ old('tanggal_muat', $suratJalan->tanggal_muat?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_muat') border-red-500 @enderror">
                    @error('tanggal_muat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Berangkat</label>
                    <input type="datetime-local"
                           name="waktu_berangkat"
                           value="{{ old('waktu_berangkat', $suratJalan->waktu_berangkat?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('waktu_berangkat') border-red-500 @enderror">
                    @error('waktu_berangkat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <input type="text"
                           name="term"
                           value="{{ old('term', $suratJalan->term) }}"
                           placeholder="Term/syarat pembayaran"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('term') border-red-500 @enderror">
                    @error('term')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rit</label>
                    <select name="rit"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('rit') border-red-500 @enderror">
                        <option value="">Pilih Rit</option>
                        <option value="menggunakan_rit" {{ old('rit', $suratJalan->rit) == 'menggunakan_rit' ? 'selected' : '' }}>Menggunakan Rit</option>
                        <option value="tidak_menggunakan_rit" {{ old('rit', $suratJalan->rit) == 'tidak_menggunakan_rit' ? 'selected' : '' }}>Tidak Menggunakan Rit</option>
                    </select>
                    @error('rit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Pemesanan</label>
                    <input type="text"
                           name="no_pemesanan"
                           value="{{ old('no_pemesanan', $suratJalan->no_pemesanan) }}"
                           placeholder="Nomor pemesanan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_pemesanan') border-red-500 @enderror">
                    @error('no_pemesanan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Financial Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Keuangan</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                    <select name="status_pembayaran"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('status_pembayaran') border-red-500 @enderror">
                        <option value="belum_dibayar" {{ old('status_pembayaran', $suratJalan->status_pembayaran) == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="sudah_dibayar" {{ old('status_pembayaran', $suratJalan->status_pembayaran) == 'sudah_dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                    @error('status_pembayaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Tarif</label>
                    <input type="number"
                           name="total_tarif"
                           value="{{ old('total_tarif', $suratJalan->total_tarif) }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('total_tarif') border-red-500 @enderror">
                    @error('total_tarif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Terbayar</label>
                    <input type="number"
                           name="jumlah_terbayar"
                           value="{{ old('jumlah_terbayar', $suratJalan->jumlah_terbayar) }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jumlah_terbayar') border-red-500 @enderror">
                    @error('jumlah_terbayar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran Uang Rit</label>
                    <select name="status_pembayaran_uang_rit"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('status_pembayaran_uang_rit') border-red-500 @enderror">
                        <option value="belum_dibayar" {{ old('status_pembayaran_uang_rit', $suratJalan->status_pembayaran_uang_rit) == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="proses_pranota" {{ old('status_pembayaran_uang_rit', $suratJalan->status_pembayaran_uang_rit) == 'proses_pranota' ? 'selected' : '' }}>Proses Pranota</option>
                        <option value="sudah_masuk_pranota" {{ old('status_pembayaran_uang_rit', $suratJalan->status_pembayaran_uang_rit) == 'sudah_masuk_pranota' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                        <option value="pranota_submitted" {{ old('status_pembayaran_uang_rit', $suratJalan->status_pembayaran_uang_rit) == 'pranota_submitted' ? 'selected' : '' }}>Pranota Submitted</option>
                        <option value="pranota_approved" {{ old('status_pembayaran_uang_rit', $suratJalan->status_pembayaran_uang_rit) == 'pranota_approved' ? 'selected' : '' }}>Pranota Approved</option>
                        <option value="dibayar" {{ old('status_pembayaran_uang_rit', $suratJalan->status_pembayaran_uang_rit) == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                    </select>
                    @error('status_pembayaran_uang_rit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran Uang Rit Kenek</label>
                    <select name="status_pembayaran_uang_rit_kenek"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('status_pembayaran_uang_rit_kenek') border-red-500 @enderror">
                        <option value="belum_dibayar" {{ old('status_pembayaran_uang_rit_kenek', $suratJalan->status_pembayaran_uang_rit_kenek) == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="proses_pranota" {{ old('status_pembayaran_uang_rit_kenek', $suratJalan->status_pembayaran_uang_rit_kenek) == 'proses_pranota' ? 'selected' : '' }}>Proses Pranota</option>
                        <option value="sudah_masuk_pranota" {{ old('status_pembayaran_uang_rit_kenek', $suratJalan->status_pembayaran_uang_rit_kenek) == 'sudah_masuk_pranota' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                        <option value="pranota_submitted" {{ old('status_pembayaran_uang_rit_kenek', $suratJalan->status_pembayaran_uang_rit_kenek) == 'pranota_submitted' ? 'selected' : '' }}>Pranota Submitted</option>
                        <option value="pranota_approved" {{ old('status_pembayaran_uang_rit_kenek', $suratJalan->status_pembayaran_uang_rit_kenek) == 'pranota_approved' ? 'selected' : '' }}>Pranota Approved</option>
                        <option value="dibayar" {{ old('status_pembayaran_uang_rit_kenek', $suratJalan->status_pembayaran_uang_rit_kenek) == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                    </select>
                    @error('status_pembayaran_uang_rit_kenek')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Berangkat</label>
                    <input type="time"
                           name="jam_berangkat"
                           value="{{ old('jam_berangkat', $suratJalan->jam_berangkat) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('jam_berangkat') border-red-500 @enderror">
                    @error('jam_berangkat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Images -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Gambar Checkpoint</h3>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Checkpoint</label>
                    <input type="file"
                           name="gambar_checkpoint"
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('gambar_checkpoint') border-red-500 @enderror">
                    @if($suratJalan->gambar_checkpoint)
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">File saat ini: {{ basename($suratJalan->gambar_checkpoint) }}</p>
                        </div>
                    @endif
                    @error('gambar_checkpoint')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- System Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Sistem</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Input By</label>
                    <input type="text"
                           name="input_by"
                           value="{{ old('input_by', $suratJalan->input_by) }}"
                           placeholder="Diinput oleh"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('input_by') border-red-500 @enderror">
                    @error('input_by')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Input Date</label>
                    <input type="datetime-local"
                           name="input_date"
                           value="{{ old('input_date', $suratJalan->input_date?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('input_date') border-red-500 @enderror">
                    @error('input_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

// Check kontainer rules on page load
document.addEventListener('DOMContentLoaded', function() {
    updateKontainerNote();
});


</script>
@endsection
