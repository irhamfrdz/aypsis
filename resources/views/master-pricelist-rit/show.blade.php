@extends('layouts.app')

@section('title', 'Detail Pricelist Rit')
@section('page_title', 'Detail Pricelist Rit')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Pricelist Rit</h2>
        <div class="flex space-x-2">
            @can('master-pricelist-rit-update')
            <a href="{{ route('master.pricelist-rit.edit', $pricelistRit) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                Edit
            </a>
            @endcan
            <a href="{{ route('master.pricelist-rit.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tujuan -->
        <div class="border-b pb-4">
            <label class="block text-sm font-medium text-gray-500 mb-1">Tujuan</label>
            <p class="text-lg font-semibold text-gray-900">{{ $pricelistRit->tujuan }}</p>
        </div>

        <!-- Tarif -->
        <div class="border-b pb-4">
            <label class="block text-sm font-medium text-gray-500 mb-1">Tarif</label>
            <p class="text-lg font-semibold text-gray-900">
                {{ $pricelistRit->tarif ? 'Rp ' . number_format($pricelistRit->tarif, 0, ',', '.') : '-' }}
            </p>
        </div>

        <!-- Status -->
        <div class="border-b pb-4">
            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
            <p class="text-lg">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                    {{ $pricelistRit->status == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $pricelistRit->status }}
                </span>
            </p>
        </div>

        <!-- Keterangan -->
        <div class="border-b pb-4 md:col-span-2">
            <label class="block text-sm font-medium text-gray-500 mb-1">Keterangan</label>
            <p class="text-gray-900">{{ $pricelistRit->keterangan ?? '-' }}</p>
        </div>

        <!-- Created At -->
        <div class="border-b pb-4">
            <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Pada</label>
            <p class="text-gray-900">{{ $pricelistRit->created_at ? $pricelistRit->created_at->format('d/m/Y H:i') : '-' }}</p>
        </div>

        <!-- Updated At -->
        <div class="border-b pb-4">
            <label class="block text-sm font-medium text-gray-500 mb-1">Diperbarui Pada</label>
            <p class="text-gray-900">{{ $pricelistRit->updated_at ? $pricelistRit->updated_at->format('d/m/Y H:i') : '-' }}</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mt-8 pt-6 border-t">
        <div>
            @can('master-pricelist-rit-delete')
            <form method="POST" action="{{ route('master.pricelist-rit.destroy', $pricelistRit) }}"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist rit ini?')"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Hapus Pricelist Rit
                </button>
            </form>
            @endcan
        </div>
        <div class="flex space-x-2">
            @can('master-pricelist-rit-update')
            <a href="{{ route('master.pricelist-rit.edit', $pricelistRit) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                Edit
            </a>
            @endcan
            <a href="{{ route('master.pricelist-rit.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                ← Kembali
            </a>
        </div>
    </div>
</div>
@endsection
