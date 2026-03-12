@extends('layouts.app')

@section('title', 'Edit Pricelist Freight')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-6 border-b border-gray-200 pb-4">
                <h1 class="text-2xl font-bold text-gray-900">Edit Pricelist Freight</h1>
            </div>

            <form action="{{ route('master-pricelist-freight.update', $masterPricelistFreight->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $masterPricelistFreight->nama_barang) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        @error('nama_barang')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                            <select name="lokasi" id="lokasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">-- Pilih Lokasi --</option>
                                <option value="Jakarta" {{ old('lokasi', $masterPricelistFreight->lokasi) === 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                <option value="Batam" {{ old('lokasi', $masterPricelistFreight->lokasi) === 'Batam' ? 'selected' : '' }}>Batam</option>
                                <option value="Pinang" {{ old('lokasi', $masterPricelistFreight->lokasi) === 'Pinang' ? 'selected' : '' }}>Pinang</option>
                            </select>
                            @error('lokasi')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor</label>
                            <input type="text" name="vendor" id="vendor" value="{{ old('vendor', $masterPricelistFreight->vendor) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Nama Vendor">
                            @error('vendor')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tarif" class="block text-sm font-medium text-gray-700">Tarif</label>
                            <input type="number" step="0.01" name="tarif" id="tarif" value="{{ old('tarif', (float)$masterPricelistFreight->tarif) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('tarif')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="Aktif" {{ old('status', $masterPricelistFreight->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ old('status', $masterPricelistFreight->status) === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('keterangan', $masterPricelistFreight->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('master-pricelist-freight.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Batal</a>
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
