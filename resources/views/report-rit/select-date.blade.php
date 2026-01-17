@extends('layouts.app')

@section('title', 'Report Rit - Pilih Periode')
@section('page_title', 'Report Rit - Pilih Periode')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-chart-line mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Report Rit</h1>
                    <p class="text-gray-600">Laporan surat jalan berdasarkan periode</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Pilih Periode --}}
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <i class="fas fa-calendar-alt text-blue-600 text-6xl mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Pilih Periode Tanggal</h2>
                <p class="text-gray-600">Silakan pilih rentang tanggal untuk menampilkan laporan surat jalan yang memiliki <strong>tanggal checkpoint</strong> atau <strong>tanda terima</strong>.</p>
            </div>

            <form method="GET" action="{{ route('report.rit.view') }}" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dari Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ old('start_date', now()->subDays(30)->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                               required>
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sampai Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="end_date" 
                               value="{{ old('end_date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                               required>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-center gap-4 pt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-md transition duration-200 inline-flex items-center text-lg font-medium">
                        <i class="fas fa-search mr-2"></i>
                        Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
