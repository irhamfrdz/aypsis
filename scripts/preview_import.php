<?php
// Preview import: parse semicolon CSV, compute periode from tanggal_awal/tanggal_akhir, print first N rows and summary
$csvPath = 'c:\\Users\\amanda\\Downloads\\template_daftar_tagihan_kontainer_sewa_rev.csv';
if (!file_exists($csvPath)) {
    echo "CSV file not found: {$csvPath}\n";
    exit(1);
}
$FH = fopen($csvPath, 'r');
if ($FH === false) {
    echo "Unable to open CSV\n";
    exit(1);
}
// read header and handle BOM
$first = fgets($FH);
rewind($FH);
$header = fgetcsv($FH, 0, ';');
if ($header === false) { echo "Empty CSV\n"; exit(1); }
// normalize headers
$map = [];
foreach ($header as $i => $h) {
    $h = trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h)));
    $map[$h] = $i;
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

function normalizeIndoMonths($s) {
    $map = [
        'jan' => 'Jan','januari'=>'Jan','feb'=>'Feb','februari'=>'Feb','mar'=>'Mar','maret'=>'Mar',
        'apr'=>'Apr','april'=>'Apr','mei'=>'May','jun'=>'Jun','juni'=>'Jun','jul'=>'Jul','juli'=>'Jul',
        'agu'=>'Aug','agustus'=>'Aug','sep'=>'Sep','sept'=>'Sep','september'=>'Sep','okt'=>'Oct','oktober'=>'Oct',
        'nov'=>'Nov','november'=>'Nov','des'=>'Dec','desember'=>'Dec'
    ];
    // replace full month names or short Indonesian to English abbreviations
    foreach ($map as $k=>$v) {
        // case-insensitive replace
        $s = preg_replace('/\b'.preg_quote($k,'/').'\b/i', $v, $s);
    }
    return $s;
}

function normalizeTwoDigitYear($s) {
    $s = trim((string)$s);
    if ($s === '') return $s;
    // If last token is a 2-digit year, expand it to 4-digit using rules:
    // 00..49 -> 2000..2049, 50..99 -> 1950..1999
    // Handle separators like space, hyphen, or slash
    // Try space-separated
    $parts = preg_split('/([\s\/\-]+)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!$parts) return $s;
    // find last non-separator token index
    for ($i = count($parts)-1; $i >= 0; $i--) {
        if (preg_match('/^\s*$/', $parts[$i])) continue;
        // skip delimiter tokens
        if (preg_match('/^[\s\/\-]+$/', $parts[$i])) continue;
        $lastIndex = $i;
        break;
    }
    if (!isset($lastIndex)) return $s;
    $last = $parts[$lastIndex];
    if (preg_match('/^\d{2}$/', $last)) {
        $y = (int)$last;
        if ($y <= 49) $full = 2000 + $y; else $full = 1900 + $y;
        $parts[$lastIndex] = (string)$full;
        return implode('', $parts);
    }
    return $s;
}

function parseDateFlex($v) {
    $v = trim((string)$v);
    if ($v === '') return null;
    // normalize month names first, then fix 2-digit years
    $v = normalizeIndoMonths($v);
    $v = normalizeTwoDigitYear($v);
    $formats = ['d-M-y','d-M-Y','d/m/Y','d-m-Y','Y-m-d','j M Y','d F Y','d M Y','j M y'];
    foreach ($formats as $f) {
        $dt = DateTime::createFromFormat($f, $v);
        if ($dt !== false) return $dt->format('Y-m-d');
    }
    // try strtotime fallback
    $ts = strtotime($v);
    if ($ts !== false) return date('Y-m-d', $ts);
    return null;
}

function monthsDiff(
    DateTime $start,
    DateTime $end
) {
    $y1 = (int)$start->format('Y');
    $m1 = (int)$start->format('n');
    $d1 = (int)$start->format('j');
    $y2 = (int)$end->format('Y');
    $m2 = (int)$end->format('n');
    $d2 = (int)$end->format('j');
    $months = ($y2 - $y1) * 12 + ($m2 - $m1);
    if ($d2 < $d1) $months -= 1;
    return max(0, $months);
}

function computePeriodeLocal($tanggal_awal, $tanggal_akhir = null) {
    if (empty($tanggal_awal)) return 1;
    try {
        $start = new DateTime($tanggal_awal);
    } catch (Exception $e) {
        return 1;
    }
    $now = new DateTime();
    $now->setTime(0,0,0);
    $start->setTime(0,0,0);
    if ($now < $start) {
        $periode = 1;
    } else {
        $months = monthsDiff($start, $now);
        $periode = $months + 1;
    }
    if (!empty($tanggal_akhir)) {
        try {
            $end = new DateTime($tanggal_akhir);
            $end->setTime(0,0,0);
            if ($end < $start) {
                $max = 1;
            } else {
                $max = monthsDiff($start, $end) + 1;
            }
            if ($periode > $max) $periode = $max;
        } catch (Exception $e) {
            // ignore
        }
    }
    return max(1, (int)$periode);
}

$rows = [];
while (($row = fgetcsv($FH, 0, ';')) !== false) {
    if (count(array_filter($row, fn($c)=>trim((string)$c) !== '')) === 0) continue;
    $vendor = $vendorIdx !== null ? ($row[$vendorIdx] ?? null) : null;
    $nomor = $nomorIdx !== null ? ($row[$nomorIdx] ?? null) : null;
    $size = $sizeIdx !== null ? ($row[$sizeIdx] ?? null) : null;
    if (empty($vendor) || empty($nomor)) continue;
    $tanggal_awal = $tanggalAwalIdx !== null ? parseDateFlex($row[$tanggalAwalIdx] ?? '') : null;
    $tanggal_akhir = $tanggalAkhirIdx !== null ? parseDateFlex($row[$tanggalAkhirIdx] ?? '') : null;
    $periode = computePeriodeLocal($tanggal_awal, $tanggal_akhir);
    $rows[] = [
        'vendor'=>trim($vendor),
        'nomor_kontainer'=>trim($nomor),
        'size'=> $size ? trim($size) : null,
        'tanggal_awal' => $tanggal_awal,
        'tanggal_akhir' => $tanggal_akhir,
        'computed_periode' => $periode,
    ];
}
fclose($FH);
// print first 20 rows
$show = array_slice($rows, 0, 20);
echo "First " . count($show) . " parsed rows (vendor, nomor_kontainer, size, tanggal_awal, tanggal_akhir, computed_periode):\n";
foreach ($show as $r) {
    echo implode(' | ', [$r['vendor'],$r['nomor_kontainer'],$r['size'] ?? '-', $r['tanggal_awal'] ?? '-', $r['tanggal_akhir'] ?? '-', $r['computed_periode']]) . "\n";
}
// summary counts by periode
$summary = [];
foreach ($rows as $r) {
    $p = $r['computed_periode'];
    if (!isset($summary[$p])) $summary[$p] = 0;
    $summary[$p]++;
}
ksort($summary);
echo "\nSummary counts by computed periode:\n";
foreach ($summary as $p => $cnt) {
    echo "Periode {$p}: {$cnt}\n";
}

echo "\nTotal parsed rows: " . count($rows) . "\n";
