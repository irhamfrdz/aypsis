@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Master Penerima')
@section('page_title', 'Master Penerima')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Master Penerima</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola data penerima dalam sistem</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Buttons omitted for now until features implemented --}}
                    <a href="{{ route('penerima.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Penerima
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('penerima.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Penerima
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari berdasarkan kode, nama penerima, atau catatan..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('penerima.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Penerima</h3>
                <p class="mt-1 text-sm text-gray-600">Total: {{ $penerimas->total() }} penerima</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 resizable-table" id="masterPenerimaTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Kode<div class="resize-handle"></div></th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nama Penerima<div class="resize-handle"></div></th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Alamat<div class="resize-handle"></div></th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">NPWP / NITKU<div class="resize-handle"></div></th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Catatan<div class="resize-handle"></div></th>
                            <th class="resizable-th px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status<div class="resize-handle"></div></th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs">
                        @forelse ($penerimas as $penerima)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $penerima->kode }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 font-medium">{{ $penerima->nama_penerima }}</td>
                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $penerima->alamat }}">{{ $penerima->alamat ?: '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                    <div class="flex flex-col">
                                        <span>{{ $penerima->npwp ?: '-' }}</span>
                                        <span class="text-xs text-gray-500">{{ $penerima->nitku ?: '' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $penerima->catatan }}">{{ $penerima->catatan ?: '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $penerima->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $penerima->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('penerima.edit', $penerima) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <span class="text-gray-300 text-xs">|</span>
                                        <form action="{{ route('penerima.destroy', $penerima) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penerima ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-xs text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-5v2m0 0v2m0-2h2m-2 0h-2"></path>
                                        </svg>
                                        <p class="text-gray-500 text-xs font-medium">Belum ada data penerima</p>
                                        <p class="text-gray-400 text-xs mt-1">Tambah penerima pertama untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $penerimas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
