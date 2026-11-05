@extends('layouts.app')
@section('title', 'Detail Pembayaran Pranota BPJS')
@section('page_title', 'Detail Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4"><h1 class="text-2xl font-bold text-gray-800">{{ $payment->nomor_pembayaran }}</h1><a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="px-4 py-2 border rounded-lg">Kembali</a></div>
    @include('pembayaran-pranota-bpjs.partials.messages')
    <div class="bg-white border rounded-xl p-6 grid md:grid-cols-2 gap-5 text-sm">
        <div><p class="text-gray-500">Tanggal Pembayaran</p><p class="font-semibold">{{ $payment->tanggal_pembayaran->format('d/m/Y') }}</p></div>
        <div><p class="text-gray-500">Status</p><p class="text-green-700 font-semibold">Lunas</p></div>
        <div><p class="text-gray-500">Akun Bank</p>
            @forelse($payment->bank_allocations ?? [] as $allocation)
                <div class="mt-2"><p>{{ $allocation['nomor_akun'] }} - {{ $allocation['nama_akun'] }}</p><p class="font-semibold">Rp {{ number_format((float) $allocation['subtotal'], 2, ',', '.') }}</p></div>
            @empty
                <p>{{ $payment->bank?->nama_akun ?? '-' }}</p>
            @endforelse
        </div>
        <div><p class="text-gray-500">Bank yang Digunakan</p><p>{{ $payment->akunCoa?->nama_akun ?? '-' }}</p></div>
        <div><p class="text-gray-500">Nomor Accurate</p>
            @can('pembayaran-pranota-bpjs-create')
                <form method="POST" action="{{ route('pembayaran-pranota-bpjs.update', $payment) }}" class="mt-2 flex flex-wrap items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <label for="nomor-accurate" class="sr-only">Nomor Accurate</label>
                    <input id="nomor-accurate" name="nomor_accurate" value="{{ old('nomor_accurate', $payment->nomor_accurate) }}" maxlength="255" placeholder="Masukkan nomor Accurate" class="rounded-lg border-gray-300 text-sm">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan</button>
                </form>
            @else
                <p>{{ $payment->nomor_accurate ?? '-' }}</p>
            @endcan
        </div>
        <div><p class="text-gray-500">Dibuat Oleh</p><p>{{ $payment->createdBy?->name ?? '-' }}</p></div>
        <div class="md:col-span-2"><p class="text-gray-500">Keterangan</p><p class="whitespace-pre-line">{{ $payment->keterangan ?? '-' }}</p></div>
    </div>
    <div class="bg-white border rounded-xl overflow-x-auto"><table class="w-full text-sm text-left"><thead class="bg-gray-50"><tr><th class="p-4">Nomor Pranota</th><th class="p-4">Periode</th><th class="p-4 text-right">Dibayar</th></tr></thead><tbody class="divide-y">
        @foreach($payment->pranotas as $pranota)
        <tr><td class="p-4">@can('pranota-bpjs-view')<a class="text-blue-600" href="{{ route('pranota-bpjs.show', $pranota) }}">{{ $pranota->nomor_pranota }}</a>@else{{ $pranota->nomor_pranota }}@endcan</td><td class="p-4">{{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</td><td class="p-4 text-right">Rp {{ number_format($pranota->pivot->subtotal, 2, ',', '.') }}</td></tr>
        @endforeach
    </tbody><tfoot class="bg-green-50 font-bold"><tr><td colspan="2" class="p-4">Total Pembayaran</td><td class="p-4 text-right">Rp {{ number_format($payment->total_pembayaran, 2, ',', '.') }}</td></tr></tfoot></table></div>
    @if($parts->isNotEmpty())
    <div class="bg-white border rounded-xl overflow-x-auto">
        <h2 class="p-4 font-semibold border-b">Rincian Tipe yang Dibayar</h2>
        <table class="w-full text-sm text-left"><thead class="bg-gray-50"><tr><th class="p-4">Komponen</th><th class="p-4">Tipe</th><th class="p-4 text-right">Nominal</th></tr></thead><tbody class="divide-y">
        @foreach($parts as $part)
            <tr><td class="p-4">{{ strtoupper($part->komponen) }}</td><td class="p-4">{{ $part->tipe }}</td><td class="p-4 text-right">Rp {{ number_format($part->subtotal, 2, ',', '.') }}</td></tr>
        @endforeach
        </tbody></table>
    </div>
    @endif
</div>
@endsection
