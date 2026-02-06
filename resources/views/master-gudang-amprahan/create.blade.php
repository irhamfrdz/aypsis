@extends('layouts.app')

@section('title', 'Tambah Gudang Amprahan')
@section('page_title', 'Tambah Gudang Amprahan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm text-gray-500">
            <a href="{{ route('master.gudang-amprahan.index') }}" class="hover:text-blue-600 transition-colors">Master Gudang Amprahan</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium">Tambah Baru</span>
        </nav>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Tambah Gudang Amprahan Baru</h2>
                <p class="text-sm text-gray- mt-1">Isi form di bawah untuk menambah gudang amprahan baru</p>
            </div>

            <form action="{{ route('master.gudang-amprahan.store') }}" method="POST" class="p-6">
                @csrf

                <div class="space-y-6">
                    <!-- Nama Gudang -->
                    <div>
                        <label for="nama_gudang" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-warehouse mr-2 text-gray-400"></i>Nama Gudang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_gudang" 
                               id="nama_gudang" 
                               value="{{ old('nama_gudang') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_gudang') border-red-500 @enderror"
                               required
                               placeholder="Contoh: Gudang A, Gudang Central, dll">
                        @error('nama_gudang')
                            <p class="mt-2 text-xs text-red-500">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>



                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-gray-400"></i>Keterangan
                        </label>
                        <textarea name="keterangan" 
                                  id="keterangan" 
                                  rows="4" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keterangan') border-red-500 @enderror"
                                  placeholder="Deskripsi atau catatan tambahan tentang gudang...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-2 text-xs text-red-500">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-toggle-on mr-2 text-gray-400"></i>Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                                required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-xs text-red-500">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('master.gudang-amprahan.index') }}" 
                       class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
