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

        <div>
            <label for="nik_npwp" class="mb-1 block text-sm font-medium text-gray-700">NIK/NPWP</label>
            <input id="nik_npwp" name="nik_npwp" type="text" maxlength="64" value="{{ old('nik_npwp', $penerima->nik_npwp ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm">
            @error('nik_npwp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="pph" class="mb-1 block text-sm font-medium text-gray-700">PPh</label>
                <input id="pph" name="pph" type="text" maxlength="100" value="{{ old('pph', $penerima->pph ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error('pph') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="grup" class="mb-1 block text-sm font-medium text-gray-700">Grup</label>
                <input id="grup" name="grup" type="text" maxlength="100" value="{{ old('grup', $penerima->grup ?? '') }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error('grup') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
            <a href="{{ route('master.penerima-bukti-potong-pph.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
