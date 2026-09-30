@extends('layouts.app')

@section('title', 'Riwayat ' . $label)
@section('page_title', 'Riwayat ' . $label)

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat {{ $label }}</h1>
            <p class="text-sm text-gray-500">{{ $item->namaStockBan?->nama ?? $label }}</p>
        </div>
        <a href="{{ route('stock-ban.index', ['tab' => 'barang-lainnya']) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Kembali ke Stock Ban</a>
    </div>

    <div class="mb-6 grid gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
        <div><p class="text-xs font-medium uppercase text-gray-500">Nama Barang</p><p class="mt-1 font-semibold text-gray-900">{{ $item->namaStockBan?->nama ?? '-' }}</p></div>
        <div><p class="text-xs font-medium uppercase text-gray-500">Ukuran</p><p class="mt-1 font-semibold text-gray-900">{{ $item->ukuran ?: '-' }}</p></div>
        <div><p class="text-xs font-medium uppercase text-gray-500">Stok Saat Ini</p><p class="mt-1 font-semibold text-gray-900">{{ $item->qty }} {{ $item->type ?: 'pcs' }}</p></div>
        <div><p class="text-xs font-medium uppercase text-gray-500">Tanggal Masuk</p><p class="mt-1 font-semibold text-gray-900">{{ $item->tanggal_masuk?->format('d/m/Y') ?? '-' }}</p></div>
        <div><p class="text-xs font-medium uppercase text-gray-500">Lokasi</p><p class="mt-1 font-semibold text-gray-900">{{ $item->lokasi ?: '-' }}</p></div>
        <div><p class="text-xs font-medium uppercase text-gray-500">Nomor Bukti</p><p class="mt-1 font-semibold text-gray-900">{{ $item->nomor_bukti ?: '-' }}</p></div>
        @if($item->keterangan)
            <div class="sm:col-span-2"><p class="text-xs font-medium uppercase text-gray-500">Keterangan Barang</p><p class="mt-1 text-gray-900">{{ $item->keterangan }}</p></div>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-gray-900">Riwayat Keluar</h2></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Tanggal</th><th class="px-5 py-3">Jumlah</th><th class="px-5 py-3">Penerima</th>
                        <th class="px-5 py-3">Tujuan</th><th class="px-5 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($usages as $usage)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4">{{ $usage->tanggal_keluar ? \Carbon\Carbon::parse($usage->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                            <td class="px-5 py-4 font-semibold">{{ $usage->qty }} {{ $item->type ?: 'pcs' }}</td>
                            <td class="px-5 py-4">{{ $usage->penerima?->nama_lengkap ?: ($usage->penerima_manual ?: '-') }}</td>
                            <td class="px-5 py-4">{{ $usage->mobil?->nomor_polisi ?: ($usage->kapal?->nama_kapal ?: ($usage->gudang?->nama_gudang ?: '-')) }}</td>
                            <td class="px-5 py-4">{{ preg_replace('/^\[(Ring Velg|Velg) ID: \d+\]\s*/', '', $usage->keterangan ?? '') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">Belum ada riwayat keluar untuk barang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
