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
            top: 7.5cm; /* 1cm lower: 7.5cm */
            left: 4.5cm;  /* 1cm more to the right: 4.5cm */
            width: 12cm; /* reasonable width for address area */
            white-space: pre-wrap; /* keep new lines */
            line-height: 1.2;
            font-size: 14px;
            font-weight: bold;
        }
        /* Positioned penerima data - below pengirim */
        .penerima {
            position: absolute;
            top: 9.0cm; /* Increased from 8.5cm to avoid overlap with long pengirim address */
            left: 4.5cm;
            width: 12cm;
            white-space: pre-wrap;
            line-height: 1.2;
            font-size: 14px;
            font-weight: bold;
        }
        .penerima-cp {
            position: absolute;
            top: 9.55cm; /* Raised by 0.75cm from 10.3cm */
            left: 4.5cm;
            font-size: 14px; /* Same as consignee */
            font-weight: bold; /* Same as consignee */
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
            left: 6.5cm; /* Moved from 5cm to 6.5cm to avoid overlap with container number */
            white-space: nowrap;
            font-size: 14px;
            font-weight: bold;
        }
        /* Positioned second unit text below first unit */
        .unit-text-2 {
            position: absolute;
            top: 20cm; /* lowered by 5cm */
            left: 6.5cm; /* aligned with first unit text */
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
            left: 11.0cm; /* Moved from 9cm to 11cm to avoid overlap with unit text */
            white-space: nowrap;
            font-size: 13px;
            font-weight: bold;
        }
        /* Positioned nama kapal */
        .nama-kapal {
            position: absolute;
            top: 7.5cm; /* aligned with pengirim: 7.5cm */
            left: 20.25cm; /* 2cm more to the right: 20.25cm */
            white-space: nowrap;
            font-weight: bold;
            font-size: 12px; /* smaller font */
            text-align: left;
        }
        /* Positioned nomor voyage (format: nomor_voyage/BULAN_ROMAWI/TAHUN) */
        .no-voyage {
            position: absolute;
            top: 6.2cm; /* lowered by 0.2cm */
            left: 12.75cm; /* 1.5cm more to the right: 12.75cm */
            white-space: nowrap;
            font-weight: bold;
            font-size: 14px;
            text-align: left;
        }
        /* Positioned pelabuhan route (asal - tujuan) */
        .pelabuhan-route {
            position: absolute;
            top: 8.9cm; /* Increased from 8.5cm to avoid overlap with tanggal-ba */
            left: 20.25cm; /* aligned with nama kapal */
            max-width: 11.5cm;
            white-space: nowrap;
            font-size: 12px; /* smaller font */
            font-weight: bold;
            text-align: left;
        }
        /* Positioned tanggal below nama kapal */
        .tanggal-ba {
            position: absolute;
            top: 8.2cm; /* Increased from 8cm to give more space for nama-kapal */
            left: 20.25cm; /* aligned with nama kapal */
            white-space: nowrap;
            font-size: 12px;
            font-weight: bold;
            text-align: left;
        }
        /* Positioned name 'Alex' from bottom-right */
        .alex-name {
            position: absolute;
            bottom: 2.75cm; /* 2.75cm from bottom */
            right: 3.5cm; /* 3.5cm from right */
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            text-align: right;
        }
        .no-print {
            padding: 15px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .no-print select {
            padding: 5px 10px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            max-width: 500px;
        }
        .no-print button {
            padding: 6px 12px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .no-print button:hover {
            background: #2563eb;
        }
        @media print {
            /* ensure no printing artifacts from browser margins */
            html, body { width: 210mm; height: 330mm; }
            .no-print { display: none !important; }
            .select2-container { display: none !important; }
        }
        
        /* Select2 Style Overrides */
        .select2-container .select2-selection--single {
            height: 32px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px;
        }
    </style>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="no-print">
        <label><b>Voyage:</b> {{ $manifest->nama_kapal }} {{ $manifest->no_voyage ? ' / ' . $manifest->no_voyage : '' }}</label>
        
        <label style="margin-left: 15px;"><b>Pilih BL:</b></label>
        <select id="bl_selector">
            @foreach($relatedManifests as $rm)
                <option value="{{ route('report.manifests.print-ba', $rm->id) }}" {{ $rm->id == $manifest->id ? 'selected' : '' }}>
                    {{ $rm->nomor_bl ?: '-' }} | {{ $rm->nomor_kontainer ?: '-' }} | {{ \Illuminate\Support\Str::limit($rm->pengirim, 20) }} -> {{ \Illuminate\Support\Str::limit($rm->penerima, 20) }}
                </option>
            @endforeach
        </select>
        
        <button type="button" onclick="window.location.href = document.getElementById('bl_selector').value;">Tampilkan</button>
        
        <button type="button" onclick="window.print()" style="background: #10b981; margin-left: auto;">Print</button>
    </div>
    
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
    {{-- Contact Person (separated div to not affect consignee position) --}}
    @if(isset($baData) && !empty($baData->contact_person))
        <div class="penerima-cp">CP: {{ e($baData->contact_person) }}</div>
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
        
        if (!$isCargo) {
            $unitText = '1 unit';
        } else {
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
        }
    @endphp
    @if(!empty($unitText))
    <div class="unit-text">{{ e($unitText) }}</div>
    {{-- Second Unit Text (same logic as first) --}}
    <div class="unit-text-2">{{ e($unitText) }}</div>
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

        // Check if voyage contains 'bj' (case-insensitive) -> Batam - Jakarta
        $voyageCheck = strtolower($baData->no_voyage ?? '');
        if (str_contains($voyageCheck, 'bj')) {
            $pelabuhanText = 'Batam - Jakarta';
        } elseif (str_contains($voyageCheck, 'pj')) {
            $pelabuhanText = 'Pinang - Jakarta';
        } else {
            $asal = $mapPelabuhan($baData->pelabuhan_asal ?? '');
            $tujuan = $mapPelabuhan($baData->pelabuhan_tujuan ?? '');
            $pelabuhanText = '';
            if ($asal !== '' || $tujuan !== '') {
                $pelabuhanText = trim(($asal ?? '') . ' - ' . ($tujuan ?? ''));
            }
        }
    @endphp
    @if(!empty($pelabuhanText)) 
        <div class="pelabuhan-route">{{ e($pelabuhanText) }}</div>
    @else
        <div class="pelabuhan-route">&nbsp;</div>
    @endif
    {{-- Tanggal BA (format: d-M-Y) --}}
    @php
        $tanggalBa = '';
        // Prioritas: Ambil dari manifest tanggal_berangkat
        if (isset($baData->manifest) && !empty($baData->manifest->tanggal_berangkat)) {
            try {
                $tanggalBa = \Carbon\Carbon::parse($baData->manifest->tanggal_berangkat)->format('d-M-Y');
            } catch (\Exception $e) {
                // If parse fails, stay empty to try fallback
            }
        }
        
        // Fallback: Ambil dari tanggal_ba jika manifest date kosong
        if (empty($tanggalBa) && isset($baData->tanggal_ba)) {
            try {
                $tanggalBa = \Carbon\Carbon::parse($baData->tanggal_ba)->format('d-M-Y');
            } catch (\Exception $e) {
                $tanggalBa = '';
            }
        }
    @endphp
    @if(!empty($tanggalBa))
        <div class="tanggal-ba">{{ e($tanggalBa) }}</div>
    @else
        <div class="tanggal-ba">&nbsp;</div>
    @endif
    {{-- Nama Alex (static) --}}
    <div class="alex-name">Alex</div>
    
    <!-- jQuery and Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#bl_selector').select2({
                width: '500px',
                placeholder: 'Cari Nomor BL, Kontainer, atau Pengirim...',
                matcher: function(params, data) {
                    // If there are no search terms, return all of the data
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // Do not display the item if there is no 'text' property
                    if (typeof data.text === 'undefined') {
                        return null;
                    }

                    // Case-insensitive search
                    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                        return data;
                    }

                    // Return `null` if the term should not be displayed
                    return null;
                }
            });
        });
    </script>
</body>
</html>