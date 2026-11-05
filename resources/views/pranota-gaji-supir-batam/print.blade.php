<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pranota Gaji Supir Batam - {{ $pranota->nomor_pranota }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.25; margin: 0; color: #000; background: #f3f4f6; }
        .print-toolbar { background: linear-gradient(135deg, #667eea, #764ba2); padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: #fff; }
        .print-toolbar h2 { margin: 0; font-size: 16px; }
        .toolbar-actions { display: flex; gap: 10px; }
        .btn { padding: 10px 18px; border: 0; border-radius: 7px; cursor: pointer; color: #fff; text-decoration: none; font-size: 13px; }
        .btn-print { background: #10b981; }
        .btn-back { background: #6b7280; }
        .print-container { max-width: 297mm; margin: 20px auto; padding: 20px; background: #fff; box-shadow: 0 4px 6px #0002; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; text-decoration: underline; }
        .info-section { margin-bottom: 15px; line-height: 1.6; }
        .info-number { float: right; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table, th, td { border: 1px solid #000; }
        th { background: #f0f0f0; padding: 6px 5px; text-align: center; font-size: 10px; }
        td { padding: 5px; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; white-space: nowrap; }
        .total-row { font-weight: bold; background: #f5f5f5; }
        .footer-notes { margin-top: 20px; white-space: pre-line; }
        .signature { margin: 55px 0 0 auto; width: 220px; text-align: center; }
        .signature-name { margin-top: 55px; border-top: 1px solid #000; padding-top: 5px; }
        @media print {
            .print-toolbar { display: none !important; }
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-container { max-width: none; margin: 0; padding: 0; box-shadow: none; border-radius: 0; }
            tr { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <h2>Print Pranota Gaji Supir Batam - {{ $pranota->nomor_pranota }}</h2>
        <div class="toolbar-actions">
            <button type="button" onclick="window.print()" class="btn btn-print">Cetak</button>
            <a href="{{ route('pranota-gaji-supir-batam.index') }}" class="btn btn-back">Kembali</a>
        </div>
    </div>
    <main class="print-container">
        <div class="header"><h1>PRANOTA GAJI SUPIR BATAM</h1></div>
        <div class="info-section">
            <div>TANGGAL PRANOTA: {{ $pranota->tanggal_pranota->format('d M Y') }}</div>
            <div class="info-number">{{ $pranota->nomor_pranota }}</div>
            <div>Jumlah supir: {{ $drivers->count() }}</div>
        </div>
        <table>
            <thead><tr>
                <th style="width:4%">No</th><th style="width:9%">NIK</th><th style="width:18%">Supir</th>
                <th>Uang Jalan</th><th>Uang Lembur</th><th>Uang Bensin</th>
                @if($showPotongan)<th>Potongan 5%</th>@endif
                <th>BPJS</th><th>PPH</th><th>Gaji Bersih</th>
            </tr></thead>
            <tbody>
                @foreach($drivers as $driver)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $driver->nik ?: '-' }}</td>
                        <td>{{ $driver->nama }}</td>
                        <td class="text-right">Rp {{ number_format((float) $driver->gaji_pokok, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float) $driver->uang_malam_libur, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float) $driver->biaya_bensin, 0, ',', '.') }}</td>
                        @if($showPotongan)
                            <td class="text-right">@if($driver->has_potongan_5_persen)Rp {{ number_format((float) $driver->potongan_5_persen, 0, ',', '.') }}@endif</td>
                        @endif
                        <td class="text-right">Rp {{ number_format((float) $driver->bpjs, 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float) $driver->pph, 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float) $driver->total_gaji, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot><tr class="total-row">
                <td colspan="{{ $showPotongan ? 9 : 8 }}" class="text-right">Total Pranota</td>
                <td class="text-right">Rp {{ number_format((float) $pranota->total_gaji, 0, ',', '.') }}</td>
            </tr></tfoot>
        </table>
        @if($pranota->keterangan)<div class="footer-notes"><strong>Keterangan:</strong> {{ $pranota->keterangan }}</div>@endif
        <div class="signature">Dibuat oleh,<div class="signature-name">{{ $pranota->creator?->name ?: '........................' }}</div></div>
    </main>
</body>
</html>
