@extends('layouts.app')

@section('title', 'Pranota '.$pranota->nomor_pranota)
@section('page_title', 'Rincian Pranota Gaji Supir Batam')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-gray-900">{{ $pranota->nomor_pranota }}</h1><p class="mt-1 text-sm text-gray-600">Tanggal {{ $pranota->tanggal_pranota->format('d/m/Y') }} · {{ $pranota->items->count() }} gaji supir</p></div>
        <div class="flex gap-2"><a href="{{ route('pranota-gaji-supir-batam.print', $pranota) }}" target="_blank" rel="noopener" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700">Cetak Pranota</a><a href="{{ route('pranota-gaji-supir-batam.index') }}" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Kembali</a></div>
    </div>
    @if(session('success'))<div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    <div class="mb-5 grid gap-4 rounded-xl border bg-white p-5 shadow-sm sm:grid-cols-3">
        <div><p class="text-xs font-semibold uppercase text-gray-500">Total Pranota</p><p class="mt-1 text-xl font-bold">Rp {{ number_format((float) $pranota->total_gaji, 0, ',', '.') }}</p></div>
        <div><p class="text-xs font-semibold uppercase text-gray-500">Dibuat oleh</p><p class="mt-1">{{ $pranota->creator?->name ?: '-' }}</p></div>
        <div><p class="text-xs font-semibold uppercase text-gray-500">Keterangan</p><p class="mt-1">{{ $pranota->keterangan ?: '-' }}</p></div>
    </div>
    <div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600"><tr><th class="px-4 py-3">No.</th><th class="px-4 py-3">Supir</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3 text-right">Gaji Bersih</th><th class="px-4 py-3">Status Bayar</th><th class="px-4 py-3">Slip</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pranota->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium">{{ $item->gaji?->karyawan?->nama_lengkap ?: 'Supir tidak tersedia' }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $item->gaji?->periode_text ?: '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $item->total_gaji, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $item->gaji?->status_pembayaran ?: '-' }}</td>
                        <td class="px-4 py-3">
                            @can('gaji-supir-batam-view')
                                @if($item->gaji)
                                    <a href="{{ route('gaji-supir-batam.show', $item->gaji) }}" class="text-indigo-700 hover:underline">Lihat slip</a>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50"><tr><td colspan="3" class="px-4 py-3 text-right font-bold">Total</td><td class="px-4 py-3 text-right font-bold">Rp {{ number_format((float) $pranota->total_gaji, 0, ',', '.') }}</td><td colspan="2"></td></tr></tfoot>
        </table>
    </div>
</div>
@endsection
