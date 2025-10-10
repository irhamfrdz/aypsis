@extends('layouts.app')

@section('title', 'Edit Kegiatan')
@section('page_title', 'Edit Kegiatan')

@section('content')
<h2 class="text-2xl font-bold mb-4 text-gray-800">Formulir Edit Kegiatan</h2>

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('master.kegiatan.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Kegiatan</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="kode_kegiatan" class="block text-sm font-medium text-gray-700">Kode Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_kegiatan" id="kode_kegiatan" value="{{ old('kode_kegiatan', $item->kode_kegiatan) }}" class="{{ $inputClasses }}" required maxlength="50">
                    @error('kode_kegiatan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_kegiatan" class="block text-sm font-medium text-gray-700">Nama Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" value="{{ old('nama_kegiatan', $item->nama_kegiatan) }}" class="{{ $inputClasses }}" required maxlength="255">
                    @error('nama_kegiatan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Type --</option>
                        <option value="kegiatan memo supir" {{ old('type', $item->type) == 'kegiatan memo supir' ? 'selected' : '' }}>Kegiatan Memo Supir</option>
                        <option value="uang muka" {{ old('type', $item->type) == 'uang muka' ? 'selected' : '' }}>Uang Muka</option>
                    </select>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="aktif" {{ old('status', $item->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $item->status) == 'nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 lg:col-span-3">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="{{ $inputClasses }}">{{ old('keterangan', $item->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('master.kegiatan.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Batal</a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Perbarui</button>
        </div>
    </form>
</div>
@endsection
