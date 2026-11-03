@extends('layouts.app')
@section('title', 'Pembayaran Pranota BPJS')
@section('page_title', 'Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div><h1 class="text-2xl font-bold text-gray-800">Pembayaran Pranota BPJS</h1><p class="text-sm text-gray-500 mt-1">Riwayat pelunasan pranota BPJS melalui kas/bank.</p></div>
        @can('pembayaran-pranota-bpjs-create')
        <a href="{{ route('pembayaran-pranota-bpjs.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg">Buat Pembayaran</a>
        @endcan
    </div>
    @include('pembayaran-pranota-bpjs.partials.messages')
    <form method="GET" class="flex gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Nomor pembayaran / pranota / Accurate" aria-label="Cari pembayaran" class="border-gray-300 rounded-lg w-full max-w-md">
        <button class="bg-gray-800 text-white px-4 py-2 rounded-lg">Cari</button>
        <a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="px-3 py-2 text-gray-600">Reset</a>
    </form>
    <div class="bg-white rounded-xl border overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-4">Nomor Pembayaran</th><th class="p-4">Tanggal</th><th class="p-4">Kas/Bank</th><th class="p-4">Status</th><th class="p-4 text-right">Total</th><th class="p-4">Aksi</th></tr></thead>
            <tbody class="divide-y">
            @forelse($payments as $payment)
                <tr><td class="p-4 font-semibold">{{ $payment->nomor_pembayaran }}</td><td class="p-4 whitespace-nowrap">{{ $payment->tanggal_pembayaran->format('d/m/Y') }}</td><td class="p-4">{{ $payment->bank?->nama_akun ?? '-' }}</td><td class="p-4 text-green-700">Lunas</td><td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($payment->total_pembayaran, 2, ',', '.') }}</td><td class="p-4"><a href="{{ route('pembayaran-pranota-bpjs.show', $payment) }}" class="text-blue-600">Detail</a></td></tr>
            @empty
                <tr><td colspan="6" class="p-8 text-center text-gray-500">Belum ada pembayaran pranota BPJS.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $payments->links() }}
</div>
@endsection
