<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEMO - {{ $suratJalan->no_surat_jalan }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 2cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .letterhead {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .logo-section {
            width: 120px;
            margin-right: 20px;
            text-align: center;
        }
        
        .logo {
            width: 100px;
            height: 100px;
        }
        
        .company-info {
            flex: 1;
            text-align: center;
        }
        
        .company-tagline {
            color: #000;
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company-name {
            color: #dc3545;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .company-address {
            font-size: 9pt;
            margin-bottom: 2px;
        }
        
        .company-contact {
            font-size: 9pt;
        }
        
        .memo-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 25px auto 20px auto;
            text-decoration: underline;
            width: 100%;
            display: block;
        }
        
        .memo-content {
            margin: 20px 0;
        }
        
        .memo-field {
            margin-bottom: 5px;
            display: flex;
        }
        
        .memo-label {
            width: 100px;
            font-weight: normal;
        }
        
        .memo-value {
            flex: 1;
        }
        
        .memo-description {
            margin: 20px 0;
            text-align: justify;
            line-height: 1.4;
        }
        
        .memo-details {
            margin: 20px 0 30px 0;
        }
        
        .memo-detail-item {
            margin-bottom: 8px;
            display: flex;
        }
        
        .memo-detail-label {
            width: 80px;
            font-weight: normal;
        }
        
        .signature-section {
            margin-top: 40px;
            text-align: left;
        }
        
        .signature-city {
            margin-bottom: 50px;
        }
        
        .signature-name {
            text-decoration: underline;
            font-weight: bold;
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
    <div class="letterhead">
        <div class="logo-section">
            <img src="{{ asset('images/logo.png') }}" alt="PT ALEXINDO YAKINPRIMA Logo" class="logo">
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
            <span class="memo-label">Jakarta,</span>
            <span class="memo-value">{{ \Carbon\Carbon::parse($suratJalan->tanggal_kirim ?? $suratJalan->created_at)->locale('id')->isoFormat('D MMMM Y') }}</span>
        </div>
        
        <br>
        
        <div class="memo-field">
            <span class="memo-label">Kepada Yth,</span>
            <span class="memo-value"></span>
        </div>
        
        <div class="memo-field">
            <span class="memo-label"></span>
            <span class="memo-value">{{ $suratJalan->pengirim ?? 'PT. ...............................' }}</span>
        </div>
        
        <div class="memo-field">
            <span class="memo-label">Bpk/Ibu,</span>
            <span class="memo-value"></span>
        </div>
        
        <div class="memo-field">
            <span class="memo-label">Di</span>
            <span class="memo-value">{{ $suratJalan->tujuan_pengambilan ?? 'JAKARTA' }}</span>
        </div>
        
        <br>
        
        <div class="memo-description">
            <p>Dengan Hormat,</p>
        </div>
        
        <div class="memo-description">
            <p>Mohon dapat diberikan muatan, kepada pembawa memo ini ;</p>
        </div>
        
        <div class="memo-details">
            <div class="memo-detail-item">
                <span class="memo-detail-label">Mobil</span>
                <span>: {{ $suratJalan->no_plat ?? 'B 9366 UIX' }}</span>
            </div>
            
            <div class="memo-detail-item">
                <span class="memo-detail-label">Muatan</span>
                <span>: {{ $suratJalan->nama_barang ?? 'PRODUK MINUMAN' }}</span>
            </div>
            
            <div class="memo-detail-item">
                <span class="memo-detail-label">Tujuan</span>
                <span>: {{ $suratJalan->tujuanPengirimanRelation->nama ?? $suratJalan->order->tujuan_kirim ?? 'Batam' }}</span>
            </div>
        </div>
        
        <div class="memo-description">
            <p>Demikian surat permohonan ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>
        </div>
    </div>
    
    <div class="signature-section">
        <div style="margin-bottom: 10px; font-weight: bold;">Hormat Kami,</div>
        <div style="margin-bottom: 50px;"></div>
        <div class="signature-name">Alex</div>
    </div>
    
    <!-- Print button for web view -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" style="background: #4F46E5; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; margin-right: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 5px;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zM5 14H4v-3h1v3zm2 0v2h6v-2H7zm0-1h6v-2H7v2z" clip-rule="evenodd"></path>
            </svg>
            Print
        </button>
        <button onclick="window.close()" style="background: #6B7280; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 5px;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            Tutup
        </button>
    </div>
    
    <script>
        // Format tanggal Indonesia
        document.addEventListener('DOMContentLoaded', function() {
            // Auto focus untuk print
            console.log('Memo siap untuk dicetak');
        });
    </script>
</body>
</html>