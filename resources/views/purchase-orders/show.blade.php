@extends('layouts.app')

@section('title', $purchaseOrder->nomor_po . ' - AYPSIS')
@section('page_title', 'Detail Purchase Order')

@push('styles')
<style>
    @media print {
        @page { size: A4 portrait; margin: 15mm; }
        body { height: auto !important; background: white !important; }
        body > header, #sidebar, #sidebar-overlay { display: none !important; }
        body > div.flex, #page-content { overflow: visible !important; }
        #page-content { margin-left: 0 !important; }
        #page-content > div { padding: 0 !important; }
        .po-screen { display: none !important; }
        .po-print { display: block !important; color: #111; font: 12pt 'Times New Roman', serif; }
        .po-print table { width: 100%; border-collapse: collapse; }
        .po-print th, .po-print td { border: 1px solid #333; padding: 6px; vertical-align: top; }
        .po-print thead { display: table-header-group; }
        .po-print tr { break-inside: avoid; }
    }
</style>
@endpush

@section('content')
<div class="po-screen mx-auto max-w-5xl space-y-6 pb-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('purchase-orders.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">&larr; Daftar Purchase Order</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $purchaseOrder->nomor_po }}</h1>
            @if($purchaseOrder->nomor_referensi)<p class="mt-1 text-sm text-slate-600">Nomor Referensi: {{ $purchaseOrder->nomor_referensi }}</p>@endif
            <p class="mt-1 text-sm text-slate-500">Tanggal PO {{ $purchaseOrder->tanggal_po->format('d/m/Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 print:hidden">
            <button type="button" onclick="window.print()" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-print mr-2"></i>Cetak</button>
            @can('purchase-order-update')
                <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Ubah</a>
            @endcan
            @can('purchase-order-delete')
                <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" onsubmit="return confirm('Hapus Purchase Order ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="rounded-lg border border-rose-200 bg-white px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">Hapus</button>
                </form>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 print:hidden">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Vendor</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $purchaseOrder->vendor }}</p>
                @if($purchaseOrder->alamat_vendor)<p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $purchaseOrder->alamat_vendor }}</p>@endif
            </div>
            <div class="sm:text-right">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Status</p>
                <p class="mt-1 font-semibold text-slate-900">{{ ['draft' => 'Draft', 'issued' => 'Diterbitkan', 'cancelled' => 'Dibatalkan'][$purchaseOrder->status] ?? $purchaseOrder->status }}</p>
                @if($purchaseOrder->tanggal_dibutuhkan)<p class="mt-1 text-sm text-slate-600">Dibutuhkan {{ $purchaseOrder->tanggal_dibutuhkan->format('d/m/Y') }}</p>@endif
            </div>
        </div>

        <div class="mt-8 overflow-x-auto">
            <table class="min-w-[650px] w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-3 py-3">No.</th><th class="px-3 py-3">Deskripsi</th><th class="px-3 py-3 text-right">Kuantitas</th><th class="px-3 py-3">Satuan</th><th class="px-3 py-3 text-right">Harga Satuan</th><th class="px-3 py-3 text-right">Jumlah</th>@if($purchaseOrder->permohonan_amprahan_id)<th class="px-3 py-3 text-center">Aset</th>@endif</tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td class="px-3 py-3 text-slate-500">{{ $item->urutan }}</td>
                            <td class="px-3 py-3 text-slate-800">{{ $item->deskripsi }}</td>
                            <td class="px-3 py-3 text-right text-slate-700">{{ number_format((float) $item->kuantitas, 3, ',', '.') }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $item->satuan ?: '-' }}</td>
                            <td class="px-3 py-3 text-right text-slate-700">Rp {{ number_format((float) $item->harga_satuan, 2, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((float) $item->jumlah, 2, ',', '.') }}</td>
                            @if($purchaseOrder->permohonan_amprahan_id)<td class="px-3 py-3 text-center text-slate-700">{{ $item->masuk_asset ? 'Ya' : 'Tidak' }}</td>@endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 ml-auto max-w-sm space-y-2 text-sm">
            <div class="flex justify-between text-slate-600"><span>Subtotal</span><span>Rp {{ number_format((float) $purchaseOrder->subtotal, 2, ',', '.') }}</span></div>
            <div class="flex justify-between text-slate-600"><span>Diskon</span><span>Rp {{ number_format((float) $purchaseOrder->diskon, 2, ',', '.') }}</span></div>
            <div class="flex justify-between text-slate-600"><span>Pajak ({{ number_format((float) $purchaseOrder->pajak_persen, 2, ',', '.') }}%)</span><span>Rp {{ number_format((float) $purchaseOrder->pajak, 2, ',', '.') }}</span></div>
            <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-bold text-slate-900"><span>Total</span><span>Rp {{ number_format((float) $purchaseOrder->total, 2, ',', '.') }}</span></div>
        </div>
        @if($purchaseOrder->catatan)
            <div class="mt-8 border-t border-slate-200 pt-5">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Catatan</p>
                <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $purchaseOrder->catatan }}</p>
            </div>
        @endif
    </div>
