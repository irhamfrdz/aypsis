@extends('layouts.app')

@section('page_title', 'Riwayat In/Out Barang Lainnya')

@section('content')
<div class="bg-white rounded-2xl shadow-sm p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-gray-100 pb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-exchange-alt mr-2 text-blue-500"></i>Riwayat Pergerakan Stok
            </h2>
            <p class="text-gray-500 text-sm mt-1">Daftar lengkap barang masuk dan barang keluar untuk kategori "Lainnya"</p>
        </div>
        <a href="{{ route('stock-ban.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-lg font-medium text-gray-600 hover:bg-gray-200 focus:outline-none transition-colors duration-200 shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 table-premium">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">TANGGAL</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">TIPE</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">NAMA BARANG</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">QTY</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">TUJUAN / LOKASI</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">PIC / PENERIMA</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">SIAPA</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">KETERANGAN</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($history as $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item->jenis_pergerakan == 'MASUK')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                <i class="fas fa-arrow-down mr-1"></i>MASUK
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                <i class="fas fa-arrow-up mr-1"></i>KELUAR
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-800">{{ $item->nama }}</div>
                        <div class="text-[10px] text-gray-400 font-semibold uppercase">ID: {{ $item->item_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black {{ $item->jenis_pergerakan == 'MASUK' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $item->jenis_pergerakan == 'MASUK' ? '+' : '-' }}{{ $item->qty }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <span class="font-medium">{{ $item->tujuan_penerima }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $item->pelaku }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium italic">
                        {{ $item->updater ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 italic max-w-xs truncate" title="{{ $item->keterangan }}">
                        {{ $item->keterangan }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 bg-gray-50/50">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-history text-5xl mb-4 text-gray-200"></i>
                            <p class="font-medium">Belum ada data pergerakan stok.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
