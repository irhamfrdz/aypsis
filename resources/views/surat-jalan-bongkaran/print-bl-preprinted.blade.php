<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print BL (Bill of Lading) - Preprinted</title>
    <style>
        /* A4 Paper size */
        @page { size: 210mm 297mm; margin: 0mm; }

        html, body { margin: 0; padding: 0; font-family: 'Arial', sans-serif; }
        
        /* Container matches A4 exact print paper size (no padding so absolute positions map to page origin) */
        .container { width: 210mm; height: 297mm; position: relative; box-sizing: border-box; padding: 0mm; margin: 0mm; }

        /* General absolute positioning class */
        .abs-text {
            position: absolute;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            line-height: 1.4;
        }

        /* 
           POSISI KIRA-KIRA BERDASARKAN GAMBAR PDF BL Alexindo
           Gunakan cm untuk mempermudah. A4 = 21cm x 29.7cm
        */
        
        /* Pengirim Barang (Kiri atas) */
        .pengirim { top: 2.5cm; left: 1cm; width: 8.5cm; }
        
        /* Penerima Barang (Bawah pengirim) */
        .penerima { top: 5.5cm; left: 1cm; width: 8.5cm; }
        
        /* Pihak Yang Memberitahu / Notify Party (Bawah penerima) */
        .notify-party { top: 9.5cm; left: 1cm; width: 8.5cm; }
        
        /* B/L No (Kanan atas, sebelah logo) */
        .bl-no { top: 1.5cm; left: 13.5cm; font-size: 16px; font-weight: bolder; }
        
        /* Nama Kapal dan No. Pelayaran */
        .nama-kapal { top: 12.7cm; left: 1cm; width: 7.5cm; }
        
        /* Pelabuhan Transit */
        .pelabuhan-transit { top: 12.7cm; left: 8.5cm; width: 7.5cm; }
        
        /* Pelabuhan Muat */
        .pelabuhan-muat { top: 12.7cm; left: 16.5cm; width: 4cm; }
        
        /* Tempat Penerimaan */
        .tempat-penerimaan { top: 14.2cm; left: 1cm; width: 7.5cm; }
        
        /* Tempat Pengiriman */
        .tempat-pengiriman { top: 14.2cm; left: 8.5cm; width: 7.5cm; }
        
        /* Jumlah B/L Asli */
        .jumlah-bl { top: 14.2cm; left: 16.5cm; width: 4cm; }
        
        /* --- AREA TENGAH (TABEL BARANG) --- */
        /* Header tabel kira-kira di 15.5cm, isi tabel mulai di 16cm */
        
        /* No. Peti Kemas/Tanda dan Nomor */
        .no-kontainer { top: 16cm; left: 1.5cm; width: 6.5cm; text-align: center; font-size: 14px; }
        .no-seal { top: 16.6cm; left: 1.5cm; width: 6.5cm; text-align: center; }
        
        /* Keterangan Barang Yang Dimuat */
        .keterangan-barang { top: 16cm; left: 8.5cm; width: 8cm; }
        
        /* Berat Kotor (KG) */
        .berat-kotor { top: 16cm; left: 16.8cm; width: 2cm; text-align: right; }
        
        /* Ukuran (M3) */
        .ukuran-m3 { top: 16cm; left: 19cm; width: 1.5cm; text-align: right; }
        
        /* --- AREA BAWAH --- */
        /* Dikirim Di Kapal TGL */
        .tgl-dikirim { top: 26.5cm; left: 1.5cm; width: 4cm; font-size: 12px; }
        
        /* Tempat dan Tanggal dikeluarkan */
        .tempat-tgl-keluar { top: 28.5cm; left: 8cm; width: 8cm; text-align: center; font-size: 12px;}

    </style>
    <style>
        /* Print-specific overrides */
        @media print {
            @page { size: 210mm 297mm; margin: 0mm; }
            html, body { margin: 0 !important; padding: 0 !important; }
            .container { padding: 0 !important; margin: 0 !important; }
            /* Hide non-content elements on print if needed */
            header, nav, .header, .nav { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Pengirim -->
        <div class="abs-text pengirim">
            {{ strtoupper($printData->pengirim ?? '') }}
        </div>
        
        <!-- Penerima -->
        <div class="abs-text penerima">
            {{ strtoupper($printData->penerima ?? '') }}
        </div>
        
        <!-- Notify Party (Menggunakan data penerima atau contact person jika ada) -->
        <div class="abs-text notify-party">
            {{ strtoupper($printData->penerima ?? '') }} <br>
            {{ strtoupper($printData->contact_person ?? '') }}
        </div>
        
        <!-- B/L No -->
        <div class="abs-text bl-no">
            {{ strtoupper($printData->nomor_bl ?? '') }}
        </div>
        
        <!-- Kapal & Voyage -->
        <div class="abs-text nama-kapal">
            {{ strtoupper($printData->nama_kapal ?? '') }} V.{{ strtoupper($printData->no_voyage ?? '') }}
        </div>
        
        <!-- Pelabuhan Transit (Kosongkan atau isi sesuai data) -->
        <div class="abs-text pelabuhan-transit">
            
        </div>
        
        <!-- Pelabuhan Muat -->
        <div class="abs-text pelabuhan-muat">
            {{ strtoupper($printData->pelabuhan_asal ?? 'JAKARTA') }}
        </div>
        
        <!-- Tempat Penerimaan -->
        <div class="abs-text tempat-penerimaan">
            {{ strtoupper($printData->pelabuhan_asal ?? 'JAKARTA') }}
        </div>
        
        <!-- Tempat Pengiriman / Pelabuhan Tujuan -->
        <div class="abs-text tempat-pengiriman">
            {{ strtoupper($printData->pelabuhan_tujuan ?? '') }}
        </div>
        
        <!-- Jumlah B/L Asli -->
        <div class="abs-text jumlah-bl">
            3 (TIGA)
        </div>
        
        <!-- Tanda dan Nomor Kontainer -->
        <div class="abs-text no-kontainer">
            {{ strtoupper($printData->nomor_kontainer ?? '') }} <br>
            {{ $printData->size_kontainer ?? '' }} {{ strtoupper($printData->tipe_kontainer ?? '') }}
        </div>
        <div class="abs-text no-seal">
            SEAL: {{ strtoupper($printData->no_seal ?? '') }}
        </div>
        
        <!-- Keterangan Barang -->
        <div class="abs-text keterangan-barang">
            STC: <br>
            {{ $printData->kuantitas ?? '' }} {{ strtoupper($printData->satuan ?? '') }} <br>
            {{ strtoupper($printData->nama_barang ?? '') }} <br>
            <br>
            FREIGHT PREPAID
        </div>
        
        <!-- Berat -->
        <div class="abs-text berat-kotor">
            
        </div>
        
        <!-- Ukuran -->
        <div class="abs-text ukuran-m3">
            
        </div>
        
        <!-- TGL Dikirim -->
        <div class="abs-text tgl-dikirim">
            {{ $printData->tanggal_bl ? \Carbon\Carbon::parse($printData->tanggal_bl)->format('d-M-Y') : '' }}
        </div>
        
        <!-- Tempat & Tanggal Dikeluarkan -->
        <div class="abs-text tempat-tgl-keluar">
            JAKARTA, {{ $printData->tanggal_bl ? \Carbon\Carbon::parse($printData->tanggal_bl)->format('d-M-Y') : '' }}
        </div>
        
    </div>
</body>
</html>
