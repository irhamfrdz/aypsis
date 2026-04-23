@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-edit mr-3 text-indigo-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Bill of Lading</h1>
                    <p class="text-gray-600">BL ID: #{{ $bl->id }} | {{ $bl->nomor_bl ?: 'No Number' }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('bl.show', $bl) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Batal
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('bl.update', $bl) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Informasi Dasar --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                    Informasi Utama
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="nomor_bl" class="block text-sm font-medium text-gray-700">Nomor BL</label>
                        <input type="text" name="nomor_bl" id="nomor_bl" value="{{ old('nomor_bl', $bl->nomor_bl) }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('nomor_bl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700">Nomor Kontainer</label>
                            <input type="text" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer', $bl->nomor_kontainer) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('nomor_kontainer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="no_seal" class="block text-sm font-medium text-gray-700">No Seal</label>
                            <input type="text" name="no_seal" id="no_seal" value="{{ old('no_seal', $bl->no_seal) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('no_seal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700">Tipe Kontainer</label>
                            <input type="text" name="tipe_kontainer" id="tipe_kontainer" value="{{ old('tipe_kontainer', $bl->tipe_kontainer) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('tipe_kontainer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700">Size Kontainer</label>
                            <select name="size_kontainer" id="size_kontainer" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="" {{ old('size_kontainer', $bl->size_kontainer) == '' ? 'selected' : '' }}>Pilih Size</option>
                                <option value="20" {{ old('size_kontainer', $bl->size_kontainer) == '20' ? 'selected' : '' }}>20ft</option>
                                <option value="40" {{ old('size_kontainer', $bl->size_kontainer) == '40' ? 'selected' : '' }}>40ft</option>
                                <option value="45" {{ old('size_kontainer', $bl->size_kontainer) == '45' ? 'selected' : '' }}>45ft</option>
                            </select>
                            @error('size_kontainer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="status_bongkar" class="block text-sm font-medium text-gray-700">Status Bongkar</label>
                        <select name="status_bongkar" id="status_bongkar" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="Belum Bongkar" {{ old('status_bongkar', $bl->status_bongkar) == 'Belum Bongkar' ? 'selected' : '' }}>Belum Bongkar</option>
                            <option value="Sudah Bongkar" {{ old('status_bongkar', $bl->status_bongkar) == 'Sudah Bongkar' ? 'selected' : '' }}>Sudah Bongkar</option>
                        </select>
                        @error('status_bongkar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Informasi Kapal & Rute --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <i class="fas fa-ship mr-2 text-indigo-600"></i>
                    Informasi Kapal & Rute
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="nama_kapal" class="block text-sm font-medium text-gray-700">Nama Kapal</label>
                        <input type="text" name="nama_kapal" id="nama_kapal" value="{{ old('nama_kapal', $bl->nama_kapal) }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('nama_kapal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="no_voyage" class="block text-sm font-medium text-gray-700">No Voyage</label>
                        <input type="text" name="no_voyage" id="no_voyage" value="{{ old('no_voyage', $bl->no_voyage) }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('no_voyage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tanggal_berangkat" class="block text-sm font-medium text-gray-700">Tanggal Berangkat</label>
                        <input type="date" name="tanggal_berangkat" id="tanggal_berangkat" value="{{ old('tanggal_berangkat', $bl->tanggal_berangkat ? Carbon\Carbon::parse($bl->tanggal_berangkat)->format('Y-m-d') : '') }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('tanggal_berangkat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="pelabuhan_asal" class="block text-sm font-medium text-gray-700">Pelabuhan Asal</label>
                            <input type="text" name="pelabuhan_asal" id="pelabuhan_asal" value="{{ old('pelabuhan_asal', $bl->pelabuhan_asal) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('pelabuhan_asal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="pelabuhan_tujuan" class="block text-sm font-medium text-gray-700">Pelabuhan Tujuan</label>
                            <input type="text" name="pelabuhan_tujuan" id="pelabuhan_tujuan" value="{{ old('pelabuhan_tujuan', $bl->pelabuhan_tujuan) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('pelabuhan_tujuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Barang --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <i class="fas fa-boxes mr-2 text-indigo-600"></i>
                    Detail Barang & Tonnage
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $bl->nama_barang) }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('nama_barang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tonnage" class="block text-sm font-medium text-gray-700">Tonnage</label>
                            <input type="number" step="0.001" name="tonnage" id="tonnage" value="{{ old('tonnage', $bl->tonnage) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('tonnage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="volume" class="block text-sm font-medium text-gray-700">Volume (m³)</label>
                            <input type="number" step="0.001" name="volume" id="volume" value="{{ old('volume', $bl->volume) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('volume') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan</label>
                            <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $bl->satuan) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('satuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="kuantitas" class="block text-sm font-medium text-gray-700">Kuantitas</label>
                            <input type="text" name="kuantitas" id="kuantitas" value="{{ old('kuantitas', $bl->kuantitas) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('kuantitas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="term" class="block text-sm font-medium text-gray-700">Term</label>
                        <input type="text" name="term" id="term" value="{{ old('term', $bl->term) }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('term') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Informasi Pengirim & Penerima --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <i class="fas fa-users mr-2 text-indigo-600"></i>
                    Pengirim & Penerima
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="pengirim" class="block text-sm font-medium text-gray-700">Nama Pengirim</label>
                        <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim', $bl->pengirim) }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('pengirim') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="penerima" class="block text-sm font-medium text-gray-700">Nama Penerima</label>
                        <input type="text" name="penerima" id="penerima" value="{{ old('penerima', $bl->penerima) }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('penerima') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $bl->contact_person) }}" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('contact_person') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="alamat_pengiriman" class="block text-sm font-medium text-gray-700">Alamat Pengiriman</label>
                        <textarea name="alamat_pengiriman" id="alamat_pengiriman" rows="3" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('alamat_pengiriman', $bl->alamat_pengiriman) }}</textarea>
                        @error('alamat_pengiriman') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow-md transition duration-200 inline-flex items-center">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
