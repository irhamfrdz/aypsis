@extends('layouts.print')

@section('content')
<style>
    .pr-container { font-family: Arial, sans-serif; color: #111; padding: 20px; }
    .pr-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
    .pr-title h1 { margin: 0; font-size: 22px; color: #rose-600; }
    .pr-info { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 20px; font-size: 13px; }
    .info-label { color: #666; font-size: 11px; text-transform: uppercase; margin-bottom: 2px; }
    .info-value { font-weight: bold; font-size: 14px; }
    .pr-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
    .pr-table th, .pr-table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    .pr-table th { background: #f9fafb; font-weight: bold; }
    .pr-summary { margin-top: 20px; display: flex; justify-content: flex-end; }
    .summary-box { width: 300px; }
    .summary-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #eee; }
    .summary-total { font-size: 16px; font-weight: bold; border-bottom: none; padding-top: 10px; }
    .pr-footer { margin-top: 50px; display: flex; justify-content: space-between; }
    .signature-box { width: 200px; text-align: center; }
    .signature-line { margin-top: 60px; border-top: 1px solid #333; padding-top: 5px; font-weight: bold; }
</style>

<div class="pr-container">
    <div class="pr-header">
        <div class="pr-title">
            <h1>PRANOTA INVOICE VENDOR SUPIR</h1>
            <div style="font-size: 14px; font-weight: bold; margin-top: 5px;">{{ $pranota->no_pranota }}</div>
        </div>
        <div style="text-align: right;">
            <div style="font-weight: bold; font-size: 18px; color: #2b3a67;">AYPSIS</div>
            <div style="font-size: 11px; color: #666;">Sistem Manajemen Logistik Terpadu</div>
        </div>
    </div>

    <div class="pr-info">
        <div>
            <div class="info-label">VENDOR / PERUSAHAAN</div>
            <div class="info-value">{{ $pranota->vendor->nama_vendor ?? '-' }}</div>
            <div style="font-size: 12px; margin-top: 5px; color: #444;">{{ $pranota->vendor->alamat ?? '-' }}</div>
        </div>
        <div style="text-align: right;">
            <div class="info-label">TANGGAL PRANOTA</div>
            <div class="info-value">{{ $pranota->tanggal_pranota->format('d F Y') }}</div>
            <div class="info-label" style="margin-top: 10px;">STATUS</div>
            <div class="info-value" style="text-transform: uppercase;">{{ str_replace('_', ' ', $pranota->status_pembayaran) }}</div>
        </div>
    </div>

    @if($pranota->keterangan)
    <div style="margin-bottom: 20px; padding: 10px; background: #f9fafb; border: 1px solid #eee; font-size: 12px;">
        <div class="info-label">KETERANGAN</div>
        <div>{{ $pranota->keterangan }}</div>
    </div>
    @endif

    <table class="pr-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>No Invoice</th>
                <th>Tanggal</th>
                <th>Surat Jalan Relasi</th>
                <th style="text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pranota->invoiceTagihanVendors as $index => $invoice)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-weight: bold;">{{ $invoice->no_invoice }}</td>
                <td>{{ $invoice->tanggal_invoice->format('d/m/Y') }}</td>
                <td>
                    <div style="font-size: 10px; color: #555;">
                        @foreach($invoice->tagihanSupirVendors as $tagihan)
                            {{ $tagihan->suratJalan->no_surat_jalan ?? '-' }}@if(!$loop->last), @endif
                        @endforeach
                    </div>
                </td>
                <td style="text-align: right; font-weight: bold;">
                    Rp {{ number_format($invoice->total_nominal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pr-summary">
        <div class="summary-box">
            <div class="summary-row summary-total">
                <span>TOTAL KESELURUHAN</span>
                <span style="color: #rose-600;">Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="pr-footer">
        <div class="signature-box">
            <div>Dibuat Oleh,</div>
            <div class="signature-line">{{ optional($pranota->creator)->name ?? 'Admin' }}</div>
            <div style="font-size: 10px;">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
        <div class="signature-box">
            <div>Vendor / Penerima,</div>
            <div class="signature-line">{{ $pranota->vendor->nama_vendor ?? '-' }}</div>
        </div>
        <div class="signature-box">
            <div>Disetujui Oleh,</div>
            <div class="signature-line">&nbsp;</div>
            <div>Manager Keuangan</div>
        </div>
    </div>
</div>
@endsection
