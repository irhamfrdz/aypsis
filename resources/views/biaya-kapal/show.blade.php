@extends('layouts.app')

@section('title', 'Detail Biaya Kapal')
@section('page_title', 'Detail Biaya Kapal')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Biaya Kapal</h2>
        <div class="flex space-x-2">
            @can('biaya-kapal-update')
            <a href="{{ route('biaya-kapal.edit', $biayaKapal->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            @endcan
            @if($biayaKapal->stuffingDetails->count() > 0)
            <a href="{{ route('biaya-kapal.print-stuffing', $biayaKapal->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium rounded-md transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Stuffing
            </a>
            @endif
            <a href="{{ route('biaya-kapal.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal</label>
                <p class="text-lg font-semibold text-gray-900">{{ $biayaKapal->tanggal->format('d/m/Y') }}</p>
            </div>

            @if($biayaKapal->nomor_referensi)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Referensi</label>
                <p class="text-lg font-semibold text-gray-900">{{ $biayaKapal->nomor_referensi }}</p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Invoice</label>
                <p class="text-lg font-semibold text-gray-900">{{ $biayaKapal->nomor_invoice }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Kapal</label>
                @php
                    $namaKapals = is_array($biayaKapal->nama_kapal) ? $biayaKapal->nama_kapal : [$biayaKapal->nama_kapal];
                @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach($namaKapals as $kapal)
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">{{ $kapal }}</span>
                    @endforeach
                </div>
            </div>

            @if($biayaKapal->no_voyage && count($biayaKapal->no_voyage) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Voyage</label>
                @php
                    $noVoyages = is_array($biayaKapal->no_voyage) ? $biayaKapal->no_voyage : [$biayaKapal->no_voyage];
                @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach($noVoyages as $voyage)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">{{ $voyage }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Biaya</label>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                    {{ $biayaKapal->jenis_biaya_label }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nominal</label>
                <p class="text-2xl font-bold text-green-600">{{ $biayaKapal->formatted_nominal }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Keterangan</label>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-900">{{ $biayaKapal->keterangan ?: '-' }}</p>
                </div>
            </div>

            @if($biayaKapal->bukti)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-2">Bukti</label>
                @if($biayaKapal->bukti_foto)
                    <a href="{{ $biayaKapal->bukti_foto }}" target="_blank" class="block">
                        <img src="{{ $biayaKapal->bukti_foto }}" alt="Bukti" class="max-w-full h-auto rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    </a>
                @elseif($biayaKapal->bukti_pdf)
                    <a href="{{ $biayaKapal->bukti_pdf }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Lihat PDF
                    </a>
                @else
                    <a href="{{ asset('storage/' . $biayaKapal->bukti) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download File
                    </a>
                @endif
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat</label>
                <p class="text-sm text-gray-700">{{ $biayaKapal->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if($biayaKapal->updated_at != $biayaKapal->created_at)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diubah</label>
                <p class="text-sm text-gray-700">{{ $biayaKapal->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    @if($biayaKapal->tkbmDetails->count() > 0)
    <div class="mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Detail TKBM</h3>
        <div class="space-y-6">
            @php
                $groupedTkbm = $biayaKapal->tkbmDetails->groupBy(function($item) {
                     return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-') . '|' . ($item->no_referensi ?? '-') . '|' . ($item->tanggal_invoice_vendor ?? '-');
                });
            @endphp
            @foreach($groupedTkbm as $groupKey => $details)
                @php
                    list($kapal, $voyage, $noRef, $tglVendor) = explode('|', $groupKey);
                    $first = $details->first();
                @endphp
                <div class="bg-amber-50 border-2 border-amber-200 rounded-lg p-5">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Kapal</span>
                            <p class="text-lg font-bold text-gray-900">{{ $kapal }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Voyage</span>
                            <p class="text-lg font-bold text-gray-900">{{ $voyage }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">No. Ref</span>
                            <p class="text-lg font-bold text-gray-900">{{ $noRef }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Tgl Vendor</span>
                            <p class="text-lg font-bold text-gray-900">{{ $tglVendor != '-' ? \Carbon\Carbon::parse($tglVendor)->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Barang</span>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tarif</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($details as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item->pricelistTkbm->nama_barang ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->tarif, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold">
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">Biaya (Items)</td>
                                        <td class="px-4 py-2 text-sm text-right">Rp {{ number_format($details->sum('subtotal'), 0, ',', '.') }}</td>
                                    </tr>
                                    @if($first->adjustment != 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">Adjustment</td>
                                        <td class="px-4 py-2 text-sm text-right @if($first->adjustment > 0) text-green-600 @else text-red-600 @endif">{{ $first->adjustment > 0 ? '+' : '' }} Rp {{ number_format($first->adjustment, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="bg-blue-50">
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">Total Biaya Per Kapal</td>
                                        <td class="px-4 py-2 text-sm text-right">Rp {{ number_format($details->sum('subtotal') + $first->adjustment, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">PPH (2%)</td>
                                        <td class="px-4 py-2 text-sm text-right text-red-600">- Rp {{ number_format($first->pph, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="bg-amber-100">
                                        <td colspan="3" class="px-4 py-2 text-base text-right font-black">Grand Total Section</td>
                                        <td class="px-4 py-2 text-base text-right font-black text-amber-800">Rp {{ number_format($first->grand_total, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @if($biayaKapal->truckingDetails->count() > 0)
    <div class="mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Detail Biaya Trucking</h3>
        <div class="space-y-6">
            @foreach($biayaKapal->truckingDetails as $trucking)
                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Kapal</span>
                            <p class="text-lg font-bold text-gray-900">{{ $trucking->kapal }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Voyage</span>
                            <p class="text-lg font-bold text-gray-900">{{ $trucking->voyage }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Vendor Trucking</span>
                            <p class="text-lg font-bold text-gray-900">{{ $trucking->nama_vendor }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Kontainer</span>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Kontainer</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Seal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $noBls = is_array($trucking->no_bl) ? $trucking->no_bl : [];
                                    @endphp
                                    @foreach($noBls as $blId)
                                        @php
                                            $detail = $blDetails[$blId] ?? null;
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900 font-medium">{{ $detail->kontainer ?? $blId }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600">
                                                @if($detail && $detail->size)
                                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $detail->size }}ft</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600">{{ $detail->seal ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    @if(empty($noBls))
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada kontainer terpilih</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-blue-200 pt-4">
                        <div class="bg-white p-3 rounded-lg border border-blue-100 shadow-sm">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Subtotal</span>
                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($trucking->subtotal, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-blue-100 shadow-sm">
                            <span class="text-xs font-semibold text-red-500 uppercase">PPh 2%</span>
                            <p class="text-lg font-bold text-red-600">- Rp {{ number_format($trucking->pph, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-blue-600 p-3 rounded-lg shadow-md">
                            <span class="text-xs font-semibold text-blue-100 uppercase">Total Biaya</span>
                            <p class="text-xl font-black text-white">Rp {{ number_format($trucking->total_biaya, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($biayaKapal->stuffingDetails->count() > 0)
    <div class="mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Detail Stuffing</h3>
        <div class="space-y-6">
            @foreach($biayaKapal->stuffingDetails as $stuffing)
                <div class="bg-rose-50 border-2 border-rose-200 rounded-lg p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Kapal</span>
                            <p class="text-lg font-bold text-gray-900">{{ $stuffing->kapal }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Voyage</span>
                            <p class="text-lg font-bold text-gray-900">{{ $stuffing->voyage }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Tanda Terima</span>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">No. Surat Jalan</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">No. Kontainer</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Pengirim</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Penerima</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stuffing->getTandaTerimas() as $tt)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap">{{ $tt->no_surat_jalan }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">{{ $tt->no_kontainer ?: '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap text-xs">{{ $tt->pengirim }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap text-xs">{{ $tt->penerima }}</td>
                                        </tr>
                                    @endforeach
                                    @if(empty($stuffing->tanda_terima_ids))
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada Tanda Terima terpilih</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($biayaKapal->labuhTambatDetails->count() > 0)
    <div class="mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Detail Labuh Tambat</h3>
        <div class="space-y-6">
            @php
                $groupedLabuh = $biayaKapal->labuhTambatDetails->groupBy(function($item) {
                     return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-') . '|' . ($item->vendor ?? '-') . '|' . ($item->lokasi ?? '-') . '|' . ($item->tanggal_invoice_vendor ?? '-');
                });
            @endphp
            @foreach($groupedLabuh as $groupKey => $details)
                @php
                    $parts = explode('|', $groupKey);
                    $kapal = $parts[0] ?? '-';
                    $voyage = $parts[1] ?? '-';
                    $vendor = $parts[2] ?? '-';
                    $lokasi = $parts[3] ?? '-';
                    $tglVendor = $parts[4] ?? '-';
                    $first = $details->first();
                @endphp
                <div class="bg-indigo-50 border-2 border-indigo-200 rounded-lg p-5">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Kapal</span>
                            <p class="text-lg font-bold text-gray-900">{{ $kapal }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Voyage</span>
                            <p class="text-lg font-bold text-gray-900">{{ $voyage }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Vendor</span>
                            <p class="text-lg font-bold text-gray-900">{{ $vendor }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Lokasi</span>
                            <p class="text-lg font-bold text-gray-900">{{ $lokasi }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Tgl Vendor</span>
                            <p class="text-lg font-bold text-gray-900">{{ $tglVendor != '-' ? \Carbon\Carbon::parse($tglVendor)->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Item Labuh Tambat</span>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe / Keterangan</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Kuantitas (GT)</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($details as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                {{ $item->type_keterangan }}
                                                @if($item->is_lumpsum)
                                                    <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded">LUMPSUM</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">{{ $item->is_lumpsum ? '-' : number_format($item->kuantitas, 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold">
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">Subtotal Section</td>
                                        <td class="px-4 py-2 text-sm text-right">Rp {{ number_format($details->sum('sub_total'), 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">PPN (11%)</td>
                                        <td class="px-4 py-2 text-sm text-right text-gray-600">Rp {{ number_format($details->sum('ppn'), 0, ',', '.') }}</td>
                                    </tr>
                                    @if($details->sum('biaya_materai') > 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">Biaya Materai</td>
                                        <td class="px-4 py-2 text-sm text-right text-gray-600">Rp {{ number_format($details->sum('biaya_materai'), 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="bg-indigo-100 uppercase">
                                        <td colspan="3" class="px-4 py-2 text-base text-right font-black">Grand Total Section</td>
                                        <td class="px-4 py-2 text-base text-right font-black text-indigo-900">Rp {{ number_format($details->sum('grand_total'), 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    @if($first->penerima || $first->nomor_rekening || $first->nomor_referensi)
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        @if($first->penerima)
                        <div>
                            <span class="text-gray-500">Penerima:</span>
                            <span class="font-semibold">{{ $first->penerima }}</span>
                        </div>
                        @endif
                        @if($first->nomor_rekening)
                        <div>
                            <span class="text-gray-500">No. Rekening:</span>
                            <span class="font-semibold">{{ $first->nomor_rekening }}</span>
                        </div>
                        @endif
                        @if($first->nomor_referensi)
                        <div>
                            <span class="text-gray-500">Ref Vendor:</span>
                            <span class="font-semibold">{{ $first->nomor_referensi }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @if($biayaKapal->oppOptDetails->count() > 0)
    <div class="mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Detail OPP/OPT</h3>
        <div class="space-y-6">
            @php
                $groupedOppOpt = $biayaKapal->oppOptDetails->groupBy(function($item) {
                     return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-');
                });
            @endphp
            @foreach($groupedOppOpt as $groupKey => $details)
                @php
                    $parts = explode('|', $groupKey);
                    $kapal = $parts[0] ?? '-';
                    $voyage = $parts[1] ?? '-';
                    $first = $details->first();
                @endphp
                <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Kapal</span>
                            <p class="text-lg font-bold text-gray-900">{{ $kapal }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Voyage</span>
                            <p class="text-lg font-bold text-gray-900">{{ $voyage }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Item OPP/OPT</span>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tarif</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($details as $item)
                                        @if($item->pricelist_opp_opt_id)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item->pricelistOppOpt->nama_barang ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->tarif, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    @if($details->whereNotNull('pricelist_opp_opt_id')->count() == 0)
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada item tercatat</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold">
                                    <tr class="bg-blue-50">
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">Total Nominal Kapal</td>
                                        <td class="px-4 py-2 text-sm text-right font-black text-blue-700">Rp {{ number_format($first->total_nominal, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm text-right">DP</td>
                                        <td class="px-4 py-2 text-sm text-right text-emerald-600">Rp {{ number_format($first->dp, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="bg-purple-100">
                                        <td colspan="3" class="px-4 py-2 text-sm text-right font-black">Sisa Pembayaran</td>
                                        <td class="px-4 py-2 text-sm text-right font-black text-purple-900">Rp {{ number_format($first->sisa_pembayaran, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif


    @if($biayaKapal->storageDetails->count() > 0)
    <div class="mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Detail Biaya Storage</h3>
        <div class="space-y-6">
            @foreach($biayaKapal->storageDetails as $storage)
                <div class="bg-sky-50 border-2 border-sky-200 rounded-lg p-5">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-semibold text-sky-600 uppercase tracking-wider">Kapal</span>
                            <p class="text-lg font-bold text-gray-900">{{ $storage->kapal }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-sky-600 uppercase tracking-wider">Voyage</span>
                            <p class="text-lg font-bold text-gray-900">{{ $storage->voyage }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-sky-600 uppercase tracking-wider">Vendor</span>
                            <p class="text-lg font-bold text-gray-900">{{ $storage->vendor }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-sky-600 uppercase tracking-wider">Lokasi</span>
                            <p class="text-lg font-bold text-gray-900">{{ $storage->lokasi }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Kontainer</span>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Kontainer</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Hari</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $kontainerIds = is_array($storage->kontainer_ids) ? $storage->kontainer_ids : [];
                                    @endphp
                                    @foreach($kontainerIds as $k)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900 font-medium">{{ $k['nomor_kontainer'] ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600">
                                                @if(isset($k['size']))
                                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $k['size'] }}ft</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-800 text-center font-bold">
                                                {{ $k['hari'] ?? '1' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(empty($kontainerIds))
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center italic">Tidak ada kontainer terpilih</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4 border-t border-sky-200 pt-4">
                        <div class="bg-white p-3 rounded-lg border border-sky-100 shadow-sm">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Subtotal</span>
                            <p class="text-base font-bold text-gray-900">Rp {{ number_format($storage->subtotal, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-sky-100 shadow-sm">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Materai</span>
                            <p class="text-base font-bold text-gray-900">Rp {{ number_format($storage->biaya_materai, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-sky-100 shadow-sm">
                            <span class="text-xs font-semibold text-blue-500 uppercase">PPN (11%)</span>
                            <p class="text-base font-bold text-blue-600">Rp {{ number_format($storage->ppn, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-sky-100 shadow-sm">
                            <span class="text-xs font-semibold text-red-500 uppercase">PPh 2%</span>
                            <p class="text-base font-bold text-red-600">- Rp {{ number_format($storage->pph, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-sky-600 p-3 rounded-lg shadow-md">
                            <span class="text-xs font-semibold text-sky-100 uppercase">Total Biaya</span>
                            <p class="text-lg font-black text-white">Rp {{ number_format($storage->total_biaya, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @can('biaya-kapal-delete')
    <div class="mt-8 pt-6 border-t border-gray-200">
        <form action="{{ route('biaya-kapal.destroy', $biayaKapal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus Data
            </button>
        </form>
    </div>
    @endcan
</div>
@endsection
