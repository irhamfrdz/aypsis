<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRANOTA SEWA KONTAINER - {{ $pranota->nomor }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 10pt; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16pt; color: #000; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table .label { width: 130px; font-weight: bold; font-size: 9pt; }
        .info-table .separator { width: 15px; text-align: center; }
        .info-table td:not(.label):not(.separator) { font-size: 9pt; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; table-layout: fixed; border: 1px solid #000; }
        .items-table th { background-color: #f2f2f2; border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 8.5pt; text-transform: uppercase; font-weight: bold; }
        .items-table td { border: 1px solid #000; padding: 6px 4px; font-size: 8.5pt; word-wrap: break-word; vertical-align: middle; }
        
        .footer { margin-top: 40px; width: 100%; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { width: 33.33%; text-align: center; vertical-align: bottom; height: 100px; }
        .signature-line { border-bottom: 1px solid #000; width: 160px; margin: 0 auto 5px; }
        .signature-label { font-size: 9pt; font-weight: bold; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .summary-box { width: 250px; margin-left: auto; border: 1px solid #000; padding: 10px; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; font-size: 9pt; margin-bottom: 4px; }
        .summary-row.total { border-top: 1px solid #000; padding-top: 5px; font-weight: bold; font-size: 10pt; }

        @media print {
            body { margin: 5mm; }
            .no-print { display: none; }
            .header { border-bottom-color: #000; }
            .items-table th { background-color: #eee !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PRANOTA SEWA KONTAINER</h1>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nomor Pranota</td>
            <td class="separator">:</td>
            <td><strong>{{ $pranota->nomor }}</strong></td>
            <td class="label">Vendor</td>
            <td class="separator">:</td>
            <td>{{ $pranota->vendor->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Invoice Vendor</td>
            <td class="separator">:</td>
            <td>{{ $pranota->no_invoice ?? '-' }}</td>
            <td class="label">Tgl. Invoice</td>
            <td class="separator">:</td>
            <td>{{ $pranota->tgl_invoice ?? '-' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width: 15%;">UNIT</th>
                <th style="width: 15%;">PERIODE</th>
                <th style="width: 21%;">MASA SEWA</th>
                <th style="width: 10%;">AYPSIS</th>
                <th style="width: 10%;">VENDOR BILL</th>
                <th style="width: 10%;">SELISIH</th>
                <th style="width: 15%;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pranota->audits as $i => $audit)
            @php
                $periodNum = '?';
                $sAmbil = null;

                if ($audit->transaction) {
                    $sAmbil = \Carbon\Carbon::parse($audit->transaction->date_in);
                } elseif ($audit->transaction_key) {
                    // Extract excel serial from key (e.g. AMFU...45358)
                    $unitNo = $audit->unit_number;
                    $serialStr = substr($audit->transaction_key, strlen($unitNo));
                    if (is_numeric($serialStr)) {
                        $sAmbil = \Carbon\Carbon::create(1899, 12, 30)->addDays((int)$serialStr);
                    }
                }

                if ($sAmbil) {
                    try {
                        $sPeriodStr = explode(' - ', $audit->period_name)[0] ?? null;
                        if ($sPeriodStr) {
                            $sPeriod = \Carbon\Carbon::createFromFormat('d/m/Y', $sPeriodStr);
                            // Compare Year and Month diff accurately
                            $periodNum = (($sPeriod->year - $sAmbil->year) * 12) + ($sPeriod->month - $sAmbil->month) + 1;
                        }
                    } catch (\Exception $e) {}
                }
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center"><b>{{ $audit->unit_number }}</b></td>
                <td class="text-center">Bulan ke-{{ $periodNum }}</td>
                <td class="text-center">{{ $audit->period_name }}</td>
                <td class="text-right">{{ number_format($audit->aypsis_nominal, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($audit->vendor_nominal, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($audit->vendor_nominal - $audit->aypsis_nominal, 0, ',', '.') }}</td>
                <td>{{ $audit->note ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <span>DPP (Total Bill)</span>
            <span>Rp {{ number_format($pranota->dpp, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>PPN 11% (+)</span>
            <span>Rp {{ number_format($pranota->ppn, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row" style="color: red;">
            <span>PPh 2% (-)</span>
            <span>- Rp {{ number_format($pranota->pph, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row total">
            <span>GRAND TOTAL</span>
            <span>Rp {{ number_format($pranota->grand_total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Dibuat Oleh</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Diperiksa Oleh</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Disetujui Oleh</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="no-print" style="margin-top: 50px; text-align: center; gap: 10px; display: flex; justify-content: center;">
        <button onclick="window.print()" style="padding: 10px 25px; background: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Pranota
        </button>
        <button onclick="window.close()" style="padding: 10px 25px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
            Tutup
        </button>
    </div>

</body>
</html>
