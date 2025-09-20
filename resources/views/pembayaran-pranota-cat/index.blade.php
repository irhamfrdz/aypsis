@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 border-b pb-4">
            Daftar Pembayaran Pranota CAT
        </h2>
        <div class="overflow-x-auto rounded-xl border shadow-sm">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs">
                        <tr>
                            <th class="px-4 py-3">Nomor Pembayaran</th>
                            <th class="px-4 py-3">Tanggal Pembayaran</th>
                            <th class="px-4 py-3">Nomor Pranota</th>
                            <th class="px-4 py-3">Vendor</th>
                            <th class="px-4 py-3">Total Pembayaran</th>
                            <th class="px-4 py-3">Bank</th>
                            <th class="px-4 py-3">Jenis Transaksi</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-[10px]">
                    @forelse ($pembayarans as $pembayaran)
                            <tr class="hover:bg-indigo-50 transition-colors">
                                <td class="px-4 py-3">{{ $pembayaran->nomor_pembayaran ?? '-' }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/M/Y') }}</td>
                                <td class="px-4 py-3">
                                    @foreach ($pembayaran->pranotas as $pranota)
                                        <div>{{ $pranota->nomor_pranota }}</div>
                                        <div class="text-xs text-gray-500">{{ $pranota->vendor ?? '-' }}</div>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">{{ $pembayaran->vendor ?? '-' }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($pembayaran->total_pembayaran, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $pembayaran->bank ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $pembayaran->jenis_transaksi ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $pembayaran->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('pembayaran-pranota-cat.print', $pembayaran) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded text-sm">Cetak</a>
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-4 text-center text-gray-500">Belum ada pembayaran pranota CAT yang dilakukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection