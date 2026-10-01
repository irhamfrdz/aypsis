@extends('layouts.app')

@section('title', 'Tagihan LOLO Batam')
@section('page_title', 'Tagihan LOLO Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tagihan LOLO Batam</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola dan pantau seluruh tagihan jasa LOLO (Lift-On / Lift-Off) kontainer di Batam.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('master.pricelist-lolo-batam.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-150">
                <i class="fas fa-tags mr-2"></i>
                Master Tarif LOLO Batam
            </a>
            @can('tagihan-lolo-batam-export')
            <a href="{{ route('tagihan-lolo-batam.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-150">
                <i class="fas fa-file-excel mr-2"></i>
                Export CSV
            </a>
            @endcan
            @can('tagihan-lolo-batam-create')
            <a href="{{ route('tagihan-lolo-batam.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-150">
                <i class="fas fa-plus mr-2"></i>
                Buat Tagihan Baru
            </a>
            @endcan
        </div>
    </div>

    {{-- Alert Section --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center text-emerald-800 text-sm">
        <i class="fas fa-check-circle text-emerald-500 text-lg mr-3"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm flex items-center text-red-800 text-sm">
        <i class="fas fa-exclamation-circle text-red-500 text-lg mr-3"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tagihan</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalTagihanCount, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-1">Semua invoice LOLO</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Belum Lunas</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($countBelumLunas, 0, ',', '.') }}</h3>
                <p class="text-xs text-amber-600 font-semibold mt-1">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-clock"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lunas</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h3>
                <p class="text-xs text-emerald-600 font-medium mt-1">Telah diselesaikan</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-check-double"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Nominal</p>
                <h3 class="text-2xl font-bold text-indigo-700 mt-1">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-1">Keseluruhan tagihan</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    {{-- Search & Filter Section --}}
    <div class="mb-6 bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('tagihan-lolo-batam.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Keyword</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="No tagihan, kontainer, kapal, voyage..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pembayaran</label>
                <select name="status_pembayaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="Belum Lunas" {{ ($statusPembayaran ?? '') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="Lunas" {{ ($statusPembayaran ?? '') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Vendor</label>
                <input type="text" name="vendor" value="{{ $vendor ?? '' }}" placeholder="Filter vendor..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center shadow-sm">
                    <i class="fas fa-search mr-1.5"></i> Filter
                </button>
                <a href="{{ route('tagihan-lolo-batam.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition-colors" title="Reset Filter">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Invoices Table Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No. Tagihan</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Vendor / Depo</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kapal / Voyage</th>
                        <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Item Kontainer</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($tagihans as $tagihan)
                    <tr class="hover:bg-gray-50/75 transition-colors">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <a href="{{ route('tagihan-lolo-batam.show', $tagihan->id) }}" class="font-bold text-indigo-600 hover:text-indigo-800 text-sm flex items-center">
                                <i class="fas fa-file-invoice text-gray-400 mr-2"></i>
                                {{ $tagihan->nomor_tagihan }}
                            </a>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                            {{ $tagihan->vendor ?: '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($tagihan->kapal)
                                <div class="font-semibold text-gray-800">{{ $tagihan->kapal }}</div>
                                <div class="text-xs text-gray-400">Voy: {{ $tagihan->voyage ?: '-' }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $tagihan->items->count() }} Kontainer
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right font-bold text-emerald-700 text-sm">
                            {{ $tagihan->formatted_total_tagihan }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $tagihan->status_color }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $tagihan->status_pembayaran === 'Lunas' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ $tagihan->status_pembayaran }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('tagihan-lolo-batam.show', $tagihan->id) }}" class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('tagihan-lolo-batam-print')
                                <a href="{{ route('tagihan-lolo-batam.print', $tagihan->id) }}" target="_blank" class="p-1.5 text-purple-600 hover:text-purple-900 hover:bg-purple-50 rounded-lg transition-colors" title="Cetak Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                                @endcan
                                @can('tagihan-lolo-batam-update')
                                <a href="{{ route('tagihan-lolo-batam.edit', $tagihan->id) }}" class="p-1.5 text-amber-600 hover:text-amber-900 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('tagihan-lolo-batam-delete')
                                <form action="{{ route('tagihan-lolo-batam.destroy', $tagihan->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan LOLO ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-file-invoice text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-base font-semibold">Belum ada data Tagihan LOLO Batam.</p>
                            <p class="text-xs text-gray-400 mt-1">Klik tombol Buat Tagihan Baru untuk membuat penagihan LOLO pertama.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tagihans->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $tagihans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
