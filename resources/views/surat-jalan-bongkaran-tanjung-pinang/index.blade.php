@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran Tanjung Pinang')

@section('content')
<div class="p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Surat Jalan Bongkaran Tanjung Pinang</h1>
        @can('surat-jalan-bongkaran-tanjung-pinang-create')
            <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.create') }}" class="rounded bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">Tambah Surat Jalan</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4 flex gap-2">
        <input type="search" name="search" value="{{ $search }}" placeholder="Cari nomor SJ, BL, kontainer, atau supir" class="w-full max-w-md rounded border-gray-300 text-sm">
        <button class="rounded bg-gray-800 px-4 py-2 text-sm text-white">Cari</button>
        @if($search !== '')
            <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="rounded border px-4 py-2 text-sm">Reset</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-gray-700">
                <tr>
                    <th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Nomor Surat Jalan</th><th class="px-4 py-3">Kapal / Voyage</th>
                    <th class="px-4 py-3">BL / Kontainer</th><th class="px-4 py-3">Supir</th><th class="px-4 py-3">Tujuan</th><th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($suratJalans as $suratJalan)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $suratJalan->tanggal_surat_jalan?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $suratJalan->nomor_surat_jalan }}</td>
                        <td class="px-4 py-3">{{ $suratJalan->nama_kapal ?: '-' }} / {{ $suratJalan->no_voyage ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $suratJalan->no_bl ?: '-' }} / {{ $suratJalan->no_kontainer ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $suratJalan->supir ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $suratJalan->tujuan_pengiriman ?: '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.print', $suratJalan->id) }}" target="_blank" class="mr-2 text-teal-700 hover:underline">Cetak</a>
                            @can('surat-jalan-bongkaran-tanjung-pinang-update')
                                <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.edit', $suratJalan->id) }}" class="mr-2 text-blue-700 hover:underline">Ubah</a>
                            @endcan
                            @can('surat-jalan-bongkaran-tanjung-pinang-delete')
                                <form method="POST" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.destroy', $suratJalan->id) }}" class="inline" onsubmit="return confirm('Hapus surat jalan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 hover:underline">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada surat jalan bongkaran Tanjung Pinang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $suratJalans->links() }}</div>
</div>
@endsection
