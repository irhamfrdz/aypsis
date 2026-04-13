@extends('layouts.app')

@section('title', 'Detail Batch Asuransi')
@section('page_title', 'Detail Batch Asuransi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300">
        <div class="p-8 bg-gradient-to-br from-white to-gray-50">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-6">
                    <div class="p-4 bg-blue-50 rounded-2xl">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $batch->nomor_polis ?? 'Tanpa Nomor Polis' }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="px-2.5 py-0.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold uppercase tracking-widest">
                                {{ $batch->vendorAsuransi->nama_asuransi }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium">Dibuat pada {{ $batch->created_at->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    @can('asuransi-tanda-terima-multi-update')
                    <a href="{{ route('asuransi-tanda-terima-multi.edit', $batch->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl font-bold text-xs text-amber-600 uppercase tracking-widest hover:bg-amber-50 transition-all shadow-sm">
                        Edit Data
                    </a>
                    @endcan
                    @if($batch->asuransi_path)
                    <a href="{{ Storage::disk('public')->url($batch->asuransi_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md">
                        Download File
                    </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-10">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Tanggal Polis</p>
                    <p class="text-sm font-bold text-gray-800">{{ $batch->tanggal_polis->format('d/m/Y') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Rate Asuransi</p>
                    <p class="text-sm font-bold text-gray-800">{{ $batch->asuransi_rate }}%</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Status</p>
                    <span class="inline-flex px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-black uppercase">Active</span>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Dibuat Oleh</p>
                    <p class="text-sm font-bold text-gray-800">{{ $batch->creator->nama_lengkap ?? 'System' }}</p>
                </div>
            </div>
        </div>

        <!-- Financial Row -->
        <div class="bg-gray-900 p-8 flex justify-between items-center">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 w-full md:w-2/3">
                <div>
                    <p class="text-[10px] font-bold text-blue-400/60 uppercase tracking-widest mb-1">Total Pertanggungan</p>
                    <p class="text-lg font-black text-white">Rp {{ number_format($batch->total_nilai_pertanggungan, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-blue-400/60 uppercase tracking-widest mb-1">Premi Dasar</p>
                    <p class="text-lg font-black text-white">Rp {{ number_format($batch->premi, 0, ',', '.') }}</p>
                </div>
                <div class="hidden md:block">
                    <p class="text-[10px] font-bold text-blue-400/60 uppercase tracking-widest mb-1">Biaya Admin</p>
                    <p class="text-lg font-black text-white">Rp {{ number_format($batch->biaya_admin, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1">Grand Total</p>
                <p class="text-3xl font-black text-blue-300 tracking-tighter shadow-blue-500/50">Rp {{ number_format($batch->grand_total, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    @if($batch->keterangan)
    <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6">
        <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-2">Keterangan Internal</h4>
        <p class="text-sm text-gray-700 leading-relaxed italic">"{{ $batch->keterangan }}"</p>
    </div>
    @endif

    <!-- Content Card -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300">
        <div class="p-6 border-b flex items-center justify-between">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Rincian Tanda Terima ({{ $batch->items->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Tanda Terima / Ref</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Kontainer</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Barang / Deskripsi</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Qty</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Satuan</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Nilai Barang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($batch->items as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $item->receipt_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter bg-gray-100 text-gray-500 border border-gray-200">
                                    {{ $item->receipt_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-bold text-blue-600">
                                    @if($item->receipt_type == 'tt' && $item->tandaTerima)
                                        {{ $item->tandaTerima->no_kontainer ?? '-' }}
                                    @elseif($item->receipt_type == 'tttsj' && $item->tandaTerimaTanpaSj)
                                        {{ $item->tandaTerimaTanpaSj->no_kontainer ?? '-' }}
                                    @elseif($item->receipt_type == 'lcl' && $item->tandaTerimaLcl)
                                        {{ $item->tandaTerimaLcl->nomor_kontainer ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[11px] text-gray-600 font-medium">
                                    @if($item->receipt_type == 'tt' && $item->tandaTerima)
                                        {{ $item->tandaTerima->nama_barang ?? '-' }}
                                    @elseif($item->receipt_type == 'tttsj' && $item->tandaTerimaTanpaSj)
                                        {{ $item->tandaTerimaTanpaSj->nama_barang ?? '-' }}
                                    @elseif($item->receipt_type == 'lcl' && $item->tandaTerimaLcl)
                                        {{ $item->tandaTerimaLcl->nomor_tanda_terima }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-xs font-bold text-gray-700">
                                @if($item->receipt_type == 'tt' && $item->tandaTerima)
                                    {{ number_format($item->tandaTerima->jumlah, 0, ',', '.') }}
                                @elseif($item->receipt_type == 'tttsj' && $item->tandaTerimaTanpaSj)
                                    {{ number_format($item->tandaTerimaTanpaSj->jumlah_barang, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase">
                                @if($item->receipt_type == 'tt' && $item->tandaTerima)
                                    {{ $item->tandaTerima->satuan ?? '-' }}
                                @elseif($item->receipt_type == 'tttsj' && $item->tandaTerimaTanpaSj)
                                    {{ $item->tandaTerimaTanpaSj->satuan_barang ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-gray-900 italic">Rp {{ number_format($item->nilai_pertanggungan, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50/80">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-right text-[10px] font-black text-gray-500 uppercase tracking-widest">Subtotal Nilai Barang</td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-black text-gray-900">Rp {{ number_format($batch->total_nilai_pertanggungan, 0, ',', '.') }}</div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="flex justify-between items-center py-4">
        <a href="{{ route('asuransi-tanda-terima-multi.index') }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
