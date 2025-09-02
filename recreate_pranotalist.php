<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Recreate Pranotalist Table ===\n\n";

try {
    echo "1. Creating pranotalist table...\n";

    Schema::create('pranotalist', function($table) {
        $table->id();
        $table->string('no_invoice');
        $table->decimal('total_amount', 15, 2)->nullable();
        $table->text('keterangan')->nullable();
        $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');
        $table->json('tagihan_ids')->nullable();
        $table->integer('jumlah_tagihan')->default(0);
        $table->date('tanggal_pranota');
        $table->date('due_date')->nullable();
        $table->timestamps();
    });

    echo "   ✅ Pranotalist table created successfully\n";

    echo "\n2. Verifying table structure...\n";
    $columns = DB::select("DESCRIBE pranotalist");
    foreach($columns as $column) {
        echo "   - {$column->Field}: {$column->Type}\n";
    }

    echo "\n3. Checking auto increment:\n";
    $result = DB::select("SHOW TABLE STATUS LIKE 'pranotalist'");
    if (!empty($result)) {
        echo "   - Next auto increment: " . $result[0]->Auto_increment . "\n";
    }

    echo "\n✅ Pranotalist table recreated successfully!\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Selesai ===\n";
