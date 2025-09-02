@extends('layouts.app')

@section('title', 'Riwayat Tagihan Kontainer Sewa')
@section('page_title', 'Riwayat Tagihan Kontainer Sewa')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Riwayat Tagihan Kontainer Sewa</h2>

    <div class="mb-4">
        <a href="{{ route('tagihan-kontainer-sewa.index') }}" class="inline-block bg-gray-200 text-gray-800 py-2 px-4 rounded-md">&larr; Kembali</a>
    </div>

    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Awal</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Akhir</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Kontainer</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                @forelse($tagihans as $i => $t)
                    @php
                        $no = $i+1;
                        $tanggalAwal = $t->tanggal_harga_awal ? (method_exists($t->tanggal_harga_awal, 'format') ? $t->tanggal_harga_awal->format('d/m/Y') : \Carbon\Carbon::parse($t->tanggal_harga_awal)->format('d/m/Y')) : '-';
                        $tanggalAkhir = $t->tanggal_harga_akhir ? (method_exists($t->tanggal_harga_akhir, 'format') ? $t->tanggal_harga_akhir->format('d/m/Y') : \Carbon\Carbon::parse($t->tanggal_harga_akhir)->format('d/m/Y')) : '-';
                        $total = $t->group_total_master ?? $t->group_total ?? null;
                        $totalDisplay = $total ? 'Rp ' . number_format($total, 2, ',', '.') : '-';
                    @endphp
                    <tr>
                        <td class="py-4 px-6">{{ $no }}</td>
                        <td class="py-4 px-6">{{ $t->vendor }}</td>
                        <td class="py-4 px-6">{{ $tanggalAwal }}</td>
                        <td class="py-4 px-6">{{ $tanggalAkhir }}</td>
                        <td class="py-4 px-6">{{ $t->tarif }}</td>
                        <td class="py-4 px-6">{{ $t->group_container_count ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $totalDisplay }}</td>
                        <td class="py-4 px-6">{{ $t->group_code ?? '-' }}</td>
                        <td class="py-4 px-6 text-center">
                            @php
                                $routeDate = '';
                                if (!empty($t->tanggal_harga_awal)) {
                                    if (method_exists($t->tanggal_harga_awal, 'format')) {
                                        $routeDate = $t->tanggal_harga_awal->format('Y-m-d');
                                    } else {
                                        $routeDate = \Carbon\Carbon::parse($t->tanggal_harga_awal)->format('Y-m-d');
                                    }
                                }
                            @endphp
                            <div class="flex items-center justify-center space-x-3">
                                <a href="{{ route('tagihan-kontainer-sewa.group.show', ['vendor' => $t->vendor, 'tanggal' => $routeDate]) }}" class="text-indigo-600 hover:underline">Lihat</a>
                                <form action="{{ route('tagihan-kontainer-sewa.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus tagihan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-4 px-6 text-center text-gray-500">Tidak ada riwayat tagihan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
