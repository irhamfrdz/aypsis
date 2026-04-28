@extends('layouts.app')

@section('title', 'Surat Jalan Tarik Kosong Batam')
@section('page_title', 'Surat Jalan Tarik Kosong Batam')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Surat Jalan Tarik Kosong Batam</h2>
        @can('surat-jalan-tarik-kosong-batam-create')
        <a href="{{ route('surat-jalan-tarik-kosong-batam.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i> Tambah Surat Jalan
        </a>
        @endcan
    </div>

    <!-- Form Filter -->
    <form action="{{ route('surat-jalan-tarik-kosong-batam.index') }}" method="GET" class="mb-6 p-4 border rounded-lg bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                <input type="text" name="search" id="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ request('search') }}" placeholder="No. SJ, Kontainer, Supir...">
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ request('start_date') }}">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ request('end_date') }}">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($items as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $item->no_surat_jalan }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->tanggal_surat_jalan->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->no_kontainer ?? '-' }} ({{ $item->size ?? '' }} FT)
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->supir ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->no_plat ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClasses = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'active' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ strtoupper($item->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('surat-jalan-tarik-kosong-batam.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('surat-jalan-tarik-kosong-batam-update')
                        <a href="{{ route('surat-jalan-tarik-kosong-batam.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endcan
                        <a href="{{ route('surat-jalan-tarik-kosong-batam.print', $item->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 mr-3">
                            <i class="fas fa-print"></i>
                        </a>
                        @can('surat-jalan-tarik-kosong-batam-delete')
                        <form action="{{ route('surat-jalan-tarik-kosong-batam.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        Belum ada data.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
