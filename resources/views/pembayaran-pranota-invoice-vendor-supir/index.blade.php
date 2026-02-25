@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pembayaran Pranota Vendor Supir</h2>
            <p class="text-sm text-gray-500">Kelola realisasi pembayaran untuk pranota invoice vendor supir</p>
        </div>
        <div>
            <a href="{{ route('pembayaran-pranota-invoice-vendor-supir.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pembayaran
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('pembayaran-pranota-invoice-vendor-supir.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Cari nomor pembayaran atau vendor...">
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->anyFilled(['search']))
                <a href="{{ route('pembayaran-pranota-invoice-vendor-supir.index') }}" class="px-4 py-2 text-rose-600 hover:text-rose-700 text-sm font-medium flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">No. Pembayaran</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Vendor</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4 text-right">Total Bayar</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pembayarans as $pembayaran)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-emerald-600">
                            {{ $pembayaran->nomor_pembayaran }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $pembayaran->tanggal_pembayaran->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $pembayaran->vendor->nama_vendor ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 uppercase">
                            {{ $pembayaran->metode_pembayaran }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="{{ route('pembayaran-pranota-invoice-vendor-supir.show', $pembayaran->id) }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            @if(auth()->user()->can('pembayaran-pranota-invoice-vendor-supir-delete'))
                            <form action="{{ route('pembayaran-pranota-invoice-vendor-supir.destroy', $pembayaran->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                            Belum ada data pembayaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pembayarans->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $pembayarans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
