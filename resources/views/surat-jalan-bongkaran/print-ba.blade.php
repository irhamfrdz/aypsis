<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print BA - Surat Jalan Bongkaran</title>
    <style>
        @page {
            size: 210mm 330mm; /* Ukuran F4 */
            margin: 0; /* set 0 so absolute offsets are exact, outer print margins handled by browser */
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px; /* increased base font size */
        }
        /* Positioned pengirim data */
        .pengirim {
            position: absolute;
            top: 6.5cm; /* 6,5cm from top */
            left: 4cm;  /* 4cm from left */
            width: 12cm; /* reasonable width for address area */
            white-space: pre-wrap; /* keep new lines */
            line-height: 1.2;
            font-size: 14px;
        }
        /* Positioned penerima data */
        .penerima {
            position: absolute;
            top: 7.5cm; /* 7,5cm from top */
            left: 4cm;  /* 4cm from left */
            width: 12cm; /* same width to align with pengirim */
            white-space: pre-wrap;
            line-height: 1.2;
            font-size: 14px;
        }
        /* Positioned container number */
        .no-kontainer {
            position: absolute;
            top: 11cm; /* 11cm from top */
            left: 1cm;  /* 1cm from left */
            white-space: nowrap; /* container number single-line */
            font-weight: 600;
            font-size: 16px; /* larger to highlight container number */
        }
        /* Positioned nama barang (jenis barang) */
        .nama-barang {
            position: absolute;
            top: 11cm; /* 11cm from top */
            left: 8cm; /* 8cm from left */
            max-width: 11cm;
            white-space: pre-wrap;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
        }
        /* Positioned nama kapal */
        .nama-kapal {
            position: absolute;
            top: 6.25cm; /* 6.25cm from top */
            left: 18.5cm; /* 18.5cm from left */
            white-space: nowrap;
            font-weight: 600;
            font-size: 14px;
            text-align: left;
        }
        /* Positioned nomor voyage (format: nomor_voyage/BULAN_ROMAWI/TAHUN) */
        .no-voyage {
            position: absolute;
            top: 5.5cm; /* 5.5cm from top */
            left: 11.25cm; /* 11.25cm from left */
            white-space: nowrap;
            font-weight: 600;
            font-size: 14px;
            text-align: left;
        }
        /* Positioned pelabuhan route (asal - tujuan) */
        .pelabuhan-route {
            position: absolute;
            top: 8cm; /* 8cm from top */
            left: 18.5cm; /* 18.5cm from left */
            max-width: 11.5cm;
            white-space: nowrap;
            font-size: 14px;
            font-weight: 500;
            text-align: left;
        }
        /* Positioned name 'Alex' from bottom-right */
        .alex-name {
            position: absolute;
            bottom: 1.75cm; /* 1.75cm from bottom */
            right: 3.5cm; /* 3.5cm from right */
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            text-align: right;
        }
        @media print {
            /* ensure no printing artifacts from browser margins */
            html, body { width: 210mm; height: 330mm; }
        }
    </style>
</head>
<body>
    {{-- Pengirim (ambil dari tabel bls via $baData->pengirim) --}}
    @if(isset($baData) && !empty($baData->pengirim))
        <div class="pengirim">{!! nl2br(e($baData->pengirim)) !!}</div>
    @else
        <div class="pengirim">&nbsp;</div>
    @endif
    {{-- Penerima (ambil dari tabel bls via $baData->penerima) --}}
    @if(isset($baData) && !empty($baData->penerima))
        <div class="penerima">{!! nl2br(e($baData->penerima)) !!}</div>
    @else
        <div class="penerima">&nbsp;</div>
    @endif
    {{-- Nomor Kontainer (ambil dari tabel bls via $baData->no_kontainer) --}}
    @if(isset($baData) && !empty($baData->no_kontainer))
        <div class="no-kontainer">{{ e($baData->no_kontainer) }}</div>
    @else
        <div class="no-kontainer">&nbsp;</div>
    @endif
    {{-- Nama Barang (ambil dari tabel bls via $baData->jenis_barang) --}}
    @if(isset($baData) && !empty($baData->jenis_barang))
        <div class="nama-barang">{!! nl2br(e($baData->jenis_barang)) !!}</div>
    @else
        <div class="nama-barang">&nbsp;</div>
    @endif
    {{-- Nama Kapal (ambil dari tabel bls via $baData->nama_kapal) --}}
    @if(isset($baData) && !empty($baData->nama_kapal))
        <div class="nama-kapal">{{ e($baData->nama_kapal) }}</div>
    @else
        <div class="nama-kapal">&nbsp;</div>
    @endif
    {{-- Nomor Voyage (format: nomor_voyage/BULAN_ROMAWI/TAHUN) --}}
    @php
        $voyageNumber = $baData->no_voyage ?? '';
        $dateForVoyage = $baData->tanggal_ba ?? now();
        try {
            $carbon = \Carbon\Carbon::parse($dateForVoyage);
        } catch (\Exception $e) {
            $carbon = \Carbon\Carbon::now();
        }
        $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $voyageFormatted = '';
        if(!empty($voyageNumber)) {
            $voyageFormatted = $voyageNumber . '/' . ($romanMonths[$carbon->month] ?? '') . '/' . $carbon->format('Y');
        }
    @endphp
    @if(!empty($voyageFormatted))
        <div class="no-voyage">{{ e($voyageFormatted) }}</div>
    @else
        <div class="no-voyage">&nbsp;</div>
    @endif
    {{-- Pelabuhan Asal - Pelabuhan Tujuan (ambil dari tabel bls via $baData->pelabuhan_asal dan $baData->pelabuhan_tujuan) --}}
    @php
        // Helper to map 'sunda kelapa' (any case/format) to 'Jakarta'
        $mapPelabuhan = function ($value) {
            $v = trim((string) ($value ?? ''));
            if ($v === '') return '';
            $clean = mb_strtolower(preg_replace('/[^a-z0-9 ]+/i', ' ', $v));
            $clean = preg_replace('/\s+/', ' ', $clean);
            if (in_array($clean, ['sunda kelapa', 'sundakelapa', 'sunda_kelapa', 'sunda-kelapa'], true)) {
                return 'Jakarta';
            }
            return $v;
        };

        $asal = $mapPelabuhan($baData->pelabuhan_asal ?? '');
        $tujuan = $mapPelabuhan($baData->pelabuhan_tujuan ?? '');
        $pelabuhanText = '';
        if ($asal !== '' || $tujuan !== '') {
            $pelabuhanText = trim(($asal ?? '') . ' - ' . ($tujuan ?? ''));
        }
    @endphp
    @if(!empty($pelabuhanText))
        <div class="pelabuhan-route">{{ e($pelabuhanText) }}</div>
    @else
        <div class="pelabuhan-route">&nbsp;</div>
    @endif
    {{-- Nama Alex (static) --}}
    <div class="alex-name">Alex</div>
</body>
</html>