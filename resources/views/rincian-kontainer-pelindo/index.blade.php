@extends('layouts.app')

@section('title', 'Rincian Kontainer Pelindo')
@section('page_title', 'Rincian Kontainer Pelindo')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rincian Kontainer Pelindo</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar rincian kontainer Pelindo dari seluruh Tanda Terima.</p>
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
        <form method="GET" action="{{ route('rincian-kontainer-pelindo.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            {{-- Search Input --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cari</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kontainer, seal, atau kegiatan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            {{-- Kapal Dropdown --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama Kapal</label>
                <select name="kapal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                    <option value="">Semua Kapal</option>
                    @foreach($kapals as $k)
                        <option value="{{ $k }}" {{ $kapal == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date Filters --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            {{-- Filter Buttons --}}
            <div class="md:col-span-5 flex justify-end space-x-2 mt-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
                    Filter / Cari
                </button>
                @if($search || $kapal || $startDate || $endDate)
                <a href="{{ route('rincian-kontainer-pelindo.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200">
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
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Tanda Terima</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Seal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kegiatan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Kapal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        @can('tagihan-pelindo-delete')
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-950 text-center">
                            {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                            @if($item->tandaTerima)
                            <a href="{{ route('tanda-terima.show', $item->tanda_terima_id) }}" class="hover:underline">
                                TT-{{ $item->tanda_terima_id }}
                            </a>
                            @elseif($item->tandaTerimaTanpaSuratJalan)
                            <a href="{{ route('tanda-terima-tanpa-surat-jalan.show', $item->tanda_terima_tanpa_surat_jalan_id) }}" class="hover:underline text-teal-600">
                                TTSJ-{{ $item->tanda_terima_tanpa_surat_jalan_id }}
                            </a>
                            @elseif($item->tandaTerimaLcl)
                            <a href="{{ route('tanda-terima-lcl.show', $item->tanda_terima_lcl_id) }}" class="hover:underline text-purple-600">
                                TTLCL-{{ $item->tanda_terima_lcl_id }}
                            </a>
                            @else
                            <span class="text-gray-400">
                                @if($item->tanda_terima_id)
                                    TT-{{ $item->tanda_terima_id }}
                                @elseif($item->tanda_terima_tanpa_surat_jalan_id)
                                    TTSJ-{{ $item->tanda_terima_tanpa_surat_jalan_id }}
                                @elseif($item->tanda_terima_lcl_id)
                                    TTLCL-{{ $item->tanda_terima_lcl_id }}
                                @else
                                    -
                                @endif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ $item->nomor_kontainer }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->ukuran }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->no_seal ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $item->kegiatan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->estimasi_nama_kapal ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}
                        </td>
                        @can('tagihan-pelindo-delete')
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form action="{{ route('rincian-kontainer-pelindo.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data rincian kontainer ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                    <i class="fas fa-trash w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                        @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user() && auth()->user()->can('tagihan-pelindo-delete') ? 9 : 8 }}" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-box text-gray-300 text-4xl mb-4"></i>
                                <p class="text-sm">Tidak ada data rincian kontainer Pelindo.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
