@extends('layouts.app')

@section('title', 'Stock Ban')
@section('page_title', 'Stock Ban')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Stock Ban</h2>

    <!-- Search Form -->
    <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <form method="GET" action="{{ route('stock-ban.index') }}" class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari kode ban, ukuran ban, merek, atau lokasi..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           autocomplete="off">
                </div>
                <div class="w-full sm:w-48">
                    <select name="lokasi" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Lokasi</option>
                        <option value="BTM" {{ request('lokasi') == 'BTM' ? 'selected' : '' }}>BTM</option>
                        <option value="JKT" {{ request('lokasi') == 'JKT' ? 'selected' : '' }}>JKT</option>
                        <option value="PNG" {{ request('lokasi') == 'PNG' ? 'selected' : '' }}>PNG</option>
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <select name="kondisi" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Kondisi</option>
                        <option value="Baru" {{ request('kondisi') == 'Baru' ? 'selected' : '' }}>Baru</option>
                        <option value="Bekas" {{ request('kondisi') == 'Bekas' ? 'selected' : '' }}>Bekas</option>
                        <option value="Vulkanisir" {{ request('kondisi') == 'Vulkanisir' ? 'selected' : '' }}>Vulkanisir</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari
                    </button>
                    @if(request('search') || request('lokasi') || request('kondisi'))
                        <a href="{{ route('stock-ban.index') }}" class="inline-flex items-center bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tombol Aksi -->
    <div class="mb-4 flex justify-between items-center">
        <div class="flex space-x-3">
            <!-- Export Button -->
            <div class="relative">
                <button type="button" id="export-dropdown-button"
                        class="inline-flex items-center px-4 py-2 border border-blue-600 text-sm font-medium rounded-md shadow-sm text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="toggleExportDropdown()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Data
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <!-- Export Dropdown Menu -->
                <div id="export-dropdown" class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 hidden">
                    <div class="py-1">
                        <a href="{{ route('stock-ban.export', ['format' => 'excel']) }}"
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                            <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export to Excel
                        </a>
                        <a href="{{ route('stock-ban.export', ['format' => 'csv']) }}"
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                            <svg class="w-4 h-4 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export to CSV
                        </a>
                        <a href="{{ route('stock-ban.export', ['format' => 'pdf']) }}"
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                            <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            Export to PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Tambah Data -->
        <div>
            @can('stock-ban-create')
                <a href="{{ route('stock-ban.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Stock Ban
                </a>
            @endcan
        </div>
    </div>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Table Header with Data Info -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="text-sm text-gray-600">
            Total <strong>{{ $stockBans->total() }}</strong> stock ban terdaftar ({{ $stockBans->firstItem() ?? 0 }} - {{ $stockBans->lastItem() ?? 0 }})
        </div>
        
        <!-- Rows Per Page Control -->
        <div class="flex items-center space-x-2">
            <label class="text-sm text-gray-600">Tampilkan:</label>
            <form method="GET" action="{{ route('stock-ban.index') }}" class="inline">
                @foreach(request()->query() as $key => $value)
                    @if($key !== 'per_page' && $key !== 'page')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <select name="per_page"
                        onchange="this.form.submit()"
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Tabel Stock Ban -->
    <div class="overflow-x-auto shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kode Ban</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Ukuran Ban</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Merek</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Jumlah</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kondisi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Lokasi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Harga Satuan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stockBans as $index => $ban)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $stockBans->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $ban->kode_ban }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $ban->ukuran_ban }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $ban->merek }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ban->jumlah <= 5 ? 'bg-red-100 text-red-800' : ($ban->jumlah <= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $ban->jumlah }} pcs
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $ban->kondisi == 'Baru' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $ban->kondisi == 'Bekas' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $ban->kondisi == 'Vulkanisir' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ $ban->kondisi }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $ban->lokasi }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($ban->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                @can('stock-ban-view')
                                    <a href="{{ route('stock-ban.show', $ban->id) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors duration-200"
                                       title="Lihat Detail">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                @endcan
                                @can('stock-ban-update')
                                    <a href="{{ route('stock-ban.edit', $ban->id) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors duration-200"
                                       title="Edit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endcan
                                @can('stock-ban-delete')
                                    <form action="{{ route('stock-ban.destroy', $ban->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus stock ban ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition-colors duration-200"
                                                title="Hapus">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-sm font-medium">Tidak ada data stock ban</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $stockBans->appends(request()->query())->links() }}
    </div>
</div>

<script>
    function toggleExportDropdown() {
        const dropdown = document.getElementById('export-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('export-dropdown');
        const button = document.getElementById('export-dropdown-button');
        
        if (!button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endsection
