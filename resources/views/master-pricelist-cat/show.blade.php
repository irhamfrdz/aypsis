@extends('layouts.app')

@section('title', 'Detail Pricelist CAT')
@section('page_title', 'Detail Pricelist CAT')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Pricelist CAT</h2>
        <div class="flex space-x-2">
            <a href="{{ route('master.pricelist-cat.edit', $pricelistCat) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                ✏️ Edit
            </a>
            <a href="{{ route('master.pricelist-cat.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Vendor -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor/Bengkel</label>
            <p class="text-lg font-semibold text-gray-900">{{ $pricelistCat->vendor }}</p>
        </div>

        <!-- Jenis CAT -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis CAT</label>
            <p class="text-lg font-semibold text-gray-900">
                @if($pricelistCat->jenis_cat == 'cat_sebagian')
                    Cat Sebagian
                @elseif($pricelistCat->jenis_cat == 'cat_full')
                    Cat Full
                @else
                    {{ $pricelistCat->jenis_cat }}
                @endif
            </p>
        </div>

        <!-- Ukuran Kontainer -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran Kontainer</label>
            <p class="text-lg font-semibold text-gray-900">{{ $pricelistCat->ukuran_kontainer }}</p>
        </div>

        <!-- Tarif per Meter -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tarif</label>
            <p class="text-lg font-semibold text-green-600">
                @if($pricelistCat->tarif)
                    Rp {{ number_format($pricelistCat->tarif, 0, ',', '.') }}
                @else
                    -
                @endif
            </p>
        </div>

        <!-- Harga Total -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Total</label>
            <p class="text-lg font-semibold text-green-600">Rp {{ number_format($pricelistCat->harga, 0, ',', '.') }}</p>
        </div>

        <!-- Tanggal Harga Awal -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Harga Awal</label>
            <p class="text-lg font-semibold text-gray-900">{{ $pricelistCat->tanggal_harga_awal->format('d/m/Y') }}</p>
        </div>

        <!-- Tanggal Harga Akhir -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Harga Akhir</label>
            <p class="text-lg font-semibold text-gray-900">
                @if($pricelistCat->tanggal_harga_akhir)
                    {{ $pricelistCat->tanggal_harga_akhir->format('d/m/Y') }}
                @else
                    -
                @endif
            </p>
        </div>

        <!-- Status Aktif -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <p class="text-lg font-semibold">
                @if($pricelistCat->isActive())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        ✓ Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        ✗ Tidak Aktif
                    </span>
                @endif
            </p>
        </div>
    </div>

    <!-- Keterangan -->
    @if($pricelistCat->keterangan)
    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
        <p class="text-gray-900 whitespace-pre-line">{{ $pricelistCat->keterangan }}</p>
    </div>
    @endif

    <!-- Informasi Sistem -->
    <div class="mt-6 bg-blue-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">Informasi Sistem</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-blue-700 mb-1">Dibuat Oleh</label>
                <p class="text-blue-900">{{ $pricelistCat->creator->name ?? 'Unknown' }}</p>
                <p class="text-sm text-blue-600">{{ $pricelistCat->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-blue-700 mb-1">Terakhir Diupdate</label>
                <p class="text-blue-900">{{ $pricelistCat->updater->name ?? 'Unknown' }}</p>
                <p class="text-sm text-blue-600">{{ $pricelistCat->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4 mt-6 pt-6 border-t">
        <a href="{{ route('master.pricelist-cat.edit', $pricelistCat) }}"
           class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-500 transition duration-200">
            ✏️ Edit Pricelist CAT
        </a>
        <form action="{{ route('master.pricelist-cat.destroy', $pricelistCat) }}" method="POST" class="inline"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist CAT ini?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 transition duration-200">
                🗑️ Hapus
            </button>
        </form>
        <a href="{{ route('master.pricelist-cat.index') }}"
           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 transition duration-200">
            ← Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
