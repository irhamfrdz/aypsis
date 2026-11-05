@extends('layouts.app')

@section('title', 'Hasil Report Biaya Agen')
@section('page_title', 'Hasil Report Biaya Agen')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('report.biaya-agen.index') }}" class="text-sm font-medium text-indigo-700 hover:underline">&larr; Ubah pilihan report</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900">Report Biaya Agen</h1>
            <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-sm text-gray-600">
                @if(in_array($validated['mode'], ['kapal', 'gabungan'], true))
                    <span>Kapal: <strong class="text-gray-900">{{ $validated['kapal'] }}</strong></span>
                @endif
                @if(in_array($validated['mode'], ['tanggal', 'gabungan'], true))
                    <span>Periode: <strong class="text-gray-900">{{ \Carbon\Carbon::parse($validated['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($validated['end_date'])->format('d/m/Y') }}</strong></span>
                @endif
                @if(!empty($validated['jenis_biaya']))
                    <span>Jenis biaya: <strong class="text-gray-900">{{ implode(', ', $validated['jenis_biaya']) }}</strong></span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">{{ $rows->count() }} item</span>
            <a href="{{ route('report.biaya-agen.export', $validated) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700"><i class="fas fa-file-excel" aria-hidden="true"></i> Download Excel</a>
            <a href="{{ route('report.biaya-agen.print', $validated) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"><i class="fas fa-print" aria-hidden="true"></i> Print</a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Subtotal</p>
            <p class="mt-2 text-xl font-bold text-gray-900">Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">PPh</p>
            <p class="mt-2 text-xl font-bold text-gray-900">Rp {{ number_format($summary['pph'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Penyesuaian</p>
            <p class="mt-2 text-xl font-bold text-gray-900">Rp {{ number_format($summary['adjustment'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-indigo-700">Total biaya agen</p>
            <p class="mt-2 text-xl font-bold text-indigo-900">Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="whitespace-nowrap px-4 py-3">Tanggal</th>
                    <th class="whitespace-nowrap px-4 py-3">Invoice</th>
                    <th class="whitespace-nowrap px-4 py-3">Kapal / Voyage</th>
                    <th class="whitespace-nowrap px-4 py-3">Vendor / Lokasi</th>
                    <th class="px-4 py-3">Jenis biaya</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Kuantitas</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Harga</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Jasa air</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Subtotal</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">PPh</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Penyesuaian</th>
                    <th class="whitespace-nowrap px-4 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ \Carbon\Carbon::parse($row->tanggal_invoice_vendor ?: $row->tanggal_biaya_kapal)->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3"><a href="{{ route('biaya-kapal.show', $row->biaya_kapal_id) }}" class="font-medium text-indigo-700 hover:underline">{{ $row->nomor_invoice ?: '-' }}</a>@if($row->nomor_referensi)<div class="text-xs text-gray-500">{{ $row->nomor_referensi }}</div>@endif</td>
                        <td class="px-4 py-3 text-gray-700"><span class="font-medium">{{ $row->kapal ?: '-' }}</span><div class="text-xs text-gray-500">Voyage {{ $row->voyage ?: '-' }}</div></td>
                        <td class="px-4 py-3 text-gray-700"><span class="font-medium">{{ $row->vendor ?: '-' }}</span><div class="text-xs text-gray-500">{{ $row->lokasi ?: '-' }}</div></td>
                        <td class="min-w-40 px-4 py-3 text-gray-700">{{ $row->type_keterangan ?: '-' }}@if($row->is_lumpsum)<span class="ml-1 text-xs text-gray-500">(Lumpsum)</span>@endif</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">{{ $row->is_lumpsum ? '-' : number_format($row->kuantitas, 2, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">Rp {{ number_format($row->harga, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">Rp {{ number_format($row->jasa_air, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">Rp {{ number_format($row->sub_total, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">Rp {{ number_format($row->pph, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">Rp {{ number_format($row->adjustment, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($row->grand_total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="px-4 py-12 text-center text-gray-500">Tidak ada biaya agen untuk pilihan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
