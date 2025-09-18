@extends('layouts.app')

@section('title', 'Detail Cabang')
@section('page_title', 'Detail Cabang')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('master.cabang.index') }}" class="text-indigo-600 hover:text-indigo-900 mr-4 transition duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Detail Cabang</h2>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.cabang.edit', $cabang) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Edit
            </a>
            <form action="{{ route('master.cabang.destroy', $cabang) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Cabang</label>
                <div class="bg-gray-50 px-3 py-2 rounded-lg border">
                    <span class="text-gray-900">{{ $cabang->nama_cabang }}</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <div class="bg-gray-50 px-3 py-2 rounded-lg border min-h-[80px]">
                    <span class="text-gray-900">{{ $cabang->keterangan ?: '-' }}</span>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Pada</label>
                <div class="bg-gray-50 px-3 py-2 rounded-lg border">
                    <span class="text-gray-900">{{ $cabang->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Terakhir Diupdate</label>
                <div class="bg-gray-50 px-3 py-2 rounded-lg border">
                    <span class="text-gray-900">{{ $cabang->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
