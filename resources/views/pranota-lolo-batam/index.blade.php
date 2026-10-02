@extends('layouts.app')

@section('title', 'Daftar Pranota LOLO Batam')
@section('page_title', 'Daftar Pranota LOLO Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-file-invoice text-indigo-600 mr-3"></i>
                Daftar Pranota LOLO Batam
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Data faktur dan pranota tagihan LOLO (Lift-On / Lift-Off) kontainer di Batam
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('tagihan-lolo-batam-view')
            <a href="{{ route('tagihan-lolo-batam.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-xl border border-indigo-200 shadow-sm transition-all duration-150">
                <i class="fas fa-cubes mr-2 text-indigo-500"></i> Kontainer LOLO Batam
            </a>
            @endcan

            @can('pranota-lolo-batam-export')
            <a href="{{ route('pranota-lolo-batam.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150">
                <i class="fas fa-file-excel mr-2"></i> Export CSV
            </a>
            @endcan
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-sm flex items-center shadow-sm">
        <i class="fas fa-check-circle text-emerald-500 text-lg mr-3"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-800 text-sm flex items-center shadow-sm">
        <i class="fas fa-exclamation-circle text-red-500 text-lg mr-3"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif


    {{-- Filter Section --}}
    <div class="mb-6 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('pranota-lolo-batam.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-600 mb-1">Cari Keyword</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="No pranota, vendor, tanggal..." class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Status Pembayaran</label>
                <select name="status_pembayaran" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="Belum Lunas" {{ ($statusPembayaran ?? '') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="Lunas" {{ ($statusPembayaran ?? '') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors flex items-center justify-center shadow-sm">
                    <i class="fas fa-search mr-1.5"></i> Filter
                </button>
                <a href="{{ route('pranota-lolo-batam.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm transition-colors" title="Reset Filter">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Pranota Invoices Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No. Pranota</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($pranotas as $p)
                    <tr class="hover:bg-gray-50/75 transition-colors">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <a href="{{ route('pranota-lolo-batam.show', $p->id) }}" class="font-bold text-indigo-600 hover:text-indigo-800 text-sm flex items-center">
                                <i class="fas fa-file-invoice text-indigo-400 mr-2"></i>
                                {{ $p->nomor_tagihan }}
                            </a>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $p->tanggal_tagihan ? $p->tanggal_tagihan->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right font-bold text-emerald-700 text-sm">
                            {{ $p->formatted_total_tagihan }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $p->status_color }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $p->status_pembayaran === 'Lunas' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ $p->status_pembayaran }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('pranota-lolo-batam.show', $p->id) }}" class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('tagihan-lolo-batam-print')
                                <a href="{{ route('pranota-lolo-batam.print', $p->id) }}" target="_blank" class="p-1.5 text-purple-600 hover:text-purple-900 hover:bg-purple-50 rounded-lg transition-colors" title="Cetak Pranota">
                                    <i class="fas fa-print"></i>
                                </a>
                                @endcan
                                @can('tagihan-lolo-batam-update')
                                <a href="{{ route('pranota-lolo-batam.edit', $p->id) }}" class="p-1.5 text-amber-600 hover:text-amber-900 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('tagihan-lolo-batam-delete')
                                <form action="{{ route('pranota-lolo-batam.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota {{ $p->nomor_tagihan }}?');">
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-file-invoice text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-base font-semibold">Belum ada data Pranota LOLO Batam.</p>
                            <p class="text-xs text-gray-400 mt-1">Penerbitan pranota dapat dilakukan dari menu Kontainer LOLO Batam.</p>
                            <a href="{{ route('tagihan-lolo-batam.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all">
                                <i class="fas fa-plus mr-1.5"></i> Terbitkan Pranota dari Kontainer LOLO
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pranotas->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $pranotas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
