@extends('layouts.app')

@section('title', 'Detail Pembayaran Pranota Rit Kenek')
@section('page_title', 'Detail Pembayaran Pranota Rit Kenek')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
        <!-- Header -->
        <div class="bg-green-700 px-6 py-4 flex justify-between items-center text-white">
            <div>
                <h3 class="text-lg font-bold">Pembayaran Rit Kenek: {{ $item->nomor_pembayaran }}</h3>
                <p class="text-xs opacity-80">Tanggal Kas: {{ $item->tanggal_pembayaran ? $item->tanggal_pembayaran->format('d F Y') : '-' }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 bg-green-500 rounded-full text-xs font-bold uppercase">{{ $item->status_pembayaran }}</span>
            </div>
        </div>

        <div class="p-6">
            <!-- Grid Informasi Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-sm text-gray-500 font-medium">Nomor Accurate:</span>
                        <span class="text-sm font-semibold">{{ $item->nomor_accurate ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-sm text-gray-500 font-medium">Bank/Kas:</span>
                        <span class="text-sm font-semibold">{{ $item->bank }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-sm text-gray-500 font-medium">Jenis Transaksi:</span>
                        <span class="text-sm font-semibold">{{ $item->jenis_transaksi }}</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2 text-green-600">
                        <span class="text-sm font-medium">Total Tagihan:</span>
                        <span class="text-sm font-bold">Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 text-red-600">
                        <span class="text-sm font-medium">Penyesuaian:</span>
                        <span class="text-sm font-bold">Rp {{ number_format($item->total_tagihan_penyesuaian, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 bg-green-50 p-1 rounded">
                        <span class="text-sm font-bold text-gray-800">TOTAL AKHIR:</span>
                        <span class="text-lg font-black text-green-700">Rp {{ number_format($item->total_tagihan_setelah_penyesuaian, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="mb-8">
                <h4 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Keterangan & Alasan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded border border-gray-100 italic text-sm text-gray-600">
                        <strong>Alasan Penyesuaian:</strong><br>
                        {{ $item->alasan_penyesuaian ?? 'Tidak ada alasan penyesuaian.' }}
                    </div>
                    <div class="bg-gray-50 p-3 rounded border border-gray-100 text-sm text-gray-600">
                        <strong>Keterangan:</strong><br>
                        {{ $item->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                    </div>
                </div>
            </div>

            <!-- Daftar Pranota Terkait -->
            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Pranota yang Dibayar</h4>
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">No. Pranota</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Kenek</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-600 uppercase">Subtotal Item</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-100 bg-white">
                            @foreach($item->pranotaUangRitKeneks as $pranota)
                            <tr>
                                <td class="px-4 py-2 text-sm font-medium">{{ $pranota->no_pranota }}</td>
                                <td class="px-4 py-2 text-sm">{{ $pranota->kenek_nama ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-right font-bold">Rp {{ number_format($pranota->pivot->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer / Action Buttons -->
        <div class="bg-gray-50 px-6 py-4 flex justify-between border-t border-gray-200">
            <a href="{{ route('pembayaran-pranota-rit-kenek.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat
            </a>
            <div class="space-x-2">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i> Cetak Bukti
                </button>
                @can('pembayaran-pranota-rit-kenek-delete')
                <form action="{{ route('pembayaran-pranota-rit-kenek.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pembayaran ini? Status pranota akan kembali menjadi approved.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
