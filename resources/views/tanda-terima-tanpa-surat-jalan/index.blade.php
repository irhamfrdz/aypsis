@extends('layouts.app')

@section('title', 'Tanda Terima Tanpa Surat Jalan')
@section('page_title', 'Tanda Terima Tanpa Surat Jalan')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Tanda Terima Tanpa Surat Jalan</h1>
                <p class="text-xs text-gray-600 mt-1">Kelola tanda terima yang tidak memerlukan surat jalan</p>
            </div>
            <div class="flex gap-4 text-sm">
                <div class="text-center">
                    <div class="text-lg font-semibold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-gray-500 text-xs">Total</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-gray-600">{{ $stats['draft'] ?? 0 }}</div>
                    <div class="text-gray-500 text-xs">Draft</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-yellow-600">{{ $stats['terkirim'] ?? 0 }}</div>
                    <div class="text-gray-500 text-xs">Terkirim</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-green-600">{{ $stats['selesai'] ?? 0 }}</div>
                    <div class="text-gray-500 text-xs">Selesai</div>
                </div>
            </div>
        </div>

        <div class="p-4">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form method="GET" action="{{ route('tanda-terima-tanpa-surat-jalan.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nomor tanda terima, penerima, pengirim, nomor kontainer, seal..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Tipe</option>
                            <option value="fcl" {{ request('tipe') == 'fcl' ? 'selected' : '' }}>FCL (Full Container Load)</option>
                            <option value="cargo" {{ request('tipe') == 'cargo' ? 'selected' : '' }}>Cargo</option>
                            <option value="lcl" {{ request('tipe') == 'lcl' ? 'selected' : '' }}>LCL (Less Container Load)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                            Reset
                        </a>
                        @can('tanda-terima-tanpa-surat-jalan-create')
                            <a href="{{ route('tanda-terima-tanpa-surat-jalan.pilih-tipe') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm ml-auto">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Baru
                            </a>
                        @endcan
                    </div>
                </form>
            </div>

            @if($tandaTerimas->count() > 0)
                <!-- Action Buttons for Selected Items (Only for LCL) -->
                @if(request('tipe') == 'lcl' && isset($isLclData) && $isLclData)
                    <div id="selectedActions" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg" style="display: none;">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-blue-800">
                                <span id="selectedCount">0</span> item terpilih
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="bulkAction('export')" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Selected
                                </button>
                                <button type="button" onclick="bulkAction('seal')" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.012-3H12l-.318-2.262a1 1 0 00-.996-.738H8.334a1 1 0 00-.996.738L7 9H4.988A.988.988 0 004 9.988v8.024c0 .546.442.988.988.988h14.024A.988.988 0 0020 18.012V9.988A.988.988 0 0019.012 9z"></path>
                                    </svg>
                                    Tambah Seal & Kirim ke Prospek
                                </button>
                                <button type="button" onclick="bulkAction('split')" 
                                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Pecah Kontainer
                                </button>
                                @can('tanda-terima-tanpa-surat-jalan-delete')
                                    <button type="button" onclick="bulkAction('delete')" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete Selected
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @if(request('tipe') == 'lcl' && isset($isLclData) && $isLclData)
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Tanda Terima</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Kontainer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Ambil</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Kirim</th>
                                @if(request('tipe') == 'lcl')
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Seal</th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tandaTerimas as $tandaTerima)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    @if(request('tipe') == 'lcl' && isset($isLclData) && $isLclData)
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="selected_items[]" value="{{ $tandaTerima->id }}" 
                                                   class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm font-medium text-gray-900">{{ $tandaTerima->nomor_tanda_terima }}</div>
                                        @else
                                            <div class="text-sm font-medium text-gray-900">{{ $tandaTerima->no_tanda_terima }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900">{{ $tandaTerima->nomor_kontainer ?? '-' }}</div>
                                        @else
                                            <div class="text-sm text-gray-900">{{ $tandaTerima->no_kontainer ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            @if($tandaTerima->tipe_kontainer)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ strtoupper($tandaTerima->tipe_kontainer) }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        @else
                                            @if($tandaTerima->tipe_kontainer)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ strtoupper($tandaTerima->tipe_kontainer) }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $tandaTerima->tanggal_tanda_terima->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->nama_penerima }}">{{ $tandaTerima->nama_penerima }}</div>
                                        @else
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->penerima }}">{{ $tandaTerima->penerima }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->nama_pengirim }}">{{ $tandaTerima->nama_pengirim }}</div>
                                        @else
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->pengirim }}">{{ $tandaTerima->pengirim }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900">{{ $tandaTerima->jenisBarang->nama_barang ?? $tandaTerima->nama_barang }}</div>
                                        @else
                                            <div class="text-sm text-gray-900">{{ $tandaTerima->jenis_barang }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->alamat_pengirim }}">{{ $tandaTerima->alamat_pengirim }}</div>
                                        @else
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->tujuan_pengambilan }}">{{ $tandaTerima->tujuan_pengambilan }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->tujuanPengiriman->nama_tujuan ?? '' }}">{{ $tandaTerima->tujuanPengiriman->nama_tujuan ?? 'Tidak ada' }}</div>
                                        @else
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $tandaTerima->tujuan_pengiriman }}">{{ $tandaTerima->tujuan_pengiriman }}</div>
                                        @endif
                                    </td>
                                    @if(request('tipe') == 'lcl' && isset($isLclData) && $isLclData)
                                        <!-- Size Kontainer -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($tandaTerima->size_kontainer)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $tandaTerima->size_kontainer }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <!-- Nomor Seal -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($tandaTerima->nomor_seal)
                                                <div class="text-sm text-gray-900">{{ $tandaTerima->nomor_seal }}</div>
                                                @if($tandaTerima->tanggal_seal)
                                                    <div class="text-xs text-gray-500">{{ $tandaTerima->tanggal_seal->format('d/m/Y') }}</div>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Belum ada seal
                                                </span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(isset($isLclData) && $isLclData)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                LCL Data
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Standard
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center gap-2">
                                            @if(isset($isLclData) && $isLclData)
                                                <a href="{{ route('tanda-terima-lcl.show', $tandaTerima) }}"
                                                   class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                @can('tanda-terima-tanpa-surat-jalan-update')
                                                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerima) }}?tipe=lcl"
                                                       class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                @endcan
                                                @can('tanda-terima-tanpa-surat-jalan-delete')
                                                    <form action="{{ route('tanda-terima-tanpa-surat-jalan.destroy', $tandaTerima) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima LCL ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @else
                                                <a href="{{ route('tanda-terima-tanpa-surat-jalan.show', $tandaTerima) }}"
                                                   class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                @can('tanda-terima-tanpa-surat-jalan-update')
                                                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerima) }}"
                                                       class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                @endcan
                                                @can('tanda-terima-tanpa-surat-jalan-delete')
                                                    <form action="{{ route('tanda-terima-tanpa-surat-jalan.destroy', $tandaTerima) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-center mt-4">
                    {{ $tandaTerimas->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-base font-medium text-gray-500 mb-1">Belum ada tanda terima</h3>
                        <p class="text-sm text-gray-400">Mulai dengan membuat tanda terima baru.</p>
                        @can('tanda-terima-tanpa-surat-jalan-create')
                            <a href="{{ route('tanda-terima-tanpa-surat-jalan.pilih-tipe') }}"
                               class="inline-flex items-center px-4 py-2 mt-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Buat Tanda Terima Baru
                            </a>
                        @endcan
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal untuk pecah kontainer -->
<div id="splitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-3/4 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Pecah Kontainer Tanda Terima</h3>
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
                        <h3 class="text-sm font-medium text-blue-800">Cara Kerja Pemecahan Kontainer</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Akan dibuat 1 kontainer baru dengan volume (m³) dan berat (ton) yang Anda tentukan</li>
                                <li>Volume dan berat akan dikurangi dari tanda terima asli secara proporsional</li>
                                <li>Tanda terima asli tetap ada dengan sisa volume dan berat</li>
                                <li>Pastikan volume dan berat yang diminta tidak melebihi kapasitas yang tersedia</li>
                                <li><strong>Satuan:</strong> Volume dalam m³ (meter kubik), Berat dalam ton</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <form id="splitForm" method="POST" action="{{ route('tanda-terima-lcl.bulk-split') }}">
                @csrf
                
                <!-- Hidden input untuk IDs yang dipilih -->
                <input type="hidden" id="splitSelectedIdsInput" name="ids" value="">
                
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Detail Kontainer Baru</h4>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div id="containerFieldsGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipe Kontainer <span class="text-red-500">*</span>
                                </label>
                                <select name="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required onchange="toggleContainerFields()">
                                    <option value="">Pilih Tipe</option>
                                    <option value="lcl">LCL (Less Container Load)</option>
                                    <option value="cargo">Cargo</option>
                                </select>
                            </div>
                            <div id="nomorKontainerField">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nomor Kontainer
                                </label>
                                <input type="text" name="nomor_kontainer" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Contoh: MRKU1234567">
                            </div>
                            <div id="sizeKontainerField">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Size Kontainer
                                </label>
                                <select name="size_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Pilih Size</option>
                                    <option value="20ft">20 Feet</option>
                                    <option value="40ft">40 Feet</option>
                                    <option value="40hc">40 Feet High Cube</option>
                                    <option value="45ft">45 Feet</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="mb-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700">
                                <strong>Catatan:</strong> Untuk tipe <strong>Cargo</strong>, field nomor kontainer dan size kontainer akan disembunyikan karena cargo tidak menggunakan kontainer standar.
                            </div>
                            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded">
                                <h5 class="text-sm font-medium text-green-800 mb-2">💡 Tips untuk Volume dan Berat:</h5>
                                <ul class="text-xs text-green-700 space-y-1">
                                    <li>• Contoh volume kecil: 0.001 m³ (1 liter), 0.01 m³ (10 liter), 0.1 m³ (100 liter)</li>
                                    <li>• Contoh berat: 0.010 ton, 0.050 ton, 0.100 ton</li>
                                    <li>• Pastikan volume dan berat tidak melebihi kapasitas tanda terima asli</li>
                                </ul>
                            </div>
                            <h5 class="text-sm font-medium text-gray-900 mb-3">Informasi Barang yang Dipindahkan</h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Volume (m³) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="volume" step="0.001" min="0.001" max="999"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                           placeholder="0.000" required onchange="validateSplitInputs()">
                                    <p class="text-xs text-gray-500 mt-1">Volume dalam meter kubik (m³)</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Berat (Ton) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="berat" step="0.001" min="0.001" max="999"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                           placeholder="0.000" required onchange="validateSplitInputs()">
                                    <p class="text-xs text-gray-500 mt-1">Berat dalam ton</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kuantitas
                                    </label>
                                    <input type="number" name="kuantitas" min="1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                           placeholder="0">
                                    <p class="text-xs text-gray-500 mt-1">Jumlah item yang dipindahkan</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Keterangan Pemecahan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="keterangan" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                      placeholder="Jelaskan detail barang yang akan dipindahkan ke kontainer baru..."></textarea>
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
                        Pecah Kontainer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk menambah seal -->
<div id="sealModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Nomor Seal dan Tanggal Seal</h3>
                <button type="button" onclick="closeSealModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="sealForm" method="POST" action="{{ route('tanda-terima-lcl.bulk-seal') }}">
                @csrf
                @method('PATCH')
                
                <!-- Hidden input untuk IDs yang dipilih -->
                <input type="hidden" id="selectedIdsInput" name="ids" value="">
                
                <div class="mb-4">
                    <label for="modalNomorSeal" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Seal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modalNomorSeal" name="nomor_seal" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan nomor seal" required>
                </div>
                
                <div class="mb-4">
                    <label for="modalTanggalSeal" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Seal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="modalTanggalSeal" name="tanggal_seal"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ date('Y-m-d') }}" required>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="modalKirimProspek" name="kirim_ke_prospek" value="1" checked
                               class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                        <label for="modalKirimProspek" class="ml-2 block text-sm text-gray-700">
                            <span class="font-medium">Kirim ke Menu Prospek</span>
                            <div class="text-xs text-gray-500 mt-1">
                                Otomatis menambahkan kontainer ini ke menu prospek setelah seal disimpan
                            </div>
                        </label>
                    </div>
                    <div class="mt-2 p-3 bg-purple-50 border border-purple-200 rounded-md text-xs text-purple-700">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <strong>Catatan:</strong> Fitur ini hanya akan bekerja jika semua kontainer yang dipilih memiliki nomor kontainer yang sama dan lengkap.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeSealModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Seal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize if LCL checkboxes exist
        if (document.getElementById('selectAll')) {
            initializeCheckboxes();
        }
    });

    function initializeCheckboxes() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const selectedActions = document.getElementById('selectedActions');
        const selectedCount = document.getElementById('selectedCount');

        // Select All functionality
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedActions();
        });

        // Individual checkbox functionality
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
                updateSelectedActions();
            });
        });

        function updateSelectAllState() {
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

        function updateSelectedActions() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const count = checkedBoxes.length;
            
            selectedCount.textContent = count;
            
            if (count > 0) {
                selectedActions.style.display = 'block';
            } else {
                selectedActions.style.display = 'none';
            }
        }
    }

    function bulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Pilih minimal satu item untuk melakukan aksi ini.');
            return;
        }

        switch(action) {
            case 'export':
                // Redirect to export route with selected IDs
                const exportUrl = new URL('{{ route("tanda-terima-lcl.export") }}', window.location.origin);
                selectedIds.forEach(id => exportUrl.searchParams.append('ids[]', id));
                window.location.href = exportUrl.toString();
                break;
                
            case 'seal':
                // Validate container numbers first
                validateContainerNumbers(selectedIds);
                break;
                
            case 'split':
                // Open split container modal
                openSplitModal(selectedIds);
                break;
                
            case 'delete':
                if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} tanda terima LCL yang dipilih?`)) {
                    // Create form for bulk delete
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("tanda-terima-lcl.bulk-delete") }}';
                    
                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    // Add method spoofing for DELETE
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    // Add selected IDs
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    
                    document.body.appendChild(form);
                    form.submit();
                }
                break;
        }
    }

    function validateContainerNumbers(selectedIds) {
        // Make AJAX request to validate container numbers
        fetch('{{ route("tanda-terima-lcl.validate-containers") }}', {
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
                    alert('⚠️ Warning: Ada item yang belum memiliki nomor kontainer!\n\nPastikan semua item sudah memiliki nomor kontainer sebelum menambahkan seal.');
                    return;
                }
                
                // All validations passed, show seal modal
                openSealModal(selectedIds);
            } else {
                alert('Terjadi error saat validasi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi error saat validasi nomor kontainer.');
        });
    }

    function openSealModal(selectedIds) {
        document.getElementById('selectedIdsInput').value = JSON.stringify(selectedIds);
        document.getElementById('sealModal').classList.remove('hidden');
        document.getElementById('modalNomorSeal').focus();
    }

    function closeSealModal() {
        document.getElementById('sealModal').classList.add('hidden');
        document.getElementById('modalNomorSeal').value = '';
        document.getElementById('modalTanggalSeal').value = '{{ date("Y-m-d") }}';
        document.getElementById('modalKirimProspek').checked = true; // Set default to checked
    }

    function openSplitModal(selectedIds) {
        document.getElementById('splitSelectedIdsInput').value = JSON.stringify(selectedIds);
        document.getElementById('splitModal').classList.remove('hidden');
        
        // Initialize container fields visibility
        toggleContainerFields();
        
        // Focus on first input in the form
        const firstInput = document.querySelector('#splitModal select[name="tipe_kontainer"]');
        if (firstInput) firstInput.focus();
    }

    function closeSplitModal() {
        document.getElementById('splitModal').classList.add('hidden');
        // Reset form
        const form = document.getElementById('splitForm');
        form.reset();
        // Show container fields again when modal is closed
        toggleContainerFields();
    }

    function toggleContainerFields() {
        const tipeSelect = document.querySelector('select[name="tipe_kontainer"]');
        const nomorKontainerField = document.getElementById('nomorKontainerField');
        const sizeKontainerField = document.getElementById('sizeKontainerField');
        const containerGrid = document.getElementById('containerFieldsGrid');
        
        if (tipeSelect && nomorKontainerField && sizeKontainerField && containerGrid) {
            if (tipeSelect.value === 'cargo') {
                // Hide container fields for cargo
                nomorKontainerField.style.display = 'none';
                sizeKontainerField.style.display = 'none';
                
                // Change grid to single column when container fields are hidden
                containerGrid.className = 'grid grid-cols-1 gap-4';
                
                // Clear values when hidden
                const nomorInput = nomorKontainerField.querySelector('input[name="nomor_kontainer"]');
                const sizeSelect = sizeKontainerField.querySelector('select[name="size_kontainer"]');
                if (nomorInput) nomorInput.value = '';
                if (sizeSelect) sizeSelect.value = '';
            } else {
                // Show container fields for LCL or when nothing selected
                nomorKontainerField.style.display = 'block';
                sizeKontainerField.style.display = 'block';
                
                // Restore grid to 3 columns when all fields are visible
                containerGrid.className = 'grid grid-cols-1 md:grid-cols-3 gap-4';
            }
        }
    }

    function validateSplitInputs() {
        const volumeInput = document.querySelector('input[name="volume"]');
        const beratInput = document.querySelector('input[name="berat"]');
        
        if (volumeInput && beratInput) {
            const volume = parseFloat(volumeInput.value) || 0;
            const berat = parseFloat(beratInput.value) || 0;
            
            // Basic validation - reasonable limits
            if (volume > 50) {
                volumeInput.style.borderColor = '#f59e0b';
                volumeInput.title = 'Volume sangat besar. Pastikan benar!';
            } else {
                volumeInput.style.borderColor = '';
                volumeInput.title = '';
            }
            
            if (berat > 10) {
                beratInput.style.borderColor = '#f59e0b';
                beratInput.title = 'Berat sangat besar. Pastikan benar!';
            } else {
                beratInput.style.borderColor = '';
                beratInput.title = '';
            }
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const sealModal = document.getElementById('sealModal');
        const splitModal = document.getElementById('splitModal');
        
        if (event.target === sealModal) {
            closeSealModal();
        }
        
        if (event.target === splitModal) {
            closeSplitModal();
        }
    });
</script>
@endpush

@endsection
