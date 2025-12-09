@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-3 py-4">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-semibold">Detail Pranota OB - {{ $pranota->nomor_pranota }}</h1>
            <p class="text-sm text-gray-600">Kapal: <strong>{{ $pranota->nama_kapal }}</strong> | Voyage: <strong>{{ $pranota->no_voyage }}</strong></p>
        </div>
        <div class="space-x-2">
            <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Kembali</a>
            <a href="/" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded">Cetak</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-600">Nomor Pranota</p>
                <p class="font-semibold">{{ $pranota->nomor_pranota }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600">Dibuat oleh</p>
                <p class="font-semibold">{{ $pranota->creator?->nama_lengkap ?? $pranota->creator?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600">Dibuat pada</p>
                <p class="font-semibold">{{ $pranota->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h3 class="text-sm font-semibold mb-2">Daftar Item</h3>
        <div class="overflow-x-auto border border-gray-100 rounded">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                    <tr>
                        <th class="px-3 py-2">No</th>
                        <th class="px-3 py-2">No. Kontainer</th>
                        <th class="px-3 py-2">Nama Barang</th>
                        <th class="px-3 py-2">Supir</th>
                        <th class="px-3 py-2">Size</th>
                        <th class="px-3 py-2">Biaya</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(is_array($pranota->items) && count($pranota->items))
                        @foreach($pranota->items as $index => $item)
                            <tr>
                                <td class="px-3 py-2 text-sm">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 text-sm font-mono">{{ $item['nomor_kontainer'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-sm">{{ $item['nama_barang'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-sm">{{ $item['supir'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-sm">{{ $item['size'] ? $item['size'] . ' Feet' : '-' }}</td>
                                <td class="px-3 py-2 text-sm">{{ isset($item['biaya']) ? ('Rp ' . number_format($item['biaya'],0,',','.')) : '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada item</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
