@extends('layouts.app')

@section('title', 'Detail Pricelist Freight')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-6 border-b border-gray-200 pb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Detail Pricelist Freight</h1>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $masterPricelistFreight->status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $masterPricelistFreight->status }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Nama Barang</h4>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $masterPricelistFreight->nama_barang ?? '-' }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Lokasi</h4>
                        <p class="mt-1 text-base text-gray-900">{{ $masterPricelistFreight->lokasi ?? '-' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Vendor</h4>
                        <p class="mt-1 text-base text-gray-900">{{ $masterPricelistFreight->vendor ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-500">Tarif</h4>
                    <p class="mt-1 text-2xl font-bold text-blue-600">{{ $masterPricelistFreight->formatted_tarif }}</p>
                </div>

                @if($masterPricelistFreight->pelabuhan_asal_id || $masterPricelistFreight->pelabuhan_tujuan_id || $masterPricelistFreight->size_kontainer)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Informasi Tambahan (Legacy)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Asal:</span> {{ $masterPricelistFreight->asal->nama_pelabuhan ?? '-' }}
                        </div>
                        <div>
                            <span class="text-gray-500">Tujuan:</span> {{ $masterPricelistFreight->tujuan->nama_pelabuhan ?? '-' }}
                        </div>
                        <div>
                            <span class="text-gray-500">Size:</span> {{ $masterPricelistFreight->size_kontainer ?? '-' }}
                        </div>
                    </div>
                </div>
                @endif

                <div>
                    <h4 class="text-sm font-medium text-gray-500">Keterangan</h4>
                    <p class="mt-1 text-base text-gray-700 whitespace-pre-line">{{ $masterPricelistFreight->keterangan ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('master-pricelist-freight.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Kembali</a>
                @can('master-pricelist-freight-update')
                <a href="{{ route('master-pricelist-freight.edit', $masterPricelistFreight->id) }}" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Edit Data</a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
