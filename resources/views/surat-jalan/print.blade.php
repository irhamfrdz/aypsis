<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $suratJalan->no_surat_jalan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            color: #000;
        }
        .no-print {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
        }
        
        @media print {
            .no-print {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
        .form-container {
            position: relative;
            width: 467.72px;
            height: 609.45px;
            margin: 0 auto;
            transform-origin: top left;
        }
        .field {
            position: absolute;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: normal;
            color: #000;
        }
        .field-bold {
            font-weight: bold;
        }
        .field-small {
            font-size: 10px;
        }
        .field-medium {
            font-size: 12px;
        }
        .field-large {
            font-size: 14px;
        }
        .field-xlarge {
            font-size: 16px;
        }
        @media print {
            @page {
                size: 467.72px 609.45px;
                margin: 0;
            }
            .no-print {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .form-container {
                width: 467.72px;
                height: 609.45px;
                margin: 0;
                position: absolute;
                top: 0;
                left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Print button (hidden when printing) -->
    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 15px; font-size: 12px;">Cetak</button>
        <button onclick="window.close()" style="padding: 8px 15px; font-size: 12px; margin-left: 10px;">Tutup</button>
        <p style="margin-top: 10px; font-size: 11px; color: #666;">
            Template ini dirancang untuk dicetak pada form surat jalan pre-printed yang sudah ada.
        </p>
    </div>

    <div class="form-container">
        <!-- Posisi disesuaikan identik dengan PDF form -->
        
        <!-- Tanggal - Kanan Atas -->
        <div class="field" style="top: 32px; right: 15px;">
            {{ $suratJalan->formatted_tanggal_surat_jalan }}
        </div>
        
        <!-- Jenis Barang - Kolom Kiri -->
        <div class="field" style="top: 203px; left: 45px;">
            {{ $suratJalan->jenis_barang ?? ($suratJalan->order->jenisBarang->nama ?? '') }}
        </div>
        
        <!-- Tipe/Size Kontainer - Kolom Kanan -->
        <div class="field" style="top: 203px; left: 262px;">
            {{ $suratJalan->tipe_kontainer ?? '' }}{{ $suratJalan->size ? ' = ' . $suratJalan->size : '' }}
        </div>
        
        <!-- No. Seal - Kolom Kiri Baris 2 -->
        <div class="field field-bold" style="top: 248px; left: 45px;">
            {{ $suratJalan->no_seal ?? '' }}
        </div>
        
        <!-- Tujuan Pengiriman - Kolom Kanan Baris 2 -->
        <div class="field" style="top: 248px; left: 262px; width: 190px;">
            {{ $suratJalan->tujuanPengirimanRelation->nama ?? ($suratJalan->order->tujuan_kirim ?? '') }}
        </div>
        
        <!-- Alamat/Penerima - Kolom Kiri Baris 3 -->
        <div class="field" style="top: 321px; left: 45px;">
            {{ $suratJalan->alamat ?? ($suratJalan->tujuanPengambilanRelation->nama ?? ($suratJalan->order->tujuan_ambil ?? '')) }}
        </div>
        
        <!-- Tujuan Pengambilan - Kolom Kanan Baris 3 -->
        <div class="field" style="top: 321px; left: 262px;">
            {{ $suratJalan->tujuanPengambilanRelation->nama ?? ($suratJalan->order->tujuan_ambil ?? '') }}
        </div>
    </div>

</body>
</html>