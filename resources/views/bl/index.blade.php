@extends('layouts.app')

@section('title', 'Bill of Lading')
@section('page_title', 'Bill of Lading')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Bill of Lading (BL)</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola data Bill of Lading</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="openImportModal()" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-upload mr-2"></i> Import Excel
                    </button>
                    <a href="{{ route('bl.download.template') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-download mr-2"></i> Template
                    </a>
                    <button onclick="openExportModal()" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </button>
                    @can('bl-create')
                    <a href="{{ route('bl.select') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Tambah BL
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Ship & Voyage Info Banner (Visible when filtered) -->
            @if(request('nama_kapal') || request('no_voyage'))
            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 rounded-lg p-4 text-white">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex items-center space-x-6">
                        @if(request('nama_kapal'))
                        <div class="flex items-center">
                            <i class="fas fa-ship mr-2 text-xl"></i>
                            <div>
                                <div class="text-xs text-purple-100">Nama Kapal</div>
                                <div class="font-bold">{{ request('nama_kapal') }}</div>
                            </div>
                        </div>
                        @endif
                        
                        @if(request('no_voyage'))
                        <div class="flex items-center">
                            <i class="fas fa-route mr-2 text-xl"></i>
                            <div>
                                <div class="text-xs text-purple-100">No. Voyage</div>
                                <div class="font-bold">{{ request('no_voyage') }}</div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="flex items-center">
                            <i class="fas fa-file-contract mr-2 text-xl"></i>
                            <div>
                                <div class="text-xs text-purple-100">Total</div>
                                <div class="font-bold">{{ $bls->total() }} Data</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('bl.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif
        
        @if(session('split_success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-r mb-4 shadow-md">
                <h3 class="font-bold text-lg mb-1">Pecah BL Berhasil!</h3>
                <p class="text-sm">{{ session('split_success') }}</p>
                @if(session('new_bl_numbers'))
                    <div class="mt-3 p-3 bg-white border border-green-200 rounded-md">
                        <p class="text-sm font-semibold text-green-800 mb-2">BL Baru:</p>
                        <ul class="list-disc list-inside text-xs space-y-1">
                            @foreach(session('new_bl_numbers') as $blNumber)
                                <li class="text-green-700">{{ $blNumber }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @if(session('error') || session('split_error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') ?? session('split_error') }}</span>
            </div>
        @endif

         <!-- Search Section -->
         <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('bl.index') }}">
                <!-- Hidden inputs to preserve filters -->
                @if(request('nama_kapal'))
                    <input type="hidden" name="nama_kapal" value="{{ request('nama_kapal') }}">
                @endif
                @if(request('no_voyage'))
                    <input type="hidden" name="no_voyage" value="{{ request('no_voyage') }}">
                @endif

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <!-- Search -->
                    <div class="md:col-span-10">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-1"></i> Pencarian
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Cari nomor BL, kontainer, voyage, kapal, barang..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Action Buttons -->
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                            Filter
                        </button>
                        <a href="{{ route('bl.index') }}"
                           class="w-full px-4 py-2 bg-gray-100 text-gray-700 text-center text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header Actions -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Total: {{ $bls->total() }} Data
                </div>
                <!-- Selected Actions -->
                <div id="selectedActions" class="hidden items-center p-2 bg-blue-50 border border-blue-200 rounded-lg">
                     <span class="text-sm text-blue-800 mr-3">
                        <span id="selectedCount" class="font-bold">0</span> item terpilih
                    </span>
                    <button type="button" onclick="bulkAction('split')" 
                            class="px-3 py-1 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm flex items-center shadow-sm">
                        <i class="fas fa-project-diagram mr-1"></i> Pecah BL
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nomor_bl', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700 group">
                                    Nomor BL
                                    <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-500"></i>
                                </a>
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nomor_kontainer', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700 group">
                                    No. Kontainer / Seal
                                    <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-500"></i>
                                </a>
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_kapal', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700 group">
                                    Kapal Info
                                    <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-500"></i>
                                </a>
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rute
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Barang
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Info Kontainer
                            </th>

                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bls as $bl)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_items[]" value="{{ $bl->id }}" 
                                           class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="nomor-bl-container" data-bl-id="{{ $bl->id }}">
                                        <div class="nomor-bl-display flex items-center gap-1 cursor-pointer hover:bg-gray-100 p-1 rounded transition" title="Klik untuk edit">
                                            <span class="text-sm font-bold text-gray-900">{{ $bl->nomor_bl ?: '-' }}</span>
                                            <i class="fas fa-edit text-xs text-gray-400"></i>
                                        </div>
                                        <div class="nomor-bl-edit hidden flex items-center gap-1">
                                            <input type="text" 
                                                class="nomor-bl-input w-36 px-2 py-1 text-sm border border-gray-300 rounded focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                                                value="{{ $bl->nomor_bl }}" data-original="{{ $bl->nomor_bl }}">
                                            <button class="save-nomor-bl text-green-600 hover:text-green-800"><i class="fas fa-check"></i></button>
                                            <button class="cancel-nomor-bl text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $bl->term ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $bl->nomor_kontainer ?: '-' }}</div>
                                    <div class="text-xs text-gray-500">
                                        Seal: {{ $bl->no_seal ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $bl->nama_kapal }}</div>
                                    <div class="text-xs text-gray-500">Voy: {{ $bl->no_voyage }}</div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="flex flex-col text-xs">
                                        <span class="font-medium text-gray-600">Asal:</span>
                                        <span class="text-gray-900 mb-1">{{ $bl->pelabuhan_asal ?: '-' }}</span>
                                        <span class="font-medium text-gray-600">Tujuan:</span>
                                        <span class="text-gray-900">{{ $bl->pelabuhan_tujuan ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-900 mb-1">{{ Str::limit($bl->nama_barang, 30) }}</div>
                                    <div class="text-xs text-gray-500">
                                        <span class="block">Dari: {{ Str::limit($bl->pengirim ?: $bl->prospek?->pengirim, 20) }}</span>
                                        <span class="block">Ke: {{ Str::limit($bl->penerima, 20) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <div class="size-kontainer-container" data-bl-id="{{ $bl->id }}">
                                            <div class="size-kontainer-display cursor-pointer hover:bg-gray-100 rounded px-1 flex items-center w-fit">
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $bl->size_kontainer }}' {{ $bl->tipe_kontainer }}
                                                </span>
                                                <i class="fas fa-edit text-xs text-gray-400 ml-1"></i>
                                            </div>
                                            <div class="size-kontainer-edit hidden flex items-center gap-1 mt-1">
                                                <select class="size-kontainer-select text-xs border border-gray-300 rounded focus:ring-purple-500 focus:border-purple-500 py-1 px-2">
                                                    <option value="20" {{ $bl->size_kontainer == '20' ? 'selected' : '' }}>20</option>
                                                    <option value="40" {{ $bl->size_kontainer == '40' ? 'selected' : '' }}>40</option>
                                                </select>
                                                <button class="save-size-kontainer text-green-600 text-xs"><i class="fas fa-check"></i></button>
                                                <button class="cancel-size-kontainer text-red-600 text-xs"><i class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Vol: {{ number_format($bl->volume, 3) }} m³
                                        </div>
                                    </div>
                                </td>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $bl->created_at->format('d/m/y H:i') }}
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('bl.show', $bl) }}" 
                                           class="text-blue-600 hover:text-blue-900 bg-blue-50 p-1.5 rounded hover:bg-blue-100 transition" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($bl->prospek)
                                        <a href="{{ route('prospek.show', $bl->prospek) }}" 
                                           class="text-teal-600 hover:text-teal-900 bg-teal-50 p-1.5 rounded hover:bg-teal-100 transition"
                                           title="Lihat Prospek">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        @endif
                                        <button type="button" 
                                                onclick="confirmDelete({{ $bl->id }}, '{{ addslashes($bl->nomor_bl) }}')"
                                                class="text-red-600 hover:text-red-900 bg-red-50 p-1.5 rounded hover:bg-red-100 transition"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $bl->id }}" action="{{ route('bl.destroy', $bl->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 text-sm">Tidak ada data Bill of Lading ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($bls->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $bls->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 transform transition-all">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-file-import text-green-600 mr-2"></i> Import Data BL
            </h3>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('bl.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Panduan Import:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Gunakan template excel yang disediakan.</li>
                        <li>Kolom Nama Kapal dan No Voyage wajib diisi.</li>
                        <li>Format file .xlsx atau .xls (Max 10MB).</li>
                    </ul>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span></p>
                            <p class="text-xs text-gray-500">XLSX, XLS (MAX. 10MB)</p>
                            <p id="file-name" class="text-sm text-green-600 font-medium mt-2"></p>
                        </div>
                        <input id="import_file" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv" required onchange="document.getElementById('file-name').textContent = this.files[0].name" />
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm">
                    <i class="fas fa-upload mr-1"></i> Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal untuk pecah BL -->
<div id="splitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Pecah BL (Split)</h3>
            <p class="text-sm text-gray-500 mt-1">Pisahkan BL yang terdiri dari beberapa kontainer menjadi BL individual.</p>
        </div>
        <div class="p-6">
             <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Anda akan memecah <span id="splitCount" class="font-bold">0</span> BL terpilih.
                            Sistem akan membuat BL baru untuk setiap kontainer yang ada dalam BL tersebut (jika lebih dari 1 kontainer).
                        </p>
                    </div>
                </div>
            </div>
            
            <form id="splitForm" action="{{ route('bl.bulk-split') }}" method="POST">
                @csrf
                <input type="hidden" name="bl_ids" id="splitBlIds">
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeSplitModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 shadow-sm">
                        <i class="fas fa-project-diagram mr-1"></i> Proses Pecah BL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
             <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-file-excel text-purple-600 mr-2"></i> Export Data Excel
            </h3>
        </div>
        <form action="{{ route('bl.export') }}" method="GET" class="p-6">
            <p class="text-sm text-gray-600 mb-6">Silakan pilih filter data yang ingin di-export ke Excel.</p>
            
            <div class="space-y-4 mb-6">
                {{-- Search Filter (Hidden/Read-only representation) --}}
                @if(request('search'))
                <div class="p-2 bg-gray-50 rounded border border-gray-200">
                    <span class="text-xs text-gray-500 block">Pencarian Aktif:</span>
                    <span class="text-sm font-medium">"{{ request('search') }}"</span>
                    <input type="hidden" name="search" value="{{ request('search') }}">
                </div>
                @endif

                {{-- Kapal Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kapal</label>
                    <select name="nama_kapal" id="export_nama_kapal" class="w-full select2-export">
                        <option value="">-- Semua Kapal --</option>
                        {{-- Data will be loaded via JS or passed from controller if available --}}
                    </select>
                </div>

                {{-- Voyage Selection --}}
                <div id="export_voyage_container" class="">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih No. Voyage</label>
                    <select name="no_voyage" id="export_no_voyage" class="w-full select2-export">
                        <option value="">-- Pilih Voyage --</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3">
                 <button type="button" onclick="closeExportModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-sm flex items-center transition">
                    <i class="fas fa-download mr-1"></i> Download Excel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // --- Modals ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
        document.getElementById('importModal').classList.add('flex');
    }
    
    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        document.getElementById('importModal').classList.remove('flex');
        document.getElementById('import_file').value = '';
        document.getElementById('file-name').textContent = '';
    }
    
    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
        document.getElementById('exportModal').classList.add('flex');
        
        // Initialize Select2 if not already done
        $('.select2-export').select2({
            dropdownParent: $('#exportModal'),
            placeholder: 'Pilih...',
            allowClear: true,
            width: '100%'
        });

        // Load Ships
        loadExportShips();
    }
    
    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
        document.getElementById('exportModal').classList.remove('flex');
    }

    function loadExportShips() {
        const kapalSelect = $('#export_nama_kapal');
        kapalSelect.empty().append('<option value="">-- Semua Kapal --</option>');
        
        fetch('{{ route("bl.get-ships") }}')
            .then(response => response.json())
            .then(data => {
                data.ships.forEach(ship => {
                    const selected = ship === "{{ request('nama_kapal') }}" ? 'selected' : '';
                    kapalSelect.append(`<option value="${ship}" ${selected}>${ship}</option>`);
                });
                
                if ("{{ request('nama_kapal') }}") {
                    loadExportVoyages("{{ request('nama_kapal') }}");
                }
            });
    }

    $('#export_nama_kapal').on('change', function() {
        const shipName = $(this).val();
        loadExportVoyages(shipName);
    });

    function loadExportVoyages(shipName) {
        const voyageSelect = $('#export_no_voyage');
        voyageSelect.empty().append('<option value="">-- Pilih Voyage --</option>');
        
        if (!shipName) return;

        fetch(`{{ route("bl.get-voyages") }}?nama_kapal=${encodeURIComponent(shipName)}`)
            .then(response => response.json())
            .then(data => {
                data.voyages.forEach(voyage => {
                    const selected = voyage === "{{ request('no_voyage') }}" ? 'selected' : '';
                    voyageSelect.append(`<option value="${voyage}" ${selected}>${voyage}</option>`);
                });
            });
    }

    function closeSplitModal() {
        document.getElementById('splitModal').classList.add('hidden');
        document.getElementById('splitModal').classList.remove('flex');
    }

    // --- Bulk Actions ---
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const selectedActions = document.getElementById('selectedActions');
    const selectedCount = document.getElementById('selectedCount');
    
    // Toggle Select All
    if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
    }
    
    // Individual Checkbox Change
    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });
    
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.textContent = count;
        
        if (count > 0) {
            selectedActions.classList.remove('hidden');
            selectedActions.classList.add('flex');
        } else {
            selectedActions.classList.add('hidden');
            selectedActions.classList.remove('flex');
        }
    }
    
    // Bulk Action Handler
    window.bulkAction = function(action) {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length === 0) return;
        
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (action === 'split') {
            document.getElementById('splitCount').textContent = ids.length;
            document.getElementById('splitBlIds').value = ids.join(',');
            document.getElementById('splitModal').classList.remove('hidden');
            document.getElementById('splitModal').classList.add('flex');
        }
    };

    // --- Delete Confirmation ---
    window.confirmDelete = function(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus BL "${name}"?`)) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // --- Inline Editing ---
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // --- Helper for creating inline editors ---
        function setupInlineEditor(containerClass, editClass, displayClass, saveBtnClass, cancelBtnClass, inputClass, updateUrlGenerator, payloadGenerator) {
            const containers = document.querySelectorAll(containerClass);
            
            containers.forEach(container => {
                const display = container.querySelector(displayClass);
                const edit = container.querySelector(editClass);
                const saveBtn = container.querySelector(saveBtnClass);
                const cancelBtn = container.querySelector(cancelBtnClass);
                const input = container.querySelector(inputClass);
                const id = container.dataset.blId;
                
                // Show Edit
                display.addEventListener('click', () => {
                   display.classList.add('hidden');
                   edit.classList.remove('hidden');
                   input.focus();
                });
                
                // Cancel
                cancelBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    edit.classList.add('hidden');
                    display.classList.remove('hidden');
                    input.value = input.dataset.original || (input.tagName === 'SELECT' ? input.querySelector('option[selected]')?.value : ''); 
                });
                
                // Save
                saveBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    const value = input.value;
                    
                    // Optimistic update
                    const originalContent = display.innerHTML;
                    
                    // Show loading or similar? For simplicity, we just wait.
                    
                    fetch(updateUrlGenerator(id), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payloadGenerator(value))
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Update display value based on type
                            if(input.tagName === 'SELECT') {
                                // For status bongkar
                                location.reload(); // Reload easiest to badge styles update
                            } else {
                                display.querySelector('span').textContent = value;
                                input.dataset.original = value;
                            }
                            
                            // Visual success
                            edit.classList.add('hidden');
                            display.classList.remove('hidden');
                            // Maybe toast
                        } else {
                            alert('Gagal update: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan sistem.');
                    });
                });
            });
        }
        
        // 1. Nomor BL
        setupInlineEditor(
            '.nomor-bl-container', 
            '.nomor-bl-edit', 
            '.nomor-bl-display', 
            '.save-nomor-bl', 
            '.cancel-nomor-bl', 
            '.nomor-bl-input',
            (id) => `/bl/${id}/nomor-bl`,
            (val) => ({ nomor_bl: val })
        );
        
        // 2. Size Kontainer
        setupInlineEditor(
            '.size-kontainer-container', 
            '.size-kontainer-edit', 
            '.size-kontainer-display', 
            '.save-size-kontainer', 
            '.cancel-size-kontainer', 
            '.size-kontainer-select',
            (id) => `/bl/${id}/size-kontainer`,
            (val) => ({ size_kontainer: val })
        );
        

    });
</script>
@endpush