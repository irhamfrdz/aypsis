@extends('layouts.app')

@section('title', 'Report Stock Akhir')
@section('page_title', 'Report Stock Akhir')

@push('styles')
<style>
    .tab-btn {
        padding: 0.75rem 1rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        color: #6b7280;
        transition: all 0.2s;
    }
    .tab-btn:hover {
        color: #1f2937;
        border-color: #d1d5db;
    }
    .tab-btn.active {
        color: #3b82f6;
        border-color: #3b82f6;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 overflow-y-auto h-full pb-24">
    <!-- Header Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Total Ban Jakarta -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Tire Stock (Jakarta)</span>
                <i class="fas fa-circle-notch text-blue-400 text-lg"></i>
            </div>
            <div class="text-3xl font-black text-blue-900">{{ $totalBanStok }}</div>
            <p class="text-xs text-blue-600 mt-1">Unit Available</p>
        </div>

        <!-- Card 2: Total Ban Batam -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Tire Stock (Batam)</span>
                <i class="fas fa-ship text-orange-400 text-lg"></i>
            </div>
            <div class="text-3xl font-black text-orange-900">{{ $totalBanBatamStok }}</div>
            <p class="text-xs text-orange-600 mt-1">Unit Available</p>
        </div>

        <!-- Card 3: Total Amprahan -->
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Amprahan Stock</span>
                <i class="fas fa-boxes text-emerald-400 text-lg"></i>
            </div>
            <div class="text-3xl font-black text-emerald-900">{{ number_format($totalAmprahanStok, 0, ',', '.') }}</div>
            <p class="text-xs text-emerald-600 mt-1">Total Items</p>
        </div>

        <!-- Card 4: Total Valuasi -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Total Valuation</span>
                <i class="fas fa-coins text-purple-400 text-lg"></i>
            </div>
            <div class="text-2xl font-black text-purple-900">Rp {{ number_format($valuasiBan + $valuasiAmprahan, 0, ',', '.') }}</div>
            <p class="text-xs text-purple-600 mt-1">Tires + Supplies</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('report.stock-akhir.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="search" class="block text-xs font-semibold text-gray-500 mb-1">Cari Keyword</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </span>
                    <input type="text" name="search" id="search" value="{{ $search }}"
                           class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none" 
                           placeholder="Cari nomor seri, merk, nama barang, dll...">
                </div>
            </div>

            <div class="w-full md:w-64">
                <label for="lokasi" class="block text-xs font-semibold text-gray-500 mb-1">Filter Lokasi</label>
                <select name="lokasi" id="lokasi" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none bg-white">
                    <option value="">Semua Lokasi</option>
                    <option value="Garasi Pluit" {{ $lokasi === 'Garasi Pluit' ? 'selected' : '' }}>Garasi Pluit</option>
                    <option value="Ruko 10" {{ $lokasi === 'Ruko 10' ? 'selected' : '' }}>Ruko 10</option>
                    <option value="Batam" {{ $lokasi === 'Batam' ? 'selected' : '' }}>Batam</option>
                    <option value="Jakarta" {{ $lokasi === 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm shadow-sm transition">
                    Filter
                </button>
                <a href="{{ route('report.stock-akhir.index') }}" class="flex-1 md:flex-none px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white text-center rounded-lg font-semibold text-sm shadow-sm transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex space-x-1 border-b border-gray-200 bg-gray-50 px-4 pt-2">
            <button class="tab-btn active" data-target="tab-ban-jkt">Stock Ban Jakarta ({{ $stockBans->count() }})</button>
            <button class="tab-btn" data-target="tab-ban-btm">Stock Ban Batam ({{ $stockBanBatams->count() }})</button>
            <button class="tab-btn" data-target="tab-amprahan">Stock Amprahan ({{ $stockAmprahans->count() }})</button>
        </div>

        <!-- Tab 1: Ban Jakarta -->
        <div id="tab-ban-jkt" class="tab-content active p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No Seri / Kode</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Merk & Ukuran</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kondisi</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi / Posisi</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stockBans as $index => $ban)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $ban->nomor_seri ?? '-' }}
                                @if($ban->namaStockBan)
                                    <div class="text-xs text-gray-400 font-normal">{{ $ban->namaStockBan->nama }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="font-medium">{{ $ban->merk ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $ban->ukuran ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($ban->kondisi) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ban->lokasi ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                Rp {{ number_format($ban->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $ban->tanggal_masuk ? $ban->tanggal_masuk->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada stock ban Jakarta.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Ban Batam -->
        <div id="tab-ban-btm" class="tab-content p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No Seri / Kode</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Merk & Ukuran</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kondisi</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi / Posisi</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stockBanBatams as $index => $ban)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $ban->nomor_seri ?? '-' }}
                                @if($ban->namaStockBan)
                                    <div class="text-xs text-gray-400 font-normal">{{ $ban->namaStockBan->nama }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="font-medium">{{ $ban->merk ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $ban->ukuran ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                    {{ ucfirst($ban->kondisi) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ban->lokasi ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                Rp {{ number_format($ban->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $ban->tanggal_masuk ? $ban->tanggal_masuk->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada stock ban Batam.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3: Stock Amprahan -->
        <div id="tab-amprahan" class="tab-content p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No Bukti</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Qty Sisa</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Harga Satuan</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Total Nilai</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stockAmprahans as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $item->nomor_bukti ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item->type_amprahan ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span class="px-2 py-1 rounded bg-green-50 text-green-700 font-bold border border-green-200">
                                    {{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-gray-700">
                                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-indigo-700">
                                @php
                                    $totalValue = ($item->harga_satuan * $item->jumlah) + $item->adjustment;
                                @endphp
                                Rp {{ number_format($totalValue, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item->lokasi ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada stock amprahan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                // Remove active classes
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                // Add active class to clicked tab & target content
                this.classList.add('active');
                const target = this.getAttribute('data-target');
                document.getElementById(target).classList.add('active');
            });
        });
    });
</script>
@endsection
