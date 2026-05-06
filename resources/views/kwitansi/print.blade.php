<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi - {{ $kwitansi->kwt_no }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
        }

        .kwitansi-container {
            width: 210mm;
            height: 148mm; /* Half A4 landscape roughly */
            margin: 20px auto;
            background-color: white;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
            border: 2px solid #0056b3;
            overflow: hidden;
        }

        /* Watermark-like background triangles from the image */
        .kwitansi-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(45deg, rgba(0, 86, 179, 0.03) 25%, transparent 25%),
                              linear-gradient(-45deg, rgba(0, 86, 179, 0.03) 25%, transparent 25%),
                              linear-gradient(45deg, transparent 75%, rgba(0, 86, 179, 0.03) 75%),
                              linear-gradient(-45deg, transparent 75%, rgba(0, 86, 179, 0.03) 75%);
            background-size: 40px 40px;
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo-placeholder {
            width: 60px;
            height: 60px;
            background-color: #fff;
            border: 2px solid #0056b3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            position: relative;
        }

        .logo-text {
            color: #0056b3;
            font-weight: 800;
            font-size: 18px;
            transform: rotate(-10deg);
        }

        .company-info {
            text-align: right;
        }

        .company-name {
            font-size: 16px;
            font-weight: 800;
            color: #0056b3;
            margin: 0;
            text-transform: uppercase;
        }

        .company-sub {
            font-size: 20px;
            font-weight: 900;
            color: #d32f2f;
            margin: 0;
            text-transform: uppercase;
        }

        .company-city {
            font-size: 14px;
            font-weight: 700;
            color: #0056b3;
            margin: 0;
        }

        .title-section {
            text-align: center;
            margin: 15px 0;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: 900;
            color: #0056b3;
            letter-spacing: 2px;
            margin: 0;
            text-decoration: underline;
        }

        .kwt-no {
            font-size: 14px;
            font-weight: 700;
            color: #0056b3;
            margin-top: 5px;
        }

        .form-row {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }

        .label {
            width: 150px;
            font-size: 14px;
            font-weight: 600;
            color: #0056b3;
        }

        .separator {
            width: 20px;
            font-weight: 700;
            color: #0056b3;
        }

        .value {
            flex: 1;
            font-size: 14px;
            font-weight: 700;
            color: #333;
            border-bottom: 1px dotted #0056b3;
            min-height: 22px;
            padding-left: 10px;
        }

        .amount-words {
            background-color: #f8f9fa;
            padding: 4px 10px;
            font-style: italic;
            border: 1px solid #dee2e6;
        }

        .payment-description {
            min-height: 60px;
            line-height: 1.4;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 30px;
        }

        .total-box {
            border: 2px solid #0056b3;
            padding: 8px 20px;
            display: inline-block;
            font-size: 20px;
            font-weight: 900;
            color: #333;
            background-color: #fff;
        }

        .total-label {
            font-size: 16px;
            font-weight: 800;
            color: #0056b3;
            margin-right: 15px;
        }

        .note-section {
            font-size: 10px;
            color: #d32f2f;
            width: 250px;
            line-height: 1.3;
        }

        .signature-section {
            text-align: center;
            width: 200px;
        }

        .date-place {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 50px;
        }

        .sign-name {
            font-size: 14px;
            font-weight: 800;
            border-bottom: 1px solid #333;
            display: inline-block;
            padding: 0 10px;
            width: 100%;
        }

        @media print {
            body {
                background-color: white;
            }
            .kwitansi-container {
                margin: 0;
                box-shadow: none;
                border: 2px solid #0056b3;
                width: 100%;
                height: auto;
            }
            .no-print {
                display: none;
            }
            .no-print-btn {
                display: none;
            }
        }

        .no-print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .no-print-btn:hover {
            background-color: #004494;
        }
    </style>
</head>
<body>
    <button class="no-print-btn no-print" onclick="window.print()">Cetak Kwitansi</button>

    <div class="kwitansi-container">
        <div class="content">
            <div class="header">
                <div class="logo-section">
                    <div class="logo-placeholder">
                        <div class="logo-text">AYP</div>
                    </div>
                </div>
                <div class="company-info">
                    <p class="company-name">Perusahaan Pelayaran Nasional</p>
                    <p class="company-sub">PT. ALEXINDO YAKINPRIMA</p>
                    <p class="company-city">JAKARTA</p>
                </div>
            </div>

            <div class="title-section">
                <h1 class="receipt-title">RECEIPT / KWITANSI</h1>
                <div class="kwt-no">KWT No. {{ $kwitansi->kwt_no }}</div>
            </div>

            <div class="form-row">
                <div class="label">Terima dari</div>
                <div class="separator">:</div>
                <div class="value">{{ $kwitansi->terima_dari ?: $kwitansi->pelanggan_nama }}</div>
            </div>

            <div class="form-row">
                <div class="label">Sejumlah</div>
                <div class="separator">:</div>
                <div class="value amount-words"># {{ ucwords($terbilang) }} Rupiah #</div>
            </div>

            <div class="form-row">
                <div class="label">Untuk Pembayaran</div>
                <div class="separator">:</div>
                <div class="value payment-description">
                    @foreach($kwitansi->details as $detail)
                        {{ $detail->item_description }}@if(!$loop->last), @endif
                    @endforeach
                    @if($kwitansi->keterangan)
                        <br>{{ $kwitansi->keterangan }}
                    @endif
                </div>
            </div>

            <div class="footer">
                <div class="left-side">
                    <div class="form-row" style="margin-bottom: 20px;">
                        <span class="total-label">TOTAL</span>
                        <div class="total-box">Rp. {{ number_format($kwitansi->total_invoice, 0, ',', '.') }},-</div>
                    </div>
                    <div class="note-section">
                        Pembayaran dengan Cheque/ Giro Bilyet<br>
                        dianggap lunas setelah diuangkan.<br>
                        Cheque/ Giro harap ditulis a/n PT. ALEXINDO YAKINPRIMA
                    </div>
                </div>
                
                <div class="signature-section">
                    <div class="date-place">Jakarta, {{ $kwitansi->tgl_inv ? $kwitansi->tgl_inv->format('d M Y') : date('d M Y') }}</div>
                    <div class="sign-name"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
