@extends('layouts.app')

@section('title', 'Report Uang Jalan - Pilih Periode')
@section('page_title', 'Report Uang Jalan - Pilih Periode')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border-l-4 border-amber-500">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-money-bill-wave mr-3 text-amber-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Report Uang Jalan</h1>
                    <p class="text-gray-600">Laporan rincian uang jalan driver berdasarkan periode</p>
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
    <div class="bg-white rounded-lg shadow-sm p-8 max-w-4xl mx-auto border border-gray-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-50 rounded-full mb-4">
                <i class="fas fa-calendar-alt text-amber-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pilih Periode Tanggal</h2>
            <p class="text-gray-600">Silakan pilih rentang tanggal untuk menampilkan seluruh data <strong>Uang Jalan</strong> yang telah dibuat.</p>
        </div>

        <form method="GET" action="{{ route('report.uang-jalan.view') }}" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                {{-- Start Date --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Dari Tanggal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="date" 
                               name="start_date" 
                               id="start_date"
                               value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-lg transition duration-200"
                               required>
                    </div>
                </div>

                {{-- End Date --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Sampai Tanggal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="date" 
                               name="end_date" 
                               id="end_date"
                               value="{{ old('end_date', now()->endOfMonth()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-lg transition duration-200"
                               required>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex flex-col items-center gap-4 pt-6">
                <button type="submit" 
                        style="background-color: #d97706 !important; color: white !important;"
                        class="w-full md:w-auto hover:bg-amber-700 text-white px-10 py-4 rounded-lg shadow-lg hover:shadow-xl transform transition duration-200 hover:-translate-y-0.5 inline-flex items-center justify-center text-lg font-bold">
                    <i class="fas fa-search mr-2"></i>
                    Tampilkan Laporan Uang Jalan
                </button>
                <p class="text-xs text-gray-400">Pastikan rentang tanggal sudah benar sebelum menekan tombol cari.</p>
            </div>
        </form>
    </div>

</div>
@endsection
