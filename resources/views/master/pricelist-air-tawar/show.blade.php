@extends('layouts.app')

@section('title', 'Detail Master Pricelist Air Tawar')
@section('page_title', 'Detail Master Pricelist Air Tawar')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Detail Master Pricelist Air Tawar</h1>
                            <p class="text-blue-100 text-sm">Informasi lengkap pricelist air tawar #{{ $pricelistAirTawar->id }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @can('master-pricelist-air-tawar-update')
                        <a href="{{ route('master.pricelist-air-tawar.edit', $pricelistAirTawar) }}" 
                           class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-6">
                    <div class="border-b border-gray-200 pb-4">
                        <dt class="text-sm font-medium text-gray-500 mb-2">ID</dt>
                        <dd class="text-lg font-semibold text-gray-900">#{{ $pricelistAirTawar->id }}</dd>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Nama Agen</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $pricelistAirTawar->nama_agen }}</dd>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Harga</dt>
                        <dd class="text-2xl font-bold text-green-600">{{ $pricelistAirTawar->formatted_harga }}</dd>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Keterangan</dt>
                        <dd class="text-base text-gray-900">{{ $pricelistAirTawar->keterangan ?? '-' }}</dd>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Tanggal Dibuat</dt>
                        <dd class="text-base text-gray-900">{{ $pricelistAirTawar->created_at->format('d M Y H:i') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">Terakhir Diupdate</dt>
                        <dd class="text-base text-gray-900">{{ $pricelistAirTawar->updated_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('master.pricelist-air-tawar.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>

                <div class="flex space-x-2">
                    @can('master-pricelist-air-tawar-update')
                    <a href="{{ route('master.pricelist-air-tawar.edit', $pricelistAirTawar) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Pricelist
                    </a>
                    @endcan

                    @can('master-pricelist-air-tawar-delete')
                    <form action="{{ route('master.pricelist-air-tawar.destroy', $pricelistAirTawar) }}" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist air tawar ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
