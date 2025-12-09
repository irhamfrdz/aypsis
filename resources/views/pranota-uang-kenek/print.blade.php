<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RITASI KENEK - {{ $pranotaUangKenek->no_pranota }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .header h2 {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 10px;
        }
        
        .document-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 11px;
        }
        
        .document-info div {
            flex: 1;
        }
        
        .document-info .right {
            text-align: right;
        }
        
        .main-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 25px;
            font-size: 11px;
        }
        
        .info-group h4 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 4px;
        }
        
        .info-item .label {
            min-width: 100px;
            font-weight: bold;
        }
        
        .info-item .separator {
            margin: 0 8px;
        }
        
        .info-item .value {
            flex: 1;
        }
        
        .table-container {
            margin: 25px 0;
        }
        
        .table-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
            background-color: #f5f5f5;
            padding: 8px;
            border: 1px solid #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
            font-size: 11px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 50px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .signature-box .name {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 40px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .header {
                page-break-inside: avoid;
            }
            
            .table-container {
                page-break-inside: avoid;
            }
            
            .signatures {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RITASI KENEK</h1>
        <h2>PT. AYPSIS INDONESIA</h2>
        <div style="font-size: 11px; margin-top: 5px;">
            Jl. Contoh Alamat No. 123, Jakarta
        </div>
    </div>

    <div class="document-info">
        <div>
            <strong>No. Pranota:</strong> {{ $pranotaUangKenek->no_pranota }}
        </div>
        <div class="right">
            <strong>Tanggal:</strong> {{ $pranotaUangKenek->tanggal ? \Carbon\Carbon::parse($pranotaUangKenek->tanggal)->format('d/m/Y') : '-' }}
        </div>
    </div>

    <div class="main-info">
        <div class="info-group">
            <h4>INFORMASI PRANOTA</h4>
            <div class="info-item">
                <span class="label">Status</span>
                <span class="separator">:</span>
                <span class="value">
                    @if($pranotaUangKenek->status === 'draft')
                        DRAFT
                    @elseif($pranotaUangKenek->status === 'submitted')
                        SUBMITTED
                    @elseif($pranotaUangKenek->status === 'approved')
                        APPROVED
                    @elseif($pranotaUangKenek->status === 'paid')
                        PAID
                    @else
                        {{ strtoupper($pranotaUangKenek->status) }}
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="label">Dibuat Oleh</span>
                <span class="separator">:</span>
                <span class="value">{{ $pranotaUangKenek->createdBy->name ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Tanggal Dibuat</span>
                <span class="separator">:</span>
                <span class="value">{{ $pranotaUangKenek->created_at ? $pranotaUangKenek->created_at->format('d/m/Y H:i') : '-' }}</span>
            </div>
        </div>
        
        <div class="info-group">
            <h4>TOTAL PEMBAYARAN</h4>
            <div class="info-item">
                <span class="label">Total Keseluruhan</span>
                <span class="separator">:</span>
                <span class="value text-bold">Rp {{ number_format($pranotaUangKenek->total_uang, 0, ',', '.') }}</span>
            </div>
            @if($pranotaUangKenek->status === 'paid' && $pranotaUangKenek->tanggal_bayar)
            <div class="info-item">
                <span class="label">Tanggal Bayar</span>
                <span class="separator">:</span>
                <span class="value">{{ \Carbon\Carbon::parse($pranotaUangKenek->tanggal_bayar)->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="table-container">
        <div class="table-title">DETAIL RITASI KENEK</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">No. Surat Jalan</th>
                    <th style="width: 40%;">Nama Kenek</th>
                    <th style="width: 30%;">Uang Rit (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @if(count($kenekDetails) > 0)
                    @foreach($kenekDetails as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail['no_surat_jalan'] }}</td>
                        <td>{{ $detail['kenek_nama'] }}</td>
                        <td class="text-right">{{ number_format($detail['uang_rit'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $pranotaUangKenek->no_surat_jalan }}</td>
                        <td>{{ $pranotaUangKenek->kenek_nama }}</td>
                        <td class="text-right">{{ number_format($pranotaUangKenek->uang_rit_kenek, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" class="text-center text-bold">TOTAL KESELURUHAN</td>
                    <td class="text-right text-bold">{{ number_format($pranotaUangKenek->total_uang, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($pranotaUangKenek->keterangan)
    <div class="info-group" style="margin-bottom: 25px;">
        <h4>KETERANGAN</h4>
        <div style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
            {{ $pranotaUangKenek->keterangan }}
        </div>
    </div>
    @endif

    <div class="signatures">
        <div class="signature-box">
            <div class="title">Dibuat Oleh</div>
            <div class="name">{{ $pranotaUangKenek->createdBy->name ?? '-' }}</div>
        </div>
        <div class="signature-box">
            <div class="title">Disetujui Oleh</div>
            <div class="name">{{ $pranotaUangKenek->approvedBy->name ?? '..............................' }}</div>
        </div>
        <div class="signature-box">
            <div class="title">Penerima</div>
            <div class="name">..............................</div>
        </div>
    </div>

    <div class="footer">
        <div>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</div>
        <div>Dokumen ini dicetak secara otomatis dari sistem</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>