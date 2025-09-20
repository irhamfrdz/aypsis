@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 border-b pb-4">
            Daftar Tagihan CATA
        </h2>
        <div class="overflow-x-auto rounded-xl border shadow-sm">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs">
                        <tr>
                            <th class="px-4 py-3">Nomor Tagihan</th>
                            <th class="px-4 py-3">Tanggal Tagihan</th>
                            <th class="px-4 py-3">Vendor</th>
                            <th class="px-4 py-3">Total Tagihan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-[10px]">
                    @forelse ($tagihans as $tagihan)
                            <tr class="hover:bg-indigo-50 transition-colors">
                                <td class="px-4 py-3">{{ $tagihan->nomor_tagihan ?? '-' }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d/M/Y') }}</td>
                                <td class="px-4 py-3">{{ $tagihan->vendor ?? '-' }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($tagihan->total_tagihan ?? 0, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $tagihan->status == 'lunas' ? 'bg-green-100 text-green-800' : ($tagihan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($tagihan->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $tagihan->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('daftar-tagihan-cata.show', $tagihan) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded text-sm mr-2">Lihat</a>
                                    @if($tagihan->status != 'lunas')
                                        <a href="{{ route('daftar-tagihan-cata.edit', $tagihan) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white rounded text-sm">Edit</a>
                                    @endif
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada tagihan CATA.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection