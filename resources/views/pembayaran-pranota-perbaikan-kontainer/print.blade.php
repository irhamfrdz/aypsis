@extends('layouts.app')

@section('title', 'Print Pembayaran Pranota Perbaikan Kontainer')
@section('page_title', 'Print Pembayaran Pranota Perbaikan Kontainer')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">BUKTI PEMBAYARAN</h1>
            <h2 class="text-lg font-semibold text-gray-600">PRANOTA PERBAIKAN KONTAINER</h2>
            <p class="text-sm text-gray-500 mt-1">{{ now()->format('d F Y H:i:s') }}</p>
        </div>

        {{-- Informasi Pembayaran --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3">Informasi Pembayaran</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nomor Pembayaran:</span>
                        <span class="font-medium">{{ $pembayaran->nomor_pembayaran ?? $pembayaran->nomor_invoice }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Pembayaran:</span>
                        <span class="font-medium">{{ $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nominal:</span>
                        <span class="font-medium">Rp {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode:</span>
                        <span class="font-medium">{{ ucfirst($pembayaran->metode_pembayaran ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bank:</span>
                        <span class="font-medium">{{ $pembayaran->bank ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium {{ $pembayaran->status_pembayaran == 'completed' ? 'text-green-600' : ($pembayaran->status_pembayaran == 'pending' ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ ucfirst($pembayaran->status_pembayaran ?? '-') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3">Informasi Pranota</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nomor Pranota:</span>
                        <span class="font-medium">{{ $pembayaran->pranotaPerbaikanKontainer->nomor_pranota ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kontainer:</span>
                        <span class="font-medium">{{ $pembayaran->pranotaPerbaikanKontainer->perbaikanKontainer->kontainer->nomor_kontainer ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Pranota:</span>
                        <span class="font-medium">{{ $pembayaran->pranotaPerbaikanKontainer->tanggal_pranota ? \Carbon\Carbon::parse($pembayaran->pranotaPerbaikanKontainer->tanggal_pranota)->format('d F Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Teknisi:</span>
                        <span class="font-medium">{{ $pembayaran->pranotaPerbaikanKontainer->nama_teknisi ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Biaya:</span>
                        <span class="font-medium">Rp {{ number_format($pembayaran->pranotaPerbaikanKontainer->total_biaya ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Pekerjaan --}}
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 mb-3">Detail Pekerjaan Perbaikan</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-700">{{ $pembayaran->pranotaPerbaikanKontainer->deskripsi_pekerjaan ?? 'Tidak ada deskripsi' }}</p>
            </div>
        </div>

        {{-- Keterangan --}}
        @if($pembayaran->keterangan)
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 mb-3">Keterangan</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-700">{{ $pembayaran->keterangan }}</p>
            </div>
        </div>
        @endif

        {{-- Tanda Tangan --}}
        <div class="grid grid-cols-2 gap-8 mt-8">
            <div class="text-center">
                <div class="border-b border-gray-400 w-full mb-2"></div>
                <p class="text-sm text-gray-600">Pembayar</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->name ?? 'Admin' }}</p>
            </div>
            <div class="text-center">
                <div class="border-b border-gray-400 w-full mb-2"></div>
                <p class="text-sm text-gray-600">Penerima</p>
                <p class="text-xs text-gray-500">{{ $pembayaran->pranotaPerbaikanKontainer->nama_teknisi ?? 'Teknisi' }}</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-8 text-xs text-gray-500 border-t border-gray-200 pt-4">
            <p>Dicetak pada {{ now()->format('d F Y H:i:s') }} oleh {{ Auth::user()->name ?? 'System' }}</p>
            <p>Sistem Manajemen AYP SIS - Pembayaran Pranota Perbaikan Kontainer</p>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex justify-center gap-4 mt-6">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
            <i class="fas fa-print mr-2"></i>Print
        </button>
        <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.show', $pembayaran) }}" class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
@endsection

@section('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        font-size: 12px;
    }
    .bg-white {
        background: white !important;
    }
    .shadow-lg {
        box-shadow: none !important;
    }
}
</style>
@endsection
