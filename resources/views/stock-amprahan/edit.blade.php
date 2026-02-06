@extends('layouts.app')

@section('title', 'Edit Stock Amprahan')
@section('page_title', 'Edit Stock Amprahan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6 text-sm text-gray-500">
            <a href="{{ route('stock-amprahan.index') }}" class="hover:text-indigo-600 transition-colors">Stock Amprahan</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium">Edit Data</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Ubah Informasi Stock</h2>
                
                <form action="{{ route('stock-amprahan.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        {{-- Nama Barang --}}
                        <div>
                            <label for="nama_barang" class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '')) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" required>
                            @error('nama_barang')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Type Barang --}}
                            <div>
                                <label for="master_nama_barang_amprahan_id" class="block text-sm font-semibold text-gray-700 mb-1">Type Barang <span class="text-red-500">*</span></label>
                                <select name="master_nama_barang_amprahan_id" id="master_nama_barang_amprahan_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" required>
                                    <option value="">-- Pilih Type --</option>
                                    @foreach($masterItems as $master)
                                        <option value="{{ $master->id }}" {{ old('master_nama_barang_amprahan_id', $item->master_nama_barang_amprahan_id) == $master->id ? 'selected' : '' }}>
                                            {{ $master->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('master_nama_barang_amprahan_id')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Harga Satuan --}}
                            <div>
                                <label for="harga_satuan" class="block text-sm font-semibold text-gray-700 mb-1">Harga Satuan</label>
                                <input type="number" name="harga_satuan" id="harga_satuan" value="{{ old('harga_satuan', $item->harga_satuan ?? 0) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('harga_satuan')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Jumlah --}}
                            <div>
                                <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="jumlah" id="jumlah" value="{{ old('jumlah', $item->jumlah) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" required>
                                @error('jumlah')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Satuan --}}
                            <div>
                                <label for="satuan" class="block text-sm font-semibold text-gray-700 mb-1">Satuan</label>
                                <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $item->satuan) }}" placeholder="Contoh: rim, pack, pcs" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('satuan')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label for="lokasi" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Penyimpanan</label>
                            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $item->lokasi) }}" placeholder="Contoh: Gudang A, Rak 2" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                            @error('lokasi')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Catatan tambahan jika ada..." class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">{{ old('keterangan', $item->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end space-x-4">
                        <a href="{{ route('stock-amprahan.index') }}" class="px-6 py-2.5 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">Batal</a>
                        <button type="submit" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md shadow-indigo-200 transition-all duration-200 focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                            Perbarui Stock
                        </button>
                    </div>
                </form>
            </div>
            
            {{-- Audit Info --}}
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-4 text-xs text-gray-400">
                <div>
                    <span class="font-semibold block uppercase">Dibuat Oleh:</span>
                    {{ $item->createdBy->name ?? '-' }} ({{ $item->created_at->format('d/m/Y H:i') }})
                </div>
                <div>
                    <span class="font-semibold block uppercase">Diperbarui Oleh:</span>
                    {{ $item->updatedBy->name ?? '-' }} ({{ $item->updated_at->format('d/m/Y H:i') }})
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
