@extends('layouts.app')

@section('title', 'Report Biaya Perijinan')
@section('page_title', 'Report Biaya Perijinan')

@section('content')
<div class="px-4 py-6 space-y-5">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">Report Biaya Perijinan</h1>
            <p class="text-sm text-gray-500 mt-1">Pembayaran Kendaraan dengan subjenis KIR</p>
        </div>

        <form method="GET" action="{{ route('report.biaya-perijinan.index') }}" class="p-5 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="tanggal_dari" class="block text-xs font-medium text-gray-700 mb-1">Tanggal Dari</label>
                <input id="tanggal_dari" name="tanggal_dari" type="date" value="{{ request('tanggal_dari') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label for="tanggal_sampai" class="block text-xs font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                <input id="tanggal_sampai" name="tanggal_sampai" type="date" value="{{ request('tanggal_sampai') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Nomor, kendaraan, penerima..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md">Filter</button>
                <a href="{{ route('report.biaya-perijinan.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-4"><div class="text-xs text-gray-500">Transaksi</div><div class="text-lg font-semibold">{{ number_format($summary->total_transaksi, 0, ',', '.') }}</div></div>
        <div class="bg-white border border-gray-200 rounded-lg p-4"><div class="text-xs text-gray-500">Total Biaya KIR</div><div class="text-lg font-semibold">Rp {{ number_format($summary->total_biaya, 0, ',', '.') }}</div></div>
        <div class="bg-white border border-gray-200 rounded-lg p-4"><div class="text-xs text-gray-500">Total PPh</div><div class="text-lg font-semibold">Rp {{ number_format($summary->total_pph, 0, ',', '.') }}</div></div>
        <div class="bg-white border border-gray-200 rounded-lg p-4"><div class="text-xs text-gray-500">Total Dibayar</div><div class="text-lg font-semibold">Rp {{ number_format($summary->total_dibayar, 0, ',', '.') }}</div></div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal / Nomor</th>
                    <th class="px-4 py-3 text-left">Nomor Accurate</th>
                    <th class="px-4 py-3 text-left">Kendaraan / Biaya</th>
                    <th class="px-4 py-3 text-left">Penerima / Keterangan</th>
                    <th class="px-4 py-3 text-right">Biaya</th>
                    <th class="px-4 py-3 text-right">PPh</th>
                    <th class="px-4 py-3 text-right">Dibayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pembayarans as $pembayaran)
                    <tr class="align-top hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div>{{ $pembayaran->tanggal?->format('d/m/Y') ?? '-' }}</div>
                            <a href="{{ route('pembayaran-aktivitas-lain.show', $pembayaran) }}" class="font-medium text-blue-600 hover:underline">{{ $pembayaran->nomor }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $pembayaran->nomor_accurate ?: '-' }}</td>
                        <td class="px-4 py-3">
                            @forelse($pembayaran->kendaraan_details ?? [] as $kendaraan)
                                <div class="whitespace-nowrap">{{ $kendaraan['nomor_polisi'] ?? '-' }} <span class="text-gray-500">· Rp {{ number_format((int) ($kendaraan['nominal'] ?? 0), 0, ',', '.') }}</span></div>
                            @empty
                                {{ implode(', ', $pembayaran->nomor_polisi_list ?: array_filter([$pembayaran->nomor_polisi])) ?: '-' }}
                            @endforelse
                        </td>
                        <td class="px-4 py-3 min-w-40">
                            <div>{{ $pembayaran->penerima ?: '-' }}</div>
                            @if($pembayaran->keterangan)<div class="text-xs text-gray-500 mt-1">{{ $pembayaran->keterangan }}</div>@endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">Rp {{ number_format($pembayaran->pph_nominal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap font-semibold">Rp {{ number_format($pembayaran->grand_total ?? ($pembayaran->jumlah - $pembayaran->pph_nominal), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">Tidak ada pembayaran KIR untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $pembayarans->links() }}
</div>
@endsection
