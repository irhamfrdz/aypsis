@extends('layouts.app')

@section('title', 'Buat Pranota Baru')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            <p class="font-bold">Peringatan</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Buat Pranota Baru</h1>
            <p class="text-gray-600 mt-1">Form pembuatan pranota</p>
        </div>

        <form method="POST" action="{{ route('pranota-kontainer-sewa.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Pranota</label>
                    <input type="text" name="no_invoice" value="{{ old('no_invoice', $nomorPranota ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan nomor pranota">
                    @error('no_invoice')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Masukkan keterangan">{{ old('keterangan', $catatan ?? '') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($tagihanCat)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tagihan CAT Terpilih</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Kontainer</label>
                            <p class="text-sm text-gray-900">{{ $tagihanCat->nomor_kontainer }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vendor</label>
                            <p class="text-sm text-gray-900">{{ $tagihanCat->vendor ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estimasi Biaya</label>
                            <p class="text-sm text-gray-900">Rp {{ number_format($tagihanCat->estimasi_biaya ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <input type="hidden" name="tagihan_cat_id" value="{{ $tagihanCat->id }}">
                </div>
            </div>
            @endif

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('pranota-kontainer-sewa.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Simpan Pranota
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
