@extends('layouts.app')

@section('page_title', 'Detail Pricelist Biaya Trucking')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pricelist</h1>
                <p class="text-gray-600 mt-1">Informasi detail pricelist biaya trucking</p>
            </div>
            <a href="{{ route('master.pricelist-biaya-trucking.index') }}" 
               class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Umum</h3>
                <div class="flex gap-2">
                    @can('master-pricelist-biaya-trucking-update')
                    <a href="{{ route('master.pricelist-biaya-trucking.edit', $pricelistBiayaTrucking) }}" 
                       class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200 transition">
                        Edit
                    </a>
                    @endcan
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Rute</h4>
                        <p class="text-gray-900 font-medium">{{ $pricelistBiayaTrucking->rute }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Tujuan Spesifik</h4>
                        <p class="text-gray-900">{{ $pricelistBiayaTrucking->tujuan ?? '-' }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Jenis Kendaraan</h4>
                        <p class="text-gray-900">{{ $pricelistBiayaTrucking->jenis_kendaraan ?? '-' }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Biaya</h4>
                        <p class="text-gray-900 font-medium text-lg">
                            Rp {{ number_format($pricelistBiayaTrucking->biaya, 0, ',', '.') }} 
                            <span class="text-sm font-normal text-gray-500">/ {{ $pricelistBiayaTrucking->satuan }}</span>
                        </p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Status</h4>
                        @if($pricelistBiayaTrucking->status === 'aktif')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 border border-green-200">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 border border-red-200">Non-Aktif</span>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Masa Berlaku</h4>
                        <p class="text-gray-900">
                            {{ $pricelistBiayaTrucking->tanggal_berlaku ? $pricelistBiayaTrucking->tanggal_berlaku->format('d M Y') : '-' }}
                            s/d
                            {{ $pricelistBiayaTrucking->tanggal_berakhir ? $pricelistBiayaTrucking->tanggal_berakhir->format('d M Y') : 'Seterusnya' }}
                        </p>
                    </div>
                    <div class="col-span-2">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Keterangan</h4>
                        <p class="text-gray-900 whitespace-pre-line">{{ $pricelistBiayaTrucking->keterangan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Sistem</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Dibuat Pada</h4>
                        <p class="text-gray-900">{{ $pricelistBiayaTrucking->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Diperbarui Pada</h4>
                        <p class="text-gray-900">{{ $pricelistBiayaTrucking->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
