@extends('layouts.app')

@section('title', 'Pricelist Uang Jalan Batam')
@section('page_title', 'Pricelist Uang Jalan Batam')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Pricelist Uang Jalan Batam</h2>
        <p class="mt-1 text-sm text-gray-600">Kelola data tarif uang jalan untuk wilayah Batam</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2">
        @can('master-pricelist-uang-jalan-batam-view')
            <a href="{{ route('pricelist-uang-jalan-batam.download-template') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-download mr-2"></i>
                Download Template
            </a>
        @endcan
        @can('master-pricelist-uang-jalan-batam-create')
            <button type="button" 
                    onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-file-import mr-2"></i>
                Import Data
            </button>
            <a href="{{ route('pricelist-uang-jalan-batam.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Pricelist
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

@if (session('warning'))
    <div class="mb-6 rounded-md bg-yellow-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">
                    {{ session('warning') }}
                </p>
                @if(session('import_errors'))
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc pl-5 space-y-1 max-h-60 overflow-y-auto">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    {{ session('error') }}
                </p>
                @if(session('import_errors'))
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1 max-h-60 overflow-y-auto">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

<!-- Search Form -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <form method="GET" action="{{ route('pricelist-uang-jalan-batam.index') }}" class="space-y-4">
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
                           placeholder="Cari berdasarkan expedisi, ring, size, f/e, atau status..." 
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
                    <a href="{{ route('pricelist-uang-jalan-batam.index') }}" 
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expedisi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ring</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rute</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">F/E</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($pricelists as $pricelist)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pricelist->expedisi }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pricelist->ring }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $pricelist->rute ?: '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pricelist->size }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($pricelist->f_e == 'Full') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $pricelist->f_e }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">Rp {{ number_format($pricelist->tarif, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($pricelist->status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($pricelist->status == 'AQUA') bg-cyan-100 text-cyan-800
                                @elseif($pricelist->status == 'CHASIS PB') bg-purple-100 text-purple-800
                                @endif">
                                {{ $pricelist->status }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            @can('master-pricelist-uang-jalan-batam-view')
                                <a href="{{ route('pricelist-uang-jalan-batam.show', $pricelist) }}" 
                                   class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endcan
                            @can('master-pricelist-uang-jalan-batam-edit')
                                <a href="{{ route('pricelist-uang-jalan-batam.edit', $pricelist) }}" 
                                   class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                   title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                            @can('master-pricelist-uang-jalan-batam-delete')
                                <form method="POST" action="{{ route('pricelist-uang-jalan-batam.destroy', $pricelist) }}" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist ini?\n\nExpedisi: {{ $pricelist->expedisi }}\nRing: {{ $pricelist->ring }}\nSize: {{ $pricelist->size }}\nF/E: {{ $pricelist->f_e }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                            title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data ditemukan</h3>
                            <p class="text-gray-500 mb-4">
                                @if($search)
                                    Tidak ada pricelist yang sesuai dengan pencarian "{{ $search }}".
                                @else
                                    Belum ada data pricelist uang jalan Batam yang tersimpan.
                                @endif
                            </p>
                            @if(!$search)
                                @can('master-pricelist-uang-jalan-batam-create')
                                    <a href="{{ route('pricelist-uang-jalan-batam.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Pricelist Pertama
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

<!-- Pagination & Info -->
@if($pricelists->hasPages() || $pricelists->count() > 0)
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
        <div class="flex-1 flex justify-between sm:hidden">
            @if($pricelists->previousPageUrl())
                <a href="{{ $pricelists->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
            @endif
            @if($pricelists->nextPageUrl())
                <a href="{{ $pricelists->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
            @endif
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Menampilkan
                    <span class="font-medium">{{ $pricelists->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-medium">{{ $pricelists->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-medium">{{ $pricelists->total() }}</span>
                    data
                </p>
            </div>
            <div>
                {{ $pricelists->links() }}
            </div>
        </div>
    </div>
@endif

<!-- Import Modal -->
<div id="importModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('pricelist-uang-jalan-batam.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-file-import text-indigo-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Import Data Pricelist
                            </h3>
                            <div class="mt-4">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        File Excel <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" 
                                           name="file" 
                                           accept=".xlsx,.xls,.csv"
                                           required
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-2 text-xs text-gray-500">
                                        Format yang didukung: .xlsx, .xls, .csv (maksimal 2MB)
                                    </p>
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-800">Informasi Import</h4>
                                            <div class="mt-2 text-xs text-blue-700">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <li>Download template terlebih dahulu untuk format yang benar</li>
                                                    <li>Pastikan semua kolom wajib terisi</li>
                                                    <li>Data duplikat akan diupdate otomatis</li>
                                                    <li>Import akan melewati baris yang error</li>
                                                </ul>
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
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-upload mr-2"></i>
                        Upload & Import
                    </button>
                    <button type="button" 
                            onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
