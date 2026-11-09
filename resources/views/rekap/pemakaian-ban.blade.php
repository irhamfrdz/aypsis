@extends('layouts.app')

@section('title', 'Rekap Pemakaian Ban')
@section('page_title', 'Rekap Pemakaian Ban')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8">
    <div class="mb-7 flex items-start gap-4">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-lg text-white shadow-sm"><i class="fas fa-truck" aria-hidden="true"></i></span>
        <div><p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Rekap &amp; Laporan</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Rekap Pemakaian Ban</h1>
        <p class="mt-2 text-sm text-gray-500">Pantau pemakaian ban dan penggantian komponen untuk setiap kendaraan.</p></div>
    </div>

    <form method="GET" action="{{ route('rekap-pemakaian-ban.index') }}" class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fas fa-calendar-alt" aria-hidden="true"></i></span><div><h2 class="text-base font-bold text-gray-900">Periode laporan</h2><p class="mt-1 text-xs text-gray-500">Tentukan rentang tanggal pemakaian untuk melihat unit.</p></div></div>
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
        <div class="flex flex-wrap items-center justify-between gap-4"><div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fas fa-truck" aria-hidden="true"></i></span><div><h2 class="text-base font-bold text-gray-900">Pilih unit kendaraan <span class="ml-1 text-sm font-medium text-gray-400">({{ $units->count() }})</span></h2><p class="mt-1 text-xs text-gray-500">Pilih unit untuk melihat rincian pemakaian.</p></div></div><span class="rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700"><i class="far fa-calendar-alt mr-1" aria-hidden="true"></i> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span></div>
        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($units as $vehicle)
            <article class="rounded-xl border p-4 shadow-sm transition {{ (int) $unit === (int) $vehicle->mobil_id ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100' : 'border-gray-200 bg-white hover:border-indigo-300 hover:shadow-md' }}">
                <a href="{{ route('rekap-pemakaian-ban.index', ['start_date' => $startDate, 'end_date' => $endDate, 'unit' => $vehicle->mobil_id]) }}" @if((int) $unit === (int) $vehicle->mobil_id) aria-current="true" @endif class="group block rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span class="flex items-center justify-between gap-3">
                    <span class="flex min-w-0 items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fas fa-truck" aria-hidden="true"></i></span><span class="min-w-0"><span class="block text-xs font-medium text-gray-500">Unit kendaraan</span><span class="block break-words font-bold text-gray-900">{{ $vehicle->unit_name ?: ($vehicle->mobil?->nomor_polisi && $vehicle->mobil->nomor_polisi !== '-' ? $vehicle->mobil->nomor_polisi : ($vehicle->mobil?->no_kir ?: 'Unit #'.$vehicle->mobil_id)) }}</span></span></span>
                    <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-indigo-600" aria-hidden="true"></i>
                </span>
                </a>
                <details open class="mt-4 border-t border-gray-100 pt-3">
                <summary class="cursor-pointer select-none text-xs font-semibold text-indigo-700 hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">Rincian pemakaian</summary>
                <span class="mt-3 block space-y-2 text-xs text-gray-600">
                    <span class="flex justify-between gap-2"><span>Pemakaian ban</span><span class="font-semibold text-gray-900">{{ $vehicle->total }} catatan</span></span>
                    <span class="flex justify-between gap-2"><span>Ring Velg</span><span class="font-semibold text-indigo-700">{{ $vehicle->ring_total ?? 0 }} kali</span></span>
                    <span class="flex justify-between gap-2"><span>Ban Dalam</span><span class="font-semibold text-indigo-700">{{ $vehicle->ban_dalam_total ?? 0 }} kali</span></span>
                    <span class="flex justify-between gap-2"><span>Ban Perut</span><span class="font-semibold text-indigo-700">{{ $vehicle->ban_perut_total ?? 0 }} kali</span></span>
                    <span class="flex justify-between gap-2"><span>Velg Lobang 8</span><span class="font-semibold text-indigo-700">{{ $vehicle->velg_8_total ?? 0 }} kali</span></span>
                    <span class="flex justify-between gap-2"><span>Velg Lobang 10</span><span class="font-semibold text-indigo-700">{{ $vehicle->velg_10_total ?? 0 }} kali</span></span>
                </span>
                </details>
            </article>
            @empty
            <p class="col-span-full py-5 text-center text-sm text-gray-500">Tidak ada pemakaian ban atau komponen untuk unit kendaraan pada periode ini.</p>
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

    @if($banDalamItems)
    <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-5 sm:px-6">
            <h2 class="font-bold text-gray-900">Detail penggantian Ban Dalam</h2>
            <p class="mt-1 text-sm text-gray-500">Total penggantian pada periode terpilih: <span class="font-semibold text-indigo-700">{{ number_format($banDalamItems->total()) }} kali</span></p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Nama Barang</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Penerima</th><th class="px-4 py-3">Keterangan</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($banDalamItems as $banDalamItem)
                        <tr class="transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3">{{ $banDalamItem->tanggal_keluar ? \Carbon\Carbon::parse($banDalamItem->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">{{ $banDalamItem->nama_barang }}</td>
                            <td class="px-4 py-3">{{ $banDalamItem->qty }}</td>
                            <td class="px-4 py-3">{{ $banDalamItem->penerima?->nama_lengkap ?: ($banDalamItem->penerima_manual ?: '-') }}</td>
                            <td class="px-4 py-3">{{ $banDalamItem->keterangan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada penggantian Ban Dalam untuk unit pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $banDalamItems->links() }}</div>
    </div>
    @endif

    @if($ringItems)
    <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-5 sm:px-6">
            <h2 class="font-bold text-gray-900">Detail penggantian Ring Velg</h2>
            <p class="mt-1 text-sm text-gray-500">Total penggantian pada periode terpilih: <span class="font-semibold text-indigo-700">{{ number_format($ringItems->total()) }} kali</span></p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Penerima</th><th class="px-4 py-3">Keterangan</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ringItems as $ringItem)
                        <tr class="transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3">{{ $ringItem->tanggal_keluar ? \Carbon\Carbon::parse($ringItem->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">{{ $ringItem->qty }}</td>
                            <td class="px-4 py-3">{{ $ringItem->penerima?->nama_lengkap ?: ($ringItem->penerima_manual ?: '-') }}</td>
                            <td class="px-4 py-3">{{ preg_replace('/^\[Ring Velg ID: \d+\]\s*/', '', $ringItem->keterangan ?? '') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">Belum ada penggantian Ring Velg untuk unit pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $ringItems->links() }}</div>
    </div>
    @endif
</div>
@endsection
