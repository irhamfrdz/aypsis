<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pranota LOLO Batam - {{ $pranota->nomor_tagihan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            margin: 4px 0 0;
            font-size: 14px;
            font-weight: normal;
            color: #555;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 18%;
            font-weight: bold;
            color: #555;
        }
        .info-table td.val {
            width: 32%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
        }
        .items-table th {
            background-color: #f5f5f5;
            text-transform: uppercase;
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .total-row td {
            background-color: #f9f9f9;
            font-weight: bold;
            font-size: 13px;
        }
        .footer-signatures {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .footer-signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 40px;
        }
        .sign-space {
            height: 70px;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
    </div>

    <div class="header">
        <h1>PT. ALEXINDO YAKINPRIMA</h1>
        <h2>PRANOTA BIAYA LOLO (LIFT-ON / LIFT-OFF) BATAM</h2>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nomor Pranota:</td>
            <td class="val font-bold">{{ $pranota->nomor_tagihan }}</td>
            <td class="label">Vendor / Depo:</td>
            <td class="val">{{ $pranota->vendor ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal:</td>
            <td class="val">{{ $pranota->tanggal_tagihan ? $pranota->tanggal_tagihan->format('d/m/Y') : '-' }}</td>
            <td class="label">Operator LOLO:</td>
            <td class="val">
                @if($pranota->tipe_operator === 'AYP')
                    AYP - {{ $pranota->operator ?: ($pranota->operatorKaryawan->nama_lengkap ?? '-') }}
                @elseif($pranota->tipe_operator === 'VENDOR')
                    Vendor - {{ $pranota->operator ?: ($pranota->vendor ?: '-') }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Kapal / Voyage:</td>
            <td class="val">{{ $pranota->kapal ?: '-' }} {{ $pranota->voyage ? '(' . $pranota->voyage . ')' : '' }}</td>
            <td class="label">Tanggal Bayar:</td>
            <td class="val">{{ $pranota->tanggal_bayar ? $pranota->tanggal_bayar->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td class="val font-bold" colspan="3">{{ $pranota->status_pembayaran }}</td>
        </tr>
        @if($pranota->keterangan)
        <tr>
            <td class="label">Keterangan:</td>
            <td class="val" colspan="3">{{ $pranota->keterangan }}</td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th class="text-left">Nomor Kontainer</th>
                <th class="text-center" style="width: 50px;">Size</th>
                <th class="text-center" style="width: 60px;">Tipe</th>
                <th class="text-left">No. Surat Jalan / Dokumen</th>
                <th class="text-left">Kegiatan</th>
                <th class="text-right" style="width: 100px;">Tarif (Rp)</th>
                <th class="text-center" style="width: 40px;">Qty</th>
                <th class="text-right" style="width: 120px;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pranota->items as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="font-bold">{{ $item->nomor_kontainer }}</td>
                <td class="text-center">{{ $item->size ?: '20' }}'</td>
                <td class="text-center">{{ $item->tipe_kontainer ?: 'FULL' }}</td>
                <td>{{ $item->nomor_surat_jalan ?: '-' }} ({{ ucfirst($item->sumber_data) }})</td>
                <td>{{ $item->kegiatan ?: 'LOLO Batam' }}</td>
                <td class="text-right">{{ number_format($item->tarif, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->jumlah }}</td>
                <td class="text-right font-bold">{{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">TOTAL:</td>
                <td class="text-center">{{ $pranota->items->sum('jumlah') }}</td>
                <td class="text-right">{{ $pranota->formatted_total_tagihan }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="footer-signatures">
        <tr>
            <td>
                <p>Dibuat Oleh,</p>
                <div class="sign-space"></div>
                <p class="font-bold">({{ $pranota->createdBy ? ($pranota->createdBy->name ?? $pranota->createdBy->username) : '..........................' }})</p>
                <p style="font-size: 10px; color: #777;">Operasional Batam</p>
            </td>
            <td>
                <p>Disetujui Oleh,</p>
                <div class="sign-space"></div>
                <p class="font-bold">(..................................................)</p>
                <p style="font-size: 10px; color: #777;">Finance / Management</p>
            </td>
        </tr>
    </table>
</body>
</html>
