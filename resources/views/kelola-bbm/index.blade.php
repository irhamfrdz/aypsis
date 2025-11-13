@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-gas-pump mr-2 text-indigo-600"></i>
                Kelola BBM
            </h1>
            <p class="text-gray-600 mt-1">Kelola data harga BBM per liter dan persentase</p>
        </div>
        @can('master-kelola-bbm-create')
            <a href="{{ route('kelola-bbm.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Data BBM
            </a>
        @endcan
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('kelola-bbm.index') }}" class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>
                    Cari Data BBM
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ $search }}" 
                           placeholder="Cari berdasarkan tanggal, harga, persentase, atau keterangan..." 
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
                    <a href="{{ route('kelola-bbm.index') }}" 
                       class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                @endif
            </div>
        </div>
        @if($search)
            <div class="flex items-center text-sm text-gray-600 mt-4">
                <i class="fas fa-info-circle mr-2"></i>
                Menampilkan hasil pencarian untuk: <span class="font-medium text-gray-900 ml-1">"{{ $search }}"</span>
            </div>
        @endif
    </form>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BBM Per Liter</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($kelolaBbm as $index => $bbm)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $kelolaBbm->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $bbm->formatted_bulan_tahun }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            Rp {{ number_format($bbm->bbm_per_liter, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ number_format($bbm->persentase, 2) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($bbm->keterangan)
                                <div class="max-w-xs truncate" title="{{ $bbm->keterangan }}">
                                    {{ $bbm->keterangan }}
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                @can('master-kelola-bbm-view')
                                    <a href="{{ route('kelola-bbm.show', $bbm) }}" 
                                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan
                                
                                @can('master-kelola-bbm-edit')
                                    <a href="{{ route('kelola-bbm.edit', $bbm) }}" 
                                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-amber-700 bg-amber-100 hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                
                                @can('master-kelola-bbm-delete')
                                    <form action="{{ route('kelola-bbm.destroy', $bbm) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data BBM periode {{ $bbm->formatted_bulan_tahun }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-gas-pump text-gray-300 text-5xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data ditemukan</h3>
                                <p class="text-gray-500 mb-4">
                                    @if($search)
                                        Tidak ada data BBM yang sesuai dengan pencarian "{{ $search }}".
                                    @else
                                        Belum ada data BBM yang tersimpan.
                                    @endif
                                </p>
                                @if(!$search)
                                    @can('master-kelola-bbm-create')
                                        <a href="{{ route('kelola-bbm.create') }}" 
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                            <i class="fas fa-plus mr-2"></i>
                                            Tambah Data BBM Pertama
                                        </a>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($kelolaBbm->hasPages() || $kelolaBbm->count() > 0)
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($kelolaBbm->previousPageUrl())
                    <a href="{{ $kelolaBbm->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif
                @if($kelolaBbm->nextPageUrl())
                    <a href="{{ $kelolaBbm->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Selanjutnya
                    </a>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $kelolaBbm->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-medium">{{ $kelolaBbm->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-medium">{{ $kelolaBbm->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    {{ $kelolaBbm->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
