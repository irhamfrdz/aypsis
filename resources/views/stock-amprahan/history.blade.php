@extends('layouts.app')

@section('title', 'Riwayat Stock Amprahan')
@section('page_title', 'Riwayat Stock')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                @if(isset($item))
                    Riwayat Activity: {{ $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? 'Barang') }}
                @else
                    Semua Riwayat Stock Amprahan
                @endif
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                @if(isset($item))
                    Daftar lengkap penambahan dan pengambilan untuk item ini.
                @else
                    Daftar lengkap seluruh aktivitas penambahan dan pengambilan barang dari stock amprahan.
                @endif
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2 no-print">
            <a href="{{ route('stock-amprahan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Stock
            </a>
            <a href="{{ route('stock-amprahan.history.print', array_merge(request()->all(), ['id' => isset($item) ? $item->id : null])) }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Laporan
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8 no-print">
        <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="flex-1">
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="flex-1">
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="flex-1">
                <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <select name="lokasi" id="lokasi" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Semua Lokasi</option>
                    <option value="KANTOR AYP JAKARTA" {{ request('lokasi') == 'KANTOR AYP JAKARTA' ? 'selected' : '' }}>Jakarta</option>
                    <option value="KANTOR AYP BATAM" {{ request('lokasi') == 'KANTOR AYP BATAM' ? 'selected' : '' }}>Batam</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ url()->current() }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Stats Cards (If specific item) --}}
    @if(isset($item))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sisa Stock</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Masuk</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($history->where('type', 'Masuk')->sum('jumlah'), 0, ',', '.') }} {{ $item->satuan }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Keluar</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($history->where('type', 'Keluar')->sum('jumlah'), 0, ',', '.') }} {{ $item->satuan }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Aktivitas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $history->count() }} Kali</p>
        </div>
    </div>
    @endif

    {{-- Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">No</th>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-center">Tipe</th>
                        @if(!isset($item))
                        <th class="px-6 py-4 text-left">Nama Barang</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-700">Lokasi</th>
                        @endif
                        <th class="px-6 py-4 text-center">Jumlah</th>
                        <th class="px-6 py-4 text-left">Penerima</th>
                        <th class="px-6 py-4 text-left">Kendaraan</th>
                        <th class="px-6 py-4 text-left">Truck</th>
                        <th class="px-6 py-4 text-left">Buntut</th>
                        <th class="px-6 py-4 text-left">Kapal</th>
                        <th class="px-6 py-4 text-left">Alat Berat</th>
                        <th class="px-6 py-4 text-left">Kantor</th>
                        <th class="px-6 py-4 text-left">KM</th>
                        <th class="px-6 py-4 text-left">Keterangan</th>
                        <th class="px-6 py-4 text-left">Oleh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($history as $usage)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-l-4 {{ $usage->type == 'Masuk' ? 'border-green-500' : 'border-orange-500' }}">
                            {{ date('d M Y', strtotime($usage->tanggal_raw)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($usage->type == 'Masuk')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    MASUK
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">
                                    KELUAR
                                </span>
                            @endif
                        </td>
                        @if(!isset($item))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-bold italic">
                            <div class="text-sm font-semibold text-gray-900">{{ $usage->nama_barang ?? ($usage->stockAmprahan->nama_barang ?? ($usage->stockAmprahan->masterNamaBarangAmprahan->nama_barang ?? '-')) }}</div>
                            <div class="text-xs text-gray-400">ID: #{{ str_pad($usage->stockAmprahan->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-black">
                            @php
                                $lokasi = $usage->lokasi ?? ($usage->stockAmprahan->lokasi ?? '-');
                                $colorClass = 'text-gray-500';
                                if (strpos(strtoupper($lokasi), 'JAKARTA') !== false) $colorClass = 'text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded';
                                if (strpos(strtoupper($lokasi), 'BATAM') !== false) $colorClass = 'text-orange-700 bg-orange-100 px-2 py-0.5 rounded';
                            @endphp
                            <span class="{{ $colorClass }} uppercase tracking-widest">{{ $lokasi == 'KANTOR AYP JAKARTA' ? 'JAKARTA' : ($lokasi == 'KANTOR AYP BATAM' ? 'BATAM' : $lokasi) }}</span>
                        </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold {{ $usage->type == 'Masuk' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $usage->type == 'Masuk' ? '+' : '-' }}{{ number_format($usage->jumlah, 0, ',', '.') }} {{ $item->satuan ?? ($usage->stockAmprahan->satuan ?? '') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($usage->penerima->nama_lengkap != '-')
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-gray-900">{{ $usage->penerima->nama_lengkap ?? '-' }}</div>
                            </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $usage->kendaraan ? ($usage->kendaraan->nomor_polisi . ' - ' . $usage->kendaraan->merek) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $usage->truck ? ($usage->truck->nomor_polisi . ' - ' . $usage->truck->merek) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $usage->buntut ? ($usage->buntut->no_kir ?? $usage->buntut->nomor_polisi) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $usage->kapal->nama_kapal ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $usage->alatBerat ? ($usage->alatBerat->kode_alat . ' - ' . $usage->alatBerat->nama . ($usage->alatBerat->merk ? ' - ' . $usage->alatBerat->merk : '')) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $usage->kantor ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $usage->kilometer ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $usage->keterangan }}">
                            {{ $usage->keterangan }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="text-xs">Dicatat oleh:</div>
                            <div class="font-medium text-gray-700">{{ $usage->createdBy->name ?? '-' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ isset($item) ? 12 : 13 }}" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm">Belum ada riwayat aktivitas barang.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @if(!isset($item) && $history->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 no-print">
        {{ $history->links() }}
    </div>
    @endif
</div>
@endsection
