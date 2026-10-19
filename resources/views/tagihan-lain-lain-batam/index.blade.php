@extends('layouts.app')

@section('title', 'Tagihan Lain Lain Batam')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tagihan Lain Lain Batam</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola tagihan lain lain Batam.</p>
        </div>
        @can('tagihan-service-kendaraan-batam-create')
            <a href="{{ route('tagihan-lain-lain-batam.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Tambah Tagihan</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('tagihan-lain-lain-batam.index') }}" class="mb-5 grid grid-cols-1 gap-3 rounded-lg border bg-white p-4 md:grid-cols-4">
        <input name="search" value="{{ request('search') }}" placeholder="No. tagihan, polisi, bengkel" class="rounded-lg border-gray-300 text-sm">
        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="rounded-lg border-gray-300 text-sm" aria-label="Tanggal service dari">
        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="rounded-lg border-gray-300 text-sm" aria-label="Tanggal service sampai">
        <div class="flex gap-2">
            <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
            <a href="{{ route('tagihan-lain-lain-batam.index') }}" class="rounded-lg border px-4 py-2 text-sm text-gray-700">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">No. Tagihan</th><th class="px-4 py-3">No. Polisi / KIR</th><th class="px-4 py-3">Bengkel</th><th class="px-4 py-3">Rincian Service</th><th class="px-4 py-3 text-right">Biaya</th><th class="px-4 py-3">PPh</th><th class="px-4 py-3 text-right">Total Biaya</th><th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($tagihan as $item)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3">{{ $item->tanggal_service->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $item->nomor_tagihan ?: '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $item->nomor_polisi }}</td>
                        <td class="px-4 py-3">{{ $item->bengkel }}</td>
                        <td class="max-w-sm px-4 py-3">{{ $item->rincian_service }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) $item->biaya, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ (float) $item->pph_rate == 0 ? 'Tanpa PPh' : rtrim(rtrim(number_format((float) $item->pph_rate, 1, ',', ''), '0'), ',').'%' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $item->total_biaya, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            @can('tagihan-service-kendaraan-batam-update')
                                <a href="{{ route('tagihan-lain-lain-batam.edit', $item) }}" class="font-medium text-blue-700 hover:underline">Edit</a>
                            @endcan
                            @can('tagihan-service-kendaraan-batam-delete')
                                <form action="{{ route('tagihan-lain-lain-batam.destroy', $item) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="font-medium text-red-700 hover:underline">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">Belum ada tagihan lain lain Batam.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $tagihan->links() }}</div>
</div>
@endsection