</div>
<div class="po-print hidden">
    <header style="display:flex;align-items:center;gap:18px;border-bottom:3px double #333;padding-bottom:14px;text-align:center">
        <img src="{{ asset('images/logo_transparent.png') }}" alt="Logo PT Alexindo Yakinprima" style="width:85px;height:85px;object-fit:contain">
        <div style="flex:1">
            <div style="font-style:italic">Perusahaan Pelayaran Nasional</div>
            <div style="font-size:20pt;font-weight:bold;color:#9b5157">PT. ALEXINDO YAKINPRIMA</div>
            <div style="font-size:10pt">Jl. Pluit Raya No. 8 Blok B No. 12 Jakarta 14440</div>
            <div style="font-size:10pt">Telp. (021) 6606231-6614175-76 &nbsp; Email: alexindo_yakinprima@yahoo.co.id</div>
        </div>
    </header>
    <h2 style="text-align:center;font:bold 15pt Arial,sans-serif;margin:22px 0">MEMO / PURCHASE ORDER</h2>
    <p style="text-align:right">Jakarta, {{ $purchaseOrder->tanggal_po->translatedFormat('d F Y') }}</p>
    <div style="margin:28px 0">
        <div>Perihal &nbsp;&nbsp;: Memo / PO {{ $purchaseOrder->nomor_po }}</div>
        <div>Kepada &nbsp;: {{ $purchaseOrder->vendor }}</div>
        @if($purchaseOrder->alamat_vendor)<div style="margin-left:85px;white-space:pre-line">{{ $purchaseOrder->alamat_vendor }}</div>@endif
    </div>
    <p>Dengan Hormat,</p>
    <p>Mohon dapat disediakan dan dikirimkan kepada kami:</p>
    <p style="margin:22px 0">Quot. No &nbsp;: {{ $purchaseOrder->nomor_referensi ?: '-' }}</p>
    <table>
        <thead><tr><th style="width:35px">No</th><th>Item</th><th style="width:65px">Qty</th><th style="width:115px">Unit Price</th><th style="width:115px">Total</th></tr></thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr><td>{{ $item->urutan }}</td><td>{{ $item->deskripsi }}</td><td>{{ number_format((float) $item->kuantitas, 3, ',', '.') }} {{ $item->satuan }}</td><td style="text-align:right">{{ number_format((float) $item->harga_satuan, 2, ',', '.') }}</td><td style="text-align:right">{{ number_format((float) $item->jumlah, 2, ',', '.') }}</td></tr>
            @endforeach
            <tr><td colspan="4" style="text-align:right;font-weight:bold">Total</td><td style="text-align:right;font-weight:bold">Rp {{ number_format((float) $purchaseOrder->total, 2, ',', '.') }}</td></tr>
        </tbody>
    </table>
    @if($purchaseOrder->catatan)<p style="white-space:pre-line;margin-top:24px">{{ $purchaseOrder->catatan }}</p>@endif
    <p style="margin-top:35px">Demikian surat ini kami sampaikan, sekian dan terima kasih.</p>
    <div style="margin:45px 10px 0 auto;width:180px;text-align:center">Hormat Kami,<br><br><br><br><br>PT. Alexindo Yakinprima</div>
</div>
@endsection
