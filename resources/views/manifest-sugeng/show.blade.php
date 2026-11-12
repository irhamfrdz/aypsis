@extends('layouts.app')

@section('title', 'Detail Manifest Sugeng')
@section('page_title', 'Detail Manifest Sugeng')

@section('content')
<div class="mx-auto max-w-5xl space-y-5 px-4 py-6">
    <a href="{{ route('manifest-sugeng.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">&larr; Kembali ke Manifest Sugeng</a>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div><h1 class="text-2xl font-bold text-gray-900">{{ $manifestSugeng->nomor_tanda_terima ?: 'Manifest Sugeng #'.$manifestSugeng->id }}</h1><p class="text-sm text-gray-500">{{ strtoupper($manifestSugeng->source_type) }} · Dikirim {{ $manifestSugeng->dikirim_pada?->format('d/m/Y H:i') ?: '-' }} oleh {{ $manifestSugeng->sender?->name ?: '-' }}</p></div>
            @can('manifest-sugeng-manage')
                <form method="POST" action="{{ route('manifest-sugeng.destroy', $manifestSugeng) }}" onsubmit="return confirm('Hapus data ini dari Manifest Sugeng?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Hapus</button></form>
            @endcan
        </div>
        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach(['Tanggal Tanda Terima' => $manifestSugeng->tanggal_tanda_terima?->format('d/m/Y'), 'Nomor Kontainer' => $manifestSugeng->nomor_kontainer, 'Pengirim' => $manifestSugeng->pengirim, 'Tujuan Pengiriman' => $manifestSugeng->tujuan_pengiriman, 'Shipper Manifest JB' => $manifestSugeng->shipperJb?->shipper] as $label => $value)
                <div><dt class="text-xs font-semibold uppercase text-gray-500">{{ $label }}</dt><dd class="mt-1 text-sm text-gray-900">{{ $value ?: '-' }}</dd></div>
            @endforeach
        </dl>
    </div>
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <h2 class="border-b border-gray-200 px-5 py-4 font-semibold text-gray-900">Barang Manifest</h2>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Nama Barang</th><th class="px-4 py-3">HS Code</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Satuan</th><th class="px-4 py-3">Ukuran</th><th class="px-4 py-3">Tonase</th><th class="px-4 py-3">Volume</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($manifestSugeng->manifest_goods ?? [] as $good)
                    <tr><td class="px-4 py-3">{{ data_get($good, 'nama_barang') ?: '-' }}</td><td class="px-4 py-3">{{ implode(', ', (array) data_get($good, 'hs_codes', [])) ?: (data_get($good, 'hs_code') ?: '-') }}</td><td class="px-4 py-3">{{ data_get($good, 'jumlah') ?: '-' }}</td><td class="px-4 py-3">{{ data_get($good, 'satuan') ?: '-' }}</td><td class="px-4 py-3">{{ data_get($good, 'ukuran') ?: '-' }}</td><td class="px-4 py-3">{{ data_get($good, 'tonase') ?: '-' }}</td><td class="px-4 py-3">{{ data_get($good, 'meter_kubik') ?: '-' }}</td></tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada rincian barang manifest.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
