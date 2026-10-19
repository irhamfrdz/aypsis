@extends('layouts.app')

@section('title', 'Rincian Pranota '.$pranota->nomor_pranota)

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pranota {{ $pranota->nomor_pranota }}</h1>
            <p class="mt-1 text-sm text-gray-600">Tanggal pranota: {{ $pranota->tanggal_pranota->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('pranota-tagihan-lain-lain-batam.index') }}" class="rounded-lg border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Kembali ke Daftar Pranota</a>
    </div>

    <div class="mb-6 grid gap-4 rounded-lg border bg-white p-5 shadow-sm sm:grid-cols-3">
        <div><p class="text-xs font-semibold uppercase text-gray-500">Tanggal</p><p class="mt-1 font-medium">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</p></div>
        <div><p class="text-xs font-semibold uppercase text-gray-500">Jumlah Tagihan</p><p class="mt-1 font-medium">{{ $pranota->items->count() }}</p></div>
        <div><p class="text-xs font-semibold uppercase text-gray-500">Total Pranota</p><p class="mt-1 text-lg font-bold">Rp {{ number_format((float) $pranota->total_biaya, 0, ',', '.') }}</p></div>
        @if ($pranota->keterangan)
            <div class="sm:col-span-3"><p class="text-xs font-semibold uppercase text-gray-500">Keterangan</p><p class="mt-1 whitespace-pre-line">{{ $pranota->keterangan }}</p></div>
        @endif
    </div>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                <tr><th class="px-4 py-3">Tanggal Tagihan</th><th class="px-4 py-3">No. Tagihan</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">No. Polisi / KIR</th><th class="px-4 py-3">Vendor</th><th class="px-4 py-3">Rincian</th><th class="px-4 py-3 text-right">Biaya</th><th class="px-4 py-3">PPh</th><th class="px-4 py-3 text-right">Total</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($pranota->items as $item)
                    @php($tagihan = $item->tagihan)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3">{{ $tagihan?->tanggal_service?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium">{{ $tagihan?->nomor_tagihan ?? 'Tagihan dihapus' }}</td>
                        <td class="px-4 py-3">{{ $tagihan?->jenis_tagihan === 'kendaraan' ? 'Kendaraan' : 'Lain Lain' }}</td>
                        <td class="px-4 py-3">{{ $tagihan?->jenis_tagihan === 'kendaraan' ? ($tagihan?->nomor_polisi ?: '—') : '—' }}</td>
                        <td class="px-4 py-3">{{ $tagihan?->bengkel ?? '—' }}</td>
                        <td class="max-w-sm px-4 py-3">{{ $tagihan?->rincian_service ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) ($tagihan?->biaya ?? 0), 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $tagihan ? rtrim(rtrim(number_format((float) $tagihan->pph_rate, 1, ',', ''), '0'), ',').'%' : '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $item->total_biaya, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50"><tr><td colspan="8" class="px-4 py-3 text-right font-bold">Total Pranota</td><td class="whitespace-nowrap px-4 py-3 text-right font-bold">Rp {{ number_format((float) $pranota->total_biaya, 0, ',', '.') }}</td></tr></tfoot>
        </table>
    </div>
</div>
@endsection
