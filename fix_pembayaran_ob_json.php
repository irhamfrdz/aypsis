<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

DB::table('pembayaran_pranota_obs')->get(['id', 'pranota_ob_ids', 'breakdown_supir'])->each(function($row) {
    if ($row->pranota_ob_ids) {
        $ids = $row->pranota_ob_ids;
        // Check if double encoded (starts with a quote after first decode would still be JSON)
        if (is_string($ids) && (strpos($ids, '\"') !== false || strpos($ids, '[') === 0)) {
            $decoded = json_decode($ids, true);
            if (is_string($decoded)) {
                $really_ids = json_decode($decoded, true);
                if (is_array($really_ids)) {
                    DB::table('pembayaran_pranota_obs')->where('id', $row->id)->update(['pranota_ob_ids' => json_encode($really_ids)]);
                    echo "Fixed ID " . $row->id . " ids\n";
                }
            }
        }
    }

    if ($row->breakdown_supir) {
        $breakdown = $row->breakdown_supir;
        if (is_string($breakdown) && (strpos($breakdown, '\"') !== false || strpos($breakdown, '[') === 0)) {
            $decoded = json_decode($breakdown, true);
            if (is_string($decoded)) {
                $really_breakdown = json_decode($decoded, true);
                if (is_array($really_breakdown)) {
                    DB::table('pembayaran_pranota_obs')->where('id', $row->id)->update(['breakdown_supir' => json_encode($really_breakdown)]);
                    echo "Fixed ID " . $row->id . " breakdown\n";
                }
            }
        }
    }
});
