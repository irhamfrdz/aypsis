@extends('layouts.app')

@section('title', 'Edit Sertifikat Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('master-sertifikat-kapal.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                    Master Sertifikat Kapal
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Edit Sertifikat</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-900">Edit Sertifikat Kapal</h1>
                <span class="text-xs text-gray-400">ID: {{ $master_sertifikat_kapal->id }}</span>
            </div>

            <form action="{{ route('master-sertifikat-kapal.update', $master_sertifikat_kapal->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="nama_sertifikat" class="block text-sm font-medium text-gray-700 mb-1">Nama Sertifikat <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sertifikat" id="nama_sertifikat" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_sertifikat') border-red-500 @enderror"
                           value="{{ old('nama_sertifikat', $master_sertifikat_kapal->nama_sertifikat) }}" placeholder="Masukkan nama sertifikat">
                    @error('nama_sertifikat')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name_certificate" class="block text-sm font-medium text-gray-700 mb-1">Name Certificate</label>
                    <input type="text" name="name_certificate" id="name_certificate"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name_certificate') border-red-500 @enderror"
                           value="{{ old('name_certificate', $master_sertifikat_kapal->name_certificate) }}" placeholder="Name Certificate">
                    @error('name_certificate')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-1">Nickname</label>
                    <input type="text" name="nickname" id="nickname"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nickname') border-red-500 @enderror"
                           value="{{ old('nickname', $master_sertifikat_kapal->nickname) }}" placeholder="Nickname">
                    @error('nickname')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jenis_dokumen" class="block text-sm font-medium text-gray-700 mb-1">Jenis Dokumen</label>
                    <input type="text" name="jenis_dokumen" id="jenis_dokumen"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jenis_dokumen') border-red-500 @enderror"
                           value="{{ old('jenis_dokumen', $master_sertifikat_kapal->jenis_dokumen) }}" placeholder="Jenis Dokumen">
                    @error('jenis_dokumen')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="aktif" {{ old('status', $master_sertifikat_kapal->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $master_sertifikat_kapal->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('master-sertifikat-kapal.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition duration-200">
                        Perbarui Sertifikat
                    </button>
                </div>
            </form>
        </div>

        @can('master-sertifikat-kapal-delete')
        <div class="mt-8 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-red-800">Zona Bahaya</h3>
                <p class="text-xs text-red-600">Menghapus data ini tidak dapat dibatalkan melalui UI.</p>
            </div>
            <form action="{{ route('master-sertifikat-kapal.destroy', $master_sertifikat_kapal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sertifikat ini secara permanen?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700 transition duration-200">
                    Hapus Permanen
                </button>
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
