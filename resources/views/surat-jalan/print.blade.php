<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
            margin-header: 0mm;
            margin-footer: 0mm;
            @top-left { content: ""; }
            @top-center { content: ""; }
            @top-right { content: ""; }
            @bottom-left { content: ""; }
            @bottom-center { content: ""; }
            @bottom-right { content: ""; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: white;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 40mm 30mm;
            box-sizing: border-box;
            min-height: 100vh;
            position: relative;
        }
        
        .date-header {
            text-align: right;
            margin-bottom: 80px;
            font-size: 16px;
            font-weight: bold;
        }
        
        .container-number {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 80px 0;
            letter-spacing: 2px;
        }
        
        .content {
            margin: 80px 0;
        }
        
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 60px;
            font-size: 18px;
            font-weight: bold;
        }
        
        .left-col {
            flex: 1;
            text-align: left;
        }
        
        .right-col {
            flex: 1;
            text-align: right;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }
        
        .route-section {
            margin: 100px 0;
        }
        
        .route-from {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 100px;
        }
        
        .route-to {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }
        
        .footer {
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            font-weight: bold;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                min-height: 100vh;
                padding: 30mm 25mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Date Header -->
        <div class="date-header">
            {{ \Carbon\Carbon::parse($suratJalan->tanggal_surat_jalan ?? now())->format('d-M-Y') }}
        </div>
        
        <!-- Container Number -->
        <div class="container-number">
            {{ $suratJalan->no_kontainer ?? 'B 9902 UEK' }}
        </div>
        
        <!-- FCL and Volume -->
        <div class="content">
            <div class="row">
                <div class="left-col">FCL</div>
                <div class="right-col">{{ $suratJalan->size ?? '4 x 1500 ML' }}</div>
            </div>
            
            <!-- Seal Number and Company -->
            <div class="row">
                <div class="left-col">SEAL {{ $suratJalan->no_seal ?? 'AYP0036824' }}</div>
                <div class="right-col">
                    <div class="company-name">{{ $suratJalan->pengirim ?? 'PT TIRTA INVESTAMA' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Route Information -->
        <div class="route-section">
            <div class="route-from">
                {{ $suratJalan->tujuan_pengambilan ?? 'Batam' }}
            </div>
            <div class="route-to">
                {{ $suratJalan->tujuan_pengiriman ?? 'SUKABUMI' }}
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            {{ $suratJalan->supir ?? 'SUMANTA' }}
        </div>
    </div>
    
    <script>
        // Auto print when page loads with clean headers
        window.onload = function() {
            // Try to set print settings programmatically
            if (window.chrome) {
                // For Chrome - this may require user permission
                document.title = 'Surat Jalan';
            }
            window.print();
        }
    </script>
</body>
</html>
