@extends('layouts.app')

@section('title', 'Detail Pricelist Uang Jalan Batam')
@section('page_title', 'Detail Pricelist Uang Jalan Batam')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Detail Pricelist</h2>
            <p class="mt-1 text-sm text-gray-600">Informasi lengkap pricelist uang jalan Batam</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('pricelist-uang-jalan-batam.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Informasi Pricelist
        </h3>
    </div>
    
    <div class="px-6 py-5">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <!-- Expedisi -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Expedisi
                </dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">
                    {{ $pricelistUangJalanBatam->expedisi }}
                </dd>
            </div>

            <!-- Ring -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Ring
                </dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">
                    {{ $pricelistUangJalanBatam->ring }}
                </dd>
            </div>

            <!-- Size -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Size
                </dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">
                    {{ $pricelistUangJalanBatam->size }}
                </dd>
            </div>

            <!-- F/E -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    F/E (Full/Empty)
                </dt>
                <dd class="mt-1">
                    @if($pricelistUangJalanBatam->f_e == 'Full')
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-box mr-1"></i> Full
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-box-open mr-1"></i> Empty
                        </span>
                    @endif
                </dd>
            </div>

            <!-- Tarif -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Tarif
                </dt>
                <dd class="mt-1 text-lg text-gray-900 font-bold">
                    Rp {{ number_format($pricelistUangJalanBatam->tarif, 2, ',', '.') }}
                </dd>
            </div>

            <!-- Status -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Status
                </dt>
                <dd class="mt-1">
                    @if($pricelistUangJalanBatam->status)
                        @if($pricelistUangJalanBatam->status == 'AQUA')
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-tint mr-1"></i> AQUA
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-truck mr-1"></i> CHASIS PB
                            </span>
                        @endif
                    @else
                        <span class="text-sm text-gray-400 italic">Tidak ada status</span>
                    @endif
                </dd>
            </div>

            <!-- Created At -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Dibuat Pada
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $pricelistUangJalanBatam->created_at->format('d/m/Y H:i') }}
                </dd>
            </div>

            <!-- Updated At -->
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Terakhir Diubah
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $pricelistUangJalanBatam->updated_at->format('d/m/Y H:i') }}
                </dd>
            </div>
        </dl>
    </div>

    <!-- Action Buttons -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
        @can('master-pricelist-uang-jalan-batam-edit')
        <a href="{{ route('pricelist-uang-jalan-batam.edit', $pricelistUangJalanBatam) }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-edit mr-2"></i>
            Edit
        </a>
        @endcan

        @can('master-pricelist-uang-jalan-batam-delete')
        <form action="{{ route('pricelist-uang-jalan-batam.destroy', $pricelistUangJalanBatam) }}" 
              method="POST" 
              class="inline"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <i class="fas fa-trash mr-2"></i>
                Hapus
            </button>
        </form>
        @endcan
    </div>
</div>
@endsection
