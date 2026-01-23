@extends('layouts.app')

@section('title', 'Detail Nama Stock Ban')
@section('page_title', 'Detail Nama Stock Ban')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Nama Stock Ban</h1>
                    <p class="mt-1 text-sm text-gray-600">Informasi lengkap nama stock ban</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('master.nama-stock-ban.edit', $namaStockBan->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('master.nama-stock-ban.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 space-y-6">
                <!-- Nama -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nama Stock Ban</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $namaStockBan->nama }}</p>
                </div>

                <!-- Status -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <div class="mt-1">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $namaStockBan->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $namaStockBan->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="pt-6 border-t border-gray-200 grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Dibuat Pada</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $namaStockBan->created_at ? $namaStockBan->created_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Terakhir Diupdate</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $namaStockBan->updated_at ? $namaStockBan->updated_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
