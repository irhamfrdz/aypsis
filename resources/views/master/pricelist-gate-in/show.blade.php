@extends('layouts.app')

@section('title', 'Detail Pricelist Gate In')
@section('page_title', 'Detail Pricelist Gate In')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Detail Pricelist Gate In</h1>
                            <p class="text-blue-100 text-sm">{{ $pricelistGateIn->kode }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        @can('master-pricelist-gate-in-update')
                        <a href="{{ route('master.pricelist-gate-in.edit', $pricelistGateIn) }}"
                           class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        @endcan
                        <a href="{{ route('master.pricelist-gate-in.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Pricelist Gate In</h2>
                <p class="text-sm text-gray-600 mt-1">Detail lengkap pricelist gate in</p>
            </div>

            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <!-- Kode -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kode</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $pricelistGateIn->kode }}</dd>
                    </div>

                    <!-- Status -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $pricelistGateIn->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($pricelistGateIn->status) }}
                            </span>
                        </dd>
                    </div>

                    <!-- Tarif -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tarif</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $pricelistGateIn->formatted_tarif }}</dd>
                    </div>

                    <!-- Created At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $pricelistGateIn->created_at->format('d M Y H:i') }}</dd>
                    </div>

                    <!-- Updated At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $pricelistGateIn->updated_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>

                <!-- Keterangan -->
                @if($pricelistGateIn->keterangan)
                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                    <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $pricelistGateIn->keterangan }}</dd>
                </div>
                @endif

                <!-- Catatan -->
                @if($pricelistGateIn->catatan)
                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                    <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $pricelistGateIn->catatan }}</dd>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
