@extends('layouts.app')

@section('title', 'Detail Tagihan LOLO Batam')
@section('page_title', 'Detail Tagihan LOLO Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">
    {{-- Header Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-800">{{ $tagihanLoloBatam->nomor_tagihan }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $tagihanLoloBatam->status_color }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $tagihanLoloBatam->status_pembayaran === 'Lunas' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    {{ $tagihanLoloBatam->status_pembayaran }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Invoice Tagihan Jasa LOLO Kontainer di Wilayah Batam</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tagihan-lolo-batam.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            @can('tagihan-lolo-batam-print')
            <a href="{{ route('tagihan-lolo-batam.print', $tagihanLoloBatam->id) }}" target="_blank" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Invoice
            </a>
            @endcan
            @can('tagihan-lolo-batam-update')
            <a href="{{ route('tagihan-lolo-batam.edit', $tagihanLoloBatam->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center text-emerald-800 text-sm">
        <i class="fas fa-check-circle text-emerald-500 text-lg mr-3"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Main Document Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        {{-- Document Header / Metadata --}}
        <div class="bg-gradient-to-r from-gray-50 to-indigo-50/30 p-6 md:p-8 border-b border-gray-100">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Tanggal Tagihan</span>
                    <span class="text-sm font-bold text-gray-800 mt-1 block">
                        {{ $tagihanLoloBatam->tanggal_tagihan ? $tagihanLoloBatam->tanggal_tagihan->format('d F Y') : '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Vendor / Depo</span>
                    <span class="text-sm font-bold text-gray-800 mt-1 block">
                        {{ $tagihanLoloBatam->vendor ?: '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Operator LOLO</span>
                    <span class="text-sm font-bold text-gray-800 mt-1 block">
                        @if($tagihanLoloBatam->tipe_operator === 'AYP')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                <i class="fas fa-user-tie mr-1 text-blue-600"></i> AYP: {{ $tagihanLoloBatam->operator ?: ($tagihanLoloBatam->operatorKaryawan->nama_lengkap ?? '-') }}
                            </span>
                        @elseif($tagihanLoloBatam->tipe_operator === 'VENDOR')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                <i class="fas fa-building mr-1 text-purple-600"></i> Vendor: {{ $tagihanLoloBatam->operator ?: ($tagihanLoloBatam->vendor ?: '-') }}
                            </span>
                        @elseif($tagihanLoloBatam->tipe_operator === 'CAMPURAN')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                <i class="fas fa-layer-group mr-1 text-indigo-600"></i> Campuran (Lihat Rincian Kontainer)
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Kapal & Voyage</span>
                    <span class="text-sm font-bold text-gray-800 mt-1 block">
                        {{ $tagihanLoloBatam->kapal ?: '-' }} {{ $tagihanLoloBatam->voyage ? '(' . $tagihanLoloBatam->voyage . ')' : '' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Tanggal Bayar</span>
                    <span class="text-sm font-bold {{ $tagihanLoloBatam->tanggal_bayar ? 'text-emerald-700' : 'text-gray-400' }} mt-1 block">
                        {{ $tagihanLoloBatam->tanggal_bayar ? $tagihanLoloBatam->tanggal_bayar->format('d F Y') : 'Belum Dibayar' }}
                    </span>
                </div>
            </div>

            @if($tagihanLoloBatam->keterangan)
            <div class="mt-4 pt-4 border-t border-gray-200/50">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Keterangan Tambahan</span>
                <p class="text-sm text-gray-700 mt-1">{{ $tagihanLoloBatam->keterangan }}</p>
            </div>
            @endif
        </div>

        {{-- Items Detail Table --}}
        <div class="p-6 md:p-8">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center">
                <i class="fas fa-list-ol text-indigo-500 mr-2"></i> Rincian Kontainer LOLO Batam
            </h2>

            <div class="border rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/75">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-12">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No. Kontainer</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-20">Size</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-24">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sumber / Dokumen</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase min-w-[170px]">Operator</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kegiatan</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase w-32">Tarif (Rp)</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-16">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase w-36">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($tagihanLoloBatam->items as $idx => $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3.5 text-center text-sm text-gray-500 font-medium">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3.5 text-sm font-bold text-gray-900">{{ $item->nomor_kontainer }}</td>
                            <td class="px-4 py-3.5 text-center text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    {{ $item->size ?: '20' }}'
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->tipe_kontainer === 'FULL' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $item->tipe_kontainer ?: 'FULL' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-600">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold mr-1.5 {{ $item->sumber_data === 'bongkaran' ? 'bg-blue-50 text-blue-700' : ($item->sumber_data === 'langsir' ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst($item->sumber_data) }}
                                </span>
                                <span class="text-xs">{{ $item->nomor_surat_jalan ?: '-' }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-sm">
                                @if($item->tipe_operator === 'AYP')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fas fa-user-tie mr-1 text-blue-500"></i> AYP: {{ $item->operator ?: ($item->operatorKaryawan->nama_lengkap ?? 'Operator AYP') }}
                                    </span>
                                @elseif($item->tipe_operator === 'VENDOR')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i class="fas fa-building mr-1 text-purple-500"></i> Vendor: {{ $item->operator ?: 'Vendor' }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">{{ $item->operator ?: '-' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">
                                {{ $item->kegiatan ?: 'LOLO Batam' }}
                            </td>
                            <td class="px-4 py-3.5 text-sm text-right font-medium text-gray-800">
                                {{ $item->formatted_tarif }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-sm font-medium text-gray-700">
                                {{ $item->jumlah }}
                            </td>
                            <td class="px-4 py-3.5 text-sm text-right font-bold text-emerald-700">
                                {{ $item->formatted_total }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/75 border-t-2 border-gray-200">
                        <tr>
                            <td colspan="8" class="px-4 py-3.5 text-right font-bold text-gray-700 text-sm">TOTAL ITEM & TAGIHAN:</td>
                            <td class="px-4 py-3.5 text-center font-bold text-gray-800 text-sm">{{ $tagihanLoloBatam->items->sum('jumlah') }}</td>
                            <td class="px-4 py-3.5 text-right font-bold text-emerald-800 text-base">{{ $tagihanLoloBatam->formatted_total_tagihan }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Audit Trail Footer --}}
        <div class="bg-gray-50/50 px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between text-xs text-gray-400 gap-2">
            <div>
                Dibuat oleh: <span class="text-gray-600 font-semibold">{{ $tagihanLoloBatam->createdBy ? ($tagihanLoloBatam->createdBy->name ?? $tagihanLoloBatam->createdBy->username) : 'Sistem' }}</span>
                pada {{ $tagihanLoloBatam->created_at ? $tagihanLoloBatam->created_at->format('d/m/Y H:i') : '-' }}
            </div>
            @if($tagihanLoloBatam->updated_at && $tagihanLoloBatam->updated_at != $tagihanLoloBatam->created_at)
            <div>
                Terakhir diubah: {{ $tagihanLoloBatam->updated_at->format('d/m/Y H:i') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
