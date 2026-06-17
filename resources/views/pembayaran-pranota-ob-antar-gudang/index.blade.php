@extends('layouts.app')

@section('title', 'Pembayaran Pranota OB Antar Gudang')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-teal-100">
        <!-- Header -->
        <div class="bg-teal-700 text-white px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Pembayaran Pranota OB Antar Gudang
                    </h5>
                    <p class="text-teal-100 text-xs mt-1">Mengelola pembayaran untuk pranota OB antar gudang</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pembayaran-pranota-ob-antar-gudang.create') }}"
                       class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-1"></i>
                        Buat Pembayaran Baru
                    </a>
                    <a href="{{ route('pranota-ob-antar-gudang.index') }}"
                       class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Daftar Pranota OB AG
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Alerts -->
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

            <!-- Table -->
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bank</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penyesuaian</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Grand Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pembayaranList as $index => $pembayaran)
                            <tr class="hover:bg-teal-50/30 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $pembayaranList->firstItem() + $index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-gray-900 font-mono">{{ $pembayaran->nomor_pembayaran }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $pembayaran->bank ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold {{ $pembayaran->jenis_transaksi === 'credit' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ strtoupper($pembayaran->jenis_transaksi) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    @if($pembayaran->penyesuaian != 0)
                                        <span class="{{ $pembayaran->penyesuaian > 0 ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                            Rp {{ number_format($pembayaran->penyesuaian, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-teal-900">Rp {{ number_format($pembayaran->total_setelah_penyesuaian, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $pembayaran->creator->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium">
                                    <div class="flex justify-center space-x-1">
                                        <a href="{{ route('pembayaran-pranota-ob-antar-gudang.show', $pembayaran->id) }}" 
                                           class="text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 p-1.5 rounded transition duration-150" 
                                           title="Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('pembayaran-pranota-ob-antar-gudang.edit', $pembayaran->id) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-1.5 rounded transition duration-150" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="{{ route('pembayaran-pranota-ob-antar-gudang.print', $pembayaran->id) }}" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-1.5 rounded transition duration-150" 
                                           title="Cetak">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>
                                        @can('pranota-ob-antar-gudang-delete')
                                            <form action="{{ route('pembayaran-pranota-ob-antar-gudang.destroy', $pembayaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Pembayaran ini? Status Pranota akan dikembalikan ke Belum Lunas.')" class="inline">
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
                                        <p class="text-gray-500 text-sm">Belum ada data pembayaran pranota OB Antar Gudang</p>
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
                    Menampilkan {{ $pembayaranList->firstItem() ?? 0 }} - {{ $pembayaranList->lastItem() ?? 0 }} 
                    dari {{ $pembayaranList->total() }} data
                </div>
                <div class="pagination-links text-xs">
                    {{ $pembayaranList->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
