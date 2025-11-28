<?php

require_once __DIR__ . '/vendor/autoload.php';

try {
    $export = new \App\Exports\TandaTerimaExport([1]);
    echo "SUCCESS: Export instantiated.\n";
    $collection = $export->collection();
    if ($collection instanceof Illuminate\Support\Collection) {
        echo "SUCCESS: collection() returned a Collection with " . $collection->count() . " rows.\n";
        echo "Sample row0: "; print_r($collection[0]);
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
