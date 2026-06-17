@extends('layouts.app')

@section('title', 'Daftar Pranota OB Antar Gudang')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-teal-100">
        {{-- Header --}}
        <div class="bg-teal-700 text-white px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Daftar Pranota OB Antar Gudang
                    </h5>
                    <p class="text-teal-100 text-xs mt-1">Mengelola invoice / pranota hasil pengelompokan tagihan antar gudang</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('tagihan-ob-antar-gudang.index') }}" class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Tagihan OB
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <div class="flex justify-between items-center">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="text-green-500 hover:text-green-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <div class="flex justify-between items-center">
                        <span>{{ session('error') }}</span>
                        <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Search -->
            <div class="mb-6">
                <form method="GET" action="{{ route('pranota-ob-antar-gudang.index') }}" class="flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm" 
                               placeholder="Cari nomor pranota, keterangan...">
                    </div>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('pranota-ob-antar-gudang.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition duration-150 flex items-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Pranota</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Adjustment</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Grand Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pranotas as $index => $item)
                            <tr class="hover:bg-teal-50/30 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $pranotas->firstItem() + $index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-gray-900 font-mono">{{ $item->nomor_pranota }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal_pranota)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900 text-center font-bold">
                                    <span class="inline-flex px-2 py-0.5 rounded bg-teal-50 text-teal-800 text-[11px]">
                                        {{ $item->items->count() }} kontainer
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    @if($item->adjustment != 0)
                                        <span class="{{ $item->adjustment > 0 ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                            Rp {{ number_format($item->adjustment, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-teal-900">Rp {{ number_format($item->grand_total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <form action="{{ route('pranota-ob-antar-gudang.update-status', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status pembayaran pranota ini?')">
                                        @csrf
                                        @method('PATCH')
                                        @if(($item->status_pembayaran ?? 'Belum Lunas') === 'Lunas')
                                            <button type="submit" name="status_pembayaran" value="Belum Lunas" class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition duration-150 cursor-pointer" title="Klik untuk ubah ke Belum Lunas">
                                                <i class="fas fa-check-circle mr-1"></i> Lunas
                                            </button>
                                        @else
                                            <button type="submit" name="status_pembayaran" value="Lunas" class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition duration-150 cursor-pointer" title="Klik untuk ubah ke Lunas">
                                                <i class="fas fa-times-circle mr-1"></i> Belum Lunas
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $item->creator->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium">
                                    <div class="flex justify-center space-x-1">
                                        <a href="{{ route('pranota-ob-antar-gudang.show', $item->id) }}" 
                                           class="text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 p-1.5 rounded transition duration-150" 
                                           title="Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('pranota-ob-antar-gudang.print', $item->id) }}" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-1.5 rounded transition duration-150" 
                                           title="Cetak">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>
                                        @can('pranota-ob-antar-gudang-delete')
                                            <form action="{{ route('pranota-ob-antar-gudang.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Pranota ini? Tagihan di dalamnya akan dikembalikan ke status belum masuk pranota.')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded transition duration-150" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500 text-sm">Belum ada data pranota OB Antar Gudang</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                <div class="text-xs text-gray-700">
                    Menampilkan {{ $pranotas->firstItem() ?? 0 }} - {{ $pranotas->lastItem() ?? 0 }} 
                    dari {{ $pranotas->total() }} data
                </div>
                <div class="pagination-links text-xs">
                    {{ $pranotas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
