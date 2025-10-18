<?php

namespace App\Models;


use App\Traits\Auditable;
class TestCrewChecklist
{
    use Auditable;

    public static function test()
    {
        return 'Working';
    }
}
