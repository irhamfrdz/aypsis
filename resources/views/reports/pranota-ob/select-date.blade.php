@extends('layouts.app')

@section('title', 'Report Pranota OB - Pilih Periode')
@section('page_title', 'Report Pranota OB - Pilih Periode')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-file-invoice mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Report Pranota OB</h1>
                    <p class="text-gray-600">Laporan pranota operasional bongkar berdasarkan periode</p>
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
                <p class="text-gray-600">Silakan pilih rentang tanggal untuk menampilkan laporan pranota OB.</p>
            </div>

            <form method="GET" action="{{ route('report.pranota-ob.view') }}" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dari Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="dari_tanggal" 
                               value="{{ old('dari_tanggal', now()->startOfMonth()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                               required>
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sampai Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="sampai_tanggal" 
                               value="{{ old('sampai_tanggal', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                               required>
                    </div>
                </div>

                {{-- Quick Date Buttons --}}
                <div class="border-t pt-6">
                    <p class="text-sm font-medium text-gray-700 mb-3">Pilihan Cepat:</p>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                        <button type="button" onclick="setToday()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition text-sm">
                            <i class="fas fa-calendar-day mr-1"></i> Hari Ini
                        </button>
                        <button type="button" onclick="setYesterday()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition text-sm">
                            <i class="fas fa-calendar-minus mr-1"></i> Kemarin
                        </button>
                        <button type="button" onclick="setThisWeek()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition text-sm">
                            <i class="fas fa-calendar-week mr-1"></i> Minggu Ini
                        </button>
                        <button type="button" onclick="setThisMonth()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition text-sm">
                            <i class="fas fa-calendar-alt mr-1"></i> Bulan Ini
                        </button>
                        <button type="button" onclick="setLastMonth()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition text-sm">
                            <i class="fas fa-calendar mr-1"></i> Bulan Lalu
                        </button>
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

@push('scripts')
<script>
    function setToday() {
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="dari_tanggal"]').value = today;
        document.querySelector('input[name="sampai_tanggal"]').value = today;
    }

    function setYesterday() {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const dateStr = yesterday.toISOString().split('T')[0];
        document.querySelector('input[name="dari_tanggal"]').value = dateStr;
        document.querySelector('input[name="sampai_tanggal"]').value = dateStr;
    }

    function setThisWeek() {
        const today = new Date();
        const firstDay = new Date(today.setDate(today.getDate() - today.getDay() + 1));
        const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 7));
        
        document.querySelector('input[name="dari_tanggal"]').value = firstDay.toISOString().split('T')[0];
        document.querySelector('input[name="sampai_tanggal"]').value = lastDay.toISOString().split('T')[0];
    }

    function setThisMonth() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        document.querySelector('input[name="dari_tanggal"]').value = firstDay.toISOString().split('T')[0];
        document.querySelector('input[name="sampai_tanggal"]').value = lastDay.toISOString().split('T')[0];
    }

    function setLastMonth() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
        
        document.querySelector('input[name="dari_tanggal"]').value = firstDay.toISOString().split('T')[0];
        document.querySelector('input[name="sampai_tanggal"]').value = lastDay.toISOString().split('T')[0];
    }
</script>
@endpush
@endsection
