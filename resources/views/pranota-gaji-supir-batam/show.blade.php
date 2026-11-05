@extends('layouts.app')

@section('title', 'Pranota '.$pranota->nomor_pranota)
@section('page_title', 'Rincian Pranota Gaji Supir Batam')

@section('content')
<div class="mx-auto w-full px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-gray-900">{{ $pranota->nomor_pranota }}</h1><p class="mt-1 text-sm text-gray-600">Tanggal {{ $pranota->tanggal_pranota->format('d/m/Y') }} · {{ $pranota->items->count() }} gaji supir</p></div>
        <a href="{{ route('pranota-gaji-supir-batam.index') }}" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Kembali</a>
    </div>
    @if(session('success'))<div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    <div class="mb-5 grid gap-4 rounded-xl border bg-white p-5 shadow-sm sm:grid-cols-3">
        <div><p class="text-xs font-semibold uppercase text-gray-500">Total Pranota</p><p class="mt-1 text-xl font-bold">Rp {{ number_format((float) $pranota->total_gaji, 0, ',', '.') }}</p></div>
        <div><p class="text-xs font-semibold uppercase text-gray-500">Dibuat oleh</p><p class="mt-1">{{ $pranota->creator?->name ?: '-' }}</p></div>
        <div><p class="text-xs font-semibold uppercase text-gray-500">Keterangan</p><p class="mt-1">{{ $pranota->keterangan ?: '-' }}</p></div>
    </div>
    @php
        $salaryFields = ['gaji_pokok', 'uang_malam_libur', 'biaya_bensin'];
        $salaryTotals = collect($salaryFields)->mapWithKeys(fn ($field) => [
            $field => $pranota->items->sum(fn ($item) => (float) ($item->gaji?->$field ?? 0)),
        ]);
        $deductionTotal = $pranota->items->sum(fn ($item) => $item->gaji?->is_potongan_5_persen
            ? (float) ($item->gaji?->nominal_potongan_5_persen ?? 0) : 0);
    @endphp
    <p class="mb-3 text-xs text-gray-500">Geser tabel ke kanan untuk melihat seluruh komponen gaji. BPJS adalah potongan JHT 2% (Hutang). Tanda — berarti pranota dibuat sebelum potongan BPJS diterapkan.</p>
    <div class="overflow-x-auto rounded-xl border bg-white shadow-sm" role="region" aria-label="Rincian komponen gaji supir" tabindex="0">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3">No.</th>
                    <th class="px-4 py-3">NIK</th>
                    <th class="px-4 py-3">Supir</th>
                    <th class="px-4 py-3">Periode</th>
                    @foreach(['Uang Jalan', 'Uang Lembur', 'Uang Bensin', 'Potongan 5%', 'BPJS', 'PPh', 'Gaji Bersih Setelah BPJS & PPh'] as $label)
                        <th class="whitespace-nowrap px-4 py-3 text-right">{{ $label }}</th>
                    @endforeach
                    <th class="whitespace-nowrap px-4 py-3">Status Bayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pranota->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $item->gaji?->karyawan?->nik ?: '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $item->gaji?->karyawan?->nama_lengkap ?: 'Supir tidak tersedia' }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $item->gaji?->periode_text ?: '-' }}</td>
                        @foreach($salaryFields as $field)
                            <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) ($item->gaji?->$field ?? 0), 2, ',', '.') }}</td>
                        @endforeach
                        <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format($item->gaji?->is_potongan_5_persen ? (float) ($item->gaji?->nominal_potongan_5_persen ?? 0) : 0, 2, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right" title="{{ $item->bpjs_nomor_pranota ?: 'Tidak ada sumber BPJS tersimpan' }}">{{ $item->bpjs === null ? '—' : 'Rp '.number_format((float) $item->bpjs, 2, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) $item->pph, 2, ',', '.') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $item->total_gaji, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $item->gaji?->status_pembayaran ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 font-bold">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right">Total</td>
                    @foreach($salaryFields as $field)
                        <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format($salaryTotals[$field], 2, ',', '.') }}</td>
                    @endforeach
                    <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format($deductionTotal, 2, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-right">{{ $pranota->items->every(fn ($item) => $item->bpjs === null) ? '—' : 'Rp '.number_format((float) $pranota->items->sum('bpjs'), 2, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) $pranota->items->sum('pph'), 2, ',', '.') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) $pranota->total_gaji, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
