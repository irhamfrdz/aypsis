@extends('layouts.app')

@section('title', 'Rekap Pemakaian Ban')
@section('page_title', 'Rekap Pemakaian Ban')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6 flex items-start gap-4">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-lg text-indigo-600"><i class="fas fa-truck" aria-hidden="true"></i></span>
        <div><p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Rekap &amp; Laporan</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Rekap Pemakaian Ban</h1>
        <p class="mt-1 text-sm text-gray-500">Pilih periode dan unit kendaraan untuk melihat riwayat pemakaian ban.</p></div>
    </div>

    <form method="GET" action="{{ route('rekap-pemakaian-ban.index') }}" class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4 flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-sm font-bold text-indigo-600">1</span><div><h2 class="font-bold text-gray-900">Pilih periode</h2><p class="text-xs text-gray-500">Tentukan rentang tanggal pemakaian.</p></div></div>
        <div class="grid items-end gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label for="start_date" class="mb-2 block text-sm font-semibold text-gray-700">Dari Tanggal</label>
            <input id="start_date" name="start_date" type="date" value="{{ $startDate }}" required class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm focus:border-indigo-500 focus:ring-indigo-200">
        </div>
        <div>
            <label for="end_date" class="mb-2 block text-sm font-semibold text-gray-700">Sampai Tanggal</label>
            <input id="end_date" name="end_date" type="date" value="{{ $endDate }}" required class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm focus:border-indigo-500 focus:ring-indigo-200">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="inline-flex h-11 items-center gap-2 whitespace-nowrap rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"><i class="fas fa-search" aria-hidden="true"></i> Tampilkan Unit</button>
            <a href="{{ route('rekap-pemakaian-ban.index') }}" class="inline-flex h-11 items-center rounded-xl border border-gray-300 px-4 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">Reset</a>
        </div>
        </div>
        @error('start_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        @error('end_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </form>

    @if($startDate && $endDate)
    <section class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3"><div class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-sm font-bold text-indigo-600">2</span><div><h2 class="font-bold text-gray-900">Pilih unit kendaraan</h2><p class="mt-1 text-xs text-gray-500">Pilih unit untuk melihat detail pemakaian ban.</p></div></div><span class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-600">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span></div>
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($units as $vehicle)
            <a href="{{ route('rekap-pemakaian-ban.index', ['start_date' => $startDate, 'end_date' => $endDate, 'unit' => $vehicle->mobil_id]) }}" @if((int) $unit === (int) $vehicle->mobil_id) aria-current="true" @endif class="flex items-center gap-3 rounded-xl border p-4 transition {{ (int) $unit === (int) $vehicle->mobil_id ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100' : 'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50' }}"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500"><i class="fas fa-truck" aria-hidden="true"></i></span><span class="min-w-0"><span class="block break-words font-bold text-gray-900">{{ $vehicle->unit_name ?: ($vehicle->mobil?->nomor_polisi && $vehicle->mobil->nomor_polisi !== '-' ? $vehicle->mobil->nomor_polisi : ($vehicle->mobil?->no_kir ?: 'Unit #'.$vehicle->mobil_id)) }}</span><span class="mt-1 block text-xs text-gray-500">{{ $vehicle->total }} catatan pemakaian</span></span></a>
            @empty
            <p class="col-span-full py-5 text-center text-sm text-gray-500">Tidak ada pemakaian ban untuk unit kendaraan pada periode ini.</p>
            @endforelse
        </div>
    </section>
    @else
    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">Pilih rentang tanggal untuk melihat unit kendaraan.</div>
    @endif

    @if($items)
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-5 sm:px-6">
            <div><h2 class="font-bold text-gray-900">Detail pemakaian ban</h2><p class="mt-1 text-sm text-gray-500">Total pemakaian unit terpilih: <span class="font-semibold text-indigo-700">{{ number_format($items->total()) }} ban</span></p></div>
            <a href="{{ route('rekap-pemakaian-ban.index', ['start_date' => $startDate, 'end_date' => $endDate, 'unit' => $unit, 'print' => 1]) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"><i class="fas fa-print" aria-hidden="true"></i> Print</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Bukti Pakai</th>
                        <th class="px-4 py-3">Tipe Ban</th>
                        <th class="px-4 py-3">Merek / Ukuran</th>
                        <th class="px-4 py-3">Nomor Seri</th>
                        <th class="px-4 py-3">Unit</th>
                        <th class="px-4 py-3">Penerima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($items as $ban)
                        <tr class="transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3">{{ $ban->tanggal_digunakan?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-indigo-700">{{ $ban->nomor_bukti_pakai ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $ban->nama_ban ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $ban->merk ?: '-' }} / {{ $ban->ukuran ?: '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $ban->nomor_seri ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $ban->unit_name ?: ($ban->mobil?->nomor_polisi ?: ($ban->mobil?->no_kir ?: '-')) }}</td>
                            <td class="px-4 py-3">{{ $ban->penerima_name ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">Belum ada pemakaian ban untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $items->links() }}</div>
    </div>
    @endif
</div>
@endsection
