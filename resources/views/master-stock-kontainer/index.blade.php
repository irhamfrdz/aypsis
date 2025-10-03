@extends('layouts.app')

@section('title','Master Stock Kontainer')
@section('page_title','Master Stock Kontainer')

@section('content')

<h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Stock Kontainer</h2>

<div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-4">
        <!-- Filter Status -->
        <select id="status-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
            <option value="">Semua Status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Disewa</option>
            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
            <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Rusak</option>
        </select>



        <!-- Search -->
        <div class="relative">
            <input type="text" id="search-input" placeholder="Cari nomor kontainer..." value="{{ request('search') }}"
                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm pl-4 pr-10">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <a href="{{ route('master.stock-kontainer.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Tambah Stock Kontainer
    </a>
</div>

@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
    {{session('success')}}
</div>
@endif

{{-- Rows Per Page Selection --}}
@include('components.rows-per-page', [
    'routeName' => 'master.stock-kontainer.index',
    'paginator' => $stockKontainers,
    'entityName' => 'stock kontainer',
    'entityNamePlural' => 'stock kontainer'
])

<div class="overflow-x-auto shadow-md sm:rounded-lg table-container">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
            <tr>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nomor Kontainer
                </th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ukuran
                </th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipe
                </th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($stockKontainers as $stockKontainer)
            <tr>
                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm font-medium text-gray-900">{{ $stockKontainer->nomor_kontainer }}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{ $stockKontainer->ukuran ?? '-' }}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-500">{{ $stockKontainer->tipe_kontainer ?? '-' }}</div>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stockKontainer->status_badge }}">
                        @switch($stockKontainer->status)
                            @case('available')
                                Tersedia
                                @break
                            @case('rented')
                                Disewa
                                @break
                            @case('maintenance')
                                Perbaikan
                                @break
                            @case('damaged')
                                Rusak
                                @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', $stockKontainer->status)) }}
                        @endswitch
                    </span>
                </td>

                <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-3 text-[10px]">
                        <a href="{{ route('master.stock-kontainer.show', $stockKontainer) }}" class="text-green-600 hover:text-green-800 hover:underline font-medium" title="Lihat Detail">Detail</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('master.stock-kontainer.edit', $stockKontainer) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium" title="Edit Data">Edit</a>
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('master.stock-kontainer.destroy', $stockKontainer) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah anda yakin ingin menghapus stock kontainer ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0" title="Hapus Data">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada data stock kontainer.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($stockKontainers->hasPages())
<div class="mt-4">
    {{ $stockKontainers->appends(request()->query())->links() }}
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-input');

    function updateFilters() {
        const params = new URLSearchParams(window.location.search);

        if (statusFilter.value) {
            params.set('status', statusFilter.value);
        } else {
            params.delete('status');
        }

        if (searchInput.value) {
            params.set('search', searchInput.value);
        } else {
            params.delete('search');
        }

        window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    }

    statusFilter.addEventListener('change', updateFilters);

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            updateFilters();
        }
    });
});
</script>

@endsection
