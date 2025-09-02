<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AdjustNormalizationTest extends TestCase
{
    /**
     * Normalize string like the client-side normalizeForSubmit:
     * - strip leading +
     * - if both dot and comma present: dots are thousands, comma is decimal -> remove dots, replace comma with dot
     * - if only comma present: replace comma with dot
     * - otherwise return cleaned string
     */
    private static function normalizeForSubmit(?string $val)
    {
        if ($val === null) return null;
        $s = trim($val);
        if ($s === '') return $s;
        // strip leading +, keep leading -
        if (strpos($s, '+') === 0) {
            $s = substr($s, 1);
        }
        $hasDot = strpos($s, '.') !== false;
        $hasComma = strpos($s, ',') !== false;
        if ($hasDot && $hasComma) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
            return $s;
        }
        if (!$hasDot && $hasComma) {
            $s = str_replace(',', '.', $s);
            return $s;
        }
        return $s;
    }

    public function test_strip_plus()
    {
        $this->assertSame('100000', self::normalizeForSubmit('+100000'));
    }

    public function test_indonesia_thousands_and_decimal()
    {
        $this->assertSame('100000.50', self::normalizeForSubmit('100.000,50'));
        $this->assertSame('1234567.89', self::normalizeForSubmit('1.234.567,89'));
    }

    public function test_only_comma_decimal()
    {
        $this->assertSame('1.25', self::normalizeForSubmit('1,25'));
    }

    public function test_only_dots_are_kept_as_is_or_removed_by_rules()
    {
        // single dot with no comma likely already dot-decimal; keep as-is
        $this->assertSame('1234.56', self::normalizeForSubmit('1234.56'));
        // dots that are thousands but no comma: JS returns same string; server may need handling but we mirror JS
        $this->assertSame('1.234.567', self::normalizeForSubmit('1.234.567'));
    }

    public function test_negative_and_empty()
    {
        $this->assertSame('-5000', self::normalizeForSubmit('-5000'));
        $this->assertSame('', self::normalizeForSubmit(''));
        $this->assertNull(self::normalizeForSubmit(null));
    }
}
