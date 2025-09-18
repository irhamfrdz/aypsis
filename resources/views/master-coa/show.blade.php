@extends('layouts.app')

@section('title', 'Detail COA')
@section('page_title', 'Detail COA')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail COA</h1>
                    <p class="mt-1 text-sm text-gray-600">Informasi lengkap Chart of Accounts (COA)</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('master-coa-edit', $coa) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('master-coa-index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            <!-- Account Number -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">No. Akun</h3>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xl font-bold bg-blue-100 text-blue-800">
                    {{ $coa->nomor_akun }}
                </span>
            </div>

            <!-- Account Name -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">Nama Akun</h3>
                </div>
                <p class="text-xl font-semibold text-gray-900">{{ $coa->nama_akun }}</p>
            </div>

            <!-- Account Type -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">Tipe Akun</h3>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-lg font-semibold
                    @if($coa->tipe_akun === 'Aset') bg-green-100 text-green-800
                    @elseif($coa->tipe_akun === 'Kewajiban') bg-red-100 text-red-800
                    @elseif($coa->tipe_akun === 'Ekuitas') bg-blue-100 text-blue-800
                    @elseif($coa->tipe_akun === 'Pendapatan') bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $coa->tipe_akun }}
                </span>
            </div>

            <!-- Balance -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">Saldo</h3>
                </div>
                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($coa->saldo, 2, ',', '.') }}</p>
            </div>

        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Created At -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="text-sm font-medium text-gray-900">Dibuat Pada</h4>
                    </div>
                    <p class="text-sm text-gray-600">{{ $coa->created_at->format('d M Y, H:i') }}</p>
                </div>

                <!-- Updated At -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <h4 class="text-sm font-medium text-gray-900">Diupdate Pada</h4>
                    </div>
                    <p class="text-sm text-gray-600">{{ $coa->updated_at->format('d M Y, H:i') }}</p>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
