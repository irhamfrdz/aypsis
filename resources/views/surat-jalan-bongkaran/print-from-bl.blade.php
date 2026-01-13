<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Bongkaran - Print dari Manifest</title>
    <style>
        /* Same paper size as surat-jalan/print.blade.php */
        @page { size: 165mm 215mm; margin: 0mm; }

        html, body { margin: 0; padding: 0; }
        /* Container now matches the exact print paper size (no padding so absolute positions map to page origin) */
        .container { width: 165mm; height: 215mm; position: relative; box-sizing: border-box; padding: 0mm; margin: 0mm; }

        /* Tanggal Surat Jalan: posisi absolute sesuai permintaan */
        .date-header {
            position: fixed; /* anchor to the page so it doesn't get cropped */
            top: 1cm; /* 1cm dari atas */
            left: 10.5cm;  /* 10.5cm dari kiri */
            z-index: 1000; /* ensure visibility */
            color: #000; /* force black color on print */
            font-size: 16px;
            font-weight: bold;
        }

        /* Nomor Voyage: posisi absolute sesuai permintaan */
        .no-voyage {
            position: absolute;
            top: 5.5cm;    /* 5.5cm dari atas */
            left: 3.5cm;  /* 3.5cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }

        /* Nama Kapal: posisi absolute sesuai permintaan */
        .nama-kapal {
            position: absolute;
            top: 4.8cm;    /* 4.8cm dari atas - sejajar dengan nomor BL */
            left: 3.5cm;   /* 3.5cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }

        /* Nomor Plat: posisi absolute sesuai permintaan */
        .no-plat {
            position: absolute;
            top: 4.5cm; /* 4.5cm dari atas */
            left: 8cm;   /* 8cm dari kiri */
            font-size: 16px;
            font-weight: bold;
        }

        /* Nomor BL: posisi absolute sesuai permintaan */
        .no-bl {
            position: absolute;
            top: 4.8cm; /* 4.8cm dari atas (diturunkan dari 4.25cm) */
            left: 12cm;   /* 12cm dari kiri (digeser 1cm ke kiri) */
            font-size: 18px; /* diperbesar dari 14px */
            font-weight: bold;
        }
        /* Nomor Kontainer: posisi absolute sesuai permintaan */
        .no-kontainer {
            position: absolute;
            top: 9cm; /* 9cm dari atas */
            left: 1cm; /* 1cm dari kiri */
            font-size: 20px;
            font-weight: bold;
        }
        /* Jenis Pengiriman: posisi absolute */
        .jenis-pengiriman-abs {
            position: absolute;
            top: 9cm; /* 9cm dari atas */
            left: 6cm; /* 6cm dari kiri */
            font-size: 16px;
            font-weight: bold;
        }
        /* Tipe Kontainer: posisi absolute */
        .tipe-kontainer-abs {
            position: absolute;
            top: 9.5cm; /* 9.5cm dari atas */
            left: 6cm; /* 6cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }
        /* Ukuran Kontainer: posisi absolute dibawah tipe kontainer */
        .ukuran-kontainer-abs {
            position: absolute;
            top: 10.2cm; /* 10.2cm dari atas - lebih kebawah agar tidak overlap */
            left: 6cm; /* 6cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }
        /* Nama Barang: posisi absolute sesuai permintaan */
        .nama-barang-abs {
            position: absolute;
            top: 9cm; /* 9cm dari atas */
            left: 10.5cm; /* 10.5cm dari kiri */
            font-size: 17px;
            font-weight: bold;
        }
        /* Seal number: posisi absolute sesuai permintaan */
        .seal-abs {
            position: absolute;
            top: 11.5cm; /* 11.5cm dari atas */
            left: 1cm; /* 1cm dari kiri */
            font-size: 18px;
            font-weight: bold;
        }
        /* Pelabuhan tujuan: posisi absolute sesuai permintaan */
        .pelabuhan-abs {
            position: absolute;
            top: 15cm; /* 15cm dari atas */
            left: 1cm; /* 1cm dari kiri */
            font-size: 16px;
            font-weight: bold;
        }
        /* Penerima: posisi absolute (atas 3.5cm, kiri 11cm) */
        .penerima-abs {
            position: absolute;
            top: 3.5cm; /* 3.5cm dari atas */
            left: 11cm; /* 11cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }
        /* Tujuan Pengambilan: posisi absolute (atas 14.5cm, kiri 10.5cm) */
        .tujuan-pengambilan-abs {
            position: absolute;
            top: 14.5cm; /* 14.5cm dari atas */
            left: 10.5cm; /* 10.5cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }
    </style>
    <style>
        /* Print-specific overrides to avoid browser margins or headers/scale issues */
        @media print {
            @page { size: 165mm 215mm; margin: 0mm; }
            html, body { margin: 0 !important; padding: 0 !important; }
            .container { padding: 0 !important; margin: 0 !important; }
            /* Hide non-content elements on print if needed */
            header, nav, .header, .nav { display: none !important; }
        }
    </style>
    
</head>
<body>
    <!-- 
        ============================================================
        DATA DIAMBIL DARI TABLE MANIFEST
        Print ini menggunakan data dari tabel 'manifests'
        Controller: SuratJalanBongkaranController@printFromBl
        ============================================================
    -->
    <div class="container">
        <!-- Tanggal Surat Jalan (posisi top 1.25cm, left 10cm) - Data dari Manifest -->
        <div class="date-header">
            {{ $printData->tanggal_surat_jalan ? \Carbon\Carbon::parse($printData->tanggal_surat_jalan)->format('d-M-Y') : '' }}
        </div>

        <!-- Nomor Voyage (posisi top 5cm, left 3.5cm) - Data dari Manifest -->
        <div class="no-voyage">
            {{ strtoupper($printData->no_voyage ?? '') }}
        </div>

        <!-- Nama Kapal (posisi top 6cm, left 3.5cm) - Data dari Manifest -->
        <div class="nama-kapal">
            {{ strtoupper($printData->nama_kapal ?? '') }}
        </div>
        
        <!-- Nomor Plat (posisi top 4.5cm, left 8cm) - Data dari Manifest -->
        <div class="no-plat">
            {{ strtoupper($printData->no_plat ?? '') }}
        </div>
        
        <!-- Nomor BL (posisi top 4.25cm, left 13cm) - Data dari Manifest (manifest.nomor_bl) -->
        <div class="no-bl">
            BL - {{ strtoupper($printData->no_bl ?? '') }}
        </div>
        
        <!-- Nomor Kontainer (posisi top 9cm, left 1cm) - Data dari Manifest (manifest.nomor_kontainer) -->
        <div class="no-kontainer">
            {{ strtoupper($printData->no_kontainer ?? '') }}
        </div>
        
        <!-- Jenis Pengiriman (posisi top 9cm, left 6cm) - Data dari Manifest -->
        <div class="jenis-pengiriman-abs">
            {{ $printData->jenis_pengiriman ? strtoupper($printData->jenis_pengiriman) : '' }}
        </div>
        
        <!-- Tipe Kontainer (posisi top 9.5cm, left 6cm) - Data dari Manifest -->
        @php
            $tipeKontainerText = 'FCL'; // Default
            if (!empty($printData->tipe_kontainer)) {
                $tipeKontainerText = strtoupper($printData->tipe_kontainer);
            }
        @endphp
        <div class="tipe-kontainer-abs">
            {{ $tipeKontainerText }}
        </div>
        
        <!-- Ukuran Kontainer: CONT 1x + ukuran (posisi top 10cm, left 6cm) - Data dari Manifest -->
        @php
            $sizeKontainer = $printData->size_kontainer ?? $printData->size ?? '';
            // Format ukuran: jika hanya angka, tambahkan 'ft'
            if (!empty($sizeKontainer)) {
                if (!str_contains(strtolower($sizeKontainer), 'ft') && !str_contains(strtoupper($sizeKontainer), 'HC')) {
                    $sizeKontainer = $sizeKontainer . 'ft';
                }
            }
            $ukuranText = 'CONT 1x' . (!empty($sizeKontainer) ? ' ' . strtoupper($sizeKontainer) : '');
        @endphp
        <div class="ukuran-kontainer-abs">
            {{ $ukuranText }}
        </div>
        
        <!-- Nama Barang (posisi top 9cm, left 10.5cm) - Data dari Manifest (manifest.nama_barang) -->
        <div class="nama-barang-abs">
            {{ strtoupper($printData->jenis_barang ?? '') }}
        </div>
        
        <!-- Penerima (posisi top 3.5cm, left 11cm) - Data dari Manifest -->
        <div class="penerima-abs">
            {{ strtoupper($printData->penerima ?? '') }}
        </div>
        
        <!-- Tujuan Pengambilan (posisi top 14.5cm, left 10.5cm) - Data dari Manifest -->
        <div class="tujuan-pengambilan-abs">
            {{ strtoupper($printData->tujuan_pengambilan ?? '') }}
        </div>
        
        <!-- Seal number (posisi top 11.5cm, left 1cm) - Data dari Manifest (manifest.no_seal) -->
        <div class="seal-abs">
            {{ $printData->no_seal ? strtoupper($printData->no_seal) : '' }}
        </div>
        
        <!-- Pelabuhan Tujuan (posisi top 15cm, left 1cm) - Data dari Manifest (manifest.pelabuhan_tujuan) -->
        <div class="pelabuhan-abs">
            {{ $printData->pelabuhan_tujuan ? strtoupper($printData->pelabuhan_tujuan) : ($printData->tujuan_pengiriman ? strtoupper($printData->tujuan_pengiriman) : '') }}
        </div>
    </div>
    
</body>
</html>
