@extends('layouts.app')

@section('title', 'Report Biaya Perijinan - Pilih Periode')
@section('page_title', 'Report Biaya Perijinan - Pilih Periode')

@section('content')
<div class="px-4 py-6">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:p-8">
        <div class="text-center mb-8">
            <i class="fas fa-calendar-alt text-indigo-600 text-4xl mb-3"></i>
            <h1 class="text-2xl font-semibold text-gray-800">Report Biaya Perijinan</h1>
            <p class="text-sm text-gray-500 mt-2">Pilih rentang tanggal pembayaran kendaraan KIR yang ingin ditampilkan.</p>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded-md bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="GET" action="{{ route('report.biaya-perijinan.view') }}" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari <span class="text-red-500">*</span></label>
                    <input id="tanggal_dari" name="tanggal_dari" type="date" value="{{ old('tanggal_dari', now()->startOfMonth()->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai <span class="text-red-500">*</span></label>
                    <input id="tanggal_sampai" name="tanggal_sampai" type="date" value="{{ old('tanggal_sampai', now()->endOfMonth()->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                    <i class="fas fa-search mr-2"></i>Tampilkan Report
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
