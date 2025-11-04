<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan</title>
    <style>
        @page {
            size: 165mm 215mm;
            margin: 0mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: white;
        }
        
        .container {
            width: 165mm;
            height: 215mm;
            margin: 0;
            padding: 20mm 5mm 5mm 5mm;  /* Menambah padding top untuk memberi ruang tanggal */
            box-sizing: border-box;
            position: relative;
        }
        
        /* Header Section */
        .date-header {
            position: absolute;
            top: 15mm;  /* 1.5cm dari atas */
            left: 100mm;  /* 10cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }
        
        /* No Plat Section */
        .no-plat {
            position: absolute;
            top: 55mm;  /* 5.5cm dari atas */
            left: 85mm; /* 8.5cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }
        
        /* Tabel 3 Kolom */
        .table-section {
            margin-bottom: 15mm;
        }
        
        .table-row {
            display: flex;
            margin-bottom: 7mm;
        }
        
        .col {
            width: 50mm;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
            padding-right: 5mm;
        }
        
        /* Tipe Kontainer dengan posisi absolut */
        .tipe-kontainer {
            position: absolute;
            top: 90mm;  /* 9cm dari atas */
            left: 60mm; /* 6cm dari kiri */
            font-size: 15px;
            font-weight: bold;
        }
        
        /* Nama Pengirim dengan posisi absolut */
        .nama-pengirim {
            position: absolute;
            top: 115mm;  /* 11.5cm dari atas */
            left: 110mm; /* 11cm dari kiri */
            font-size: 15px;
            font-weight: bold;
        }
        
        /* Nama Barang dengan posisi absolut */
        .nama-barang {
            position: absolute;
            top: 90mm;  /* 9cm dari atas */
            left: 110mm; /* 11cm dari kiri */
            font-size: 15px;
            font-weight: bold;
        }
        
        /* Tujuan Ambil dengan posisi absolut */
        .tujuan-ambil {
            position: absolute;
            top: 140mm;  /* 14cm dari atas */
            left: 110mm; /* 11cm dari kiri */
            font-size: 15px;
            font-weight: bold;
        }
        
        /* Nomor Seal dengan posisi absolut */
        .nomor-seal {
            position: absolute;
            top: 140mm;  /* 14cm dari atas */
            left: 10mm;  /* 1cm dari kiri */
            font-size: 15px;
            font-weight: bold;
        }
        
        /* Tujuan Kirim dengan posisi absolut */
        .tujuan-kirim {
            position: absolute;
            top: 110mm;  /* 11cm dari atas */
            left: 10mm;  /* 1cm dari kiri */
            font-size: 15px;
            font-weight: bold;
        }
        
        .col-center {
            text-align: center;
        }
        
        /* Baris SEAL, UKURAN, PENGIRIM */
        .seal-row {
            display: flex;
            margin-bottom: 15mm;
        }
        
        .seal-col {
            width: 50mm;
            font-size: 14px;
            font-weight: bold;
            padding-right: 5mm;
        }
        
        .ukuran-col {
            width: 50mm;
            font-size: 14px;
            font-weight: bold;
            font-family: 'Times New Roman', serif;
            padding-right: 5mm;
        }
        
        .pengirim-col {
            width: 50mm;
            font-size: 14px;
            font-weight: bold;
        }
        
        /* Baris Bawah: Tujuan */
        .bottom-row {
            display: flex;
            margin-bottom: 10mm;
        }
        
        /* TTD Section */
        .ttd-section {
            position: absolute;
            bottom: 15mm;
            left: 5mm;
            right: 5mm;
        }
        
        .ttd-row {
            display: flex;
            text-align: center;
            margin-bottom: 5mm;
        }
        
        .ttd-col {
            width: 50mm;
            font-size: 15px;
            font-weight: bold;
        }
        
        .supir-name {
            position: absolute;
            top: 190mm;  /* 19cm dari atas */
            left: 70mm;  /* 7cm dari kiri */
            text-align: center;
        }
        
        @media print {
            @page { 
                margin: 0 !important; 
                size: 165mm 215mm !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background: white !important;
                color: black !important;
            }
            
            .container {
                box-shadow: none !important;
                padding: 20mm 5mm 5mm 5mm !important;  /* Konsisten dengan padding non-print */
                page-break-inside: avoid !important;
                margin: 0 !important;
                background: white !important;
            }
            
            /* Aggressively hide browser headers/footers */
            @page { margin: 0 !important; }
            @page :first { margin-top: 0 !important; }
            @page :left { margin: 0 !important; }
            @page :right { margin: 0 !important; }
            
            /* Hide print instructions when printing */
            div[style*="background: #f0f0f0"] {
                display: none !important;
            }
            
            /* Hide all possible header/footer elements */
            header, .header, .print-header, 
            footer, .footer:not(.ttd-section .ttd-col), .print-footer,
            nav, .nav, .navigation,
            .page-header, .page-footer { 
                display: none !important; 
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Prevent page breaks */
            * { 
                page-break-inside: avoid !important;
                -webkit-region-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>
    <!-- Print Instructions (only visible on screen) -->
    <div style="display: block; padding: 10px; background: #f0f0f0; margin-bottom: 10px; font-size: 12px; border: 1px solid #ccc;">
        <strong>PETUNJUK PRINT:</strong> Untuk menghilangkan URL dan nomor halaman, pastikan <strong>"Headers and footers"</strong> dinonaktifkan di pengaturan print browser (Ctrl+P → More settings → hapus centang "Headers and footers")
    </div>

    <div class="container">
        <!-- SESI 1: HEADER -->
        <div class="date-header">
            {{ \Carbon\Carbon::parse($suratJalan->tanggal_surat_jalan ?? now())->format('d-M-Y') }}
        </div>
        
        <!-- SESI 2: NO PLAT KENDARAAN -->
        <div class="no-plat">
            {{ strtoupper($suratJalan->no_plat ?? ($suratJalan->no_plat != '--Pilih No Plat' ? $suratJalan->no_plat : '')) }}
        </div>
        
        <!-- TIPE KONTAINER dengan posisi absolut -->
        <div class="tipe-kontainer">
            {{ strtoupper($suratJalan->tipe_kontainer ?? 'FCL') }}
        </div>
        
        <!-- NAMA PENGIRIM dengan posisi absolut -->
        <div class="nama-pengirim">
            {{ strtoupper($suratJalan->pengirim ?? 'PT TIRTA INVESTAMA') }}
        </div>
        
        <!-- NAMA BARANG dengan posisi absolut -->
        <div class="nama-barang">
            {{ strtoupper($suratJalan->jenis_barang ?? $suratJalan->order->jenisBarang->nama ?? 'AQUA') }}
        </div>
        
        <!-- TUJUAN AMBIL dengan posisi absolut -->
        <div class="tujuan-ambil">
            {{ strtoupper($suratJalan->tujuan_pengambilan ?? 'BATAM') }}
        </div>
        
        <!-- NOMOR SEAL dengan posisi absolut -->
        <div class="nomor-seal">
            SEAL AYP{{ $suratJalan->no_seal ?? '0036824' }}
        </div>
        
        <!-- TUJUAN KIRIM dengan posisi absolut -->
        <div class="tujuan-kirim">
            {{ strtoupper($suratJalan->tujuan_pengiriman ?? 'SUKABUMI') }}
        </div>
        
        <!-- SESI 3: TABEL BARANG (3 Kolom) -->
        <div class="table-section">
            <!-- Baris 1: No Kontainer | Kosong (Tipe Kontainer dipindah) | Kosong (Jenis Barang dipindah) -->
            <div class="table-row">
                <div class="col">{{ strtoupper($suratJalan->no_kontainer ?? '') }}</div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
            
            <!-- Baris 2 & 3: Kosong -->
            <div class="table-row">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
            
            <div class="table-row">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
            
            <!-- Baris 4: KOSONG (Seal dipindah ke posisi absolut) | KOSONG | KOSONG (Pengirim dipindah ke posisi absolut) -->
            <div class="seal-row">
                <div class="seal-col"></div>
                <div class="ukuran-col"></div>
                <div class="pengirim-col"></div>
            </div>
            
            <!-- Baris 5, 6, 7: Kosong -->
            <div class="table-row">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
            
            <div class="table-row">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
            
            <div class="table-row">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
            
            <!-- Baris 8: KOSONG (Tujuan Kirim dipindah ke posisi absolut) | Kosong | Kosong (Tujuan Ambil dipindah ke posisi absolut) -->
            <div class="bottom-row">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
        </div>
        
        <!-- NAMA SUPIR dengan posisi absolut -->
        <div class="supir-name">
            {{ strtoupper($suratJalan->supir ?? 'SUMANTA') }}
        </div>
        
        <!-- SESI 4: TTD AREA -->
        <div class="ttd-section">
            <div class="ttd-row">
                <div class="ttd-col"></div>
                <div class="ttd-col"></div>
                <div class="ttd-col"></div>
            </div>
        </div>
    </div>
    
    <script>
        // Aggressive clean print function
        window.onload = function() {
            // Remove title completely
            document.title = '';
            
            // Remove any meta info
            var metas = document.getElementsByTagName('meta');
            for(var i = 0; i < metas.length; i++) {
                if(metas[i].name === 'description' || metas[i].name === 'keywords') {
                    metas[i].remove();
                }
            }
            
            // Print with delay
            setTimeout(function() {
                // Try to override browser print settings
                try {
                    var printSettings = {
                        silent: true,
                        printBackground: false,
                        deviceName: ''
                    };
                } catch(e) {}
                
                window.print();
            }, 300);
        }
        
        // Clean up before print
        window.addEventListener('beforeprint', function() {
            document.title = '';
            
            // Hide any remaining elements
            var elementsToHide = ['header', 'nav', '.header', '.nav'];
            elementsToHide.forEach(function(selector) {
                var elements = document.querySelectorAll(selector);
                elements.forEach(function(el) {
                    el.style.display = 'none';
                });
            });
        });
        
        // Clean up after print
        window.addEventListener('afterprint', function() {
            document.title = '';
        });
    </script>
</body>
</html>
