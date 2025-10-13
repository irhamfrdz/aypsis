@extends('layouts.app')

@section('title', 'Import Data Transportasi')
@section('page_title', 'Import Data Transportasi')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Import Data Transportasi</h2>
            <a href="{{ route('master.tujuan-kegiatan-utama.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        <!-- Simple Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
            <h3 class="text-sm font-medium text-blue-800 mb-2">Petunjuk Import</h3>
            <div class="text-sm text-blue-700">
                <p>1. Download template CSV atau gunakan file CSV yang sudah ada</p>
                <p>2. Pastikan kolom "Dari" dan "Ke" terisi</p>
                <p>3. Upload file CSV (max 10MB)</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('errors') && is_array(session('errors')))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <h4 class="font-bold">Error saat import:</h4>
                <ul class="list-disc list-inside mt-2">
                    @foreach (session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (isset($errors) && is_object($errors) && $errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <h4 class="font-bold">Terjadi kesalahan:</h4>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Simple Import Form -->
        <form action="{{ route('master.tujuan-kegiatan-utama.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- File Upload Section -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <div class="mb-4">
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File CSV
                    </label>
                    <input 
                        id="csv_file" 
                        name="csv_file" 
                        type="file" 
                        accept=".csv,.txt" 
                        required 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                </div>
                <p class="text-xs text-gray-500">Mendukung format CSV dengan delimiter koma (,) atau titik koma (;)</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('master.tujuan-kegiatan-utama.download-template') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Template
                </a>
                
                <div class="flex space-x-3">
                    <a href="{{ route('master.tujuan-kegiatan-utama.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Import Data
                    </button>
                </div>
            </div>
        </form>

        <!-- Quick Format Guide -->
        <div class="mt-8 bg-gray-50 rounded-lg p-4">
            <h3 class="text-md font-medium text-gray-900 mb-3">Format yang Didukung</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-white p-3 rounded border">
                    <h4 class="font-medium text-gray-800">Delimiter</h4>
                    <p class="text-gray-600">Koma (,) atau Titik Koma (;)</p>
                </div>
                <div class="bg-white p-3 rounded border">
                    <h4 class="font-medium text-gray-800">Format Angka</h4>
                    <p class="text-gray-600">1500000 atau 1.500.000</p>
                </div>
                <div class="bg-white p-3 rounded border">
                    <h4 class="font-medium text-gray-800">Kolom Wajib</h4>
                    <p class="text-gray-600">Dari dan Ke</p>
                </div>
            </div>
        </div>
    </div>
@endsection