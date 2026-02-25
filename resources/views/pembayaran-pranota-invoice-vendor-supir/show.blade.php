@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Detail Pembayaran Pranota</h2>
            <p class="text-sm text-gray-500">Bukti realisasi pembayaran vendor supir</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('pembayaran-pranota-invoice-vendor-supir.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Payment Info -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Informasi Pembayaran</h3>
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase">Lunas</span>
                </div>
                <div class="p-6 grid grid-cols-2 gap-y-4 gap-x-8">
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">No. Pembayaran</div>
                        <div class="mt-1 text-sm font-medium text-gray-900">{{ $pembayaran->nomor_pembayaran }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Tanggal Bayar</div>
                        <div class="mt-1 text-sm font-medium text-gray-900">{{ $pembayaran->tanggal_pembayaran->format('d F Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Metode</div>
                        <div class="mt-1 text-sm font-medium text-gray-900 uppercase">{{ $pembayaran->metode_pembayaran }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Vendor</div>
                        <div class="mt-1 text-sm font-medium text-gray-900">{{ $pembayaran->vendor->nama_vendor ?? '-' }}</div>
                    </div>
                    @if($pembayaran->bank)
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Bank</div>
                        <div class="mt-1 text-sm font-medium text-gray-900 uppercase">{{ $pembayaran->bank }}</div>
                    </div>
                    @endif
                    @if($pembayaran->no_referensi)
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">No. Referensi</div>
                        <div class="mt-1 text-sm font-medium text-gray-900">{{ $pembayaran->no_referensi }}</div>
                    </div>
                    @endif
                    
                    @if($pembayaran->keterangan)
                    <div class="col-span-2">
                        <div class="text-xs text-gray-500 uppercase font-semibold">Keterangan</div>
                        <div class="mt-1 text-sm font-medium text-gray-900 italic">"{{ $pembayaran->keterangan }}"</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-semibold text-gray-800">Daftar Pranota Terbayar</h3>
                </div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50/50 text-gray-600 font-medium">
                        <tr>
                            <th class="px-6 py-3">No. Pranota</th>
                            <th class="px-6 py-3">Tgl Pranota</th>
                            <th class="px-6 py-3 text-right">Nominal Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pembayaran->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                @if($item->pranota)
                                <a href="{{ route('pranota-invoice-vendor-supir.show', $item->pranota_id) }}" class="text-emerald-600 hover:underline font-medium">
                                    {{ $item->pranota->no_pranota }}
                                </a>
                                @else
                                <span class="text-gray-400">Pranota tidak ditemukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $item->pranota ? $item->pranota->tanggal_pranota->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-emerald-50/50 font-bold border-t border-emerald-100">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right text-emerald-900 border-none">TOTAL PEMBAYARAN</td>
                            <td class="px-6 py-4 text-right text-emerald-900 border-none">
                                Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h4 class="font-bold text-gray-800 text-sm uppercase">Audit Trail</h4>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-50 flex items-center justify-center">
                            <svg class="h-3 w-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-semibold text-gray-900">Input Oleh</p>
                            <p class="text-[10px] text-gray-500">{{ $pembayaran->creator->name ?? 'System' }}</p>
                            <p class="text-[10px] text-gray-400 italic">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-emerald-600 rounded-xl shadow-md p-6 text-white text-center">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-xs font-medium uppercase tracking-wider opacity-75">Status Pembayaran</div>
                <div class="text-2xl font-black mt-1 uppercase">Lunas</div>
                <div class="text-[10px] mt-2 opacity-60">Dokumen sudah diverifikasi dan tercatat dalam sistem keuangan.</div>
            </div>
        </div>
    </div>
</div>
@endsection
