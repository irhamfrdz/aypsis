@extends('layouts.app')

@section('title', 'Detail Master Pricelist Freight')
@section('page_title', 'Detail Master Pricelist Freight')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-5" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('master-pricelist-freight.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-purple-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Pricelist Freight
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail Data #{{ $masterPricelistFreight->id }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Detail Pricelist Freight</h3>
                <div class="flex space-x-2">
                    @can('master-pricelist-freight-update')
                    <a href="{{ route('master-pricelist-freight.edit', $masterPricelistFreight) }}" class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-xs font-medium rounded-md transition duration-150 ease-in-out border border-white border-opacity-30">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    @endcan
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pelabuhan Asal</h4>
                            <p class="text-base font-medium text-gray-900 bg-gray-50 p-2 rounded border border-gray-100 italic">{{ $masterPricelistFreight->asal->nama_pelabuhan ?? '-' }}</p>
                        </div>

                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pelabuhan Tujuan</h4>
                            <p class="text-base font-medium text-gray-900 bg-gray-50 p-2 rounded border border-gray-100 italic">{{ $masterPricelistFreight->tujuan->nama_pelabuhan ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Size Kontainer</h4>
                            <div>
                                <span class="inline-flex px-3 py-1 text-sm font-bold rounded-full bg-purple-100 text-purple-800">
                                    {{ $masterPricelistFreight->size_kontainer_label }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Biaya (Freight)</h4>
                            <p class="text-xl font-bold text-gray-900 bg-green-50 p-2 rounded border border-green-100 text-green-700">{{ $masterPricelistFreight->formatted_biaya }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Keterangan</h4>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 min-h-[100px]">
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $masterPricelistFreight->keterangan ?? 'Tidak ada keterangan.' }}</p>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="mt-8 grid grid-cols-2 gap-4 text-xs text-gray-500 border-t border-gray-100 pt-6">
                    <div>
                        <p>Dibuat pada: <span class="font-medium">{{ $masterPricelistFreight->created_at ? $masterPricelistFreight->created_at->format('d M Y H:i:s') : '-' }}</span></p>
                    </div>
                    <div class="text-right">
                        <p>Terakhir diperbarui: <span class="font-medium">{{ $masterPricelistFreight->updated_at ? $masterPricelistFreight->updated_at->format('d M Y H:i:s') : '-' }}</span></p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-center">
                <a href="{{ route('master-pricelist-freight.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
