@extends('layouts.app')

@section('title', 'Tambah Stock Amprahan')
@section('page_title', 'Tambah Stock Amprahan')

@section('content')
@push('styles')
<style>
    .rounded-xl { border-radius: 0.75rem; }
    .focus-ring-premium {
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }
    input:focus, select:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }
    .group:focus-within label i {
        transform: scale(1.1);
    }
    label i {
        transition: transform 0.2s ease;
    }
    .btn-submit-premium {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border: none;
    }
    .btn-submit-premium:hover {
        background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
    }
</style>
@endpush

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6 text-sm text-gray-500">
            <a href="{{ route('stock-amprahan.index') }}" class="hover:text-indigo-600 transition-colors">Stock Amprahan</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium">Tambah Baru</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Informasi Stock Baru</h2>
                
                <form action="{{ route('stock-amprahan.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="space-y-6">
                        {{-- Nama Barang --}}
                        <div class="group">
                            <label for="nama_barang" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                <i class="fas fa-box-open mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm" required>
                            @error('nama_barang')
                                <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Type Barang --}}
                            <div class="group">
                                <label for="master_nama_barang_amprahan_id" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-tags mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Type Barang <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="master_nama_barang_amprahan_id" id="master_nama_barang_amprahan_id" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm appearance-none" required>
                                        <option value="">-- Pilih Type --</option>
                                        @foreach($masterItems as $master)
                                            <option value="{{ $master->id }}" {{ old('master_nama_barang_amprahan_id') == $master->id ? 'selected' : '' }}>
                                                {{ $master->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                @error('master_nama_barang_amprahan_id')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Harga Satuan --}}
                            <div class="group">
                                <label for="harga_satuan" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-money-bill-wave mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Harga Satuan
                                </label>
                                <input type="number" name="harga_satuan" id="harga_satuan" value="{{ old('harga_satuan', 0) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                @error('harga_satuan')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Jumlah --}}
                            <div class="group">
                                <label for="jumlah" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-calculator mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Jumlah <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="jumlah" id="jumlah" value="{{ old('jumlah', 0) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm" required>
                                @error('jumlah')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Satuan --}}
                            <div class="group">
                                <label for="satuan" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-tag mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Satuan
                                </label>
                                <input type="text" name="satuan" id="satuan" value="{{ old('satuan') }}" placeholder="rim, pack, pcs" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                @error('satuan')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Lokasi --}}
                        <div class="group">
                            <label for="lokasi" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Lokasi Penyimpanan
                            </label>
                            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}" placeholder="Gudang A, Rak 2" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                            @error('lokasi')
                                <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div class="group">
                            <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                <i class="fas fa-sticky-note mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Keterangan
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Catatan tambahan jika ada..." class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm resize-none">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                        <a href="{{ route('stock-amprahan.index') }}" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors">Batal</a>
                        <button type="submit" class="btn-submit-premium px-10 py-3 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95">
                            <i class="fas fa-save mr-2"></i>Simpan Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
