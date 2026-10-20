<?php

namespace Tests\Unit;

use App\Http\Controllers\BackupController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class BackupControllerTest extends TestCase
{
    public function test_rejects_a_corrupted_numeric_value_even_with_a_complete_footer(): void
    {
        $this->assertFalse($this->accepts("INSERT INTO `audit_logs` VALUES (1,'name',635u,'created');\n-- Dump completed on 2026-10-20\n"));
    }

    public function test_accepts_quoted_text_and_valid_numeric_values(): void
    {
        $this->assertTrue($this->accepts("INSERT INTO `audit_logs` VALUES (1,'635u\\' text',635,'created');\n-- Dump completed on 2026-10-20\n"));
    }

    public function test_rejects_a_truncated_dump(): void
    {
        $this->assertFalse($this->accepts("INSERT INTO `audit_logs` VALUES (1,'name',635,'created');\n"));
    }

    private function accepts(string $sql): bool
    {
        $path = tempnam(sys_get_temp_dir(), 'backup-test-');
        file_put_contents($path, $sql);

        try {
            $controller = new BackupController;
            $complete = new ReflectionMethod($controller, 'isCompleteDump');
            $validValues = new ReflectionMethod($controller, 'hasValidInsertValues');

            return $complete->invoke($controller, $path) && $validValues->invoke($controller, $path);
        } finally {
            unlink($path);
        }
    }
}
