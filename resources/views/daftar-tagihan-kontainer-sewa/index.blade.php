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

<div class="container mx-auto p-4">
    <!-- Notification Container -->
    <div id="notification-container" class="notification-container"></div>
    <h1 class="text-2xl font-bold mb-4">Daftar Tagihan Kontainer Sewa</h1>

    <!-- Action Buttons Section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Primary Actions -->
                @can('tagihan-kontainer-create')
                <a href="{{ route('daftar-tagihan-kontainer-sewa.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Tagihan
                </a>
                @endcan

                <!-- Buat Group -->
                @can('tagihan-kontainer-create')
                <a href="{{ route('daftar-tagihan-kontainer-sewa.create-group') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Buat Group
                </a>
                @endcan

                <!-- Import Data -->
                @can('tagihan-kontainer-sewa-create')
                <a href="{{ route('daftar-tagihan-kontainer-sewa.import') }}" class="bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Import Data
                </a>
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
                   class="px-3 py-1 text-xs rounded-full border {{ !request()->anyFilled(['status', 'status_pranota', 'vendor', 'size']) ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} transition-colors duration-150">
                    📋 Semua
                </a>

                <!-- Filter Ongoing -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index', ['status' => 'ongoing']) }}"
                   class="px-3 py-1 text-xs rounded-full border {{ request('status') == 'ongoing' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-green-50' }} transition-colors duration-150">
                    🟢 Ongoing
                </a>

                <!-- Filter Belum Pranota -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index', ['status_pranota' => 'null']) }}"
                   class="px-3 py-1 text-xs rounded-full border {{ request('status_pranota') == 'null' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-orange-50' }} transition-colors duration-150">
                    🔄 Belum Pranota
                </a>

                <!-- Filter Vendor ZONA -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index', ['vendor' => 'ZONA']) }}"
                   class="px-3 py-1 text-xs rounded-full border {{ request('vendor') == 'ZONA' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-purple-50' }} transition-colors duration-150">
                    🏢 ZONA
                </a>

                <!-- Filter 40ft -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.index', ['size' => '40']) }}"
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
                    @can('pranota-kontainer-sewa-create')
                    <button type="button" id="btnMasukanPranota" onclick="masukanKePranota()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Masukan ke Pranota
                    </button>
                    @endcan
                    @can('tagihan-kontainer-delete')
                    <button type="button" onclick="ungroupSelectedContainers()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Hapus Group
                    </button>
                    @endcan
                    @can('tagihan-kontainer-sewa-update')
                    <button type="button" onclick="bulkEditVendorInfo()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Input Vendor Info
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
        <form action="{{ route('daftar-tagihan-kontainer-sewa.index') }}" method="GET" class="space-y-4">
            <!-- Search Input Row -->
            <div class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari berdasarkan: nomor kontainer, vendor, group, atau invoice vendor..."
                        class="w-full border border-gray-300 rounded-lg px-2 py-2 pr-10 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            @if(request('q'))
                @php
                    // Check if we're in group search mode
                    $searchTerm = request('q');
                    $matchingContainers = \App\Models\DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                        ->whereNotNull('group')
                        ->where('group', '!=', '')
                        ->get();
                    $relatedGroups = $matchingContainers->pluck('group')->unique();
                    $isGroupSearch = $relatedGroups->isNotEmpty();
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

    <div class="max-w-full mx-auto px-4">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <!-- Rows per page control -->
            <div class="px-6 py-3 border-b border-gray-200">
                @include('components.rows-per-page')
            </div>
            
            <!-- Table Section with Sticky Header -->
            <div class="table-container overflow-x-auto max-h-screen">
                <table class="min-w-full divide-y divide-gray-200" style="min-width: 2650px;">
                    <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                <tr class="border-b-2 border-gray-200">
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="width: 60px;">
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="select-all" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 140px;">
                        <div class="flex items-center space-x-1">
                            <span>Grup</span>
                            <div class="relative group">
                                <svg class="w-3 h-3 text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="absolute invisible group-hover:visible bg-gray-800 text-white text-xs rounded p-2 bottom-full left-1/2 transform -translate-x-1/2 mb-1 whitespace-nowrap z-20">
                                    Format: TK(2)+Cetak(1)+Tahun(2)+Bulan(2)+Running(7)
                                    <div class="w-3 h-3 bg-gray-800 transform rotate-45 absolute top-full left-1/2 -translate-x-1/2 -mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 120px;">
                        <div class="flex items-center space-x-1">
                            <span>Vendor</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 160px;">
                        <div class="flex items-center space-x-1">
                            <span>Nomor Kontainer</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 80px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Size</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 100px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Periode</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 120px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Masa</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 120px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Tgl Awal</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 120px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Tgl Akhir</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 100px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Tarif</span>
                        </div>
                    </th>
                    <th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 140px;">
                        <div class="flex items-center justify-end space-x-1">
                            <span>DPP</span>
                        </div>
                    </th>
                    <th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 140px;">
                        <div class="flex items-center justify-end space-x-1">
                            <span>Adjustment</span>
                            <div class="relative group">
                                <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    Penyesuaian harga DPP
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 200px;">
                        <div class="flex items-center justify-start space-x-1">
                            <span>Alasan Adjustment</span>
                            <div class="relative group">
                                <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    Alasan penyesuaian harga
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 150px;">
                        <div class="flex items-center justify-start space-x-1">
                            <span>Invoice Vendor</span>
                            <div class="relative group">
                                <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    Nomor invoice dari vendor
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 120px;">
                        <div class="flex items-center justify-start space-x-1">
                            <span>Tanggal Vendor</span>
                            <div class="relative group">
                                <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    Tanggal invoice vendor
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 120px;">
                        <div class="flex items-center justify-end space-x-1">
                            <span>PPN</span>
                        </div>
                    </th>
                    <th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 120px;">
                        <div class="flex items-center justify-end space-x-1">
                            <span>PPH</span>
                        </div>
                    </th>
                    <th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 160px;">
                        <div class="flex items-center justify-end space-x-1">
                            <span>Grand Total</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider " style="min-width: 140px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Status</span>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider bg-orange-50 " style="min-width: 160px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Status Pranota</span>
                            <div class="relative group">
                                <svg class="w-3 h-3 text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="absolute invisible group-hover:visible bg-gray-800 text-white text-xs rounded p-2 bottom-full left-1/2 transform -translate-x-1/2 mb-1 whitespace-nowrap z-20">
                                    Status dalam sistem pranota
                                    <div class="w-3 h-3 bg-gray-800 transform rotate-45 absolute top-full left-1/2 -translate-x-1/2 -mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider bg-gray-50" style="min-width: 140px;">
                        <div class="flex items-center justify-center space-x-1">
                            <span>Aksi</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tagihans ?? [] as $index => $tagihan)
                    @php /** @var \App\Models\DaftarTagihanKontainerSewa $tagihan */ @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                            <input type="checkbox" name="selected_items[]" value="{{ $tagihan->id }}" class="row-checkbox w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                        </td>
                        <!-- Kolom Group -->
                        <td class="px-2 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 font-mono" style="min-width: 100px;">
                            <div class="relative group min-h-[40px] flex items-center justify-center">
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
                                    <button type="button" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors"
                                            onclick="editGroupInfo({{ $tagihan->id }}, '{{ addslashes(optional($tagihan)->group ?? '') }}')"
                                            title="Edit group">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 font-medium ">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                <span class="font-semibold">{{ optional($tagihan)->vendor ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 font-mono ">
                            {{ optional($tagihan)->nomor_kontainer ?? '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center text-gray-900 ">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ optional($tagihan)->size == '20' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ optional($tagihan)->size ?? '-' }}'
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center text-gray-900 ">
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
                                <div class="flex flex-col items-center space-y-1">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 animate-pulse">
                                        {{ $displayPeriode }}
                                    </span>
                                    <span class="text-xs text-green-600 font-medium">
                                        (Berjalan)
                                    </span>
                                </div>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $displayPeriode ?? '-' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center text-gray-900 ">
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
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center text-gray-900 ">
                            <div class="text-xs bg-gray-100 px-3 py-2 rounded-lg font-medium">
                                {{ optional($tagihan)->tanggal_awal ? \Carbon\Carbon::parse(optional($tagihan)->tanggal_awal)->format('d-M-Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center text-gray-900 ">
                            @if(optional($tagihan)->tanggal_akhir)
                                <div class="flex flex-col items-center space-y-1">
                                    <div class="text-xs bg-gray-100 px-3 py-2 rounded-lg font-medium">
                                        {{ \Carbon\Carbon::parse(optional($tagihan)->tanggal_akhir)->format('d-M-Y') }}
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                </div>
                            @else
                                <div class="flex flex-col items-center space-y-2">
                                    <div class="text-xs bg-gray-100 px-3 py-2 rounded-lg font-medium text-gray-400">
                                        Belum Selesai
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 animate-pulse">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Ongoing
                                    </span>
                                    @if(optional($tagihan)->tanggal_awal)
                                        @php
                                            try {
                                                $startDate = \Carbon\Carbon::parse(optional($tagihan)->tanggal_awal);
                                                $daysSince = $startDate->diffInDays(\Carbon\Carbon::now());
                                            } catch (\Exception $e) {
                                                $daysSince = 0;
                                            }
                                        @endphp
                                        <div class="text-xs text-green-600">
                                            {{ $daysSince }} hari
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                            @php
                                $tarif = optional($tagihan)->tarif ?? '-';
                                $isHarian = strtolower($tarif) === 'harian';
                                $isBulanan = strtolower($tarif) === 'bulanan';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $isHarian ? 'bg-green-100 text-green-800' : ($isBulanan ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $tarif }}
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-[10px] text-gray-900 text-right font-mono">
                            @php
                                $originalDpp = (float)(optional($tagihan)->dpp ?? 0);
                                $adjustment = (float)(optional($tagihan)->adjustment ?? 0);
                                $adjustedDpp = $originalDpp + $adjustment;
                            @endphp
                            <div class="font-semibold text-blue-900">
                                Rp {{ number_format($adjustedDpp, 0, '.', ',') }}
                            </div>
                            @if($adjustment != 0)
                                <div class="text-xs text-gray-600 mt-1">
                                    Disesuaikan dari Rp {{ number_format($originalDpp, 0, '.', ',') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-right font-mono text-gray-900 ">
                            <div class="group relative">
                                @if(optional($tagihan)->adjustment)
                                    @php
                                        $adjustment = (float)(optional($tagihan)->adjustment ?? 0);
                                        $isPositive = $adjustment >= 0;
                                    @endphp
                                    <div class="font-semibold {{ $isPositive ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $isPositive ? '+' : '' }}Rp {{ number_format(abs($adjustment), 0, '.', ',') }}
                                    </div>
                                    <div class="text-xs {{ $isPositive ? 'text-green-600' : 'text-red-600' }} mt-1">
                                        {{ $isPositive ? '↗ Penambahan' : '↘ Pengurangan' }}
                                    </div>
                                @else
                                    <div class="font-medium text-gray-400 text-sm">
                                        -
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Tidak ada
                                    </div>
                                @endif

                                <!-- Edit adjustment inline (could be implemented later) -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-cyan-100 bg-opacity-50 rounded flex items-center justify-center">
                                    <button type="button" class="text-xs bg-cyan-600 text-white px-2 py-1 rounded hover:bg-cyan-700 transition-colors"
                                            onclick="editAdjustment({{ $tagihan->id }}, {{ optional($tagihan)->adjustment ?? 0 }})"
                                            title="Edit adjustment">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <!-- Kolom Alasan Adjustment -->
                        <td class="px-2 py-2 whitespace-nowrap text-left text-[10px] text-gray-900" style="min-width: 200px;">
                            <div class="relative group min-h-[40px] flex items-center">
                                @if(optional($tagihan)->adjustment_note)
                                    <div class="text-sm text-gray-700 w-full">
                                        <div class="truncate max-w-[180px]" title="{{ $tagihan->adjustment_note }}">
                                            {{ $tagihan->adjustment_note }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 w-full">
                                        -
                                    </div>
                                @endif

                                <!-- Edit button for adjustment note -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-blue-100 bg-opacity-50 rounded flex items-center justify-center">
                                    <button type="button" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors"
                                            onclick="editAdjustmentNote({{ $tagihan->id }}, '{{ addslashes(optional($tagihan)->adjustment_note ?? '') }}')"
                                            title="Edit alasan adjustment">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <!-- Kolom Invoice Vendor -->
                        <td class="px-2 py-2 whitespace-nowrap text-left text-[10px] text-gray-900" style="min-width: 150px;">
                            <div class="relative group min-h-[40px] flex items-center">
                                @if(optional($tagihan)->invoice_vendor)
                                    <div class="text-sm text-gray-700 w-full">
                                        <div class="truncate max-w-[130px]" title="{{ $tagihan->invoice_vendor }}">
                                            {{ $tagihan->invoice_vendor }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 w-full">
                                        -
                                    </div>
                                @endif

                                <!-- Edit button for invoice vendor -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-blue-100 bg-opacity-50 rounded flex items-center justify-center">
                                    <button type="button" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors"
                                            onclick="editVendorInfo({{ $tagihan->id }}, '{{ addslashes(optional($tagihan)->invoice_vendor ?? '') }}', '{{ optional($tagihan)->tanggal_vendor ? optional($tagihan)->tanggal_vendor->format('Y-m-d') : '' }}')"
                                            title="Edit invoice vendor">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <!-- Kolom Tanggal Vendor -->
                        <td class="px-2 py-2 whitespace-nowrap text-left text-[10px] text-gray-900" style="min-width: 120px;">
                            <div class="relative group min-h-[40px] flex items-center">
                                @if(optional($tagihan)->tanggal_vendor)
                                    <div class="text-sm text-gray-700 w-full">
                                        {{ optional($tagihan)->tanggal_vendor->format('d-M-Y') }}
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 w-full">
                                        -
                                    </div>
                                @endif

                                <!-- Edit button for tanggal vendor (same as invoice vendor) -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-blue-100 bg-opacity-50 rounded flex items-center justify-center">
                                    <button type="button" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors"
                                            onclick="editVendorInfo({{ $tagihan->id }}, '{{ addslashes(optional($tagihan)->invoice_vendor ?? '') }}', '{{ optional($tagihan)->tanggal_vendor ? optional($tagihan)->tanggal_vendor->format('Y-m-d') : '' }}')"
                                            title="Edit tanggal vendor">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-right font-mono text-gray-900 ">
                            @php
                                // Calculate adjusted DPP for PPN calculation
                                $originalDpp = (float)(optional($tagihan)->dpp ?? 0);
                                $adjustment = (float)(optional($tagihan)->adjustment ?? 0);
                                $adjustedDpp = $originalDpp + $adjustment;
                                $ppnRate = 0.11; // 11% PPN
                                $calculatedPpn = $adjustedDpp * $ppnRate;
                            @endphp
                            <div class="font-semibold text-green-700">
                                Rp {{ number_format($calculatedPpn, 0, '.', ',') }}
                            </div>
                            @if($adjustment != 0)
                                <div class="text-xs text-green-600 mt-1">
                                    Dihitung dari DPP yang disesuaikan
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-right font-mono text-gray-900 ">
                            @php
                                // Calculate PPH from adjusted DPP
                                $pphRate = 0.02; // 2% PPH (adjust as needed)
                                $calculatedPph = $adjustedDpp * $pphRate;
                            @endphp
                            <div class="font-semibold text-red-700">
                                Rp {{ number_format($calculatedPph, 0, '.', ',') }}
                            </div>
                            @if($adjustment != 0)
                                <div class="text-xs text-red-600 mt-1">
                                    Dihitung dari DPP yang disesuaikan
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-right font-mono text-gray-900 ">
                            @php
                                // Calculate grand total with adjustment impact
                                // Formula: DPP + PPN - PPH (tanpa DPP Nilai Lain)
                                $newGrandTotal = $adjustedDpp + $calculatedPpn - $calculatedPph;
                            @endphp
                            <div class="font-bold text-yellow-800">
                                Rp {{ number_format($newGrandTotal, 0, '.', ',') }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center ">
                            @php
                                $paymentStatus = strtolower((string) optional($tagihan)->status_pembayaran);
                                $isPranota = (bool) (optional($tagihan)->is_pranota ?? false);
                                $nomorPranota = trim((string) (optional($tagihan)->nomor_pranota ?? ''));
                            @endphp

                            @if(!empty($paymentStatus) && $paymentStatus === 'lunas')
                                <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Sudah Dibayar
                                </span>
                            @elseif($isPranota || !empty($nomorPranota))
                                <span @if(!empty($nomorPranota)) title="Pranota: {{ $nomorPranota }}" @endif class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Sudah Masuk Pranota
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Belum Masuk Daftar Tagihan
                                </span>
                            @endif
                        </td>
                        <!-- Status Pranota Column -->
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center ">
                            @if($tagihan->pranota_id)
                                @php
                                    // Try to find in PranotaTagihanKontainerSewa first, then fallback to Pranota
                                    $pranota = \App\Models\PranotaTagihanKontainerSewa::find($tagihan->pranota_id);
                                    if (!$pranota) {
                                        $pranota = \App\Models\Pranota::find($tagihan->pranota_id);
                                    }
                                @endphp
                                @if($pranota)
                                    <div class="flex flex-col items-center space-y-1">
                                        @if($tagihan->status_pranota == 'included')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Included
                                            </span>
                                        @elseif($tagihan->status_pranota == 'invoiced')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                                Terkirim
                                            </span>
                                        @elseif($tagihan->status_pranota == 'paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Lunas
                                            </span>
                                        @elseif($tagihan->status_pranota == 'cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                                Dibatalkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($tagihan->status_pranota) }}
                                            </span>
                                        @endif
                                        <a href="{{ route('pranota.show', $pranota->id) }}" class="text-xs text-blue-600 hover:text-blue-800 font-mono">
                                            {{ $pranota->no_invoice }}
                                        </a>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Error: Pranota tidak ditemukan
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Belum masuk pranota
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('daftar-tagihan-kontainer-sewa.show', $tagihan->id) }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Lihat
                                </a>
                                <a href="{{ route('daftar-tagihan-kontainer-sewa.edit', $tagihan->id) }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('daftar-tagihan-kontainer-sewa.destroy', $tagihan->id) }}" method="POST" onsubmit="return confirm('Hapus tagihan kontainer ini? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                                @can('audit-log-view')
                                <button type="button" class="audit-log-btn inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors"
                                        data-model="{{ get_class($tagihan) }}"
                                        data-id="{{ $tagihan->id }}"
                                        title="Lihat Riwayat">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    max-height: calc(100vh - 300px); /* Adjust based on your layout */
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
</style>

@push('scripts')
<script>


// Checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selected-count');
    const selectionInfo = document.getElementById('selection-info');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const btnBulkStatus = document.getElementById('btnBulkStatus');
    const btnBulkPranota = document.getElementById('btnBulkPranota');
    const btnCancelSelection = document.getElementById('btnCancelSelection');

    console.log('JavaScript loaded successfully');
    console.log('Elements found:', {
        selectAllCheckbox: !!selectAllCheckbox,
        rowCheckboxes: rowCheckboxes.length,
        bulkActions: !!bulkActions,
        selectedCount: !!selectedCount,
        btnBulkPranota: !!btnBulkPranota
    });

    // Initialize bulk actions on page load
    updateBulkActions();

    // Handle Export button
    const btnExport = document.getElementById('btnExport');
    if (btnExport) {
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
    selectAllCheckbox.addEventListener('change', function() {
        console.log('Select all checkbox changed:', this.checked);
        const isChecked = this.checked;
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateBulkActions();
    });

    // Handle individual checkboxes
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('Individual checkbox changed:', this.checked, 'ID:', this.value);
            updateSelectAllState();
            updateBulkActions();
        });
    });

    // Update select all checkbox state
    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const totalBoxes = rowCheckboxes.length;

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

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkedBoxes.length;

        console.log('updateBulkActions called, checked boxes:', count);
        console.log('bulkActions element:', bulkActions);
        console.log('selectedCount element:', selectedCount);

        if (selectedCount) {
            selectedCount.textContent = count;
        }

        if (bulkActions) {
            if (count > 0) {
                console.log('Showing bulk actions - removing hidden class');
                bulkActions.classList.remove('hidden');
                bulkActions.style.display = 'block'; // Force show

                // Cek apakah ada item yang memiliki grup untuk tombol "Masukan ke Pranota"
                let hasItemsWithGroup = false;
                let hasItemsAlreadyInPranota = false;
                checkedBoxes.forEach((checkbox, index) => {
                    const row = checkbox.closest('tr');
                    if (row) {
                        const groupElement = row.querySelector('td:nth-child(2)'); // Group column (index 2)
                        const groupValue = groupElement ? groupElement.textContent.trim() : '';

                        const statusPranotaElement = row.querySelector('td:nth-child(20)'); // Status Pranota column (index 20, was 21 before)
                        const statusPranotaValue = statusPranotaElement ? statusPranotaElement.textContent.trim() : '';

                        console.log(`Item ${index + 1}: groupElement=`, groupElement, `groupValue="${groupValue}"`);
                        console.log(`Item ${index + 1}: statusPranotaElement=`, statusPranotaElement, `statusPranotaValue="${statusPranotaValue}"`);

                        if (groupValue && groupValue !== '-' && groupValue !== '') {
                            hasItemsWithGroup = true;
                            console.log(`Item ${index + 1} has valid group: "${groupValue}"`);
                        } else {
                            console.log(`Item ${index + 1} has invalid/no group: "${groupValue}"`);
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

                console.log('Final result: hasItemsWithGroup =', hasItemsWithGroup, 'hasItemsAlreadyInPranota =', hasItemsAlreadyInPranota);

                // Enable/disable tombol "Masukan ke Pranota" berdasarkan validasi grup dan status pranota
                const btnMasukanPranota = document.getElementById('btnMasukanPranota');
                if (btnMasukanPranota) {
                    if (hasItemsWithGroup && !hasItemsAlreadyInPranota) {
                        btnMasukanPranota.disabled = false;
                        btnMasukanPranota.classList.remove('opacity-50', 'cursor-not-allowed');
                        btnMasukanPranota.title = 'Masukan item terpilih ke pranota';
                    } else {
                        btnMasukanPranota.disabled = true;
                        btnMasukanPranota.classList.add('opacity-50', 'cursor-not-allowed');
                        if (hasItemsAlreadyInPranota) {
                            btnMasukanPranota.title = 'Beberapa item sudah masuk pranota dan tidak dapat dimasukkan kembali';
                        } else {
                            btnMasukanPranota.title = 'Pilih item yang memiliki grup terlebih dahulu';
                        }
                    }
                }
            } else {
                console.log('Hiding bulk actions - adding hidden class');
                bulkActions.classList.add('hidden');
                bulkActions.style.display = 'none'; // Force hide
            }
        } else {
            console.error('bulkActions element not found!');
        }
    }

    // Cancel selection
    btnCancelSelection.addEventListener('click', function() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateBulkActions();
    });

    // Bulk delete handler
    if (btnBulkDelete) {
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

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Bulk status update handler
    if (btnBulkStatus) {
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
    }
});

// Test function
window.testPranota = function() {
    console.log('Test function works!');
};

// Function for "Masukan ke Pranota" - shows modal first
window.masukanKePranota = function() {
    console.log('masukanKePranota called');

    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

    console.log('Selected IDs for pranota:', selectedIds);

    if (selectedIds.length === 0) {
        alert('Pilih minimal satu item untuk dimasukkan ke pranota');
        return;
    }

    // Validasi: Periksa apakah semua item yang dipilih memiliki grup
    let itemsWithoutGroup = [];
    checkedBoxes.forEach((checkbox, index) => {
        const row = checkbox.closest('tr');
        if (row) {
            const groupElement = row.querySelector('td:nth-child(2)'); // Group column (index 2)
            const groupValue = groupElement ? groupElement.textContent.trim() : '';

            console.log(`Validation Item ${index + 1}: groupElement=`, groupElement, `groupValue="${groupValue}"`);

            if (!groupValue || groupValue === '-' || groupValue === '') {
                const containerElement = row.querySelector('td:nth-child(4)');
                const containerName = containerElement ? containerElement.textContent.trim() : `Item ${index + 1}`;
                itemsWithoutGroup.push(containerName);
                console.log(`Item ${index + 1} (${containerName}) added to itemsWithoutGroup`);
            } else {
                console.log(`Item ${index + 1} has valid group: "${groupValue}"`);
            }
        }
    });

    // Jika ada item yang tidak memiliki grup, tampilkan pesan error
    if (itemsWithoutGroup.length > 0) {
        const itemList = itemsWithoutGroup.join(', ');
        alert(`❌ Tidak dapat memasukkan ke pranota!\n\nItem berikut belum memiliki grup:\n${itemList}\n\nSilakan buat grup terlebih dahulu sebelum memasukkan ke pranota.`);
        return;
    }

    // Validasi: Periksa apakah semua item memiliki nomor vendor (invoice vendor)
    let itemsWithoutVendorNumber = [];
    checkedBoxes.forEach((checkbox, index) => {
        const row = checkbox.closest('tr');
        if (row) {
            const invoiceVendorElement = row.querySelector('td:nth-child(14)'); // Invoice Vendor column (index 14, was 14 before)
            const invoiceVendorValue = invoiceVendorElement ? invoiceVendorElement.textContent.trim() : '';

            console.log(`Vendor Invoice Item ${index + 1}: invoiceVendorElement=`, invoiceVendorElement, `invoiceVendorValue="${invoiceVendorValue}"`);

            if (!invoiceVendorValue || invoiceVendorValue === '-' || invoiceVendorValue === '') {
                const containerElement = row.querySelector('td:nth-child(4)');
                const containerName = containerElement ? containerElement.textContent.trim() : `Item ${index + 1}`;
                itemsWithoutVendorNumber.push(containerName);
                console.log(`Item ${index + 1} (${containerName}) added to itemsWithoutVendorNumber`);
            } else {
                console.log(`Item ${index + 1} has vendor number: "${invoiceVendorValue}"`);
            }
        }
    });

    // Jika ada item yang tidak memiliki nomor vendor, tampilkan pesan error
    if (itemsWithoutVendorNumber.length > 0) {
        const itemList = itemsWithoutVendorNumber.join(', ');
        alert(`⚠️ Tidak dapat memasukkan ke pranota!\n\nItem berikut belum memiliki nomor vendor:\n${itemList}\n\nTolong input nomor vendor terlebih dahulu sebelum memasukkan ke pranota.`);
        return;
    }

    // Validasi: Periksa apakah ada item yang sudah masuk pranota
    let itemsAlreadyInPranota = [];
    checkedBoxes.forEach((checkbox, index) => {
        const row = checkbox.closest('tr');
        if (row) {
            const statusPranotaElement = row.querySelector('td:nth-child(20)'); // Status Pranota column (index 20, was 21 before)
            const statusPranotaValue = statusPranotaElement ? statusPranotaElement.textContent.trim() : '';

            console.log(`Pranota Status Item ${index + 1}: statusPranotaElement=`, statusPranotaElement, `statusPranotaValue="${statusPranotaValue}"`);

            // Jika status menunjukkan sudah masuk pranota (bukan "Belum masuk pranota" atau kosong)
            // Case insensitive check untuk "belum masuk pranota"
            const isNotInPranota = !statusPranotaValue ||
                                   statusPranotaValue.toLowerCase().includes('belum masuk pranota') ||
                                   statusPranotaValue === '-';

            if (!isNotInPranota) {
                const containerElement = row.querySelector('td:nth-child(4)');
                const containerName = containerElement ? containerElement.textContent.trim() : `Item ${index + 1}`;
                itemsAlreadyInPranota.push(`${containerName} (${statusPranotaValue})`);
                console.log(`Item ${index + 1} (${containerName}) already in pranota: "${statusPranotaValue}"`);
            } else {
                console.log(`Item ${index + 1} not in pranota: "${statusPranotaValue}"`);
            }
        }
    });

    // Jika ada item yang sudah masuk pranota, tampilkan pesan error
    if (itemsAlreadyInPranota.length > 0) {
        const itemList = itemsAlreadyInPranota.join(', ');
        alert(`❌ Tidak dapat memasukkan ke pranota!\n\nItem berikut sudah masuk pranota:\n${itemList}\n\nItem yang sudah masuk pranota tidak dapat dimasukkan kembali.`);
        return;
    }

    // Collect data from selected rows
    const selectedData = {
        containers: [],
        vendors: [],
        sizes: [],
        periodes: [],
        totals: []
    };

    checkedBoxes.forEach((checkbox, index) => {
        console.log(`Processing checkbox ${index + 1}:`, checkbox.value);

        const row = checkbox.closest('tr');
        if (!row) {
            console.error(`Row not found for checkbox ${index + 1}`);
            return;
        }

        const containerElement = row.querySelector('td:nth-child(4)');
        const vendorElement = row.querySelector('td:nth-child(3) .font-semibold');
        const sizeElement = row.querySelector('td:nth-child(5) .inline-flex');
        const periodeElement = row.querySelector('td:nth-child(6) .inline-flex');
        const totalElement = row.querySelector('td:nth-child(18)'); // Grand Total column (18th column, was 19 before) - Total Biaya

        selectedData.containers.push(containerElement ? containerElement.textContent.trim() : '-');
        selectedData.vendors.push(vendorElement ? vendorElement.textContent.trim() : '-');
        selectedData.sizes.push(sizeElement ? sizeElement.textContent.trim() : '-');
        selectedData.periodes.push(periodeElement ? periodeElement.textContent.trim() : '-');
        selectedData.totals.push(totalElement ? totalElement.textContent.trim() : '-');
    });

    console.log('Bulk data extracted:', selectedData);
    console.log('Opening modal...');

    // Open modal with collected data
    openModal('bulk', selectedIds, selectedData, 'masukan_ke_pranota');
};

window.buatPranotaTerpilih = function() {
    console.log('buatPranotaTerpilih called'); // Debug log

    // Check permission for creating pranota
    @if(!auth()->user()->hasPermissionTo('pranota-tagihan-kontainer.create'))
        // Show warning if user doesn't have permission
        const result = confirm('⚠️ PERINGATAN: Anda tidak memiliki izin untuk membuat pranota.\n\n' +
                              'Untuk dapat menggunakan fitur ini, Anda memerlukan izin "Input" pada modul Pranota Tagihan Kontainer.\n\n' +
                              'Silakan hubungi administrator untuk mendapatkan izin yang diperlukan.\n\n' +
                              'Apakah Anda ingin melanjutkan? (Fitur mungkin tidak akan berfungsi dengan baik)');

        if (!result) {
            return; // User cancelled
        }
    @endif

    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

    console.log('buatPranotaTerpilih called, checked boxes:', checkedBoxes.length); // Debug log
    console.log('Selected IDs:', selectedIds); // Debug log

    if (selectedIds.length === 0) {
        alert('Pilih minimal satu item untuk membuat pranota');
        return;
    }

    // Show loading state first
    const bulkBtn = document.getElementById('bulk-pranota-btn');
    const originalText = bulkBtn.textContent;
    bulkBtn.textContent = 'Memproses...';
    bulkBtn.disabled = true;

    // Collect data from selected rows
    const selectedData = {
        containers: [],
        vendors: [],
        sizes: [],
        periodes: [],
        totals: []
    };

    checkedBoxes.forEach((checkbox, index) => {
        console.log(`Processing checkbox ${index + 1}:`, checkbox.value); // Debug log

        const row = checkbox.closest('tr');
        if (!row) {
            console.error(`Row not found for checkbox ${index + 1}`);
            return;
        }

        const containerElement = row.querySelector('td:nth-child(4)');
        const vendorElement = row.querySelector('td:nth-child(3) .font-semibold');
        const sizeElement = row.querySelector('td:nth-child(5) .inline-flex');
        const periodeElement = row.querySelector('td:nth-child(6) .inline-flex');
        const totalElement = row.querySelector('td:nth-child(18)'); // Grand Total column (18th column, was 19 before) - Total Biaya

        selectedData.containers.push(containerElement ? containerElement.textContent.trim() : '-');
        selectedData.vendors.push(vendorElement ? vendorElement.textContent.trim() : '-');
        selectedData.sizes.push(sizeElement ? sizeElement.textContent.trim() : '-');
        selectedData.periodes.push(periodeElement ? periodeElement.textContent.trim() : '-');
        selectedData.totals.push(totalElement ? totalElement.textContent.trim() : '-');
    });

    console.log('Bulk data extracted:', selectedData); // Debug log

    // Reset button
    setTimeout(() => {
        bulkBtn.textContent = originalText;
        bulkBtn.disabled = false;
    }, 1000);

    openModal('bulk', selectedIds, selectedData);
};

// Function for single pranota creation
window.buatPranota = function(id) {
    console.log('buatPranota called for ID:', id); // Debug log

    // Check permission for creating pranota
    @if(!auth()->user()->hasPermissionTo('pranota-tagihan-kontainer.create'))
        // Show warning if user doesn't have permission
        const result = confirm('⚠️ PERINGATAN: Anda tidak memiliki izin untuk membuat pranota.\n\n' +
                              'Untuk dapat menggunakan fitur ini, Anda memerlukan izin "Input" pada modul Pranota Tagihan Kontainer.\n\n' +
                              'Silakan hubungi administrator untuk mendapatkan izin yang diperlukan.\n\n' +
                              'Apakah Anda ingin melanjutkan? (Fitur mungkin tidak akan berfungsi dengan baik)');

        if (!result) {
            return; // User cancelled
        }
    @endif

    // Find the row for this ID
    const checkbox = document.querySelector(`input[type="checkbox"][value="${id}"]`);
    if (!checkbox) {
        console.error('Checkbox not found for ID:', id);
        alert('Error: Data tidak ditemukan');
        return;
    }

    const row = checkbox.closest('tr');
    if (!row) {
        console.error('Row not found for ID:', id);
        alert('Error: Baris data tidak ditemukan');
        return;
    }

    // Extract data from the row - use more robust selectors
    const cells = row.querySelectorAll('td');

    // Based on table structure: checkbox, no, vendor, container, ukuran, periode, etc
    const vendor = cells[2] ? cells[2].textContent.trim() : '-';
    const container = cells[3] ? cells[3].textContent.trim() : '-';
    const size = cells[4] ? cells[4].textContent.trim() : '-'; // Size column (index 4)
    const periode = cells[5] ? cells[5].textContent.trim() : '-'; // Periode column (index 5)
    const total = cells[12] ? cells[12].textContent.trim() : '-'; // Grand Total column (index 12) - Total Biaya

    console.log('Single pranota data extracted:', { container, vendor, size, periode, total }); // Debug log

    openModal('single', [id], {
        containers: [container],
        vendors: [vendor],
        sizes: [size],
        periodes: [periode],
        totals: [total]
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
                `${data.ungrouped_count} kontainer berhasil dikembalikan ke status individual.`);

            // Reload page after success
            setTimeout(() => {
                window.location.reload();
            }, 1500);
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

window.openModal = function(type, ids, data, action = 'buat_pranota') {
    console.log('openModal called:', { type, ids, data, action }); // Debug log

    const modal = document.getElementById('pranotaModal');
    if (!modal) {
        console.error('Modal element not found!');
        return;
    }

    console.log('Modal element found, updating content...');

    const modalTitle = document.getElementById('modal-title');
    const tagihanInfo = document.getElementById('tagihan-info');
    const jumlahTagihan = document.getElementById('jumlah-tagihan');
    const totalNilai = document.getElementById('total-nilai');
    const selectedTagihanIds = document.getElementById('selected_tagihan_ids');
    const pranotaType = document.getElementById('pranota_type');
    const tanggalPranota = document.getElementById('tanggal_pranota');

    console.log('Modal elements found:', {
        modal: !!modal,
        modalTitle: !!modalTitle,
        tagihanInfo: !!tagihanInfo,
        jumlahTagihan: !!jumlahTagihan,
        totalNilai: !!totalNilai
    }); // Debug log

    // Set form data
    selectedTagihanIds.value = ids.join(',');
    pranotaType.value = type;

    // Set action type (buat_pranota or masukan_ke_pranota)
    const actionInput = document.getElementById('pranota_action');
    if (actionInput) {
        actionInput.value = action;
    }

    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    tanggalPranota.value = today;

    // Calculate container statistics for default keterangan
    const containerStats = {};
    data.sizes.forEach((size, index) => {
        const cleanSize = size.replace(/[^0-9]/g, ''); // Extract number only (20ft -> 20)
        const sizeKey = cleanSize + 'ft';
        containerStats[sizeKey] = (containerStats[sizeKey] || 0) + 1;
    });

    // Generate default keterangan
    let defaultKeterangan = 'Pranota ';
    const statParts = [];
    Object.entries(containerStats).forEach(([size, count]) => {
        statParts.push(`${count} kontainer ${size}`);
    });
    defaultKeterangan += statParts.join(' dan ');

    // Set default keterangan
    const keteranganField = document.getElementById('keterangan');
    if (keteranganField) {
        keteranganField.value = defaultKeterangan;
    }

    // Get nomor pranota elements
    const nomorPranotaDisplay = document.getElementById('nomor_pranota_display');

    // Generate nomor pranota via AJAX
    function updateNomorPranota() {
        // Show loading state
        nomorPranotaDisplay.value = 'Memuat...';

        // Make AJAX call to get next pranota number
        fetch('{{ route("api.next-pranota-number") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            const isJson = contentType && contentType.includes('application/json');

            if (isJson) {
                return response.json();
            } else {
                // Not JSON, probably an error page
                return response.text().then(text => {
                    throw new Error('Server returned non-JSON response: ' + text.substring(0, 100) + '...');
                });
            }
        })
        .then(data => {
            if (data.success) {
                nomorPranotaDisplay.value = data.nomor_pranota;
            } else {
                console.error('Error getting pranota number:', data.message);
                nomorPranotaDisplay.value = 'Error loading number';
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);
            nomorPranotaDisplay.value = 'Error loading number';
        });
    }

    // Update nomor pranota when tanggal changes
    tanggalPranota.addEventListener('change', updateNomorPranota);

    // Initial nomor pranota generation
    updateNomorPranota();

    // Update modal content based on type and action
    if (action === 'masukan_ke_pranota') {
        modalTitle.textContent = `Masukan ke Pranota - ${ids.length} Items`;
        // Update button text
        const submitBtn = document.querySelector('#pranotaForm button[type="submit"] .btn-text');
        if (submitBtn) {
            submitBtn.textContent = 'Masukan ke Pranota';
        }

        // Create detailed table for bulk items (same as bulk pranota)
        let containerRows = '';
        data.containers.forEach((container, index) => {
            containerRows += `
                <tr class="border-b border-gray-200">
                    <td class="px-2 py-1 text-sm">${index + 1}</td>
                    <td class="px-2 py-1 text-sm font-medium">${container}</td>
                    <td class="px-2 py-1 text-sm">${data.vendors[index]}</td>
                    <td class="px-2 py-1 text-sm text-center">${data.sizes[index]}</td>
                    <td class="px-2 py-1 text-sm">${data.periodes[index]}</td>
                    <td class="px-2 py-1 text-sm text-right font-medium">${data.totals[index]}</td>
                </tr>
            `;
        });

        tagihanInfo.innerHTML = `
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-700 mb-2">Detail Tagihan (${ids.length} items):</div>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            ${containerRows}
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        jumlahTagihan.textContent = `${ids.length} tagihan`;

        // Calculate total (remove currency formatting for calculation)
        const totals = data.totals.map(total => {
            // Handle Indonesian number format: Rp 35,450 (comma as thousands separator)
            const cleanTotal = total.replace(/Rp\s*/g, '').replace(/,/g, '');
            return parseFloat(cleanTotal) || 0;
        });
        const grandTotal = totals.reduce((sum, total) => sum + total, 0);
        totalNilai.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;

        console.log('Masukan ke Pranota total calculation:', { rawTotals: data.totals, cleanTotals: totals, grandTotal }); // Debug log
    } else if (type === 'single') {
        modalTitle.textContent = 'Buat Pranota - Single Item';
        tagihanInfo.innerHTML = `
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-700 mb-2">Detail Tagihan:</div>
                <div class="bg-gray-50 rounded-lg p-3 border">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div><strong>Container:</strong></div>
                        <div>${data.containers[0]}</div>
                        <div><strong>Vendor:</strong></div>
                        <div>${data.vendors[0]}</div>
                        <div><strong>Ukuran:</strong></div>
                        <div>${data.sizes[0]}</div>
                        <div><strong>Periode:</strong></div>
                        <div>${data.periodes[0]}</div>
                        <div><strong>Total Biaya:</strong></div>
                        <div class="font-semibold text-green-600">${data.totals[0]}</div>
                    </div>
                </div>
            </div>
        `;

        console.log('tagihanInfo content set for single:', tagihanInfo.innerHTML);
        jumlahTagihan.textContent = '1 tagihan';

        // Format single total consistently
        // Handle Indonesian number format: Rp 35,450 (comma as thousands separator)
        const singleTotal = data.totals[0].replace(/Rp\s*/g, '').replace(/,/g, '');
        const formattedSingleTotal = parseFloat(singleTotal) || 0;
        totalNilai.textContent = `Rp ${formattedSingleTotal.toLocaleString('id-ID')}`;

        console.log('Single total calculation:', { rawTotal: data.totals[0], cleanTotal: singleTotal, formatted: formattedSingleTotal }); // Debug log
    } else {
        modalTitle.textContent = `Buat Pranota - ${ids.length} Items`;

        // Create detailed table for bulk items
        let containerRows = '';
        data.containers.forEach((container, index) => {
            containerRows += `
                <tr class="border-b border-gray-200">
                    <td class="px-2 py-1 text-sm">${index + 1}</td>
                    <td class="px-2 py-1 text-sm font-medium">${container}</td>
                    <td class="px-2 py-1 text-sm">${data.vendors[index]}</td>
                    <td class="px-2 py-1 text-sm text-center">${data.sizes[index]}</td>
                    <td class="px-2 py-1 text-sm">${data.periodes[index]}</td>
                    <td class="px-2 py-1 text-sm text-right font-medium">${data.totals[index]}</td>
                </tr>
            `;
        });

        tagihanInfo.innerHTML = `
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-700 mb-2">Detail Tagihan (${ids.length} items):</div>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Container</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${containerRows}
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        console.log('tagihanInfo content set for bulk:', tagihanInfo.innerHTML);

        jumlahTagihan.textContent = `${ids.length} tagihan`;

        // Calculate total (remove currency formatting for calculation)
        const totals = data.totals.map(total => {
            // Handle Indonesian number format: Rp 35,450 (comma as thousands separator)
            const cleanTotal = total.replace(/Rp\s*/g, '').replace(/,/g, '');
            return parseFloat(cleanTotal) || 0;
        });
        const grandTotal = totals.reduce((sum, total) => sum + total, 0);
        totalNilai.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;

        console.log('Bulk total calculation:', { rawTotals: data.totals, cleanTotals: totals, grandTotal }); // Debug log
    }

    console.log('Modal content updated, showing modal...');

    // Show modal with animation
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    console.log('Modal should now be visible');

    // Trigger animation after a small delay to ensure the modal is rendered
    setTimeout(() => {
        modal.classList.add('modal-show');
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.add('modal-show');
        }
        console.log('Modal animation triggered');
    }, 10);
};

window.closeModal = function() {
    const modal = document.getElementById('pranotaModal');
    const modalContent = modal.querySelector('.modal-content');

    // Add closing animation
    modal.classList.add('modal-hide');
    modal.classList.remove('modal-show');

    if (modalContent) {
        modalContent.classList.add('modal-hide');
        modalContent.classList.remove('modal-show');
    }

    // Hide modal after animation completes
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('modal-hide');
        if (modalContent) {
            modalContent.classList.remove('modal-hide');
        }
        document.body.style.overflow = 'auto';
    }, 300); // Match the CSS transition duration

    // Reset form
    document.getElementById('pranotaForm').reset();

    // Reset tanggal to today after form reset
    const today = new Date().toISOString().split('T')[0];
    const tanggalField = document.getElementById('tanggal_pranota');
    if (tanggalField) {
        tanggalField.value = today;
    }
};

// Add backdrop click to close modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('pranotaModal');

    // Close modal when clicking backdrop
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('pranotaModal');
            if (!modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });
});

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const pranotaForm = document.getElementById('pranotaForm');

    if (pranotaForm) {
        pranotaForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const pranotaType = formData.get('pranota_type');
            const selectedIds = formData.get('selected_tagihan_ids').split(',').filter(id => id);
            const tanggalPranota = formData.get('tanggal_pranota');

            // Validate required fields
            if (!tanggalPranota) {
                alert('Tanggal pranota harus diisi');
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const originalText = btnText.textContent;

            // Set loading text based on action
            const pranotaAction = document.getElementById('pranota_action').value;
            const loadingText = pranotaAction === 'masukan_ke_pranota' ? 'Memproses...' : 'Membuat Pranota...';

            btnText.innerHTML = `<span class="loading-spinner"></span>${loadingText}`;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            // Prepare submission data
            const submitData = new FormData();
            submitData.append('_token', '{{ csrf_token() }}');
            submitData.append('nomor_cetakan', '1'); // Fixed value since input removed
            submitData.append('tanggal_pranota', tanggalPranota);
            submitData.append('keterangan', formData.get('keterangan') || '');

            let actionUrl;

            if (pranotaAction === 'masukan_ke_pranota') {
                // Masukan ke pranota (update status existing items) - menggunakan sistem pranota kontainer sewa
                actionUrl = '{{ route("pranota-kontainer-sewa.bulk-create-from-tagihan-kontainer-sewa") }}';
                submitData.append('action', 'masukan_ke_pranota');
                selectedIds.forEach(id => {
                    submitData.append('selected_ids[]', id);
                });
            } else if (pranotaType === 'bulk') {
                // Bulk pranota submission (create new pranota)
                actionUrl = '{{ route("pranota.bulk.store") }}';
                selectedIds.forEach(id => {
                    submitData.append('selected_ids[]', id);
                });
            } else {
                // Single pranota submission (create new pranota)
                actionUrl = '{{ route("pranota.store") }}';
            }

            // Submit to server
            fetch(actionUrl, {
                method: 'POST',
                body: submitData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');

                if (response.ok) {
                    if (isJson) {
                        return response.json().then(data => {
                            // Success handling with JSON response
                            const pranotaAction = document.getElementById('pranota_action').value;
                            const isMasukanKePranota = pranotaAction === 'masukan_ke_pranota';

                            let successTitle, successMessage;

                            if (isMasukanKePranota) {
                                successTitle = 'Berhasil Masukan ke Pranota';
                                successMessage = `Berhasil memproses ${selectedIds.length} tagihan kontainer sewa ke dalam sistem pranota. Data telah diperbarui.`;
                            } else {
                                successTitle = 'Berhasil Membuat Pranota';
                                successMessage = `Pranota baru telah berhasil dibuat dengan nomor ${data.nomor_pranota || 'tergenerasi otomatis'}.`;
                            }

                            // Show success notification
                            showSuccess(successTitle, successMessage);

                            // Close modal
                            closeModal();

                            // Reset form
                            document.getElementById('pranotaForm').reset();

                            // Reset tanggal to today
                            const today = new Date().toISOString().split('T')[0];
                            const tanggalField = document.getElementById('tanggal_pranota');
                            if (tanggalField) {
                                tanggalField.value = today;
                            }

                            // Keep invoice vendor fields as they are required
                            // Fields will be reset by form.reset() but user needs to fill them again

                            // Reload page after a short delay to show updated data
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);

                            return data;
                        });
                    } else {
                        // Success but not JSON (probably redirect or HTML response)
                        const pranotaAction = document.getElementById('pranota_action').value;
                        const isMasukanKePranota = pranotaAction === 'masukan_ke_pranota';

                        const successTitle = isMasukanKePranota ? 'Berhasil Masukan ke Pranota' : 'Berhasil Membuat Pranota';
                        const successMessage = isMasukanKePranota
                            ? `Berhasil memproses ${selectedIds.length} tagihan kontainer sewa ke dalam sistem pranota.`
                            : 'Pranota baru telah berhasil dibuat.';

                        // Show success notification
                        showSuccess(successTitle, successMessage);

                        // Close modal
                        closeModal();

                        // Reset form
                        document.getElementById('pranotaForm').reset();

                        // Reset tanggal to today
                        const today = new Date().toISOString().split('T')[0];
                        const tanggalField = document.getElementById('tanggal_pranota');
                        if (tanggalField) {
                            tanggalField.value = today;
                        }

                        // Reload page after a short delay to show updated data
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);

                        return { success: true };
                    }
                } else {
                    // Error response
                    if (isJson) {
                        return response.json().then(data => {
                            // Error handling with JSON response
                            const pranotaAction = document.getElementById('pranota_action').value;
                            const isMasukanKePranota = pranotaAction === 'masukan_ke_pranota';

                            let errorTitle, errorMessage;

                            if (isMasukanKePranota) {
                                errorTitle = 'Gagal Masukan ke Pranota';
                                errorMessage = data.message || 'Terjadi kesalahan saat memproses tagihan ke pranota. Silakan coba lagi.';
                            } else {
                                errorTitle = 'Gagal Membuat Pranota';
                                errorMessage = data.message || 'Terjadi kesalahan saat membuat pranota. Silakan periksa data dan coba lagi.';
                            }

                            // Handle validation errors
                            if (data.errors) {
                                const validationErrors = Object.values(data.errors).flat();
                                if (validationErrors.length > 0) {
                                    errorMessage = validationErrors.join('\n');
                                }
                            }

                            // Show error notification
                            showError(errorTitle, errorMessage);

                            // Reset button state
                            btnText.textContent = originalText;
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');

                            throw new Error(errorMessage);
                        });
                    } else {
                        // Error response that's not JSON (HTML error page, etc.)
                        return response.text().then(text => {
                            const pranotaAction = document.getElementById('pranota_action').value;
                            const isMasukanKePranota = pranotaAction === 'masukan_ke_pranota';

                            const errorTitle = isMasukanKePranota ? 'Gagal Masukan ke Pranota' : 'Gagal Membuat Pranota';
                            const errorMessage = `Terjadi kesalahan server (${response.status}). Silakan coba lagi atau hubungi administrator.`;

                            showError(errorTitle, errorMessage);

                            // Reset button state
                            btnText.textContent = originalText;
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');

                            throw new Error(errorMessage);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Network or other errors
                const pranotaAction = document.getElementById('pranota_action').value;
                const isMasukanKePranota = pranotaAction === 'masukan_ke_pranota';

                const errorTitle = isMasukanKePranota ? 'Gagal Masukan ke Pranota' : 'Gagal Membuat Pranota';
                const errorMessage = 'Koneksi bermasalah. Silakan periksa koneksi internet dan coba lagi.';

                showError(errorTitle, errorMessage);

                // Reset button state
                btnText.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        });
    }
});

// Function to open delete group modal
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
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input type="checkbox" id="select-all-groups" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nama Group
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jumlah Kontainer
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dibuat Pada
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi
                                            </th>
                                        </tr>
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

    if (selectAllGroups) {
        selectAllGroups.addEventListener('change', function() {
            groupCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }

    groupCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.group-checkbox:checked');
            selectAllGroups.checked = checkedBoxes.length === groupCheckboxes.length;
            selectAllGroups.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < groupCheckboxes.length;
        });
    });
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
                `Berhasil menghapus ${groupNames.length} group. Kontainer-kontainer telah dikembalikan ke status individual.`);

            // Close modal and reload page
            closeDeleteGroupModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
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

// Function to edit adjustment note
window.editAdjustmentNote = function(tagihanId, currentNote) {
    console.log('editAdjustmentNote called:', { tagihanId, currentNote });

    // Check permission for updating tagihan
    @if(!auth()->user()->hasPermissionTo('tagihan-kontainer-sewa-update'))
        showNotification('error', 'Akses Ditolak', 'Anda tidak memiliki izin untuk mengedit alasan adjustment. Diperlukan izin "Edit" pada modul Tagihan Kontainer.');
        return;
    @endif

    // Create modal HTML for adjustment note editing
    const modalHTML = `
        <div id="adjustmentNoteModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            Edit Alasan Adjustment
                        </h3>
                        <button type="button" onclick="closeAdjustmentNoteModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="adjustmentNoteForm" class="mt-4">
                        <div class="space-y-4">
                            <div>
                                <label for="adjustment_note_value" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alasan Adjustment
                                </label>
                                <textarea id="adjustment_note_value" name="adjustment_note" rows="4"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                         placeholder="Masukkan alasan penyesuaian harga...">${currentNote}</textarea>
                                <div class="text-xs text-gray-500 mt-1">
                                    Jelaskan mengapa ada penyesuaian harga pada item ini
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" onclick="closeAdjustmentNoteModal()"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
    const modal = document.getElementById('adjustmentNoteModal');
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
    const form = document.getElementById('adjustmentNoteForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const adjustmentNote = document.getElementById('adjustment_note_value').value.trim();

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
        formData.append('adjustment_note', adjustmentNote);

        // Send AJAX request
        fetch(`{{ url('daftar-tagihan-kontainer-sewa') }}/${tagihanId}/adjustment-note`, {
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
                showNotification('success', 'Alasan Adjustment Berhasil Disimpan',
                    'Alasan adjustment telah berhasil diperbarui.');

                // Close modal and reload page
                closeAdjustmentNoteModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Gagal menyimpan alasan adjustment');
            }
        })
        .catch(error => {
            console.error('Error saving adjustment note:', error);
            showNotification('error', 'Gagal Menyimpan', error.message || 'Terjadi kesalahan saat menyimpan alasan adjustment');

            // Reset button state
            btnText.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
};

// Function to close adjustment note modal
window.closeAdjustmentNoteModal = function() {
    const modal = document.getElementById('adjustmentNoteModal');
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

// Function to edit adjustment
window.editAdjustment = function(tagihanId, currentAdjustment) {
    console.log('editAdjustment called:', { tagihanId, currentAdjustment });

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
                                          placeholder="Jelaskan alasan adjustment..."></textarea>
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
                    `Adjustment sebesar Rp ${adjustmentValue.toLocaleString('id-ID')} telah disimpan.`);

                // Close modal and reload page
                closeAdjustmentModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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
                    'Informasi vendor telah berhasil diperbarui.');

                // Close modal and reload page after success
                closeVendorInfoModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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
                    'Informasi group telah berhasil diperbarui.');

                // Close modal and reload page after success
                closeGroupInfoModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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
                            `Informasi vendor berhasil disimpan untuk ${successCount} kontainer.`);
                    } else if (successCount > 0 && errorCount > 0) {
                        showNotification('warning', 'Bulk Update Sebagian Berhasil',
                            `${successCount} berhasil, ${errorCount} gagal. Cek detail error di console.`);
                        console.error('Bulk update errors:', errors);
                    } else {
                        showNotification('error', 'Bulk Update Gagal',
                            `Semua ${errorCount} item gagal diupdate. Cek detail error di console.`);
                        console.error('Bulk update errors:', errors);
                    }

                    // Close modal and reload page
                    closeBulkVendorInfoModal();
                    if (successCount > 0) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                }
            });
        });
    });
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
                    `Informasi group telah berhasil diperbarui untuk ${successCount} kontainer.`);

                // Close modal and reload page after success
                closeBulkGroupInfoModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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


</script>

<!-- Modal Buat Pranota -->
<div id="pranotaModal" class="modal-overlay modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="modal-content relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-medium" id="modal-title">
                    Buat Pranota
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="pranotaForm" class="mt-4">
                <!-- Hidden inputs for form data -->
                <input type="hidden" id="pranota_action" name="pranota_action">
                <div class="space-y-4">
                    <!-- Info Tagihan -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">Informasi Tagihan</h4>
                        <div id="tagihan-info" class="text-sm text-blue-800">
                            <!-- Dynamic content will be inserted here -->
                        </div>
                    </div>

                    <!-- Data Pranota -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nomor_pranota_display" class="block text-[10px] font-medium text-gray-700 mb-2">
                                Nomor Pranota (Otomatis)
                            </label>
                            <input type="text" id="nomor_pranota_display" readonly
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-600"
                                   placeholder="PTK-1-25-09-000001">
                        </div>

                        <div>
                            <label for="tanggal_pranota" class="block text-[10px] font-medium text-gray-700 mb-2">
                                Tanggal Pranota <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_pranota" name="tanggal_pranota" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <small class="text-gray-500 text-xs mt-1">Pilih tanggal pembuatan pranota</small>
                        </div>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-[10px] font-medium text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Keterangan tambahan untuk pranota ini..."></textarea>
                    </div>



                    <!-- Summary -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium mb-2">Ringkasan</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Jumlah Tagihan:</span>
                                <span id="jumlah-tagihan" class="font-medium ml-2">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Nilai:</span>
                                <span id="total-nilai" class="font-medium ml-2">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for selected tagihan IDs -->
                <input type="hidden" id="selected_tagihan_ids" name="selected_tagihan_ids">
                <input type="hidden" id="pranota_type" name="pranota_type"> <!-- single or bulk -->

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeModal()"
                            class="btn-animated px-2 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn-animated px-2 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <span class="btn-text">Buat Pranota</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Audit Log Modal -->
@include('components.audit-log-modal')

@endpush
