@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-3 py-2 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Detail Pranota OB</h1>
            <p class="text-xs text-gray-600">Nomor Pranota: {{ $pranota->nomor_pranota }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('pranota-ob.print', $pranota->id) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Print</a>
            <a href="{{ route('pranota-ob.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Informasi Kapal</h3>
            <div class="space-y-1">
                <p class="text-sm text-gray-600"><span class="font-medium">Nama Kapal:</span> {{ $pranota->nama_kapal }}</p>
                <p class="text-sm text-gray-600"><span class="font-medium">No Voyage:</span> {{ $pranota->no_voyage }}</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Informasi Lainnya</h3>
            <div class="space-y-1">
                <p class="text-sm text-gray-600"><span class="font-medium">Tanggal OB:</span> {{ $pranota->tanggal_ob ? \Carbon\Carbon::parse($pranota->tanggal_ob)->format('d/m/Y') : '-' }}</p>
                <p class="text-sm text-gray-600"><span class="font-medium">Nomor Accurate:</span> {{ $pranota->nomor_accurate ?? '-' }}</p>
                <p class="text-sm text-gray-600"><span class="font-medium">Pembuat:</span> {{ $pranota->creator?->nama_lengkap ?? $pranota->creator?->name ?? '-' }}</p>
                <p class="text-sm text-gray-600"><span class="font-medium">Tgl Dibuat:</span> {{ $pranota->created_at ? $pranota->created_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-900">Daftar Item ({{ count($displayItems) }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nomor Kontainer</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nama Barang</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Supir</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Size</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Biaya</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($displayItems as $i => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $i + 1 }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item['nomor_kontainer'] ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item['nama_barang'] ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item['supir'] ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item['size'] ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">
                            @if($item['biaya'])
                                Rp {{ number_format($item['biaya'], 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-gray-500">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection