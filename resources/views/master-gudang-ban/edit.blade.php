@extends('layouts.app')

@section('title', 'Edit Gudang Ban')
@section('page_title', 'Edit Gudang Ban')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-t-lg shadow-sm p-6 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Edit Gudang Ban</h1>
                    <p class="text-gray-600 text-sm mt-1">Perbarui informasi gudang ban</p>
                </div>
                <a href="{{ route('master-gudang-ban.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-b-lg shadow-sm p-6">
            <form action="{{ route('master-gudang-ban.update', $masterGudangBan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="nama_gudang" class="block text-sm font-medium text-gray-700 mb-1">Nama Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_gudang" id="nama_gudang" value="{{ old('nama_gudang', $masterGudangBan->nama_gudang) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_gudang') border-red-500 @enderror"
                               placeholder="Contoh: Ruko 10">
                        @error('nama_gudang')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                        <select name="lokasi" id="lokasi" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('lokasi') border-red-500 @enderror">
                            <option value="">-- Pilih Lokasi --</option>
                            <option value="Jakarta" {{ old('lokasi', $masterGudangBan->lokasi) == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                            <option value="Batam" {{ old('lokasi', $masterGudangBan->lokasi) == 'Batam' ? 'selected' : '' }}>Batam</option>
                            <option value="Pinang" {{ old('lokasi', $masterGudangBan->lokasi) == 'Pinang' ? 'selected' : '' }}>Pinang</option>
                        </select>
                        @error('lokasi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $masterGudangBan->keterangan) }}</textarea>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="aktif" {{ old('status', $masterGudangBan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $masterGudangBan->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('master-gudang-ban.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md shadow-sm transition duration-200">
                        <i class="fas fa-save mr-2"></i>Update Gudang Ban
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
