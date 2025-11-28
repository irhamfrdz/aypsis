@extends('layouts.app')

@section('title', 'Detail Pembayaran Bongkaran')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-3">Detail Pembayaran Bongkaran</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500">Nomor Pembayaran</label>
                <div class="text-sm font-medium">{{ $pembayaran->nomor_pembayaran }}</div>
            </div>
            <div>
                <label class="text-xs text-gray-500">Tanggal Pembayaran</label>
                <div class="text-sm">{{ optional($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}</div>
            </div>
            <div>
                <label class="text-xs text-gray-500">Bank</label>
                <div class="text-sm">{{ $pembayaran->bank }}</div>
            </div>
            <div>
                <label class="text-xs text-gray-500">Jenis Transaksi</label>
                <div class="text-sm">{{ $pembayaran->jenis_transaksi }}</div>
            </div>
            <div>
                <label class="text-xs text-gray-500">Total</label>
                <div class="text-sm font-semibold">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</div>
            </div>
            <div>
                <label class="text-xs text-gray-500">Status</label>
                <div class="text-sm">{{ $pembayaran->status_pembayaran }}</div>
            </div>
        </div>

        <div class="mt-4">
            <h3 class="text-sm font-semibold">Pranota Terlampir</h3>
            <ul class="text-sm mt-1 list-disc list-inside">
                @foreach($pembayaran->items as $item)
                    <li>{{ $item->pranotaUangJalanBongkaran?->nomor_pranota ?? 'Pranota #' . $item->pranota_uang_jalan_bongkaran_id }} — Rp {{ number_format($item->subtotal,0,',','.') }}</li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6 text-right">
            <a href="{{ route('pembayaran-pranota-uang-jalan-bongkaran.index') }}" class="inline-flex justify-center py-1 px-3 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Kembali</a>
        </div>
    </div>
</div>
@endsection