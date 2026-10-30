@extends('layouts.app')

@section('title', 'Report Biaya Bensin')
@section('page_title', 'Report Biaya Bensin')

@section('content')
<div class="px-4 py-6 space-y-5">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Report Biaya Bensin</h1>
                <p class="mt-1 text-sm text-gray-500">Rekap transaksi dari Biaya Bensin berdasarkan tanggal pengisian.</p>
            </div>
            <a href="{{ route('biaya-bensin.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Buka Biaya Bensin</a>
        </div>

        <form method="GET" action="{{ route('report.biaya-bensin.index') }}" class="grid grid-cols-1 items-end gap-4 p-5 md:grid-cols-2 xl:grid-cols-5">
            <div>
                <label for="tanggal_dari" class="mb-1 block text-xs font-medium text-gray-700">Tanggal Dari</label>
                <input id="tanggal_dari" name="tanggal_dari" type="date" value="{{ $filters['tanggal_dari'] }}" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="tanggal_sampai" class="mb-1 block text-xs font-medium text-gray-700">Tanggal Sampai</label>
                <input id="tanggal_sampai" name="tanggal_sampai" type="date" value="{{ $filters['tanggal_sampai'] }}" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="status" class="mb-1 block text-xs font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Semua status</option>
                    <option value="pending" @selected($filters['status'] === 'pending')>Menunggu Approval</option>
                    <option value="approved" @selected($filters['status'] === 'approved')>Disetujui</option>
                    <option value="rejected" @selected($filters['status'] === 'rejected')>Ditolak</option>
                </select>
            </div>
            <div>
                <label for="search" class="mb-1 block text-xs font-medium text-gray-700">Pencarian</label>
                <input id="search" name="search" type="search" value="{{ $filters['search'] }}" placeholder="Kendaraan, supir, kartu..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Tampilkan</button>
                <a href="{{ route('report.biaya-bensin.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-4"><div class="text-xs text-gray-500">Jumlah Transaksi</div><div class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($summary->jumlah_transaksi, 0, ',', '.') }}</div></div>
        <div class="rounded-lg border border-gray-200 bg-white p-4"><div class="text-xs text-gray-500">Total Liter</div><div class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($summary->total_liter, 2, ',', '.') }} L</div></div>
        <div class="rounded-lg border border-gray-200 bg-white p-4"><div class="text-xs text-gray-500">Total Biaya</div><div class="mt-1 text-xl font-semibold text-gray-900">Rp {{ number_format($summary->total_biaya, 0, ',', '.') }}</div></div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Kendaraan / Alat Berat</th>
                    <th class="px-4 py-3 text-left">No. Kartu</th>
                    <th class="px-4 py-3 text-left">Supir</th>
                    <th class="px-4 py-3 text-right">KM Awal / Akhir</th>
                    <th class="px-4 py-3 text-right">Liter</th>
                    <th class="px-4 py-3 text-right">Harga / Liter</th>
                    <th class="px-4 py-3 text-right">Biaya</th>
                    <th class="px-4 py-3 text-left">Status / Keterangan</th>
                    <th class="px-4 py-3 text-left">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                    <tr class="align-top hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3">{{ $item->tanggal?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($item->mobil)
                                <div class="font-medium">{{ $item->mobil->nomor_polisi ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->mobil->kode_no ?: '-' }}</div>
                            @elseif($item->alatBerat)
                                <div class="font-medium">{{ $item->alatBerat->nama ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->alatBerat->kode_alat ?: '-' }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $item->nomor_kartu ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $item->supir?->nama_panggilan ?: ($item->supir?->nama_lengkap ?: '-') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            {{ $item->km_awal !== null ? number_format($item->km_awal, 0, ',', '.') : '-' }} /
                            {{ $item->km_akhir !== null ? number_format($item->km_akhir, 0, ',', '.') : '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">{{ number_format($item->liter, 2, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format($item->harga_per_liter, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format($item->biaya, 0, ',', '.') }}</td>
                        <td class="min-w-40 px-4 py-3">
                            @if($item->status === 'approved')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Disetujui</span>
                            @elseif($item->status === 'rejected')
                                <span class="rounded bg-red-100 px-2 py-1 text-xs font-medium text-red-800">Ditolak</span>
                            @else
                                <span class="rounded bg-amber-100 px-2 py-1 text-xs font-medium text-amber-800">Menunggu Approval</span>
                            @endif
                            @if($item->pranotaBiayaBensin)<div class="mt-2 text-xs text-blue-700">{{ $item->pranotaBiayaBensin->nomor_pranota }}</div>@endif
                            @if($item->keterangan)<div class="mt-2 text-xs text-gray-500">{{ $item->keterangan }}</div>@endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3"><a href="{{ route('biaya-bensin.print', $item->id) }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">Lihat</a></td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="px-4 py-10 text-center text-gray-500">Tidak ada biaya bensin untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
</div>
@endsection
