<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pranota Uang Makan - {{ $pranota->nomor_pranota }}</title>
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
        <h1>PRANOTA UANG MAKAN</h1>
        <h2>{{ $pranota->nomor_pranota }}</h2>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nomor Pranota</div>
                <div class="info-value">: {{ $pranota->nomor_pranota }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Pranota</div>
                <div class="info-value">: {{ $pranota->tanggal_pranota->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">: {{ ucfirst($pranota->status) }}</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 25%;">Karyawan</th>
                <th style="width: 15%;" class="text-center">Kehadiran</th>
                <th style="width: 15%;" class="text-right">Nominal Awal</th>
                <th style="width: 10%;" class="text-right">Adjustment</th>
                <th style="width: 15%;">Catatan</th>
                <th style="width: 15%;" class="text-right">Total Akhir</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($pranota->details as $detail)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>
                    <strong>{{ $detail->karyawan->nama_lengkap ?? '-' }}</strong><br>
                    <span style="color: #666;">{{ $detail->karyawan->nik ?? '-' }}</span>
                </td>
                <td class="text-center">{{ $detail->kehadiran }}</td>
                <td class="text-right">Rp {{ number_format($detail->nominal_awal, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($detail->adjustment, 0, ',', '.') }}</td>
                <td>{{ $detail->catatan ?? '-' }}</td>
                <td class="text-right font-bold" style="font-weight: bold;">Rp {{ number_format($detail->total_akhir, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada rincian data karyawan.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>TOTAL KESELURUHAN:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="title">Dibuat Oleh,</div>
            <div class="name">_______________</div>
        </div>
        <div class="signature-box">
            <div class="title">Disetujui Oleh,</div>
            <div class="name">_______________</div>
        </div>
        <div class="signature-box">
            <div class="title">Diterima Oleh,</div>
            <div class="name">_______________</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
