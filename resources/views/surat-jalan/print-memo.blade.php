<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEMO - {{ $suratJalan->no_surat_jalan }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm 1.5cm;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            float: left;
            margin-right: 20px;
        }
        
        .company-info {
            text-align: center;
            margin-top: 10px;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .company-address {
            font-size: 10pt;
            margin-bottom: 5px;
        }
        
        .company-contact {
            font-size: 10pt;
        }
        
        .memo-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 30px 0 20px 0;
            text-decoration: underline;
        }
        
        .memo-content {
            margin: 20px 0;
        }
        
        .memo-field {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        
        .memo-label {
            width: 120px;
            font-weight: normal;
            display: inline-block;
        }
        
        .memo-value {
            flex: 1;
            border-bottom: 1px dotted #666;
            min-height: 20px;
            padding-bottom: 2px;
        }
        
        .memo-description {
            margin: 30px 0;
            min-height: 100px;
        }
        
        .memo-description-label {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .memo-description-content {
            border: 1px solid #000;
            min-height: 80px;
            padding: 10px;
        }
        
        .signature-section {
            margin-top: 50px;
            text-align: right;
        }
        
        .signature-city {
            margin-bottom: 80px;
        }
        
        .signature-name {
            text-decoration: underline;
            font-weight: bold;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="company-logo">
            <!-- Logo placeholder - ganti dengan logo perusahaan -->
            <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="45" stroke="#d32f2f" stroke-width="3" fill="none"/>
                <text x="50" y="35" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="#d32f2f">AYP</text>
                <text x="50" y="65" text-anchor="middle" font-family="Arial, sans-serif" font-size="8" fill="#d32f2f">EXPEDISI</text>
            </svg>
        </div>
        
        <div class="company-info">
            <div class="company-tagline">Jasa Expedisi & Angkutan</div>
            <div class="company-name">PT ALEXINDO YAKINPRIMA</div>
            <div class="company-address">Jl Pluit No. 8 Blok B No. 12 Jakarta Utara</div>
            <div class="company-contact">Telp : (021)-6606231,6614175,6614176 Fax: (021)-6619907</div>
        </div>
    </div>
    
    <div class="memo-title">MEMO</div>
    
    <div class="memo-content">
        <div class="memo-field">
            <span class="memo-label">Jakarta</span>
            <span>, {{ \Carbon\Carbon::now()->format('Y-m-d') }}</span>
        </div>
        
        <div class="memo-field">
            <span class="memo-label">Kepada Yth</span>
            <span>,</span>
        </div>
        
        <div class="memo-field">
            <span class="memo-label"></span>
            <span class="memo-value">{{ $suratJalan->penerima ?? 'PT. ................................' }}</span>
        </div>
        
        <div class="memo-field">
            <span class="memo-label">UP</span>
            <span>, Bpk/Ibu</span>
        </div>
        
        <div class="memo-field">
            <span class="memo-label">Di</span>
            <span>. {{ $suratJalan->tujuanPengirimanRelation->nama ?? $suratJalan->tujuan_kirim ?? 'SUKABUMI' }}</span>
        </div>
        
        <div class="memo-field">
            <span class="memo-label">Dengan Hormat</span>
            <span>,</span>
        </div>
        
        <div class="memo-description">
            <div class="memo-description-content">
                Mohon dapat diberikan muatan, kepada pembawa memo ini ;<br><br>
                
                Mobil : {{ $suratJalan->no_plat ?? 'B 9366 UIX' }}<br><br>
                
                Muatan : {{ $suratJalan->jenis_barang ?? 'PRODUK MINUMAN' }}<br><br>
                
                Tujuan : {{ $suratJalan->tujuanPengirimanRelation->nama ?? $suratJalan->tujuan_kirim ?? 'Batam' }}<br><br>
                
                Demikianlah surat permohonan ini kami sampaikan, atas perhatian
            </div>
        </div>
    </div>
    
    <div class="signature-section">
        <div class="signature-city">Jakarta, {{ \Carbon\Carbon::now()->format('Y-m-d') }}</div>
        <div class="signature-name">(.............................)</div>
    </div>
    
    <!-- Print button for web view -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" style="background: #4F46E5; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px;">
            Print Memo
        </button>
        <button onclick="window.close()" style="background: #6B7280; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 5px;">
            Tutup
        </button>
    </div>
    
    <script>
        // Auto print when opened
        window.onload = function() {
            // Uncomment the line below if you want auto print
            // window.print();
        }
    </script>
</body>
</html>