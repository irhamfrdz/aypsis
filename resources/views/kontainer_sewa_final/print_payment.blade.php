<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BUKTI PEMBAYARAN - {{ $payment->nomor_pembayaran }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 13px; color: #333; margin: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #f2f2f2; border: 1px solid #000; padding: 8px; text-align: left; }
        .items-table td { border: 1px solid #000; padding: 8px; }
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .summary-table td { padding: 5px 0; }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        .sign-box { text-align: center; width: 200px; }
        .sign-box p { margin: 0 0 70px 0; }
        .sign-box .name { font-weight: bold; text-decoration: underline; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="header">
    <h2>Permohonan Transfer</h2>
</div>

<table class="info-table">
    <tr>
        <td width="180">Nomor Pembayaran</td>
        <td width="20">:</td>
        <td><strong>{{ $payment->nomor_pembayaran }}</strong></td>
        <td width="150" align="right">Tanggal Bayar</td>
        <td width="20" align="center">:</td>
        <td width="120"><strong>{{ date('d-M-Y', strtotime($payment->tanggal_pembayaran)) }}</strong></td>
    </tr>
    <tr>
        <td>Bank / Kas</td>
        <td>:</td>
        <td>{{ $payment->bank }}</td>
        <td align="right">Nomor Accurate</td>
        <td align="center">:</td>
        <td>{{ $payment->nomor_accurate ?: '-' }}</td>
    </tr>
    <tr>
        <td>Keterangan</td>
        <td>:</td>
        <td colspan="4">{{ $payment->keterangan ?: '-' }}</td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th width="30">No</th>
            <th>Nomor Invoice Vendor</th>
            <th>Vendor</th>
            <th align="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payment->details as $index => $detail)
        <tr>
            <td align="center">{{ $index + 1 }}</td>
            <td>{{ $detail->pranota->no_invoice ?: '-' }}</td>
            <td>{{ $detail->pranota->vendor->name ?? '-' }}</td>
            <td align="right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" align="right"><strong>TOTAL PRANOTA</strong></td>
            <td align="right"><strong>Rp {{ number_format($payment->total_pembayaran, 0, ',', '.') }}</strong></td>
        </tr>
        @if($payment->total_penyesuaian != 0)
        <tr>
            <td colspan="3" align="right">
                PENYESUAIAN 
                @if($payment->alasan_penyesuaian) <br><small>({{ $payment->alasan_penyesuaian }})</small> @endif
            </td>
            <td align="right" style="color: {{ $payment->total_penyesuaian < 0 ? 'red' : 'black' }}">
                Rp {{ number_format($payment->total_penyesuaian, 0, ',', '.') }}
            </td>
        </tr>
        @endif
        <tr style="font-size: 1.1em;">
            <td colspan="3" align="right"><strong>GRAND TOTAL</strong></td>
            <td align="right" style="background: #f2f2f2;"><strong>Rp {{ number_format($payment->grand_total, 0, ',', '.') }}</strong></td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    <div class="sign-box">
        <p>Disiapkan Oleh,</p>
        <span class="name">{{ $payment->creator->name ?? 'Staff Finance' }}</span>
    </div>
    <div class="sign-box">
        <p>Kasir/Keuangan,</p>
        <span class="name">____________________</span>
    </div>
    <div class="sign-box">
        <p>Mengetahui,</p>
        <span class="name">____________________</span>
    </div>
</div>

<div class="no-print" style="margin-top: 50px; text-align: center;">
    <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; cursor: pointer; border-radius: 5px;">Cetak Ulang</button>
    <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; cursor: pointer; border-radius: 5px;">Tutup</button>
</div>

<!-- HALAMAN 2: DETAIL KONTAINER -->
<div style="page-break-before: always; margin-top: 30px;">
    <div class="header">
        <h2>Lampiran Detail Kontainer</h2>
        <p>Nomor Pembayaran: {{ $payment->nomor_pembayaran }}</p>
    </div>

    @foreach($payment->details as $detail)
    <div style="margin-bottom: 30px;">
        <h4 style="margin-bottom: 5px; background: #f2f2f2; padding: 5px;">Pranota: {{ $detail->pranota->nomor }} (Invoice: {{ $detail->pranota->no_invoice ?: '-' }})</h4>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>Nomor Kontainer</th>
                    <th>Masa Sewa</th>
                    <th align="right">AYPSIS</th>
                    <th align="right">Vendor Bill</th>
                    <th align="right">Selisih</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail->pranota->audits as $idx => $audit)
                <tr>
                    <td align="center">{{ $idx + 1 }}</td>
                    <td>{{ $audit->unit_number }}</td>
                    <td>{{ $audit->period_name }}</td>
                    <td align="right">{{ number_format($audit->aypsis_nominal, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($audit->vendor_nominal, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($audit->vendor_nominal - $audit->aypsis_nominal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" align="right"><strong>Subtotal Pranota</strong></td>
                    <td align="right"><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach

    <div style="margin-top: 20px; border-top: 2px solid #000; padding-top: 10px; text-align: right;">
        <p style="font-size: 1.2em;"><strong>Grand Total: Rp {{ number_format($payment->grand_total, 0, ',', '.') }}</strong></p>
    </div>
</div>

</body>
</html>
