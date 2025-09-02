@extends('layouts.print')

@section('content')
<style>
    /* Print-friendly styles */
    .pr-container { font-family: Arial, Helvetica, sans-serif; color:#111; padding:18px; }
    .pr-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; }
    .pr-title { text-align:center; flex:1; }
    .pr-title h1 { margin:0; font-size:18px; }
    .pr-meta { font-size:13px; color:#333; }
    .pr-table { width:100%; border-collapse:collapse; margin-top:10px; font-size:13px; }
    .pr-table th, .pr-table td { padding:8px 6px; border:1px solid #ddd; }
    .pr-table thead th { background:#f6f6f8; text-align:left; }
    .pr-total { margin-top:12px; text-align:right; font-weight:700; font-size:14px; }
    .pr-footer { margin-top:28px; display:flex; justify-content:space-between; }
    .signature { width:200px; text-align:center; }

    /* hide interactive controls when printing */
    @media print {
        .no-print { display:none !important; }
        body { -webkit-print-color-adjust: exact; }
    }
</style>

<div class="pr-container">
    <div class="pr-header">
        <div style="width:140px">
            {{-- optional logo area, replace with <img> if available --}}
            <div style="font-weight:700; color:#2b3a67;">AYPSIS</div>
            <div style="font-size:11px; color:#666;">Jalan Contoh No.1 &middot; Kota</div>
        </div>

        <div class="pr-title">
            <h1>Pranota Supir</h1>
            <div style="font-size:12px; color:#444; margin-top:4px;">Ringkasan permohonan yang terkait dengan pranota ini</div>
        </div>

        <div class="pr-meta" style="text-align:right; min-width:160px;">
            <div>Nomor: <strong>{{ $pranotaSupir->nomor_pranota }}</strong></div>
            <div>Tanggal: {{ optional($pranotaSupir->tanggal_pranota)->format('d/m/Y') }}</div>
            <div>Jumlah Permohonan: <strong>{{ $pranotaSupir->permohonans->count() }}</strong></div>
        </div>
    </div>

    @php
        // build a kode_kegiatan => nama_kegiatan map to avoid N+1 queries
        $kegiatanMap = \App\Models\MasterKegiatan::pluck('nama_kegiatan', 'kode_kegiatan')->toArray();
    @endphp

    <table class="pr-table" aria-label="Daftar Permohonan">
        <thead>
            <tr>
                <th style="width:20%">Nomor Memo</th>
                <th style="width:40%">Kegiatan</th>
                <th style="width:20%">Supir</th>
                <th style="width:20%">Plat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pranotaSupir->permohonans as $p)
                <tr>
                    <td>{{ $p->nomor_memo }}</td>
                    <td>{{ $kegiatanMap[$p->kegiatan] ?? $p->kegiatan }}</td>
                    <td>{{ $p->supir->nama_lengkap ?? $p->supir->nama_panggilan ?? '-' }}</td>
                    <td>{{ $p->plat_nomor ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pr-total">Total Biaya: Rp {{ number_format($pranotaSupir->total_biaya_pranota, 2, ',', '.') }}</div>

    <div class="pr-footer">
        <div class="signature">
            <div style="height:60px"></div>
            <div>________________________________</div>
            <div>Mengetahui / Supir</div>
        </div>

        <div class="signature">
            <div style="height:60px"></div>
            <div>________________________________</div>
            <div>Penanggung Jawab</div>
        </div>
    </div>

    <div style="margin-top:14px;" class="no-print">
        <button onclick="window.print()" class="inline-block px-4 py-2 bg-blue-600 text-white rounded">Cetak</button>
        <a href="{{ route('pranota-supir.index') }}" class="inline-block px-4 py-2 border ml-2">Kembali</a>
    </div>
</div>

@endsection
