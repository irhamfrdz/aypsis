<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print BA Pre-printed - Surat Jalan Bongkaran</title>
    <style>
        @page {
            size: 210mm 330mm; /* Ukuran F4 */
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }
        /* Positioned fields based on Alexindo template */
        .pengirim {
            position: absolute;
            top: 7.2cm;
            left: 4.5cm;
            width: 9.5cm;
            font-weight: bold;
            line-height: 1.2;
            white-space: pre-wrap;
        }
        .penerima {
            position: absolute;
            top: 8.8cm;
            left: 4.5cm;
            width: 9.5cm;
            font-weight: bold;
            line-height: 1.2;
            white-space: pre-wrap;
        }
        .penerima-cp {
            position: absolute;
            top: 10.3cm;
            left: 4.5cm;
            font-weight: bold;
        }
        .nama-kapal {
            position: absolute;
            top: 7.1cm;
            left: 17.5cm;
            font-weight: bold;
            white-space: nowrap;
        }
        .tgl-berangkat {
            position: absolute;
            top: 8.1cm;
            left: 17.5cm;
            font-weight: bold;
            white-space: nowrap;
        }
        .dari-tujuan {
            position: absolute;
            top: 9.1cm;
            left: 17.5cm;
            font-weight: bold;
            white-space: nowrap;
        }
        
        /* Table content */
        .code-merek {
            position: absolute;
            top: 11.5cm;
            left: 0.5cm;
            width: 4.5cm;
            font-weight: bold;
            word-wrap: break-word;
        }
        .banyaknya {
            position: absolute;
            top: 11.5cm;
            left: 5.5cm;
            width: 6.5cm;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }
        .banyaknya-2 {
            position: absolute;
            top: 20cm;
            left: 5.5cm;
            width: 6.5cm;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }
        .uraian {
            position: absolute;
            top: 11.5cm;
            left: 12.5cm;
            width: 8cm;
            font-weight: bold;
            white-space: pre-wrap;
            line-height: 1.3;
        }
        
        /* Bottom fields */
        .jenis-barang {
            position: absolute;
            bottom: 8.5cm;
            left: 3.5cm;
            font-weight: bold;
        }
        .tgl-bawah {
            position: absolute;
            bottom: 5.5cm;
            left: 2cm;
            font-weight: bold;
        }
        .no-voyage {
            position: absolute;
            top: 5.4cm;
            left: 10.5cm;
            font-weight: bold;
        }
        
        @media print {
            html, body { width: 210mm; height: 330mm; }
        }
    </style>
