@extends('layouts.app')

@section('title', 'Edit Jenis Layanan Pelabuhan')
@section('page_title', 'Edit Jenis Layanan Pelabuhan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('master.jenis-layanan-pelabuhan.update', $masterJenisLayananPelabuhan) }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $masterJenisLayananPelabuhan->nama) }}" class="w-full px-3 py-2 border rounded-md">
                @error('nama') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="flex justify-end">
                <a href="{{ route('master.jenis-layanan-pelabuhan.index') }}" class="px-4 py-2 bg-gray-300 rounded mr-2">Batal</a>
                <button class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
