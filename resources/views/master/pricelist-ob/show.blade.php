@extends('layouts.app')

@section('title', 'Detail Master Pricelist OB')
@section('page_title', 'Detail Master Pricelist OB')

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
                            <h1 class="text-2xl font-bold text-white">Detail Master Pricelist OB</h1>
                            <p class="text-blue-100 text-sm">Pricelist OB #{{ $pricelistOb->id }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @can('master-pricelist-ob-update')
                        <a href="{{ route('master.pricelist-ob.edit', $pricelistOb) }}" 
                           class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        @endcan
                        <a href="{{ route('master.pricelist-ob.index') }}" 
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
                <h2 class="text-lg font-semibold text-gray-900">Informasi Pricelist OB</h2>
                <p class="text-sm text-gray-600 mt-1">Detail lengkap pricelist OB yang dipilih</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informasi Dasar -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Dasar</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID Pricelist</label>
                            <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                #{{ $pricelistOb->id }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer</label>
                            <div class="text-sm text-gray-900">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $pricelistOb->size_kontainer_label }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Kontainer</label>
                            <div class="text-sm text-gray-900">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $pricelistOb->status_kontainer === 'full' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ $pricelistOb->status_kontainer_label }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Biaya</label>
                            <div class="text-lg font-semibold text-gray-900 bg-green-50 px-3 py-2 rounded-md border border-green-200">
                                {{ $pricelistOb->formatted_biaya }}
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Tambahan -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Tambahan</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md min-h-[80px]">
                                {{ $pricelistOb->keterangan ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dibuat</label>
                            <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                {{ $pricelistOb->created_at ? $pricelistOb->created_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Diperbarui</label>
                            <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                {{ $pricelistOb->updated_at ? $pricelistOb->updated_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        @canany(['master-pricelist-ob-update', 'master-pricelist-ob-delete'])
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi</h3>
                <div class="flex space-x-4">
                    @can('master-pricelist-ob-update')
                    <a href="{{ route('master.pricelist-ob.edit', $pricelistOb) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Pricelist OB
                    </a>
                    @endcan
                    
                    @can('master-pricelist-ob-delete')
                    <form action="{{ route('master.pricelist-ob.destroy', $pricelistOb) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus pricelist OB ini? Tindakan ini tidak dapat dibatalkan.')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Pricelist OB
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany

        <!-- Info Panel -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Catatan Penting</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pricelist ini digunakan untuk menghitung biaya OB berdasarkan size dan status kontainer</li>
                            <li>Perubahan biaya akan mempengaruhi perhitungan transaksi selanjutnya</li>
                            <li>Pastikan data akurat sebelum digunakan dalam operasional</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection