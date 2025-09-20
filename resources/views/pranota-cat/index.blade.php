@extends('layouts.app')

@section('title', 'Daftar Pranota CAT')
@section('page_title', 'Daftar Pranota CAT')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <!-- Form Filter -->
    <form action="{{ route('pranota-cat.index') }}" method="GET" class="mb-6 p-4 border rounded-lg bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Cari Nomor Pranota / Vendor</label>
                <input type="text" name="search" id="search" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" value="{{ $search ?? '' }}" placeholder="Masukkan kata kunci...">
            </div>
            <div>
                <label for="status_pembayaran" class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                <select name="status_pembayaran" id="status_pembayaran" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5">
                    <option value="">Semua Status</option>
                    <option value="Belum Lunas" {{ ($status_pembayaran ?? '') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="Lunas" {{ ($status_pembayaran ?? '') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex justify-center py-2.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full">
                    Filter
                </button>
                <a href="{{ route('pranota-cat.index') }}" class="inline-flex justify-center py-2.5 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Tabel Daftar Pranota CAT -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pranota</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Perbaikan</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($pranotas as $pranota)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pranota->nomor_pranota }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pranota->vendor ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pranota->jenis_perbaikan ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($pranota->total_biaya ?? 0, 2, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $pranota->status_pembayaran == 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $pranota->status_pembayaran ?? 'Belum Lunas' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('pranota-cat.show', $pranota) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Lihat</a>
                        @if($pranota->status_pembayaran != 'Lunas')
                            <a href="{{ route('pranota-cat.edit', $pranota) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                        @endif
                        <a href="{{ route('pranota-cat.print', $pranota) }}" target="_blank" class="text-green-600 hover:text-green-900">Cetak</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada pranota CAT.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pranotas->hasPages())
    <div class="mt-6">
        {{ $pranotas->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection