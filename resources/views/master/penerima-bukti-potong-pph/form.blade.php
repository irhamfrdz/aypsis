@extends('layouts.app')

@php($editing = isset($penerima))
@section('title', ($editing ? 'Edit' : 'Tambah') . ' Penerima Bukti Potong PPh')
@section('page_title', ($editing ? 'Edit' : 'Tambah') . ' Penerima Bukti Potong PPh')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-6">
    <a href="{{ route('master.penerima-bukti-potong-pph.index') }}" class="text-sm font-medium text-blue-700 hover:underline">&larr; Kembali ke daftar</a>
    <h1 class="mb-5 mt-4 text-2xl font-bold text-gray-900">{{ $editing ? 'Edit' : 'Tambah' }} Penerima Bukti Potong PPh</h1>

    <form method="POST" action="{{ $editing ? route('master.penerima-bukti-potong-pph.update', $penerima) : route('master.penerima-bukti-potong-pph.store') }}" class="space-y-5 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div>
            <label for="nama" class="mb-1 block text-sm font-medium text-gray-700">Nama penerima <span class="text-red-600">*</span></label>
            <input id="nama" name="nama" type="text" required maxlength="255" value="{{ old('nama', $penerima->nama ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm">
            @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="npwp" class="mb-1 block text-sm font-medium text-gray-700">NPWP</label>
                <input id="npwp" name="npwp" type="text" maxlength="32" value="{{ old('npwp', $penerima->npwp ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error('npwp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="nik" class="mb-1 block text-sm font-medium text-gray-700">NIK</label>
                <input id="nik" name="nik" type="text" maxlength="20" value="{{ old('nik', $penerima->nik ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error('nik') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="alamat" class="mb-1 block text-sm font-medium text-gray-700">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3" class="w-full rounded-lg border-gray-300 text-sm">{{ old('alamat', $penerima->alamat ?? '') }}</textarea>
            @error('alamat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $penerima->email ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="telepon" class="mb-1 block text-sm font-medium text-gray-700">Telepon</label>
                <input id="telepon" name="telepon" type="text" maxlength="30" value="{{ old('telepon', $penerima->telepon ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error('telepon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="is_active" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
            <select id="is_active" name="is_active" class="w-full rounded-lg border-gray-300 text-sm">
                <option value="1" @selected((string) old('is_active', isset($penerima) ? (int) $penerima->is_active : 1) === '1')>Aktif</option>
                <option value="0" @selected((string) old('is_active', isset($penerima) ? (int) $penerima->is_active : 1) === '0')>Nonaktif</option>
            </select>
            @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
            <a href="{{ route('master.penerima-bukti-potong-pph.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
