<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pranota;
use App\Models\TagihanCat;

$pranotas = Pranota::whereNotNull('tagihan_ids')
    ->where('tagihan_ids', '!=', '[]')
    ->get();

$validTagihanIds = TagihanCat::pluck('id')->toArray();

foreach ($pranotas as $pranota) {
    if (is_array($pranota->tagihan_ids)) {
        $validIds = array_filter($pranota->tagihan_ids, function($id) use ($validTagihanIds) {
            return in_array($id, $validTagihanIds);
        });

        if (count($validIds) != count($pranota->tagihan_ids)) {
            // Update tagihan_ids to only include valid IDs
            $pranota->update([
                'tagihan_ids' => array_values($validIds),
                'jumlah_tagihan' => count($validIds)
            ]);
            echo "Updated pranota {$pranota->id}: removed invalid tagihan_cat IDs\n";
        }

        // Now populate pivot table with valid IDs
        foreach ($validIds as $tagihanId) {
            \Illuminate\Support\Facades\DB::table('pranota_tagihan_cat_items')->updateOrInsert(
                ['pranota_id' => $pranota->id, 'tagihan_cat_id' => $tagihanId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}

echo "Cleanup completed\n";
