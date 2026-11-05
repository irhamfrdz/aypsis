@extends('layouts.app')
@section('title', 'Pembayaran Pranota BPJS')
@section('page_title', 'Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-10">
    <div><p class="text-xs font-semibold uppercase tracking-widest text-indigo-600">Payroll / BPJS</p><h1 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">Pembayaran Pranota BPJS</h1><p class="text-sm text-gray-500 mt-1">Kelola pembayaran dan pantau sisa tagihan setiap pranota.</p></div>
    @include('pembayaran-pranota-bpjs.partials.messages')
    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="w-full sm:max-w-md"><label for="search-pranota" class="mb-1.5 block text-sm font-medium text-gray-700">Cari nomor pranota</label><input id="search-pranota" name="search" value="{{ request('search') }}" placeholder="Masukkan nomor pranota" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
        <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Cari</button>
        <a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
    </form>
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-gray-900">Daftar Pranota</h2><p class="mt-1 text-sm text-gray-500">Lihat status dan riwayat pembayaran BPJS.</p></div>
        <div class="overflow-x-auto"><table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="p-4">Nomor Pranota</th><th class="p-4">Tanggal / Periode</th><th class="p-4">Status</th><th class="p-4 text-right">Total</th><th class="p-4 text-right">Terbayar</th><th class="p-4 text-right">Sisa Tagihan</th><th class="p-4">Aksi</th></tr></thead>
            <tbody class="divide-y">
            @forelse($pranotas as $pranota)
                @php
                    $paid = round($pranota->pembayaran->sum('pivot.subtotal'), 2);
                    $remaining = max(0, round($pranota->grand_total - $paid, 2));
                @endphp
                <tr class="align-top hover:bg-gray-50/70">
                    <td class="p-4 font-semibold text-gray-900 whitespace-nowrap">{{ $pranota->nomor_pranota }}</td>
                    <td class="p-4 whitespace-nowrap">{{ $pranota->tanggal_pranota->format('d/m/Y') }}<br><span class="text-gray-500">{{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</span></td>
                    <td class="p-4 whitespace-nowrap"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $pranota->status === 'cancelled' ? 'bg-gray-100 text-gray-700' : ($pranota->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($paid > 0 ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700')) }}">{{ $pranota->status === 'cancelled' ? 'Dibatalkan' : ($pranota->status === 'paid' ? 'Lunas' : ($paid > 0 ? 'Dibayar Sebagian' : 'Belum Dibayar')) }}</span></td>
                    <td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($pranota->grand_total, 2, ',', '.') }}</td>
                    <td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($paid, 2, ',', '.') }}</td>
                    <td class="p-4 text-right whitespace-nowrap font-semibold text-gray-900">Rp {{ number_format($remaining, 2, ',', '.') }}</td>
                    <td class="p-4">
                        @can('pembayaran-pranota-bpjs-create')
                        @if($remaining > 0 && in_array($pranota->status, ['draft', 'approved', 'unpaid', 'partial']))
                        <a href="{{ route('pembayaran-pranota-bpjs.create', ['pranota_id' => $pranota->id]) }}" class="inline-block rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700 whitespace-nowrap">Pisah Pembayaran</a>
                        @endif
                        @endcan
                        @if($pranota->pembayaran->isNotEmpty())
                        <details class="mt-3"><summary class="cursor-pointer text-xs font-semibold text-indigo-600 hover:text-indigo-800">Riwayat Pembayaran ({{ $pranota->pembayaran->count() }})</summary>
                            @foreach($pranota->pembayaran as $payment)
                            <a class="mt-2 block rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs hover:border-indigo-300" href="{{ route('pembayaran-pranota-bpjs.show', $payment) }}"><span class="block font-semibold text-indigo-600 whitespace-nowrap">{{ $payment->nomor_pembayaran }}</span><span class="mt-1 block whitespace-nowrap text-gray-700">Rp {{ number_format($payment->pivot->subtotal, 2, ',', '.') }}</span></a>
                            @endforeach
                        </details>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-8 text-center text-gray-500">Belum ada pranota BPJS.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    {{ $pranotas->links() }}
</div>
@endsection
