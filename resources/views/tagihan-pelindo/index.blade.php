@extends('layouts.app')

@section('title', 'Tagihan Pelindo')
@section('page_title', 'Tagihan Pelindo')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Tagihan Pelindo</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola dan pantau seluruh invoice penagihan jasa Pelindo.</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            @can('tagihan-pelindo-create')
            <a href="{{ route('tagihan-pelindo.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Tagihan
            </a>
            @endcan
        </div>
    </div>

    {{-- Alert Section --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm flex items-center">
        <div class="flex-shrink-0">
            <i class="fas fa-check-circle text-green-500 text-lg"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm flex items-center">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Search & Filter Section --}}
    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('tagihan-pelindo.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nomor tagihan, nomor kontainer, atau kegiatan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            <div class="w-full md:w-48">
                <select name="status_pembayaran" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="Belum Lunas" {{ $status == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="Lunas" {{ $status == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    Cari
                </button>
                @if($search || $status)
                <a href="{{ route('tagihan-pelindo.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Tagihan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Tagihan</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($tagihans as $tagihan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                            <a href="{{ route('tagihan-pelindo.show', $tagihan->id) }}" class="hover:underline">
                                {{ $tagihan->nomor_tagihan }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                            Rp {{ number_format($tagihan->total_tagihan, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $tagihan->status_color }}">
                                {{ $tagihan->status_pembayaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $tagihan->tanggal_bayar ? $tagihan->tanggal_bayar->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            <div>{{ $tagihan->createdBy->username ?? '-' }}</div>
                            <div class="text-[10px] text-gray-400">{{ $tagihan->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('tagihan-pelindo.show', $tagihan->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Lihat Detail">
                                    <i class="fas fa-eye w-4 h-4"></i>
                                </a>
                                @can('tagihan-pelindo-edit')
                                <a href="{{ route('tagihan-pelindo.edit', $tagihan->id) }}" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded" title="Edit">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </a>
                                @endcan
                                @can('tagihan-pelindo-delete')
                                <form action="{{ route('tagihan-pelindo.destroy', $tagihan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tagihan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                        <i class="fas fa-trash w-4 h-4"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-invoice text-gray-300 text-4xl mb-4"></i>
                                <p class="text-sm">Tidak ada data tagihan Pelindo.</p>
                                @can('tagihan-pelindo-create')
                                <a href="{{ route('tagihan-pelindo.create') }}" class="mt-2 text-indigo-600 font-semibold hover:underline">Tambah tagihan baru</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($tagihans->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $tagihans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
