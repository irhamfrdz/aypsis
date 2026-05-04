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
        /* Grouped info containers to prevent overlapping */
        .left-info {
            position: absolute;
            top: 7.5cm;
            left: 4.5cm;
            width: 12cm;
            display: flex;
            flex-direction: column;
            gap: 0.4cm; /* Fixed gap between blocks */
        }
        .left-info div {
            white-space: pre-wrap;
            line-height: 1.2;
            font-size: 14px;
            font-weight: bold;
        }
        .right-info {
            position: absolute;
            top: 7.5cm;
            left: 20.25cm;
            display: flex;
            flex-direction: column;
            gap: 0.3cm;
            text-align: left;
        }
        .right-info div {
            white-space: nowrap;
            font-weight: bold;
            font-size: 12px;
        }
        /* Positioned container number */
        .no-kontainer {
            position: absolute;
            top: 11.5cm; /* aligned with nama barang */
            left: 1cm;  /* 1cm from left */
            white-space: nowrap; /* container number single-line */
            font-weight: bold;
            font-size: 16px; /* larger to highlight container number */
        }
        /* Positioned unit text */
        .unit-text {
            position: absolute;
            top: 11.5cm; /* aligned with nama barang */
            left: 5cm; /* 5cm from left */
            white-space: nowrap;
            font-size: 14px;
            font-weight: bold;
        }
        /* Positioned second unit text below first unit */
        .unit-text-2 {
            position: absolute;
            top: 20cm; /* lowered by 5cm */
            left: 5cm; /* aligned with first unit text */
            white-space: nowrap;
            font-size: 14px;
            font-weight: bold;
        }
        /* Positioned nama barang (jenis barang) */
        /* Positioned nama barang (jenis barang) */
        .nama-barang {
            position: absolute;
            top: 12.5cm; /* moved down so kontainer info can be on top */
            left: 9cm; /* 1cm more to the right: 9cm */
            max-width: 10cm; /* reduced width to prevent overflow */
            white-space: pre-wrap;
            font-size: 13px; /* slightly smaller */
            font-weight: bold;
            line-height: 1.3;
        }
        /* Positioned kontainer info (1 kontainer + size) above nama barang */
        .kontainer-info {
            position: absolute;
            top: 11.5cm; /* aligned with no-kontainer */
            left: 9cm; /* aligned with nama barang */
            white-space: nowrap;
            font-size: 13px;
            font-weight: bold;
        }
        /* Positioned nomor voyage (format: nomor_voyage/BULAN_ROMAWI/TAHUN) */
        .no-voyage {
            position: absolute;
            top: 6.2cm;
            left: 12.75cm;
            white-space: nowrap;
            font-weight: bold;
            font-size: 14px;
            text-align: left;
        }
        /* Positioned name 'Alex' from bottom-right */
        .alex-name {
            position: absolute;
            bottom: 2.75cm;
            right: 3.5cm;
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
    <div class="left-info">
        {{-- Pengirim --}}
        @if(isset($baData) && !empty($baData->pengirim))
            <div>{!! nl2br(e($baData->pengirim)) !!}</div>
        @endif

        {{-- Penerima --}}
        @if(isset($baData) && !empty($baData->penerima))
            <div>{!! nl2br(e($baData->penerima)) !!}</div>
        @endif

        {{-- Contact Person --}}
        @if(isset($baData) && !empty($baData->contact_person))
            <div>CP: {{ e($baData->contact_person) }}</div>
        @endif
    </div>
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
    {{-- Kontainer Info: 1 kontainer + size kontainer - HIDDEN for CARGO type --}}
    @php
        $sizeKontainer = $baData->size_kontainer ?? $baData->size ?? '';
        $tipeKontainer = strtoupper(trim($baData->tipe_kontainer ?? ''));
        $noKontainer = strtoupper(trim($baData->no_kontainer ?? ''));
        
        // Check if CARGO: either tipe_kontainer is CARGO or no_kontainer is CARGO
        $isCargo = ($tipeKontainer === 'CARGO') || ($noKontainer === 'CARGO');
        
        // Format size kontainer: jika hanya angka, tambahkan 'ft'
        if (!empty($sizeKontainer)) {
            // Check if already has 'ft' or 'HC'
            if (!str_contains(strtolower($sizeKontainer), 'ft') && !str_contains(strtoupper($sizeKontainer), 'HC')) {
                $sizeKontainer = $sizeKontainer . 'ft';
            }
        }
        $kontainerText = '1 CONTAINER' . (!empty($sizeKontainer) ? ' ' . $sizeKontainer : '');
    @endphp
    {{-- Only show kontainer info if NOT cargo --}}
    @if(!$isCargo)
    <div class="kontainer-info">{{ e($kontainerText) }}</div>
    @endif
    {{-- Unit Text: ambil dari manifest (kuantitas + satuan) --}}
    @php
        // Default fallback
        $unitText = '';
        
        // Prioritas 1: Ambil langsung dari object baData (kuantitas + satuan dari manifests table)
        if (isset($baData->kuantitas) && !empty($baData->kuantitas)) {
            $kuantitas = $baData->kuantitas;
            $satuan = $baData->satuan ?? 'unit';
            $unitText = $kuantitas . ' ' . $satuan;
        }
        // Prioritas 2: Cek via relationship manifest (jika object baData punya relation manifest)
        elseif (isset($baData->manifest) && !empty($baData->manifest->kuantitas)) {
            $kuantitas = $baData->manifest->kuantitas;
            $satuan = $baData->manifest->satuan ?? 'unit';
            $unitText = $kuantitas . ' ' . $satuan;
        }
    @endphp
    @if(!empty($unitText))
    <div class="unit-text">{{ e($unitText) }}</div>
    {{-- Second Unit Text (same logic as first) --}}
    <div class="unit-text-2">{{ e($unitText) }}</div>
    @endif
    <div class="right-info">
        {{-- Nama Kapal --}}
        @if(isset($baData) && !empty($baData->nama_kapal))
            <div>{{ e($baData->nama_kapal) }}</div>
        @endif

        {{-- Tanggal BA --}}
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
            <div>{{ e($tanggalBa) }}</div>
        @endif

        {{-- Pelabuhan Route --}}
        @php
            $mapPelabuhan = function ($value) {
                $v = trim((string) ($value ?? ''));
                if ($v === '') return '';
                $clean = mb_strtolower(preg_replace('/[^a-z0-9 ]+/i', ' ', $v));
                if (in_array($clean, ['sunda kelapa', 'sundakelapa', 'sunda_kelapa', 'sunda-kelapa'], true)) return 'Jakarta';
                return $v;
            };
            $voyageCheck = strtolower($baData->no_voyage ?? '');
            if (str_contains($voyageCheck, 'bj')) {
                $pelabuhanText = 'Batam - Jakarta';
            } elseif (str_contains($voyageCheck, 'pj')) {
                $pelabuhanText = 'Pinang - Jakarta';
            } else {
                $asal = $mapPelabuhan($baData->pelabuhan_asal ?? '');
                $tujuan = $mapPelabuhan($baData->pelabuhan_tujuan ?? '');
                $pelabuhanText = ($asal !== '' || $tujuan !== '') ? trim(($asal ?? '') . ' - ' . ($tujuan ?? '')) : '';
            }
        @endphp
        @if(!empty($pelabuhanText)) 
            <div>{{ e($pelabuhanText) }}</div>
        @endif
    </div>
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
            // Format: NOMOR_BL/VOYAGE/BULAN_ROMAWI/TAHUN (contoh: 05/AP25BJ25/I/2026)
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
    @else
        <div class="no-voyage">&nbsp;</div>
    @endif
    {{-- Nama Alex (static) --}}
    <div class="alex-name">Alex</div>
</body>
</html>
