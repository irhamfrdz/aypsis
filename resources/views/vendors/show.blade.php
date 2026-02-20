@extends('layouts.app')

@section('title', 'Detail Vendor')
@section('page_title', 'Detail Vendor')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Detail Vendor</h1>
                            <p class="text-blue-100 text-sm">Informasi lengkap data vendor</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('master.vendors.edit', $vendor) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            Edit
                        </a>
                        <a href="{{ route('master.vendors.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Nama Vendor</h3>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $vendor->nama_vendor }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Tipe Hitung</h3>
                        <p class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vendor->tipe_hitung == 'bulanan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($vendor->tipe_hitung) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Dibuat Pada</h3>
                        <p class="mt-1 text-gray-900">{{ $vendor->created_at->format('d F Y H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Terakhir Diperbarui</h3>
                        <p class="mt-1 text-gray-900">{{ $vendor->updated_at->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
