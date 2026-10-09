@extends('layouts.app')

@section('title', 'Detail Pranota OB Muat Temas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold">Pranota OB Muat Temas {{ $pranota->nomor_pranota }}</h1>
        <div class="flex gap-3">
            <a href="{{ route('pranota-ob.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Daftar Pranota OB</a>
            <a href="{{ route('pranota-ob.muat-temas.print', $pranota) }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-teal-600 text-white rounded-lg">Cetak Pranota</a>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <p class="mb-2">Tanggal Pranota: <strong>{{ $pranota->tanggal_pranota->format('d/m/Y') }}</strong></p>
        <p class="mb-2">Kapal / Voyage: <strong>{{ $pranota->nama_kapal }} / {{ $pranota->no_voyage }}</strong></p>
        <p class="mb-4">Nomor Accurate: {{ $pranota->nomor_accurate ?? '-' }} | Dibuat Oleh: {{ $pranota->creator->name ?? '-' }}</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left"><tr>
                    <th class="p-3">Tanggal OB</th><th class="p-3">Kontainer</th><th class="p-3">Surat Jalan</th>
                    <th class="p-3">Supir</th><th class="p-3">Status</th><th class="p-3">Tujuan</th>
                    <th class="p-3">Mobil Panjang</th><th class="p-3 text-right">Biaya</th>
                </tr></thead>
                <tbody>
                    @foreach($pranota->items as $item)
                        @php($detail = $item->snapshot)
                        <tr class="border-t">
                            <td class="p-3">{{ $detail->tanggal_ob ? \Carbon\Carbon::parse($detail->tanggal_ob)->format('d/m/Y') : '-' }}</td>
                            <td class="p-3">{{ $detail->nomor_kontainer }}</td>
                            <td class="p-3">{{ $detail->nomor_surat_jalan ?? '-' }}</td>
                            <td class="p-3">{{ $detail->nama_supir }}</td>
                            <td class="p-3">{{ ucfirst($detail->status_kontainer) }}</td>
                            <td class="p-3">{{ $detail->tujuan_gudang }}</td>
                            <td class="p-3">{{ $detail->is_ckls_mobil_panjang ? 'Ya' : 'Tidak' }}</td>
                            <td class="p-3 text-right">Rp {{ number_format($detail->biaya, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-right space-y-1">
            <p>Subtotal: Rp {{ number_format($pranota->nominal, 0, ',', '.') }}</p>
            <p>Adjustment: Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}</p>
            <p class="font-bold">Total: Rp {{ number_format($pranota->grand_total, 0, ',', '.') }}</p>
        </div>
        @if($pranota->keterangan)<p class="mt-4">Catatan: {{ $pranota->keterangan }}</p>@endif
    </div>
</div>
@endsection
