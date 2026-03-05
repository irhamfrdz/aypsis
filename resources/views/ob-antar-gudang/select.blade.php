@extends('layouts.app')

@section('title', 'OB Antar Gudang - Pilih Gudang')
@section('page_title', 'OB Antar Gudang')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <i class="fas fa-warehouse mr-2 md:mr-3 text-teal-600 text-xl md:text-2xl"></i>
                <div>
                    <h1 class="text-lg md:text-2xl font-bold text-gray-800">OB Antar Gudang</h1>
                    <p class="text-xs md:text-base text-gray-600">Pilih gudang untuk melihat data kontainer</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-3 md:p-6">
        <form id="obAntarGudangForm" method="GET" action="{{ route('ob-antar-gudang.index') }}">
            <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-1 md:gap-6">
                <div>
                    <label for="gudang_id" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Gudang <span class="text-red-500">*</span></label>
                    <select id="gudang_id" name="gudang_id" class="w-full px-3 py-2.5 md:py-2 text-sm md:text-base border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required>
                        <option value="">--Pilih Gudang--</option>
                        @foreach($gudangs as $gudang)
                            <option value="{{ $gudang->id }}" {{ request('gudang_id') == $gudang->id ? 'selected' : '' }}>
                                {{ $gudang->nama_gudang }} {{ $gudang->lokasi ? '- ' . $gudang->lokasi : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 md:mt-6">
                <button type="submit" class="w-full md:w-auto bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 md:py-2 rounded-md text-sm md:text-base font-medium shadow-sm hover:shadow-md transition-all">
                    <i class="fas fa-arrow-right mr-2"></i>Lihat Data Kontainer
                </button>
            </div>
        </form>
    </div>

    <!-- Info Section -->
    <div class="bg-teal-50 rounded-lg border border-teal-200 p-3 md:p-4 mt-4 md:mt-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-teal-500 mr-2 md:mr-3 mt-0.5 md:mt-1 text-sm md:text-base"></i>
            <div>
                <h3 class="text-xs md:text-sm font-medium text-teal-900">Informasi OB Antar Gudang</h3>
                <p class="text-xs md:text-sm text-teal-700 mt-1">Fitur ini menampilkan data kontainer berdasarkan gudang yang dipilih:</p>
                <ul class="text-xs md:text-sm text-teal-700 mt-2 space-y-1">
                    <li>• <strong>Stock Kontainer:</strong> Data kontainer milik perusahaan di gudang yang dipilih</li>
                    <li>• <strong>Kontainer Sewa:</strong> Data kontainer sewa yang berada di gudang yang dipilih</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
