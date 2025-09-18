@extends('layouts.app')

@section('title', 'Edit Cabang')
@section('page_title', 'Edit Cabang')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('master.cabang.index') }}" class="text-indigo-600 hover:text-indigo-900 mr-4 transition duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Edit Cabang</h2>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('master.cabang.update', $cabang) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="nama_cabang" class="block text-sm font-medium text-gray-700 mb-2">Nama Cabang <span class="text-red-500">*</span></label>
            <input type="text" id="nama_cabang" name="nama_cabang" value="{{ old('nama_cabang', $cabang->nama_cabang) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 @error('nama_cabang') border-red-500 @enderror"
                   placeholder="Masukkan nama cabang" required>
            @error('nama_cabang')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
            <textarea id="keterangan" name="keterangan" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 @error('keterangan') border-red-500 @enderror"
                      placeholder="Masukkan keterangan cabang (opsional)">{{ old('keterangan', $cabang->keterangan) }}</textarea>
            @error('keterangan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('master.cabang.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Batal
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
