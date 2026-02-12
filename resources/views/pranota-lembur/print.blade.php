<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pranota Lembur - {{ $pranotaLembur->nomor_pranota }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 3px 10px 3px 0;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        table td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 10px;
        }
        table tfoot td {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-lembur {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-nginap {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-muat {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-bongkar {
            background-color: #fed7aa;
            color: #9a3412;
        }
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .signature-box .name {
            font-weight: bold;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
            min-width: 150px;
        }
        .catatan-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 15px;
            background-color: #fffbea;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Cetak
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ✖️ Tutup
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>PRANOTA LEMBUR/NGINAP</h1>
        <h2>{{ $pranotaLembur->nomor_pranota }}</h2>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nomor Pranota</div>
                <div class="info-value">: {{ $pranotaLembur->nomor_pranota }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Pranota</div>
                <div class="info-value">: {{ $pranotaLembur->tanggal_pranota->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">: {{ $pranotaLembur->status_label }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dibuat Oleh</div>
                <div class="info-value">: {{ $pranotaLembur->creator->name ?? '-' }}</div>
            </div>
            @if($pranotaLembur->approved_by)
            <div class="info-row">
                <div class="info-label">Disetujui Oleh</div>
                <div class="info-value">: {{ $pranotaLembur->approver->name ?? '-' }} ({{ $pranotaLembur->approved_at ? $pranotaLembur->approved_at->format('d/m/Y') : '' }})</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Catatan -->
    @if($pranotaLembur->catatan)
    <div class="catatan-box">
        <strong>Catatan:</strong> {{ $pranotaLembur->catatan }}
    </div>
    @endif

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Tgl TT</th>
                <th style="width: 7%;">Tipe</th>
                <th style="width: 12%;">No Surat Jalan</th>
                <th style="width: 15%;">Supir</th>
                <th style="width: 10%;">No Plat</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 12%;" class="text-right">Biaya Lembur</th>
                <th style="width: 12%;" class="text-right">Biaya Nginap</th>
                <th style="width: 11%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $totalBiaya = 0; @endphp
            @foreach($pranotaLembur->suratJalans as $sj)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $sj->tandaTerima ? $sj->tandaTerima->tanggal->format('d/m/Y') : '-' }}</td>
                <td><span class="badge badge-muat">Muat</span></td>
                <td>{{ $sj->no_surat_jalan }}</td>
                <td>{{ $sj->pivot->supir }}</td>
                <td>{{ $sj->pivot->no_plat }}</td>
                <td>
                    @if($sj->pivot->is_lembur) <span class="badge badge-lembur">Lembur</span> @endif
                    @if($sj->pivot->is_nginap) <span class="badge badge-nginap">Nginap</span> @endif
                </td>
                <td class="text-right">Rp {{ number_format($sj->pivot->biaya_lembur, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sj->pivot->biaya_nginap, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sj->pivot->total_biaya, 0, ',', '.') }}</td>
            </tr>
            @php $totalBiaya += $sj->pivot->total_biaya; @endphp
            @endforeach
            @foreach($pranotaLembur->suratJalanBongkarans as $sj)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $sj->tandaTerima ? $sj->tandaTerima->tanggal_tanda_terima->format('d/m/Y') : '-' }}</td>
                <td><span class="badge badge-bongkar">Bongkar</span></td>
                <td>{{ $sj->nomor_surat_jalan }}</td>
                <td>{{ $sj->pivot->supir }}</td>
                <td>{{ $sj->pivot->no_plat }}</td>
                <td>
                    @if($sj->pivot->is_lembur) <span class="badge badge-lembur">Lembur</span> @endif
                    @if($sj->pivot->is_nginap) <span class="badge badge-nginap">Nginap</span> @endif
                </td>
                <td class="text-right">Rp {{ number_format($sj->pivot->biaya_lembur, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sj->pivot->biaya_nginap, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sj->pivot->total_biaya, 0, ',', '.') }}</td>
            </tr>
            @php $totalBiaya += $sj->pivot->total_biaya; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text-right">TOTAL BIAYA:</td>
                <td class="text-right">Rp {{ number_format($pranotaLembur->total_biaya, 0, ',', '.') }}</td>
            </tr>
            @if($pranotaLembur->adjustment != 0)
            <tr>
                <td colspan="9" class="text-right">
                    ADJUSTMENT
                    @if($pranotaLembur->alasan_adjustment)
                        <br><small>({{ $pranotaLembur->alasan_adjustment }})</small>
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($pranotaLembur->adjustment, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="9" class="text-right" style="font-size: 12px;">GRAND TOTAL:</td>
                <td class="text-right" style="font-size: 12px;">Rp {{ number_format($pranotaLembur->total_setelah_adjustment, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tfoot>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="title">Dibuat Oleh,</div>
            <div class="name">{{ $pranotaLembur->creator->name ?? '_______________' }}</div>
        </div>
        <div class="signature-box">
            <div class="title">Disetujui Oleh,</div>
            <div class="name">{{ $pranotaLembur->approver->name ?? '_______________' }}</div>
        </div>
        <div class="signature-box">
            <div class="title">Diterima Oleh,</div>
            <div class="name">_______________</div>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
