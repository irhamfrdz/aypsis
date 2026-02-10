@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Detail Tagihan</h1>

    <div class="bg-white p-4 rounded shadow">
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-semibold">Vendor</dt>
                <dd class="mt-1">{{ $item->vendor ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-semibold">Nomor Kontainer</dt>
                <dd class="mt-1">{{ $item->nomor_kontainer ?? '-' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-semibold">Group</dt>
                <dd class="mt-1">{{ $item->group ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-semibold">Periode</dt>
                <dd class="mt-1">{{ $item->periode ?? '-' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-semibold">Tanggal Harga Awal</dt>
                <dd class="mt-1">{{ optional($item->tanggal_harga_awal)->format('Y-m-d') ?? ($item->tanggal_harga_awal ?? '-') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-semibold">Tanggal Harga Akhir</dt>
                <dd class="mt-1">{{ optional($item->tanggal_harga_akhir)->format('Y-m-d') ?? ($item->tanggal_harga_akhir ?? '-') }}</dd>
            </div>

            <div>
                <dt class="text-sm font-semibold">Masa</dt>
                <dd class="mt-1">{{ $item->masa ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-semibold">DPP</dt>
                <dd class="mt-1">{{ number_format($item->dpp ?? 0, 2, '.', ',') }}</dd>
            </div>

            <div>
                <dt class="text-sm font-semibold">DPP Nilai Lain</dt>
                <dd class="mt-1">{{ number_format($item->dpp_nilai_lain ?? 0, 2, '.', ',') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-semibold">PPN</dt>
                <dd class="mt-1">{{ number_format($item->ppn ?? 0, 2, '.', ',') }}</dd>
            </div>

            <div>
                <dt class="text-sm font-semibold">PPH</dt>
                <dd class="mt-1">{{ number_format($item->pph ?? 0, 2, '.', ',') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-semibold">Grand Total</dt>
                <dd class="mt-1">{{ number_format($item->grand_total ?? 0, 2, '.', ',') }}</dd>
            </div>
        </dl>

        <div class="mt-4">
            <a href="{{ route('daftar-tagihan-kontainer-sewa-2.edit', $item->id ?? 0) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Edit</a>
            <a href="{{ route('daftar-tagihan-kontainer-sewa-2.index') }}" class="ml-2 inline-block px-4 py-2 border rounded">Kembali</a>
        </div>
    </div>
</div>
@endsection
