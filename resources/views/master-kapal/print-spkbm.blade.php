<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SPKBM - {{ $validated['nomor_surat'] }}</title>
    <style>
        @page {
            margin: 3.5cm 2cm 1.5cm 2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000000;
        }
        .text-justify {
            text-align: justify;
        }
        .text-indent {
            text-indent: 40px;
        }
        .mb-2 { margin-bottom: 6px; }
        .mb-4 { margin-bottom: 12px; }
        .mb-6 { margin-bottom: 18px; }
        .mb-8 { margin-bottom: 24px; }
        
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 40px;
            margin-bottom: 15px;
        }
        .details-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        
        .signature-block {
            margin-top: 30px;
            line-height: 1.4;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Header info & Date -->
    <table class="meta-table">
        <tr>
            <td style="width: 8%;">No</td>
            <td style="width: 2%;">:</td>
            <td style="width: 50%;">{{ $validated['nomor_surat'] }}</td>
            <td style="width: 42%; text-align: right;">Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Hal</td>
            <td>:</td>
            <td colspan="2" style="font-weight: bold;">{{ $validated['hal'] }}</td>
        </tr>
    </table>

    <!-- Address Block -->
    <div class="mb-6">
        Kepada Yth,<br>
        <strong>{!! nl2br(e($validated['ditujukan_kepada'])) !!}</strong>
    </div>

    <!-- Greeting -->
    <div class="mb-4">Dengan hormat,</div>

    <!-- Opening Paragraph -->
    <div class="text-justify text-indent mb-4">
        Bersama ini kami menunjuk PT. Pelabuhan Indonesia I Kijang untuk melaksanakan kegiatan Bongkar/Muat kontainer kapal milik kami yang singgah di pelabuhan Sei Kolak Kijang dengan detail sebagai berikut:
    </div>

    <!-- Technical & Operational Details -->
    <table class="details-table">
        <tr>
            <td style="width: 25%;">Nama kapal</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;">{{ $masterKapal->nama_kapal }}</td>
        </tr>
        <tr>
            <td>Voyage</td>
            <td>:</td>
            <td>{{ $validated['voyage'] }}</td>
        </tr>
        <tr>
            <td>Bendera</td>
            <td>:</td>
            <td>Indonesia</td>
        </tr>
        <tr>
            <td>GT</td>
            <td>:</td>
            <td>{{ $masterKapal->formatted_gross_tonnage }} ton</td>
        </tr>
        <tr>
            <td>DWT</td>
            <td>:</td>
            <td>{{ $masterKapal->formatted_deadweight_tonnage }} ton</td>
        </tr>
        <tr>
            <td>LOA</td>
            <td>:</td>
            <td>{{ $masterKapal->formatted_length_overall }} meter</td>
        </tr>
        <tr>
            <td>Rencana tiba</td>
            <td>:</td>
            <td>{{ $validated['rencana_tiba'] }}</td>
        </tr>
        <tr>
            <td>Rencana sandar</td>
            <td>:</td>
            <td>{{ $validated['rencana_sandar'] }}</td>
        </tr>
        <tr>
            <td>Rencana bongkar</td>
            <td>:</td>
            <td>{!! nl2br(e($validated['rencana_bongkar'])) !!}</td>
        </tr>
        <tr>
            <td>Rencana muat</td>
            <td>:</td>
            <td>{!! nl2br(e($validated['rencana_muat'])) !!}</td>
        </tr>
        <tr>
            <td>Tujuan</td>
            <td>:</td>
            <td>{{ $validated['tujuan'] }}</td>
        </tr>
    </table>

    <!-- Closing Paragraphs -->
    <div class="text-justify text-indent mb-4">
        Sesuai surat penunjukan kerja ini, mohon kiranya untuk penambahan jam kerja kegiatan bongkar muat mulai dari kegiatan dapat diselesaikan sampai malam/selesai.
    </div>

    <div class="text-justify text-indent mb-6">
        Demikian Surat Penunjukan ini kami buat, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
    </div>

    <!-- Signature Block -->
    <div class="signature-block">
        Hormat kami,<br>
        <strong>PT. ALEXINDO YAKINPRIMA</strong>
        <br><br><br><br>
        <strong><u>Robert</u></strong>
    </div>
</body>
</html>
