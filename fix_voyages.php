<?php

$record35 = App\Models\BiayaKapal::find(35);
if ($record35) {
    // Current: ["SR02JP26","AP01JB26","SP01JB26","SA01JP26"]
    // Target:  ["SR02JP26","AP01JB26","","SP01JB26","SA01JP26"]
    $voyages35 = is_array($record35->no_voyage) ? $record35->no_voyage : json_decode($record35->no_voyage, true);
    if (count($voyages35) === 4 && $voyages35[2] === "SP01JB26") {
        $newVoyages35 = [
            $voyages35[0], // SR02JP26
            $voyages35[1], // AP01JB26
            "",            // empty for KM. ALEXINDO 1
            $voyages35[2], // SP01JB26 for KM. SEKAR PERMATA
            $voyages35[3], // SA01JP26 for KM. SUMBER ABADI 178
        ];
        $record35->no_voyage = $newVoyages35;
        $record35->save();
        echo "Fixed ID 35\n";
    }
}

$record66 = App\Models\BiayaKapal::find(66);
if ($record66) {
    // Current: ["AP02JB26","SA02JP26","SP02JB26"]
    // Target:  ["AP02JB26","","SA02JP26","SP02JB26"]
    $voyages66 = is_array($record66->no_voyage) ? $record66->no_voyage : json_decode($record66->no_voyage, true);
    if (count($voyages66) === 3 && $voyages66[1] === "SA02JP26") {
        $newVoyages66 = [
            $voyages66[0], // AP02JB26
            "",            // empty for KM. ALEXINDO 1
            $voyages66[1], // SA02JP26 for KM. SUMBER ABADI 178
            $voyages66[2], // SP02JB26 for KM. SEKAR PERMATA
        ];
        $record66->no_voyage = $newVoyages66;
        $record66->save();
        echo "Fixed ID 66\n";
    }
}
