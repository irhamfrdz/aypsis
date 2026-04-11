@extends('layouts.app')

@section('title', 'Daftar Pranota Ongkos Truk')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pranota Ongkos Truk</h1>
                <p class="text-gray-600 mt-1">Kelola daftar pranota ongkos truk yang telah dikirim</p>
            </div>
            <a href="{{ route('report.ongkos-truk.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                <i class="fas fa-plus mr-2"></i> Buat Melalui Laporan
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
            <form action="{{ route('pranota-ongkos-truk.index') }}" method="GET" class="flex-1 flex gap-2">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pranota atau supir..."
                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl hover:bg-gray-800 transition text-sm font-medium">
                    Filter
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-medium">No. Pranota</th>
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Supir / Vendor</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Total</th>
                        <th class="px-6 py-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pranotas as $pranota)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-gray-900">{{ $pranota->no_pranota }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pranota->tanggal_pranota->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">{{ $pranota->supir->nama_karyawan ?? $pranota->vendor->nama_vendor ?? '-' }}</span>
                                <span class="text-xs text-gray-500 font-medium">{{ $pranota->supir ? 'Supir' : ($pranota->vendor ? 'Vendor' : '-') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $pranota->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $pranota->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-black text-gray-900">
                            Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('pranota-ongkos-truk.show', $pranota->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('pranota-ongkos-truk.destroy', $pranota->id) }}" method="POST" onsubmit="return confirm('Hapus pranota ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-receipt text-gray-200 text-5xl mb-4"></i>
                                <p class="text-gray-500 font-medium">Belum ada data pranota ongkos truk.</p>
                                <p class="text-gray-400 text-sm mt-1">Silakan buat melalui menu Laporan Ongkos Truk.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $pranotas->links() }}
        </div>
    </div>
</div>
@endsection
