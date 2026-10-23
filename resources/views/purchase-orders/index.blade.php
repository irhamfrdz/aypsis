@extends('layouts.app')

@section('title', 'Purchase Order - AYPSIS')
@section('page_title', 'Purchase Order')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Purchase Order</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola pesanan pembelian barang dan jasa.</p>
        </div>
        @can('purchase-order-create')
            <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><i class="fas fa-plus"></i>Tambah PO</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('purchase-orders.index') }}" class="grid grid-cols-1 gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-[1fr_180px_auto] sm:items-end">
        <div>
            <label for="search" class="mb-2 block text-sm font-semibold text-slate-700">Cari PO atau Vendor</label>
            <input id="search" name="search" type="search" maxlength="100" value="{{ request('search') }}" placeholder="Nomor PO atau nama vendor" class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="filter_status" class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
            <select id="filter_status" name="status" class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua status</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="issued" @selected(request('status') === 'issued')>Diterbitkan</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
            </select>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('purchase-orders.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">Cari</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-[780px] w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nomor PO</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Vendor</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-blue-700"><a href="{{ route('purchase-orders.show', $order) }}" class="hover:underline">{{ $order->nomor_po }}</a></td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-700">{{ $order->tanggal_po->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $order->vendor }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->status === 'issued' ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800') }}">
                                    {{ ['draft' => 'Draft', 'issued' => 'Diterbitkan', 'cancelled' => 'Dibatalkan'][$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900">Rp {{ number_format((float) $order->total, 2, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right"><a href="{{ route('purchase-orders.show', $order) }}" class="text-sm font-semibold text-blue-700 hover:underline">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada Purchase Order.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $orders->links() }}
</div>
@endsection
