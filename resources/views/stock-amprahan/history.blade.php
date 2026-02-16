@extends('layouts.app')

@section('title', 'Riwayat Pemakaian Stock Amprahan')
@section('page_title', 'Riwayat Pemakaian')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                @if(isset($item))
                    Riwayat Pemakaian: {{ $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? 'Barang') }}
                @else
                    Semua Riwayat Pemakaian Stock Amprahan
                @endif
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                @if(isset($item))
                    Daftar lengkap pengambilan untuk item ini.
                @else
                    Daftar lengkap seluruh aktivitas pengambilan barang dari stock amprahan.
                @endif
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('stock-amprahan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Stock
            </a>
            @if(isset($item))
            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Laporan
            </button>
            @endif
        </div>
    </div>

    {{-- Stats Cards (If specific item) --}}
    @if(isset($item))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sisa Stock</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pemakaian</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($usages->sum('jumlah'), 0, ',', '.') }} {{ $item->satuan }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Frekuensi Ambil</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $usages->count() }} Kali</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi Simpan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $item->lokasi ?? '-' }}</p>
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
                        @if(!isset($item))
                        <th class="px-6 py-4 text-left">Nama Barang</th>
                        @endif
                        <th class="px-6 py-4 text-center">Jumlah</th>
                        <th class="px-6 py-4 text-left">Penerima</th>
                        <th class="px-6 py-4 text-left">Mobil</th>
                        <th class="px-6 py-4 text-left">Keterangan</th>
                        <th class="px-6 py-4 text-left">Oleh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($usages as $usage)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-l-4 border-transparent hover:border-indigo-500">
                            {{ date('d M Y', strtotime($usage->tanggal_pengambilan)) }}
                        </td>
                        @if(!isset($item))
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $usage->stockAmprahan->nama_barang ?? ($usage->stockAmprahan->masterNamaBarangAmprahan->nama_barang ?? '-') }}</div>
                            <div class="text-xs text-gray-400">ID: #{{ str_pad($usage->stockAmprahan->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-amber-100 text-amber-800">
                                {{ number_format($usage->jumlah, 0, ',', '.') }} {{ $item->satuan ?? ($usage->stockAmprahan->satuan ?? '') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-gray-900">{{ $usage->penerima->nama_lengkap ?? '-' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $usage->mobil ? ($usage->mobil->nomor_polisi . ' - ' . $usage->mobil->merek) : '-' }}
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
                        <td colspan="{{ isset($item) ? 7 : 8 }}" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm">Belum ada riwayat pengambilan barang.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination for All History --}}
        @if(!isset($item) && $usages->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $usages->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .container { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
        .shadow-sm { shadow: none !important; }
        .border { border-color: #eee !important; }
    }
</style>
@endsection
