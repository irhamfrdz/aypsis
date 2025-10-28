@extends('layouts.app')

@section('title', 'Edit Tanda Terima LCL')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Tanda Terima LCL</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $tandaTerima->nomor_tanda_terima }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('tanda-terima-lcl.show', $tandaTerima) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('tanda-terima-lcl.update', $tandaTerima) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Dasar</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-2">No. Tanda Terima *</label>
                                <input type="text" 
                                       id="nomor_tanda_terima" 
                                       name="nomor_tanda_terima" 
                                       value="{{ old('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('nomor_tanda_terima') border-red-500 @enderror"
                                       required>
                                @error('nomor_tanda_terima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="tanggal_tanda_terima" class="block text-sm font-medium text-gray-700 mb-2">Tanggal *</label>
                                <input type="date" 
                                       id="tanggal_tanda_terima" 
                                       name="tanggal_tanda_terima" 
                                       value="{{ old('tanggal_tanda_terima', $tandaTerima->tanggal_tanda_terima->format('Y-m-d')) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('tanggal_tanda_terima') border-red-500 @enderror"
                                       required>
                                @error('tanggal_tanda_terima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="kuantitas" class="block text-sm font-medium text-gray-700 mb-2">Kuantitas</label>
                                <input type="number" 
                                       id="kuantitas" 
                                       name="kuantitas" 
                                       value="{{ old('kuantitas', $tandaTerima->kuantitas) }}"
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('kuantitas') border-red-500 @enderror">
                                @error('kuantitas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="no_surat_jalan_customer" class="block text-sm font-medium text-gray-700 mb-2">No. Surat Jalan Customer</label>
                                <input type="text" 
                                       id="no_surat_jalan_customer" 
                                       name="no_surat_jalan_customer" 
                                       value="{{ old('no_surat_jalan_customer', $tandaTerima->no_surat_jalan_customer) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('no_surat_jalan_customer') border-red-500 @enderror">
                                @error('no_surat_jalan_customer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="term_id" class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                                <select id="term_id" 
                                        name="term_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('term_id') border-red-500 @enderror">
                                    <option value="">Pilih Term</option>
                                    @foreach(App\Models\Term::all() as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id', $tandaTerima->term_id) == $term->id ? 'selected' : '' }}>
                                            {{ $term->nama_status }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('term_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Penerima -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Penerima</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_penerima" class="block text-sm font-medium text-gray-700 mb-2">Nama Penerima *</label>
                                <input type="text" 
                                       id="nama_penerima" 
                                       name="nama_penerima" 
                                       value="{{ old('nama_penerima', $tandaTerima->nama_penerima) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('nama_penerima') border-red-500 @enderror"
                                       required>
                                @error('nama_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pic_penerima" class="block text-sm font-medium text-gray-700 mb-2">PIC Penerima</label>
                                <input type="text" 
                                       id="pic_penerima" 
                                       name="pic_penerima" 
                                       value="{{ old('pic_penerima', $tandaTerima->pic_penerima) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('pic_penerima') border-red-500 @enderror">
                                @error('pic_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="telepon_penerima" class="block text-sm font-medium text-gray-700 mb-2">Telepon Penerima</label>
                                <input type="text" 
                                       id="telepon_penerima" 
                                       name="telepon_penerima" 
                                       value="{{ old('telepon_penerima', $tandaTerima->telepon_penerima) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('telepon_penerima') border-red-500 @enderror">
                                @error('telepon_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-2">Alamat Penerima</label>
                                <textarea id="alamat_penerima" 
                                          name="alamat_penerima" 
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('alamat_penerima') border-red-500 @enderror">{{ old('alamat_penerima', $tandaTerima->alamat_penerima) }}</textarea>
                                @error('alamat_penerima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pengirim -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Pengirim</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_pengirim" class="block text-sm font-medium text-gray-700 mb-2">Nama Pengirim *</label>
                                <input type="text" 
                                       id="nama_pengirim" 
                                       name="nama_pengirim" 
                                       value="{{ old('nama_pengirim', $tandaTerima->nama_pengirim) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('nama_pengirim') border-red-500 @enderror"
                                       required>
                                @error('nama_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pic_pengirim" class="block text-sm font-medium text-gray-700 mb-2">PIC Pengirim</label>
                                <input type="text" 
                                       id="pic_pengirim" 
                                       name="pic_pengirim" 
                                       value="{{ old('pic_pengirim', $tandaTerima->pic_pengirim) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('pic_pengirim') border-red-500 @enderror">
                                @error('pic_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="telepon_pengirim" class="block text-sm font-medium text-gray-700 mb-2">Telepon Pengirim</label>
                                <input type="text" 
                                       id="telepon_pengirim" 
                                       name="telepon_pengirim" 
                                       value="{{ old('telepon_pengirim', $tandaTerima->telepon_pengirim) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('telepon_pengirim') border-red-500 @enderror">
                                @error('telepon_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-2">Alamat Pengirim</label>
                                <textarea id="alamat_pengirim" 
                                          name="alamat_pengirim" 
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('alamat_pengirim') border-red-500 @enderror">{{ old('alamat_pengirim', $tandaTerima->alamat_pengirim) }}</textarea>
                                @error('alamat_pengirim')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Barang -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Barang</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-2">Nama Barang *</label>
                                <input type="text" 
                                       id="nama_barang" 
                                       name="nama_barang" 
                                       value="{{ old('nama_barang', $tandaTerima->nama_barang) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('nama_barang') border-red-500 @enderror"
                                       required>
                                @error('nama_barang')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="jenis_barang_id" class="block text-sm font-medium text-gray-700 mb-2">Jenis Barang</label>
                                <select id="jenis_barang_id" 
                                        name="jenis_barang_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('jenis_barang_id') border-red-500 @enderror">
                                    <option value="">Pilih Jenis Barang</option>
                                    @foreach(App\Models\JenisBarang::all() as $jenisBarang)
                                        <option value="{{ $jenisBarang->id }}" {{ old('jenis_barang_id', $tandaTerima->jenis_barang_id) == $jenisBarang->id ? 'selected' : '' }}>
                                            {{ $jenisBarang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_barang_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="keterangan_barang" class="block text-sm font-medium text-gray-700 mb-2">Keterangan Barang</label>
                                <textarea id="keterangan_barang" 
                                          name="keterangan_barang" 
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('keterangan_barang') border-red-500 @enderror">{{ old('keterangan_barang', $tandaTerima->keterangan_barang) }}</textarea>
                                @error('keterangan_barang')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Kontainer -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Kontainer</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" 
                                               name="tipe_kontainer" 
                                               value="cargo" 
                                               {{ old('tipe_kontainer', $tandaTerima->tipe_kontainer) == 'cargo' ? 'checked' : '' }}
                                               class="form-radio h-4 w-4 text-blue-600" 
                                               onchange="toggleKontainerFields()">
                                        <span class="ml-2 text-sm text-gray-700">Cargo</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" 
                                               name="tipe_kontainer" 
                                               value="lcl" 
                                               {{ old('tipe_kontainer', $tandaTerima->tipe_kontainer) == 'lcl' || !$tandaTerima->tipe_kontainer ? 'checked' : '' }}
                                               class="form-radio h-4 w-4 text-blue-600" 
                                               onchange="toggleKontainerFields()">
                                        <span class="ml-2 text-sm text-gray-700">LCL</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="kontainer-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontainer</label>
                                <input type="text" 
                                       id="nomor_kontainer" 
                                       name="nomor_kontainer" 
                                       value="{{ old('nomor_kontainer', $tandaTerima->nomor_kontainer) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('nomor_kontainer') border-red-500 @enderror">
                                @error('nomor_kontainer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Size Kontainer</label>
                                <select id="size_kontainer" 
                                        name="size_kontainer" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('size_kontainer') border-red-500 @enderror">
                                    <option value="">Pilih Size</option>
                                    <option value="20ft" {{ old('size_kontainer', $tandaTerima->size_kontainer) == "20ft" ? 'selected' : '' }}>20 Feet</option>
                                    <option value="40ft" {{ old('size_kontainer', $tandaTerima->size_kontainer) == "40ft" ? 'selected' : '' }}>40 Feet</option>
                                    <option value="40hc" {{ old('size_kontainer', $tandaTerima->size_kontainer) == "40hc" ? 'selected' : '' }}>40 Feet High Cube</option>
                                    <option value="45ft" {{ old('size_kontainer', $tandaTerima->size_kontainer) == "45ft" ? 'selected' : '' }}>45 Feet</option>
                                </select>
                                @error('size_kontainer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pengiriman -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Pengiriman</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="supir" class="block text-sm font-medium text-gray-700 mb-2">Supir *</label>
                                <input type="text" 
                                       id="supir" 
                                       name="supir" 
                                       value="{{ old('supir', $tandaTerima->supir) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('supir') border-red-500 @enderror"
                                       required>
                                @error('supir')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-2">No. Plat *</label>
                                <input type="text" 
                                       id="no_plat" 
                                       name="no_plat" 
                                       value="{{ old('no_plat', $tandaTerima->no_plat) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('no_plat') border-red-500 @enderror"
                                       required>
                                @error('no_plat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="master_tujuan_kirim_id" class="block text-sm font-medium text-gray-700 mb-2">Tujuan Pengiriman</label>
                                <select id="master_tujuan_kirim_id" 
                                        name="master_tujuan_kirim_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('master_tujuan_kirim_id') border-red-500 @enderror">
                                    <option value="">Pilih Tujuan</option>
                                    @foreach(App\Models\MasterTujuanKirim::all() as $tujuan)
                                        <option value="{{ $tujuan->id }}" {{ old('master_tujuan_kirim_id', $tandaTerima->master_tujuan_kirim_id) == $tujuan->id ? 'selected' : '' }}>
                                            {{ $tujuan->nama_tujuan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('master_tujuan_kirim_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dimensi dan Volume -->
                @if($tandaTerima->items && $tandaTerima->items->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Dimensi dan Volume</h2>
                        </div>
                        <div class="p-6">
                            <div id="dimensi-container">
                                @foreach($tandaTerima->items as $index => $item)
                                    <div class="dimensi-row border border-gray-200 rounded-lg p-4 mb-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-md font-medium text-gray-700">Item {{ $index + 1 }}</h3>
                                            @if($loop->count > 1)
                                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeDimensiRow(this)">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Panjang (m)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][panjang]" 
                                                       value="{{ old('items.'.$index.'.panjang', $item->panjang) }}"
                                                       step="0.01" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                                       onchange="calculateVolume(this)">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (m)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][lebar]" 
                                                       value="{{ old('items.'.$index.'.lebar', $item->lebar) }}"
                                                       step="0.01" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                                       onchange="calculateVolume(this)">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (m)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][tinggi]" 
                                                       value="{{ old('items.'.$index.'.tinggi', $item->tinggi) }}"
                                                       step="0.01" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                                       onchange="calculateVolume(this)">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Volume (m³)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][meter_kubik]" 
                                                       value="{{ old('items.'.$index.'.meter_kubik', $item->meter_kubik) }}"
                                                       step="0.001" 
                                                       class="volume-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                                       readonly>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Berat (Ton)</label>
                                                <input type="number" 
                                                       name="items[{{ $index }}][tonase]" 
                                                       value="{{ old('items.'.$index.'.tonase', $item->tonase) }}"
                                                       step="0.01" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" 
                                    onclick="addDimensiRow()" 
                                    class="inline-flex items-center px-4 py-2 border border-green-300 rounded-md text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Item
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('tanda-terima-lcl.show', $tandaTerima) }}" 
                               class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleKontainerFields() {
    const cargo = document.querySelector('input[name="tipe_kontainer"]:checked').value === 'cargo';
    const kontainerFields = document.getElementById('kontainer-fields');
    
    if (cargo) {
        kontainerFields.style.display = 'none';
        // Clear kontainer fields when cargo is selected
        document.getElementById('nomor_kontainer').value = '';
        document.getElementById('size_kontainer').value = '';
    } else {
        kontainerFields.style.display = 'grid';
    }
}

function calculateVolume(element) {
    const row = element.closest('.dimensi-row');
    const panjang = parseFloat(row.querySelector('input[name*="[panjang]"]').value) || 0;
    const lebar = parseFloat(row.querySelector('input[name*="[lebar]"]').value) || 0;
    const tinggi = parseFloat(row.querySelector('input[name*="[tinggi]"]').value) || 0;
    
    // Calculate volume in cubic meters (m × m × m = m³)
    const volume = panjang * lebar * tinggi;
    
    const volumeInput = row.querySelector('.volume-input');
    volumeInput.value = formatVolumeForDatabase(volume);
}

// Formatting functions for input fields (clean whole numbers, smart decimal display)
function formatVolume(value) {
    if (!value || value === 0) return '';
    
    // Round to 3 decimal places
    const rounded = Math.round(value * 1000) / 1000;
    
    // Check if it's a whole number
    if (Number.isInteger(rounded)) {
        return rounded.toString(); // Show as "1000" not "1000.000"
    }
    
    return rounded.toFixed(3); // Show decimals when needed
}

function formatWeight(value) {
    if (!value || value === 0) return '';
    
    // Round to 3 decimal places
    const rounded = Math.round(value * 1000) / 1000;
    
    // Check if it's a whole number
    if (Number.isInteger(rounded)) {
        return rounded.toString(); // Show as "5" not "5.000"
    }
    
    return rounded.toFixed(3); // Show decimals when needed
}

// Formatting functions for display totals (with thousand separator)
function formatVolumeDisplay(value) {
    if (!value || value === 0) return '0';
    
    // Round to 3 decimal places
    const rounded = Math.round(value * 1000) / 1000;
    
    // Check if it's a whole number
    if (Number.isInteger(rounded)) {
        return rounded.toLocaleString('id-ID'); // Show as "1,000" not "1,000.000"
    }
    
    return rounded.toLocaleString('id-ID', {
        minimumFractionDigits: 3,
        maximumFractionDigits: 3
    }); // Show decimals with thousand separator
}

    function formatWeightDisplay(value) {
        if (!value || value === 0) return '0';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toLocaleString('id-ID'); // Show as "5" not "5.000"
        }
        
        return rounded.toLocaleString('id-ID', {
            minimumFractionDigits: 3,
            maximumFractionDigits: 3
        }); // Show decimals with thousand separator
    }

    // Formatting functions for database (clean values, no excessive decimals)
    function formatVolumeForDatabase(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Send as "1000" not "1000.000"
        }
        
        // Remove trailing zeros from decimals
        return parseFloat(rounded.toFixed(3)).toString();
    }

    function formatWeightForDatabase(value) {
        if (!value || value === 0) return '';
        
        // Round to 3 decimal places
        const rounded = Math.round(value * 1000) / 1000;
        
        // Check if it's a whole number
        if (Number.isInteger(rounded)) {
            return rounded.toString(); // Send as "5" not "5.000"
        }
        
        // Remove trailing zeros from decimals
        return parseFloat(rounded.toFixed(3)).toString();
    }let itemIndex = {{ $tandaTerima->items ? $tandaTerima->items->count() : 0 }};

function addDimensiRow() {
    const container = document.getElementById('dimensi-container');
    const newRow = document.createElement('div');
    newRow.className = 'dimensi-row border border-gray-200 rounded-lg p-4 mb-4';
    newRow.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-md font-medium text-gray-700">Item ${itemIndex + 1}</h3>
            <button type="button" class="text-red-600 hover:text-red-800" onclick="removeDimensiRow(this)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Panjang (m)</label>
                <input type="number" name="items[${itemIndex}][panjang]" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                       onchange="calculateVolume(this)">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (m)</label>
                <input type="number" name="items[${itemIndex}][lebar]" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                       onchange="calculateVolume(this)">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (m)</label>
                <input type="number" name="items[${itemIndex}][tinggi]" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                       onchange="calculateVolume(this)">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Volume (m³)</label>
                <input type="text" name="items[${itemIndex}][meter_kubik]" 
                       class="volume-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                       readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berat (Ton)</label>
                <input type="number" name="items[${itemIndex}][tonase]" step="0.01" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
    itemIndex++;
}

function removeDimensiRow(button) {
    button.closest('.dimensi-row').remove();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleKontainerFields();
    
    // Format input values on blur for smart decimal display
    document.addEventListener('blur', function(e) {
        if (e.target.matches('input[name*="[panjang]"], input[name*="[lebar]"], input[name*="[tinggi]"], input[name*="[tonase]"]')) {
            const value = parseFloat(e.target.value);
            if (!isNaN(value) && value > 0) {
                if (e.target.matches('input[name*="[tonase]"]')) {
                    e.target.value = formatWeight(value);
                } else {
                    e.target.value = formatVolume(value);
                }
            }
        }
    }, true);
});
</script>
@endsection