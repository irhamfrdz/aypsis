<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $pembayaran->nomor_pembayaran }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 11px;
            line-height: 1.4;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            vertical-align: middle;
        }
        .table thead th {
            background-color: #ffcccc;
            font-weight: bold;
            text-align: center;
        }
        .table tbody td {
            background-color: #fff;
        }
        .table td.number {
            text-align: right;
        }
        .say-section {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fff;
        }
        .memo-section {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            min-height: 60px;
            background-color: #fff;
        }
        .signature-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .signature-section td {
            width: 25%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-space {
            height: 70px;
            margin: 15px 0;
        }
        .company-footer {
            text-align: center;
            border: 1px solid #000;
            padding: 15px;
            background-color: #e6f3ff;
            font-size: 10px;
        }
        .no-print {
            margin-bottom: 20px;
        }
        @media print {
            @page {
                size: 215mm 165mm; /* Setengah Folio landscape */
                margin: 8mm;
            }
            .no-print {
                display: none !important;
            }
            body {
                padding: 5px;
                font-size: 9px;
                width: 199mm;
            }
            .signature-space {
                height: 30px;
                margin: 8px 0;
            }
            .table {
                margin-bottom: 8px;
            }
            .table th, .table td {
                padding: 3px;
                font-size: 8px;
            }
            .say-section, .memo-section {
                padding: 5px;
                font-size: 8px;
                margin-bottom: 6px;
                min-height: auto;
            }
            .signature-section {
                margin-top: 10px;
                margin-bottom: 5px;
            }
            .signature-section td {
                padding: 5px;
                font-size: 7px;
            }
            table[style*="background-color: #ffcccc"] {
                margin-bottom: 10px;
            }
            table[style*="background-color: #ffcccc"] td {
                padding: 5px;
                font-size: 8px;
            }
            table[style*="background-color: #ffcccc"] div {
                font-size: 8px;
            }
            table[style*="background-color: #ffcccc"] strong {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Print button (hidden when printing) -->
    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" style="padding: 8px 15px; font-size: 12px;">Cetak</button>
        <button onclick="window.close()" style="padding: 8px 15px; font-size: 12px; margin-left: 10px;">Tutup</button>
    </div>

    @php
        // Hitung total amount dari transaksi COA
        $totalAmount = $pembayaran->total_tagihan_setelah_penyesuaian ?? $pembayaran->total_pembayaran;

        // Ambil nomor invoice dari items
        $invoiceNumbers = $pembayaran->items->map(function($item) {
            return $item->pranota->no_invoice ?? '';
        })->filter()->implode(', ');

        // Get vendor/payee name from first tagihan item
        $payeeName = 'N/A';
        $firstItem = $pembayaran->items->first();
        if ($firstItem && $firstItem->pranota) {
            $tagihanItems = $firstItem->pranota->tagihanKontainerSewaItems();
            if ($tagihanItems->isNotEmpty()) {
                $payeeName = $tagihanItems->first()->vendor ?? 'N/A';
            }
        }
    @endphp

    <!-- Header Section -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #ffcccc;">
        <tr>
            <td style="width: 55%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                <div style="border: 1px solid #000; padding: 8px; margin-bottom: 8px;">
                    <strong style="font-size: 12px;">PT. ALEXINDO YAKINPRIMA</strong><br>
                    <span style="font-size: 9px;">JL. PLUIT RAYA NO.8 BLOK B NO. 12<br>
                    JAKARTA-14440<br>
                    (021)6606231-6614175</span>
                </div>
                <div style="font-size: 9px;">
                    <strong>Paid From :</strong> {{ $pembayaran->bank }}<br>
                    <strong>Payee :</strong> {{ $payeeName }}
                </div>
            </td>
            <td style="width: 45%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                <div style="text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 10px;">
                    Other Payment
                </div>
                <table style="width: 100%; font-size: 9px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px; width: 40%;"><strong>Date</strong></td>
                        <td style="border: 1px solid #000; padding: 4px;">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;"><strong>Voucher No.</strong></td>
                        <td style="border: 1px solid #000; padding: 4px;">{{ $pembayaran->nomor_pembayaran }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;"><strong>Rate</strong></td>
                        <td style="border: 1px solid #000; padding: 4px;">1</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;"><strong>Cheque No.</strong></td>
                        <td style="border: 1px solid #000; padding: 4px;">-</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;"><strong>Currency</strong></td>
                        <td style="border: 1px solid #000; padding: 4px;"><strong>Amount</strong></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;">IDR</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: right;"><strong>{{ number_format($totalAmount, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Main Table -->
    <table class="table">
        <thead>
            <tr>
                <th style="width: 15%;">Account No.</th>
                <th style="width: 35%;">Account Name</th>
                <th style="width: 15%;">Amount</th>
                <th style="width: 35%;">Nomor Pranota Kontainer Sewa :<br>{{ $invoiceNumbers }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowCount = $coaTransactions->count();
            @endphp
            @foreach($coaTransactions as $index => $transaction)
                <tr>
                    <td>{{ $transaction->coa->nomor_akun ?? '-' }}</td>
                    <td>{{ $transaction->coa->nama_akun ?? '-' }}</td>
                    <td class="number">
                        @if($transaction->debit > 0)
                            {{ number_format((float)$transaction->debit, 0, ',', '.') }}
                        @else
                            {{ number_format((float)$transaction->kredit, 0, ',', '.') }}
                        @endif
                    </td>
                    @if($loop->first)
                        <td rowspan="{{ $rowCount + 1 }}" style="vertical-align: top; padding-top: 15px;">
                            <strong>Nomor Pranota Kontainer Sewa :</strong><br>
                            {{ $invoiceNumbers }}
                        </td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: right; font-weight: bold;">Total Payment :</td>
                <td class="number" style="font-weight: bold;">{{ number_format($totalAmount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Say Section (Terbilang) -->
    <div class="say-section">
        <strong>Say :</strong>
        {{ ucwords(\App\Helpers\Terbilang::make($totalAmount)) }} Rupiah
    </div>

    <!-- Memo Section -->
    <div class="memo-section">
        <strong>Memo</strong><br>
        Nomor Pranota Kontainer Sewa : {{ $invoiceNumbers }}
        @if($pembayaran->keterangan)
            <br>{{ $pembayaran->keterangan }}
        @endif
    </div>

    <!-- Signature Section -->
    <table class="signature-section">
        <tr>
            <td>
                <strong>Diterima,</strong>
                <div class="signature-space"></div>
                <div>_________________</div>
                <div>Date: _____________</div>
            </td>
            <td>
                <strong>Dibuat,</strong>
                <div class="signature-space"></div>
                <div>_________________</div>
                <div>Date: _____________</div>
            </td>
            <td>
                <strong>Diperiksa,</strong>
                <div class="signature-space"></div>
                <div>_________________</div>
                <div>Date: _____________</div>
            </td>
            <td>
                <strong>Disetujui,</strong>
                <div class="signature-space"></div>
                <div>_________________</div>
                <div>Date: _____________</div>
            </td>
        </tr>
    </table>
</body>
</html>
