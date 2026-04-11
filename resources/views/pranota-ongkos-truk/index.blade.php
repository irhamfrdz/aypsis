@extends('layouts.app')

@section('title', 'Pranota Ongkos Truk')
@section('page_title', 'Daftar Pranota Ongkos Truk')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header & Search -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-gray-50/50 to-white">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">List Pranota</h3>
                    <p class="text-xs text-gray-500 font-medium">Kelola dan pantau semua pranota ongkos truk</p>
                </div>
            </div>
            
            <form action="{{ route('pranota-ongkos-truk.index') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari No. Pranota..." 
                           class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm font-medium w-64">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-sm font-bold text-sm">
                    Filter
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 uppercase tracking-widest text-[10px] font-bold text-gray-500">
                        <th class="px-6 py-4 text-left">No. Pranota</th>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-right">Adjustment</th>
                        <th class="px-6 py-4 text-right">Total Nominal</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pranotas as $pranota)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-blue-600 group-hover:underline">{{ $pranota->no_pranota }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600 font-medium">{{ $pranota->tanggal_pranota->format('d/M/Y') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <span class="{{ $pranota->adjustment < 0 ? 'text-red-500' : ($pranota->adjustment > 0 ? 'text-green-500' : 'text-gray-400') }}">
                                    Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-900">
                                Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-wider">
                                    {{ $pranota->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pranota-ongkos-truk.show', $pranota->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('pranota-ongkos-truk-delete')
                                    <form action="{{ route('pranota-ongkos-truk.destroy', $pranota->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-gray-200 text-6xl mb-4"></i>
                                    <p class="text-gray-500 font-medium">Belum ada data pranota yang tersimpan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
            {{ $pranotas->links() }}
        </div>
    </div>
</div>
@endsection
