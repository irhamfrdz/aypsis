@extends('layouts.app')

@section('title', 'Edit Tagihan Kontainer Sewa')
@section('page_title', 'Edit Tagihan Kontainer Sewa')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Edit Tagihan Kontainer Sewa</h2>

    <form action="{{ route('tagihan-kontainer-sewa.update', $tagihanKontainerSewa->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Vendor</label>
            <input type="text" name="vendor" value="{{ old('vendor', $tagihanKontainerSewa->vendor) }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
            @error('vendor')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tarif</label>
            <select name="tarif" class="mt-1 block w-full border-gray-300 rounded-md" required>
                <option value="Bulanan" {{ old('tarif', $tagihanKontainerSewa->tarif) == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                <option value="Harian" {{ old('tarif', $tagihanKontainerSewa->tarif) == 'Harian' ? 'selected' : '' }}>Harian</option>
            </select>
            @error('tarif')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Ukuran Kontainer</label>
            <input type="text" name="ukuran_kontainer" value="{{ old('ukuran_kontainer', $tagihanKontainerSewa->ukuran_kontainer) }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
            @error('ukuran_kontainer')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Harga</label>
                <input type="number" step="0.01" name="harga" value="{{ old('harga', $tagihanKontainerSewa->harga) }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                @error('harga')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Harga Awal</label>
                <input type="date" name="tanggal_harga_awal" value="{{ old('tanggal_harga_awal', $tagihanKontainerSewa->tanggal_harga_awal ? $tagihanKontainerSewa->tanggal_harga_awal->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                @error('tanggal_harga_awal')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Harga Akhir (opsional)</label>
            <input type="date" name="tanggal_harga_akhir" value="{{ old('tanggal_harga_akhir', $tagihanKontainerSewa->tanggal_harga_akhir ? $tagihanKontainerSewa->tanggal_harga_akhir->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('tanggal_harga_akhir')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Keterangan (opsional)</label>
            <textarea name="keterangan" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('keterangan', $tagihanKontainerSewa->keterangan) }}</textarea>
            @error('keterangan')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Periode (opsional)</label>
            <input type="text" name="periode" value="{{ old('periode', $tagihanKontainerSewa->periode) }}" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="YYYY-MM atau teks" />
            @error('periode')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
