@extends('layouts.app')

@section('title', 'Report Ongkos Truk - Pilih Periode')
@section('page_title', 'Report Ongkos Truk - Pilih Periode')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-truck mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Report Ongkos Truk</h1>
                    <p class="text-gray-600">Laporan ongkos truk berdasarkan periode dan plat mobil</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Pilih Periode & Plat --}}
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <i class="fas fa-calendar-alt text-blue-600 text-6xl mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Pilih Periode & Kendaraan</h2>
                <p class="text-gray-600">Silakan pilih rentang tanggal dan plat mobil untuk menampilkan laporan.</p>
            </div>

            <form method="GET" action="{{ route('report.ongkos-truk.view') }}" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dari Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ old('start_date', now()->format('Y-m-d')) }}"
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

                {{-- Plat Mobil --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Plat Mobil (Bisa pilih lebih dari satu)
                    </label>
                    <select name="no_plat[]" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg select2" multiple="multiple">
                        @foreach($allPlats as $plat)
                            <option value="{{ $plat }}">{{ $plat }}</option>
                        @endforeach
                    </select>
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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 50px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endpush
@endsection
