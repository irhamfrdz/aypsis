@extends('layouts.app')

@section('title', 'Detail Pembayaran Pranota OB Antar Gudang')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-teal-100">
        <!-- Header -->
        <div class="bg-teal-700 text-white px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h5 class="text-lg font-bold flex items-center font-mono">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        {{ $pembayaran->nomor_pembayaran }}
                    </h5>
                    <p class="text-teal-100 text-xs mt-1">Detail record pembayaran pranota OB antar gudang</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pembayaran-pranota-ob-antar-gudang.print', $pembayaran->id) }}" target="_blank" class="bg-yellow-500 hover:bg-yellow-400 text-teal-950 px-4 py-2 rounded-md text-sm font-bold transition duration-150 ease-in-out">
                        <i class="fas fa-print mr-1"></i>
                        Cetak Pembayaran
                    </a>
                    <a href="{{ route('pembayaran-pranota-ob-antar-gudang.index') }}" class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Daftar Pembayaran
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- Info Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Info Utama --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Informasi Pembayaran</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Nomor Accurate:</span>
                            <span class="text-xs font-semibold text-gray-900 font-mono">{{ $pembayaran->nomor_accurate ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Tanggal Kas:</span>
                            <span class="text-xs font-semibold text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Akun Bank:</span>
                            <span class="text-xs font-semibold text-gray-900">{{ $pembayaran->akunBank->nama_akun ?? ($pembayaran->bank ?? '-') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Akun Biaya:</span>
                            <span class="text-xs font-semibold text-gray-900">{{ $pembayaran->akunCoa->nama_akun ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Dibuat Pada:</span>
                            <span class="text-xs font-semibold text-gray-900">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Dibuat Oleh:</span>
                            <span class="text-xs font-semibold text-gray-900">{{ $pembayaran->creator->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan Biaya --}}
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-5">
                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-3">Ringkasan Nominal</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-teal-700">Subtotal Tagihan:</span>
                            <span class="text-xs font-bold text-teal-900">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-teal-700">Penyesuaian (Adjustment):</span>
                            <span class="text-xs font-bold {{ $pembayaran->penyesuaian >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($pembayaran->penyesuaian, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($pembayaran->alasan_penyesuaian)
                            <div class="text-[10px] text-teal-600 italic bg-white/50 px-2 py-0.5 rounded border border-teal-100">
                                Alasan: {{ $pembayaran->alasan_penyesuaian }}
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-teal-200 pt-2">
                            <span class="text-sm font-bold text-teal-800">Grand Total Bayar:</span>
                            <span class="text-sm font-extrabold text-teal-950">Rp {{ number_format($pembayaran->total_setelah_penyesuaian, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-8">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Catatan / Keterangan</h3>
                <p class="text-xs text-gray-700 italic">
                    {{ $pembayaran->keterangan ?: 'Tidak ada catatan tambahan.' }}
                </p>
            </div>

            {{-- Table Pranota Terkait --}}
            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-file-invoice-dollar text-teal-600 mr-2"></i>
                Daftar Pranota OB Antar Gudang yang Dibayar
            </h3>
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg mb-8">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. Pranota</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Item</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $pranotas = $pembayaran->pranota_ob_antar_gudangs; @endphp
                        @forelse ($pranotas as $index => $item)
                            <tr class="hover:bg-teal-50/10">
                                <td class="px-6 py-4 text-xs text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-xs font-semibold font-mono text-gray-950">
                                    <a href="{{ route('pranota-ob-antar-gudang.show', $item->id) }}" class="text-teal-600 hover:text-teal-800 underline">
                                        {{ $item->nomor_pranota }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal_pranota)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-xs text-center text-gray-900 font-bold">{{ $item->items->count() }} kontainer</td>
                                <td class="px-6 py-4 text-xs text-right font-bold text-gray-900">Rp {{ number_format($item->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-xs text-center text-gray-400 italic">Data pranota tidak ditemukan (mungkin telah dihapus)</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
