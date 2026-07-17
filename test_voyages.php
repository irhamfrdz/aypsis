<?php
$records = App\Models\BiayaKapal::whereIn('id', [35, 66])->get();
foreach ($records as $r) {
    echo "ID: " . $r->id . "\n";
    echo "Nama Kapal: " . json_encode($r->nama_kapal) . "\n";
    echo "No Voyage: " . json_encode($r->no_voyage) . "\n";
    echo "--------------------------\n";
}
