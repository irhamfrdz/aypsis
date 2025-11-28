<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Bongkaran - Blank</title>
    <style>
        /* File cleared for re-measurement. Add styles as needed. */
        html, body { margin: 0; padding: 0; }
        .container { width: 100%; height: 100%; position: relative; }

        /* Tanggal Surat Jalan: posisi absolute sesuai permintaan */
        .date-header {
            position: absolute;
            top: 1.25cm; /* 1.25cm dari atas */
            left: 10cm;  /* 10cm dari kiri */
            font-size: 16px;
            font-weight: bold;
        }

        /* Nomor Voyage: posisi absolute sesuai permintaan */
        .no-voyage {
            position: absolute;
            top: 5cm;    /* 5cm dari atas */
            left: 3.5cm;  /* 3.5cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }

        /* Nama Kapal: posisi absolute sesuai permintaan */
        .nama-kapal {
            position: absolute;
            top: 6cm;    /* 6cm dari atas */
            left: 3.5cm;  /* 3.5cm dari kiri */
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
            top: 4.25cm; /* 4.25cm dari atas */
            left: 13cm;   /* 13cm dari kiri */
            font-size: 14px;
            font-weight: bold;
        }
        /* Nomor Kontainer: posisi absolute sesuai permintaan */
        .no-kontainer {
            position: absolute;
            top: 9cm; /* 9cm dari atas */
            left: 1cm; /* 1cm dari kiri */
            font-size: 16px;
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
            font-size: 24px;
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
    </style>
    
</head>
<body>
    <!-- File intentionally cleared for re-measurement -->
    <div class="container">
        <!-- Tanggal Surat Jalan (posisi top 1.25cm, left 10cm) -->
        <div class="date-header">
            {{ $suratJalanBongkaran->tanggal_surat_jalan ? \Carbon\Carbon::parse($suratJalanBongkaran->tanggal_surat_jalan)->format('d-M-Y') : '' }}
        </div>

        <!-- Nomor Voyage (posisi top 5cm, left 3.5cm) -->
        <div class="no-voyage">
            {{ strtoupper($suratJalanBongkaran->no_voyage ?? '') }}
        </div>

        <!-- Nama Kapal (posisi top 6cm, left 3.5cm) -->
        <div class="nama-kapal">
            {{-- Print ship name property instead of the whole model (which renders JSON) --}}
            {{ strtoupper(optional($suratJalanBongkaran->kapal)->nama_kapal ?? ($suratJalanBongkaran->nama_kapal ?? '')) }}
        </div>
        
        <!-- Nomor Plat (posisi top 4.5cm, left 8cm) -->
        <div class="no-plat">
            {{ strtoupper($suratJalanBongkaran->no_plat ?? '') }}
        </div>
        
        <!-- Nomor BL (posisi top 4.25cm, left 13cm) -->
        <div class="no-bl">
            {{ strtoupper($suratJalanBongkaran->bl->nomor_bl ?? $suratJalanBongkaran->no_bl ?? $suratJalanBongkaran->noBillOfLading ?? '') }}
        </div>
        <!-- Nomor Kontainer (posisi top 9cm, left 1cm) -->
        <div class="no-kontainer">
            {{ strtoupper($suratJalanBongkaran->no_kontainer ?? '') }}
        </div>
        <!-- Nama Barang (posisi top 9cm, left 10.5cm) -->
        <div class="nama-barang-abs">
            {{ $suratJalanBongkaran->jenis_barang ? strtoupper($suratJalanBongkaran->jenis_barang) : ($suratJalanBongkaran->order && $suratJalanBongkaran->order->jenisBarang ? strtoupper($suratJalanBongkaran->order->jenisBarang->nama) : '') }}
        </div>
        <!-- Seal number (posisi top 11.5cm, left 1cm) -->
        <div class="seal-abs">
            {{ $suratJalanBongkaran->no_seal ? strtoupper($suratJalanBongkaran->no_seal) : '' }}
        </div>
        <!-- Pelabuhan Tujuan (posisi top 15cm, left 1cm) -->
        <div class="pelabuhan-abs">
            {{ $suratJalanBongkaran->pelabuhan_tujuan ? strtoupper($suratJalanBongkaran->pelabuhan_tujuan) : ($suratJalanBongkaran->tujuan_pengiriman ? strtoupper($suratJalanBongkaran->tujuan_pengiriman) : '') }}
        </div>
        <!-- Add layout elements here while you remeasure the print layout -->
    </div>
    
</body>
</html>