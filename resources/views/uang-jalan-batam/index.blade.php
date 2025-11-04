@extends('layouts.app')

@section('title', 'Uang Jalan Batam')
@section('page_title', 'Uang Jalan Batam')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Data Uang Jalan Batam</h2>
        <p class="mt-1 text-sm text-gray-600">Kelola data tarif uang jalan untuk wilayah Batam</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2">
        @can('uang-jalan-batam.create')
            <a href="{{ route('uang-jalan-batam.download-template') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-download mr-2"></i>
                Download Template
            </a>
            <a href="{{ route('uang-jalan-batam.import-form') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-upload mr-2"></i>
                Import Data
            </a>
        @endcan
        @can('uang-jalan-batam.create')
            <a href="{{ route('uang-jalan-batam.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Data
            </a>
        @endcan
    </div>
</div>

@if (session('success'))
    <div class="mb-6 rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
@endif

<!-- Search Form -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <form method="GET" action="{{ route('uang-jalan-batam.index') }}" class="space-y-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    Pencarian Data
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ $search }}" 
                           placeholder="Cari berdasarkan wilayah, rute, expedisi, ring, ft, f/e, atau status..." 
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
            </div>
            <div class="flex items-end space-x-3">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('uang-jalan-batam.index') }}" 
                       class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                @endif
            </div>
        </div>
        @if($search)
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                Menampilkan hasil pencarian untuk: <span class="font-medium text-gray-900">"{{ $search }}"</span>
            </div>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rute</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expedisi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ring</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FT</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">F/E</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Awal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Akhir</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($uangJalanBatams as $uangJalan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->wilayah }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->rute }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->expedisi }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->ring }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->ft }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->f_e }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($uangJalan->tarif, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($uangJalan->status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($uangJalan->status == 'aqua') bg-blue-100 text-blue-800
                                @elseif($uangJalan->status == 'chasis PB') bg-green-100 text-green-800
                                @endif">
                                {{ $uangJalan->status }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->tanggal_awal_berlaku->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uangJalan->tanggal_akhir_berlaku->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('uang-jalan-batam.show', $uangJalan) }}" 
                               class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('uang-jalan-batam.edit', $uangJalan) }}" 
                               class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                               title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('uang-jalan-batam.destroy', $uangJalan) }}" class="inline" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?\n\nData: {{ $uangJalan->wilayah }} - {{ $uangJalan->rute }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                        title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data ditemukan</h3>
                            <p class="text-gray-500 mb-4">
                                @if($search)
                                    Tidak ada data yang sesuai dengan pencarian "{{ $search }}".
                                @else
                                    Belum ada data uang jalan Batam yang tersimpan.
                                @endif
                            </p>
                            @if(!$search)
                                <a href="{{ route('uang-jalan-batam.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Data Pertama
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination & Info -->
@if($uangJalanBatams->hasPages() || $uangJalanBatams->count() > 0)
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
        <div class="flex-1 flex justify-between sm:hidden">
            @if($uangJalanBatams->previousPageUrl())
                <a href="{{ $uangJalanBatams->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
            @endif
            @if($uangJalanBatams->nextPageUrl())
                <a href="{{ $uangJalanBatams->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
            @endif
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Menampilkan
                    <span class="font-medium">{{ $uangJalanBatams->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-medium">{{ $uangJalanBatams->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-medium">{{ $uangJalanBatams->total() }}</span>
                    data
                </p>
            </div>
            <div>
                {{ $uangJalanBatams->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endif
@endsection