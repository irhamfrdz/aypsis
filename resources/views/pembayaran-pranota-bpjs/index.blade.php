@extends('layouts.app')
@section('title', 'Pembayaran Pranota BPJS')
@section('page_title', 'Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-[1440px] mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Payroll <span class="mx-1 text-gray-300">/</span> BPJS</p><h1 class="mt-2 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Pembayaran Pranota BPJS</h1><p class="mt-2 text-sm text-gray-500">Kelola pembayaran, cetak bukti, dan pantau sisa tagihan pranota.</p></div><div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800"><span class="font-semibold">{{ $pranotas->total() }}</span> pranota tercatat</div></div>
    @include('pembayaran-pranota-bpjs.partials.messages')
    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="w-full sm:max-w-lg"><label for="search-pranota" class="mb-1.5 block text-sm font-semibold text-gray-700">Cari nomor pranota</label><input id="search-pranota" name="search" value="{{ request('search') }}" placeholder="Contoh: PBPJS26110003" class="w-full rounded-xl border-gray-300 bg-gray-50 text-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"></div>
        <button class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">Cari Pranota</button>
        <a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
    </form>
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4"><div><h2 class="font-semibold text-gray-900">Daftar Pranota</h2><p class="mt-1 text-sm text-gray-500">Lihat tagihan, status, dan riwayat pembayaran BPJS.</p></div><span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $pranotas->count() }} ditampilkan</span></div>
        <div class="overflow-x-auto"><table class="w-full min-w-[1050px] text-sm text-left">
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500"><tr><th class="px-5 py-3.5">Nomor Pranota</th><th class="px-4 py-3.5">Tanggal / Periode</th><th class="px-4 py-3.5">Status</th><th class="px-4 py-3.5 text-right">Total Tagihan</th><th class="px-4 py-3.5 text-right">Terbayar</th><th class="px-4 py-3.5 text-right">Sisa Tagihan</th><th class="px-5 py-3.5">Aksi & Riwayat</th></tr></thead>
            <tbody class="divide-y">
            @forelse($pranotas as $pranota)
                @php
                    $paid = round($pranota->pembayaran->sum('pivot.subtotal'), 2);
                    $remaining = max(0, round($pranota->grand_total - $paid, 2));
                @endphp
                <tr class="align-top transition hover:bg-indigo-50/30">
                    <td class="px-5 py-4"><a href="{{ route('pranota-bpjs.show', $pranota) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ $pranota->nomor_pranota }}</a><span class="mt-1 block text-xs text-gray-400">ID {{ $pranota->id }}</span></td>
                    <td class="px-4 py-4 whitespace-nowrap"><span class="font-medium text-gray-800">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</span><span class="mt-1 block text-xs text-gray-500">Periode {{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</span></td>
                    <td class="px-4 py-4 whitespace-nowrap"><span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-xs font-semibold {{ $pranota->status === 'cancelled' ? 'bg-gray-100 text-gray-700' : ($pranota->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($paid > 0 ? 'bg-amber-50 text-amber-700' : 'bg-sky-50 text-sky-700')) }}"><span class="h-1.5 w-1.5 rounded-full {{ $pranota->status === 'cancelled' ? 'bg-gray-400' : ($pranota->status === 'paid' ? 'bg-emerald-500' : ($paid > 0 ? 'bg-amber-500' : 'bg-sky-500')) }}"></span>{{ $pranota->status === 'cancelled' ? 'Dibatalkan' : ($pranota->status === 'paid' ? 'Lunas' : ($paid > 0 ? 'Dibayar Sebagian' : 'Belum Dibayar')) }}</span></td>
                    <td class="px-4 py-4 text-right whitespace-nowrap font-medium text-gray-700">Rp {{ number_format($pranota->grand_total, 2, ',', '.') }}</td>
                    <td class="px-4 py-4 text-right whitespace-nowrap font-medium text-emerald-700">Rp {{ number_format($paid, 2, ',', '.') }}</td>
                    <td class="px-4 py-4 text-right whitespace-nowrap font-semibold {{ $remaining > 0 ? 'text-amber-700' : 'text-gray-500' }}">Rp {{ number_format($remaining, 2, ',', '.') }}</td>
                    <td class="px-5 py-4">
                        @can('pembayaran-pranota-bpjs-create')
                        @if($remaining > 0 && in_array($pranota->status, ['draft', 'approved', 'unpaid', 'partial']))
                        <a href="{{ route('pembayaran-pranota-bpjs.create', ['pranota_id' => $pranota->id]) }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700 whitespace-nowrap">Buat Pembayaran</a>
                        @endif
                        @endcan
                        @if($pranota->pembayaran->isNotEmpty())
                        <details class="mt-3"><summary class="cursor-pointer select-none text-xs font-semibold text-indigo-700 hover:text-indigo-900">Riwayat Pembayaran <span class="ml-1 rounded-full bg-indigo-50 px-1.5 py-0.5">{{ $pranota->pembayaran->count() }}</span></summary>
                            @foreach($pranota->pembayaran as $payment)
                            <div class="mt-2 flex items-center justify-between gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs"><a class="min-w-0" href="{{ route('pembayaran-pranota-bpjs.show', $payment) }}"><span class="block font-semibold text-indigo-700 whitespace-nowrap">{{ $payment->nomor_pembayaran }}</span><span class="mt-1 block whitespace-nowrap text-gray-600">Rp {{ number_format($payment->pivot->subtotal, 2, ',', '.') }}</span></a><a target="_blank" rel="noopener" aria-label="Cetak {{ $payment->nomor_pembayaran }}" class="shrink-0 rounded-lg border border-gray-300 bg-white px-3 py-1.5 font-semibold text-gray-700 transition hover:border-indigo-300 hover:text-indigo-700" href="{{ route('pembayaran-pranota-bpjs.print', $payment) }}">Cetak</a></div>
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
