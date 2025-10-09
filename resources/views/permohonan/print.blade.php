<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Memo Surat Jalan - {{ $permohonan->nomor_memo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
            background: white;
            width: 100vw;
            height: 100vh;
            box-sizing: border-box;
        }

        .print-container {
            max-width: 100%;
            width: 100%;
            height: 100vh;
            margin: 0;
            background: white;
            border: 1px solid #000;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            padding: 2mm;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 1mm;
            background: #f8f9fa;
            flex-shrink: 0;
        }

        .header h1 {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 1px 0 0 0;
            font-size: 9px;
            color: #666;
        }

        .memo-info {
            display: flex;
            justify-content: space-between;
            padding: 2mm;
            border-bottom: 1px solid #ddd;
            background: #fff;
            gap: 5mm;
            flex-shrink: 0;
        }

        .memo-left, .memo-right {
            width: 48%;
            flex: 1;
        }

        .info-row {
            display: flex;
            margin-bottom: 4px;
            align-items: center;
            padding: 1px 0;
        }

        .info-label {
            font-weight: bold;
            width: 70px;
            flex-shrink: 0;
            font-size: 10px;
            padding-right: 5px;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px solid #333;
            min-height: 14px;
            padding-left: 3px;
            padding-top: 1px;
            padding-bottom: 1px;
            font-size: 10px;
        }

        .content-section {
            padding: 1mm;
            flex: 1;
        }

        .section-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 2px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1mm;
            font-size: 10px;
            height: 100%;
        }

        th, td {
            border: 1px solid #333;
            padding: 1mm;
            text-align: left;
            min-height: 15mm;
        }

        th {
            text-align: center;
            padding: 2px 1px;
            font-weight: bold;
            font-size: 10px;
            line-height: 1;
            height: 4mm;
            vertical-align: middle;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 2mm;
            padding: 1mm;
            flex-shrink: 0;
        }

        .signature-box {
            text-align: center;
            width: 120px;
            font-size: 8px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 25px;
            margin-bottom: 3px;
        }



        @media print {
            @page {
                size: 215mm 165mm;
                margin: 3mm;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                font-family: Arial, sans-serif;
                width: 209mm;
                height: 159mm;
                overflow: hidden;
            }

            .print-container {
                width: 100%;
                height: 100%;
                max-width: none;
                border: 1px solid #000;
                margin: 0;
                padding: 2mm;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
            }

            .header {
                padding: 1mm;
                margin-bottom: 1mm;
            }

            .header h1 {
                font-size: 14px;
                margin-bottom: 1mm;
            }

            .header h2 {
                font-size: 10px;
            }

            .memo-info {
                padding: 2mm;
                margin-bottom: 1.5mm;
                gap: 5mm;
            }

            .content-section {
                padding: 1mm;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            table {
                flex: 1;
                font-size: 11px;
            }

            th, td {
                padding: 1mm;
                min-height: auto;
            }

            th {
                text-align: center;
                padding: 2px 1px;
                font-weight: bold;
                font-size: 10px;
                line-height: 1;
                height: 2.5mm;
                max-height: 2.5mm;
                vertical-align: middle;
            }

            .signatures {
                margin-top: 1mm;
                padding: 1mm;
            }

            .signature-box {
                width: 100px;
                font-size: 9px;
            }

            .signature-line {
                height: 20px;
                margin-bottom: 2mm;
            }

            .print-button {
                display: none !important;
            }
        }        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Dokumen</button>

    <div class="print-container">
        <!-- Header -->
        <div class="header" style="position: relative;">
            <div style="position: absolute; left: 0; top: 1mm; font-size: 13px; font-weight: bold;">
                {{ $permohonan->nomor_memo }}
            </div>
            <div style="position: absolute; right: 0; top: 1mm; font-size: 13px; font-weight: bold;">
                {{ $permohonan->created_at->format('d M y') }}
            </div>
            <h1>Memo Surat Jalan</h1>
            <h2>{{ config('app.name', 'PT. AYPSIS') }}</h2>
        </div>

        <!-- Memo Information -->
        <div class="memo-info">
            <div class="memo-left">
                <div class="info-row">
                    <span class="info-label">Kegiatan:</span>
                    <span class="info-value">{{ $kegiatan->nama_kegiatan ?? $permohonan->kegiatan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $permohonan->vendor_perusahaan ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tujuan:</span>
                    <span class="info-value">{{ $permohonan->ke ?? '-' }}</span>
                </div>
            </div>
            <div class="memo-right">
                <div class="info-row">
                    <span class="info-label">Supir:</span>
                    <span class="info-value">{{ $permohonan->supir->nama_panggilan ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Krani:</span>
                    <span class="info-value">{{ $permohonan->krani->nama_panggilan ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Plat Nomor:</span>
                    <span class="info-value">{{ $permohonan->plat_nomor ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Kontainer Details -->
        <div class="content-section" style="flex: 1; display: flex; flex-direction: column;">
            <div class="section-title">Detail Kontainer</div>
            <table class="kontainer-table" style="flex: 1; height: 100%;"
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">No</th>
                        <th style="width: 35%; text-align: center;">Nomor Seri Kontainer</th>
                        <th style="width: 15%; text-align: center;">Ukuran</th>
                        <th style="width: 42%; text-align: center;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Empty rows for manual entry -->
                    <tr style="height: 25%; min-height: 25mm;">
                        <td>1</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr style="height: 25%; min-height: 25mm;">
                        <td>2</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr style="height: 25%; min-height: 25mm;">
                        <td>3</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr style="height: 25%; min-height: 25mm;">
                        <td>4</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Additional Info -->
        @if($permohonan->keterangan)
        <div class="content-section" style="flex-shrink: 0; padding: 1mm;">
            <div class="section-title">Keterangan</div>
            <div style="border: 1px solid #ddd; padding: 1mm; min-height: 8mm; background: #f9f9f9; font-size: 9px;">
                {{ $permohonan->keterangan }}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Mengetahui</strong></div>
                <div>Kepala Bagian</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Dibuat Oleh</strong></div>
                <div>Admin</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Diterima Oleh</strong></div>
                <div>{{ $permohonan->supir->nama_panggilan ?? 'Supir' }}</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus untuk print
        window.onload = function() {
            // Auto print jika ada parameter print
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_print') === '1') {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        };
    </script>
</body>
</html>