</head>
<body>
    {{-- Pengirim --}}
    @if(isset($baData) && !empty($baData->pengirim))
        <div class="pengirim">{!! nl2br(e($baData->pengirim)) !!}</div>
    @endif
    
    {{-- Penerima --}}
    @if(isset($baData) && !empty($baData->penerima))
        <div class="penerima">{!! nl2br(e($baData->penerima)) !!}</div>
    @endif
    
    {{-- Contact Person --}}
    @if(isset($baData) && !empty($baData->contact_person))
        <div class="penerima-cp">CP: {{ e($baData->contact_person) }}</div>
    @endif
    
    {{-- Nama Kapal --}}
    @if(isset($baData) && !empty($baData->nama_kapal))
        <div class="nama-kapal">{{ e($baData->nama_kapal) }}</div>
    @endif
    
    {{-- Tanggal Berangkat (Tgl BA) --}}
    @php
        $tanggalBa = '';
        if (isset($baData->manifest) && !empty($baData->manifest->tanggal_berangkat)) {
            try {
                $tanggalBa = \Carbon\Carbon::parse($baData->manifest->tanggal_berangkat)->format('d-M-Y');
            } catch (\Exception $e) {}
        }
        if (empty($tanggalBa) && isset($baData->tanggal_ba)) {
            try {
                $tanggalBa = \Carbon\Carbon::parse($baData->tanggal_ba)->format('d-M-Y');
            } catch (\Exception $e) {}
        }
    @endphp
    @if(!empty($tanggalBa))
        <div class="tgl-berangkat">{{ e($tanggalBa) }}</div>
        <div class="tgl-bawah">{{ e($tanggalBa) }}</div>
    @endif
    
    {{-- Dari/Tujuan (Pelabuhan Route) --}}
    @php
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

        $voyageCheck = strtolower($baData->no_voyage ?? '');
        $pelabuhanText = '';
        if (str_contains($voyageCheck, 'bj')) {
            $pelabuhanText = 'Batam - Jakarta';
        } elseif (str_contains($voyageCheck, 'pj')) {
            $pelabuhanText = 'Pinang - Jakarta';
        } else {
            $asal = $mapPelabuhan($baData->pelabuhan_asal ?? '');
            $tujuan = $mapPelabuhan($baData->pelabuhan_tujuan ?? '');
            if ($asal !== '' || $tujuan !== '') {
                $pelabuhanText = trim(($asal ?? '') . ' - ' . ($tujuan ?? ''));
            }
        }
    @endphp
    @if(!empty($pelabuhanText)) 
        <div class="dari-tujuan">{{ e($pelabuhanText) }}</div>
    @endif
    
    {{-- CODE/MEREK (No Kontainer) --}}
    @if(isset($baData) && !empty($baData->no_kontainer))
        <div class="code-merek">{{ e($baData->no_kontainer) }}</div>
    @endif
    
    {{-- BANYAKNYA (Unit) --}}
    @php
        $unitText = '';
        if (isset($baData->kuantitas) && !empty($baData->kuantitas)) {
            $kuantitas = $baData->kuantitas;
            $satuan = $baData->satuan ?? 'unit';
            $unitText = $kuantitas . ' ' . $satuan;
        } elseif (isset($baData->manifest) && !empty($baData->manifest->kuantitas)) {
            $kuantitas = $baData->manifest->kuantitas;
            $satuan = $baData->manifest->satuan ?? 'unit';
            $unitText = $kuantitas . ' ' . $satuan;
        }
    @endphp
    @if(!empty($unitText))
        <div class="banyaknya">{{ e($unitText) }}</div>
        <div class="banyaknya-2">{{ e($unitText) }}</div>
    @endif
    
    {{-- URAIAN (Kontainer Info + Jenis Barang) --}}
    @php
        $sizeKontainer = $baData->size_kontainer ?? $baData->size ?? '';
        $tipeKontainer = strtoupper(trim($baData->tipe_kontainer ?? ''));
        $noKontainer = strtoupper(trim($baData->no_kontainer ?? ''));
        
        $isCargo = ($tipeKontainer === 'CARGO') || ($noKontainer === 'CARGO');
        
        if (!empty($sizeKontainer)) {
            if (!str_contains(strtolower($sizeKontainer), 'ft') && !str_contains(strtoupper($sizeKontainer), 'HC')) {
                $sizeKontainer = $sizeKontainer . 'ft';
            }
        }
        $kontainerText = '1 CONTAINER' . (!empty($sizeKontainer) ? ' ' . $sizeKontainer : '');
        $uraianHtml = '';
        if(!$isCargo) {
            $uraianHtml .= e($kontainerText) . "<br>";
        }
        if(isset($baData) && !empty($baData->jenis_barang)) {
            $uraianHtml .= nl2br(e($baData->jenis_barang));
        }
    @endphp
    @if(!empty($uraianHtml))
        <div class="uraian">{!! $uraianHtml !!}</div>
    @endif
    
    {{-- Jenis Barang (Bottom) --}}
    @if(isset($baData) && !empty($baData->jenis_barang))
        <div class="jenis-barang">{!! nl2br(e($baData->jenis_barang)) !!}</div>
    @endif
    
    {{-- Nomor Voyage --}}
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
            $nomorBl = $baData->no_bl ?? '';
            if(!empty($nomorBl)) {
                $voyageFormatted = $nomorBl . '/' . $voyageNumber . '/' . ($romanMonths[$carbon->month] ?? '') . '/' . $carbon->format('Y');
            } else {
                $voyageFormatted = $voyageNumber . '/' . ($romanMonths[$carbon->month] ?? '') . '/' . $carbon->format('Y');
            }
        }
    @endphp
    @if(!empty($voyageFormatted))
        <div class="no-voyage">{{ e($voyageFormatted) }}</div>
    @endif

</body>
</html>
