@extends('layouts.print')

@section('content')
<div class="print-wrapper">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <strong>Pembayaran Aktivitas Lain - {{ $pembayaranAktivitasLain->nomor }}</strong>
        </div>
        <div class="small">{{ \Carbon\Carbon::parse($pembayaranAktivitasLain->tanggal)->format('d F Y') }}</div>
    </div>

    <table class="compact" style="width:100%;">
        <tbody>
            <tr>
                <th style="width:30%; text-align:left;">Nomor</th>
                <td>{{ $pembayaranAktivitasLain->nomor }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Nomor Accurate</th>
                <td>{{ $pembayaranAktivitasLain->nomor_accurate ?? '-' }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Jenis Aktivitas</th>
                <td>{{ $pembayaranAktivitasLain->jenis_aktivitas }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Penerima</th>
                <td>{{ $pembayaranAktivitasLain->penerima }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Akun Biaya</th>
                <td>{{ $akunCoas[$pembayaranAktivitasLain->akun_coa_id]->kode_nomor ?? '-' }} - {{ $akunCoas[$pembayaranAktivitasLain->akun_coa_id]->nama_akun ?? '-' }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Akun Bank</th>
                <td>{{ $akunCoas[$pembayaranAktivitasLain->akun_bank_id]->kode_nomor ?? '-' }} - {{ $akunCoas[$pembayaranAktivitasLain->akun_bank_id]->nama_akun ?? '-' }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Jumlah</th>
                <td class="numeric">Rp {{ number_format($pembayaranAktivitasLain->jumlah, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Keterangan</th>
                <td>{{ $pembayaranAktivitasLain->keterangan }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Status</th>
                <td>{{ ucfirst($pembayaranAktivitasLain->status) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
