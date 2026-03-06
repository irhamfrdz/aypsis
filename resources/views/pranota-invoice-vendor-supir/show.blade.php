@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Detail Pranota Invoice Vendor Supir</h2>
            <p class="text-sm text-gray-500">Informasi lengkap pranota beserta daftar invoice vendor</p>
        </div>
        <div class="flex space-x-2">
            @if(auth()->user()->can('pranota-invoice-vendor-supir-update'))
            <a href="{{ route('pranota-invoice-vendor-supir.edit', $pranota->id) }}" class="flex items-center text-sm font-medium text-white bg-amber-500 rounded-lg px-4 py-2 hover:bg-amber-600 transition-colors shadow-sm">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Ubah Status
            </a>
            @endif
            <a href="{{ route('pranota-invoice-vendor-supir.print', $pranota->id) }}" target="_blank" class="flex items-center text-sm font-medium text-white bg-gray-600 rounded-lg px-4 py-2 hover:bg-gray-700 transition-colors shadow-sm">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak Pranota
            </a>
            <a href="{{ route('pranota-invoice-vendor-supir.index') }}" class="flex items-center text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg px-4 py-2 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Header Pranota Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 text-rose-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Pranota {{ $pranota->no_pranota }}
            </h3>
            <div>
                @if($pranota->status_pembayaran == 'lunas')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Lunas</span>
                @elseif($pranota->status_pembayaran == 'sebagian')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Sebagian</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Belum Dibayar</span>
                @endif
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <div>
                        <div class="text-sm text-gray-500">Nama Vendor</div>
                        <div class="font-medium text-gray-900 mt-1 text-lg">{{ $pranota->vendor->nama_vendor ?? '-' }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Tanggal Pranota</div>
                        <div class="font-medium text-gray-900 mt-1">{{ optional($pranota->tanggal_pranota)->format('d F Y') }}</div>
                    </div>

                    @if($pranota->keterangan)
                    <div>
                        <div class="text-sm text-gray-500">Keterangan Tambahan</div>
                        <div class="w-full mt-1 p-3 bg-gray-50 border border-gray-100 rounded-lg text-sm text-gray-700 leading-relaxed min-h-[60px]">
                            {{ $pranota->keterangan }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">
                    <div class="p-4 bg-rose-50 border border-rose-100 rounded-lg">
                        <div class="text-sm font-medium text-rose-800 mb-1">Total Setelah PPH (Grand Total)</div>
                        <div class="text-2xl font-bold text-rose-900 mb-1">Rp {{ number_format($pranota->grand_total > 0 ? $pranota->grand_total : $pranota->total_nominal, 0, ',', '.') }}</div>
                        @if($pranota->pph > 0)
                            <div class="text-xs text-rose-600 line-through">Rp {{ number_format($pranota->total_nominal + $pranota->pph, 0, ',', '.') }}</div>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Dibuat Pada</div>
                            <div class="font-medium text-gray-900 text-sm">
                                {{ $pranota->created_at->format('d/m/Y H:i') }}
                                <span class="text-xs text-gray-400 block mt-0.5">{{ optional($pranota->creator)->name ?? '-' }}</span>
                            </div>
                        </div>
                        
                        @if($pranota->updated_at != $pranota->created_at)
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Terakhir Diperbarui</div>
                            <div class="font-medium text-gray-900 text-sm">
                                {{ $pranota->updated_at->format('d/m/Y H:i') }}
                                <span class="text-xs text-gray-400 block mt-0.5">{{ optional($pranota->updater)->name ?? '-' }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Invoices -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-800 flex items-center">
                Daftar Invoice Vendor ({{ $pranota->invoiceTagihanVendors->count() }} item)
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">No Invoice</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Daftar Surat Jalan</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pranota->invoiceTagihanVendors as $invoice)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-blue-600">
                            {{ $invoice->no_invoice }}
                        </td>
                        <td class="px-6 py-4">{{ $invoice->tanggal_invoice->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs overflow-hidden text-ellipsis whitespace-nowrap">
                                @foreach($invoice->tagihanSupirVendors as $tagihan)
                                    <span class="inline-block px-1.5 py-0.5 bg-gray-100 rounded text-[10px] mr-1 mb-1">{{ $tagihan->suratJalan->no_surat_jalan ?? '-' }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-medium">
                            Rp {{ number_format($invoice->total_nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data invoice yang terkait.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($pranota->invoiceTagihanVendors->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700">Subtotal</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-700 whitespace-nowrap">
                            Rp {{ number_format($pranota->total_nominal + $pranota->pph, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if($pranota->pph > 0)
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700">PPH 2%</td>
                        <td class="px-6 py-4 text-right font-bold text-red-600 whitespace-nowrap">
                            - Rp {{ number_format($pranota->pph, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900 border-t-2 border-gray-200">Total Keseluruhan</td>
                        <td class="px-6 py-4 text-right font-bold text-rose-800 whitespace-nowrap border-t-2 border-gray-200">
                            Rp {{ number_format($pranota->grand_total > 0 ? $pranota->grand_total : $pranota->total_nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
