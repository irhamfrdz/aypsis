@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-file-contract mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Bill of Lading (BL)</h1>
                    <p class="text-gray-600">Kelola data Bill of Lading</p>
                    @if(request('nama_kapal') || request('no_voyage'))
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-filter mr-1"></i>
                                Filter aktif: 
                                @if(request('nama_kapal'))
                                    {{ request('nama_kapal') }}
                                @endif
                                @if(request('nama_kapal') && request('no_voyage'))
                                    | 
                                @endif
                                @if(request('no_voyage'))
                                    Voyage {{ request('no_voyage') }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div>
                <button type="button" onclick="openImportModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 mr-3">
                    <i class="fas fa-upload mr-2"></i>Import Excel
                </button>
                <a href="{{ route('bl.download.template') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-download mr-2"></i>Download Template
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-r mb-4 shadow-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl mr-3"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1">Import Berhasil!</h3>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-4 text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r mb-4 shadow-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1">Import Gagal!</h3>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-4 text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 px-6 py-4 rounded-r mb-4 shadow-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1">Peringatan Import</h3>
                    <p class="text-sm">{{ session('warning') }}</p>
                    @if(session('import_errors'))
                        <details class="mt-3">
                            <summary class="cursor-pointer text-sm font-semibold hover:text-yellow-900">
                                <i class="fas fa-list mr-1"></i>Lihat detail error ({{ count(session('import_errors')) }} error)
                            </summary>
                            <div class="mt-2 ml-4 text-xs space-y-1 max-h-60 overflow-y-auto">
                                @foreach(session('import_errors') as $error)
                                    <div class="flex items-start py-1">
                                        <i class="fas fa-circle text-xs mr-2 mt-1"></i>
                                        <span>{{ $error }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-4 text-yellow-600 hover:text-yellow-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('bl.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Search --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari nomor BL, kontainer, voyage, kapal, barang..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Actions --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('bl.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- BL Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Data BL</h3>
                <div class="flex items-center gap-4">
                    {{-- Action Buttons for Selected Items --}}
                    <div id="selectedActions" class="p-3 bg-blue-50 border border-blue-200 rounded-lg" style="display: none;">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-blue-800 mr-4">
                                <span id="selectedCount">0</span> item terpilih
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="bulkAction('split')" 
                                        class="px-3 py-1 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Pecah BL
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        Total: {{ $bls->total() }} BL
                    </div>
                </div>
            </div>
        </div>

        @if($bls->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nomor_bl', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Nomor BL
                                    @if(request('sort') === 'nomor_bl')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nomor_kontainer', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Nomor Kontainer
                                    @if(request('sort') === 'nomor_kontainer')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No Seal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_kapal', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Kapal
                                    @if(request('sort') === 'nama_kapal')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'no_voyage', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Voyage
                                    @if(request('sort') === 'no_voyage')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_barang', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Nama Barang
                                    @if(request('sort') === 'nama_barang')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipe Kontainer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tonnage
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Volume
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Term
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Supir OB
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status Bongkar
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="hover:text-gray-700">
                                    Tanggal Dibuat
                                    @if(request('sort') === 'created_at')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bls as $bl)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_items[]" value="{{ $bl->id }}" 
                                           class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="nomor-bl-container" data-bl-id="{{ $bl->id }}">
                                        <div class="nomor-bl-display cursor-pointer" title="Klik untuk edit">
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $bl->nomor_bl ?: '-' }}
                                            </span>
                                            <i class="fas fa-edit ml-1 text-gray-400 text-xs"></i>
                                        </div>
                                        <div class="nomor-bl-edit hidden">
                                            <div class="flex items-center space-x-2">
                                                <input type="text" 
                                                       class="nomor-bl-input w-32 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                       value="{{ $bl->nomor_bl }}"
                                                       placeholder="Nomor BL">
                                                <button class="save-nomor-bl bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="cancel-nomor-bl bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $bl->nomor_kontainer ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->no_seal ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->nama_kapal }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->no_voyage }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ Str::limit($bl->nama_barang, 30) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->tipe_kontainer ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->tonnage ? number_format($bl->tonnage, 2) . ' Ton' : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->volume ? number_format($bl->volume, 3) . ' m³' : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->term ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->supir_ob ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="status-bongkar-container" data-bl-id="{{ $bl->id }}">
                                        <div class="status-bongkar-display cursor-pointer" title="Klik untuk edit">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $bl->status_bongkar === 'Sudah Bongkar' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $bl->status_bongkar }}
                                            </span>
                                            <i class="fas fa-edit ml-1 text-gray-400 text-xs"></i>
                                        </div>
                                        <div class="status-bongkar-edit hidden">
                                            <div class="flex items-center space-x-2">
                                                <select class="status-bongkar-select w-40 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="Belum Bongkar" {{ $bl->status_bongkar === 'Belum Bongkar' ? 'selected' : '' }}>Belum Bongkar</option>
                                                    <option value="Sudah Bongkar" {{ $bl->status_bongkar === 'Sudah Bongkar' ? 'selected' : '' }}>Sudah Bongkar</option>
                                                </select>
                                                <button class="save-status-bongkar bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="cancel-status-bongkar bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $bl->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('bl.show', $bl) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition duration-200">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($bl->prospek)
                                            <a href="{{ route('prospek.show', $bl->prospek) }}" 
                                               class="text-green-600 hover:text-green-900 transition duration-200"
                                               title="Lihat Prospek">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Rows Per Page Selection --}}
            @include('components.rows-per-page', [
                'routeName' => 'bl.index',
                'paginator' => $bls,
                'entityName' => 'bl',
                'entityNamePlural' => 'bl'
            ])

            {{-- Pagination --}}
            @include('components.modern-pagination', ['paginator' => $bls, 'routeName' => 'bl.index'])
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-contract text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data BL</h3>
                <p class="text-gray-600 mb-6">Belum ada Bill of Lading yang dibuat.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal untuk Import Excel -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-upload mr-2 text-blue-600"></i>
                    Import Data BL dari Excel
                </h3>
                <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Petunjuk Import</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Download template terlebih dahulu</li>
                                <li>Isi data sesuai dengan format yang ada</li>
                                <li>Kolom yang wajib diisi: Nomor Kontainer, Nama Kapal, No Voyage</li>
                                <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                <li>Maksimal ukuran file: 10 MB</li>
                                <li>Status bongkar otomatis diset "Belum Bongkar"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('bl.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Excel <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="import_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload file</span>
                                    <input id="import_file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required onchange="showFileName(this)">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                XLSX, XLS, CSV maksimal 10MB
                            </p>
                            <p id="file-name" class="text-sm text-gray-700 font-medium mt-2"></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeImportModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-upload mr-1"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk pecah BL -->
<div id="splitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-3/4 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Pecah BL</h3>
                <button type="button" onclick="closeSplitModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Cara Kerja Pemecahan BL</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Sistem akan membuat BL baru dengan kontainer yang sama</li>
                                <li>Tonnage dan volume akan dipindahkan dari BL asli ke BL baru sesuai jumlah yang ditentukan</li>
                                <li>BL asli akan berkurang tonnage dan volumenya sesuai yang dipindahkan</li>
                                <li>Nomor kontainer dan tipe kontainer tetap sama di kedua BL</li>
                                <li>Hanya nama barang yang bisa berbeda untuk membedakan kedua BL</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <form id="splitForm" method="POST" action="{{ route('bl.bulk-split') }}">
                @csrf
                
                <!-- Hidden input untuk IDs yang dipilih -->
                <input type="hidden" id="splitSelectedIdsInput" name="ids" value="">
                
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Informasi Barang yang Dipindahkan</h4>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md text-sm text-yellow-700">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <strong>Catatan:</strong> Kontainer yang dipilih akan tetap sama (nomor dan tipe). Yang akan berubah hanya tonnage, volume, dan nama barang untuk membedakan pecahan.
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="tonnageDipindah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tonnage Dipindah (Ton) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="tonnageDipindah" name="tonnage_dipindah" step="0.001" min="0.001"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.000" required>
                                <p class="text-xs text-gray-500 mt-1">Masukkan jumlah tonnage yang akan dipindahkan dari BL ini</p>
                            </div>
                            <div>
                                <label for="volumeDipindah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Volume Dipindah (m³) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="volumeDipindah" name="volume_dipindah" step="0.001" min="0.001"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.000" required>
                                <p class="text-xs text-gray-500 mt-1">Masukkan jumlah volume yang akan dipindahkan dari BL ini</p>
                            </div>
                            <div>
                                <label for="namaBarangDipindah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Barang untuk BL Baru <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="namaBarangDipindah" name="nama_barang_dipindah"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Nama barang untuk BL yang baru" required>
                                <p class="text-xs text-gray-500 mt-1">Nama barang untuk membedakan BL baru dengan BL asli</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="termBaru" class="block text-sm font-medium text-gray-700 mb-1">
                                        Term untuk BL Baru
                                    </label>
                                    <input type="text" id="termBaru" name="term_baru"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Kosongkan jika sama dengan BL asli">
                                    <p class="text-xs text-gray-500 mt-1">Opsional: Term khusus untuk BL baru, jika kosong akan sama dengan BL asli</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Keterangan Pemecahan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="keterangan" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Jelaskan alasan pemecahan BL ini..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeSplitModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Pecah BL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Global functions for onclick handlers
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('import_file').value = '';
    document.getElementById('file-name').textContent = '';
}

