@extends('layouts.app')

@section('title', $purchaseOrder->nomor_po . ' - AYPSIS')
@section('page_title', 'Detail Purchase Order')

@push('styles')
<style>
    @media print {
        body { height: auto !important; background: white !important; }
        body > header, #sidebar, #sidebar-overlay { display: none !important; }
        body > div.flex, #page-content { overflow: visible !important; }
        #page-content { margin-left: 0 !important; }
        #page-content > div { padding: 0 !important; }
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-5xl space-y-6 pb-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('purchase-orders.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">&larr; Daftar Purchase Order</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $purchaseOrder->nomor_po }}</h1>
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
@endsection
