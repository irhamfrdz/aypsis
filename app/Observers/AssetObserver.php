<?php

namespace App\Observers;

use App\Models\Asset;
use App\Models\TandaTerimaAsset;

class AssetObserver
{
    public function created(Asset $asset): void
    {
        $asset->tandaTerima()->create(TandaTerimaAsset::attributesFromAsset($asset));
    }
}
