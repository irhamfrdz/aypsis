@extends('layouts.app')
@section('title', 'Rekap Pemakaian Barang')
@section('page_title', 'Dashboard Pemakaian Barang')


@push('styles')
<style>
    body:has(.usage-results) { font-family: Arial, sans-serif; font-size: 12px; }
    body:has(.usage-results) #page-content { background: #F5F7FA; }
    body:has(.usage-results) > header h1 { font-family: Arial, sans-serif; font-size: 20px; }
    body:has(.usage-results) #sidebar :is(a, input, .menu-text),
    body:has(.usage-results) > header :is(button, a, span:not([class*="fa-"])) { font-family: Arial, sans-serif; font-size: 12px; }
    .usage-results {
        --results-navy: #1E3A5F; --results-bg: #F5F7FA; --results-line: #e1e6ed;
        font: 12px/1.5 Arial, sans-serif; color: #344256; width: 100%; min-width: 0;
    }
    .usage-results :is(p, span, a, button, input, select, label, table, th, td) { font-family: Arial, sans-serif; font-size: 12px; }
    .usage-results h1 { font: 600 20px/1.4 Arial, sans-serif; color: var(--results-navy); }
    .usage-results h2 { font: 600 16px/1.4 Arial, sans-serif; color: var(--results-navy); }
    .usage-results .usage-results-header { margin-bottom: 18px; gap: 12px 20px; }
    .usage-results .usage-results-header p:first-child { color: #8490a1; font-weight: 500; letter-spacing: .04em; margin-bottom: 4px; }
    .usage-results .usage-results-header p:last-child { color: #748091; margin-top: 4px; }
    .usage-results .usage-results-header > a {
        min-height: 34px; border: 1px solid #cdd7e4; border-radius: 5px; padding: 7px 12px;
        background: #fff; color: var(--results-navy); font-weight: 500; flex-shrink: 0;
    }
    .usage-results .usage-results-header > a:hover { background: #edf2f8; border-color: #9bacc1; }
    .usage-results :is(a, button, input, select):focus-visible { outline: 2px solid #718cac; outline-offset: 2px; }
    .usage-results .usage-results-panel { border-color: var(--results-line); border-radius: 8px; background: #fff; box-shadow: 0 2px 6px rgb(30 58 95 / 4%); }
    .usage-results .usage-results-period { background: var(--results-bg); border-color: var(--results-line); padding: 16px; }
    .usage-results .usage-results-period > div { display: flex; flex-wrap: wrap; gap: 6px 32px; color: #6b7788; margin-top: 8px; }
    .usage-results .usage-results-period p { margin: 0; }
    .usage-results .usage-results-table { padding: 0; }
    .usage-results .usage-results-table > div { border: 0; border-radius: 0; }
    .usage-results table { width: 100%; min-width: 640px; border-collapse: collapse; }
    .usage-results thead th { padding: 11px 16px; background: #fafbfd; border-bottom: 1px solid var(--results-line); color: #647184; font-weight: 600; letter-spacing: .02em; white-space: nowrap; }
    .usage-results thead th:first-child { width: 52px; }
    .usage-results thead th:nth-child(2) { min-width: 220px; }
    .usage-results thead th:nth-child(n+3) { min-width: 170px; }
    .usage-results thead th:nth-child(4) { color: var(--results-navy); }
    .usage-results td { padding: 10px 16px; border-color: #edf0f4; vertical-align: middle; }
    .usage-results tbody tr { transition: none; }
    .usage-results tbody tr:hover { background: #f8fafc; }
    .usage-results tbody td:first-child { color: #8490a1; font-variant-numeric: tabular-nums; }
    .usage-results tbody td:nth-child(2) { color: #344256; font-weight: 600; overflow-wrap: anywhere; }
    .usage-results :is(td, th):nth-child(n+3) { text-align: right; font-variant-numeric: tabular-nums; }
    .usage-results tbody td:nth-child(4) { color: var(--results-navy); font-weight: 600; }
    .usage-results tbody td:nth-child(5) { color: #536379; font-weight: 500; }
    .usage-results tbody td[colspan] { text-align: center; padding: 28px 16px; color: #748091; }
    .usage-results tfoot { border-top: 1px solid #dce3ec; background: var(--results-bg); }
    .usage-results tfoot th { padding: 11px 16px; color: var(--results-navy); font-weight: 600; font-variant-numeric: tabular-nums; }
    .usage-results tfoot tr:last-child { background: #edf2f8; border-top: 1px solid #dce3ec; }
    .usage-results .overflow-x-auto::-webkit-scrollbar { height: 5px; }
    .usage-results .overflow-x-auto::-webkit-scrollbar-thumb { background: #d5dde7; border-radius: 4px; }
    @media (max-width: 640px) {
        .usage-results .usage-results-header { align-items: flex-start; }
        .usage-results .usage-results-header > a { align-self: flex-end; }
        .usage-results .usage-results-period { padding: 14px; }
        .usage-results .usage-results-period > div { flex-direction: column; gap: 6px; }
        .usage-results :is(td, th), .usage-results thead th, .usage-results tfoot th { padding: 10px 14px; }
    }
</style>
@endpush

@section('content')
<div class="usage-results">
    <div class="usage-results-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600 mb-2">Dashboard Pemakaian Barang</p>
            <h1 class="text-2xl font-bold text-gray-900">Rekap {{ $categories[$kategori] }}</h1>
            <p class="text-sm text-gray-500 mt-2">Periode {{ $fromDate->format('d/M/Y') }} sampai {{ $toDate->format('d/M/Y') }}</p>
        </div>
        <a href="{{ route('stock-amprahan.dashboard-pemakaian', request()->only(['kategori_pemakai', 'from_date', 'to_date'])) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-indigo-200 bg-white text-indigo-700 text-sm hover:bg-indigo-50">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Ubah Kategori / Periode
        </a>
    </div>

    <div class="usage-results-panel bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="usage-results-period bg-indigo-50 px-4 py-4 border-b border-indigo-200">
            <h2 class="text-base font-semibold text-indigo-800">Pemakaian per {{ $categories[$kategori] }}</h2>
            <div class="text-xs text-indigo-700 mt-2 space-y-1">
                <p>Saldo awal:
                    @if($fromDate->equalTo($yearStart))
                        Rp 0,00 (periode dimulai pada {{ $yearStart->format('d/M/Y') }}).
                    @else
                        {{ $yearStart->format('d/M/Y') }} sampai {{ $fromDate->copy()->subDay()->format('d/M/Y') }}.
                    @endif
                </p>
                <p>Saldo berjalan: {{ $fromDate->format('d/M/Y') }} sampai {{ $toDate->format('d/M/Y') }}.</p>
            </div>
        </div>
        <div class="usage-results-table p-4 sm:p-5">
            @php
                $totalSaldoAwal = collect($cards)->sum('saldo_awal');
                $totalSaldoBerjalan = collect($cards)->sum('saldo_berjalan');
                $totalBanPerut = collect($cards)->sum('ban_perut_total');
            @endphp
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">{{ $categories[$kategori] }}</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Saldo Awal</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-indigo-700">Saldo Berjalan</th>
                            @if($kategori === 'kendaraan')
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Penggantian Ban Perut</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($cards as $index => $card)
                            <tr class="transition hover:bg-indigo-50/50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-semibold uppercase text-gray-800">{{ $card['nama'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">Rp {{ number_format($card['saldo_awal'], 2, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-indigo-900">Rp {{ number_format($card['saldo_berjalan'], 2, ',', '.') }}</td>
                                @if($kategori === 'kendaraan')
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-indigo-700">{{ number_format($card['ban_perut_total'] ?? 0) }} kali</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $kategori === 'kendaraan' ? 5 : 4 }}" class="px-4 py-10 text-center text-sm text-gray-500">Belum ada pemakai untuk kategori ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($cards))
                        <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                            <tr>
                                <th colspan="2" class="px-4 py-3 text-left text-sm font-bold text-gray-800">Total</th>
                                <th class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-gray-800">Rp {{ number_format($totalSaldoAwal, 2, ',', '.') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-indigo-900">Rp {{ number_format($totalSaldoBerjalan, 2, ',', '.') }}</th>
                                @if($kategori === 'kendaraan')
                                    <th class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-indigo-700">{{ number_format($totalBanPerut) }} kali</th>
                                @endif
                            </tr>
                            <tr class="bg-indigo-50">
                                <th colspan="2" class="px-4 py-3 text-left text-sm font-bold text-gray-800">Total Biaya (Saldo Awal + Saldo Berjalan)</th>
                                <th colspan="2" class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-indigo-900">Rp {{ number_format($totalSaldoAwal + $totalSaldoBerjalan, 2, ',', '.') }}</th>
                                @if($kategori === 'kendaraan')
                                    <th></th>
                                @endif
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
