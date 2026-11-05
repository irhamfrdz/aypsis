@extends('layouts.app')

@section('title', 'Rekap Pemakaian Ban')
@section('page_title', 'Rekap Pemakaian Ban')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Rekap Pemakaian Ban</h1>
        <p class="mt-1 text-sm text-gray-500">Daftar ban yang tercatat digunakan dari Stock Ban.</p>
    </div>

    <form method="GET" action="{{ route('rekap-pemakaian-ban.index') }}" class="mb-6 grid gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label for="start_date" class="mb-1 block text-sm font-medium text-gray-700">Dari Tanggal</label>
            <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="end_date" class="mb-1 block text-sm font-medium text-gray-700">Sampai Tanggal</label>
            <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="search" class="mb-1 block text-sm font-medium text-gray-700">Cari Ban atau Unit</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Tipe, merek, nomor seri, unit..." class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Tampilkan</button>
            <a href="{{ route('rekap-pemakaian-ban.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
        </div>
        @error('start_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        @error('end_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        @error('search') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4 text-sm font-semibold text-gray-800">Total pemakaian: {{ number_format($total) }} ban</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Bukti Pakai</th>
                        <th class="px-4 py-3">Tipe Ban</th>
                        <th class="px-4 py-3">Merek / Ukuran</th>
                        <th class="px-4 py-3">Nomor Seri</th>
                        <th class="px-4 py-3">Unit</th>
                        <th class="px-4 py-3">Penerima</th>
                        <th class="px-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($items as $ban)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3">{{ ($ban->tanggal_digunakan ?? $ban->tanggal_keluar ?? $ban->updated_at)?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $ban->nomor_bukti_pakai ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $ban->namaStockBan->nama ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $ban->merk ?: '-' }} / {{ $ban->ukuran ?: '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $ban->nomor_seri ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $ban->mobil?->nomor_polisi ?: ($ban->mobil?->no_kir ?: ($ban->alatBerat?->nama ?: ($ban->kapal?->nama_kapal ?: '-'))) }}</td>
                            <td class="px-4 py-3">{{ $ban->penerima?->nama_lengkap ?: ($ban->penerima_manual ?: '-') }}</td>
                            <td class="px-4 py-3">{{ $ban->keterangan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-gray-500">Belum ada pemakaian ban untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $items->links() }}</div>
    </div>
</div>
@endsection
