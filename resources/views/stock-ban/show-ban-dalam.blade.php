@extends('layouts.app')

@section('page_title', 'Detail Stock Ban Dalam')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>Detail Stock Ban Dalam
                </h2>
                <div class="flex space-x-2">
                    <a href="{{ route('stock-ban-dalam.use', $stockBanDalam->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200 shadow-sm">
                        <i class="fas fa-wrench mr-2"></i>Gunakan
                    </a>
                    <a href="{{ route('stock-ban.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-lg font-medium text-gray-600 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            {{-- Info Table --}}
            <div class="mb-8">
                <h3 class="font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3">Informasi Barang</h3>
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Nama Barang</p>
                            <p class="font-bold text-gray-900 text-lg">{{ $stockBanDalam->namaStockBan->nama }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Ukuran</p>
                            <p class="font-medium text-gray-800">{{ $stockBanDalam->ukuran ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Lokasi</p>
                            <p class="font-medium text-gray-800">{{ $stockBanDalam->lokasi }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Sisa Stock Saat Ini</p>
                            <p class="font-bold text-2xl text-blue-600">{{ $stockBanDalam->qty }} <span class="text-sm font-normal text-gray-500">{{ $stockBanDalam->type }}</span></p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Tanggal Masuk</p>
                            <p class="font-medium text-gray-800">{{ $stockBanDalam->tanggal_masuk ? \Carbon\Carbon::parse($stockBanDalam->tanggal_masuk)->format('d F Y') : '-' }}</p>
                        </div>
                        <div>
                             <p class="text-gray-500 mb-1">Harga Beli</p>
                             <p class="font-medium text-gray-800">Rp {{ number_format($stockBanDalam->harga_beli, 0, ',', '.') }}</p>
                        </div>
                         @if($stockBanDalam->nomor_bukti)
                        <div>
                            <p class="text-gray-500 mb-1">Nomor Bukti / Ref</p>
                            <p class="font-medium text-gray-600">{{ $stockBanDalam->nomor_bukti }}</p>
                        </div>
                        @endif
                         @if($stockBanDalam->keterangan)
                        <div class="col-span-1 md:col-span-2 lg:col-span-3">
                            <p class="text-gray-500 mb-1">Keterangan Barang</p>
                            <p class="font-medium text-gray-600 italic">"{{ $stockBanDalam->keterangan }}"</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Usage History --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-l-4 border-green-500 pl-3">Riwayat Penggunaan</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kendaraan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Pada</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stockBanDalam->usages as $usage)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($usage->tanggal_keluar)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        {{ $usage->mobil->nomor_polisi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    {{ $usage->qty }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $usage->keterangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $usage->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 bg-gray-50">
                                    <i class="fas fa-history text-4xl mb-3 text-gray-300"></i>
                                    <p>Belum ada riwayat penggunaan untuk barang ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
