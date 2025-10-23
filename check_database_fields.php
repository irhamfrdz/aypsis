<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Checking Database Fields for pembayaran_pranota_surat_jalan\n";
    echo "==========================================================\n\n";

    // Get table structure
    $columns = DB::select("DESCRIBE pembayaran_pranota_surat_jalan");

    echo "Current table structure:\n";
    echo "------------------------\n";
    printf("%-35s %-20s %-10s %-15s\n", "Field", "Type", "Null", "Default");
    echo str_repeat("-", 80) . "\n";

    foreach ($columns as $column) {
        printf("%-35s %-20s %-10s %-15s\n",
            $column->Field,
            $column->Type,
            $column->Null,
            $column->Default ?? 'NULL'
        );
    }

    echo "\n\nChecking specific fields:\n";
    echo "=========================\n";

    $requiredFields = [
        'total_tagihan_penyesuaian' => 'decimal(15,2)',
        'alasan_penyesuaian' => 'text',
        'keterangan' => 'text'
    ];

    $existingFields = [];
    foreach ($columns as $column) {
        $existingFields[$column->Field] = $column->Type;
    }

    foreach ($requiredFields as $fieldName => $expectedType) {
        if (array_key_exists($fieldName, $existingFields)) {
            echo "✓ {$fieldName} - EXISTS ({$existingFields[$fieldName]})\n";
        } else {
            echo "✗ {$fieldName} - MISSING (expected: {$expectedType})\n";
        }
    }

    echo "\n\nMissing fields analysis:\n";
    echo "========================\n";

    $missingFields = [];
    foreach ($requiredFields as $fieldName => $expectedType) {
        if (!array_key_exists($fieldName, $existingFields)) {
            $missingFields[] = $fieldName;
        }
    }

    if (empty($missingFields)) {
        echo "🎉 All required fields are present!\n";
    } else {
        echo "Missing fields that need to be added:\n";
        foreach ($missingFields as $field) {
            echo "- {$field} ({$requiredFields[$field]})\n";
        }

        echo "\n\nSuggested migration to add missing fields:\n";
        echo "==========================================\n";
        echo "php artisan make:migration add_missing_fields_to_pembayaran_pranota_surat_jalan_table\n\n";

        echo "Migration content:\n";
        echo "<?php\n";
        echo "use Illuminate\\Database\\Migrations\\Migration;\n";
        echo "use Illuminate\\Database\\Schema\\Blueprint;\n";
        echo "use Illuminate\\Support\\Facades\\Schema;\n\n";
        echo "class AddMissingFieldsToPembayaranPranotaSuratJalanTable extends Migration\n";
        echo "{\n";
        echo "    public function up()\n";
        echo "    {\n";
        echo "        Schema::table('pembayaran_pranota_surat_jalan', function (Blueprint \$table) {\n";

        foreach ($missingFields as $field) {
            switch ($field) {
                case 'total_tagihan_penyesuaian':
                    echo "            \$table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0)->after('total_pembayaran');\n";
                    break;
                case 'alasan_penyesuaian':
                    echo "            \$table->text('alasan_penyesuaian')->nullable()->after('total_tagihan_setelah_penyesuaian');\n";
                    break;
                case 'keterangan':
                    echo "            \$table->text('keterangan')->nullable()->after('alasan_penyesuaian');\n";
                    break;
            }
        }

        echo "        });\n";
        echo "    }\n\n";
        echo "    public function down()\n";
        echo "    {\n";
        echo "        Schema::table('pembayaran_pranota_surat_jalan', function (Blueprint \$table) {\n";

        foreach ($missingFields as $field) {
            echo "            \$table->dropColumn('{$field}');\n";
        }

        echo "        });\n";
        echo "    }\n";
        echo "}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