function showFileName(input) {
    const fileName = input.files[0]?.name || '';
    document.getElementById('file-name').textContent = fileName ? `File: ${fileName}` : '';
}

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu item untuk melakukan aksi ini.');
        return;
    }

    switch(action) {
        case 'split':
            // Validate container numbers first
            validateContainerNumbers(selectedIds);
            break;
    }
}

function validateContainerNumbers(selectedIds) {
    // Make AJAX request to validate container numbers
    fetch('{{ route("bl.validate-containers") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ids: selectedIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.has_different_containers) {
                alert('⚠️ Warning: Item yang dipilih memiliki nomor kontainer yang berbeda!\n\n' + data.container_info);
                return;
            }
            
            if (data.has_no_container) {
                alert('⚠️ Warning: Ada item yang belum memiliki nomor kontainer!\n\nPastikan semua item sudah memiliki nomor kontainer sebelum melakukan pemecahan.');
                return;
            }
            
            // All validations passed, show split modal
            openSplitModal(selectedIds);
        } else {
            alert('Terjadi error saat validasi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi error saat validasi nomor kontainer.');
    });
}

function openSplitModal(selectedIds) {
    document.getElementById('splitSelectedIdsInput').value = JSON.stringify(selectedIds);
    document.getElementById('splitModal').classList.remove('hidden');
    
    // Focus on first input in the form
    const firstInput = document.getElementById('tonnageDipindah');
    if (firstInput) firstInput.focus();
}

