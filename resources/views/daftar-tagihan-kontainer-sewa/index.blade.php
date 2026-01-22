@extends('layouts.app')

@section('content')
<style>
/* Modal Animation Styles */
.modal-overlay {
    transition: opacity 0.3s ease-out;
}

.modal-overlay.modal-show {
    opacity: 1;
}

.modal-overlay.modal-hide {
    opacity: 0;
}

.modal-content {
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
    transform: translateY(-20px) scale(0.95);
    opacity: 0;
}

.modal-content.modal-show {
    transform: translateY(0) scale(1);
    opacity: 1;
}

.modal-content.modal-hide {
    transform: translateY(-20px) scale(0.95);
    opacity: 0;
}

/* Backdrop blur animation */
.modal-backdrop {
    backdrop-filter: blur(0px);
    transition: backdrop-filter 0.3s ease-out;
}

.modal-backdrop.modal-show {
    backdrop-filter: blur(4px);
}

/* Loading spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #ffffff33;
    border-radius: 50%;
    border-top-color: #ffffff;
    animation: spin 0.8s ease-in-out infinite;
    margin-right: 8px;
}

/* Notification styles */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 400px;
}

.notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 16px;
    margin-bottom: 12px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease-in-out;
    border-left: 4px solid;
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

.notification.success {
    border-left-color: #10b981;
}

.notification.error {
    border-left-color: #ef4444;
}

.notification.warning {
    border-left-color: #f59e0b;
}

.notification-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.notification.success .notification-icon {
    background-color: #10b981;
}

.notification.error .notification-icon {
    background-color: #ef4444;
}

.notification.warning .notification-icon {
    background-color: #f59e0b;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
    color: #1f2937;
}

.notification-message {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.4;
}

.notification-close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 2px;
    border-radius: 4px;
    transition: all 0.2s;
    flex-shrink: 0;
}

.notification-close:hover {
    color: #6b7280;
    background-color: #f3f4f6;
}

/* Button hover effects */
.btn-animated {
    transition: all 0.2s ease-in-out;
}

.btn-animated:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-animated:active {
    transform: translateY(0);
}

/* Required field styling */
input[required]:invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 1px #ef4444;
}

input[required]:valid {
    border-color: #10b981;
}

input[required]:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    border-color: #3b82f6;
}
</style>

