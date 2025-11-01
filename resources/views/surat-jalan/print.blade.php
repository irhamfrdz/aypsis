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
            font-size: 12px;
            line-height: 1.2;
            color: #000;
        }
        .no-print {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
        }
        .form-container {
            position: relative;
            width: 210mm;
            height: 297mm; /* A4 Portrait */
            margin: 0 auto;
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
        .field-large {
            font-size: 13px;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .form-container {
                width: 210mm;
                height: 297mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Print button (hidden when printing) -->
    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 15px; font-size: 12px;">Cetak</button>
        <button onclick="window.close()" style="padding: 8px 15px; font-size: 12px; margin-left: 10px;">Tutup</button>
        <a href="{{ route('surat-jalan.download', $suratJalan->id) }}" style="display: inline-block; padding: 8px 15px; font-size: 12px; margin-left: 10px; background-color: #7c3aed; color: white; text-decoration: none; border-radius: 4px;">Download PDF</a>
        <p style="margin-top: 10px; font-size: 11px; color: #666;">
            Template ini dirancang untuk dicetak pada kertas formulir surat jalan yang sudah ada.
            Pastikan kertas terposisi dengan benar sebelum mencetak.
        </p>
    </div>

    <div class="form-container">
        <!-- Nomor Surat Jalan - posisi kanan atas -->
        <div class="field field-bold field-large" style="top: 22mm; right: 15mm;">
            {{ $suratJalan->no_surat_jalan }}
        </div>
        
        <!-- Tanggal - posisi kanan atas -->
        <div class="field" style="top: 32mm; right: 15mm;">
            {{ $suratJalan->formatted_tanggal_surat_jalan }}
        </div>
        
        <!-- Nama Pengirim -->
        <div class="field" style="top: 65mm; left: 20mm;">
            {{ $suratJalan->pengirim ?? $suratJalan->order->pengirim->nama ?? '' }}
        </div>
        
        <!-- Alamat Pengirim (jika ada) -->
        <div class="field field-small" style="top: 75mm; left: 20mm;">
            {{ $suratJalan->alamat_pengirim ?? '' }}
        </div>
        
        <!-- Nama Penerima -->
        <div class="field" style="top: 65mm; left: 110mm;">
            {{ $suratJalan->alamat ?? '' }}
        </div>
        
        <!-- Jenis Barang - baris pertama tabel -->
        <div class="field" style="top: 120mm; left: 20mm;">
            {{ $suratJalan->jenis_barang ?? $suratJalan->order->jenisBarang->nama ?? '' }}
        </div>
        
        <!-- Tujuan Pengambilan -->
        <div class="field field-small" style="top: 120mm; left: 70mm; width: 50mm;">
            {{ $suratJalan->tujuanPengambilanRelation->nama ?? $suratJalan->order->tujuan_ambil ?? '' }}
        </div>
        
        <!-- Tujuan Pengiriman -->
        <div class="field field-small" style="top: 120mm; left: 125mm; width: 60mm;">
            {{ $suratJalan->tujuanPengirimanRelation->nama ?? $suratJalan->order->tujuan_kirim ?? '' }}
        </div>
        
        <!-- No. Kontainer -->
        <div class="field" style="top: 140mm; left: 20mm;">
            {{ $suratJalan->no_kontainer ?? '' }}
        </div>
        
        <!-- Tipe/Size Kontainer -->
        <div class="field" style="top: 140mm; left: 70mm;">
            {{ $suratJalan->tipe_kontainer ?? '' }} {{ $suratJalan->size ?? '' }}
        </div>
        
        <!-- No. Seal -->
        <div class="field" style="top: 140mm; left: 125mm;">
            {{ $suratJalan->no_seal ?? '' }}
        </div>
        
        <!-- No. Plat Kendaraan -->
        <div class="field field-bold" style="top: 185mm; left: 20mm;">
            {{ $suratJalan->no_plat ?? '' }}
        </div>
        
        <!-- Nama Supir -->
        <div class="field field-bold" style="top: 195mm; left: 20mm;">
            {{ $suratJalan->supir ?? '' }}
        </div>
        
        <!-- Uang Jalan -->
        <div class="field" style="top: 205mm; left: 20mm;">
            @if($suratJalan->uang_jalan)
                Rp {{ number_format($suratJalan->uang_jalan, 0, ',', '.') }}
            @endif
        </div>
        
        <!-- Keterangan -->
        <div class="field field-small" style="top: 160mm; left: 20mm; width: 170mm;">
            {{ $suratJalan->keterangan ?? '' }}
        </div>
        
        <!-- No. Order -->
        <div class="field field-small" style="top: 50mm; left: 150mm;">
            {{ $suratJalan->order->nomor_order ?? '' }}
        </div>
    </div>

</body>
</html>