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

                <!-- Template Download -->
                <a href="{{ route('daftar-tagihan-kontainer-sewa.template.csv') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Template CSV
                </a>

                <!-- Import CSV Standard -->
                <form action="{{ route('daftar-tagihan-kontainer-sewa.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <div class="flex items-center gap-1">
                        <label class="inline-flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 cursor-pointer hover:bg-gray-50 transition-colors duration-150">
                            <input type="file" name="file" accept=".csv,text/csv" class="hidden file-input" id="csvFileInput" />
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Upload CSV</span>
                            <span id="uploadFilename" class="text-sm text-gray-500">(belum ada file)</span>
                        </label>
                        <div class="text-gray-400 text-xs" title="Import CSV standar tanpa auto-grouping">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import
                    </button>
                </form>

                <!-- Import CSV with Grouping -->
                <form action="{{ route('daftar-tagihan-kontainer-sewa.import.grouped') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <div class="flex items-center gap-1">
                        <label class="inline-flex items-center gap-2 bg-white border border-orange-300 rounded-lg px-3 py-2 cursor-pointer hover:bg-orange-50 transition-colors duration-150">
                            <input type="file" name="csv_file" accept=".csv,text/csv" class="hidden file-input" id="csvFileGroupInput" />
                            <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                            </svg>
                            <span class="text-sm text-orange-700">Upload CSV dengan Grouping</span>
                            <span id="uploadGroupFilename" class="text-sm text-orange-500">(belum ada file)</span>
                        </label>
                        <div class="text-orange-400 text-xs" title="Import CSV dengan auto-grouping berdasarkan kontainer yang sama">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Import & Group
                    </button>
                </form>

                <!-- Buat Group -->
                @can('tagihan-kontainer-create')
                <a href="{{ route('daftar-tagihan-kontainer-sewa.create-group') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Buat Group
                </a>
                @endcan
            </div>
        </div>

        <!-- Import Information -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center gap-2 font-medium text-gray-700">
                        <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Import CSV Standard
                    </div>
                    <ul class="mt-2 text-gray-600 text-xs space-y-1">
                        <li>• Import data sesuai template CSV</li>
                        <li>• Tidak ada auto-grouping</li>
                        <li>• Manual group assignment</li>
                    </ul>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <div class="flex items-center gap-2 font-medium text-orange-700">
                        <svg class="h-4 w-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Import CSV dengan Grouping
                    </div>
                    <ul class="mt-2 text-orange-600 text-xs space-y-1">
                        <li>• Import data dengan auto-grouping</li>
                        <li>• Group berdasarkan kontainer sama</li>
                        <li>• Generate group ID otomatis</li>
                    </ul>
                </div>
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
                    @can('pranota-create')
                    <button type="button" id="btnMasukanPranota" onclick="masukanKePranota()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Masukan ke Pranota
                    </button>
                    @endcan
                    @can('tagihan-kontainer-delete')
                    <button type="button" onclick="ungroupSelectedContainers()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                        Hapus Group
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
                        placeholder="Cari nomor kontainer (akan menampilkan semua kontainer dalam grup yang sama), vendor, atau group..."
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
                    $foundContainer = \App\Models\DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')->first();
                    $isGroupSearch = $foundContainer && $foundContainer->group;
                @endphp

                @if($isGroupSearch)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <div class="text-sm">
                            <span class="font-medium text-blue-800">Mode Pencarian Grup:</span>
                            <span class="text-blue-700">Menampilkan semua kontainer dalam grup "{{ $foundContainer->group }}" yang terkait dengan "{{ $searchTerm }}"</span>
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

                <!-- Status Filter -->
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Semua Status</option>
                    @foreach(($statusOptions ?? ['ongoing' => 'Container Ongoing', 'selesai' => 'Container Selesai']) as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <!-- Filter by Vendor -->
                <select name="vendor" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Semua Vendor</option>
                    @foreach(($vendors ?? ['ZONA', 'DPE']) as $vendor)
                        <option value="{{ $vendor }}" {{ request('vendor') == $vendor ? 'selected' : '' }}>
                            {{ $vendor }}
                        </option>
                    @endforeach
                </select>

                <!-- Filter by Size -->
                <select name="size" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Semua Size</option>
                    @foreach(($sizes ?? ['20', '40']) as $size)
                        <option value="{{ $size }}" {{ request('size') == $size ? 'selected' : '' }}>
                            {{ $size }}'
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
                    @foreach(($periodes ?? []) as $periode)
                        <option value="{{ $periode }}" {{ request('periode') == $periode ? 'selected' : '' }}>
                            Periode {{ $periode }}
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
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Status: {{ request('status') == 'ongoing' ? 'Container Ongoing' : 'Container Selesai' }}
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

    {{-- Flash messages for import/download actions --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    <div class="max-w-full mx-auto px-4">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
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
                    <th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider " style="min-width: 140px;">
                        <div class="flex items-center justify-end space-x-1">
                            <span>DPP Nilai Lain</span>
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
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 font-mono ">
                            {{ optional($tagihan)->group ?? '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 font-medium ">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                <span class="font-semibold">{{ optional($tagihan)->vendor ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 font-mono text-gray-900 ">
                            {{ optional($tagihan)->nomor_kontainer ?? '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center text-gray-900 ">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ optional($tagihan)->size == '20' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ optional($tagihan)->size ?? '-' }}'
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center text-gray-900 ">
                            @php
                                // Implementasi logika periode sesuai CSV
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
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-center text-gray-900 ">
                            @php
                                $tarif = optional($tagihan)->tarif ?? '-';
                                $isHarian = strtolower($tarif) === 'harian';
                                $isBulanan = strtolower($tarif) === 'bulanan';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $isHarian ? 'bg-green-100 text-green-800' : ($isBulanan ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $tarif }}
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-right font-mono text-gray-900 ">
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
                        <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 text-right font-mono text-gray-900 ">
                            <div class="font-semibold text-blue-900">
                                Rp {{ number_format((float)(optional($tagihan)->dpp_nilai_lain ?? 0), 0, '.', ',') }}
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
                                    $pranota = \App\Models\Pranota::find($tagihan->pranota_id);
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
                                <p class="text-sm text-gray-500 mt-1">Mulai dengan menambahkan tagihan baru atau import data dari CSV</p>
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
                    {{ $tagihans->appends(request()->query())->links() }}
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
document.addEventListener('DOMContentLoaded', function(){
    // Handle regular CSV file input
    const input = document.getElementById('csvFileInput');
    const label = document.getElementById('uploadFilename');
    if (input && label) {
        input.addEventListener('change', function(e){
            const f = input.files && input.files[0];
            if (f) label.textContent = f.name;
            else label.textContent = '(belum ada file)';
        });
    }

    // Handle grouped CSV file input
    const groupInput = document.getElementById('csvFileGroupInput');
    const groupLabel = document.getElementById('uploadGroupFilename');
    if (groupInput && groupLabel) {
        groupInput.addEventListener('change', function(e){
            const f = groupInput.files && groupInput.files[0];
            if (f) groupLabel.textContent = f.name;
            else groupLabel.textContent = '(belum ada file)';
        });
    }
});

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

    console.log('Starting data collection...');

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
        const totalElement = row.querySelector('td:nth-child(16)'); // Grand Total column (16th column) - Total Biaya

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
        const totalElement = row.querySelector('td:nth-child(16)'); // Grand Total column (16th column) - Total Biaya

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
            const groupCell = row.querySelector('td:nth-child(7)'); // Group column (index 7)
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

            // Validate tanggal pranota
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
@endpush
