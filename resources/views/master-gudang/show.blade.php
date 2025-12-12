@extends('layouts.app')

@section('title', 'Detail Gudang')
@section('page_title', 'Detail Gudang')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Gudang</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap gudang</p>
            </div>
            <div class="flex gap-2">
                @can('master-gudang-edit')
                <a href="{{ route('master-gudang.edit', $masterGudang) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                @endcan
                <a href="{{ route('master-gudang.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Detail Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Nama Gudang</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $masterGudang->nama_gudang }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Lokasi</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $masterGudang->lokasi }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                @if($masterGudang->status == 'aktif')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-2"></i>
                        Nonaktif
                    </span>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Dibuat Pada</h3>
                <p class="text-lg text-gray-900">{{ $masterGudang->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Keterangan</h3>
                <p class="text-lg text-gray-900">{{ $masterGudang->keterangan ?: '-' }}</p>
            </div>

            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Terakhir Diupdate</h3>
                <p class="text-lg text-gray-900">{{ $masterGudang->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @can('master-gudang-delete')
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form action="{{ route('master-gudang.destroy', $masterGudang) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus gudang ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-trash mr-2"></i>Hapus Gudang
                </button>
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
