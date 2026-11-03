@extends('layouts.app')

@section('title', 'Pranota Gaji Supir Batam')
@section('page_title', 'Pranota Gaji Supir Batam')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pranota Gaji Supir Batam</h1>
            <p class="mt-1 text-sm text-gray-600">Kumpulan gaji supir yang disiapkan untuk pembayaran.</p>
        </div>
        @can('pranota-gaji-supir-batam-create')
            <a href="{{ route('pranota-gaji-supir-batam.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Buat Pranota</a>
        @endcan
    </div>
    @if(session('success'))<div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600"><tr>
                <th class="px-4 py-3">Nomor Pranota</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3 text-center">Jumlah Supir</th><th class="px-4 py-3 text-right">Total Gaji</th><th class="px-4 py-3">Keterangan</th><th class="px-4 py-3 text-right">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pranotas as $pranota)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $pranota->nomor_pranota }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">{{ $pranota->items_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $pranota->total_gaji, 0, ',', '.') }}</td>
                        <td class="max-w-xs px-4 py-3">{{ $pranota->keterangan ?: '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('pranota-gaji-supir-batam.show', $pranota) }}" class="font-medium text-indigo-700 hover:underline">Rincian</a>
                            <a href="{{ route('pranota-gaji-supir-batam.print', $pranota) }}" target="_blank" rel="noopener" class="ml-3 font-medium text-purple-700 hover:underline">Cetak</a>
                            @can('pranota-gaji-supir-batam-delete')
                                <form action="{{ route('pranota-gaji-supir-batam.destroy', $pranota) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus pranota ini? Gaji yang belum dibayar akan tersedia kembali.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="font-medium text-red-700 hover:underline">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada pranota gaji supir Batam.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $pranotas->links() }}</div>
</div>
@endsection
