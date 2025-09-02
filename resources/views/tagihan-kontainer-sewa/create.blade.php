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
            <label class="block text-sm font-medium text-gray-700">Nomor Kontainer (pisahkan dengan koma jika lebih dari 1)</label>
            <input type="text" name="nomor_kontainer" value="{{ old('nomor_kontainer') }}" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="CONT123, CONT456" />
            @error('nomor_kontainer')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Harga Awal</label>
                <input type="date" name="tanggal_harga_awal" value="{{ old('tanggal_harga_awal') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                @error('tanggal_harga_awal')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Harga Akhir (opsional)</label>
                <input type="date" name="tanggal_harga_akhir" value="{{ old('tanggal_harga_akhir') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                @error('tanggal_harga_akhir')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Periode (opsional, contoh: 2025-08)</label>
            <input type="text" name="periode" value="{{ old('periode') }}" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="YYYY-MM atau teks" />
            @error('periode')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-3 gap-4 mt-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Masa (opsional)</label>
                <input type="text" name="masa" value="{{ old('masa') }}" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="contoh: 1 bulan" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">DPP</label>
                <input type="number" step="0.01" name="dpp" value="{{ old('dpp') }}" class="mt-1 block w-full border-gray-300 rounded-md" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">DPP Nilai Lain</label>
                <input type="number" step="0.01" name="dpp_nilai_lain" value="{{ old('dpp_nilai_lain') }}" class="mt-1 block w-full border-gray-300 rounded-md" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">PPN</label>
                <input type="number" step="0.01" name="ppn" value="{{ old('ppn') }}" class="mt-1 block w-full border-gray-300 rounded-md" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">PPH</label>
                <input type="number" step="0.01" name="pph" value="{{ old('pph') }}" class="mt-1 block w-full border-gray-300 rounded-md" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Grand Total</label>
                <input type="number" step="0.01" name="grand_total" value="{{ old('grand_total') }}" class="mt-1 block w-full border-gray-300 rounded-md" />
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
