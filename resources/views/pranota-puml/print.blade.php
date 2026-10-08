<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half-Folio');
    $paperMap = [
        'Half-Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '195.9mm',
            'fontSize' => '9.5px',
            'headerH1' => '14px',
            'tableFont' => '8.5px',
            'signatureBottom' => '3mm'
        ],
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '195.9mm',
            'fontSize' => '11px',
            'headerH1' => '16px',
            'tableFont' => '9.5px',
            'signatureBottom' => '5mm'
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '190mm',
            'fontSize' => '11px',
            'headerH1' => '16px',
            'tableFont' => '9.5px',
            'signatureBottom' => '5mm'
        ],
        'Half-A4' => [
            'size' => '148.5mm 210mm',
            'width' => '148.5mm',
            'height' => '210mm',
            'containerWidth' => '148.5mm',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '8px',
            'signatureBottom' => '3mm'
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>PERMOHONAN TRANSFER - {{ $puml->nomor_pranota }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }} portrait;
            margin: 5mm;
        }

        html {
            width: {{ $currentPaper['width'] }};
        }

        body {
            font-family: Arial, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.3;
            color: #222;
            background: white;
            position: relative;
            width: {{ $currentPaper['width'] }};
            margin: 0;
            padding: 0;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body { margin: 0; padding: 0; }
            .container {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }

        .container {
            width: 100%;
            max-width: calc({{ $currentPaper['containerWidth'] }} - 10mm);
            margin: 0 auto;
            padding: 4mm 6mm;
            position: relative;
            box-sizing: border-box;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1.5px 0;
            vertical-align: top;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .info-table .label {
            width: 120px;
            font-weight: bold;
            color: #333;
        }

        .info-table .separator {
            width: 15px;
            text-align: center;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        .items-table th, .items-table td {
            border: 1px solid #333;
            padding: 2.5px 4px;
            font-size: {{ $currentPaper['tableFont'] }};
            vertical-align: middle;
        }

        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            color: #111;
        }

        .items-table td {
            word-wrap: break-word;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; }

        .terbilang-box {
            margin-top: 4px;
            padding: 4px 6px;
            border: 1px dashed #666;
            background-color: #fafafa;
            font-size: {{ $currentPaper['tableFont'] }};
            font-style: italic;
        }

        .potongan-summary {
            margin-top: 4px;
            padding: 3px 6px;
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            font-size: 8px;
            color: #92400e;
        }

        .footer-signatures {
            margin-top: 15px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 25%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 15px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 80%;
            margin: 0 auto 3px;
        }

        .signature-label {
            font-size: 8.5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Instruction Banner & Controls -->
    <div class="no-print" style="background: #fef3c7; padding: 10px 15px; border: 1px solid #f59e0b; margin: 10px; font-size: 11px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; gap: 15px;">
        <div>
            <strong>⚠️ Setting Print:</strong> Paper Size: <b>Legal / Folio</b>, Scale: <b>100%</b>, Orientation: <b>Portrait</b>. (Untuk Half-Folio, potong 2 secara horizontal).
        </div>
        <div style="display: flex; align-items: center; gap: 10px; shrink-to-fit: 0;">
            @include('components.paper-selector', ['selectedSize' => $paperSize ?? 'Half-Folio'])
            <button onclick="window.print()" style="padding: 6px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; white-space: nowrap;">
                🖨️ Cetak Permohonan Transfer
            </button>
        </div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

        <!-- Info Section -->
        <table class="info-table">
            <tr>
                <td class="label">Nomor Dokumen</td>
                <td class="separator">:</td>
                <td><strong>{{ $puml->nomor_pranota }}</strong></td>
                <td class="label">Keperluan</td>
                <td class="separator">:</td>
                <td>Pembayaran Uang Makan & Lembur (PUML)</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pranota</td>
                <td class="separator">:</td>
                <td>{{ $puml->tanggal_pranota ? $puml->tanggal_pranota->format('d F Y') : '-' }}</td>
                <td class="label">Periode Kerja</td>
                <td class="separator">:</td>
                <td>
                    @if($puml->periode_start && $puml->periode_end)
                        {{ $puml->periode_start->format('d/m/Y') }} s/d {{ $puml->periode_end->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Total Penerima</td>
                <td class="separator">:</td>
                <td><strong>{{ $karyawanRekap->count() }} Orang</strong></td>
                <td class="label">Status</td>
                <td class="separator">:</td>
                <td style="text-transform: uppercase; font-weight: bold;">{{ $puml->status }}</td>
            </tr>
        </table>

        <!-- Table of Transfer Details -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 4%;">NO</th>
                    <th style="width: 11%;">NIK</th>
                    <th style="width: 25%;">NAMA KARYAWAN</th>
                    <th style="width: 20%;">BANK / REKENING</th>
                    <th style="width: 12%;">U. MAKAN (RP)</th>
                    <th style="width: 12%;">LEMBUR (RP)</th>
                    <th style="width: 10%;">POT. (RP)</th>
                    <th style="width: 13%;">TRANSFER (RP)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawanRekap as $index => $item)
                    @php
                        $kar = $item['karyawan'];
                        $bankName = $kar ? ($kar->nama_bank ?: ($kar->bank ?: 'BCA')) : 'BCA';
                        $accNo = $kar ? trim((string) ($kar->akun_bank ?: ($kar->no_rekening ?? '-'))) : '-';
                        if (empty($accNo)) {
                            $accNo = '-';
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-mono">{{ $kar->nik ?? '-' }}</td>
                        <td class="font-bold">
                            {{ $kar ? ($kar->nama_lengkap ?: ($kar->nama_panggilan ?: 'Karyawan #'.$kar->id)) : 'Karyawan' }}
                        </td>
                        <td>
                            <span class="font-bold">{{ $bankName }}</span>: <span class="font-mono">{{ $accNo }}</span>
                        </td>
                        <td class="text-right">
                            {{ $item['total_uang_makan'] > 0 ? number_format($item['total_uang_makan'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right">
                            {{ $item['total_lembur'] > 0 ? number_format($item['total_lembur'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right" style="{{ $item['total_potongan'] > 0 ? 'color: #b91c1c;' : '' }}">
                            {{ $item['total_potongan'] > 0 ? '('.number_format($item['total_potongan'], 0, ',', '.').')' : '-' }}
                        </td>
                        <td class="text-right font-bold">
                            {{ number_format($item['total_terima'], 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 10px;">Tidak ada rincian penerima dalam pranota ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <td colspan="4" class="text-center font-bold">TOTAL</td>
                    <td class="text-right font-bold">{{ number_format($totalUangMakan, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($totalLembur, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="{{ $totalPotongan > 0 ? 'color: #b91c1c;' : '' }}">
                        {{ $totalPotongan > 0 ? '('.number_format($totalPotongan, 0, ',', '.').')' : '0' }}
                    </td>
                    <td class="text-right font-bold" style="font-size: {{ $currentPaper['headerH1'] }}; color: #1e1b4b;">
                        {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Terbilang Box -->
        <div class="terbilang-box">
            <strong>Terbilang:</strong> {{ \App\Helpers\Terbilang::make($grandTotal) }} Rupiah
        </div>

        @if($totalPotongan > 0)
        <!-- Rincian Potongan Summary -->
        @php
            $sumUtang = $karyawanRekap->sum('pot_utang');
            $sumBpjs = $karyawanRekap->sum('pot_bpjs');
            $sumPph = $karyawanRekap->sum('pot_pph');
            $sumTerlambat = $karyawanRekap->sum('pot_terlambat');
        @endphp
        <div class="potongan-summary">
            <strong>Rincian Potongan:</strong>
            Utang: Rp {{ number_format($sumUtang, 0, ',', '.') }} |
            BPJS: Rp {{ number_format($sumBpjs, 0, ',', '.') }} |
            PPh: Rp {{ number_format($sumPph, 0, ',', '.') }} |
            Terlambat: Rp {{ number_format($sumTerlambat, 0, ',', '.') }}
        </div>
        @endif

        <!-- Signature Section -->
        <div class="footer-signatures">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-label" style="margin-bottom: 35px;">Dibuat Oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-label">( {{ $puml->creator->name ?? ($puml->creator->username ?? 'Staff HRD') }} )</div>
                        <div style="font-size: 8px; color: #555;">Staff / HRD</div>
                    </td>
                    <td>
                        <div class="signature-label" style="margin-bottom: 35px;">Diperiksa Oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-label">( ___________________ )</div>
                        <div style="font-size: 8px; color: #555;">Pemeriksa / Finance</div>
                    </td>
                    <td>
                        <div class="signature-label" style="margin-bottom: 35px;">Disetujui Oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-label">( ___________________ )</div>
                        <div style="font-size: 8px; color: #555;">Pimpinan / Direktur</div>
                    </td>
                    <td>
                        <div class="signature-label" style="margin-bottom: 35px;">Dibayar Oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-label">( ___________________ )</div>
                        <div style="font-size: 8px; color: #555;">Kasir</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
