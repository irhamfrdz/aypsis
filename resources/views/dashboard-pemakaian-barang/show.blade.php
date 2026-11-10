@extends('layouts.app')
@section('title', 'Rekap Pemakaian Barang')
@section('page_title', 'Dashboard Pemakaian Barang')

@section('content')
<div class="container mx-auto px-4 py-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600 mb-2">Dashboard Pemakaian Barang</p>
            <h1 class="text-2xl font-bold text-gray-900">Rekap {{ $categories[$kategori] }}</h1>
            <p class="text-sm text-gray-500 mt-2">Periode {{ $fromDate->format('d/m/Y') }} sampai {{ $toDate->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('stock-amprahan.dashboard-pemakaian', request()->only(['kategori_pemakai', 'from_date', 'to_date'])) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-indigo-200 bg-white text-indigo-700 text-sm hover:bg-indigo-50">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Ubah Kategori / Periode
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-indigo-50 px-4 py-4 border-b border-indigo-200">
            <h2 class="text-base font-semibold text-indigo-800">Pemakaian per {{ $categories[$kategori] }}</h2>
            <div class="text-xs text-indigo-700 mt-2 space-y-1">
                <p>Saldo awal:
                    @if($fromDate->equalTo($yearStart))
                        Rp0 (periode dimulai pada 1 Januari).
                    @else
                        {{ $yearStart->format('d/m/Y') }} sampai {{ $fromDate->copy()->subDay()->format('d/m/Y') }}.
                    @endif
                </p>
                <p>Saldo berjalan: {{ $fromDate->format('d/m/Y') }} sampai {{ $toDate->format('d/m/Y') }}.</p>
            </div>
        </div>
        <div class="p-4 sm:p-5">
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
