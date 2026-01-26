@extends('layouts.app')

@section('title', 'Edit Dokumen Perijinan Kapal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Dokumen Perijinan</h1>
                </div>

                <form action="{{ route('master-dokumen-perijinan-kapal.update', $master_dokumen_perijinan_kapal->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="nama_dokumen" class="block text-sm font-medium text-gray-700 mb-1">Nama Dokumen</label>
                        <input type="text" name="nama_dokumen" id="nama_dokumen" value="{{ old('nama_dokumen', $master_dokumen_perijinan_kapal->nama_dokumen) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        @error('nama_dokumen')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('keterangan', $master_dokumen_perijinan_kapal->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status_aktif" class="inline-flex items-center">
                            <input type="hidden" name="status_aktif" value="0">
                            <input type="checkbox" name="status_aktif" id="status_aktif" value="1" {{ old('status_aktif', $master_dokumen_perijinan_kapal->status_aktif) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Status Aktif</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('master-dokumen-perijinan-kapal.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
