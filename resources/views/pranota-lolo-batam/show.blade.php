@extends('layouts.app')

@section('title', 'Detail Pranota LOLO Batam')
@section('page_title', 'Detail Pranota LOLO Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">
    {{-- Header Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-800">{{ $pranota->nomor_tagihan }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $pranota->status_color }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $pranota->status_pembayaran === 'Lunas' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    {{ $pranota->status_pembayaran }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Pranota Tagihan Jasa LOLO Kontainer di Wilayah Batam</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('pranota-lolo-batam.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Pranota
            </a>
            @can('pranota-lolo-batam-print')
            <a href="{{ route('pranota-lolo-batam.print', $pranota->id) }}" target="_blank" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Pranota
            </a>
            @endcan
            @can('pranota-lolo-batam-update')
            <a href="{{ route('pranota-lolo-batam.edit', $pranota->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm flex items-center text-emerald-800 text-sm">
        <i class="fas fa-check-circle text-emerald-500 text-lg mr-3"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Main Document Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        {{-- Document Header / Metadata --}}
        <div class="bg-gradient-to-r from-gray-50 to-indigo-50/30 p-6 md:p-8 border-b border-gray-100">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Tanggal Pranota</span>
                    <span class="text-sm font-bold text-gray-800 mt-1 block">
                        {{ $pranota->tanggal_tagihan ? $pranota->tanggal_tagihan->format('d F Y') : '-' }}
                    </span>
                </div>
            </div>

            @if($pranota->keterangan)
            <div class="mt-4 pt-4 border-t border-gray-200/50">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Keterangan Tambahan</span>
                <p class="text-sm text-gray-700 mt-1">{{ $pranota->keterangan }}</p>
            </div>
            @endif
        </div>

        {{-- Items Detail Table --}}
        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center">
                <i class="fas fa-boxes text-indigo-500 mr-2"></i> Rincian Kontainer LOLO ({{ $pranota->items->count() }} Kontainer)
            </h3>

            <div class="border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/75">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Size & Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sumber / Surat Jalan</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Operator</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Tarif (Rp)</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($pranota->items as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3.5 text-center text-xs text-gray-500 font-semibold">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">{{ $item->nomor_kontainer }}</span>
                                @if($item->keterangan)
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $item->keterangan }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center text-xs font-semibold text-gray-700">
                                {{ $item->size }}' <span class="text-gray-400 font-normal">({{ $item->tipe_kontainer }})</span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $item->sumber_data === 'bongkaran' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }} uppercase mr-1">
                                    {{ $item->sumber_data }}
                                </span>
                                <span class="font-medium text-gray-800">{{ $item->nomor_surat_jalan ?: '-' }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600">
                                {{ $item->operator ?: '-' }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs font-semibold text-gray-700">
                                Rp {{ number_format($item->tarif, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center text-xs font-bold text-gray-700">
                                {{ $item->jumlah }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs font-bold text-emerald-700">
                                Rp {{ number_format($item->total, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t border-gray-200">
                        <tr>
                            <td colspan="7" class="px-4 py-3.5 text-right text-xs uppercase tracking-wider text-gray-600">Grand Total Tagihan:</td>
                            <td class="px-4 py-3.5 text-right text-base text-indigo-700 font-black">
                                {{ $pranota->formatted_total_tagihan }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Footer Audit Info --}}
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between text-xs text-gray-400">
            <span>Dibuat oleh: <strong class="text-gray-600">{{ $pranota->createdBy->name ?? 'System' }}</strong> pada {{ $pranota->created_at ? $pranota->created_at->format('d/m/Y H:i') : '-' }}</span>
            @if($pranota->updatedBy)
            <span>Terakhir diperbarui oleh: <strong class="text-gray-600">{{ $pranota->updatedBy->name }}</strong></span>
            @endif
        </div>
    </div>
</div>
@endsection
