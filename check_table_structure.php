<?php<?php<?php



require_once 'vendor/autoload.php';



$app = require_once 'bootstrap/app.php';require_once 'vendor/autoload.php';require_once __DIR__ . '/vendor/autoload.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();



use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;$app = require_once 'bootstrap/app.php';use Illuminate\Database\Capsule\Manager as DB;



$tables = [$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    'pranota_tagihan_kontainers',

    'pranota_tagihan_kontainer_sewas',// Setup database connection

    'pranota_tagihan_cats',

    'pranotas' // old tableuse Illuminate\Support\Facades\Schema;$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

];

use Illuminate\Support\Facades\DB;$dotenv->load();

foreach ($tables as $table) {

    if (Schema::hasTable($table)) {

        echo "Table: $table\n";

        try {$tables = [$capsule = new DB;

            $columns = Schema::getColumnListing($table);

            echo "Columns: " . implode(', ', $columns) . "\n";    'pranota_tagihan_kontainers',$capsule->addConnection([



            // Check for records    'pranota_tagihan_kontainer_sewas',    'driver' => 'mysql',

            $count = DB::table($table)->count();

            echo "Records: $count\n";    'pranota_tagihan_cats',    'host' => $_ENV['DB_HOST'],



            if ($count > 0) {    'pranotas' // old table    'database' => $_ENV['DB_DATABASE'],

                // Check for PTKS pattern in no_invoice or similar columns

                $invoiceColumns = ['no_invoice', 'nomor_pranota', 'no_pranota'];];    'username' => $_ENV['DB_USERNAME'],

                foreach ($invoiceColumns as $col) {

                    if (in_array($col, $columns)) {    'password' => $_ENV['DB_PASSWORD'],

                        $ptksCount = DB::table($table)->where($col, 'like', 'PTKS%')->count();

                        if ($ptksCount > 0) {foreach ($tables as $table) {    'charset' => 'utf8',

                            echo "  PTKS records in $col: $ptksCount\n";

                            $latestPtks = DB::table($table)->where($col, 'like', 'PTKS%')->orderBy($col, 'desc')->first();    if (Schema::hasTable($table)) {    'collation' => 'utf8_unicode_ci',

                            echo "  Latest PTKS: {$latestPtks->$col}\n";

                        }        echo "Table: $table\n";    'prefix' => '',

                    }

                }        try {]);

            }

        } catch (Exception $e) {            $columns = Schema::getColumnListing($table);

            echo "Error: " . $e->getMessage() . "\n";

        }            echo "Columns: " . implode(', ', $columns) . "\n";$capsule->setAsGlobal();

        echo "\n";

    } else {$capsule->bootEloquent();

        echo "Table $table does not exist\n\n";

    }            // Check for records

}
            $count = DB::table($table)->count();echo "=== Database Structure Check ===\n\n";

            echo "Records: $count\n";

try {

            if ($count > 0) {    $columns = DB::select('DESCRIBE pembayaran_pranota_kontainer');

                // Check for PTKS pattern in no_invoice or similar columns

                $invoiceColumns = ['no_invoice', 'nomor_pranota', 'no_pranota'];    echo "Columns in pembayaran_pranota_kontainer table:\n";

                foreach ($invoiceColumns as $col) {    foreach ($columns as $column) {

                    if (in_array($col, $columns)) {        echo "- {$column->Field} ({$column->Type})\n";

                        $ptksCount = DB::table($table)->where($col, 'like', 'PTKS%')->count();    }

                        if ($ptksCount > 0) {

                            echo "  PTKS records in $col: $ptksCount\n";} catch (Exception $e) {

                            $latestPtks = DB::table($table)->where($col, 'like', 'PTKS%')->orderBy($col, 'desc')->first();    echo "❌ Error: " . $e->getMessage() . "\n";

                            echo "  Latest PTKS: {$latestPtks->$col}\n";}

                        }

                    }echo "\n=== Check Complete ===\n";

                }
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    } else {
        echo "Table $table does not exist\n\n";
    }
}
