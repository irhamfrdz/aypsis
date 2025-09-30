<?php<?php<?php<?php<?php



require_once 'vendor/autoload.php';



use Illuminate\Support\Facades\DB;require_once 'vendor/autoload.php';require "vendor/autoload.php";



echo "Tables with pembayaran_pranota:" . PHP_EOL;



try {use Illuminate\Support\Facades\DB;$app = require "bootstrap/app.php";require "vendor/autoload.php";require_once 'vendor/autoload.php';

    $tables = DB::select('SHOW TABLES LIKE "%pembayaran_pranota%"');

    foreach($tables as $table) {

        foreach($table as $name) {

            echo "- " . $name . PHP_EOL;echo "Tables with pembayaran_pranota:" . PHP_EOL;$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

        }

    }



    echo PHP_EOL . "Checking pembayaran_pranota_items structure:" . PHP_EOL;try {$app = require "bootstrap/app.php";$app = require_once 'bootstrap/app.php';

    $columns = DB::select('DESCRIBE pembayaran_pranota_items');

    foreach($columns as $column) {    $tables = DB::select('SHOW TABLES LIKE "%pembayaran_pranota%"');

        echo "- " . $column->Field . " (" . $column->Type . ")" . PHP_EOL;

    }    foreach($tables as $table) {try {



} catch (Exception $e) {        foreach($table as $name) {

    echo "Error: " . $e->getMessage() . PHP_EOL;

}            echo "- " . $name . PHP_EOL;    $schema = \DB::getDoctrineSchemaManager();$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        }

    }    $columns = $schema->listTableColumns('pembayaran_pranota_kontainer_items');



    echo PHP_EOL . "Checking pembayaran_pranota_items structure:" . PHP_EOL;    echo "Struktur tabel pembayaran_pranota_kontainer_items:\n";

    $columns = DB::select('DESCRIBE pembayaran_pranota_items');

    foreach($columns as $column) {    foreach($columns as $column) {

        echo "- " . $column->Field . " (" . $column->Type . ")" . PHP_EOL;

    }        echo $column->getName() . ': ' . $column->getType()->getName() . ' (' . ($column->getNotnull() ? 'NOT NULL' : 'NULL') . ")\n";try {use Illuminate\Support\Facades\DB;



} catch (Exception $e) {    }

    echo "Error: " . $e->getMessage() . PHP_EOL;

}    $schema = \DB::getDoctrineSchemaManager();

    echo "\nStruktur tabel pranota_tagihan_kontainer_sewa:\n";

    $columns2 = $schema->listTableColumns('pranota_tagihan_kontainer_sewa');    $columns = $schema->listTableColumns('pembayaran_pranota_kontainer_items');$tables = DB::select('SHOW TABLES');

    foreach($columns2 as $column) {

        echo $column->getName() . ': ' . $column->getType()->getName() . ' (' . ($column->getNotnull() ? 'NOT NULL' : 'NULL') . ")\n";    echo "Struktur tabel pembayaran_pranota_kontainer_items:\n";echo 'Permission-related tables:' . PHP_EOL;

    }

} catch (\Exception $e) {    foreach($columns as $column) {foreach($tables as $table) {

    echo "Error: " . $e->getMessage() . "\n";

}        echo $column->getName() . ': ' . $column->getType()->getName() . ' (' . ($column->getNotnull() ? 'NOT NULL' : 'NULL') . ")\n";    $tableName = current($table);

    }    if(str_contains(strtolower($tableName), 'permission') ||

       str_contains(strtolower($tableName), 'role') ||

    echo "\nStruktur tabel pranota_tagihan_kontainer_sewa:\n";       str_contains(strtolower($tableName), 'user')) {

    $columns2 = $schema->listTableColumns('pranota_tagihan_kontainer_sewa');        echo '- ' . $tableName . PHP_EOL;

    foreach($columns2 as $column) {

        echo $column->getName() . ': ' . $column->getType()->getName() . ' (' . ($column->getNotnull() ? 'NOT NULL' : 'NULL') . ")\n";        // Get table structure

    }        try {

} catch (\Exception $e) {            $columns = DB::select('DESCRIBE ' . $tableName);

    echo "Error: " . $e->getMessage() . "\n";            echo '  Columns: ';

}            $columnNames = array_map(function($col) { return $col->Field; }, $columns);
            echo implode(', ', $columnNames) . PHP_EOL;
        } catch (Exception $e) {
            echo '  Error getting columns: ' . $e->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }
}
?>
