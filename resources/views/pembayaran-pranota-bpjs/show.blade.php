@extends('layouts.app')
@section('title', 'Detail Pembayaran Pranota BPJS')
@section('page_title', 'Detail Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4"><h1 class="text-2xl font-bold text-gray-800">{{ $payment->nomor_pembayaran }}</h1><a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="px-4 py-2 border rounded-lg">Kembali</a></div>
    <div class="bg-white border rounded-xl p-6 grid md:grid-cols-2 gap-5 text-sm">
        <div><p class="text-gray-500">Tanggal Pembayaran</p><p class="font-semibold">{{ $payment->tanggal_pembayaran->format('d/m/Y') }}</p></div>
        <div><p class="text-gray-500">Status</p><p class="text-green-700 font-semibold">Lunas</p></div>
        <div><p class="text-gray-500">Kas/Bank (Kredit)</p><p>{{ $payment->bank?->nama_akun ?? '-' }}</p></div>
        <div><p class="text-gray-500">Akun BPJS / Hutang BPJS (Debit)</p><p>{{ $payment->akunCoa?->nama_akun ?? '-' }}</p></div>
        <div><p class="text-gray-500">Nomor Accurate</p><p>{{ $payment->nomor_accurate ?? '-' }}</p></div>
        <div><p class="text-gray-500">Dibuat Oleh</p><p>{{ $payment->createdBy?->name ?? '-' }}</p></div>
        <div class="md:col-span-2"><p class="text-gray-500">Keterangan</p><p class="whitespace-pre-line">{{ $payment->keterangan ?? '-' }}</p></div>
    </div>
    <div class="bg-white border rounded-xl overflow-x-auto"><table class="w-full text-sm text-left"><thead class="bg-gray-50"><tr><th class="p-4">Nomor Pranota</th><th class="p-4">Periode</th><th class="p-4 text-right">Dibayar</th></tr></thead><tbody class="divide-y">
        @foreach($payment->pranotas as $pranota)
        <tr><td class="p-4">@can('pranota-bpjs-view')<a class="text-blue-600" href="{{ route('pranota-bpjs.show', $pranota) }}">{{ $pranota->nomor_pranota }}</a>@else{{ $pranota->nomor_pranota }}@endcan</td><td class="p-4">{{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</td><td class="p-4 text-right">Rp {{ number_format($pranota->pivot->subtotal, 2, ',', '.') }}</td></tr>
        @endforeach
    </tbody><tfoot class="bg-green-50 font-bold"><tr><td colspan="2" class="p-4">Total Pembayaran</td><td class="p-4 text-right">Rp {{ number_format($payment->total_pembayaran, 2, ',', '.') }}</td></tr></tfoot></table></div>
</div>
@endsection
