@extends('layouts.app')


@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Daftar Pranota Supir')
@section('page_title', 'Daftar Pranota Supir')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div></div>
    <a href="{{ route('pranota-supir.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
        </svg>
        Tambah Pranota Supir
    </a>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <!-- Form Filter -->
    <form action="{{ route('pranota-supir.index') }}" method="GET" class="mb-6 p-4 border rounded-lg bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Cari Nomor Pranota / Nama Supir</label>
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
                <a href="{{ route('pranota-supir.index') }}" class="inline-flex justify-center py-2.5 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Filter Tanggal untuk Print -->
    <div class="mb-6 bg-white p-4 rounded-lg border border-indigo-200 shadow-sm">
        <h3 class="text-lg font-semibold text-indigo-800 mb-3">Print Berdasarkan Tanggal</h3>
        <form action="{{ route('pranota-supir.print.by-date') }}" method="GET" target="_blank" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}"
                       class="border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}"
                       class="border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow">
                    🖨️ Print Berdasarkan Tanggal
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Pranota -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pranota</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($pranotas as $pranota)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $pranota->nomor_pranota }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($pranota->permohonans->isNotEmpty())
                            @php
                                $supirs = $pranota->permohonans->pluck('supir')->filter()->unique('id');
                            @endphp
                            @if ($supirs->isNotEmpty())
                                <div class="text-sm text-gray-900">
                                    @foreach ($supirs as $supir)
                                        <div>{{ $supir->nama_lengkap ?? $supir->nama_panggilan }}</div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-sm text-gray-500">-</div>
                            @endif
                        @else
                            <div class="text-sm text-gray-500">-</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm text-gray-900">Rp {{ number_format($pranota->total_biaya_pranota, 2, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($pranota->status_pembayaran == 'Lunas')
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-300">
                                Lunas
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">
                                Belum Lunas
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('pranota-supir.show', $pranota->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('pranota-supir.print', $pranota->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900">Cetak</a>
                        @can('audit-log-view')
                            <span class="text-gray-300">|</span>
                            <button type="button"
                                    class="audit-log-btn text-purple-600 hover:text-purple-900 bg-transparent border-none cursor-pointer"
                                    data-model-type="App\Models\PranotaSupir"
                                    data-model-id="{{ $pranota->id }}"
                                    data-item-name="{{ $pranota->nomor_pranota }}"
                                    title="Lihat Riwayat">
                                Riwayat
                            </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        Tidak ada data pranota supir yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $pranotas->links() }}
    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
