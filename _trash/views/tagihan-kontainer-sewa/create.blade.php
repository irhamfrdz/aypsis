@extends('layouts.app')

@section('title', 'Tambah Tagihan Kontainer Sewa')
@section('page_title', 'Tambah Tagihan Kontainer Sewa')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Tambah Tagihan Kontainer Sewa</h2>

    <form action="{{ route('tagihan-kontainer-sewa.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Vendor</label>
            <input type="text" name="vendor" value="{{ old('vendor') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
            @error('vendor')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tarif</label>
            <select name="tarif" class="mt-1 block w-full border-gray-300 rounded-md" required>
                <option value="Bulanan" {{ old('tarif') == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                <option value="Harian" {{ old('tarif') == 'Harian' ? 'selected' : '' }}>Harian</option>
            </select>
            @error('tarif')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Ukuran Kontainer</label>
            <input type="text" name="ukuran_kontainer" value="{{ old('ukuran_kontainer') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
            @error('ukuran_kontainer')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Harga</label>
                <input type="number" step="0.01" name="harga" value="{{ old('harga') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                @error('harga')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Harga Awal</label>
                <input type="date" name="tanggal_harga_awal" value="{{ old('tanggal_harga_awal') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                @error('tanggal_harga_awal')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Periode (opsional, contoh: 2025-08)</label>
            <input type="text" name="periode" value="{{ old('periode') }}" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="YYYY-MM atau teks" />
            @error('periode')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Harga Akhir (opsional)</label>
            <input type="date" name="tanggal_harga_akhir" value="{{ old('tanggal_harga_akhir') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('tanggal_harga_akhir')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Keterangan (opsional)</label>
            <textarea name="keterangan" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('keterangan') }}</textarea>
            @error('keterangan')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
            <select name="status_pembayaran" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="Belum Pembayaran" {{ old('status_pembayaran', 'Belum Pembayaran') == 'Belum Pembayaran' ? 'selected' : '' }}>Belum Pembayaran</option>
                <option value="Sudah Masuk Pranota" {{ old('status_pembayaran') == 'Sudah Masuk Pranota' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                <option value="Lunas" {{ old('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
            @error('status_pembayaran')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
