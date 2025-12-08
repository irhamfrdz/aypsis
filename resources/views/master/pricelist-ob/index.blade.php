@extends('layouts.app')

@section('title', 'Master Pricelist OB')
@section('page_title', 'Master Pricelist OB')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 002 2v14a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Master Pricelist OB</h1>
                            <p class="text-blue-100 text-sm">Kelola daftar harga biaya OB berdasarkan size dan status kontainer</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @can('master-pricelist-ob-create')
                        <a href="{{ route('master.pricelist-ob.create') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Pricelist OB
                        </a>
                        @endcan
                        @can('master-pricelist-ob-view')
                        <a href="{{ route('master.pricelist-ob.export-template') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M20 20H4"/></svg>
                            Download Template CSV
                        </a>
                        @endcan
                        @can('master-pricelist-ob-create')
                        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M20 20H4"/></svg>
                            Import CSV / Excel
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Filter & Pencarian</h3>
                <form method="GET" action="{{ route('master.pricelist-ob.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Pencarian</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                   placeholder="Cari size, status, atau keterangan..." 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700">Size Kontainer</label>
                            <select id="size_kontainer" name="size_kontainer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Semua Size</option>
                                <option value="20ft" {{ request('size_kontainer') == '20ft' ? 'selected' : '' }}>20ft</option>
                                <option value="40ft" {{ request('size_kontainer') == '40ft' ? 'selected' : '' }}>40ft</option>
                            </select>
                        </div>
                        <div>
                            <label for="status_kontainer" class="block text-sm font-medium text-gray-700">Status Kontainer</label>
                            <select id="status_kontainer" name="status_kontainer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Semua Status</option>
                                <option value="full" {{ request('status_kontainer') == 'full' ? 'selected' : '' }}>Full</option>
                                <option value="empty" {{ request('status_kontainer') == 'empty' ? 'selected' : '' }}>Empty</option>
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Filter
                            </button>
                            <a href="{{ route('master.pricelist-ob.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                                Reset
                            </a>
                            
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Import Modal -->
        <div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Import Data CSV / Excel</h3>
                        <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                            <span class="text-2xl">&times;</span>
                        </button>
                    </div>

                    <form action="{{ route('master.pricelist-ob.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV atau Excel</label>
                            <input type="file" name="file" id="file" accept=".csv,.txt,.xlsx,.xls" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-sm text-gray-500">Format file: CSV (delimiter ; ) atau Excel (.xlsx) dengan header sesuai template</p>
                        </div>

                        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Format yang Diharapkan:</h4>
                            <p class="text-xs text-blue-700 mb-2">Header kolom (case-insensitive):</p>
                            <div class="text-xs text-blue-700 font-mono bg-white p-2 rounded border">
                                size_kontainer;status_kontainer;biaya;keterangan
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                                class="px-3 py-1 bg-gray-300 text-gray-700 rounded text-sm hover:bg-gray-400 transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors duration-200">
                                Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Pricelist OB</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola semua pricelist OB yang tersedia berdasarkan size dan status kontainer</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pricelistOb as $pricelist)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#{{ $pricelist->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $pricelist->size_kontainer_label }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $pricelist->status_kontainer === 'full' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ $pricelist->status_kontainer_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $pricelist->formatted_biaya }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $pricelist->keterangan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @can('master-pricelist-ob-view')
                                    <a href="{{ route('master.pricelist-ob.show', $pricelist) }}"
                                       class="text-blue-600 hover:text-blue-900">Lihat</a>
                                    @endcan
                                    @can('master-pricelist-ob-update')
                                    <a href="{{ route('master.pricelist-ob.edit', $pricelist) }}"
                                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @endcan
                                    @can('master-pricelist-ob-delete')
                                    <form action="{{ route('master.pricelist-ob.destroy', $pricelist) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist OB ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 002 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada data pricelist OB</p>
                                    <p class="text-sm">Tambahkan pricelist OB pertama Anda untuk memulai.</p>
                                    @can('master-pricelist-ob-create')
                                    <a href="{{ route('master.pricelist-ob.create') }}"
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Tambah Pricelist OB Pertama
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pricelistOb->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                @include('components.modern-pagination', ['paginator' => $pricelistOb])
                @include('components.rows-per-page')
            </div>
            @endif
        </div>
    </div>
</div>
@endsection