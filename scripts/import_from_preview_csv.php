<?php
// Import each row from output_preview.csv directly into daftar_tagihan_kontainer_sewa
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\DaftarTagihanKontainerSewa;
// default input
$in = __DIR__ . '/output_preview.csv';
if (!file_exists($in)) { echo "Input file not found: $in\n"; exit(1); }

// Quick check: if input appears not expanded (no 'periode' or 'masa' header), run fill_preview_from_raw.php
$headerLine = null;
$fhCheck = fopen($in, 'r');
if ($fhCheck) {
    $headerLine = fgets($fhCheck);
    fclose($fhCheck);
}
if ($headerLine !== null) {
    $h = array_map('trim', array_map('strtolower', str_getcsv(trim($headerLine), ';')));
    $hasPeriode = in_array('periode', $h, true) || in_array('computed_periode', $h, true) || in_array('masa', $h, true);
    if (!$hasPeriode) {
        echo "Input not expanded, running preview fill to expand periods...\n";
        $cmd = escapeshellcmd((defined('PHP_BINARY')?PHP_BINARY:'php')) . ' "' . __DIR__ . '\\fill_preview_from_raw.php" ' . escapeshellarg($in);
        $out = null; $rc = null; exec($cmd, $out, $rc);
        if ($rc !== 0) { echo "Preview expansion failed (rc=$rc).\n"; }
        $expanded = __DIR__ . '/output_preview_preview.csv';
        if (file_exists($expanded)) { $in = $expanded; echo "Using expanded preview: $expanded\n"; }
    }
}

$FH = fopen($in, 'r');
if (!$FH) { echo "Unable to open input\n"; exit(1); }
$header = fgetcsv($FH, 0, ';');
$map = [];
foreach ($header as $i=>$h) $map[strtolower(trim($h))] = $i;
// Map to track group assignment
$group_map = [];
$group_seq = 1;
$count = 0; $created = 0; $skipped = 0;
while (($row = fgetcsv($FH, 0, ';')) !== false) {
    $count++;
    $data = [];
    foreach ($map as $col=>$idx) {
        $data[$col] = isset($row[$idx]) ? trim($row[$idx]) : null;
    }
        // Set tanggal_akhir to null if empty
        if (isset($data['tanggal_akhir']) && $data['tanggal_akhir'] === '') {
            $data['tanggal_akhir'] = null;
        }
    // Assign group based on vendor+tanggal_awal
    $group_key = ($data['vendor'] ?? '') . '|' . ($data['tanggal_awal'] ?? '');
    if (!isset($group_map[$group_key])) {
        $group_map[$group_key] = 'G' . str_pad($group_seq, 3, '0', STR_PAD_LEFT);
        $group_seq++;
    }
    $data['group'] = $group_map[$group_key];

    // Ensure periode matches preview logic: prefer 'periode', then 'computed_periode', else 1
    $periode_val = null;
    if (isset($data['periode']) && is_numeric($data['periode'])) $periode_val = (int)$data['periode'];
    elseif (isset($data['computed_periode']) && is_numeric($data['computed_periode'])) $periode_val = (int)$data['computed_periode'];
    else $periode_val = 1;
    $data['periode'] = $periode_val;

    // Uniqueness: vendor, nomor_kontainer, group, tanggal_awal, periode
    $attrs = [
        'vendor' => $data['vendor'] ?? null,
        'nomor_kontainer' => $data['nomor_kontainer'] ?? null,
        'group' => $data['group'] ?? null,
        'tanggal_awal' => $data['tanggal_awal'] ?? null,
        'periode' => isset($data['periode']) ? (int)$data['periode'] : null,
    ];
    $existing = DaftarTagihanKontainerSewa::where($attrs)->first();
    if ($existing) { $skipped++; continue; }
    DaftarTagihanKontainerSewa::create($data);
    $created++;
}
fclose($FH);
echo "Import finished. Total input rows: $count. Created: $created. Skipped(existing): $skipped\n";
