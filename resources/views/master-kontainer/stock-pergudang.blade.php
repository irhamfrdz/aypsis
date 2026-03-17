@extends('layouts.app')

@section('title', 'Stock Kontainer per Gudang')
@section('page_title', 'Stock Kontainer per Gudang')

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-4 rounded-lg shadow-sm border">
    <h2 class="text-xl font-bold text-gray-800">Ringkasan Stock Kontainer per Gudang</h2>
</div>

<!-- Card Totals Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-xl shadow-lg text-white">
        <h3 class="text-sm font-semibold uppercase tracking-wider opacity-80">Total Kontainer Sewa</h3>
        <p class="text-3xl font-extrabold mt-2">{{ $data->sum('total_sewa') }}</p>
    </div>
    <div class="bg-gradient-to-br from-cyan-500 to-teal-500 p-6 rounded-xl shadow-lg text-white">
        <h3 class="text-sm font-semibold uppercase tracking-wider opacity-80">Total Stock Kontainer</h3>
        <p class="text-3xl font-extrabold mt-2">{{ $data->sum('total_stock') }}</p>
    </div>
    <div class="bg-gradient-to-br from-blue-600 to-indigo-800 p-6 rounded-xl shadow-lg text-white">
        <h3 class="text-sm font-semibold uppercase tracking-wider opacity-80">Total Gabungan</h3>
        <p class="text-3xl font-extrabold mt-2">{{ $data->sum('total_gabungan') }}</p>
    </div>
</div>

<div class="overflow-hidden shadow-md sm:rounded-lg border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Gudang</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lokasi</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kontainer Sewa</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock Kontainer</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-indigo-600 uppercase tracking-wider">Total Gabungan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($data as $item)
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $item['nama_gudang'] }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['lokasi'] ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-700">{{ $item['total_sewa'] }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-700">{{ $item['total_stock'] }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-indigo-600 bg-indigo-50/30">{{ $item['total_gabungan'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Tidak ada data stock per gudang tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
