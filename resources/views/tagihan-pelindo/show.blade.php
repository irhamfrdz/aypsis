@extends('layouts.app')

@section('title', 'Detail Tagihan Pelindo')
@section('page_title', 'Detail Tagihan Pelindo')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    {{-- Action Bar --}}
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('tagihan-pelindo.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <div class="flex space-x-2">
            @can('tagihan-pelindo-edit')
            <a href="{{ route('tagihan-pelindo.edit', $tagihan->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition duration-150">
                <i class="fas fa-edit mr-2"></i> Edit Tagihan
            </a>
            @endcan
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition duration-150">
                <i class="fas fa-print mr-2"></i> Cetak Halaman
            </button>
        </div>
    </div>

    {{-- Invoice Preview Section --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden" id="invoicePrintArea">
        {{-- Invoice Header Header --}}
        <div class="bg-indigo-900 text-white p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-black tracking-tight">INVOICE TAGIHAN PELINDO</h2>
                    <p class="text-indigo-200 text-sm mt-1">PT. Alexindo YakinPrima Shipping Management System</p>
                </div>
                <div class="text-right">
                    <span class="text-xs uppercase tracking-widest text-indigo-300 font-bold">Nomor Invoice</span>
                    <div class="text-2xl font-black tracking-wider">{{ $tagihan->nomor_tagihan }}</div>
                </div>
            </div>
        </div>

        {{-- Invoice Meta Details --}}
        <div class="p-8 border-b border-gray-100 bg-gray-50/50">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400">Tanggal Tagihan</span>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d F Y') : '-' }}</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400">Status Pembayaran</span>
                    <div class="mt-1">
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $tagihan->status_color }}">
                            {{ $tagihan->status_pembayaran }}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400">Tanggal Bayar</span>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $tagihan->tanggal_bayar ? $tagihan->tanggal_bayar->format('d F Y') : '-' }}</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400">Total Tagihan</span>
                    <p class="text-base font-extrabold text-indigo-700 mt-1">Rp {{ number_format($tagihan->total_tagihan, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Invoice Detail Items --}}
        <div class="p-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Daftar Rincian Pekerjaan / Kegiatan</h3>
            <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-12">No</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nomor Kontainer</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kegiatan</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-24">Ukuran</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-24">Full/Empty</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase w-32">Tarif</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-20">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase w-36">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($tagihan->items as $item)
                    <tr>
                        <td class="px-4 py-3.5 text-center text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3.5 text-sm font-semibold text-gray-800">{{ $item->nomor_kontainer ?: '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->kegiatan }}</td>
                        <td class="px-4 py-3.5 text-center text-sm text-gray-600">{{ $item->ukuran ? ($item->ukuran . 'ft') : '-' }}</td>
                        <td class="px-4 py-3.5 text-center text-sm text-gray-600">{{ $item->status_kontainer ?: '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-gray-700">Rp {{ number_format($item->tarif, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-center text-sm text-gray-800 font-medium">{{ $item->jumlah }}</td>
                        <td class="px-4 py-3.5 text-sm text-right font-semibold text-gray-900">Rp {{ number_format($item->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">{{ $item->keterangan ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Final Summary & Signature --}}
            <div class="mt-8 flex flex-col md:flex-row justify-between items-start gap-8">
                <div class="flex-1">
                    @if($tagihan->keterangan)
                    <div class="bg-gray-50 p-4 rounded-lg border text-xs text-gray-600 max-w-md">
                        <strong class="block text-gray-700 mb-1">Keterangan / Catatan:</strong>
                        {{ $tagihan->keterangan }}
                    </div>
                    @endif
                </div>
                <div class="w-full md:w-80 space-y-4">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm font-semibold text-gray-600">Total Biaya:</span>
                        <span class="text-base font-bold text-gray-800">Rp {{ number_format($tagihan->total_tagihan, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b bg-indigo-50/50 px-3 rounded-lg border-indigo-100">
                        <span class="text-sm font-black text-indigo-900">GRAND TOTAL:</span>
                        <span class="text-lg font-black text-indigo-700">Rp {{ number_format($tagihan->total_tagihan, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice Footer --}}
        <div class="bg-gray-50 px-8 py-4 text-center text-[10px] text-gray-400 border-t flex justify-between items-center">
            <div>Dibuat oleh: {{ $tagihan->createdBy->username ?? '-' }} ({{ $tagihan->created_at->format('d/m/Y H:i') }})</div>
            <div>Halaman Cetak - AYPSIS</div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #invoicePrintArea, #invoicePrintArea * {
            visibility: visible;
        }
        #invoicePrintArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
@endsection
