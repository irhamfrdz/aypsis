@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Detail Pranota: {{ $pranota->no_invoice }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('pranota-kontainer-sewa.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('pranota-kontainer-sewa.print', $pranota->id) }}" target="_blank"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </a>
                @if($pranota->status == 'unpaid')
                <button type="button"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center"
                        onclick="openTambahKontainerModal()">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Kontainer
                </button>
                <button type="button"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center"
                        onclick="openStatusModal()">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Ubah Status
                </button>
                @endif
            </div>
        </div>

        <!-- Alert Messages -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Informasi Pranota -->
                <div class="bg-gray-50 rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Pranota</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">No. Pranota:</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $pranota->no_invoice }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Tanggal Pranota:</dt>
                                <dd class="text-sm text-gray-900">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Due Date:</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($pranota->due_date)
                                        {{ $pranota->due_date->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                <dd class="text-sm">
                                    @if($pranota->status == 'unpaid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Belum Lunas
                                        </span>
                                    @elseif($pranota->status == 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Lunas
                                        </span>
                                    @elseif($pranota->status == 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Keterangan:</dt>
                                <dd class="text-sm text-gray-900">{{ $pranota->keterangan ?: '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">No. Invoice Vendor:</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $pranota->no_invoice_vendor ?: '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Tgl Invoice Vendor:</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($pranota->tgl_invoice_vendor)
                                        {{ $pranota->tgl_invoice_vendor->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-blue-50 rounded-lg border border-blue-200">
                    <div class="px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-medium text-blue-900">Ringkasan</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Jumlah Tagihan:</dt>
                                <dd class="text-sm text-blue-900 font-semibold">{{ $pranota->jumlah_tagihan }} item</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Total Amount:</dt>
                                <dd class="text-lg font-bold text-green-600">Rp {{ number_format($pranota->total_amount, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Dibuat:</dt>
                                <dd class="text-sm text-blue-900">{{ $pranota->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-blue-700">Diupdate:</dt>
                                <dd class="text-sm text-blue-900">{{ $pranota->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Daftar Tagihan -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Tagihan dalam Pranota</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAllTagihan" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllTagihan()">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DPP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total</th>
                                    @if($pranota->status == 'unpaid')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tagihanItems as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox"
                                               class="tagihan-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                               value="{{ $item->id }}"
                                               data-amount="{{ $item->grand_total }}"
                                               data-vendor="{{ $item->vendor }}"
                                               onchange="updateTagihanSelection()">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->vendor }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $item->nomor_kontainer }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->size }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->periode }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->masa }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($item->tarif)
                                            @if(strtolower($item->tarif) == 'harian')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Harian
                                                </span>
                                            @elseif(strtolower($item->tarif) == 'bulanan')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Bulanan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $item->tarif }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($item->dpp ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($item->grand_total, 2, ',', '.') }}
                                    </td>
                                    @if($pranota->status == 'unpaid')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button type="button"
                                                onclick="keluarkanKontainer({{ $item->id }}, '{{ $item->nomor_kontainer }}', '{{ $item->vendor }}')"
                                                class="text-red-600 hover:text-red-900 font-medium flex items-center"
                                                title="Keluarkan dari pranota">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Keluarkan
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $pranota->status == 'unpaid' ? '11' : '10' }}" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada tagihan ditemukan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($tagihanItems->count() > 0)
                            <tfoot class="bg-blue-50">
                                <tr>
                                    <th colspan="9" class="px-6 py-3 text-right text-sm font-medium text-blue-900">Total:</th>
                                    <th class="px-6 py-3 text-left text-sm font-bold text-green-600">
                                        Rp {{ number_format($tagihanItems->sum('grand_total'), 2, ',', '.') }}
                                    </th>
                                    @if($pranota->status == 'unpaid')
                                    <th class="px-6 py-3"></th>
                                    @endif
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Selected Items Summary & Actions -->
                    <div id="selectedItemsSummary" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="text-sm text-blue-800">
                                    <span id="selectedTagihanCount" class="font-semibold">0</span> tagihan dipilih
                                    (<span id="selectedTotalAmount" class="font-bold text-green-600">Rp 0</span>)
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button"
                                        id="lepasKontainerBtn"
                                        onclick="lepasKontainer()"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm font-medium">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Lepas Kontainer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal -->
@if($pranota->status == 'unpaid')
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ubah Status Pranota</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeStatusModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-700 mb-6">
                Pilih status baru untuk pranota <strong>{{ $pranota->no_invoice }}</strong>:
            </p>
            <div class="grid grid-cols-1 gap-3">
                <form action="{{ route('pranota-kontainer-sewa.update.status', $pranota->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="sent">
                    <button type="submit"
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center justify-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim
                    </button>
                </form>
                <form action="{{ route('pranota-kontainer-sewa.update.status', $pranota->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pranota ini?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit"
                            class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center justify-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Tambah Kontainer -->
<div id="tambahKontainerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Kontainer ke Pranota {{ $pranota->no_invoice }}</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeTambahKontainerModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Search Form -->
            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="searchVendor" class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                        <input type="text" id="searchVendor" placeholder="Cari vendor..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="searchContainer" class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                        <input type="text" id="searchContainer" placeholder="Cari nomor kontainer..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="searchStatus" class="block text-sm font-medium text-gray-700 mb-1">Status Pranota</label>
                        <select id="searchStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="belum_pranota">Belum Masuk Pranota</option>
                            <option value="sudah_pranota">Sudah Masuk Pranota</option>
                        </select>
                    </div>
                </div>
                
                <!-- Additional Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="searchMasa" class="block text-sm font-medium text-gray-700 mb-1">Masa</label>
                        <input type="number" id="searchMasa" placeholder="Masa..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="searchTanggalAwal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
                        <input type="date" id="searchTanggalAwal"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="searchTanggalAkhir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" id="searchTanggalAkhir"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-end">
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                            <div class="flex space-x-2">
                                <button type="button" onclick="searchTagihan()" 
                                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                    <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="resetSearch()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Reset
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="text-center py-8 hidden">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <p class="mt-2 text-gray-600">Memuat data tagihan...</p>
            </div>

            <!-- Tagihan List -->
            <div id="tagihanList" class="max-h-96 overflow-y-auto">
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" id="selectAllAvailable" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleAllAvailable()">
                            <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                        </label>
                        <div id="selectedItemsInfo" class="text-sm text-gray-600">
                            <span id="selectedAvailableCount">0</span> item dipilih 
                            (<span id="selectedAvailableTotal" class="font-semibold text-green-600">Rp 0</span>)
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2" id="availableTagihanContainer">
                    <!-- Available tagihan will be loaded here -->
                </div>

                <div id="noDataMessage" class="text-center py-8 text-gray-500 hidden">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="mt-2">Tidak ada tagihan yang tersedia</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 flex justify-between items-center pt-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    <span class="font-semibold" id="finalSelectedCount">0</span> tagihan dipilih untuk ditambahkan
                    (<span class="font-bold text-green-600" id="finalSelectedTotal">Rp 0</span>)
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeTambahKontainerModal()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Batal
                    </button>
                    <button type="button" onclick="tambahKontainerTerpilih()" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah ke Pranota
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAllTagihan() {
    const selectAll = document.getElementById('selectAllTagihan');
    const checkboxes = document.querySelectorAll('.tagihan-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateTagihanSelection();
}

function updateTagihanSelection() {
    const checkboxes = document.querySelectorAll('.tagihan-checkbox:checked');
    const selectedCount = checkboxes.length;
    const totalAmount = Array.from(checkboxes).reduce((sum, checkbox) => {
        return sum + parseFloat(checkbox.dataset.amount);
    }, 0);

    // Update select all checkbox state
    const selectAll = document.getElementById('selectAllTagihan');
    const allCheckboxes = document.querySelectorAll('.tagihan-checkbox');

    if (selectedCount === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (selectedCount === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
        selectAll.checked = false;
    }

    // Update selected items summary
    const summaryDiv = document.getElementById('selectedItemsSummary');
    const countSpan = document.getElementById('selectedTagihanCount');
    const amountSpan = document.getElementById('selectedTotalAmount');

    if (selectedCount > 0) {
        summaryDiv.classList.remove('hidden');
        countSpan.textContent = selectedCount;
        amountSpan.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalAmount);
    } else {
        summaryDiv.classList.add('hidden');
        countSpan.textContent = '0';
        amountSpan.textContent = 'Rp 0';
    }
}

function keluarkanKontainer(tagihanId, nomorKontainer, vendor) {
    const confirmation = confirm(
        `Anda akan mengeluarkan kontainer dari pranota:\n\n` +
        `Vendor: ${vendor}\n` +
        `No. Kontainer: ${nomorKontainer}\n\n` +
        'Kontainer akan dikembalikan ke status "Belum Masuk Pranota".\n\n' +
        'Apakah Anda yakin ingin melanjutkan?'
    );

    if (confirmation) {
        // Create JSON payload
        const payload = {
            _token: '{{ csrf_token() }}',
            tagihan_ids: [tagihanId]
        };

        // Send POST request
        fetch(`{{ route('pranota-kontainer-sewa.lepas-kontainer', $pranota->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Kontainer berhasil dikeluarkan dari pranota.');
                location.reload();
            } else {
                alert('Gagal mengeluarkan kontainer: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses permintaan.');
        });
    }
}

function lepasKontainer() {
    const checkboxes = document.querySelectorAll('.tagihan-checkbox:checked');
    const selectedItems = Array.from(checkboxes).map(checkbox => ({
        id: checkbox.value,
        vendor: checkbox.dataset.vendor,
        amount: checkbox.dataset.amount
    }));

    if (selectedItems.length === 0) {
        alert('Silakan pilih tagihan yang akan dilepas kontainernya.');
        return;
    }

    const totalAmount = selectedItems.reduce((sum, item) => sum + parseFloat(item.amount), 0);
    const confirmation = confirm(
        `Anda akan melepas kontainer untuk ${selectedItems.length} tagihan:\n\n` +
        selectedItems.map(item => `- ${item.vendor} (Rp ${new Intl.NumberFormat('id-ID').format(item.amount)})`).join('\n') +
        `\n\nTotal Amount: Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)}\n\n` +
        'Kontainer akan dikembalikan ke status "Belum Masuk Pranota".\n\n' +
        'Apakah Anda yakin ingin melanjutkan?'
    );

    if (confirmation) {
        // Create JSON payload
        const payload = {
            _token: '{{ csrf_token() }}',
            tagihan_ids: selectedItems.map(item => item.id)
        };

        // Send POST request
        fetch(`{{ route('pranota-kontainer-sewa.lepas-kontainer', $pranota->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('Kontainer berhasil dilepas dari pranota.');
                location.reload(); // Reload page to show updated data
            } else {
                alert('Gagal melepas kontainer: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses permintaan.');
        });
    }
}

function openStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('statusModal');
    const tambahModal = document.getElementById('tambahKontainerModal');
    if (event.target == modal) {
        closeStatusModal();
    }
    if (event.target == tambahModal) {
        closeTambahKontainerModal();
    }
}

// Tambah Kontainer Modal Functions
function openTambahKontainerModal() {
    document.getElementById('tambahKontainerModal').classList.remove('hidden');
    // Load initial data
    searchTagihan();
}

function closeTambahKontainerModal() {
    document.getElementById('tambahKontainerModal').classList.add('hidden');
    // Reset form
    document.getElementById('searchVendor').value = '';
    document.getElementById('searchContainer').value = '';
    document.getElementById('searchStatus').value = '';
    document.getElementById('searchMasa').value = '';
    document.getElementById('searchTanggalAwal').value = '';
    document.getElementById('searchTanggalAkhir').value = '';
    document.getElementById('availableTagihanContainer').innerHTML = '';
    resetAvailableSelection();
}

function searchTagihan() {
    const vendor = document.getElementById('searchVendor').value;
    const container = document.getElementById('searchContainer').value;
    const status = document.getElementById('searchStatus').value;
    const masa = document.getElementById('searchMasa').value;
    const tanggalAwal = document.getElementById('searchTanggalAwal').value;
    const tanggalAkhir = document.getElementById('searchTanggalAkhir').value;
    
    // Show loading
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('availableTagihanContainer').innerHTML = '';
    document.getElementById('noDataMessage').classList.add('hidden');
    
    // Build query parameters
    const params = new URLSearchParams({
        ajax: '1',
        available_for_pranota: '1',
        exclude_pranota_id: '{{ $pranota->id }}'
    });
    
    if (vendor) params.append('vendor', vendor);
    if (container) params.append('nomor_kontainer', container);
    if (status) params.append('status_pranota', status);
    if (masa) params.append('masa', masa);
    if (tanggalAwal) params.append('tanggal_awal', tanggalAwal);
    if (tanggalAkhir) params.append('tanggal_akhir', tanggalAkhir);
    
    fetch(`{{ route('daftar-tagihan-kontainer-sewa.index') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingState').classList.add('hidden');
        
        if (data.success && data.tagihan && data.tagihan.length > 0) {
            displayAvailableTagihan(data.tagihan);
        } else {
            document.getElementById('noDataMessage').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error fetching tagihan:', error);
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('noDataMessage').classList.remove('hidden');
        alert('Gagal memuat data tagihan. Silakan coba lagi.');
    });
}

function displayAvailableTagihan(tagihanList) {
    const container = document.getElementById('availableTagihanContainer');
    container.innerHTML = '';
    
    tagihanList.forEach(tagihan => {
        const statusBadge = tagihan.status_pranota === 'sudah_pranota' 
            ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Sudah Pranota</span>'
            : '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Belum Pranota</span>';
            
        const item = document.createElement('div');
        item.className = 'border border-gray-200 rounded-lg p-4 hover:bg-gray-50';
        item.innerHTML = `
            <div class="flex items-start space-x-3">
                <input type="checkbox" 
                       class="available-tagihan-checkbox mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                       value="${tagihan.id}"
                       data-vendor="${tagihan.vendor || ''}"
                       data-container="${tagihan.nomor_kontainer || ''}"
                       data-amount="${tagihan.grand_total || 0}"
                       data-masa="${tagihan.masa || ''}"
                       data-tanggal-awal="${tagihan.tanggal_awal || ''}"
                       data-tanggal-akhir="${tagihan.tanggal_akhir || ''}"
                       onchange="updateAvailableSelection()">
                <div class="flex-1">
                    <!-- Main Info Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Vendor:</label>
                            <p class="text-gray-900 text-sm">${tagihan.vendor || '-'}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Kontainer:</label>
                            <p class="font-mono text-gray-900 text-sm">${tagihan.nomor_kontainer || '-'}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Size:</label>
                            <p class="text-gray-900 text-sm">${tagihan.size || '-'}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Status:</label>
                            ${statusBadge}
                        </div>
                    </div>
                    
                    <!-- Detail Info Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Periode:</label>
                            <p class="text-gray-900 text-sm">${tagihan.periode || '-'}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Masa:</label>
                            <p class="text-gray-900 text-sm">${tagihan.masa || '-'}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Tgl Awal:</label>
                            <p class="text-gray-900 text-sm">${tagihan.tanggal_awal ? new Date(tagihan.tanggal_awal).toLocaleDateString('id-ID') : '-'}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Tgl Akhir:</label>
                            <p class="text-gray-900 text-sm">${tagihan.tanggal_akhir ? new Date(tagihan.tanggal_akhir).toLocaleDateString('id-ID') : '-'}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 text-xs">Grand Total:</label>
                            <p class="font-semibold text-green-600 text-sm">Rp ${new Intl.NumberFormat('id-ID').format(tagihan.grand_total || 0)}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(item);
    });
    
    resetAvailableSelection();
}

function toggleAllAvailable() {
    const selectAll = document.getElementById('selectAllAvailable');
    const checkboxes = document.querySelectorAll('.available-tagihan-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateAvailableSelection();
}

function updateAvailableSelection() {
    const checkboxes = document.querySelectorAll('.available-tagihan-checkbox:checked');
    const selectedCount = checkboxes.length;
    const totalAmount = Array.from(checkboxes).reduce((sum, checkbox) => {
        return sum + parseFloat(checkbox.dataset.amount || 0);
    }, 0);
    
    // Update select all checkbox state
    const selectAll = document.getElementById('selectAllAvailable');
    const allCheckboxes = document.querySelectorAll('.available-tagihan-checkbox');
    
    if (selectedCount === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (selectedCount === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
        selectAll.checked = false;
    }
    
    // Update counters
    document.getElementById('selectedAvailableCount').textContent = selectedCount;
    document.getElementById('selectedAvailableTotal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalAmount);
    document.getElementById('finalSelectedCount').textContent = selectedCount;
    document.getElementById('finalSelectedTotal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalAmount);
}

function resetAvailableSelection() {
    document.getElementById('selectAllAvailable').checked = false;
    document.getElementById('selectAllAvailable').indeterminate = false;
    document.getElementById('selectedAvailableCount').textContent = '0';
    document.getElementById('selectedAvailableTotal').textContent = 'Rp 0';
    document.getElementById('finalSelectedCount').textContent = '0';
    document.getElementById('finalSelectedTotal').textContent = 'Rp 0';
}

function resetSearch() {
    document.getElementById('searchVendor').value = '';
    document.getElementById('searchContainer').value = '';
    document.getElementById('searchStatus').value = '';
    document.getElementById('searchMasa').value = '';
    document.getElementById('searchTanggalAwal').value = '';
    document.getElementById('searchTanggalAkhir').value = '';
    searchTagihan();
}

function tambahKontainerTerpilih() {
    const checkboxes = document.querySelectorAll('.available-tagihan-checkbox:checked');
    const selectedIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Silakan pilih minimal satu tagihan untuk ditambahkan ke pranota.');
        return;
    }
    
    const selectedItems = Array.from(checkboxes).map(checkbox => ({
        id: checkbox.value,
        vendor: checkbox.dataset.vendor,
        container: checkbox.dataset.container,
        amount: parseFloat(checkbox.dataset.amount || 0),
        masa: checkbox.dataset.masa || '',
        tanggal_awal: checkbox.dataset.tanggalAwal || '',
        tanggal_akhir: checkbox.dataset.tanggalAkhir || ''
    }));
    
    const totalAmount = selectedItems.reduce((sum, item) => sum + item.amount, 0);
    const confirmation = confirm(
        `Anda akan menambahkan ${selectedItems.length} tagihan ke pranota {{ $pranota->no_invoice }}:\n\n` +
        selectedItems.map(item => {
            const tanggalAwal = item.tanggal_awal ? new Date(item.tanggal_awal).toLocaleDateString('id-ID') : '-';
            const tanggalAkhir = item.tanggal_akhir ? new Date(item.tanggal_akhir).toLocaleDateString('id-ID') : '-';
            return `- ${item.vendor} (${item.container})\n  Masa: ${item.masa || '-'} | ${tanggalAwal} - ${tanggalAkhir}\n  Amount: Rp ${new Intl.NumberFormat('id-ID').format(item.amount)}`;
        }).join('\n\n') +
        `\n\nTotal Amount: Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)}\n\n` +
        'Apakah Anda yakin ingin melanjutkan?'
    );
    
    if (confirmation) {
        // Disable button to prevent double submission
        const button = event.target;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span> Memproses...';
        
        fetch(`{{ route('pranota-kontainer-sewa.add-items-to-existing') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                pranota_id: {{ $pranota->id }},
                tagihan_ids: selectedIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Kontainer berhasil ditambahkan ke pranota.');
                location.reload(); // Reload page to show updated data
            } else {
                alert('Gagal menambahkan kontainer: ' + (data.message || 'Unknown error'));
                button.disabled = false;
                button.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses permintaan.');
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }
}
</script>
@endsection
