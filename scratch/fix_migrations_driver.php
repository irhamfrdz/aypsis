<?php

$dir = __DIR__.'/../database/migrations';
$filesToFix = [
    '2025_10_14_121000_add_checkpoint_status_to_surat_jalans.php',
    '2025_10_15_151510_add_belum_masuk_checkpoint_to_status_enum_in_surat_jalans_table.php',
    '2025_10_16_133324_add_approved_status_to_surat_jalans_table.php',
    '2025_10_23_124715_update_status_pembayaran_enum_in_surat_jalans_table.php',
    '2025_10_27_151528_update_lcl_dimensions_from_cm_to_meters.php',
    '2025_10_27_161432_update_meter_kubik_to_3_decimal.php',
    '2025_10_28_155427_update_tanda_terimas_meter_kubik_to_3_decimal.php',
    '2025_10_28_161537_update_prospek_total_volume_to_3_decimal.php',
    '2025_10_30_102344_update_status_pembayaran_uang_rit_add_sudah_masuk_pranota.php',
    '2025_11_07_020037_update_status_enum_in_uang_jalans_table.php',
    '2025_11_07_095911_add_belum_masuk_pranota_to_uang_jalans_status_enum.php',
    '2025_11_07_102829_add_approved_status_to_pranota_uang_jalans_enum.php',
    '2025_11_22_120524_modify_status_pembayaran_uang_jalan_enum_to_surat_jalans.php',
    '2026_01_26_150000_change_kondisi_to_string_in_stock_bans.php',
    '2026_02_07_085000_change_status_to_string_in_stock_bans.php',
];

foreach ($filesToFix as $file) {
    $path = $dir.'/'.$file;
    if (! file_exists($path)) {
        echo "File does not exist: {$file}\n";

        continue;
    }

    $content = file_get_contents($path);

    // We want to rewrite the up() and down() methods to wrap their bodies.
    // Let's use a regex to find the up() method body and wrap it.
    // Since methods can have different signatures, e.g., public function up(): void or public function up()

    // Match up method: public function up(...): ... { <body> }
    // Since PHP files can have nested braces, but these migrations are very simple classes,
    // we can parse the up() body by finding the matching brace, or using a robust regex if brace nesting is low.

    // Let's do it by finding DB::statement(...) calls inside the code and wrapping those specifically,
    // or wrapping the entire up/down method.
    // Actually, wrapping specific DB::statement calls that contain ALTER TABLE is much more precise!
    // Let's find DB::statement(...) occurrences that contain ALTER TABLE.

    // Let's use preg_replace_callback to find DB::statement(...) or DB::statement(...) with multi-line.
    // In PHP, DB::statement(...) can be matched with:
    // DB::statement\(\s*(["'])(?:(?!\1).)*\bALTER\s+TABLE\b(?:(?!\1).)*\1\s*\);
    // But since it can be multi-line, we can use the 's' modifier.

    $pattern = '/DB::statement\(\s*([\'"])(.*?)\1\s*\);/s';

    $newContent = preg_replace_callback($pattern, function ($matches) {
        $fullMatch = $matches[0];
        $sql = $matches[2];

        if (stripos($sql, 'ALTER TABLE') !== false) {
            // Wrap with SQLite driver check
            return "if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {\n            ".$fullMatch."\n        }";
        }

        return $fullMatch;
    }, $content);

    if ($newContent !== $content) {
        file_put_contents($path, $newContent);
        echo "Successfully updated: {$file}\n";
    } else {
        echo "No changes needed for: {$file}\n";
    }
}
