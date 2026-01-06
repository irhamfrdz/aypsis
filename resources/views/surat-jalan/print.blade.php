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
            font-size: 14px; /* increased base font-size for print */
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
            top: 10mm;  /* 1cm dari atas */
            left: 100mm;  /* 10cm dari kiri */
            font-size: 16px;
            font-weight: bold;
        }
        
        /* Nomor Surat Jalan */
        .no-surat-jalan {
            position: absolute;
            top: 35mm;  /* 3.5cm dari atas */
            left: 100mm;  /* 10cm dari kiri */
            font-size: 16px;
            font-weight: bold;
        }
        
        /* No Plat Section */
        .no-plat {
            position: absolute;
            top: 55mm;  /* 5.5cm dari atas */
            left: 85mm; /* 8.5cm dari kiri */
            font-size: 16px;
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
            font-size: 13px;
            font-weight: bold;
            text-align: left;
            padding-right: 5mm;
        }
        
        /* Tipe Kontainer dengan posisi absolut */
        .tipe-kontainer {
            position: absolute;
            top: 90mm;  /* 9cm dari atas */
            left: 60mm; /* 6cm dari kiri */
            font-size: 17px;
            font-weight: bold;
        }
        
        /* Nama Pengirim dengan posisi absolut */
        .nama-pengirim {
            position: absolute;
            top: 115mm;  /* 11.5cm dari atas */
            left: 110mm; /* 11cm dari kiri */
            font-size: 17px;
            font-weight: bold;
        }
        
        /* Nama Barang dengan posisi absolut */
        .nama-barang {
            position: absolute;
            top: 90mm;  /* 9cm dari atas */
            left: 110mm; /* 11cm dari kiri */
            font-size: 17px;
            font-weight: bold;
        }
        
        /* Tujuan Ambil dengan posisi absolut */
        .tujuan-ambil {
            position: absolute;
            top: 140mm;  /* 14cm dari atas */
            left: 110mm; /* 11cm dari kiri */
            font-size: 17px;
            font-weight: bold;
        }
        
        /* Nomor Seal dengan posisi absolut */
        .nomor-seal {
            position: absolute;
            top: 140mm;  /* 14cm dari atas */
            left: 10mm;  /* 1cm dari kiri */
            font-size: 24px; /* Increased from 17px */
            font-weight: bold;
        }
        
        /* No. Voyage at top-left */
        .no-voyage {
            position: absolute;
            top: 45mm; /* 4.5cm */
            left: 35mm; /* 3.5cm */
            font-size: 14px;
            font-weight: bold;
        }
        
        /* Tujuan Kirim dengan posisi absolut */
        .tujuan-kirim {
            position: absolute;
            top: 110mm;  /* 11cm dari atas */
            left: 10mm;  /* 1cm dari kiri */
            font-size: 17px;
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
            font-size: 16px;
            font-weight: bold;
            padding-right: 5mm;
        }
        
        .ukuran-col {
            width: 50mm;
            font-size: 16px;
            font-weight: bold;
            font-family: 'Times New Roman', serif;
            padding-right: 5mm;
        }
        
        .pengirim-col {
            width: 50mm;
            font-size: 16px;
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
            font-size: 17px;
            font-weight: bold;
        }
        
        .supir-name {
            position: absolute;
            top: 190mm;  /* 19cm dari atas */
            left: 60mm;  /* 6cm dari kiri */
            text-align: center;
            font-size: 17px;
            font-weight: bold;
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

            /* Print-specific override for nomor-seal to ensure clarity on print */
            .nomor-seal {
                font-size: 26px !important;
                font-weight: bold !important;
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
            <div style="display: block; padding: 10px; background: #f0f0f0; margin-bottom: 10px; font-size: 14px; border: 1px solid #ccc;">
        <strong>PETUNJUK PRINT:</strong> Untuk menghilangkan URL dan nomor halaman, pastikan <strong>"Headers and footers"</strong> dinonaktifkan di pengaturan print browser (Ctrl+P → More settings → hapus centang "Headers and footers")
    </div>

    <div class="container">
        <!-- SESI 1: HEADER -->
        <div class="date-header">
            {{ $suratJalan->tanggal_muat ? \Carbon\Carbon::parse($suratJalan->tanggal_muat)->format('d-M-Y') : '' }}
        </div>
        
        <!-- NO VOYAGE -->
        <div class="no-voyage">
            {{ $suratJalan->no_voyage ?? '' }}
        </div>
        
        <!-- NOMOR SURAT JALAN removed -->
        
        <!-- SESI 2: NO PLAT KENDARAAN -->
        <div class="no-plat">
            {{ $suratJalan->no_plat ? strtoupper($suratJalan->no_plat) : '' }}
        </div>
        
        <!-- TIPE KONTAINER dengan posisi absolut -->
        <div class="tipe-kontainer">
            {{ $suratJalan->tipe_kontainer ? strtoupper($suratJalan->tipe_kontainer) : '' }}
        </div>
        
        <!-- NAMA PENGIRIM dengan posisi absolut -->
        <div class="nama-pengirim">
            {{ $suratJalan->pengirim ? strtoupper($suratJalan->pengirim) : '' }}
        </div>
        
        <!-- NAMA BARANG dengan posisi absolut -->
        <div class="nama-barang">
            {{ $suratJalan->jenis_barang ? strtoupper($suratJalan->jenis_barang) : ($suratJalan->order && $suratJalan->order->jenisBarang ? strtoupper($suratJalan->order->jenisBarang->nama) : '') }}
        </div>
        
        <!-- TUJUAN AMBIL dengan posisi absolut -->
        <div class="tujuan-ambil">
            {{ $suratJalan->tujuan_pengambilan ? strtoupper($suratJalan->tujuan_pengambilan) : '' }}
        </div>
        
        <!-- NOMOR SEAL dengan posisi absolut -->
        <div class="nomor-seal">
            {{ $suratJalan->no_seal ? strtoupper($suratJalan->no_seal) : '' }}
        </div>
        
        <!-- TUJUAN KIRIM dengan posisi absolut -->
        <div class="tujuan-kirim">
            {{ $suratJalan->tujuan_pengiriman ? strtoupper($suratJalan->tujuan_pengiriman) : '' }}
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
            {{ $suratJalan->supir ? strtoupper($suratJalan->supir) : '' }}
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
