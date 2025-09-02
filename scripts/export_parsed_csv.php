<?php
// Read the original CSV, normalize dates and compute periode, write a parsed CSV with computed_periode
$input = 'c:\\Users\\amanda\\Downloads\\template_daftar_tagihan_kontainer_sewa_rev.csv';
$output = 'c:\\Users\\amanda\\Downloads\\template_daftar_tagihan_kontainer_sewa_rev_parsed.csv';
if (!file_exists($input)) { echo "Input CSV not found: {$input}\n"; exit(1); }
$FH = fopen($input, 'r'); if ($FH === false) { echo "Unable to open input\n"; exit(1); }
$first = fgets($FH); rewind($FH);
$header = fgetcsv($FH, 0, ';'); if ($header === false) { echo "Empty CSV\n"; exit(1); }
// normalize header tokens
$rawHeader = $header;
// prepare indexes
$map = [];
foreach ($header as $i => $h) {
    $map[strtolower(trim(str_replace("\xEF\xBB\xBF", '', $h)))] = $i;
}
$findIndex = function($alts) use ($map) {
    foreach ($alts as $a) {
        $a = trim(strtolower($a));
        if (isset($map[$a])) return $map[$a];
    }
    return null;
};
$vendorIdx = $findIndex(['vendor']);
$nomorIdx = $findIndex(['nomor_kontainer','nomor','container_number']);
$sizeIdx = $findIndex(['size','ukuran','ukuran_kontainer']);
$tanggalAwalIdx = $findIndex(['tanggal_awal','tanggal_mulai','start_date']);
$tanggalAkhirIdx = $findIndex(['tanggal_akhir','tanggal_selesai','end_date']);

function normalizeIndoMonthsLocal($s) {
    $map = [
        'jan' => 'Jan','januari'=>'Jan','feb'=>'Feb','februari'=>'Feb','mar'=>'Mar','maret'=>'Mar',
        'apr'=>'Apr','april'=>'Apr','mei'=>'May','jun'=>'Jun','juni'=>'Jun','jul'=>'Jul','juli'=>'Jul',
        'agu'=>'Aug','agustus'=>'Aug','sep'=>'Sep','sept'=>'Sep','september'=>'Sep','okt'=>'Oct','oktober'=>'Oct',
        'nov'=>'Nov','november'=>'Nov','des'=>'Dec','desember'=>'Dec'
    ];
    foreach ($map as $k=>$v) {
        $s = preg_replace('/\b'.preg_quote($k,'/').'\b/i', $v, $s);
    }
    return $s;
}

