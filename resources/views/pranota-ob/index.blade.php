@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-3 py-2 overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Pranota OB</h1>
            <p class="text-xs text-gray-600">Daftar pranota OB (Order Bongkaran/Naik Kapal)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-600">Total Pranota</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-600">Bulan Ini</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <form method="GET" action="{{ route('pranota-ob.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pranota, kapal, voyage, atau nomor kontainer..." class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filter</button>
                    <a href="{{ route('pranota-ob.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nomor Pranota</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Kapal / Voyage</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah Item</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Pembuat</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Tgl Dibuat</th>
                        <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pranotas as $i => $pranota)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $pranotas->firstItem() + $i }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $pranota->nomor_pranota }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $pranota->nama_kapal }} / {{ $pranota->no_voyage }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900 text-center">
                            @php
                                $itemsCount = ($pranota->itemsPivot && $pranota->itemsPivot->count()) ? $pranota->itemsPivot->count() : (is_array($pranota->items) ? count($pranota->items) : 0);
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $itemsCount }}</span>
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $pranota->creator?->nama_lengkap ?? $pranota->creator?->name ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $pranota->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-2 text-center text-sm space-x-2">
                            <a href="{{ route('pranota-ob.show', $pranota) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                            <a href="{{ route('pranota-ob.print', $pranota->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900">Cetak</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-gray-500">Belum ada pranota OB</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
            {{ $pranotas->links() }}
        </div>
    </div>
</div>
@endsection
