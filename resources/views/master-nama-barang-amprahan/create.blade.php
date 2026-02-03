@extends('layouts.app')

@section('title', 'Tambah Nama Barang Amprahan')
@section('page_title', 'Tambah Nama Barang Amprahan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <a href="{{ route('master.nama-barang-amprahan.index') }}" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800">Tambah Nama Barang Amprahan Baru</h2>
                <p class="text-sm text-gray-600">Isi form berikut untuk menambahkan data nama barang amprahan</p>
            </div>
            
            <form action="{{ route('master.nama-barang-amprahan.store') }}" method="POST" class="p-6">
                @csrf
                
                <div class="space-y-6">
                    <!-- Nama Barang -->
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama_barang') border-red-500 @enderror" 
                               placeholder="Masukkan nama barang" required>
                        @error('nama_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('master.nama-barang-amprahan.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm">
                        Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
