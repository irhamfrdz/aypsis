<?php

namespace Tests\Unit;

use App\Services\ManifestApprovalGoodsSync;
use PHPUnit\Framework\TestCase;

class ManifestApprovalGoodsSyncTest extends TestCase
{
    public function test_dot_only_metric_is_kept_as_decimal(): void
    {
        $sync = new ManifestApprovalGoodsSync;

        $this->assertSame('5.825', $sync->parseMetric('5.825'));
        $this->assertSame('13.728', $sync->parseMetric('13.728'));
        $this->assertSame('25.000', $sync->parseMetric('25.000'));
    }

    public function test_comma_decimal_with_grouping_is_normalized(): void
    {
        $sync = new ManifestApprovalGoodsSync;

        $this->assertSame('17665.78', $sync->parseMetric('17.665,78'));
        $this->assertSame('3500.25', $sync->parseMetric('3.500,25'));
    }
}
