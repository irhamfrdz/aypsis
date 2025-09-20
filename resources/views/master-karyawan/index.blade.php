@extends('layouts.app')

@section('title', 'Master Karyawan')
@section('page_title', 'Master Karyawan')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Header Section -->
        <div class="px-6 py-4 border-b bg-white">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-xl font-semibold text-gray-900">Daftar Karyawan</h2>

                <!-- Search Box -->
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <form method="GET" action="{{ route('master.karyawan.index') }}" class="flex-1 sm:flex-initial">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="block w-full sm:w-80 pl-10 pr-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                   placeholder="Cari nama, NIK, divisi, pekerjaan..."
                                   autocomplete="off">
                            <!-- Tombol submit manual -->
                            <button type="submit" class="absolute inset-y-0 right-8 flex items-center px-2 text-gray-400 hover:text-blue-600" title="Cari">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                            @if(request('search'))
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <a href="{{ route('master.karyawan.index') }}" class="text-gray-400 hover:text-gray-600" title="Hapus filter">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    <!-- Action Buttons -->
                    <div class="flex flex-nowrap gap-2 items-center">
                        <a href="{{ route('master.karyawan.create') }}"
                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah
                        </a>

                        <!-- Template Dropdown -->
                        <div class="relative group">
                            <button class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Template
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 top-full mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden">
                                <div class="py-2">
                                    <a href="{{ route('master.karyawan.template') }}"
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <div class="font-medium">Template CSV</div>
                                            <div class="text-xs text-gray-500">Format CSV standar</div>
                                        </div>
                                    </a>
                                    <a href="{{ route('master.karyawan.simple-excel-template') }}"
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <div class="font-medium">Template Excel</div>
                                            <div class="text-xs text-gray-500">Kompatibel dengan Excel</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('master.karyawan.print') }}"
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Cetak
                        </a>

                        <!-- Export Dropdown -->
                        <div class="relative group">
                            <button class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                Export
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 top-full mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden">
                                <div class="py-2">
                                    <a href="{{ route('master.karyawan.export') }}?sep=%3B"
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <div class="font-medium">Export CSV</div>
                                            <div class="text-xs text-gray-500">Format CSV dengan separator ;</div>
                                        </div>
                                    </a>
                                    <a href="{{ route('master.karyawan.export-excel') }}"
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <div class="font-medium">Export Excel</div>
                                            <div class="text-xs text-gray-500">Anti scientific notation</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('master.karyawan.import') }}"
                           class="inline-flex items-center px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Import
                        </a>
                    </div>
                </div>
            </div>

            @if(request('search'))
                <div class="mt-3 flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                    <a href="{{ route('master.karyawan.index') }}" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                        Hapus filter
                    </a>
                </div>
            @endif

            {{-- Rows Per Page Selection --}}
            @include('components.rows-per-page', [
                'routeName' => 'master.karyawan.index',
                'paginator' => $karyawans,
                'entityName' => 'karyawan',
                'entityNamePlural' => 'karyawan'
            ])
        </div>

        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        {!! nl2br(e(session('success'))) !!}
                    </div>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="mx-6 mt-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Import Selesai dengan Peringatan</div>
                        <div class="text-sm">
                            {!! nl2br(e(session('warning'))) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium mb-1">Import Gagal</div>
                        <div class="text-sm">
                            {!! nl2br(e(session('error'))) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Info for CSV Import -->
        <div class="mx-6 mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-blue-900 mb-1">Cara Import Data Karyawan:</h3>
                    <div class="text-sm text-blue-800">
                        <span class="inline-flex items-center bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-medium mr-2">1</span>
                        <a href="{{ route('master.karyawan.template') }}" class="text-blue-600 hover:text-blue-800 underline font-medium">Download Template CSV</a>
                        atau
                        <a href="{{ route('master.karyawan.excel-template') }}" class="text-blue-600 hover:text-blue-800 underline font-medium">Template Excel</a>
                        <span class="mx-2">→</span>
                        <span class="inline-flex items-center bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-medium mr-2">2</span>
                        Isi data karyawan
                        <span class="mx-2">→</span>
                        <span class="inline-flex items-center bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-medium mr-2">3</span>
                        <a href="{{ route('master.karyawan.import') }}" class="text-blue-600 hover:text-blue-800 underline font-medium">Upload file CSV/Excel</a>
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="flex-shrink-0 text-blue-400 hover:text-blue-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Table Section with Sticky Header -->
        <div class="table-container overflow-x-auto max-h-screen">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <span>NO.</span>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>NIK</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'nik', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'nik' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan A-Z">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'nik', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'nik' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Z-A">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>NAMA LENGKAP</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'nama_lengkap', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'nama_lengkap' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan A-Z">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'nama_lengkap', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'nama_lengkap' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Z-A">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>NAMA PANGGILAN</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'nama_panggilan', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'nama_panggilan' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan A-Z">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'nama_panggilan', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'nama_panggilan' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Z-A">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>DIVISI</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'divisi', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'divisi' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan A-Z">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'divisi', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'divisi' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Z-A">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>PEKERJAAN</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'pekerjaan', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'pekerjaan' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan A-Z">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'pekerjaan', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'pekerjaan' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Z-A">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>NO HP</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'no_hp', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'no_hp' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan A-Z">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'no_hp', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'no_hp' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Z-A">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>TANGGAL MASUK</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'tanggal_masuk', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'tanggal_masuk' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Terlama">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.index', array_merge(request()->query(), ['sort' => 'tanggal_masuk', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'tanggal_masuk' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Terbaru">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($karyawans as $karyawan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 font-medium">
                                {{ ($karyawans->currentPage() - 1) * $karyawans->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">{{ strtoupper($karyawan->nik) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">{{ strtoupper($karyawan->nama_lengkap) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">{{ strtoupper($karyawan->nama_panggilan) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                                <span class="inline-flex px-2 py-1 text-[10px] font-medium rounded-md
                                    {{ strtolower($karyawan->divisi) === 'it' ? 'bg-blue-100 text-blue-800' :
                                       (strtolower($karyawan->divisi) === 'abk' ? 'bg-blue-100 text-blue-800' :
                                       (strtolower($karyawan->divisi) === 'supir' ? 'bg-gray-100 text-gray-800' :
                                       'bg-gray-100 text-gray-800')) }}">
                                    {{ strtoupper($karyawan->divisi) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">{{ strtoupper($karyawan->pekerjaan) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">{{ strtoupper($karyawan->no_hp) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-3 text-[10px]">
                                    {{-- Show crew checklist links only for ABK division --}}
                                    @if(strtolower($karyawan->divisi) === 'abk')
                                        <a href="{{ route('master.karyawan.crew-checklist', $karyawan->id) }}"
                                           class="text-purple-600 hover:text-purple-800 hover:underline font-medium"
                                           title="Checklist Kelengkapan Crew">
                                            Checklist
                                        </a>
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('master.karyawan.crew-checklist-new', $karyawan->id) }}"
                                           class="text-green-600 hover:text-green-800 hover:underline font-medium"
                                           title="Checklist Baru (Simplified)">
                                            New
                                        </a>
                                        <span class="text-gray-300">|</span>
                                    @endif

                                    <!-- View Link -->
                                    <a href="{{ route('master.karyawan.show', $karyawan->id) }}"
                                       class="text-blue-600 hover:text-blue-800 hover:underline font-medium"
                                       title="Lihat Detail">
                                        Lihat
                                    </a>
                                    <span class="text-gray-300">|</span>

                                    <!-- Print Link -->
                                    <a href="{{ route('master.karyawan.print.single', $karyawan->id) }}"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-800 hover:underline font-medium"
                                       title="Cetak Data">
                                        Cetak
                                    </a>
                                    <span class="text-gray-300">|</span>

                                    <!-- Edit Link -->
                                    <a href="{{ route('master.karyawan.edit', $karyawan->id) }}"
                                       class="text-amber-600 hover:text-amber-800 hover:underline font-medium"
                                       title="Edit Data">
                                        Edit
                                    </a>
                                    <span class="text-gray-300">|</span>

                                    <!-- Delete Link -->
                                    <button type="button"
                                            onclick="openDeleteModal('{{ $karyawan->id }}', '{{ $karyawan->nik }}', '{{ $karyawan->nama_lengkap }}')"
                                            class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer"
                                            title="Hapus Data">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-2 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium">Belum ada data karyawan</p>
                                    <p class="text-xs mt-1">Tambah karyawan baru untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modern Pagination Design -->
        @include('components.modern-pagination', ['paginator' => $karyawans, 'routeName' => 'master.karyawan.index'])
    </div>
</div>

<!-- Delete Confirmation Modal - Enhanced Version -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-md shadow-2xl rounded-xl bg-white transform transition-all duration-300">
        <!-- Header with close button -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
            </div>
            <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Warning Message -->
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-900 mb-2">Yakin ingin menghapus karyawan ini?</p>
                <p class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
            </div>

            <!-- Employee Info Card -->
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-red-500 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="grid grid-cols-2 gap-y-2 text-sm">
                            <div>
                                <span class="font-medium text-red-700">NIK:</span>
                            </div>
                            <div>
                                <span id="deleteModalNik" class="font-semibold text-red-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-red-700">Nama:</span>
                            </div>
                            <div>
                                <span id="deleteModalNama" class="font-semibold text-red-900"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning Notice -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-amber-800">Peringatan Penting!</h4>
                        <div class="mt-1 text-sm text-amber-700">
                            <ul class="list-disc ml-4 space-y-1">
                                <li>Data karyawan akan dihapus <strong>permanen</strong></li>
                                <li>Semua riwayat dan dokumen terkait akan hilang</li>
                                <li>Pastikan backup data sudah tersedia</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl border-t border-gray-200">
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0">
                <!-- Cancel Button -->
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </button>

                <!-- Delete Button -->
                <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Ya, Hapus Karyawan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div><script>
// Enhanced Delete Modal Functions
function openDeleteModal(karyawanId, nik, namaLengkap) {
    // Set form action
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/master/karyawan/${karyawanId}`;

    // Set modal content
    document.getElementById('deleteModalNik').textContent = nik || '-';
    document.getElementById('deleteModalNama').textContent = namaLengkap || '-';

    // Show modal with animation
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Animate modal appearance
    setTimeout(() => {
        modal.querySelector('.relative').style.transform = 'scale(1)';
        modal.querySelector('.relative').style.opacity = '1';
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('.relative');

    // Animate modal disappearance
    modalContent.style.transform = 'scale(0.95)';
    modalContent.style.opacity = '0';

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 200);
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;

    // Auto-submit form setelah user berhenti mengetik selama 500ms
    if (searchInput) {
        const searchForm = searchInput.closest('form');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
                    if (searchForm) searchForm.submit();
                }
            }, 500);
        });

        // Submit langsung saat Enter ditekan
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                if (searchForm) searchForm.submit();
            }
        });
    }

    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.relative.group');

    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('button');
        const menu = dropdown.querySelector('.absolute');

        if (button && menu) {
            // Toggle dropdown on button click
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Close other dropdowns
                dropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        const otherMenu = otherDropdown.querySelector('.absolute');
                        if (otherMenu) {
                            otherMenu.classList.add('opacity-0', 'invisible');
                            otherMenu.classList.remove('opacity-100', 'visible');
                        }
                    }
                });

                // Toggle current dropdown
                menu.classList.toggle('opacity-0');
                menu.classList.toggle('opacity-100');
                menu.classList.toggle('invisible');
                menu.classList.toggle('visible');
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector('.absolute');
            if (menu && !dropdown.contains(e.target)) {
                menu.classList.add('opacity-0', 'invisible');
                menu.classList.remove('opacity-100', 'visible');
            }
        });
    });

    // Close dropdowns on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdowns.forEach(dropdown => {
                const menu = dropdown.querySelector('.absolute');
                if (menu) {
                    menu.classList.add('opacity-0', 'invisible');
                    menu.classList.remove('opacity-100', 'visible');
                }
            });
        }
    });

    // Sticky Header Enhancement
    const tableContainer = document.querySelector('.table-container');
    const stickyHeader = document.querySelector('.sticky-table-header');

    if (tableContainer && stickyHeader) {
        // Add scroll event listener for visual feedback
        tableContainer.addEventListener('scroll', function() {
            if (tableContainer.scrollTop > 0) {
                tableContainer.classList.add('scrolled');
            } else {
                tableContainer.classList.remove('scrolled');
            }
        });

        // Optional: Add smooth scroll to top button
        const scrollToTopBtn = document.createElement('button');
        scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        scrollToTopBtn.className = 'fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 opacity-0 invisible z-50';
        scrollToTopBtn.title = 'Scroll ke atas';
        document.body.appendChild(scrollToTopBtn);

        // Show/hide scroll to top button
        tableContainer.addEventListener('scroll', function() {
            if (tableContainer.scrollTop > 200) {
                scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                scrollToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'invisible');
                scrollToTopBtn.classList.remove('opacity-100', 'visible');
            }
        });

        // Scroll to top functionality
        scrollToTopBtn.addEventListener('click', function() {
            tableContainer.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
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

/* Ensure dropdown menus appear above sticky header */
.relative.group .absolute {
    z-index: 20;
}

/* Enhanced Pagination Styles */
.pagination-links .page-link {
    @apply inline-flex items-center px-2.5 py-1.5 text-sm font-medium transition-colors duration-200 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900;
}

.pagination-links .page-link.active {
    @apply bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700;
}

.pagination-links .page-link.disabled {
    @apply opacity-50 cursor-not-allowed pointer-events-none;
}

.pagination-links .page-item:first-child .page-link {
    @apply rounded-l-md;
}

.pagination-links .page-item:last-child .page-link {
    @apply rounded-r-md;
}

.pagination-links .page-item:not(:first-child):not(:last-child) .page-link {
    @apply border-l-0;
}
</style>
@endsection
