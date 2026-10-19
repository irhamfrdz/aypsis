@extends('layouts.app')

@section('title', 'Pranota Tagihan Lain Lain Batam')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pranota Tagihan Batam</h1>
            <p class="mt-1 text-sm text-gray-600">Daftar pranota yang dibuat dari tagihan kendaraan dan tagihan lain lain.</p>
        </div>
        <a href="{{ route('tagihan-lain-lain-batam.index') }}" class="rounded-lg border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Kembali ke Tagihan</a>
    </div>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Nomor Pranota</th><th class="px-4 py-3">Jumlah Tagihan</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Keterangan</th><th class="px-4 py-3 text-right">Aksi</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pranotas as $pranota)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-semibold">{{ $pranota->nomor_pranota }}</td>
                        <td class="px-4 py-3">{{ $pranota->items_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $pranota->total_biaya, 0, ',', '.') }}</td>
                        <td class="max-w-md px-4 py-3">{{ $pranota->keterangan ?: '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right"><a href="{{ route('pranota-tagihan-lain-lain-batam.show', $pranota) }}" class="font-medium text-blue-700 hover:underline">Lihat rincian</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada pranota tagihan Batam.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $pranotas->links() }}</div>
</div>
@endsection
