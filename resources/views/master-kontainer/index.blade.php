@extends('layouts.app')

@section('title','Master Kontainer Sewa')
@section('page_title','Master Kontainer Sewa')

@section('content')

<h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Kontainer Sewa</h2>

{{-- Search and Filter Section --}}
<div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
    <form method="GET" action="{{ route('master.kontainer.index') }}" class="space-y-4">
        {{-- Preserve per_page parameter --}}
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
        
        <div class="flex flex-wrap gap-4 items-end">
            {{-- Search Input --}}
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                    Cari Kontainer
                </label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nomor kontainer..."
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            {{-- Vendor Filter --}}
            <div class="min-w-48">
                <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1">
                    Vendor
                </label>
                <select id="vendor"
                        name="vendor"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Semua Vendor</option>
                    @if(isset($vendors))
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor }}" {{ request('vendor') == $vendor ? 'selected' : '' }}>
                                {{ $vendor }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Ukuran Filter --}}
            <div class="min-w-32">
                <label for="ukuran" class="block text-sm font-medium text-gray-700 mb-1">
                    Ukuran
                </label>
                <select id="ukuran"
                        name="ukuran"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Semua Ukuran</option>
                    <option value="20" {{ request('ukuran') == '20' ? 'selected' : '' }}>20ft</option>
                    <option value="40" {{ request('ukuran') == '40' ? 'selected' : '' }}>40ft</option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="min-w-36">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Status
                </label>
                <select id="status"
                        name="status"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Tidak Tersedia" {{ request('status') == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
            </div>

            {{-- Tanggal Sewa Filter --}}
            <div class="min-w-48">
                <label for="tanggal_sewa" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Sewa
                </label>
                <select id="tanggal_sewa"
                        name="tanggal_sewa"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Semua Kontainer</option>
                    <option value="tanpa_tanggal_akhir" {{ request('tanggal_sewa') == 'tanpa_tanggal_akhir' ? 'selected' : '' }}>Tanpa Tanggal Akhir Sewa</option>
                    <option value="ada_tanggal_akhir" {{ request('tanggal_sewa') == 'ada_tanggal_akhir' ? 'selected' : '' }}>Ada Tanggal Akhir Sewa</option>
                    <option value="tanpa_tanggal_mulai" {{ request('tanggal_sewa') == 'tanpa_tanggal_mulai' ? 'selected' : '' }}>Tanpa Tanggal Mulai Sewa</option>
                    <option value="lengkap" {{ request('tanggal_sewa') == 'lengkap' ? 'selected' : '' }}>Tanggal Sewa Lengkap</option>
                </select>
            </div>

            {{-- Filter Buttons --}}
            <div class="flex space-x-2">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('master.kontainer.index', request()->only('per_page')) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </a>
            </div>
        </div>

        {{-- Active Filters Display --}}
        @if(request('search') || request('vendor') || request('ukuran') || request('status') || request('tanggal_sewa'))
            <div class="pt-3 border-t border-gray-200">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Filter aktif:</span>

                    @if(request('search'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Pencarian: "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => '']) }}" class="ml-1 text-blue-600 hover:text-blue-800">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif

                    @if(request('vendor'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Vendor: {{ request('vendor') }}
                            <a href="{{ request()->fullUrlWithQuery(['vendor' => '']) }}" class="ml-1 text-green-600 hover:text-green-800">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif

                    @if(request('ukuran'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Ukuran: {{ request('ukuran') }}ft
                            <a href="{{ request()->fullUrlWithQuery(['ukuran' => '']) }}" class="ml-1 text-purple-600 hover:text-purple-800">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif

                    @if(request('status'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Status: {{ request('status') }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}" class="ml-1 text-yellow-600 hover:text-yellow-800">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif

                    @if(request('tanggal_sewa'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            Filter: 
                            @switch(request('tanggal_sewa'))
                                @case('tanpa_tanggal_akhir')
                                    Tanpa Tanggal Akhir Sewa
                                    @break
                                @case('ada_tanggal_akhir')
                                    Ada Tanggal Akhir Sewa
                                    @break
                                @case('tanpa_tanggal_mulai')
                                    Tanpa Tanggal Mulai Sewa
                                    @break
                                @case('lengkap')
                                    Tanggal Sewa Lengkap
                                    @break
                                @default
                                    {{ request('tanggal_sewa') }}
                            @endswitch
                            <a href="{{ request()->fullUrlWithQuery(['tanggal_sewa' => '']) }}" class="ml-1 text-orange-600 hover:text-orange-800">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </form>
</div>

<div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-3">
        <!-- Download Template Button -->
        <a href="{{ route('master.kontainer.download-template') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Template (Terpisah)
        </a>

        <!-- Download Template Nomor Gabungan Button -->
        <a href="{{ route('master.kontainer.download-template-nomor-gabungan') }}"
           class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md shadow-sm text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Template (Gabungan)
        </a>

        <!-- Download Template Tanggal Sewa Button -->
        <a href="{{ route('master.kontainer.download-template-tanggal-sewa') }}"
           class="inline-flex items-center px-4 py-2 border border-purple-600 text-sm font-medium rounded-md shadow-sm text-purple-600 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Template Tanggal Sewa
        </a>

        <!-- Export CSV Button -->
        <a href="{{ route('master.kontainer.export', request()->query()) }}"
           class="inline-flex items-center px-4 py-2 border border-blue-600 text-sm font-medium rounded-md shadow-sm text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export CSV
        </a>

        <!-- Export Kontainer Dengan Tanggal Mulai Sewa Tanpa Tanggal Akhir Button -->
        <a href="{{ route('master.kontainer.export', array_merge(request()->query(), ['search' => '', 'vendor' => '', 'ukuran' => '', 'status' => '', 'tanggal_sewa' => 'tanpa_tanggal_akhir'])) }}"
           class="inline-flex items-center px-4 py-2 border border-red-600 text-sm font-medium rounded-md shadow-sm text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            Export Sewa Aktif Tanpa Tanggal Akhir
        </a>

        <!-- Export Kontainer Tidak Tersedia Button -->
        <a href="{{ route('master.kontainer.export', array_merge(request()->query(), ['search' => '', 'vendor' => '', 'ukuran' => '', 'status' => 'Tidak Tersedia', 'tanggal_sewa' => ''])) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
            </svg>
            Export Kontainer Tidak Tersedia
        </a>

        <!-- Import Button -->
        <button onclick="openImportModal()"
                class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md shadow-sm text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            Import (Terpisah)
        </button>

        <!-- Import Nomor Gabungan Button -->
        <button onclick="openImportNomorGabunganModal()"
                class="inline-flex items-center px-4 py-2 border border-teal-600 text-sm font-medium rounded-md shadow-sm text-teal-600 bg-white hover:bg-teal-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Import (Gabungan)
        </button>

        <!-- Import Tanggal Sewa Button -->
        <button onclick="openImportTanggalSewaModal()"
                class="inline-flex items-center px-4 py-2 border border-orange-600 text-sm font-medium rounded-md shadow-sm text-orange-600 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Import Tanggal Sewa
        </button>
    </div>

    <div>
        <a href="{{ route('master.kontainer.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Kontainer Baru
        </a>
    </div>
</div>

@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
    {{session('success')}}
</div>
@endif

@if (session('warning'))
<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-md mb-4" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">{{session('warning')}}</p>
        </div>
    </div>
</div>
@endif

{{-- Rows Per Page Selection --}}
@include('components.rows-per-page', [
    'routeName' => 'master.kontainer.index',
    'paginator' => $kontainers,
    'entityName' => 'kontainer',
    'entityNamePlural' => 'kontainer'
])

{{-- Results Summary --}}
@if($kontainers->count() > 0)
    <div class="mb-4 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Menampilkan {{ $kontainers->firstItem() }} sampai {{ $kontainers->lastItem() }}
            dari {{ $kontainers->total() }} kontainer
            @if(request('search') || request('vendor') || request('ukuran') || request('status') || request('tanggal_sewa'))
                (difilter)
            @endif
        </div>
        <div class="flex items-center space-x-4 text-sm">
            @if(request('search') || request('vendor') || request('ukuran') || request('status') || request('tanggal_sewa'))
                <div class="text-blue-600">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Export akan menggunakan filter yang aktif ({{ $kontainers->total() }} data)
                </div>
                <a href="{{ route('master.kontainer.index', request()->only('per_page')) }}" class="text-indigo-600 hover:text-indigo-800">
                    Lihat semua kontainer
                </a>
            @else
                <div class="text-green-600">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Export akan mengunduh semua data ({{ $kontainers->total() }} kontainer)
                </div>
            @endif
        </div>
    </div>
@endif

<div class="overflow-x-auto shadow-md sm:rounded-lg table-container">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
            <tr>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nomor Kontainer
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ukuran
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipe
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vendor
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tanggal Mulai Sewa
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tanggal Selesai Sewa
                </th>

                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>

                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($kontainers as $kontainer )
            <tr>
                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm font-medium text-gray-900">{{$kontainer->nomor_seri_gabungan}}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{$kontainer->ukuran}}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{$kontainer->tipe_kontainer}}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{$kontainer->vendor ?? '-'}}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">
                        {{ $kontainer->tanggal_mulai_sewa ? $kontainer->tanggal_mulai_sewa->format('d/M/Y') : '-' }}
                    </div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">
                        {{ $kontainer->tanggal_selesai_sewa ? $kontainer->tanggal_selesai_sewa->format('d/M/Y') : '-' }}
                    </div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    @php
                        // Hanya 2 status: Tersedia atau Tidak Tersedia
                        $displayStatus = 'Tersedia'; // Default
                        $statusClass = 'bg-green-100 text-green-800'; // Hijau untuk Tersedia

                        // Jika status bukan "Tersedia", maka "Tidak Tersedia"
                        if ($kontainer->status !== 'Tersedia') {
                            $displayStatus = 'Tidak Tersedia';
                            $statusClass = 'bg-red-100 text-red-800'; // Merah untuk Tidak Tersedia
                        }
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                        {{ $displayStatus }}
                    </span>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-3 text-[10px]">
                        <a href="{{route('master.kontainer.edit',$kontainer->id)}}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium" title="Edit Data">Edit</a>
                        <span class="text-gray-300">|</span>
                        @can('audit-log-view')
                            <button type="button" class="audit-log-btn text-purple-600 hover:text-purple-800 hover:underline font-medium"
                                    data-model-type="{{ get_class($kontainer) }}"
                                    data-model-id="{{ $kontainer->id }}"
                                    data-item-name="{{ $kontainer->nomor_seri_gabungan }}"
                                    title="Lihat Riwayat">
                                Riwayat
                            </button>
                        @endcan
                        <span class="text-gray-300">|</span>
                        <form action="{{route('master.kontainer.destroy',$kontainer->id)}}" method="POST" class="inline-block" onsubmit="return confirm('Apakah anda yakin ingin menghapus kontainer ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0" title="Hapus Data">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada data kontainer.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modern Pagination Design --}}
@include('components.modern-pagination', ['paginator' => $kontainers, 'routeName' => 'master.kontainer.index'])

{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeImportModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="importForm" action="{{ route('master.kontainer.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Import Data Kontainer
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload file CSV untuk mengimpor data kontainer secara bulk. 
                                    Template menyediakan <strong>format lengkap (10 kolom)</strong> dengan kolom opsional yang dapat dikosongkan.
                                </p>

                                <div class="mb-4">
                                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih File CSV
                                    </label>
                                    <input type="file"
                                           id="excel_file"
                                           name="excel_file"
                                           accept=".csv"
                                           required
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-800">Format File CSV (delimiter: semicolon):</h4>
                                            <div class="mt-1 text-sm text-blue-700">
                                                <div class="mb-2">
                                                    <strong>Template Dasar (5 kolom wajib):</strong>
                                                    <ul class="list-disc pl-5 space-y-1 mt-1">
                                                        <li>Kolom 1: Awalan Kontainer (4 karakter, contoh: ALLU)</li>
                                                        <li>Kolom 2: Nomor Seri (maks 6 digit, auto-pad dengan 0)</li>
                                                        <li>Kolom 3: Akhiran (1 karakter, kosong = "0")</li>
                                                        <li>Kolom 4: Ukuran (10, 20, atau 40)</li>
                                                        <li>Kolom 5: Vendor (ZONA atau DPE)</li>
                                                    </ul>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Template Lengkap (10 kolom total):</strong>
                                                    <ul class="list-disc pl-5 space-y-1 mt-1 text-xs">
                                                        <li>Kolom 6: Tipe Kontainer (default: "Dry Container")</li>
                                                        <li>Kolom 7: Tanggal Mulai Sewa (dd/mmm/yyyy)</li>
                                                        <li>Kolom 8: Tanggal Selesai Sewa (dd/mmm/yyyy)</li>
                                                        <li>Kolom 9: Keterangan (text)</li>
                                                        <li>Kolom 10: Status (Tersedia/Tidak Tersedia)</li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3 space-y-1">
                                                    <p class="text-xs text-blue-600 font-medium">
                                                        📝 Nomor Seri Gabungan: Auto-generated (Awalan+Nomor+Akhiran)
                                                    </p>
                                                    <p class="text-xs text-green-600 font-medium">
                                                        🏷️ Auto-set: Tipe="Dry Container", Status="Tersedia"
                                                    </p>
                                                    <p class="text-xs text-purple-600 font-medium">
                                                        🔄 Gate In & Checkpoint: Default "pending"
                                                    </p>
                                                    <p class="text-xs text-yellow-600 font-medium">
                                                        ⚠️ Duplikasi: Kontainer lama auto-nonaktif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span class="upload-text">Upload & Import</span>
                        <span class="upload-loading hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button type="button"
                            onclick="closeImportModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import Nomor Gabungan Modal --}}
<div id="importNomorGabunganModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeImportNomorGabunganModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="importNomorGabunganForm" action="{{ route('master.kontainer.import-nomor-gabungan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Import Data Kontainer (Format Nomor Gabungan)
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload file CSV dengan format nomor seri gabungan (11 karakter: ABCD123456X). 
                                    Sistem akan <strong>otomatis memecah</strong> menjadi awalan, nomor seri, dan akhiran.
                                </p>

                                <div class="mb-4">
                                    <label for="excel_file_gabungan" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih File CSV
                                    </label>
                                    <input type="file"
                                           id="excel_file_gabungan"
                                           name="excel_file"
                                           accept=".csv"
                                           required
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                                </div>

                                <div class="bg-teal-50 border border-teal-200 rounded-md p-3">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-teal-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-teal-800">Format File CSV (delimiter: semicolon):</h4>
                                            <div class="mt-1 text-sm text-teal-700">
                                                <div class="mb-2">
                                                    <strong>Kolom WAJIB:</strong>
                                                    <ul class="list-disc pl-5 space-y-1 mt-1">
                                                        <li>Kolom 1: Nomor Seri Gabungan (11 karakter, contoh: ALLU2202097)</li>
                                                        <li>Kolom 2: Ukuran (10, 20, atau 40)</li>
                                                        <li>Kolom 3: Vendor (ZONA atau DPE)</li>
                                                    </ul>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Kolom OPSIONAL:</strong>
                                                    <ul class="list-disc pl-5 space-y-1 mt-1 text-xs">
                                                        <li>Kolom 4: Tipe Kontainer (default: "Dry Container")</li>
                                                        <li>Kolom 5: Tanggal Mulai Sewa (dd/mmm/yyyy atau dd mmm yy)</li>
                                                        <li>Kolom 6: Tanggal Selesai Sewa (dd/mmm/yyyy atau dd mmm yy)</li>
                                                        <li>Kolom 7: Keterangan (text)</li>
                                                        <li>Kolom 8: Status (Tersedia/Tidak Tersedia, default: Tersedia)</li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3 space-y-1">
                                                    <p class="text-xs text-teal-600 font-medium">
                                                        🔄 ALLU2202097 → Awalan: ALLU, Nomor: 220209, Akhiran: 7
                                                    </p>
                                                    <p class="text-xs text-green-600 font-medium">
                                                        ✅ Otomatis Update jika nomor sudah ada
                                                    </p>
                                                    <p class="text-xs text-blue-600 font-medium">
                                                        ➕ Otomatis Create jika nomor belum ada
                                                    </p>
                                                    <p class="text-xs text-purple-600 font-medium">
                                                        📅 Support format: 01/Jan/2024 atau 01 Jan 24
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span class="upload-text-gabungan">Upload & Import</span>
                        <span class="upload-loading-gabungan hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button type="button"
                            onclick="closeImportNomorGabunganModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import Tanggal Sewa Modal --}}
<div id="importTanggalSewaModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeImportTanggalSewaModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="importTanggalSewaForm" action="{{ route('master.kontainer.import-tanggal-sewa') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Import Tanggal Sewa Kontainer
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload file CSV untuk mengupdate tanggal mulai sewa dan tanggal selesai sewa berdasarkan nomor kontainer.
                                </p>

                                <div class="mb-4">
                                    <label for="excel_file_tanggal_sewa" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih File CSV
                                    </label>
                                    <input type="file"
                                           id="excel_file_tanggal_sewa"
                                           name="excel_file"
                                           accept=".csv"
                                           required
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                </div>

                                <div class="bg-orange-50 border border-orange-200 rounded-md p-3">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-orange-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-orange-800">Format File CSV (delimiter: semicolon):</h4>
                                            <div class="mt-1 text-sm text-orange-700">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <li>Kolom 1: Nomor Kontainer Lengkap (11 karakter, contoh: ALLU2202097) - <strong>WAJIB</strong></li>
                                                    <li>Kolom 2: Tanggal Mulai Sewa (format: dd/Mmm/yyyy, contoh: 01/Jan/2024) - opsional</li>
                                                    <li>Kolom 3: Tanggal Selesai Sewa (format: dd/Mmm/yyyy, contoh: 31/Des/2024) - opsional</li>
                                                    <li>Kolom 4: Status (Tersedia atau Tidak Tersedia) - opsional</li>
                                                </ul>
                                                <div class="mt-3 space-y-1">
                                                    <p class="text-xs text-red-600 font-medium">
                                                        ⚠️ HANYA UPDATE data yang sudah ada - TIDAK MEMBUAT kontainer baru
                                                    </p>
                                                    <p class="text-xs text-orange-600 font-medium">
                                                        🔍 Pencarian berdasarkan nomor kontainer gabungan (exact match)
                                                    </p>
                                                    <p class="text-xs text-green-600 font-medium">
                                                        📅 Kolom opsional boleh kosong jika tidak ingin diupdate
                                                    </p>
                                                    <p class="text-xs text-blue-600 font-medium">
                                                        📄 Contoh update status saja: AMFU3153692;;;Tidak Tersedia
                                                    </p>
                                                    <p class="text-xs text-purple-600 font-medium">
                                                        ℹ️ Kontainer tidak ditemukan akan ditampilkan sebagai warning
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span class="upload-text-tanggal-sewa">Upload & Import</span>
                        <span class="upload-loading-tanggal-sewa hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button type="button"
                            onclick="closeImportTanggalSewaModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Import Modal Functions
    function openImportModal() {
        const modal = document.getElementById('importModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Reset form
            const form = document.getElementById('importForm');
            if (form) {
                form.reset();
            }

            // Reset button state
            const submitBtn = document.querySelector('#importForm button[type="submit"]');
            if (submitBtn) {
                const uploadText = submitBtn.querySelector('.upload-text');
                const uploadLoading = submitBtn.querySelector('.upload-loading');

                if (uploadText) uploadText.classList.remove('hidden');
                if (uploadLoading) uploadLoading.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }
    }

    // Make functions global for onclick handlers
    window.openImportModal = openImportModal;
    window.closeImportModal = closeImportModal;

    // Import Nomor Gabungan Modal Functions
    function openImportNomorGabunganModal() {
        const modal = document.getElementById('importNomorGabunganModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImportNomorGabunganModal() {
        const modal = document.getElementById('importNomorGabunganModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Reset form
            const form = document.getElementById('importNomorGabunganForm');
            if (form) {
                form.reset();
            }

            // Reset button state
            const submitBtn = document.querySelector('#importNomorGabunganForm button[type="submit"]');
            if (submitBtn) {
                const uploadText = submitBtn.querySelector('.upload-text-gabungan');
                const uploadLoading = submitBtn.querySelector('.upload-loading-gabungan');

                if (uploadText) uploadText.classList.remove('hidden');
                if (uploadLoading) uploadLoading.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }
    }

    window.openImportNomorGabunganModal = openImportNomorGabunganModal;
    window.closeImportNomorGabunganModal = closeImportNomorGabunganModal;

    // Import Tanggal Sewa Modal Functions
    function openImportTanggalSewaModal() {
        const modal = document.getElementById('importTanggalSewaModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImportTanggalSewaModal() {
        const modal = document.getElementById('importTanggalSewaModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Reset form
            const form = document.getElementById('importTanggalSewaForm');
            if (form) {
                form.reset();
            }

            // Reset button state
            const submitBtn = document.querySelector('#importTanggalSewaForm button[type="submit"]');
            if (submitBtn) {
                const uploadText = submitBtn.querySelector('.upload-text-tanggal-sewa');
                const uploadLoading = submitBtn.querySelector('.upload-loading-tanggal-sewa');

                if (uploadText) uploadText.classList.remove('hidden');
                if (uploadLoading) uploadLoading.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }
    }

    // Make tanggal sewa functions global
    window.openImportTanggalSewaModal = openImportTanggalSewaModal;
    window.closeImportTanggalSewaModal = closeImportTanggalSewaModal;

    // Handle form submission
    const importForm = document.getElementById('importForm');
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const uploadText = submitBtn.querySelector('.upload-text');
                const uploadLoading = submitBtn.querySelector('.upload-loading');

                // Show loading state
                if (uploadText) uploadText.classList.add('hidden');
                if (uploadLoading) uploadLoading.classList.remove('hidden');
                submitBtn.disabled = true;
            }
        });
    }

    // Handle form submission for nomor gabungan
    const importNomorGabunganForm = document.getElementById('importNomorGabunganForm');
    if (importNomorGabunganForm) {
        importNomorGabunganForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const uploadText = submitBtn.querySelector('.upload-text-gabungan');
                const uploadLoading = submitBtn.querySelector('.upload-loading-gabungan');

                // Show loading state
                if (uploadText) uploadText.classList.add('hidden');
                if (uploadLoading) uploadLoading.classList.remove('hidden');
                submitBtn.disabled = true;
            }
        });
    }

    // Handle form submission for tanggal sewa
    const importTanggalSewaForm = document.getElementById('importTanggalSewaForm');
    if (importTanggalSewaForm) {
        importTanggalSewaForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const uploadText = submitBtn.querySelector('.upload-text-tanggal-sewa');
                const uploadLoading = submitBtn.querySelector('.upload-loading-tanggal-sewa');

                // Show loading state
                if (uploadText) uploadText.classList.add('hidden');
                if (uploadLoading) uploadLoading.classList.remove('hidden');
                submitBtn.disabled = true;
            }
        });
    }

    // Close modal when clicking outside
    const importModal = document.getElementById('importModal');
    if (importModal) {
        importModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });
    }

    // Close nomor gabungan modal when clicking outside
    const importNomorGabunganModal = document.getElementById('importNomorGabunganModal');
    if (importNomorGabunganModal) {
        importNomorGabunganModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportNomorGabunganModal();
            }
        });
    }

    // Close tanggal sewa modal when clicking outside
    const importTanggalSewaModal = document.getElementById('importTanggalSewaModal');
    if (importTanggalSewaModal) {
        importTanggalSewaModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportTanggalSewaModal();
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('importModal');
            const tanggalSewaModal = document.getElementById('importTanggalSewaModal');
            
            if (modal && !modal.classList.contains('hidden')) {
                closeImportModal();
            }
            
            if (tanggalSewaModal && !tanggalSewaModal.classList.contains('hidden')) {
                closeImportTanggalSewaModal();
            }
        }
    });

    // File input validation
    const fileInput = document.getElementById('excel_file');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // Size in MB

                if (fileSize > 5) {
                    alert('File terlalu besar! Maksimal 5MB.');
                    e.target.value = '';
                    return;
                }

                if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                    alert('Format file tidak didukung! Gunakan file .csv');
                    e.target.value = '';
                    return;
                }
            }
        });
    }

    // File input validation for tanggal sewa
    const fileInputTanggalSewa = document.getElementById('excel_file_tanggal_sewa');
    if (fileInputTanggalSewa) {
        fileInputTanggalSewa.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // Size in MB

                if (fileSize > 5) {
                    alert('File terlalu besar! Maksimal 5MB.');
                    e.target.value = '';
                    return;
                }

                if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                    alert('Format file tidak didukung! Gunakan file .csv');
                    e.target.value = '';
                    return;
                }
            }
        });
    }

    // Export CSV functionality
    const exportButton = document.querySelector('a[href*="export"]');
    if (exportButton) {
        exportButton.addEventListener('click', function(e) {
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = `
                <svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengunduh...
            `;
            this.classList.add('pointer-events-none');

            // Reset after download starts (approximate)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('pointer-events-none');
            }, 3000);
        });
    }

    // Export CSV functionality for all export buttons
    const exportButtons = document.querySelectorAll('a[href*="export"]');
    exportButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = `
                <svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengunduh...
            `;
            this.classList.add('pointer-events-none');

            // Reset after download starts (approximate)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('pointer-events-none');
            }, 3000);
        });
    });

    // Auto-submit filter form when filter selections change
    const filterForm = document.querySelector('form[action*="master.kontainer.index"]');
    if (filterForm) {
        const filterSelects = filterForm.querySelectorAll('select[name="vendor"], select[name="ukuran"], select[name="status"], select[name="tanggal_sewa"]');

        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });

        // Handle Enter key in search input
        const searchInput = filterForm.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    filterForm.submit();
                }
            });
        }
    }
});
</script>

<style>
/* Sticky Table Header Styles */
.sticky-table-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251); /* bg-gray-50 */
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Enhanced table container for better scrolling */
.table-container {
    max-height: calc(100vh - 300px); /* Adjust based on your layout */
    overflow-y: auto;
    border: 1px solid rgb(229 231 235); /* border-gray-200 */
    border-radius: 0.5rem;
}

/* Smooth scrolling for better UX */
.table-container {
    scroll-behavior: smooth;
}

/* Table header cells need specific background to avoid transparency issues */
.sticky-table-header th {
    background-color: rgb(249 250 251) !important;
    border-bottom: 1px solid rgb(229 231 235);
}

/* Optional: Add a subtle border when scrolling */
.table-container.scrolled .sticky-table-header {
    border-bottom: 2px solid rgb(59 130 246); /* blue-500 */
}
</style>

<!-- Include Audit Log Modal -->
@include('components.audit-log-modal')
