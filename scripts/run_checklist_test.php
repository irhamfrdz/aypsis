<?php
// scripts/run_checklist_test.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

echo "Starting checklist test...\n";

$k = DB::table('karyawans')->whereRaw('LOWER(divisi)=?', ['abk'])->select('id','nama_lengkap')->first();
if (!$k) {
    echo "No ABK karyawan found.\n";
    exit(0);
}
$id = $k->id;
echo "Using karyawan id: {$id} (" . ($k->nama_lengkap ?? '') . ")\n";

$items = DB::table('crew_checklists')->where('karyawan_id', $id)->select('id','item_name')->get();
if (count($items) > 0) {
    $first = $items[0]->id;
    $payload = [
        'checklist' => [ (string)$first => [
            'nomor_sertifikat' => 'ABCDE12345',
            'issued_date' => '2025-01-01',
            'expired_date' => '2026-01-01',
            'catatan' => 'test from script',
        ]]
    ];
    echo "Will update existing checklist id: {$first}\n";
} else {
    $payload = [
        'checklist' => [ 'new_1' => [
            'item_name' => 'Test Item',
            'nomor_sertifikat' => 'ABCDE12345',
            'issued_date' => '2025-01-01',
            'expired_date' => '2026-01-01',
            'catatan' => 'test from script',
        ]]
    ];
    echo "Will create new checklist item (new_1)\n";
}

// Build a Request instance
$req = Request::create('/dummy', 'POST', $payload);

// Simulate controller update logic directly to avoid HTTP redirects in CLI
echo "Simulating update logic directly...\n";
$fourAlnumPattern = '/^[A-Za-z0-9]{4,}$/';
foreach ($payload['checklist'] as $itemKey => $row) {
    // Determine if itemKey is existing id or new_x
    if (is_numeric($itemKey) || preg_match('/^\d+$/', $itemKey)) {
        // existing
        $existing = DB::table('crew_checklists')->where('id', (int)$itemKey)->where('karyawan_id', $id)->first();
        if ($existing) {
            $nomor = isset($row['nomor_sertifikat']) ? trim($row['nomor_sertifikat']) : null;
            $status = ($nomor && preg_match($fourAlnumPattern, $nomor)) ? 'ada' : 'tidak';
            $issued = ($status === 'ada') ? ($row['issued_date'] ?? null) : null;
            $expired = ($status === 'ada') ? ($row['expired_date'] ?? null) : null;
            DB::table('crew_checklists')->where('id', (int)$itemKey)->update([
                'status' => $status,
                'nomor_sertifikat' => $nomor,
                'issued_date' => $issued,
                'expired_date' => $expired,
                'catatan' => $row['catatan'] ?? null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "Updated checklist id {$itemKey} => status={$status}\n";
        } else {
            echo "Item id {$itemKey} not found, skipping.\n";
        }
    } else {
        // new item
        if (!empty($row['item_name'])) {
            $nomor = isset($row['nomor_sertifikat']) ? trim($row['nomor_sertifikat']) : null;
            $status = ($nomor && preg_match($fourAlnumPattern, $nomor)) ? 'ada' : 'tidak';
            $issued = ($status === 'ada') ? ($row['issued_date'] ?? null) : null;
            $expired = ($status === 'ada') ? ($row['expired_date'] ?? null) : null;
            $newId = DB::table('crew_checklists')->insertGetId([
                'karyawan_id' => $id,
                'item_name' => $row['item_name'],
                'status' => $status,
                'nomor_sertifikat' => $nomor,
                'issued_date' => $issued,
                'expired_date' => $expired,
                'catatan' => $row['catatan'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "Created new checklist id {$newId} (status={$status})\n";
        } else {
            echo "New item key {$itemKey} has no item_name, skipping.\n";
        }
    }
}

// Fetch rows
$rows = DB::table('crew_checklists')->where('karyawan_id', $id)->get();
echo "Rows for karyawan {$id}:\n";
print_r($rows->toArray());

echo "Done.\n";
