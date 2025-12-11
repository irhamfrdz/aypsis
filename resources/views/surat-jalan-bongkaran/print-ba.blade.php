<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Bongkaran - Print Preview</title>
    <style>
        @page {
            size: 8.5in 13in; /* Folio paper size */
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 8.5in;
            height: 13in;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 8.5in;
            height: 13in;
            position: relative;
        }

        .pengirim {
            position: absolute;
            top: 6.75cm;
            left: 4cm;
            font-size: 14px;
            font-weight: bold;
        }

        .penerima {
            position: absolute;
            top: 8cm;
            left: 4cm;
            font-size: 14px;
            font-weight: bold;
        }

        .nama-kapal {
            position: absolute;
            top: 6.5cm;
            left: 17.5cm;
            font-size: 14px;
            font-weight: bold;
        }

        .alamat-pengiriman {
            position: absolute;
            top: 8cm;
            left: 4cm;
            font-size: 14px;
            font-weight: bold;
        }

        .pelabuhan-info {
            position: absolute;
            top: 8cm;
            left: 17.5cm;
            font-size: 14px;
            font-weight: bold;
        }

        .nomor-kontainer {
            position: absolute;
            top: 11cm;
            left: 1cm;
            font-size: 14px;
            font-weight: bold;
        }

        .nama-barang {
            position: absolute;
            top: 10cm;
            left: 9cm;
            max-width: 9.59cm; /* Page width 21.59cm - left 9cm - right margin 3cm */
            font-size: 14px;
            font-weight: bold;
            word-wrap: break-word;
            white-space: normal;
        }

        .unit-kontainer {
            position: absolute;
            top: 10cm;
            left: 4.5cm;
            font-size: 14px;
            font-weight: bold;
        }

        .pelabuhan-info {
            position: absolute;
            top: 8cm;
            left: 17.5cm;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body onload="window.print()">
    @php
        // Function to convert month number to Roman numerals
        function toRoman($num) {
            $romans = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
                6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
                11 => 'XI', 12 => 'XII'
            ];
            return $romans[$num] ?? '';
        }

        // Get current date
        $currentDate = now();
        $bulanRomawi = toRoman($currentDate->month);
        $tahun = $currentDate->year;
        
        // Format: nomor BL/nomor voyage/bulan(romawi)/tahun
        $nomorBlInfo = strtoupper($baData->no_bl ?? '') . '/' . 
                       strtoupper($baData->no_voyage ?? '') . '/' . 
                       $bulanRomawi . '/' . 
                       $tahun;

        // Replace "SUNDA KELAPA" with "JAKARTA" for display
        $pelabuhanAsal = strtoupper($baData->pelabuhan_asal ?? '');
        $pelabuhanTujuan = strtoupper($baData->pelabuhan_tujuan ?? '');
        
        if ($pelabuhanAsal === 'SUNDA KELAPA') {
            $pelabuhanAsal = 'JAKARTA';
        }
        if ($pelabuhanTujuan === 'SUNDA KELAPA') {
            $pelabuhanTujuan = 'JAKARTA';
        }
        
        $pelabuhanInfo = $pelabuhanAsal . ' - ' . $pelabuhanTujuan;
    @endphp

    <div class="container">
        <!-- Nomor BL Info (posisi top 5.5cm, left 11cm) -->
        <div class="nomor-bl-info">
            {{ $nomorBlInfo }}
        </div>

        <!-- Pengirim (posisi top 6.5cm, left 4cm) -->
        <div class="pengirim">
            {{ strtoupper($baData->pengirim ?? '') }}
        </div>

        <!-- Penerima (posisi top 8cm, left 4cm) -->
        <div class="penerima">
            {{ strtoupper($baData->penerima ?? '') }}
        </div>

        <!-- Nama Kapal (posisi top 6.5cm, left 17.5cm) -->
        <div class="nama-kapal">
            {{ strtoupper($baData->nama_kapal ?? '') }}
        </div>

        <!-- Alamat Pengiriman (posisi top 8cm, left 4cm) -->
        <div class="alamat-pengiriman">
            {{ strtoupper($baData->alamat_pengiriman ?? '') }}
        </div>

        <!-- Pelabuhan Info (posisi top 8cm, left 17.5cm) -->
        <div class="pelabuhan-info">
            {{ $pelabuhanInfo }}
        </div>

        <!-- Nomor Kontainer (posisi top 10cm, left 1cm) -->
        <div class="nomor-kontainer">
            {{ strtoupper($baData->no_kontainer ?? '') }}
        </div>

        <!-- Unit Kontainer (posisi top 10cm, left 4.5cm) -->
        <div class="unit-kontainer">
            1 UNIT
        </div>

        <!-- Nama Barang (posisi top 10cm, left 9cm, max-width 9.59cm) -->
        <div class="nama-barang">
            {{ strtoupper($baData->jenis_barang ?? '') }}
        </div>
    </div>
</body>
</html>