function closeSplitModal() {
    document.getElementById('splitModal').classList.add('hidden');
    // Reset form
    const form = document.getElementById('splitForm');
    form.reset();
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize checkboxes
    initializeCheckboxes();
    
    // Handle click on nomor BL display to enable editing
    document.querySelectorAll('.nomor-bl-display').forEach(function(element) {
        element.addEventListener('click', function() {
            const container = this.closest('.nomor-bl-container');
            const display = container.querySelector('.nomor-bl-display');
            const edit = container.querySelector('.nomor-bl-edit');
            const input = container.querySelector('.nomor-bl-input');
            
            // Hide display, show edit
            display.classList.add('hidden');
            edit.classList.remove('hidden');
            
            // Focus on input
            input.focus();
            input.select();
        });
    });

    // Handle save button click
    document.querySelectorAll('.save-nomor-bl').forEach(function(button) {
        button.addEventListener('click', function() {
            const container = this.closest('.nomor-bl-container');
            const blId = container.dataset.blId;
            const input = container.querySelector('.nomor-bl-input');
            const nomorBl = input.value;
            
            // Disable button during request
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Send AJAX request
            fetch(`/bl/${blId}/nomor-bl`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    nomor_bl: nomorBl
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update display
                    const display = container.querySelector('.nomor-bl-display span');
                    display.textContent = data.nomor_bl;
                    
                    // Hide edit, show display
                    container.querySelector('.nomor-bl-edit').classList.add('hidden');
                    container.querySelector('.nomor-bl-display').classList.remove('hidden');
                    
                    // Show success message
                    showNotification('Nomor BL berhasil diupdate', 'success');
                } else {
                    showNotification(data.message || 'Gagal mengupdate nomor BL', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat mengupdate nomor BL', 'error');
            })
            .finally(() => {
                // Re-enable button
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check"></i>';
            });
        });
    });

    // Handle cancel button click
    document.querySelectorAll('.cancel-nomor-bl').forEach(function(button) {
        button.addEventListener('click', function() {
            const container = this.closest('.nomor-bl-container');
            const display = container.querySelector('.nomor-bl-display');
            const edit = container.querySelector('.nomor-bl-edit');
            const input = container.querySelector('.nomor-bl-input');
            const originalValue = display.querySelector('span').textContent;
            
            // Reset input value
            input.value = originalValue === '-' ? '' : originalValue;
            
            // Hide edit, show display
            edit.classList.add('hidden');
            display.classList.remove('hidden');
        });
    });

    // Handle Enter key to save, Escape key to cancel
    document.querySelectorAll('.nomor-bl-input').forEach(function(input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.closest('.nomor-bl-container').querySelector('.save-nomor-bl').click();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                this.closest('.nomor-bl-container').querySelector('.cancel-nomor-bl').click();
            }
        });
    });
    
    // ===== Status Bongkar Inline Editing =====
    
    // Handle display click to show edit mode
    document.querySelectorAll('.status-bongkar-display').forEach(function(element) {
        element.addEventListener('click', function() {
            const container = this.closest('.status-bongkar-container');
            this.classList.add('hidden');
            container.querySelector('.status-bongkar-edit').classList.remove('hidden');
            container.querySelector('.status-bongkar-select').focus();
        });
    });

    // Handle save button click
    document.querySelectorAll('.save-status-bongkar').forEach(function(button) {
        button.addEventListener('click', function() {
            const container = this.closest('.status-bongkar-container');
            const blId = container.dataset.blId;
            const select = container.querySelector('.status-bongkar-select');
            const statusBongkar = select.value;
            
            // Disable button during request
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Send AJAX request
            fetch(`/bl/${blId}/status-bongkar`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status_bongkar: statusBongkar
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update display
                    const display = container.querySelector('.status-bongkar-display span');
                    display.textContent = data.status_bongkar;
                    
                    // Update badge color
                    display.className = 'px-2 py-1 text-xs font-semibold rounded-full ' + 
                        (data.status_bongkar === 'Sudah Bongkar' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800');
                    
                    // Hide edit, show display
                    container.querySelector('.status-bongkar-edit').classList.add('hidden');
                    container.querySelector('.status-bongkar-display').classList.remove('hidden');
                    
                    // Show success message
                    showNotification('Status bongkar berhasil diupdate', 'success');
                } else {
                    showNotification(data.message || 'Gagal mengupdate status bongkar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat mengupdate status bongkar', 'error');
            })
            .finally(() => {
                // Re-enable button
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check"></i>';
            });
        });
    });

    // Handle cancel button click
    document.querySelectorAll('.cancel-status-bongkar').forEach(function(button) {
        button.addEventListener('click', function() {
            const container = this.closest('.status-bongkar-container');
            const display = container.querySelector('.status-bongkar-display');
            const edit = container.querySelector('.status-bongkar-edit');
            const select = container.querySelector('.status-bongkar-select');
            const originalValue = display.querySelector('span').textContent;
            
            // Reset select value
            select.value = originalValue;
            
            // Hide edit, show display
            edit.classList.add('hidden');
            display.classList.remove('hidden');
        });
    });
    
    // Checkbox functionality
    function initializeCheckboxes() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        // Select All functionality
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }

        // Individual checkbox functionality
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
                updateSelectedCount();
            });
        });

        function updateSelectAllState() {
            if (!selectAllCheckbox) return;
            
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const totalBoxes = rowCheckboxes.length;
            
            if (checkedBoxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedBoxes.length === totalBoxes) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }

        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const count = checkedBoxes.length;
            const selectedActions = document.getElementById('selectedActions');
            const selectedCount = document.getElementById('selectedCount');
            
            if (selectedCount) {
                selectedCount.textContent = count;
            }
            
            if (selectedActions) {
                if (count > 0) {
                    selectedActions.style.display = 'block';
                } else {
                    selectedActions.style.display = 'none';
                }
            }
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const splitModal = document.getElementById('splitModal');
        
        if (event.target === splitModal) {
            closeSplitModal();
        }
    });
});
</script>
@endpush