@extends('layouts.app')

@section('title', 'Pranota Top Up Kartu Bensin Batam')
@section('page_title', 'Pranota Top Up Kartu Bensin Batam')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pranota Top Up Kartu Bensin Batam</h1>
            <p class="mt-1 text-sm text-gray-600">Daftar pranota yang dibuat dari transaksi top up saldo kartu bensin.</p>
        </div>
        <a href="{{ route('master-kartu-bensin-batam.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Lihat Kartu Bensin</a>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="GET" action="{{ route('pranota-topup-kartu-bensin-batam.index') }}" class="mb-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="md:col-span-2">
                <label for="pranota-search" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-600">Cari pranota atau kartu</label>
                <input type="search" id="pranota-search" name="search" value="{{ request('search') }}" placeholder="Nomor pranota, nomor kartu, atau nama kartu" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="pranota-start" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-600">Dari tanggal</label>
                <input type="date" id="pranota-start" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="pranota-end" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-600">Sampai tanggal</label>
                <input type="date" id="pranota-end" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <a href="{{ route('pranota-topup-kartu-bensin-batam.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Cari</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50 px-5 py-3">
            <h2 class="font-semibold text-gray-800">Daftar Pranota</h2>
            <span class="text-xs text-gray-500">{{ $pranotas->total() }} pranota ditemukan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1100px] w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Nomor Pranota</th>
                        <th class="px-5 py-3">Kartu Bensin</th>
                        <th class="px-5 py-3 text-right">Top Up</th>
                        <th class="px-5 py-3 text-right">Biaya Admin</th>
                        <th class="px-5 py-3 text-right">Total Pranota</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pranotas as $pranota)
                    <tr class="hover:bg-blue-50/40">
                        <td class="whitespace-nowrap px-5 py-4 text-gray-700">{{ $pranota->tanggal?->translatedFormat('d M Y') ?: '-' }}</td>
                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-blue-700">{{ $pranota->nomor_pranota }}</td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-800">{{ $pranota->card?->nama_kartu ?: '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $pranota->card?->nomor_kartu ?: '-' }}</div>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right text-gray-700">Rp {{ number_format($pranota->nominal, 2, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right text-gray-700">Rp {{ number_format($pranota->biaya_admin, 2, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right font-bold text-gray-900">Rp {{ number_format((float) $pranota->nominal + (float) $pranota->biaya_admin, 2, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-5 py-4">
                            @if($pranota->card)
                                <div class="flex gap-3">
                                    <a href="{{ route('master-kartu-bensin-batam.pranota', [$pranota->master_kartu_bensin_batam_id, $pranota->id]) }}" class="font-semibold text-blue-700 hover:underline">Lihat / Cetak</a>
                                    <a href="{{ route('master-kartu-bensin-batam.history', $pranota->master_kartu_bensin_batam_id) }}" class="text-gray-600 hover:underline">Riwayat</a>
                                </div>
                            @else
                                <span class="text-gray-400">Kartu tidak tersedia</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-500">Belum ada pranota top up untuk pilihan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pranotas->hasPages())<div class="mt-5">{{ $pranotas->links() }}</div>@endif
</div>
@endsection
