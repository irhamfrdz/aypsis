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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
                @forelse($cards as $card)
                    <article class="rounded-lg p-4 border border-gray-200 bg-gray-50 hover:border-indigo-300 hover:shadow-sm transition-all text-center">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide break-words min-h-10">{{ $card['nama'] }}</h3>
                        <div class="mt-4">
                            <p class="text-xs text-gray-500">Saldo Awal</p>
                            <p class="text-base font-semibold text-gray-700 mt-1 break-words">Rp {{ number_format($card['saldo_awal'], 2, ',', '.') }}</p>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-xs text-indigo-600">Saldo Berjalan</p>
                            <p class="text-lg font-bold text-indigo-900 mt-1 break-words">Rp {{ number_format($card['saldo_berjalan'], 2, ',', '.') }}</p>
                        </div>
                    </article>
                @empty
                    <p class="col-span-full py-10 text-center text-gray-500">Belum ada pemakai untuk kategori ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
