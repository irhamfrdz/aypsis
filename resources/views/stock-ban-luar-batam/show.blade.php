@extends('layouts.app')

@section('title', 'Detail Stock Ban Luar Batam')
@section('page_title', 'Detail Stock Ban Luar Batam')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('stock-ban.index') }}" class="flex items-center text-blue-600 hover:text-blue-800 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke List
            </a>
            <div class="flex gap-2">
                <a href="{{ route('stock-ban-luar-batam.edit', $stockBan->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gray-50 border-b border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $stockBan->nomor_seri ?: 'Tanpa Nomor Seri' }}</h1>
                        <p class="text-gray-500">{{ $stockBan->namaStockBan->nama ?? '-' }}</p>
                    </div>
                    <div class="flex items-center">
                        <span class="px-4 py-1 rounded-full text-sm font-bold 
                            @if($stockBan->status == 'Stok') bg-green-100 text-green-700 
                            @elseif($stockBan->status == 'Terpakai') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ strtoupper($stockBan->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informasi Utama -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Utama</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Nomor Bukti</span>
                                <span class="font-medium text-gray-900">{{ $stockBan->nomor_bukti ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Nomor Faktur</span>
                                <span class="font-medium text-gray-900">{{ $stockBan->nomor_faktur ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Merk</span>
                                <span class="font-medium text-gray-900">{{ $stockBan->merk ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Ukuran</span>
                                <span class="font-medium text-gray-900">{{ $stockBan->ukuran ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Kondisi</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold
                                    @if($stockBan->kondisi == 'asli') bg-emerald-100 text-emerald-700
                                    @elseif($stockBan->kondisi == 'kanisir') bg-yellow-100 text-yellow-700
                                    @elseif($stockBan->kondisi == 'afkir') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ strtoupper($stockBan->kondisi) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Lokasi -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Status & Penggunaan</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Lokasi Saat Ini</span>
                                <span class="font-medium text-blue-600">{{ $stockBan->lokasi ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Terpasang Di Unit</span>
                                <span class="font-medium text-gray-900">
                                    @if($stockBan->mobil)
                                        <i class="fas fa-truck mr-1 text-gray-400"></i> {{ $stockBan->mobil->nomor_polisi }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Penerima</span>
                                <span class="font-medium text-gray-900">{{ $stockBan->penerima->nama_lengkap ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Tanggal Masuk</span>
                                <span class="font-medium text-gray-900">{{ $stockBan->tanggal_masuk ? $stockBan->tanggal_masuk->format('d F Y') : '-' }}</span>
                            </div>
                            @if($stockBan->tanggal_digunakan)
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Tanggal Digunakan</span>
                                <span class="font-medium text-gray-900 text-blue-600">{{ $stockBan->tanggal_digunakan->format('d F Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Keuangan -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Keuangan</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-600">Harga Beli</span>
                                <span class="font-bold text-gray-900">Rp {{ number_format($stockBan->harga_beli, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Log Sistem</h3>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dibuat Oleh</span>
                                <span class="text-gray-700">{{ $stockBan->createdBy->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Waktu Buat</span>
                                <span class="text-gray-700">{{ $stockBan->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Terakhir Update</span>
                                <span class="text-gray-700">{{ $stockBan->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($stockBan->keterangan)
                <div class="mt-8 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                    <h4 class="text-xs font-bold text-yellow-700 uppercase mb-2">Keterangan</h4>
                    <p class="text-gray-700">{{ $stockBan->keterangan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
