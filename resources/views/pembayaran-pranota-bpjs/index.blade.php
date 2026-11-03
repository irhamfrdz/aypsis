@extends('layouts.app')
@section('title', 'Pembayaran Pranota BPJS')
@section('page_title', 'Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div><h1 class="text-2xl font-bold text-gray-800">Pembayaran Pranota BPJS</h1><p class="text-sm text-gray-500 mt-1">Pilih pranota untuk membayar tipe JKN atau Jamsostek secara terpisah.</p></div>
    @include('pembayaran-pranota-bpjs.partials.messages')
    <form method="GET" class="flex gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Nomor pranota" aria-label="Cari pranota" class="border-gray-300 rounded-lg w-full max-w-md">
        <button class="bg-gray-800 text-white px-4 py-2 rounded-lg">Cari</button>
        <a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="px-3 py-2 text-gray-600">Reset</a>
    </form>
    <div class="bg-white rounded-xl border overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-4">Nomor Pranota</th><th class="p-4">Tanggal / Periode</th><th class="p-4">Status</th><th class="p-4 text-right">Total</th><th class="p-4 text-right">Terbayar</th><th class="p-4 text-right">Sisa Tagihan</th><th class="p-4">Aksi</th></tr></thead>
            <tbody class="divide-y">
            @forelse($pranotas as $pranota)
                @php
                    $paid = round($pranota->pembayaran->sum('pivot.subtotal'), 2);
                    $remaining = max(0, round($pranota->grand_total - $paid, 2));
                @endphp
                <tr>
                    <td class="p-4 font-semibold">{{ $pranota->nomor_pranota }}</td>
                    <td class="p-4 whitespace-nowrap">{{ $pranota->tanggal_pranota->format('d/m/Y') }}<br><span class="text-gray-500">{{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</span></td>
                    <td class="p-4">{{ $pranota->status === 'cancelled' ? 'Dibatalkan' : ($pranota->status === 'paid' ? 'Lunas' : ($paid > 0 ? 'Dibayar Sebagian' : 'Belum Dibayar')) }}</td>
                    <td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($pranota->grand_total, 2, ',', '.') }}</td>
                    <td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($paid, 2, ',', '.') }}</td>
                    <td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($remaining, 2, ',', '.') }}</td>
                    <td class="p-4">
                        @can('pembayaran-pranota-bpjs-create')
                        @if($remaining > 0 && in_array($pranota->status, ['draft', 'approved', 'unpaid', 'partial']))
                        <a href="{{ route('pembayaran-pranota-bpjs.create', ['pranota_id' => $pranota->id]) }}" class="inline-block px-3 py-2 bg-blue-600 text-white rounded-lg whitespace-nowrap">Pisah Pembayaran</a>
                        @endif
                        @endcan
                        @if($pranota->pembayaran->isNotEmpty())
                        <details class="mt-2"><summary class="cursor-pointer text-blue-600">Riwayat Pembayaran</summary>
                            @foreach($pranota->pembayaran as $payment)
                            <a class="block mt-2 text-blue-600 whitespace-nowrap" href="{{ route('pembayaran-pranota-bpjs.show', $payment) }}">{{ $payment->nomor_pembayaran }} ? Rp {{ number_format($payment->pivot->subtotal, 2, ',', '.') }}</a>
                            @endforeach
                        </details>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-8 text-center text-gray-500">Belum ada pranota BPJS.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pranotas->links() }}
</div>
@endsection
