<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half-Folio');
    $paperMap = [
        'Half-Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '195.9mm', // width - 2*margin
            'fontSize' => '9.5px',
            'headerH1' => '14px',
            'tableFont' => '8.5px',
            'signatureBottom' => '3mm'
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '190mm', // width - 2*margin
            'fontSize' => '11px',
            'headerH1' => '16px',
            'tableFont' => '10px',
            'signatureBottom' => '5mm'
        ],
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '195.9mm',
            'fontSize' => '11px',
            'headerH1' => '16px',
            'tableFont' => '10px',
            'signatureBottom' => '5mm'
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Print Pranota LOLO Batam - {{ $pranota->nomor_tagihan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }} portrait;
            margin: 8mm;
        }

        html {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.35;
            color: #111;
            background: white;
            position: relative;
            width: {{ $currentPaper['width'] }};
            margin: 0;
            padding: 0;
        }

        .container {
            width: {{ $currentPaper['containerWidth'] }};
            max-width: {{ $currentPaper['containerWidth'] }};
            margin: 0 auto;
            padding: 0;
            position: relative;
            box-sizing: border-box;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9.5px;
            margin-top: 4px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2.5px 4px;
            vertical-align: top;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .info-table td.label {
            width: 16%;
            font-weight: bold;
            color: #444;
        }

        .info-table td.separator {
            width: 2%;
            text-align: center;
        }

        .info-table td.val {
            width: 32%;
        }

        .table-container {
            margin: 8px 0;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        table.items-table th, 
        table.items-table td {
            border: 1px solid #333;
            padding: 3px 4px;
            font-size: {{ $currentPaper['tableFont'] }};
            vertical-align: middle;
            word-wrap: break-word;
            line-height: 1.2;
        }

        table.items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .total-row td {
            background-color: #f5f5f5 !important;
            font-weight: bold !important;
            border-top: 2px solid #333 !important;
        }

        .keterangan-section {
            margin: 6px 0;
            padding: 5px 8px;
            border: 1px dashed #777;
            background-color: #fafafa;
            font-size: 8.5px;
        }

        .signature-section {
            margin-top: 15px;
            page-break-inside: avoid;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 40px;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 130px;
            margin: 0 auto 3px;
        }

        .signature-name {
            font-size: 8.5px;
            font-weight: bold;
        }

        .signature-title {
            font-size: 7.5px;
            color: #555;
        }

        .no-print {
            display: block;
        }

        @media print {
            @page {
                size: {{ $currentPaper['size'] }} portrait;
                margin: 8mm;
            }

            .no-print {
                display: none !important;
            }

            html, body {
                width: {{ $currentPaper['width'] }};
                margin: 0;
                padding: 0;
            }

            .container {
                width: {{ $currentPaper['containerWidth'] }};
                margin: 0 auto;
                padding: 0;
                box-sizing: border-box;
            }

            table.items-table th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .total-row td {
                background-color: #f5f5f5 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <!-- Print Instructions Banner (hidden when printing) -->
    <div class="no-print" style="position: fixed; top: 10px; left: 10px; right: 10px; background: #fef3c7; padding: 10px 15px; border: 2px solid #f59e0b; border-radius: 8px; z-index: 1001; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-size: 11px;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <strong>⚠️ PENTING - Setting Print:</strong><br>
                1. Scale: <strong>100%</strong> | 2. Orientation: <strong>Portrait</strong> | 3. Margin: <strong>Default / Minimal</strong><br>
                Ukuran default: <strong>Half-Folio (8.5 x 6.5 in)</strong>. Potong kertas Folio secara horizontal setelah dicetak.
            </div>
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <div style="background: white; padding: 4px 8px; border-radius: 6px; border: 1px solid #d1d5db; display: flex; align-items: center; gap: 5px;">
                    <span style="font-weight: bold; color: #374151;">Ukuran:</span>
                    <a href="?paper_size=Half-Folio" style="text-decoration: none; padding: 3px 8px; border-radius: 4px; font-size: 10px; {{ $paperSize === 'Half-Folio' ? 'background: #4f46e5; color: white;' : 'background: #e5e7eb; color: #4b5563;' }}">Half-Folio</a>
                    <a href="?paper_size=A4" style="text-decoration: none; padding: 3px 8px; border-radius: 4px; font-size: 10px; {{ $paperSize === 'A4' ? 'background: #4f46e5; color: white;' : 'background: #e5e7eb; color: #4b5563;' }}">A4</a>
                    <a href="?paper_size=Folio" style="text-decoration: none; padding: 3px 8px; border-radius: 4px; font-size: 10px; {{ $paperSize === 'Folio' ? 'background: #4f46e5; color: white;' : 'background: #e5e7eb; color: #4b5563;' }}">Folio</a>
                </div>
                <button onclick="window.print()" style="background: #4f46e5; color: white; border: none; padding: 7px 14px; border-radius: 6px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 5px; font-size: 11px;">
                    🖨️ CETAK
                </button>
                <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 7px 12px; border-radius: 6px; cursor: pointer; font-size: 11px;">
                    TUTUP
                </button>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top: 75px;">
        <!-- Header -->
        <div class="header">
            <div class="header-meta">
                <span><strong>No. Pranota: {{ $pranota->nomor_tagihan }}</strong></span>
                <span><strong>Tanggal: {{ $pranota->tanggal_tagihan ? $pranota->tanggal_tagihan->format('d/m/Y') : '-' }}</strong></span>
            </div>
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

        <!-- Items Table -->
        <div class="table-container">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 4%">No</th>
                        <th style="width: 22%">No. Kontainer</th>
                        <th style="width: 8%">Size</th>
                        <th style="width: 8%">Tipe</th>
                        <th style="width: 22%">No. SJ / Dokumen</th>
                        <th style="width: 16%">Operator</th>
                        <th style="width: 10%">Tarif (Rp)</th>
                        <th style="width: 10%">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pranota->items as $idx => $item)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-bold text-center">{{ $item->nomor_kontainer }}</td>
                        <td class="text-center">{{ $item->size ?: '20' }}'</td>
                        <td class="text-center">{{ $item->tipe_kontainer ?: 'FULL' }}</td>
                        <td>
                            {{ $item->nomor_surat_jalan ?: '-' }}
                            <span style="font-size: 7.5px; color: #555;">({{ ucfirst($item->sumber_data) }})</span>
                        </td>
                        <td style="font-size: 8px;">
                            @if($item->tipe_operator === 'AYP')
                                AYP: {{ $item->operator ?: ($item->operatorKaryawan->nama_lengkap ?? 'Operator AYP') }}
                            @elseif($item->tipe_operator === 'VENDOR')
                                Vendor: {{ $item->operator ?: 'Vendor' }}
                            @else
                                {{ $item->operator ?: '-' }}
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->tarif, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">{{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="7" class="text-right font-bold">TOTAL TAGIHAN:</td>
                        <td class="text-right font-bold">Rp {{ number_format($pranota->total_tagihan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($pranota->keterangan)
        <div class="keterangan-section">
            <strong>Catatan Tambahan:</strong> {{ $pranota->keterangan }}
        </div>
        @endif

        <!-- Signatures Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-label">Dibuat Oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $pranota->createdBy ? ($pranota->createdBy->name ?? $pranota->createdBy->username) : 'Operasional' }}</div>
                        <div class="signature-title">Operasional Batam</div>
                    </td>
                    <td>
                        <div class="signature-label">Diperiksa Oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">( .................................... )</div>
                        <div class="signature-title">Finance / Accounting</div>
                    </td>
                    <td>
                        <div class="signature-label">Disetujui Oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">( .................................... )</div>
                        <div class="signature-title">Direksi / Management</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
