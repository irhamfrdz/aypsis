@extends('layouts.app')

@section('title', 'Detail Stock Ban')
@section('page_title', 'Detail Stock Ban')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
        <h2 class="text-xl font-bold text-white">Detail Stock Ban</h2>
        <p class="text-indigo-100 text-sm mt-1">Informasi lengkap stock ban</p>
    </div>

    <!-- Content -->
    <div class="p-6">
        <!-- Main Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Kode Ban / Nomor Seri -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nomor Seri / Kode</label>
                <p class="text-base font-medium text-gray-900">{{ $stockBan->nomor_seri ?? '-' }}</p>
                @if($stockBan->namaStockBan)
                    <p class="text-xs text-gray-500 mt-1">{{ $stockBan->namaStockBan->nama }}</p>
                @endif
            </div>

            <!-- Ukuran Ban -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Ukuran Ban</label>
                <p class="text-base font-medium text-gray-900">{{ $stockBan->ukuran ?? '-' }}</p>
            </div>

            <!-- Merek -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Merek</label>
                <p class="text-base font-medium text-gray-900">
                    {{ $stockBan->merk ?? ($stockBan->merkBan->nama ?? '-') }}
                </p>
            </div>

            <!-- Kondisi -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kondisi</label>
                <p class="text-base font-medium text-gray-900">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold 
                        {{ $stockBan->kondisi == 'asli' ? 'bg-green-100 text-green-800' : 
                           ($stockBan->kondisi == 'kanisir' ? 'bg-yellow-100 text-yellow-800' : 
                           ($stockBan->kondisi == 'afkir' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($stockBan->kondisi) }}
                    </span>
                </p>
            </div>

            <!-- Status -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                <p class="text-base font-medium text-gray-900">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold 
                        {{ $stockBan->status == 'Stok' ? 'bg-blue-100 text-blue-800' : 
                           ($stockBan->status == 'Terpakai' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $stockBan->status }}
                    </span>
                </p>
            </div>

            <!-- Lokasi -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Lokasi</label>
                <p class="text-base font-medium text-gray-900">{{ $stockBan->lokasi ?? '-' }}</p>
                @if($stockBan->mobil)
                <p class="text-sm text-blue-600 mt-1 font-medium">
                    <i class="fas fa-truck mr-1"></i> Terpasang di: {{ $stockBan->mobil->nomor_polisi }}
                </p>
                @endif
            </div>

            <!-- Harga Beli -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Harga Beli</label>
                <p class="text-base font-medium text-gray-900">Rp {{ number_format($stockBan->harga_beli, 0, ',', '.') }}</p>
            </div>

            <!-- Supplier / Tempat Beli -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tempat Beli / Supplier</label>
                <p class="text-base font-medium text-gray-900">{{ $stockBan->tempat_beli ?? '-' }}</p>
            </div>

            <!-- Tanggal Masuk -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Masuk</label>
                <p class="text-base font-medium text-gray-900">
                    {{ $stockBan->tanggal_masuk ? \Carbon\Carbon::parse($stockBan->tanggal_masuk)->format('d M Y') : '-' }}
                </p>
            </div>

            <!-- Tanggal Keluar/Pasang -->
            @if($stockBan->tanggal_keluar)
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Pasang</label>
                <p class="text-base font-medium text-gray-900">
                    {{ \Carbon\Carbon::parse($stockBan->tanggal_keluar)->format('d M Y') }}
                </p>
            </div>
            @endif

            <!-- Kapal / Pengiriman -->
            @if($stockBan->status == 'Dikirim Ke Batam' || $stockBan->kapal)
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <label class="block text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1">Informasi Pengiriman Batam</label>
                <p class="text-base font-medium text-blue-900">
                    <i class="fas fa-ship mr-1"></i> {{ $stockBan->kapal->nama_kapal ?? '-' }}
                </p>
                @if($stockBan->tanggal_kirim)
                    <p class="text-xs text-blue-600 mt-1">
                        Dikirim pada: {{ \Carbon\Carbon::parse($stockBan->tanggal_kirim)->format('d M Y') }}
                    </p>
                @endif
                @if($stockBan->penerima)
                    <p class="text-xs text-blue-600 mt-1">
                        Penerima: {{ $stockBan->penerima->nama_lengkap }}
                    </p>
                @endif
            </div>
            @endif
            
            <!-- Masak Info -->
            @if($stockBan->status_masak && $stockBan->jumlah_masak > 0)
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                <label class="block text-xs font-semibold text-orange-700 uppercase tracking-wide mb-1">Status Kanisir</label>
                <p class="text-base font-medium text-orange-900">
                    Sudah dimasak {{ $stockBan->jumlah_masak }} kali
                </p>
            </div>
            @endif
        </div>

        <!-- Keterangan -->
        @if($stockBan->keterangan)
        <div class="mb-6">
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <label class="block text-xs font-semibold text-yellow-800 uppercase tracking-wide mb-2">Keterangan</label>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $stockBan->keterangan }}</p>
            </div>
        </div>
        @endif

        <!-- Audit Information -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informasi Audit
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-600">Dibuat pada:</span>
                    <span class="font-medium text-gray-900 ml-2">
                        {{ $stockBan->created_at ? $stockBan->created_at->format('d M Y H:i') : '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-600">Diubah pada:</span>
                    <span class="font-medium text-gray-900 ml-2">
                        {{ $stockBan->updated_at ? $stockBan->updated_at->format('d M Y H:i') : '-' }}
                    </span>
                </div>
                @if(optional($stockBan->createdBy)->name)
                <div>
                    <span class="text-gray-600">Dibuat oleh:</span>
                    <span class="font-medium text-gray-900 ml-2">{{ $stockBan->createdBy->name }}</span>
                </div>
                @endif
                @if(optional($stockBan->updatedBy)->name)
                <div>
                    <span class="text-gray-600">Diubah oleh:</span>
                    <span class="font-medium text-gray-900 ml-2">{{ $stockBan->updatedBy->name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <a href="{{ route('stock-ban.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>

            <div class="flex space-x-3">
                @can('stock-ban-update')
                    <a href="{{ route('stock-ban.edit', $stockBan->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endcan

                @can('stock-ban-delete')
                    <form action="{{ route('stock-ban.destroy', $stockBan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus stock ban ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif
@endsection
