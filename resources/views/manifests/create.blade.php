@extends('layouts.app')

@section('title', 'Tambah Manifest')
@section('page_title', 'Tambah Manifest')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('report.manifests.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Manifest
            </a>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Tambah Manifest Baru</h1>
        </div>

        <form action="{{ route('report.manifests.store') }}" method="POST">
            @csrf
            
            <!-- Informasi BL & Kontainer -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi BL & Kontainer</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nomor_bl" class="block text-sm font-medium text-gray-700 mb-2">No. BL <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_bl" id="nomor_bl" value="{{ old('nomor_bl') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('nomor_bl') border-red-500 @enderror">
                        @error('nomor_bl')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prospek_id" class="block text-sm font-medium text-gray-700 mb-2">Prospek</label>
                        <select name="prospek_id" id="prospek_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">- Pilih Prospek -</option>
                            @foreach($prospeks as $prospek)
                                <option value="{{ $prospek->id }}" {{ old('prospek_id') == $prospek->id ? 'selected' : '' }}>
                                    {{ $prospek->pt_pengirim }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">No. Kontainer <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('nomor_kontainer') border-red-500 @enderror">
                        @error('nomor_kontainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-2">No. Seal</label>
                        <input type="text" name="no_seal" id="no_seal" value="{{ old('no_seal') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                        <select name="tipe_kontainer" id="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">- Pilih Tipe -</option>
                            <option value="Dry Container" {{ old('tipe_kontainer') == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                            <option value="High Cube" {{ old('tipe_kontainer') == 'High Cube' ? 'selected' : '' }}>High Cube</option>
                            <option value="Reefer" {{ old('tipe_kontainer') == 'Reefer' ? 'selected' : '' }}>Reefer</option>
                            <option value="Open Top" {{ old('tipe_kontainer') == 'Open Top' ? 'selected' : '' }}>Open Top</option>
                            <option value="Flat Rack" {{ old('tipe_kontainer') == 'Flat Rack' ? 'selected' : '' }}>Flat Rack</option>
                        </select>
                    </div>

                    <div>
                        <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Size Kontainer</label>
                        <select name="size_kontainer" id="size_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">- Pilih Size -</option>
                            <option value="20" {{ old('size_kontainer') == '20' ? 'selected' : '' }}>20'</option>
                            <option value="40" {{ old('size_kontainer') == '40' ? 'selected' : '' }}>40'</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Informasi Kapal & Pelabuhan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kapal & Pelabuhan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Nama Kapal</label>
                        <input type="text" name="nama_kapal" id="nama_kapal" value="{{ old('nama_kapal') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">No. Voyage</label>
                        <input type="text" name="no_voyage" id="no_voyage" value="{{ old('no_voyage') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="pelabuhan_asal" class="block text-sm font-medium text-gray-700 mb-2">Pelabuhan Asal</label>
                        <input type="text" name="pelabuhan_asal" id="pelabuhan_asal" value="{{ old('pelabuhan_asal') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="pelabuhan_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Pelabuhan Tujuan</label>
                        <input type="text" name="pelabuhan_tujuan" id="pelabuhan_tujuan" value="{{ old('pelabuhan_tujuan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="tanggal_berangkat" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berangkat</label>
                        <input type="date" name="tanggal_berangkat" id="tanggal_berangkat" value="{{ old('tanggal_berangkat') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="penerimaan" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Penerimaan</label>
                        <input type="date" name="penerimaan" id="penerimaan" value="{{ old('penerimaan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>

            <!-- Informasi Barang -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Barang</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-2">Nama Barang</label>
                        <textarea name="nama_barang" id="nama_barang" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">{{ old('nama_barang') }}</textarea>
                    </div>

                    <div>
                        <label for="tonnage" class="block text-sm font-medium text-gray-700 mb-2">Tonnage</label>
                        <input type="number" step="0.001" name="tonnage" id="tonnage" value="{{ old('tonnage') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="volume" class="block text-sm font-medium text-gray-700 mb-2">Volume</label>
                        <input type="number" step="0.001" name="volume" id="volume" value="{{ old('volume') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                        <input type="text" name="satuan" id="satuan" value="{{ old('satuan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="kuantitas" class="block text-sm font-medium text-gray-700 mb-2">Kuantitas</label>
                        <input type="number" name="kuantitas" id="kuantitas" value="{{ old('kuantitas') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="term" class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                        <input type="text" name="term" id="term" value="{{ old('term') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>

            <!-- Informasi Pengirim & Penerima -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengirim & Penerima</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="pengirim" class="block text-sm font-medium text-gray-700 mb-2">Pengirim</label>
                        <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">Penerima</label>
                        <input type="text" name="penerima" id="penerima" value="{{ old('penerima') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="asal_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Asal Kontainer</label>
                        <input type="text" name="asal_kontainer" id="asal_kontainer" value="{{ old('asal_kontainer') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="ke" class="block text-sm font-medium text-gray-700 mb-2">Ke</label>
                        <input type="text" name="ke" id="ke" value="{{ old('ke') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat_pengiriman" class="block text-sm font-medium text-gray-700 mb-2">Alamat Pengiriman</label>
                        <textarea name="alamat_pengiriman" id="alamat_pengiriman" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">{{ old('alamat_pengiriman') }}</textarea>
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('report.manifests.index') }}"
                   class="px-6 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    Simpan Manifest
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