function normalizeTwoDigitYearLocal($s) {
    $s = trim((string)$s);
    if ($s === '') return $s;
    $parts = preg_split('/([\s\/\-]+)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!$parts) return $s;
    $lastIndex = null;
    for ($i = count($parts)-1; $i >= 0; $i--) {
        if (preg_match('/^[\s\/\-]+$/', $parts[$i])) continue;
        $lastIndex = $i; break;
    }
    if (!isset($lastIndex)) return $s;
    $last = $parts[$lastIndex];
    if (preg_match('/^\d{2}$/', $last)) {
        $y = (int)$last; $full = ($y <= 49) ? (2000+$y) : (1900+$y);
        $parts[$lastIndex] = (string)$full;
        return implode('', $parts);
    }
    return $s;
}

function parseDateFlexLocal($v) {
    $v = trim((string)$v); if ($v === '') return null;
    $v = normalizeIndoMonthsLocal($v);
    $v = normalizeTwoDigitYearLocal($v);
    $formats = ['d-M-y','d-M-Y','d/m/Y','d-m-Y','Y-m-d','j M Y','d F Y','d M Y','j M y'];
    foreach ($formats as $f) {
        $dt = DateTime::createFromFormat($f, $v);
        if ($dt !== false) return $dt->format('Y-m-d');
    }
    $ts = strtotime($v);
    if ($ts !== false) return date('Y-m-d', $ts);
    return null;
}

function monthsDiffLocal(DateTime $start, DateTime $end) {
    $y1 = (int)$start->format('Y'); $m1 = (int)$start->format('n'); $d1 = (int)$start->format('j');
    $y2 = (int)$end->format('Y'); $m2 = (int)$end->format('n'); $d2 = (int)$end->format('j');
    $months = ($y2 - $y1) * 12 + ($m2 - $m1);
    if ($d2 < $d1) $months -= 1;
    return max(0, $months);
}

function computePeriodeLocal($tanggal_awal, $tanggal_akhir = null) {
    if (empty($tanggal_awal)) return 1;
    try { $start = new DateTime($tanggal_awal); } catch (Exception $e) { return 1; }
    $now = new DateTime(); $now->setTime(0,0,0); $start->setTime(0,0,0);
    if ($now < $start) { $periode = 1; } else { $months = monthsDiffLocal($start, $now); $periode = $months + 1; }
    if (!empty($tanggal_akhir)) {
        try { $end = new DateTime($tanggal_akhir); $end->setTime(0,0,0);
            if ($end < $start) { $max = 1; } else { $max = monthsDiffLocal($start, $end) + 1; }
            if ($periode > $max) $periode = $max;
        } catch (Exception $e) {}
    }
    return max(1, (int)$periode);
}

$outFH = fopen($output, 'w'); if ($outFH === false) { echo "Cannot write output: {$output}\n"; exit(1); }
// write BOM and header (original headers + computed_periode)
fwrite($outFH, "\xEF\xBB\xBF");
$writeHeader = $rawHeader; $writeHeader[] = 'computed_periode'; fputcsv($outFH, $writeHeader, ';');

// Two-pass: read all rows, compute vendor+date sequences, then write with grouped codes
$all = [];
rewind($FH);
// skip header
fgetcsv($FH, 0, ';');
while (($row = fgetcsv($FH, 0, ';')) !== false) {
    if (count(array_filter($row, fn($c)=>trim((string)$c) !== '')) === 0) continue;
    $vendorRaw = $vendorIdx !== null ? trim((string)($row[$vendorIdx] ?? '')) : '';
    $tanggal_awal = $tanggalAwalIdx !== null ? parseDateFlexLocal($row[$tanggalAwalIdx] ?? '') : null;
    $tanggal_akhir = $tanggalAkhirIdx !== null ? parseDateFlexLocal($row[$tanggalAkhirIdx] ?? '') : null;
    $computed = computePeriodeLocal($tanggal_awal, $tanggal_akhir);
    $all[] = [ 'orig' => $row, 'vendor' => strtoupper($vendorRaw), 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir, 'computed' => $computed ];
}
fclose($FH);

// build mapping vendor -> distinct sorted tanggal_awal -> seq no
$vendorDates = [];
foreach ($all as $r) {
    $v = $r['vendor']; $d = $r['tanggal_awal'] ?? '';
    if ($v === '' || $d === '') continue;
    $vendorDates[$v][$d] = true;
}
foreach ($vendorDates as $v => $dates) {
    $keys = array_keys($dates); sort($keys);
    $seq = [];$i=1; foreach ($keys as $k) { $seq[$k] = $i; $i++; }
    $vendorDates[$v] = $seq;
}

$outFH2 = $outFH; // same handle
$count = 0; $rows = [];
foreach ($all as $r) {
    $row = $r['orig'];
    $d = $r['tanggal_awal']; $computed = $r['computed']; $vendorKey = $r['vendor'];
    $outRow = $row;
    if ($tanggalAwalIdx !== null) $outRow[$tanggalAwalIdx] = $d ?? '';
    if ($tanggalAkhirIdx !== null) $outRow[$tanggalAkhirIdx] = $r['tanggal_akhir'] ?? '';

    // decide group
    $groupIdxLocal = isset($map['group']) ? $map['group'] : null;
    $existingGroup = ($groupIdxLocal !== null && trim((string)($row[$groupIdxLocal] ?? '')) !== '') ? trim($row[$groupIdxLocal]) : null;
    if ($existingGroup) {
        $groupVal = $existingGroup;
    } else {
        if ($vendorKey === 'ZONA') $prefix = 'Z';
        elseif ($vendorKey === 'DPE') $prefix = 'D';
        else $prefix = substr(preg_replace('/\s+/', '', $vendorKey), 0, 3);
        $seqNo = 1;
        if (isset($vendorDates[$vendorKey]) && $d !== null && $d !== '') $seqNo = $vendorDates[$vendorKey][$d] ?? 1;
        $groupVal = $prefix . sprintf('%03d', $seqNo);
    }
    if ($groupIdxLocal !== null) $outRow[$groupIdxLocal] = $groupVal; else $outRow[] = $groupVal;

    $outRow[] = $computed;
    fputcsv($outFH2, $outRow, ';');
    $count++; if ($count <= 20) $rows[] = $outRow;
}
fclose($outFH2);

echo "Wrote {$count} rows to {$output}\n\n";
echo "First " . count($rows) . " rows written (as preview):\n";
foreach ($rows as $r) { echo implode(' | ', $r) . "\n"; }
