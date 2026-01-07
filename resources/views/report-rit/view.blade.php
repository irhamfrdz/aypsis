@extends('layouts.app')

@section('title', 'Report Rit')
@section('page_title', 'Report Rit')

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
            <a href="{{ route('report.rit.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Pilih Periode Lain
            </a>
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

    {{-- Form Filter Tanggal --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-filter mr-2"></i>Filter & Pencarian
        </h3>
        
        <form method="GET" action="{{ route('report.rit.view') }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="No SJ, supir, plat..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Supir Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supir</label>
                    <input type="text"
                           name="supir"
                           value="{{ request('supir') }}"
                           placeholder="Nama supir..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Kegiatan Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kegiatan</label>
                    <select name="kegiatan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kegiatan</option>
                        <option value="muat" {{ request('kegiatan') == 'muat' ? 'selected' : '' }}>Muat</option>
                        <option value="bongkar" {{ request('kegiatan') == 'bongkar' ? 'selected' : '' }}>Bongkar</option>
                    </select>
                </div>

                {{-- Per Page --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per Halaman</label>
                    <select name="per_page"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                        <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('report.rit.view', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Reset Filter
                </a>
                <a href="{{ route('report.rit.print', request()->all()) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </a>
                <a href="{{ route('report.rit.export', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <i class="fas fa-file-alt text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Surat Jalan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $suratJalans->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <i class="fas fa-arrow-up text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Muat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $suratJalans->where('kegiatan', 'muat')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-full p-3">
                    <i class="fas fa-arrow-down text-2xl text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Bongkar</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $suratJalans->where('kegiatan', 'bongkar')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                    <i class="fas fa-calendar-alt text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Periode</p>
                    <p class="text-sm font-bold text-gray-900">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratJalans as $key => $sj)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $suratJalans->firstItem() + $key }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $sj->tanggal ? $sj->tanggal->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $sj->nomor_surat_jalan ?: '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sj->kegiatan == 'muat' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ ucfirst($sj->kegiatan) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->nama_supir ?: '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $sj->no_plat ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate" title="{{ $sj->pengirim }}">{{ $sj->pengirim ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate" title="{{ $sj->penerima }}">{{ $sj->penerima ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate" title="{{ $sj->jenis_barang }}">{{ $sj->jenis_barang ?: '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $sj->tipe_kontainer ?: '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $sj->jumlah_kontainer ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data untuk periode yang dipilih
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($suratJalans->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $suratJalans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
