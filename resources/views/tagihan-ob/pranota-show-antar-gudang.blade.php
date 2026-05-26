@extends('layouts.app')

@section('title', 'Detail Pranota OB Antar Gudang')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-teal-100">
        {{-- Header --}}
        <div class="bg-teal-700 text-white px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h5 class="text-lg font-bold flex items-center font-mono">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        {{ $pranota->nomor_pranota }}
                    </h5>
                    <p class="text-teal-100 text-xs mt-1">Detail invoice / pranota hasil pengelompokan tagihan antar gudang</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pranota-ob-antar-gudang.print', $pranota->id) }}" target="_blank" class="bg-yellow-500 hover:bg-yellow-400 text-teal-950 px-4 py-2 rounded-md text-sm font-bold transition duration-150 ease-in-out">
                        <i class="fas fa-print mr-1"></i>
                        Cetak Pranota
                    </a>
                    <a href="{{ route('pranota-ob-antar-gudang.index') }}" class="bg-teal-600 hover:bg-teal-500 text-white border border-teal-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Daftar Pranota
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- Info Cards Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                {{-- Info Utama --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Informasi Pranota</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Tanggal Pranota:</span>
                            <span class="text-xs font-semibold text-gray-900">{{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Dibuat Pada:</span>
                            <span class="text-xs font-semibold text-gray-900">{{ $pranota->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Dibuat Oleh:</span>
                            <span class="text-xs font-semibold text-gray-900">{{ $pranota->creator->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Catatan / Keterangan</h3>
                    <p class="text-xs text-gray-700 italic">
                        {{ $pranota->keterangan ?: 'Tidak ada catatan tambahan.' }}
                    </p>
                </div>

                {{-- Ringkasan Biaya --}}
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-5">
                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-3">Ringkasan Biaya</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-teal-700">Subtotal (Nominal):</span>
                            <span class="text-xs font-bold text-teal-900">Rp {{ number_format($pranota->nominal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-teal-700">Adjustment:</span>
                            <span class="text-xs font-bold {{ $pranota->adjustment >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($pranota->alasan_adjustment)
                            <div class="text-[10px] text-teal-600 italic bg-white/50 px-2 py-0.5 rounded border border-teal-100">
                                Alasan: {{ $pranota->alasan_adjustment }}
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-teal-200 pt-2">
                            <span class="text-sm font-bold text-teal-800">Grand Total:</span>
                            <span class="text-sm font-extrabold text-teal-950">Rp {{ number_format($pranota->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Kontainer --}}
            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-cubes text-teal-600 mr-2"></i>
                Daftar Kontainer Terkait ({{ $pranota->items->count() }} Item)
            </h3>
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Supir</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan Rute</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($pranota->items as $index => $item)
                            @if($item->tagihanOb)
                                <tr class="hover:bg-teal-50/10 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $item->tagihanOb->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold font-mono text-gray-900">{{ $item->tagihanOb->nomor_kontainer }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $item->tagihanOb->nama_supir }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-900">{{ $item->tagihanOb->keterangan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $item->tagihanOb->status_kontainer === 'full' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($item->tagihanOb->status_kontainer) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-right font-bold text-gray-900">
                                        Rp {{ number_format($item->tagihanOb->biaya, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-xs text-gray-400 italic">Data tagihan OB tidak ditemukan (mungkin telah dihapus)</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