<div class="container mx-auto p-1">
    <!-- Notification Container -->
    <div id="notification-container" class="notification-container"></div>
    <h1 class="text-2xl font-bold mb-4">Daftar Tagihan Kontainer Sewa</h1>

    <!-- Action Buttons Section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Primary Actions -->
                @can('tagihan-kontainer-sewa-create')
                <a href="{{ route('daftar-tagihan-kontainer-sewa.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Tagihan
                </a>
                @endcan

                <!-- Buat Group -->
                @can('tagihan-kontainer-sewa-create')
                <a href="{{ route('daftar-tagihan-kontainer-sewa.create-group') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Buat Group
                </a>
                @endcan

                <!-- Import Data (Old) -->
                @can('tagihan-kontainer-sewa-create')
                <a href="{{ route('daftar-tagihan-kontainer-sewa.import') }}" class="bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Import Data
                </a>
                @endcan

                <!-- Import CSV (New with Modal) -->
                @can('tagihan-kontainer-sewa-create')
                <button type="button" onclick="openImportModal()" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center font-medium shadow-sm border border-cyan-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    📊 Import CSV
                </button>
                @endcan

                <!-- Export Data -->
                @can('tagihan-kontainer-sewa-create')
                <button type="button" id="btnExport" class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Data
                </button>
                @endcan

                <!-- Export Template -->
                @can('tagihan-kontainer-sewa-create')
                <a href="{{ route('daftar-tagihan-kontainer-sewa.export-template') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Template
                </a>
                @endcan
            </div>

            <!-- Quick Filter Buttons -->
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Quick Filter:</span>

                <!-- Filter Semua -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}"
                   onclick="if(typeof saveCheckboxState === 'function') saveCheckboxState();"
                   class="px-3 py-1 text-xs rounded-full border {{ !request()->anyFilled(['status', 'status_pranota', 'vendor', 'size']) ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} transition-colors duration-150">
                    📋 Semua
                </a>

                <!-- Filter Ongoing -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index', ['status' => 'ongoing']) }}"
                   onclick="if(typeof saveCheckboxState === 'function') saveCheckboxState();"
                   class="px-3 py-1 text-xs rounded-full border {{ request('status') == 'ongoing' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-green-50' }} transition-colors duration-150">
                    🟢 Ongoing
                </a>

                <!-- Filter Belum Pranota -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index', ['status_pranota' => 'null']) }}"
                   onclick="if(typeof saveCheckboxState === 'function') saveCheckboxState();"
                   class="px-3 py-1 text-xs rounded-full border {{ request('status_pranota') == 'null' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-orange-50' }} transition-colors duration-150">
                    🔄 Belum Pranota
                </a>

                <!-- Filter Vendor ZONA -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index', ['vendor' => 'ZONA']) }}"
                   onclick="if(typeof saveCheckboxState === 'function') saveCheckboxState();"
                   class="px-3 py-1 text-xs rounded-full border {{ request('vendor') == 'ZONA' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-purple-50' }} transition-colors duration-150">
                    🏢 ZONA
                </a>

                <!-- Filter 40ft -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index', ['size' => '40']) }}"
                   onclick="if(typeof saveCheckboxState === 'function') saveCheckboxState();"
                   class="px-3 py-1 text-xs rounded-full border {{ request('size') == '40' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50' }} transition-colors duration-150">
                    📦 40ft
                </a>
            </div>
        </div>


    </div>

    <!-- Bulk Actions Section -->
    <div id="bulkActions" class="hidden mb-6 bg-blue-50 border border-blue-200 rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-800">Aksi Bulk</span>
                </div>
                <span id="selection-info" class="text-sm text-blue-600"><span id="selected-count">0</span> item dipilih</span>
                <div class="flex items-center gap-2">
                    @can('tagihan-kontainer-delete')
                    <button type="button" id="btnBulkDelete" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Hapus Terpilih
                    </button>
                    @endcan
                    <button type="button" id="btnBulkStatus" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Update Status
                    </button>
                    @can('tagihan-kontainer-delete')
                    <button type="button" onclick="ungroupSelectedContainers()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Hapus Group
                    </button>
                    @endcan
                    @can('tagihan-kontainer-sewa-update')
                    <button type="button" onclick="bulkAddInvoice()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Tambah Invoice
                    </button>
                    @endcan
                    @can('tagihan-kontainer-sewa-update')
                    <button type="button" onclick="bulkEditGroupInfo()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Edit Group
                    </button>
                    @endcan
                </div>
            </div>
            <button type="button" id="btnCancelSelection" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Batal
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form action="{{ route('daftar-tagihan-kontainer-sewa.index') }}" method="GET" class="space-y-4" id="searchFilterForm" onsubmit="if(typeof saveCheckboxState === 'function') saveCheckboxState();">
            @php
                // Precompute relatedGroups early so toggle buttons can reflect auto-detect state
                if (request('q')) {
                    $searchTerm = request('q');
                    $matchingContainers = \App\Models\DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                        ->whereNotNull('group')
                        ->where('group', '!=', '')
                        ->get();
                    $relatedGroups = $matchingContainers->pluck('group')->unique();
                }
            @endphp
            <!-- Search Input Row -->
            <div class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <input
                        type="search"
                        name="q"
                        id="searchInput"
                        value="{{ request('q') }}"
                        placeholder="Cari berdasarkan: nomor kontainer, vendor, group, atau invoice vendor..."
                        class="w-full border border-gray-300 rounded-lg px-2 py-2 pr-10 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        onkeydown="if(event.key === 'Enter' && typeof saveCheckboxState === 'function') saveCheckboxState();"
                    />
                    @if(request('q'))
                        <div class="text-sm text-gray-500 mt-1">Hasil pencarian untuk: <strong>{{ request('q') }}</strong></div>
                    @endif
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                {{-- Search mode toggle buttons (normal / group) --}}
                @php
                    $explicitMode = request('search_mode');
                    $autoGroupMode = (isset($relatedGroups) && $relatedGroups->isNotEmpty());
                    if ($explicitMode === 'group') {
                        $isGroupModeActive = true;
                    } elseif ($explicitMode === 'normal') {
                        $isGroupModeActive = false;
                    } else {
                        $isGroupModeActive = $autoGroupMode;
                    }
                @endphp

                <input type="hidden" name="search_mode" id="search_mode" value="{{ $explicitMode ?? ($isGroupModeActive ? 'group' : 'normal') }}">
                <div class="flex items-center gap-2">
                    <button type="button" id="btnNormalMode" title="Mode Pencarian Biasa" class="px-3 py-1 text-sm rounded-lg border {{ $isGroupModeActive ? 'bg-white text-gray-700' : 'bg-blue-600 text-white' }}">Pencarian Biasa</button>
                    <button type="button" id="btnGroupMode" title="Mode Pencarian Grup" class="px-3 py-1 text-sm rounded-lg border {{ $isGroupModeActive ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">Mode Pencarian Grup</button>
                </div>
            </div>

            @if(request('q'))
                @php
                    // Check if we're in group search mode; precomputed $relatedGroups is used if available
                    if (!isset($relatedGroups)) {
                        $searchTerm = request('q');
                        $matchingContainers = \App\Models\DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                            ->whereNotNull('group')
                            ->where('group', '!=', '')
                            ->get();
                        $relatedGroups = $matchingContainers->pluck('group')->unique();
                    }

                    $explicitMode = request('search_mode');
                    $autoGroupMode = $relatedGroups->isNotEmpty();
                    if ($explicitMode === 'group') {
                        $isGroupSearch = true;
                    } elseif ($explicitMode === 'normal') {
                        $isGroupSearch = false;
                    } else {
                        $isGroupSearch = $autoGroupMode;
                    }
                @endphp

                @if($isGroupSearch)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <div class="text-sm">
                            <span class="font-medium text-blue-800">Mode Pencarian Grup:</span>
                            @if($relatedGroups->count() === 1)
                                <span class="text-blue-700">Menampilkan semua kontainer dalam grup "{{ $relatedGroups->first() }}" yang terkait dengan "{{ $searchTerm }}"</span>
                            @else
                                <span class="text-blue-700">Menampilkan kontainer dari {{ $relatedGroups->count() }} grup yang terkait dengan "{{ $searchTerm }}": {{ $relatedGroups->join(', ') }}</span>
                            @endif
                        </div>
                        <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="ml-auto text-blue-600 hover:text-blue-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            @endif

            <!-- Filter Row -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Filter:</label>
                </div>

                <!-- Status Kontainer Filter -->
                <select name="status" class="border border-green-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm bg-green-50">
                    <option value="">📋 Semua Status Kontainer</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>
                        🟢 Ongoing (Kontainer Aktif)
                    </option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                        ✅ Selesai (Kontainer Dikembalikan)
                    </option>
                    @foreach(($statusOptions ?? []) as $value => $label)
                        @if(!in_array($value, ['ongoing', 'selesai']))
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endif
                    @endforeach
                </select>

                <!-- Filter by Vendor -->
                <select name="vendor" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Semua Vendor</option>
                    <option value="DPE" {{ request('vendor') == 'DPE' ? 'selected' : '' }}>DPE</option>
                    <option value="ZONA" {{ request('vendor') == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                    @foreach(($vendors ?? []) as $vendor)
                        <option value="{{ $vendor }}" {{ request('vendor') == $vendor ? 'selected' : '' }}>
                            {{ $vendor }} (Dynamic)
                        </option>
                    @endforeach
                </select>

                <!-- Filter by Size -->
                <select name="size" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Semua Size</option>
                    <option value="20" {{ request('size') == '20' ? 'selected' : '' }}>20'</option>
                    <option value="40" {{ request('size') == '40' ? 'selected' : '' }}>40'</option>
                    @foreach(($sizes ?? []) as $size)
                        <option value="{{ $size }}" {{ request('size') == $size ? 'selected' : '' }}>
                            {{ $size }}' (Dynamic)
                        </option>
                    @endforeach
                </select>

                <!-- Filter by Status Pranota -->
                <select name="status_pranota" class="border border-orange-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-[10px] bg-orange-50">
                    <option value="">Semua Status Pranota</option>
                    <option value="null" {{ request('status_pranota') == 'null' ? 'selected' : '' }}>
                        🔄 Belum Masuk Pranota
                    </option>
                    <option value="included" {{ request('status_pranota') == 'included' ? 'selected' : '' }}>
                        🔵 Included (Draft)
                    </option>
                    <option value="invoiced" {{ request('status_pranota') == 'invoiced' ? 'selected' : '' }}>
                        🟡 Invoiced (Terkirim)
                    </option>
                    <option value="paid" {{ request('status_pranota') == 'paid' ? 'selected' : '' }}>
                        🟢 Paid (Lunas)
                    </option>
                    <option value="cancelled" {{ request('status_pranota') == 'cancelled' ? 'selected' : '' }}>
                        🔴 Cancelled (Dibatalkan)
                    </option>
                </select>

                <!-- Filter by Status Invoice -->
                <select name="status_invoice" class="border border-purple-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-purple-50">
                    <option value="">📋 Status Invoice</option>
                    <option value="null" {{ request('status_invoice') == 'null' ? 'selected' : '' }}>
                        ⚪ Belum Ada Invoice
                    </option>
                    <option value="draft" {{ request('status_invoice') == 'draft' ? 'selected' : '' }}>
                        ⚫ Draft
                    </option>
                    <option value="submitted" {{ request('status_invoice') == 'submitted' ? 'selected' : '' }}>
                        🟡 Submitted
                    </option>
                    <option value="approved" {{ request('status_invoice') == 'approved' ? 'selected' : '' }}>
                        🔵 Approved
                    </option>
                    <option value="paid" {{ request('status_invoice') == 'paid' ? 'selected' : '' }}>
                        🟢 Paid
                    </option>
                    <option value="cancelled" {{ request('status_invoice') == 'cancelled' ? 'selected' : '' }}>
                        🔴 Cancelled
                    </option>
                </select>

                <!-- Filter by Periode -->
                <select name="periode" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Semua Periode</option>
                    <option value="1" {{ request('periode') == '1' ? 'selected' : '' }}>Periode 1</option>
                    <option value="2" {{ request('periode') == '2' ? 'selected' : '' }}>Periode 2</option>
                    <option value="3" {{ request('periode') == '3' ? 'selected' : '' }}>Periode 3</option>
                    <option value="4" {{ request('periode') == '4' ? 'selected' : '' }}>Periode 4</option>
                    <option value="5" {{ request('periode') == '5' ? 'selected' : '' }}>Periode 5</option>
                    @foreach(($periodes ?? []) as $periode)
                        <option value="{{ $periode }}" {{ request('periode') == $periode ? 'selected' : '' }}>
                            Periode {{ $periode }} (Dynamic)
                        </option>
                    @endforeach
                </select>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 ml-auto">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>

                    @if(request()->anyFilled(['q', 'vendor', 'size', 'periode', 'status', 'status_pranota']))
                        <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <!-- Active Filters Display -->
            @if(request()->anyFilled(['q', 'vendor', 'size', 'periode', 'status', 'status_pranota']))
                <div class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <svg class="h-4 w-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-700">Filter aktif:</span>
                    <div class="flex flex-wrap gap-1">
                        @if(request('q'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Pencarian: "{{ request('q') }}"
                            </span>
                        @endif
                        @if(request('vendor'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Vendor: {{ request('vendor') }}
                            </span>
                        @endif
                        @if(request('size'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Size: {{ request('size') }}'
                            </span>
                        @endif
                        @if(request('periode'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Periode: {{ request('periode') }}
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Status:
                                @if(request('status') == 'ongoing')
                                    🟢 Ongoing (Kontainer Aktif)
                                @elseif(request('status') == 'selesai')
                                    ✅ Selesai (Kontainer Dikembalikan)
                                @else
                                    {{ ucfirst(request('status')) }}
                                @endif
                            </span>
                        @endif
                        @if(request('status_pranota'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Status Pranota:
                                @if(request('status_pranota') == 'null')
                                    🔄 Belum Masuk Pranota
                                @elseif(request('status_pranota') == 'included')
                                    🔵 Included (Draft)
                                @elseif(request('status_pranota') == 'invoiced')
                                    🟡 Invoiced (Terkirim)
                                @elseif(request('status_pranota') == 'paid')
                                    🟢 Paid (Lunas)
                                @elseif(request('status_pranota') == 'cancelled')
                                    🔴 Cancelled (Dibatalkan)
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </form>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    {{-- Debug Info --}}
    @if(config('app.debug'))
    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded text-xs">
        <strong>Debug Info:</strong>
        Vendors: {{ count($vendors ?? []) }} |
        Sizes: {{ count($sizes ?? []) }} |
        Periodes: {{ count($periodes ?? []) }} |
        Status Options: {{ count($statusOptions ?? []) }}
    </div>
    @endif

    <!-- Summary Information -->
    @if(isset($tagihans) && $tagihans->count() > 0)
    <div class="mb-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Items -->
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Total Kontainer</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tagihans->total() }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Grand Total DPP -->
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Total DPP</p>
                        <p class="text-xl font-bold text-green-600 mt-1">
                            @php
                                $totalDPP = $tagihans->sum('dpp');
                            @endphp
                            Rp {{ number_format($totalDPP, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Grand Total Amount -->
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Grand Total</p>
                        <p class="text-xl font-bold text-indigo-600 mt-1">
                            @php
                                $grandTotal = $tagihans->sum('grand_total');
                            @endphp
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average per Container -->
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Rata-rata / Kontainer</p>
                        <p class="text-xl font-bold text-purple-600 mt-1">
                            @php
                                $avgPerContainer = $tagihans->count() > 0 ? $grandTotal / $tagihans->count() : 0;
                            @endphp
                            Rp {{ number_format($avgPerContainer, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info Row -->
        <div class="mt-3 pt-3 border-t border-blue-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div class="flex items-center space-x-2">
                    <span class="text-gray-500">Halaman ini:</span>
                    <span class="font-semibold text-gray-700">{{ $tagihans->count() }} kontainer</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-gray-500">Total PPN:</span>
                    <span class="font-semibold text-gray-700">Rp {{ number_format($tagihans->sum('ppn'), 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-gray-500">Total PPh:</span>
                    <span class="font-semibold text-gray-700">Rp {{ number_format($tagihans->sum('pph'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="max-w-full mx-auto px-1">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <!-- Rows per page control -->
            <div class="px-4 py-2 border-b border-gray-200">
                @include('components.rows-per-page')
            </div>
            
            <!-- Table Section with Sticky Header -->
            <div class="table-container overflow-x-auto max-h-screen">
                <table class="min-w-full divide-y divide-gray-200 compact-table resizable-table" id="tagihanKontainerSewaTable" style="min-width: 1400px;">
                    <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                <tr class="border-b border-gray-200"><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-wider" style="width: 30px;" style="position: relative;">
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="select-all" class="checkbox-compact text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" style="width: 12px; height: 12px;">
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-wider" style="width: 35px;" style="position: relative;">
                        <span>No</span>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight" style="min-width: 70px;" style="position: relative;">
                        <div class="flex items-center space-x-1">
                            <span>Grup</span>
                            <div class="relative group">
                                <svg class="icon-compact text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="absolute invisible group-hover:visible bg-gray-800 text-white text-xs rounded p-2 bottom-full left-1/2 transform -translate-x-1/2 mb-1 whitespace-nowrap z-20">
                                    Format: TK(2)+Cetak(1)+Tahun(2)+Bulan(2)+Running(7)
                                    <div class="w-3 h-3 bg-gray-800 transform rotate-45 absolute top-full left-1/2 -translate-x-1/2 -mt-1"></div>
                                </div>
                            </div>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight" style="min-width: 60px;" style="position: relative;">
                        <div class="flex items-center space-x-0.5">
                            <span>Vendor</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight" style="min-width: 90px;" style="position: relative;">
                        <div class="flex items-center space-x-1">
                            <span>Nomor Kontainer</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight" style="min-width: 35px;" style="position: relative;">
                        <div class="flex items-center justify-center space-x-0.5">
                            <span>Size</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight" style="min-width: 45px;" style="position: relative;">
                        <div class="flex items-center justify-center space-x-0.5">
                            <span>Periode</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight" style="min-width: 70px;" style="position: relative;">
                        <div class="flex items-center justify-center space-x-0.5">
                            <span>Masa</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight" style="min-width: 45px;" style="position: relative;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Tarif</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-semibold text-gray-700 uppercase tracking-tight" style="min-width: 70px;" style="position: relative;">
                        <div class="flex items-center justify-center space-x-0.5">
                            <span>DPP</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-right text-[7px] font-semibold text-gray-700 uppercase tracking-tight" style="min-width: 65px;" style="position: relative;">
                        <div class="flex items-center justify-end space-x-0.5">
                            <span>Adjustment</span>
                            <div class="relative group">
                                <svg class="icon-compact text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 8px; height: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    Penyesuaian harga DPP
                                </div>
                            </div>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-left text-[7px] font-semibold text-gray-700 uppercase tracking-tight" style="min-width: 75px;" style="position: relative;">
                        <div class="flex items-center justify-start space-x-0.5">
                            <span>Invoice Vendor</span>
                            <div class="relative group">
                                <svg class="icon-compact text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 8px; height: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    Nomor invoice dari vendor
                                </div>
                            </div>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-left text-[7px] font-semibold text-gray-700 uppercase tracking-tight" style="min-width: 65px;" style="position: relative;">
                        <div class="flex items-center justify-start space-x-0.5">
                            <span>Tanggal Vendor</span>
                            <div class="relative group">
                                <svg class="icon-compact text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 8px; height: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    Tanggal invoice vendor
                                </div>
                            </div>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-left text-[7px] font-semibold text-gray-700 uppercase tracking-tight" style="min-width: 70px;" style="position: relative;">
                        <div class="flex items-center justify-start space-x-0.5">
                            <span>Nomor Bank</span>
                            <div class="relative group">
                                <svg class="icon-compact text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 8px; height: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    Nomor rekening bank
                                </div>
                            </div>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-right text-[7px] font-semibold text-gray-700 uppercase tracking-tight" style="min-width: 55px;" style="position: relative;">
                        <div class="flex items-center justify-end space-x-0.5">
                            <span>PPN</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-right text-[7px] font-semibold text-gray-700 uppercase tracking-tight" style="min-width: 55px;" style="position: relative;">
                        <div class="flex items-center justify-end space-x-0.5">
                            <span>PPH</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-right text-[7px] font-semibold text-gray-700 uppercase tracking-tight" style="min-width: 70px;" style="position: relative;">
                        <div class="flex items-center justify-end space-x-0.5">
                            <span>Grand Total</span>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight bg-orange-50" style="min-width: 75px;" style="position: relative;">
                        <div class="flex items-center justify-center space-x-0.5">
                            <span>Status Pranota</span>
                            <div class="relative group">
                                <svg class="icon-compact text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20" style="width: 8px; height: 8px;">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="absolute invisible group-hover:visible bg-gray-800 text-white text-xs rounded p-2 bottom-full left-1/2 transform -translate-x-1/2 mb-1 whitespace-nowrap z-20">
                                    Status dalam sistem pranota
                                    <div class="w-3 h-3 bg-gray-800 transform rotate-45 absolute top-full left-1/2 -translate-x-1/2 -mt-1"></div>
                                </div>
                            </div>
                        </div>
                    <div class="resize-handle"></div></th><th class="resizable-th px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight bg-purple-50" style="min-width: 75px;" style="position: relative;">
                        <div class="flex items-center justify-center space-x-0.5">
                            <span>Status Invoice</span>
                            <div class="relative group">
                                <svg class="icon-compact text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20" style="width: 8px; height: 8px;">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="absolute invisible group-hover:visible bg-gray-800 text-white text-xs rounded p-2 bottom-full left-1/2 transform -translate-x-1/2 mb-1 whitespace-nowrap z-20">
                                    Status invoice tagihan
                                    <div class="w-3 h-3 bg-gray-800 transform rotate-45 absolute top-full left-1/2 -translate-x-1/2 -mt-1"></div>
                                </div>
                            </div>
                        </div>
                    <div class="resize-handle"></div></th><th class="px-1 py-0.5 text-center text-[7px] font-medium text-gray-500 uppercase tracking-tight bg-gray-50" style="min-width: 70px;">
                        <div class="flex items-center justify-center space-x-0.5">
                            <span>Aksi</span>
                        </div>
                    </th></tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tagihans ?? [] as $index => $tagihan)
                    @php /** @var \App\Models\DaftarTagihanKontainerSewa $tagihan */ @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900">
                            <input type="checkbox" class="row-checkbox checkbox-compact text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" value="{{ $tagihan->id }}" style="width: 12px; height: 12px;">
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] font-medium text-gray-700">
                            {{ ($tagihans->currentPage() - 1) * $tagihans->perPage() + $index + 1 }}
                        </td>
                        <!-- Kolom Group -->
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900 font-mono" style="min-width: 90px;">
                            <div class="relative group compact-cell flex items-center justify-center">
                                @if(optional($tagihan)->group)
                                    <div class="text-sm text-gray-700 w-full text-center">
                                        <div class="truncate max-w-[80px]" title="{{ $tagihan->group }}">
                                            {{ $tagihan->group }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 w-full text-center">
                                        -
                                    </div>
                                @endif

                                <!-- Edit button for group -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-blue-100 bg-opacity-50 rounded flex items-center justify-center">
                                    <button type="button" class="btn-compact bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                                            onclick="editGroupInfo({{ $tagihan->id }}, '{{ addslashes(optional($tagihan)->group ?? '') }}')"
                                            title="Edit group">
                                        <svg class="icon-compact" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900 font-medium">
                            <div class="flex items-center">
                                <div class="w-1 h-1 bg-blue-500 rounded-full mr-1"></div>
                                <span class="font-semibold">{{ optional($tagihan)->vendor ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900 font-mono">
                            {{ optional($tagihan)->nomor_kontainer ?? '-' }}
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900">
                            <span class="badge-compact inline-flex items-center rounded-full font-medium {{ optional($tagihan)->size == '20' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ optional($tagihan)->size ?? '-' }}'
                            </span>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900">
                            @php
                                // Implementasi logika periode
                                $currentPeriode = optional($tagihan)->periode ?? 1;
                                $tanggalAwal = optional($tagihan)->tanggal_awal;
                                $tanggalAkhir = optional($tagihan)->tanggal_akhir;

                                // Tentukan apakah kontainer masih berjalan
                                $isOngoing = !$tanggalAkhir && $tanggalAwal;

                                // Gunakan periode dari database (tidak dikalkulasi ulang)
                                $displayPeriode = $currentPeriode;
                            @endphp

                            @if($isOngoing)
                                <div class="flex flex-col items-center space-y-0.5">
                                    <span class="badge-compact inline-flex items-center rounded-full font-medium bg-green-100 text-green-800 animate-pulse">
                                        {{ $displayPeriode }}
                                    </span>
                                    <span class="text-[7px] text-green-600 font-medium">
                                        (Berjalan)
                                    </span>
                                </div>
                            @else
                                <span class="badge-compact inline-flex items-center rounded-full font-medium bg-indigo-100 text-indigo-800">
                                    {{ $displayPeriode ?? '-' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[7px] text-gray-900">
                            <div class="font-semibold">
                                @if(optional($tagihan)->masa)
                                    @php
                                        $masa = optional($tagihan)->masa;
                                        // Check if it's a date range format
                                        if (strpos($masa, ' - ') !== false) {
                                            $dates = explode(' - ', $masa);
                                            $formattedDates = [];
                                            foreach ($dates as $date) {
                                                try {
                                                    $formattedDates[] = \Carbon\Carbon::parse(trim($date))->format('d-M-Y');
                                                } catch (\Exception $e) {
                                                    $formattedDates[] = trim($date);
                                                }
                                            }
                                            $masa = implode(' - ', $formattedDates);
                                        } else {
                                            // Single date
                                            try {
                                                $masa = \Carbon\Carbon::parse($masa)->format('d-M-Y');
                                            } catch (\Exception $e) {
                                                // Keep original if parsing fails
                                            }
                                        }
                                    @endphp
                                    {{ $masa }}
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900">
                            @php
                                $tarif = optional($tagihan)->tarif ?? '-';
                                $isHarian = strtolower($tarif) === 'harian';
                                $isBulanan = strtolower($tarif) === 'bulanan';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[7px] font-medium {{ $isHarian ? 'bg-green-100 text-green-800' : ($isBulanan ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $tarif }}
                            </span>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-[8px] text-gray-900 text-right font-mono">
                            @php
                                $originalDpp = (float)(optional($tagihan)->dpp ?? 0);
                            @endphp
                            <div class="font-semibold text-blue-900">
                                Rp {{ number_format($originalDpp, 0, '.', ',') }}
                            </div>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-[8px] text-gray-900 text-right font-mono">
                            <div class="group relative">
                                @if(optional($tagihan)->adjustment && optional($tagihan)->adjustment != 0)
                                    @php
                                        $adjustment = (float)(optional($tagihan)->adjustment ?? 0);
                                        $isPositive = $adjustment >= 0;
                                    @endphp
                                    <div class="font-semibold {{ $isPositive ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $isPositive ? '+' : '' }}Rp {{ number_format(abs($adjustment), 0, '.', ',') }}
                                    </div>
                                    @if(optional($tagihan)->adjustment_note)
                                        <div class="text-[6px] text-gray-500 italic mt-0.5" title="{{ optional($tagihan)->adjustment_note }}">
                                            {{ Str::limit(optional($tagihan)->adjustment_note, 15) }}
                                        </div>
                                    @endif
                                @else
                                    <div class="font-medium text-gray-400 text-[8px]">
                                        Rp 0
                                    </div>
                                @endif

                                <!-- Edit adjustment button -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-cyan-100 bg-opacity-50 rounded flex items-center justify-center">
                                    <button type="button" class="text-[7px] bg-cyan-600 text-white px-1.5 py-0.5 rounded hover:bg-cyan-700 transition-colors"
                                            onclick="editAdjustment({{ $tagihan->id }}, {{ optional($tagihan)->adjustment ?? 0 }}, '{{ addslashes(optional($tagihan)->adjustment_note ?? '') }}')"
                                            title="Edit adjustment{{ optional($tagihan)->adjustment_note ? ': ' . optional($tagihan)->adjustment_note : '' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <!-- Kolom Invoice Vendor -->
                        <td class="px-1 py-0.5 whitespace-nowrap text-left text-[8px] text-gray-900" style="min-width: 120px;">
                            <div class="relative group min-h-[30px] flex items-center">
                                @if(optional($tagihan)->invoice_vendor)
                                    <div class="text-[8px] text-gray-700 w-full">
                                        <div class="truncate max-w-[110px]" title="{{ $tagihan->invoice_vendor }}">
                                            {{ $tagihan->invoice_vendor }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-[7px] text-gray-400 w-full">
                                        -
                                    </div>
                                @endif

                                <!-- Edit button for invoice vendor -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-blue-100 bg-opacity-50 rounded flex items-center justify-center">
                                    <button type="button" class="text-[7px] bg-blue-600 text-white px-1.5 py-0.5 rounded hover:bg-blue-700 transition-colors"
                                            onclick="editVendorInfo({{ $tagihan->id }}, '{{ addslashes(optional($tagihan)->invoice_vendor ?? '') }}', '{{ optional($tagihan)->tanggal_vendor ? optional($tagihan)->tanggal_vendor->format('Y-m-d') : '' }}')"
                                            title="Edit invoice vendor">
                                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <!-- Kolom Tanggal Vendor -->
                        <td class="px-1 py-0.5 whitespace-nowrap text-left text-[8px] text-gray-900" style="min-width: 100px;">
                            <div class="relative group min-h-[30px] flex items-center">
                                @if(optional($tagihan)->tanggal_vendor)
                                    <div class="text-[8px] text-gray-700 w-full">
                                        {{ optional($tagihan)->tanggal_vendor->format('d-M-Y') }}
                                    </div>
                                @else
                                    <div class="text-[7px] text-gray-400 w-full">
                                        -
                                    </div>
                                @endif

                                <!-- Edit button for tanggal vendor (same as invoice vendor) -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-blue-100 bg-opacity-50 rounded flex items-center justify-center">
                                    <button type="button" class="text-[7px] bg-blue-600 text-white px-1.5 py-0.5 rounded hover:bg-blue-700 transition-colors"
                                            onclick="editVendorInfo({{ $tagihan->id }}, '{{ addslashes(optional($tagihan)->invoice_vendor ?? '') }}', '{{ optional($tagihan)->tanggal_vendor ? optional($tagihan)->tanggal_vendor->format('Y-m-d') : '' }}')"
                                            title="Edit tanggal vendor">
                                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <!-- Kolom Nomor Bank -->
                        <td class="px-1 py-0.5 whitespace-nowrap text-left text-[8px] text-gray-900" style="min-width: 100px;">
                            <div class="relative group min-h-[30px] flex items-center">
                                @if(optional($tagihan)->nomor_bank)
                                    <div class="text-[8px] text-gray-700 w-full font-mono">
                                        {{ optional($tagihan)->nomor_bank }}
                                    </div>
                                @else
                                    <div class="text-[7px] text-gray-400 w-full">
                                        -
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-[8px] text-gray-900 text-right font-mono">
                            @php
                                // PPN dari database (sudah dihitung dengan adjustment)
                                $ppnValue = (float)(optional($tagihan)->ppn ?? 0);
                            @endphp
                            <div class="font-semibold text-green-700">
                                Rp {{ number_format($ppnValue, 0, '.', ',') }}
                            </div>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-[8px] text-gray-900 text-right font-mono">
                            @php
                                // PPH dari database (sudah dihitung dengan adjustment)
                                $pphValue = (float)(optional($tagihan)->pph ?? 0);
                            @endphp
                            <div class="font-semibold text-red-700">
                                Rp {{ number_format($pphValue, 0, '.', ',') }}
                            </div>
                        </td>
                        <td class="px-1 py-0.5 whitespace-nowrap text-[8px] text-gray-900 text-right font-mono">
                            @php
                                // Grand Total dari database (sudah dihitung dengan adjustment)
                                $grandTotalValue = (float)(optional($tagihan)->grand_total ?? 0);
                            @endphp
                            <div class="font-bold text-yellow-800">
                                Rp {{ number_format($grandTotalValue, 0, '.', ',') }}
                            </div>
                        </td>
                        <!-- Status Pranota Column -->
                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900">
                            @if($tagihan->status_pranota == 'paid')
                                {{-- Prioritas: Jika status_pranota = paid, tampilkan Lunas --}}
                                <div class="flex flex-col items-center space-y-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-green-100 text-green-800">
                                        <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Lunas
                                    </span>
                                    @if($tagihan->pranota_id)
                                        @php
                                            $pranota = \App\Models\PranotaTagihanKontainerSewa::find($tagihan->pranota_id);
                                        @endphp
                                        @if($pranota)
                                            <a href="{{ route('pranota-kontainer-sewa.show', $pranota->id) }}" class="text-[7px] text-blue-600 hover:text-blue-800 font-mono">
                                                {{ $pranota->no_invoice }}
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            @elseif($tagihan->pranota_id)
                                @php
                                    // Find in PranotaTagihanKontainerSewa 
                                    $pranota = \App\Models\PranotaTagihanKontainerSewa::find($tagihan->pranota_id);
                                @endphp
                                @if($pranota)
                                    <div class="flex flex-col items-center space-y-0.5">
                                        @if($tagihan->status_pranota == 'included')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Included
                                            </span>
                                        @elseif($tagihan->status_pranota == 'invoiced')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                                Terkirim
                                            </span>
                                        @elseif($tagihan->status_pranota == 'cancelled')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-red-100 text-red-800">
                                                <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                                Dibatalkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($tagihan->status_pranota) }}
                                            </span>
                                        @endif
                                        <a href="{{ route('pranota-kontainer-sewa.show', $pranota->id) }}" class="text-[7px] text-blue-600 hover:text-blue-800 font-mono">
                                            {{ $pranota->no_invoice }}
                                        </a>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-red-100 text-red-800">
                                        Error: Pranota tidak ditemukan
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-gray-100 text-gray-600">
                                    <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Belum masuk pranota
                                </span>
                            @endif
                        </td>

                        <!-- Status Invoice Column -->
                        <td class="px-1 py-0.5 text-center text-[8px] whitespace-nowrap">
                            @if($tagihan->invoice_id)
                                @php
                                    $invoice = \App\Models\InvoiceKontainerSewa::find($tagihan->invoice_id);
                                @endphp
                                
                                @if($invoice && $invoice->status == 'paid')
                                    {{-- Prioritas: Jika status invoice = paid, tampilkan Lunas --}}
                                    <div class="flex flex-col items-center space-y-0.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-green-100 text-green-800">
                                            <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Paid (Lunas)
                                        </span>
                                        <a href="{{ route('invoice-tagihan-sewa.show', $invoice->id) }}" class="text-[7px] text-blue-600 hover:text-blue-800 font-mono">
                                            {{ $invoice->nomor_invoice }}
                                        </a>
                                    </div>
                                @elseif($invoice && $invoice->status == 'approved')
                                    <div class="flex flex-col items-center space-y-0.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Approved
                                        </span>
                                        <a href="{{ route('invoice-tagihan-sewa.show', $invoice->id) }}" class="text-[7px] text-blue-600 hover:text-blue-800 font-mono">
                                            {{ $invoice->nomor_invoice }}
                                        </a>
                                    </div>
                                @elseif($invoice && $invoice->status == 'submitted')
                                    <div class="flex flex-col items-center space-y-0.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            Terkirim
                                        </span>
                                        <a href="{{ route('invoice-tagihan-sewa.show', $invoice->id) }}" class="text-[7px] text-blue-600 hover:text-blue-800 font-mono">
                                            {{ $invoice->nomor_invoice }}
                                        </a>
                                    </div>
                                @elseif($invoice && $invoice->status == 'cancelled')
                                    <div class="flex flex-col items-center space-y-0.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-red-100 text-red-800">
                                            <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            Dibatalkan
                                        </span>
                                        <a href="{{ route('invoice-tagihan-sewa.show', $invoice->id) }}" class="text-[7px] text-blue-600 hover:text-blue-800 font-mono">
                                            {{ $invoice->nomor_invoice }}
                                        </a>
                                    </div>
                                @else
                                    {{-- Status invoice lainnya (draft, dll) atau invoice ditemukan - tampilkan sebagai "Included" --}}
                                    <div class="flex flex-col items-center space-y-0.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-purple-100 text-purple-800">
                                            <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                            </svg>
                                            Included
                                        </span>
                                        <a href="{{ route('invoice-tagihan-sewa.show', $invoice->id) }}" class="text-[7px] text-blue-600 hover:text-blue-800 font-mono">
                                            {{ $invoice->nomor_invoice }}
                                        </a>
                                    </div>
                                @endif
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[7px] font-medium bg-gray-100 text-gray-600">
                                    <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Belum masuk invoice
                                </span>
                            @endif
                        </td>

                        <td class="px-1 py-0.5 whitespace-nowrap text-center text-[8px] text-gray-900">
                            <div class="flex items-center justify-center space-x-1">
                                <a href="{{ route('daftar-tagihan-kontainer-sewa.show', $tagihan->id) }}" class="inline-flex items-center px-2 py-1 rounded text-[7px] font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                                    <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Lihat
                                </a>
                                <a href="{{ route('daftar-tagihan-kontainer-sewa.edit', $tagihan->id) }}" class="inline-flex items-center px-2 py-1 rounded text-[7px] font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors">
                                    <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('daftar-tagihan-kontainer-sewa.destroy', $tagihan->id) }}" method="POST" onsubmit="return confirm('Hapus tagihan kontainer ini? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1 rounded text-[7px] font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                                        <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                                @can('audit-log-view')
                                <button type="button" class="audit-log-btn inline-flex items-center px-2 py-1 rounded text-[7px] font-medium bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors"
                                        data-model="{{ get_class($tagihan) }}"
                                        data-id="{{ $tagihan->id }}"
                                        title="Lihat Riwayat">
                                    <svg class="w-2 h-2 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Audit
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                        <td class="px-2 py-2 text-center text-xs font-medium bg-gray-100 text-gray-800 " colspan="17">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data tagihan</p>
                                <p class="text-sm text-gray-500 mt-1">Mulai dengan menambahkan tagihan baru</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
            </div>
        </div>
    </div>
    @if(isset($tagihans) && $tagihans instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                    @if(request()->anyFilled(['q', 'vendor', 'size', 'periode']))
                        @php
                            $searchTerm = request('q');
                            $foundContainer = null;
                            $isGroupSearch = false;
                            $uniqueContainers = 0;

                            if ($searchTerm) {
                                $foundContainer = \App\Models\DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')->first();
                                $isGroupSearch = $foundContainer && $foundContainer->group;

                                if ($isGroupSearch) {
                                    // Count unique containers in the group
                                    $uniqueContainers = \App\Models\DaftarTagihanKontainerSewa::where('group', $foundContainer->group)
                                        ->distinct('nomor_kontainer')
                                        ->count('nomor_kontainer');
                                }
                            }
                        @endphp

                        @if($isGroupSearch)
                            <span class="font-medium">Ditemukan {{ $tagihans->total() ?? 0 }} periode dari {{ $uniqueContainers }} kontainer</span>
                            dalam grup "{{ $foundContainer->group }}"
                            (Menampilkan {{ $tagihans->firstItem() ?? 0 }} - {{ $tagihans->lastItem() ?? 0 }})
                        @else
                            <span class="font-medium">Ditemukan {{ $tagihans->total() ?? 0 }} hasil</span>
                            (Menampilkan {{ $tagihans->firstItem() ?? 0 }} - {{ $tagihans->lastItem() ?? 0 }})
                        @endif
                    @else
                        <span class="font-medium">Menampilkan {{ $tagihans->firstItem() ?? 0 }} - {{ $tagihans->lastItem() ?? 0 }}</span>
                        dari <span class="font-medium">{{ $tagihans->total() ?? 0 }}</span> total data
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    @include('components.modern-pagination', ['paginator' => $tagihans])
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

<style>
/* Sticky Table Header Styles */
.sticky-table-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251); /* bg-gray-50 */
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Enhanced table container for better scrolling */
.table-container {
    max-height: calc(100vh - 250px); /* More compact spacing */
    overflow-y: auto;
    border: 1px solid rgb(229 231 235); /* border-gray-200 */
    border-radius: 0.5rem;
}

/* Smooth scrolling for better UX */
.table-container {
    scroll-behavior: smooth;
}

/* Table header cells need specific background to avoid transparency issues */
.sticky-table-header th {
    background-color: rgb(249 250 251) !important;
    border-bottom: 1px solid rgb(229 231 235);
}

/* Optional: Add a subtle border when scrolling */
.table-container.scrolled .sticky-table-header {
    border-bottom: 2px solid rgb(59 130 246); /* blue-500 */
}

/* Ensure dropdown menus appear above sticky header */
.relative.group .absolute {
    z-index: 20;
}

/* Compact table styling */
.compact-table th {
    padding: 2px 3px !important;
    font-size: 10px !important;
    font-family: Arial, sans-serif !important;
    line-height: 1.0 !important;
}

.compact-table td {
    padding: 1px 2px !important;
    font-size: 10px !important;
    font-family: Arial, sans-serif !important;
    line-height: 1.1 !important;
}

.compact-table .compact-cell {
    min-height: 20px !important;
}

.compact-table .badge-compact {
    padding: 1px 4px !important;
    font-size: 10px !important;
    font-family: Arial, sans-serif !important;
    font-weight: 500;
}

.compact-table .btn-compact {
    padding: 1px 3px !important;
    font-size: 10px !important;
    font-family: Arial, sans-serif !important;
}

.compact-table .icon-compact {
    width: 10px !important;
    height: 10px !important;
}

.compact-table .checkbox-compact {
    width: 12px !important;
    height: 12px !important;
}

/* Override all table cells for compact layout */
.compact-table tbody td {
    padding: 1px 2px !important;
    font-size: 10px !important;
    font-family: Arial, sans-serif !important;
    line-height: 1.1 !important;
}

/* Specific adjustments for different cell types */
.compact-table tbody td.text-right {
    text-align: right !important;
}

.compact-table tbody td.text-center {
    text-align: center !important;
}

.compact-table tbody td.text-left {
    text-align: left !important;
}

/* Compact buttons and inputs inside table */
.compact-table button {
    padding: 1px 3px !important;
    font-size: 10px !important;
    font-family: Arial, sans-serif !important;
}

.compact-table input {
    font-size: 10px !important;
    font-family: Arial, sans-serif !important;
}

.compact-table .badge {
    padding: 1px 3px !important;
    font-size: 10px !important;
    font-family: Arial, sans-serif !important;
}
</style>

@push('scripts')
<script>    

// Global function to save checkbox state - must be defined before DOMContentLoaded 
// so it can be called from form onsubmit attribute
window.saveCheckboxState = function() {
    try {
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        
        // Get existing saved IDs from localStorage
        const rawData = localStorage.getItem('daftar_tagihan_checked_ids');
        // Ensure all saved IDs are strings for consistent comparison
        const existingSavedIds = (JSON.parse(rawData || '[]')).map(String);
        
        // Get IDs of checkboxes on current page
        const currentPageIds = [];
        rowCheckboxes.forEach(checkbox => {
            currentPageIds.push(String(checkbox.value));
        });
        
        // Remove IDs that exist on current page from saved list
        const savedIdsNotOnCurrentPage = existingSavedIds.filter(id => !currentPageIds.includes(id));
        
        // Get checked IDs from current page
        const currentPageCheckedIds = [];
        rowCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                currentPageCheckedIds.push(String(checkbox.value));
            }
        });
        
        // Merge: IDs not on current page + currently checked IDs
        const mergedIds = [...new Set([...savedIdsNotOnCurrentPage, ...currentPageCheckedIds])];
        
        localStorage.setItem('daftar_tagihan_checked_ids', JSON.stringify(mergedIds));
        console.log('Global saveCheckboxState called, saved:', mergedIds.length, 'items');
    } catch (error) {
        console.error('Error in global saveCheckboxState:', error);
    }
};

// Global function to clear saved state
window.clearSavedState = function() {
    localStorage.removeItem('daftar_tagihan_checked_ids');
    console.log('Cleared saved checkbox state');
    
    // Remove badge if exists
    const badge = document.getElementById('hiddenSelectionBadge');
    if (badge) {
        badge.remove();
    }
};

// Checkbox functionality with state persistence
document.addEventListener('DOMContentLoaded', function() {
    // Declare variables in function scope so they're accessible to all functions
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selected-count');
    const selectionInfo = document.getElementById('selection-info');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const btnBulkStatus = document.getElementById('btnBulkStatus');
    const btnCancelSelection = document.getElementById('btnCancelSelection');

    console.log('JavaScript loaded successfully');
    console.log('Elements found:', {
        selectAllCheckbox: !!selectAllCheckbox,
        rowCheckboxes: rowCheckboxes.length,
        bulkActions: !!bulkActions,
        selectedCount: !!selectedCount
    });

    if (!rowCheckboxes || rowCheckboxes.length === 0) {
        console.warn('No row checkboxes found');
        return;
    }

    // Search mode toggles: group/normal
    const btnNormalMode = document.getElementById('btnNormalMode');
    const btnGroupMode = document.getElementById('btnGroupMode');
    const searchModeInput = document.getElementById('search_mode');
    const searchForm = document.querySelector('form[method="GET"].space-y-4');

    function setSearchMode(mode, autoSubmit = true) {
        if (!searchModeInput) return;
        searchModeInput.value = mode;
        if (btnNormalMode && btnGroupMode) {
            if (mode === 'group') {
                btnGroupMode.classList.add('bg-blue-600', 'text-white');
                btnGroupMode.classList.remove('bg-white', 'text-gray-700');
                btnNormalMode.classList.remove('bg-blue-600', 'text-white');
                btnNormalMode.classList.add('bg-white', 'text-gray-700');
            } else {
                btnNormalMode.classList.add('bg-blue-600', 'text-white');
                btnNormalMode.classList.remove('bg-white', 'text-gray-700');
                btnGroupMode.classList.remove('bg-blue-600', 'text-white');
                btnGroupMode.classList.add('bg-white', 'text-gray-700');
            }
        }

        if (autoSubmit && searchForm) {
            // Submit the GET form to apply mode change
            searchForm.submit();
        }
    }

    // Attach event listeners if buttons exist
    if (btnNormalMode) btnNormalMode.addEventListener('click', function() { setSearchMode('normal'); });
    if (btnGroupMode) btnGroupMode.addEventListener('click', function() { setSearchMode('group'); });

    // Initialize buttons styling to reflect current mode
    if (searchModeInput && searchModeInput.value) {
        setSearchMode(searchModeInput.value, false);
    }

    // Restore checkbox state from localStorage on page load
    // Use setTimeout to ensure all elements are fully loaded
    setTimeout(() => {
        restoreCheckboxState();
    }, 100);
    
    // Initialize bulk actions on page load
    updateBulkActions();

    // Handle Export button
    const btnExport = document.getElementById('btnExport');
    if (btnExport && btnExport !== null) {
        btnExport.addEventListener('click', function() {
            // Get current filter parameters from URL
            const urlParams = new URLSearchParams(window.location.search);

            // Build export URL with same filters
            let exportUrl = '{{ route("daftar-tagihan-kontainer-sewa.export") }}';
            const params = [];

            // Add all existing filters
            if (urlParams.has('vendor')) params.push('vendor=' + urlParams.get('vendor'));
            if (urlParams.has('size')) params.push('size=' + urlParams.get('size'));
            if (urlParams.has('periode')) params.push('periode=' + urlParams.get('periode'));
            if (urlParams.has('status')) params.push('status=' + urlParams.get('status'));
            if (urlParams.has('status_pranota')) params.push('status_pranota=' + urlParams.get('status_pranota'));
            if (urlParams.has('q')) params.push('q=' + urlParams.get('q'));

            if (params.length > 0) {
                exportUrl += '?' + params.join('&');
            }

            // Show loading state
            const originalText = btnExport.innerHTML;
            btnExport.disabled = true;
            btnExport.innerHTML = `
                <svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Exporting...
            `;

            // Trigger download
            window.location.href = exportUrl;

            // Reset button after delay
            setTimeout(() => {
                btnExport.disabled = false;
                btnExport.innerHTML = originalText;
                showSuccess('Berhasil', 'Data berhasil diexport ke CSV');
            }, 2000);
        });
    }

    // Handle select all checkbox
    if (selectAllCheckbox && selectAllCheckbox !== null) {
        selectAllCheckbox.addEventListener('change', function() {
            console.log('Select all checkbox changed:', this.checked);
            const isChecked = this.checked;
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBulkActions();
            saveCheckboxState(); // Save state after change
        });
    } else {
        console.warn('selectAllCheckbox element not found');
    }

    // Handle individual checkboxes
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('Individual checkbox changed:', this.checked, 'ID:', this.value);
            updateSelectAllState();
            updateBulkActions();
            saveCheckboxState(); // Save state after change
        });
    });

    // Save checkbox state to localStorage
    function saveCheckboxState() {
        try {
            // Get existing saved IDs from localStorage
            const rawData = localStorage.getItem('daftar_tagihan_checked_ids');
            const existingSavedIds = JSON.parse(rawData || '[]');
            
            // Get IDs of checkboxes on current page
            const currentPageIds = [];
            rowCheckboxes.forEach(checkbox => {
                currentPageIds.push(checkbox.value);
            });
            
            // Remove IDs that exist on current page from saved list
            // (we'll re-add the checked ones)
            const savedIdsNotOnCurrentPage = existingSavedIds.filter(id => !currentPageIds.includes(id));
            
            // Get checked IDs from current page
            const currentPageCheckedIds = [];
            rowCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    currentPageCheckedIds.push(checkbox.value);
                }
            });
            
            // Merge: IDs not on current page + currently checked IDs
            const mergedIds = [...new Set([...savedIdsNotOnCurrentPage, ...currentPageCheckedIds])];
            
            localStorage.setItem('daftar_tagihan_checked_ids', JSON.stringify(mergedIds));
            console.log('Saved checkbox state:', {
                previouslySaved: existingSavedIds.length,
                notOnCurrentPage: savedIdsNotOnCurrentPage.length,
                currentPageChecked: currentPageCheckedIds.length,
                totalSaved: mergedIds.length,
                mergedIds: mergedIds
            });
        } catch (error) {
            console.error('Error saving checkbox state:', error);
        }
    }

    // Restore checkbox state from localStorage
    function restoreCheckboxState() {
        try {
            const rawData = localStorage.getItem('daftar_tagihan_checked_ids');
            console.log('Raw localStorage data:', rawData);
            
            const savedIds = JSON.parse(rawData || '[]');
            console.log('Parsed saved IDs:', savedIds);
            console.log('Saved IDs type:', typeof savedIds, 'Length:', savedIds.length);
            console.log('Available checkboxes:', rowCheckboxes.length);
            
            if (savedIds.length > 0) {
                let restoredCount = 0;
                const availableIds = [];
                
                rowCheckboxes.forEach(checkbox => {
                    availableIds.push(checkbox.value);
                    if (savedIds.includes(checkbox.value)) {
                        checkbox.checked = true;
                        restoredCount++;
                        console.log('✓ Restored checkbox:', checkbox.value);
                    }
                });
                
                console.log('Available IDs on page:', availableIds);
                console.log(`Restored ${restoredCount} out of ${savedIds.length} saved checkboxes`);
                
                updateSelectAllState();
                updateBulkActions();
                
                // Show badge if there are items not visible on this page
                const notFoundCount = savedIds.length - restoredCount;
                if (notFoundCount > 0) {
                    showHiddenSelectionBadge(savedIds.length, notFoundCount);
                    console.log(`Showing badge: ${notFoundCount} items hidden`);
                }
                
                // Only show notification if some items were restored
                if (restoredCount > 0) {
                    setTimeout(() => {
                        const message = notFoundCount > 0 
                            ? `${restoredCount} item terlihat dicentang. ${notFoundCount} item lainnya tersimpan (tidak tampil di halaman ini).`
                            : `${restoredCount} item yang dicentang telah dipulihkan.`;
                        
                        showNotification('info', 'Centangan Dipulihkan', message, 4000);
                    }, 500);
                } else if (savedIds.length > 0) {
                    // All saved items are not visible on current page
                    console.warn('All saved items are not visible on current page');
                    setTimeout(() => {
                        showNotification('warning', 'Item Tersimpan', `${savedIds.length} item tercentang tidak tampil di halaman ini. Ubah filter/pencarian untuk melihatnya.`, 5000);
                    }, 500);
                }
            } else {
                console.log('No saved checkbox state found in localStorage');
                // Remove badge if no selections
                const existingBadge = document.getElementById('hiddenSelectionBadge');
                if (existingBadge) {
                    existingBadge.remove();
                }
            }
        } catch (error) {
            console.error('Error restoring checkbox state:', error);
            localStorage.removeItem('daftar_tagihan_checked_ids');
        }
    }

    // Show badge for hidden selected items
    function showHiddenSelectionBadge(totalSelected, notVisibleCount) {
        // Remove existing badge if any
        const existingBadge = document.getElementById('hiddenSelectionBadge');
        if (existingBadge) {
            existingBadge.remove();
        }
        
        // Create badge element
        const badge = document.createElement('div');
        badge.id = 'hiddenSelectionBadge';
        badge.className = 'fixed top-20 right-4 z-40 bg-blue-600 text-white px-4 py-3 rounded-lg shadow-lg flex flex-col gap-2 pointer-events-auto';
        badge.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <div class="font-semibold text-sm">Total ${totalSelected} item terpilih</div>
                    <div class="text-xs opacity-90">${notVisibleCount} item tidak tampil di halaman ini</div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-1 pt-2 border-t border-blue-500">
                <button onclick="document.getElementById('hiddenSelectionBadge').remove()" class="px-2 py-1 text-xs text-blue-100 hover:text-white hover:bg-blue-700 rounded transition-colors" title="Sembunyikan notifikasi ini (Pilihan TETAP ADA)">
                    Sembunyikan
                </button>
                <button onclick="clearSelectionAndBadge()" class="px-2 py-1 text-xs bg-red-500 hover:bg-red-600 text-white rounded font-medium transition-colors shadow-sm" title="Hapus semua pilihan yang tersimpan">
                    Hapus Pilihan
                </button>
            </div>
        `;
        
        document.body.appendChild(badge);
        
        // Add fade-in animation
        setTimeout(() => {
            badge.style.opacity = '0';
            badge.style.transition = 'opacity 0.3s';
            badge.style.opacity = '1';
        }, 10);
    }

    // clearSavedState function moved to global scope above

    // Auto-save checkbox state when any navigation is about to happen
    window.addEventListener('beforeunload', function() {
        saveCheckboxState();
        console.log('Page unloading, checkbox state saved');
    });

    // Handle search form submission to preserve checkbox state
    const searchForm2 = document.querySelector('form[method="GET"].space-y-4');
    if (searchForm2 && searchForm2 !== null) {
        // Save on form submit
        searchForm2.addEventListener('submit', function(e) {
            // Force save before allowing form to submit
            saveCheckboxState();
            console.log('Search form submitted, checkbox state saved');
            console.log('Saved IDs:', localStorage.getItem('daftar_tagihan_checked_ids'));
            // Allow form to continue submitting
        });
        
        // Also save when search button is clicked (before form submit)
        const searchButtons = searchForm2.querySelectorAll('button[type="submit"]');
        searchButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                saveCheckboxState();
                console.log('Search button clicked, state saved');
            });
        });
    } else {
        console.warn('searchForm element not found - using alternative selector');
        // Try alternative selector
        const altSearchForm = document.querySelector('form[method="GET"]');
        if (altSearchForm) {
            altSearchForm.addEventListener('submit', function(e) {
                saveCheckboxState();
                console.log('Search form submitted (alt selector), checkbox state saved');
                console.log('Saved IDs:', localStorage.getItem('daftar_tagihan_checked_ids'));
            });
        }
    }

    // Handle pagination links to preserve checkbox state  
    document.querySelectorAll('a[href*="daftar-tagihan-kontainer-sewa.index"]').forEach(link => {
        link.addEventListener('click', function() {
            saveCheckboxState(); // Save current state before navigation
            console.log('Pagination link clicked, checkbox state saved');
        });
    });

    // Handle filter buttons to preserve checkbox state
    document.querySelectorAll('a[href*="status="], a[href*="status_pranota="], a[href*="daftar-tagihan-kontainer-sewa"], .pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't prevent default, just save state before navigation
            saveCheckboxState(); // Save current state before navigation
            console.log('Filter/navigation link clicked, checkbox state saved');
        });
    });

    // Update select all checkbox state
    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const totalBoxes = rowCheckboxes.length;

        if (selectAllCheckbox) {
            if (checkedBoxes.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedBoxes.length === totalBoxes) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
    }

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const visibleCount = checkedBoxes.length;
        
        // Get total count from localStorage (including hidden items)
        let totalCount = visibleCount;
        try {
            const savedData = localStorage.getItem('daftar_tagihan_checked_ids');
            if (savedData) {
                const savedIds = JSON.parse(savedData);
                if (Array.isArray(savedIds) && savedIds.length > 0) {
                    totalCount = savedIds.length;
                }
            }
        } catch (error) {
            console.error('Error reading saved state:', error);
        }

        // Get elements fresh each time to avoid stale references
        const bulkActionsElement = document.getElementById('bulkActions');
        const selectedCountElement = document.getElementById('selected-count');

        console.log('updateBulkActions called, visible:', visibleCount, 'total saved:', totalCount);
        console.log('bulkActions element:', bulkActionsElement);
        console.log('selectedCount element:', selectedCountElement);

        if (selectedCountElement) {
            // Show both visible and total if different
            if (totalCount > visibleCount && visibleCount > 0) {
                selectedCountElement.textContent = `${totalCount} (${visibleCount} tampil)`;
            } else {
                selectedCountElement.textContent = totalCount;
            }
        }

        if (bulkActionsElement) {
            // Show bulk actions if there are any selections (visible or hidden)
            if (totalCount > 0) {
                console.log('Showing bulk actions - removing hidden class');
                bulkActionsElement.classList.remove('hidden');
                bulkActionsElement.style.display = 'block'; // Force show

                // Cek apakah ada item yang memiliki nomor vendor untuk tombol "Masukan ke Pranota"
                let hasItemsWithVendorNumber = false;
                let hasItemsAlreadyInPranota = false;
                checkedBoxes.forEach((checkbox, index) => {
                    const row = checkbox.closest('tr');
                    if (row) {
                        // Kolom: 12=invoice_vendor
                        const invoiceVendorElement = row.querySelector('td:nth-child(12)');
                        const invoiceVendorValue = invoiceVendorElement ? invoiceVendorElement.textContent.trim() : '';

                        // Kolom: 1=checkbox, 2=no, 3=grup, 4=vendor, 5=kontainer, 6=size, 7=periode, 8=masa, 9=tarif, 10=dpp, 11=adjustment, 12=invoice, 13=tgl_vendor, 14=nomor_bank, 15=ppn, 16=pph, 17=grand_total, 18=status_pranota
                        const statusPranotaElement = row.querySelector('td:nth-child(18)');
                        const statusPranotaValue = statusPranotaElement ? statusPranotaElement.textContent.trim() : '';

                        console.log(`Item ${index + 1}: invoiceVendorElement=`, invoiceVendorElement, `invoiceVendorValue="${invoiceVendorValue}"`);
                        console.log(`Item ${index + 1}: statusPranotaElement=`, statusPranotaElement, `statusPranotaValue="${statusPranotaValue}"`);

                        // Cek apakah item memiliki nomor vendor
                        if (invoiceVendorValue && invoiceVendorValue !== '-' && invoiceVendorValue !== '') {
                            hasItemsWithVendorNumber = true;
                            console.log(`Item ${index + 1} has valid vendor number: "${invoiceVendorValue}"`);
                        } else {
                            console.log(`Item ${index + 1} has invalid/no vendor number: "${invoiceVendorValue}"`);
                        }

                        // Cek apakah item sudah masuk pranota
                        // Case insensitive check untuk "belum masuk pranota"
                        const isNotInPranota = !statusPranotaValue ||
                                               statusPranotaValue.toLowerCase().includes('belum masuk pranota') ||
                                               statusPranotaValue === '-';

                        if (!isNotInPranota) {
                            hasItemsAlreadyInPranota = true;
                            console.log(`Item ${index + 1} already in pranota: "${statusPranotaValue}"`);
                        } else {
                            console.log(`Item ${index + 1} not in pranota: "${statusPranotaValue}"`);
                        }
                    }
                });

                console.log('Final result: hasItemsWithVendorNumber =', hasItemsWithVendorNumber, 'hasItemsAlreadyInPranota =', hasItemsAlreadyInPranota);

                // Enable/disable tombol "Buat Pranota Baru" berdasarkan validasi nomor vendor dan status pranota
                const btnMasukanPranota = document.getElementById('btnMasukanPranota');
                if (btnMasukanPranota) {
                    // Tombol aktif hanya jika ada item dengan nomor vendor DAN tidak ada yang sudah masuk pranota
                    if (hasItemsWithVendorNumber && !hasItemsAlreadyInPranota) {
                        btnMasukanPranota.disabled = false;
                        btnMasukanPranota.classList.remove('opacity-50', 'cursor-not-allowed');
                        btnMasukanPranota.title = 'Buat pranota baru dari item terpilih yang memiliki nomor vendor';
                        console.log('✓ Button "Buat Pranota Baru" ENABLED');
                    } else {
                        btnMasukanPranota.disabled = true;
                        btnMasukanPranota.classList.add('opacity-50', 'cursor-not-allowed');
                        if (hasItemsAlreadyInPranota) {
                            btnMasukanPranota.title = 'Tidak dapat membuat pranota: Beberapa item sudah masuk pranota';
                            console.log('✗ Button "Buat Pranota Baru" DISABLED: Item sudah masuk pranota');
                        } else if (!hasItemsWithVendorNumber) {
                            btnMasukanPranota.title = 'Tidak dapat membuat pranota: Pilih item yang memiliki nomor invoice vendor terlebih dahulu';
                            console.log('✗ Button "Buat Pranota Baru" DISABLED: Tidak ada item dengan nomor vendor');
                        } else {
                            btnMasukanPranota.title = 'Tidak dapat membuat pranota';
                            console.log('✗ Button "Buat Pranota Baru" DISABLED: Unknown reason');
                        }
                    }
                }
            } else {
                console.log('Hiding bulk actions - adding hidden class');
                bulkActionsElement.classList.add('hidden');
                bulkActionsElement.style.display = 'none'; // Force hide
            }
        } else {
            console.error('bulkActions element not found!');
        }
    }

    // Cancel selection
    if (btnCancelSelection && btnCancelSelection !== null) {
        btnCancelSelection.addEventListener('click', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateBulkActions();
            window.clearSavedState(); // Clear saved state when cancelled
        });
    } else {
        console.warn('btnCancelSelection element not found');
    }

    // Bulk delete handler
    if (btnBulkDelete && btnBulkDelete !== null) {
        btnBulkDelete.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Pilih minimal satu item untuk dihapus');
                return;
            }

            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            const message = `Apakah Anda yakin ingin menghapus ${checkedBoxes.length} item yang dipilih?`;

            if (confirm(message)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("daftar-tagihan-kontainer-sewa.bulk-delete") }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add method
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                // Add selected IDs
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                // Clear saved state before submission since we're deleting items
                window.clearSavedState();

                document.body.appendChild(form);
                form.submit();
            }
        });
    } else {
        console.warn('btnBulkDelete element not found');
    }

    // Bulk status update handler
    if (btnBulkStatus && btnBulkStatus !== null) {
        btnBulkStatus.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Pilih minimal satu item untuk update status');
                return;
            }

            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            const newStatus = prompt('Masukkan status pembayaran baru:\n1. belum_dibayar\n2. sudah_dibayar');

            let statusValue = '';
            if (newStatus === '1' || newStatus === 'belum_dibayar') {
                statusValue = 'belum_dibayar';
            } else if (newStatus === '2' || newStatus === 'sudah_dibayar') {
                statusValue = 'sudah_dibayar';
            }

            if (statusValue) {
                const message = `Apakah Anda yakin ingin mengubah status pembayaran ${checkedBoxes.length} item menjadi "${statusValue}"?`;
                if (confirm(message)) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("daftar-tagihan-kontainer-sewa.bulk-update-status") }}';

                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Add status
                    const statusField = document.createElement('input');
                    statusField.type = 'hidden';
                    statusField.name = 'status_pembayaran';
                    statusField.value = statusValue;
                    form.appendChild(statusField);

                    // Add selected IDs
                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            } else if (newStatus) {
                alert('Status tidak valid. Pilih:\n1. belum_dibayar\n2. sudah_dibayar');
            }
        });
    } else {
        console.warn('btnBulkStatus element not found');
    }
});

// Test function
window.testPranota = function() {
    console.log('Test function works!');
};

// Clear saved checkbox state - moved to global scope
window.clearSavedState = function() {
    localStorage.removeItem('daftar_tagihan_checked_ids');
    console.log('Cleared saved checkbox state');
};

// Clear selection and remove badge
window.clearSelectionAndBadge = function() {
    // Clear localStorage
    localStorage.removeItem('daftar_tagihan_checked_ids');
    
    // Uncheck all visible checkboxes
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    
    // Update select all
    const selectAll = document.getElementById('select-all');
    if (selectAll) selectAll.checked = false;
    
    // Remove badge
    const badge = document.getElementById('hiddenSelectionBadge');
    if (badge) badge.remove();
    
    // Hide bulk actions
    const bulkActions = document.getElementById('bulkActions');
    if (bulkActions) bulkActions.classList.add('hidden');
    
    showNotification('success', 'Pilihan Dihapus', 'Semua pilihan telah dihapus');
    
    console.log('Cleared all selections and badge');
};

// Function to ungroup selected containers
window.ungroupSelectedContainers = function() {
    console.log('ungroupSelectedContainers called');

    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

    console.log('Selected IDs for ungrouping:', selectedIds);

    if (selectedIds.length === 0) {
        alert('Pilih minimal satu kontainer untuk menghapus group');
        return;
    }

    // Check if any selected containers are not in a group
    let containersWithoutGroup = 0;
    checkedBoxes.forEach((checkbox) => {
        const row = checkbox.closest('tr');
        if (row) {
            const groupCell = row.querySelector('td:nth-child(3)'); // Group column
            const groupValue = groupCell ? groupCell.textContent.trim() : '';
            if (!groupValue || groupValue === '-') {
                containersWithoutGroup++;
            }
        }
    });

    if (containersWithoutGroup === checkedBoxes.length) {
        alert('Kontainer yang dipilih sudah tidak memiliki group');
        return;
    }

    const message = selectedIds.length === 1
        ? 'Apakah Anda yakin ingin menghapus group dari kontainer yang dipilih?'
        : `Apakah Anda yakin ingin menghapus group dari ${selectedIds.length} kontainer yang dipilih?`;

    if (!confirm(message)) {
        return;
    }

    // Show loading state
    const ungroupBtn = document.querySelector('button[onclick="ungroupSelectedContainers()"]');
    const originalText = ungroupBtn ? ungroupBtn.innerHTML : '';
    if (ungroupBtn) {
        ungroupBtn.innerHTML = '<span class="loading-spinner"></span>Menghapus Group...';
        ungroupBtn.disabled = true;
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PATCH');

    selectedIds.forEach(id => {
        formData.append('container_ids[]', id);
    });

    // Send AJAX request
    fetch('{{ route("daftar-tagihan-kontainer-sewa.ungroup-containers") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            return response.text().then(text => {
                throw new Error(`Server error: ${response.status}`);
            });
        }
    })
    .then(data => {
        if (data.success) {
            showNotification('success', 'Group Berhasil Dihapus',
                `${data.ungrouped_count} kontainer berhasil dikembalikan ke status individual. Halaman akan dimuat ulang...`);

            // Reload page immediately
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal menghapus group');
        }
    })
    .catch(error => {
        console.error('Error ungrouping containers:', error);
        showNotification('error', 'Gagal Menghapus Group', error.message || 'Terjadi kesalahan saat menghapus group');

        // Reset button state
        if (ungroupBtn) {
            ungroupBtn.innerHTML = originalText;
            ungroupBtn.disabled = false;
        }
    });
};

// Function to ungroup selected containers
window.ungroupSelectedContainers = function() {
    console.log('ungroupSelectedContainers called');

    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

    console.log('Selected IDs for ungrouping:', selectedIds);

    if (selectedIds.length === 0) {
        alert('Pilih minimal satu kontainer untuk menghapus group');
        return;
    }

    // Check if any selected containers are not in a group
    let containersWithoutGroup = 0;
    checkedBoxes.forEach((checkbox) => {
        const row = checkbox.closest('tr');
        if (row) {
            const groupCell = row.querySelector('td:nth-child(2)'); // Group column (index 2, was 7 before)
            const groupValue = groupCell ? groupCell.textContent.trim() : '';
            if (!groupValue || groupValue === '-') {
                containersWithoutGroup++;
            }
        }
    });

    if (containersWithoutGroup === checkedBoxes.length) {
        alert('Kontainer yang dipilih sudah tidak memiliki group');
        return;
    }

    const message = selectedIds.length === 1
        ? 'Apakah Anda yakin ingin menghapus group dari kontainer yang dipilih?'
        : `Apakah Anda yakin ingin menghapus group dari ${selectedIds.length} kontainer yang dipilih?`;

    if (!confirm(message)) {
        return;
    }

    // Show loading state
    const ungroupBtn = document.querySelector('button[onclick="ungroupSelectedContainers()"]');
    const originalText = ungroupBtn ? ungroupBtn.innerHTML : '';
    if (ungroupBtn) {
        ungroupBtn.innerHTML = '<span class="loading-spinner"></span>Menghapus Group...';
        ungroupBtn.disabled = true;
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PATCH');

    selectedIds.forEach(id => {
        formData.append('container_ids[]', id);
    });

    // Send AJAX request
    fetch('{{ route("daftar-tagihan-kontainer-sewa.ungroup-containers") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            return response.text().then(text => {
                throw new Error(`Server error: ${response.status}`);
            });
        }
    })
    .then(data => {
        if (data.success) {
            showNotification('success', 'Group Berhasil Dihapus',
                `${data.ungrouped_count} kontainer berhasil dikembalikan ke status individual. Halaman akan dimuat ulang...`);

            // Reload page immediately
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal menghapus group');
        }
    })
    .catch(error => {
        console.error('Error ungrouping containers:', error);
        showNotification('error', 'Gagal Menghapus Group', error.message || 'Terjadi kesalahan saat menghapus group');

        // Reset button state
        if (ungroupBtn) {
            ungroupBtn.innerHTML = originalText;
            ungroupBtn.disabled = false;
        }
    });
};

window.openDeleteGroupModal = function() {
    console.log('openDeleteGroupModal called');

    // Show loading notification
    showNotification('warning', 'Memuat Groups', 'Sedang memuat daftar group yang ada...');

    // Fetch existing groups via AJAX
    fetch('{{ route("daftar-tagihan-kontainer-sewa.groups") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to fetch groups');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.groups && data.groups.length > 0) {
            showDeleteGroupModal(data.groups);
        } else {
            showNotification('warning', 'Tidak Ada Group', 'Belum ada group yang dibuat atau semua group sudah dihapus.');
        }
    })
    .catch(error => {
        console.error('Error fetching groups:', error);
        showNotification('error', 'Error', 'Gagal memuat daftar group. Silakan coba lagi.');
    });
};

// Function to show delete group modal
window.showDeleteGroupModal = function(groups) {
    console.log('showDeleteGroupModal called with groups:', groups);

    // Create modal HTML
    const modalHTML = `
        <div id="deleteGroupModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            Hapus Group Tagihan Kontainer
                        </h3>
                        <button type="button" onclick="closeDeleteGroupModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="mt-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div class="text-sm text-yellow-800">
                                    <strong>Perhatian:</strong> Menghapus group akan menghapus pengelompokan kontainer dan mengembalikan kontainer-kontainer tersebut ke status individual (tanpa group).
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900">Daftar Group yang Tersedia</h4>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr><th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                                                <input type="checkbox" id="select-all-groups" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                            <div class="resize-handle"></div></th><th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                                                Nama Group
                                            <div class="resize-handle"></div></th><th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                                                Jumlah Kontainer
                                            <div class="resize-handle"></div></th><th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                                                Dibuat Pada
                                            <div class="resize-handle"></div></th><th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi
                                            </th></tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        ${groups.map(group => `
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="checkbox" class="group-checkbox w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500" value="${group.name}">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    ${group.name}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    ${group.count} kontainer
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    ${new Date(group.created_at).toLocaleDateString('id-ID')}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <button type="button" onclick="deleteSingleGroup('${group.name}')"
                                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                        <button type="button" onclick="closeDeleteGroupModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="button" onclick="deleteSelectedGroups()"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Group Terpilih
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal with animation
    const modal = document.getElementById('deleteGroupModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
    }, 10);

    // Add event listeners for checkboxes
    setupGroupCheckboxes();
};

// Function to setup group checkboxes
window.setupGroupCheckboxes = function() {
    const selectAllGroups = document.getElementById('select-all-groups');
    const groupCheckboxes = document.querySelectorAll('.group-checkbox');

    if (selectAllGroups && selectAllGroups !== null) {
        selectAllGroups.addEventListener('change', function() {
            groupCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }

    if (groupCheckboxes && groupCheckboxes.length > 0) {
        groupCheckboxes.forEach(checkbox => {
            if (checkbox && checkbox !== null) {
                checkbox.addEventListener('change', function() {
                    const checkedBoxes = document.querySelectorAll('.group-checkbox:checked');
                    if (selectAllGroups && selectAllGroups !== null) {
                        selectAllGroups.checked = checkedBoxes.length === groupCheckboxes.length;
                        selectAllGroups.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < groupCheckboxes.length;
                    }
                });
            }
        });
    }
};

// Function to delete single group
window.deleteSingleGroup = function(groupName) {
    if (confirm(`Apakah Anda yakin ingin menghapus group "${groupName}"?\n\nKontainer-kontainer dalam group ini akan dikembalikan ke status individual.`)) {
        deleteGroups([groupName]);
    }
};


// Function to delete selected groups
window.deleteSelectedGroups = function() {
    const selectedGroups = Array.from(document.querySelectorAll('.group-checkbox:checked')).map(cb => cb.value);

    if (selectedGroups.length === 0) {
        alert('Pilih minimal satu group untuk dihapus');
        return;
    }

    const message = selectedGroups.length === 1
        ? `Apakah Anda yakin ingin menghapus group "${selectedGroups[0]}"?`
        : `Apakah Anda yakin ingin menghapus ${selectedGroups.length} group yang dipilih?`;

    if (confirm(message + '\n\nKontainer-kontainer dalam group ini akan dikembalikan ke status individual.')) {
        deleteGroups(selectedGroups);
    }
};

// Function to delete groups via AJAX
window.deleteGroups = function(groupNames) {
    console.log('deleteGroups called with:', groupNames);

    // Show loading state
    const modal = document.getElementById('deleteGroupModal');
    const submitBtn = modal.querySelector('button[onclick="deleteSelectedGroups()"]');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.innerHTML = '<span class="loading-spinner"></span>Menghapus...';
        submitBtn.disabled = true;
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'DELETE');

    groupNames.forEach(name => {
        formData.append('group_names[]', name);
    });

    // Send AJAX request
    fetch('{{ route("daftar-tagihan-kontainer-sewa.delete-groups") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            return response.text().then(text => {
                throw new Error(`Server error: ${response.status}`);
            });
        }
    })
    .then(data => {
        if (data.success) {
            showNotification('success', 'Group Berhasil Dihapus',
                `Berhasil menghapus ${groupNames.length} group. Halaman akan dimuat ulang...`);

            // Close modal and reload page immediately
            closeDeleteGroupModal();
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal menghapus group');
        }
    })
    .catch(error => {
        console.error('Error deleting groups:', error);
        showNotification('error', 'Gagal Menghapus Group', error.message || 'Terjadi kesalahan saat menghapus group');

        // Reset button state
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
};

// Function to close delete group modal
window.closeDeleteGroupModal = function() {
    const modal = document.getElementById('deleteGroupModal');
    if (!modal) return;

    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    setTimeout(() => {
        modal.remove();
        document.body.style.overflow = 'auto';
    }, 300);
};

// Notification system functions
window.showNotification = function(type, title, message, duration = 5000) {
    const container = document.getElementById('notification-container');
    if (!container) {
        console.error('Notification container not found');
        return;
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    // Set icon based on type
    let icon = '✓';
    if (type === 'error') icon = '✕';
    if (type === 'warning') icon = '⚠';

    notification.innerHTML = `
        <div class="notification-icon">${icon}</div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;

    // Add to container
    container.appendChild(notification);

    // Trigger animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            if (notification.parentElement) {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, duration);
    }

    return notification;
};

window.showSuccess = function(title, message, duration) {
    return showNotification('success', title, message, duration);
};

window.showError = function(title, message, duration) {
    return showNotification('error', title, message, duration);
};

window.showWarning = function(title, message, duration) {
    return showNotification('warning', title, message, duration);
};

// Function to edit adjustment
window.editAdjustment = function(tagihanId, currentAdjustment, currentAdjustmentNote = '') {
    console.log('editAdjustment called:', { tagihanId, currentAdjustment, currentAdjustmentNote });

    // Check permission for updating tagihan
    @if(!auth()->user()->hasPermissionTo('tagihan-kontainer-sewa-update'))
        showNotification('error', 'Akses Ditolak', 'Anda tidak memiliki izin untuk mengedit adjustment. Diperlukan izin "Edit" pada modul Tagihan Kontainer.');
        return;
    @endif

    // Create modal HTML for adjustment editing
    const modalHTML = `
        <div id="adjustmentModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Edit Adjustment</h3>
                        <button type="button" onclick="closeAdjustmentModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="adjustmentForm" class="mt-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai Adjustment (Rp)
                                </label>
                                <input type="number" id="adjustment_value" name="adjustment_value"
                                       value="${currentAdjustment}"
                                       step="0.01"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Masukkan nilai adjustment (bisa negatif)">
                                <p class="text-xs text-gray-500 mt-1">
                                    Masukkan nilai positif untuk penambahan, negatif untuk pengurangan
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan Adjustment
                                </label>
                                <textarea id="adjustment_note" name="adjustment_note" rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Jelaskan alasan adjustment...">${currentAdjustmentNote}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" onclick="closeAdjustmentModal()"
                                    class="btn-animated px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="btn-animated px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <span class="btn-text">Simpan Adjustment</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal with animation
    const modal = document.getElementById('adjustmentModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
    }, 10);

    // Handle form submission
    const form = document.getElementById('adjustmentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
        e.preventDefault();

        const adjustmentValue = parseFloat(document.getElementById('adjustment_value').value) || 0;
        const adjustmentNote = document.getElementById('adjustment_note').value.trim();

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = submitBtn.querySelector('.btn-text');
        const originalText = btnText.textContent;
        btnText.innerHTML = '<span class="loading-spinner"></span>Menyimpan...';
        submitBtn.disabled = true;

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');
        formData.append('adjustment', adjustmentValue);
        formData.append('adjustment_note', adjustmentNote);

        // Send AJAX request
        fetch(`{{ url('daftar-tagihan-kontainer-sewa') }}/${tagihanId}/adjustment`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error(`Server error: ${response.status}`);
                });
            }
        })
        .then(data => {
            if (data.success) {
                showNotification('success', 'Adjustment Berhasil',
                    'Data berhasil disimpan. Halaman akan dimuat ulang...');

                // Close modal immediately
                closeAdjustmentModal();
                
                // Reload page immediately to get fresh data from database
                // This ensures when user clicks "Masukan ke Pranota", 
                // the values shown are the updated ones
                window.location.reload();
            } else {
                throw new Error(data.message || 'Gagal menyimpan adjustment');
            }
        })
        .catch(error => {
            console.error('Error saving adjustment:', error);
            showNotification('error', 'Gagal Menyimpan', error.message || 'Terjadi kesalahan saat menyimpan adjustment');

            // Reset button state
            btnText.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
    }
};

// Function to close adjustment modal
window.closeAdjustmentModal = function() {
    const modal = document.getElementById('adjustmentModal');
    if (!modal) return;

    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    setTimeout(() => {
        modal.remove();
        document.body.style.overflow = 'auto';
    }, 300);
};

// Function to edit vendor info
window.editVendorInfo = function(tagihanId, currentInvoice, currentTanggal) {
    console.log('editVendorInfo called:', { tagihanId, currentInvoice, currentTanggal });

    // Check permission for updating tagihan
    @if(!auth()->user()->hasPermissionTo('tagihan-kontainer-sewa-update'))
        showNotification('error', 'Akses Ditolak', 'Anda tidak memiliki izin untuk mengedit informasi vendor. Diperlukan izin "Edit" pada modul Tagihan Kontainer.');
        return;
    @endif

    // Create modal HTML for vendor info editing
    const modalHTML = `
        <div id="vendorInfoModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Edit Informasi Vendor
                        </h3>
                        <button type="button" onclick="closeVendorInfoModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="vendorInfoForm" class="mt-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Invoice Vendor
                                </label>
                                <input type="text" id="invoice_vendor_value" name="invoice_vendor" maxlength="100"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Masukkan nomor invoice vendor" value="${currentInvoice || ''}">
                                <p class="text-xs text-gray-500 mt-1">Maksimal 100 karakter</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Vendor
                                </label>
                                <input type="date" id="tanggal_vendor_value" name="tanggal_vendor"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="${currentTanggal || ''}">
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" onclick="closeVendorInfoModal()"
                                    class="btn-animated px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="btn-animated px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <span class="btn-text">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal with animation
    const modal = document.getElementById('vendorInfoModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
    }, 10);

    // Handle form submission
    const form = document.getElementById('vendorInfoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
        e.preventDefault();

        const invoiceVendor = document.getElementById('invoice_vendor_value').value.trim();
        const tanggalVendor = document.getElementById('tanggal_vendor_value').value;

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = submitBtn.querySelector('.btn-text');
        const originalText = btnText.textContent;
        btnText.innerHTML = '<span class="loading-spinner"></span>Menyimpan...';
        submitBtn.disabled = true;

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');
        formData.append('invoice_vendor', invoiceVendor);
        formData.append('tanggal_vendor', tanggalVendor);

        // Send AJAX request
        fetch(`{{ url('daftar-tagihan-kontainer-sewa') }}/${tagihanId}/vendor-info`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error(`Server error: ${response.status}`);
                });
            }
        })
        .then(data => {
            if (data.success) {
                showNotification('success', 'Informasi Vendor Berhasil Disimpan',
                    'Data berhasil disimpan. Halaman akan dimuat ulang...');

                // Close modal and reload page immediately
                closeVendorInfoModal();
                window.location.reload();
            } else {
                throw new Error(data.message || 'Gagal menyimpan informasi vendor');
            }
        })
        .catch(error => {
            console.error('Error saving vendor info:', error);
            showNotification('error', 'Gagal Menyimpan', error.message || 'Terjadi kesalahan saat menyimpan informasi vendor');

            // Reset button state
            btnText.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
    }
};

// Function to close vendor info modal
window.closeVendorInfoModal = function() {
    const modal = document.getElementById('vendorInfoModal');
    if (!modal) return;

    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    setTimeout(() => {
        modal.remove();
        document.body.style.overflow = 'auto';
    }, 300);
};

// Function to edit group info
window.editGroupInfo = function(tagihanId, currentGroup) {
    console.log('editGroupInfo called:', { tagihanId, currentGroup });

    // Check permission for updating tagihan
    @if(!auth()->user()->hasPermissionTo('tagihan-kontainer-sewa-update'))
        showNotification('error', 'Akses Ditolak', 'Anda tidak memiliki izin untuk mengedit informasi group. Diperlukan izin "Edit" pada modul Tagihan Kontainer.');
        return;
    @endif

    // Create modal HTML for group info editing
    const modalHTML = `
        <div id="groupInfoModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Edit Group
                        </h3>
                        <button type="button" onclick="closeGroupInfoModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="groupInfoForm" class="mt-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Group
                                </label>
                                <input type="text" id="group_value" name="group" maxlength="50"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Masukkan nama group" value="${currentGroup || ''}">
                                <p class="text-xs text-gray-500 mt-1">Maksimal 50 karakter</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" onclick="closeGroupInfoModal()"
                                    class="btn-animated px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="btn-animated px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <span class="btn-text">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal with animation
    const modal = document.getElementById('groupInfoModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
    }, 10);

    // Handle form submission
    const form = document.getElementById('groupInfoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
        e.preventDefault();

        const groupValue = document.getElementById('group_value').value.trim();

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = submitBtn.querySelector('.btn-text');
        const originalText = btnText.textContent;
        btnText.innerHTML = '<span class="loading-spinner"></span>Menyimpan...';
        submitBtn.disabled = true;

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');
        formData.append('group', groupValue);

        // Send AJAX request
        fetch(`{{ url('daftar-tagihan-kontainer-sewa') }}/${tagihanId}/group-info`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error(`Server error: ${response.status}`);
                });
            }
        })
        .then(data => {
            if (data.success) {
                showNotification('success', 'Group Berhasil Disimpan',
                    'Data berhasil disimpan. Halaman akan dimuat ulang...');

                // Close modal and reload page immediately
                closeGroupInfoModal();
                window.location.reload();
            } else {
                throw new Error(data.message || 'Gagal menyimpan informasi group');
            }
        })
        .catch(error => {
            console.error('Error saving group info:', error);
            showNotification('error', 'Gagal Menyimpan', error.message || 'Terjadi kesalahan saat menyimpan informasi group');

            // Reset button state
            btnText.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
    }
};

// Function to close group info modal
window.closeGroupInfoModal = function() {
    const modal = document.getElementById('groupInfoModal');
    if (!modal) return;

    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    setTimeout(() => {
        modal.remove();
        document.body.style.overflow = 'auto';
    }, 300);
};

// Function to bulk edit vendor info
window.bulkEditVendorInfo = function() {
    console.log('bulkEditVendorInfo called');

    // Check permission for updating tagihan
    @if(!auth()->user()->hasPermissionTo('tagihan-kontainer-sewa-update'))
        showNotification('error', 'Akses Ditolak', 'Anda tidak memiliki izin untuk mengedit informasi vendor. Diperlukan izin "Edit" pada modul Tagihan Kontainer.');
        return;
    @endif

    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

    console.log('Selected IDs for bulk vendor info:', selectedIds);

    if (selectedIds.length === 0) {
        alert('Pilih minimal satu kontainer untuk mengedit informasi vendor');
        return;
    }

    // Create modal HTML for bulk vendor info editing
    const modalHTML = `
        <div id="bulkVendorInfoModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Input Vendor Info Bulk (${selectedIds.length} kontainer)
                        </h3>
                        <button type="button" onclick="closeBulkVendorInfoModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="bulkVendorInfoForm" class="mt-4">
                        <div class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm text-blue-800">
                                        Informasi vendor ini akan diterapkan ke <strong>${selectedIds.length} kontainer</strong> yang dipilih
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Invoice Vendor <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="bulk_invoice_vendor" name="invoice_vendor" maxlength="100" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Masukkan nomor invoice vendor">
                                <p class="text-xs text-gray-500 mt-1">Maksimal 100 karakter</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Vendor <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="bulk_tanggal_vendor" name="tanggal_vendor" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" onclick="closeBulkVendorInfoModal()"
                                    class="btn-animated px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="btn-animated px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <span class="btn-text">Simpan ke ${selectedIds.length} Kontainer</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal with animation
    const modal = document.getElementById('bulkVendorInfoModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
    }, 10);

    // Handle form submission
    const form = document.getElementById('bulkVendorInfoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
        e.preventDefault();

        const invoiceVendor = document.getElementById('bulk_invoice_vendor').value.trim();
        const tanggalVendor = document.getElementById('bulk_tanggal_vendor').value;

        // Validation
        if (!invoiceVendor) {
            alert('Invoice vendor harus diisi');
            return;
        }

        if (!tanggalVendor) {
            alert('Tanggal vendor harus diisi');
            return;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = submitBtn.querySelector('.btn-text');
        const originalText = btnText.textContent;
        btnText.innerHTML = '<span class="loading-spinner"></span>Menyimpan...';
        submitBtn.disabled = true;

        // Process each selected item
        let completedRequests = 0;
        let successCount = 0;
        let errorCount = 0;
        const errors = [];

        selectedIds.forEach((id, index) => {
            // Prepare form data for each item
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append('invoice_vendor', invoiceVendor);
            formData.append('tanggal_vendor', tanggalVendor);

            // Send AJAX request for each item
            fetch(`{{ url('daftar-tagihan-kontainer-sewa') }}/${id}/vendor-info`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        throw new Error(`Server error for item ${index + 1}: ${response.status}`);
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    successCount++;
                } else {
                    errorCount++;
                    errors.push(`Item ${index + 1}: ${data.message || 'Unknown error'}`);
                }
            })
            .catch(error => {
                console.error(`Error saving vendor info for item ${index + 1}:`, error);
                errorCount++;
                errors.push(`Item ${index + 1}: ${error.message}`);
            })
            .finally(() => {
                completedRequests++;

                // Check if all requests are completed
                if (completedRequests === selectedIds.length) {
                    // Show summary notification
                    if (successCount > 0 && errorCount === 0) {
                        showNotification('success', 'Bulk Update Berhasil',
                            `Data berhasil disimpan untuk ${successCount} kontainer. Halaman akan dimuat ulang...`);
                    } else if (successCount > 0 && errorCount > 0) {
                        showNotification('warning', 'Bulk Update Sebagian Berhasil',
                            `${successCount} berhasil, ${errorCount} gagal. Halaman akan dimuat ulang...`);
                        console.error('Bulk update errors:', errors);
                    } else {
                        showNotification('error', 'Bulk Update Gagal',
                            `Semua ${errorCount} item gagal diupdate. Cek detail error di console.`);
                        console.error('Bulk update errors:', errors);
                    }

                    // Close modal and reload page immediately if any success
                    closeBulkVendorInfoModal();
                    if (successCount > 0) {
                        window.location.reload();
                    }
                }
            });
        });
    });
    }
};

// Function to close bulk vendor info modal
window.closeBulkVendorInfoModal = function() {
    const modal = document.getElementById('bulkVendorInfoModal');
    if (!modal) return;

    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    setTimeout(() => {
        modal.remove();
        document.body.style.overflow = 'auto';
    }, 300);
};

// Function to bulk edit group info
window.bulkEditGroupInfo = function() {
    console.log('bulkEditGroupInfo called');

    // Check permission for updating tagihan
    @if(!auth()->user()->hasPermissionTo('tagihan-kontainer-sewa-update'))
        showNotification('error', 'Akses Ditolak', 'Anda tidak memiliki izin untuk mengedit informasi group. Diperlukan izin "Edit" pada modul Tagihan Kontainer.');
        return;
    @endif

    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

    console.log('Selected IDs for bulk group info:', selectedIds);

    if (selectedIds.length === 0) {
        alert('Pilih minimal satu kontainer untuk mengedit informasi group');
        return;
    }

    // Create modal HTML for bulk group info editing
    const modalHTML = `
        <div id="bulkGroupInfoModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Edit Group Bulk (${selectedIds.length} kontainer)
                        </h3>
                        <button type="button" onclick="closeBulkGroupInfoModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="bulkGroupInfoForm" class="mt-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Group
                                </label>
                                <input type="text" id="bulk_group" name="group" maxlength="50" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Masukkan nama group">
                                <p class="text-xs text-gray-500 mt-1">Maksimal 50 karakter</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" onclick="closeBulkGroupInfoModal()"
                                    class="btn-animated px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="btn-animated px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <span class="btn-text">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal with animation
    const modal = document.getElementById('bulkGroupInfoModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
    }, 10);

    // Handle form submission
    const form = document.getElementById('bulkGroupInfoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
        e.preventDefault();

        const groupValue = document.getElementById('bulk_group').value.trim();

        if (!groupValue) {
            alert('Nama group tidak boleh kosong');
            return;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const btnText = submitBtn.querySelector('.btn-text');
        const originalText = btnText.textContent;
        btnText.innerHTML = '<span class="loading-spinner"></span>Menyimpan...';
        submitBtn.disabled = true;

        // Process each selected ID
        const updatePromises = selectedIds.map(id => {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append('group', groupValue);

            return fetch(`{{ url('daftar-tagihan-kontainer-sewa') }}/${id}/group-info`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to update record ${id}`);
                }
                return response.json();
            });
        });

        // Wait for all updates to complete
        Promise.all(updatePromises)
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            if (successCount === selectedIds.length) {
                showNotification('success', 'Group Berhasil Disimpan',
                    `Data berhasil disimpan untuk ${successCount} kontainer. Halaman akan dimuat ulang...`);

                // Close modal and reload page immediately
                closeBulkGroupInfoModal();
                window.location.reload();
            } else {
                throw new Error(`Only ${successCount} out of ${selectedIds.length} records were updated successfully`);
            }
        })
        .catch(error => {
            console.error('Error saving bulk group info:', error);
            showNotification('error', 'Gagal Menyimpan', error.message || 'Terjadi kesalahan saat menyimpan informasi group');

            // Reset button state
            btnText.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
    }
};

// Function to close bulk group info modal
window.closeBulkGroupInfoModal = function() {
    const modal = document.getElementById('bulkGroupInfoModal');
    if (!modal) return;

    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    setTimeout(() => {
        modal.remove();
        document.body.style.overflow = 'auto';
    }, 300);
};

// Function to bulk add invoice
window.bulkAddInvoice = function() {
    console.log('bulkAddInvoice called');

    // Check permission for updating tagihan
    @if(!auth()->user()->hasPermissionTo('tagihan-kontainer-sewa-update'))
        showNotification('error', 'Akses Ditolak', 'Anda tidak memiliki izin untuk mengedit informasi vendor. Diperlukan izin "Edit" pada modul Tagihan Kontainer.');
        return;
    @endif

    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

    console.log('Selected IDs for bulk invoice:', selectedIds);

    if (selectedIds.length === 0) {
        alert('Pilih minimal satu kontainer untuk menambahkan invoice');
        return;
    }

    // Create modal HTML for bulk invoice
    const modalHTML = `
        <div id="bulkInvoiceModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Tambah Invoice Vendor (${selectedIds.length} kontainer)
                        </h3>
                        <button type="button" onclick="closeBulkInvoiceModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="bulkInvoiceForm" class="mt-4">
                        <div class="space-y-4">
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm text-purple-800">
                                        Invoice ini akan diterapkan ke <strong>${selectedIds.length} kontainer</strong> yang dipilih
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Invoice Vendor <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" id="bulk_invoice_vendor" name="invoice_vendor" maxlength="100" required
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                           placeholder="Masukkan nomor invoice vendor">
                                    <button type="button" onclick="generateInvoiceNumber()" 
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium whitespace-nowrap">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Generate
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Maksimal 100 karakter. Klik "Generate" untuk membuat nomor otomatis</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Vendor <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="bulk_tanggal_vendor" name="tanggal_vendor" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" onclick="closeBulkInvoiceModal()"
                                    class="btn-animated px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="btn-animated px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <span class="btn-text">Simpan ke ${selectedIds.length} Kontainer</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal with animation
    const modal = document.getElementById('bulkInvoiceModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
    }, 10);

    // Handle form submission
    const form = document.getElementById('bulkInvoiceForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const invoiceVendor = document.getElementById('bulk_invoice_vendor').value.trim();
            const tanggalVendor = document.getElementById('bulk_tanggal_vendor').value;

            // Validation
            if (!invoiceVendor) {
                alert('Invoice vendor harus diisi');
                return;
            }

            if (!tanggalVendor) {
                alert('Tanggal vendor harus diisi');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const originalText = btnText.textContent;
            btnText.innerHTML = '<span class="loading-spinner"></span>Menyimpan...';
            submitBtn.disabled = true;

            // Process each selected item
            let completedRequests = 0;
            let successCount = 0;
            let errorCount = 0;
            const errors = [];

            selectedIds.forEach((id, index) => {
                // Prepare form data for each item
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PATCH');
                formData.append('invoice_vendor', invoiceVendor);
                formData.append('tanggal_vendor', tanggalVendor);

                // Send AJAX request for each item
                fetch(`{{ url('daftar-tagihan-kontainer-sewa') }}/${id}/vendor-info`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            throw new Error(`Server error for item ${index + 1}: ${response.status}`);
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        successCount++;
                    } else {
                        errorCount++;
                        errors.push(`Item ${index + 1}: ${data.message || 'Unknown error'}`);
                    }
                })
                .catch(error => {
                    console.error(`Error saving invoice for item ${index + 1}:`, error);
                    errorCount++;
                    errors.push(`Item ${index + 1}: ${error.message}`);
                })
                .finally(() => {
                    completedRequests++;

                    // Check if all requests are completed
                    if (completedRequests === selectedIds.length) {
                        // Show summary notification
                        if (successCount > 0 && errorCount === 0) {
                            showNotification('success', 'Bulk Invoice Berhasil',
                                `Invoice berhasil ditambahkan untuk ${successCount} kontainer. Halaman akan dimuat ulang...`);
                        } else if (successCount > 0 && errorCount > 0) {
                            showNotification('warning', 'Bulk Invoice Sebagian Berhasil',
                                `${successCount} berhasil, ${errorCount} gagal. Halaman akan dimuat ulang...`);
                            console.error('Bulk invoice errors:', errors);
                        } else {
                            showNotification('error', 'Bulk Invoice Gagal',
                                `Semua ${errorCount} item gagal diupdate. Cek detail error di console.`);
                            console.error('Bulk invoice errors:', errors);
                        }

                        // Close modal and reload page immediately if any success
                        closeBulkInvoiceModal();
                        if (successCount > 0) {
                            window.location.reload();
                        }
                    }
                });
            });
        });
    }
};

// Function to generate invoice number with format MS-MMYY-0000001
window.generateInvoiceNumber = function() {
    const invoiceField = document.getElementById('bulk_invoice_vendor');
    
    if (!invoiceField) {
        console.error('Invoice field not found');
        return;
    }

    // Show loading state
    invoiceField.value = 'Generating...';
    invoiceField.disabled = true;

    console.log('Calling generate invoice number API...');

    // Call API to generate invoice number
    fetch('{{ route("daftar-tagihan-kontainer-sewa.generate-invoice-number") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response text:', text);
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('API response data:', data);
        
        if (data.success && data.invoice_number) {
            invoiceField.value = data.invoice_number;
            console.log('✅ Successfully generated invoice number:', data.invoice_number);
            showNotification('success', 'Berhasil', 'Nomor invoice berhasil di-generate: ' + data.invoice_number);
        } else {
            console.error('API returned success=false:', data);
            throw new Error(data.message || 'Gagal generate nomor invoice');
        }
    })
    .catch(error => {
        console.error('❌ Error generating invoice number:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        
        invoiceField.value = '';
        showNotification('error', 'Error Generate Invoice', 'Gagal generate nomor invoice. Silakan input manual atau coba lagi.');
    })
    .finally(() => {
        invoiceField.disabled = false;
    });
};

// Function to close bulk invoice modal
window.closeBulkInvoiceModal = function() {
    const modal = document.getElementById('bulkInvoiceModal');
    if (!modal) return;

    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    setTimeout(() => {
        modal.remove();
        document.body.style.overflow = 'auto';
    }, 300);
};

// Function to open import CSV modal
window.openImportModal = function() {
    const modal = document.getElementById('importCsvModal');
    if (!modal) {
        console.error('Import modal not found!');
        return;
    }

    // Reset form
    document.getElementById('importCsvForm').reset();
    
    // Hide progress bar
    document.getElementById('import_progress').classList.add('hidden');
    
    // Show modal with animation
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
    }, 10);
};

// Function to close import CSV modal
window.closeImportModal = function() {
    const modal = document.getElementById('importCsvModal');
    if (!modal) return;

    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('modal-hide');
        if (modalContent) {
            modalContent.classList.remove('modal-hide');
        }
        document.body.style.overflow = 'auto';
    }, 300);
};

// Handle import CSV form submission
document.addEventListener('DOMContentLoaded', function() {
    const importForm = document.getElementById('importCsvForm');
    
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('csv_file');
            const file = fileInput.files[0];
            
            if (!file) {
                showNotification('error', 'Error', 'Silakan pilih file CSV terlebih dahulu.');
                return;
            }

            // Validate file type
            const fileName = file.name.toLowerCase();
            if (!fileName.endsWith('.csv') && !fileName.endsWith('.txt')) {
                showNotification('error', 'Format File Salah', 'File harus berformat CSV (.csv atau .txt)');
                return;
            }

            // Validate file size (max 10MB)
            if (file.size > 10 * 1024 * 1024) {
                showNotification('error', 'File Terlalu Besar', 'Ukuran file maksimal 10MB');
                return;
            }

            // Show loading state
            const submitBtn = document.getElementById('import_submit_btn');
            const btnText = document.getElementById('import_btn_text');
            const originalText = btnText.textContent;
            
            btnText.innerHTML = '<span class="loading-spinner"></span>Mengimport...';
            submitBtn.disabled = true;

            // Show progress bar
            const progressDiv = document.getElementById('import_progress');
            const progressBar = document.getElementById('progress_bar');
            const progressText = document.getElementById('progress_text');
            
            progressDiv.classList.remove('hidden');
            progressBar.style.width = '10%';
            progressText.textContent = '10%';

            // Prepare form data
            const formData = new FormData(importForm);

            // Send AJAX request
            fetch('{{ route("daftar-tagihan-kontainer-sewa.import-csv") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                progressBar.style.width = '50%';
                progressText.textContent = '50%';
                
                return response.json();
            })
            .then(data => {
                progressBar.style.width = '100%';
                progressText.textContent = '100%';
                
                console.log('Import response:', data);

                if (data.success) {
                    // Show success notification
                    const message = `✅ Import berhasil!\n\n` +
                                  `📊 Diimport: ${data.imported_count || 0} data\n` +
                                  `🔄 Diupdate: ${data.updated_count || 0} data\n` +
                                  `⏭️ Dilewati: ${data.skipped_count || 0} data`;
                    
                    showNotification('success', 'Import Berhasil', message, 8000);

                    // Show warnings if any
                    if (data.warnings && data.warnings.length > 0) {
                        setTimeout(() => {
                            const warningMsg = data.warnings.slice(0, 5).join('\n');
                            showNotification('warning', 'Peringatan', warningMsg, 10000);
                        }, 1000);
                    }

                    // Close modal and reload page
                    setTimeout(() => {
                        closeImportModal();
                        window.location.reload();
                    }, 2000);
                } else {
                    // Show error notification
                    let errorMessage = data.message || 'Terjadi kesalahan saat import data.';
                    
                    if (data.errors && data.errors.length > 0) {
                        errorMessage += '\n\n❌ Error:\n';
                        errorMessage += data.errors.slice(0, 5).map(err => 
                            `Baris ${err.row}: ${err.message}`
                        ).join('\n');
                        
                        if (data.errors.length > 5) {
                            errorMessage += `\n\n... dan ${data.errors.length - 5} error lainnya`;
                        }
                    }

                    showNotification('error', 'Import Gagal', errorMessage, 15000);

                    // Reset button
                    btnText.textContent = originalText;
                    submitBtn.disabled = false;
                    
                    // Hide progress
                    progressDiv.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Import error:', error);
                
                showNotification('error', 'Error', 
                    'Terjadi kesalahan saat mengirim data. Silakan coba lagi.\n\n' + 
                    'Detail: ' + error.message, 
                    10000
                );

                // Reset button
                btnText.textContent = originalText;
                submitBtn.disabled = false;
                
                // Hide progress
                progressDiv.classList.add('hidden');
            });
        });
    }
});


</script>

<!-- Modal Import CSV -->
<div id="importCsvModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="modal-content relative top-20 mx-auto p-6 border w-11/12 max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Import Data CSV</h3>
            <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="importCsvForm" enctype="multipart/form-data">
            @csrf
            
            <!-- File Upload -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih File CSV
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="file" 
                           id="csv_file" 
                           name="import_file" 
                           accept=".csv,.txt"
                           required
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-blue-500 p-2">
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Format: CSV dengan delimiter semicolon (;). Maksimal 10MB.
                </p>
            </div>

            <!-- Import Options -->
            <div class="mb-4 space-y-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Opsi Import
                </label>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="skip_duplicates" 
                           name="skip_duplicates" 
                           value="1"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="skip_duplicates" class="ml-2 text-sm text-gray-700">
                        Lewati data duplikat
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           id="update_existing" 
                           name="update_existing" 
                           value="1"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="update_existing" class="ml-2 text-sm text-gray-700">
                        Update data yang sudah ada
                    </label>
                </div>
            </div>

            <!-- Progress Bar (Hidden initially) -->
            <div id="import_progress" class="hidden mb-4">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium text-gray-700">Progress Import</span>
                    <span id="progress_text" class="text-sm text-gray-600">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progress_bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button type="button" 
                        onclick="closeImportModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        id="import_submit_btn"
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    <span id="import_btn_text">Import Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Include Audit Log Modal -->
@include('components.audit-log-modal')

@endpush