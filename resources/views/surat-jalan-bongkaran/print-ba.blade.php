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
            top: 6.5cm;
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
    </style>
</head>
<body onload="window.print()">
    <div class="container">
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
    </div>
</body>
</html>
