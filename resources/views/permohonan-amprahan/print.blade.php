<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Permintaan Amprahan #{{ $permohonan->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table td:nth-child(1), .info-table td:nth-child(3) {
            font-weight: bold;
            width: 15%;
        }
        .info-table td:nth-child(2), .info-table td:nth-child(4) {
            width: 35%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .items-table th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .items-table td:nth-child(1), .items-table td:nth-child(3) {
            text-align: center;
        }
        .footer {
            width: 100%;
            margin-top: 40px;
        }
        .signatures {
            width: 100%;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .signature-box {
            width: 30%;
        }
        .signature-box .name {
            margin-top: 80px;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 150px;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .btn-print {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print btn-print">Cetak Dokumen</button>

    <div class="header">
        <h2>PERMINTAAN AMPRAHAN KAPAL</h2>
        <p style="margin:5px 0 0 0;">PT. ALEXINDO YAKINPRIMA</p>
    </div>

    <table class="info-table">
        <tr>
            <td>Kapal</td>
            <td>: {{ $permohonan->kapal->nama ?? '-' }}</td>
            <td>Tanggal Request</td>
            <td>: {{ $permohonan->created_at->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>Nomor Voyage</td>
            <td>: {{ $permohonan->nomor_voyage }}</td>
            <td>Status</td>
            <td>: {{ ucfirst($permohonan->status) }}</td>
        </tr>
        <tr>
            <td>Pemohon</td>
            <td>: {{ $permohonan->user->name ?? '-' }}</td>
            <td>Keterangan</td>
            <td>: {{ $permohonan->keterangan_umum ?: '-' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 40%;">Nama Barang</th>
                <th style="width: 15%;">Jumlah</th>
                <th style="width: 15%;">Satuan</th>
                <th style="width: 25%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permohonan->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ rtrim(rtrim(number_format($item->jumlah, 2, ',', '.'), '0'), ',') }}</td>
                <td>{{ $item->satuan }}</td>
                <td>{{ $item->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <p>Diminta Oleh,</p>
            <span class="name">{{ $permohonan->user->name ?? 'ABK / Nahkoda' }}</span>
        </div>
        <div class="signature-box">
            <p>Mengetahui,</p>
            <span class="name">Operasional</span>
        </div>
        <div class="signature-box">
            <p>Disetujui Oleh,</p>
            <span class="name">Manajemen</span>
        </div>
    </div>

    <script>
        // Auto print when page loaded
        window.onload = function() {
            // window.print(); // uncomment this to auto-print
        }
    </script>
</body>
</html>
