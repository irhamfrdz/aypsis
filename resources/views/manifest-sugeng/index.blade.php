@extends('layouts.app')

@section('title', 'Manifest Sugeng')
@section('page_title', 'Manifest Sugeng')

@section('content')
<div class="mx-auto max-w-full space-y-5 px-4 py-6">
    <div class="flex flex-wrap items-start justify-between gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div><h1 class="text-2xl font-bold text-gray-900">Manifest Sugeng</h1><p class="text-sm text-gray-600">Manifest & Perincian dari Approval Tanda Terima 2</p>
            @if(request('nama_kapal') && request('no_voyage'))<p class="mt-2 font-semibold text-indigo-700">{{ request('nama_kapal') }} · Voyage {{ request('no_voyage') }}</p>@endif
        </div>
        @if(request('nama_kapal') && request('no_voyage'))<a href="{{ route('manifest-sugeng.export', ['nama_kapal' => request('nama_kapal'), 'no_voyage' => request('no_voyage')]) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export Excel Manifest & Perincian</a>@endif
    </div>
    @if(session('success'))<div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    <form method="GET" class="grid gap-3 rounded-xl border border-gray-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-5">
        <input name="search" value="{{ request('search') }}" placeholder="Cari BL, tanda terima, kontainer, pengirim" class="rounded-lg border-gray-300 text-sm lg:col-span-2">
        <input name="nama_kapal" list="manifest-sugeng-ships" value="{{ request('nama_kapal') }}" placeholder="Nama kapal" class="rounded-lg border-gray-300 text-sm">
        <datalist id="manifest-sugeng-ships">@foreach($ships as $ship)<option value="{{ $ship }}">@endforeach</datalist>
        <input name="no_voyage" list="manifest-sugeng-voyages" value="{{ request('no_voyage') }}" placeholder="No. voyage" class="rounded-lg border-gray-300 text-sm">
        <datalist id="manifest-sugeng-voyages">@foreach($voyages as $voyage)<option value="{{ $voyage }}">@endforeach</datalist>
        <div class="flex gap-2"><select name="type" class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm"><option value="">Semua jenis</option>@foreach(['fcl' => 'FCL', 'lcl' => 'LCL', 'ttsj' => 'TTSJ'] as $value => $label)<option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>@endforeach</select><button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Cari</button></div>
    </form>
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-[2600px] divide-y divide-gray-200 text-xs">
            <thead class="bg-slate-100 text-left font-semibold uppercase text-slate-700">
                <tr><th colspan="5" class="border-r border-gray-300 px-3 py-2">Dokumen & Kontainer</th><th colspan="4" class="border-r border-gray-300 px-3 py-2">Manifest</th><th colspan="3" class="border-r border-gray-300 px-3 py-2">Perincian</th><th colspan="2" class="border-r border-gray-300 px-3 py-2">Berat & Volume</th><th colspan="3" class="border-r border-gray-300 px-3 py-2">Shipper</th><th colspan="3" class="border-r border-gray-300 px-3 py-2">Consignee</th><th colspan="3" class="border-r border-gray-300 px-3 py-2">Notify Party</th><th colspan="4" class="px-3 py-2">Tanda Terima</th></tr>
                <tr class="bg-slate-50 text-[10px] text-gray-600">@foreach(['No. BL','HS Code','Mark / Kontainer','Seal','Tipe / Size','Jumlah','Satuan','Nama Barang','Keterangan','Jumlah','Satuan','Nama Barang','Weight','Meass','Shipper','Alamat','NPWP','Consignee','Alamat','NPWP','Notify Party','Alamat','NPWP','No. Tanda Terima','Tanggal','Kapal / Voyage','Aksi'] as $heading)<th class="min-w-24 border-t border-gray-200 px-3 py-3">{{ $heading }}</th>@endforeach</tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                    @php
                        $manifest = collect($item->manifest_goods ?? [])->first() ?? [];
                        $detail = collect($item->perincian_barang ?? [])->first() ?? [];
                        $shipper = $item->shipperJb;
                        $hsCodes = (array) (data_get($manifest, 'hs_codes') ?: (data_get($manifest, 'hs_code') ? [data_get($manifest, 'hs_code')] : []));
                        $cells = [
                            $item->nomor_bl, implode(', ', $hsCodes), $item->nomor_kontainer, $item->no_seal,
                            trim($item->tipe_kontainer.' '.$item->size_kontainer), data_get($manifest, 'jumlah'),
                            data_get($manifest, 'satuan'), data_get($manifest, 'nama_barang'), data_get($manifest, 'keterangan_barang'),
                            data_get($detail, 'jumlah'), data_get($detail, 'satuan'), data_get($detail, 'nama_barang'),
                            data_get($manifest, 'tonase'), data_get($manifest, 'meter_kubik'),
                            $shipper?->shipper ?: $item->pengirim, $shipper?->alamat_shipper, $shipper?->npwp_shipper,
                            $shipper?->consignee, $shipper?->alamat_consignee, $shipper?->npwp_consignee,
                            $shipper?->notify_party_consignee, $shipper?->alamat_notify_party_consignee, $shipper?->npwp_notify_party_consignee,
                            $item->nomor_tanda_terima, $item->tanggal_tanda_terima?->format('d/m/Y'),
                            trim($item->nama_kapal.' / '.$item->no_voyage, ' /'),
                        ];
                    @endphp
                    <tr class="align-top hover:bg-indigo-50/40">@foreach($cells as $cell)<td class="max-w-64 whitespace-pre-line px-3 py-3">{{ $cell === null || $cell === '' ? '-' : $cell }}</td>@endforeach<td class="px-3 py-3"><a href="{{ route('manifest-sugeng.show', $item) }}" class="font-semibold text-indigo-600 hover:underline">Lihat / Edit</a></td></tr>
                @empty
                    <tr><td colspan="27" class="px-4 py-10 text-center text-gray-500">Belum ada data Manifest Sugeng.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
