<?php

namespace App\Services;

use Illuminate\Support\Collection;

class BiayaAgenTripCounter
{
    public function count(Collection $rows): int
    {
        return $rows->pluck('voyage')
            ->map(fn ($voyage) => $this->tripKey((string) $voyage))
            ->filter()
            ->unique()
            ->count();
    }

    public function tripKey(string $voyage): ?string
    {
        $voyage = strtoupper(preg_replace('/\s+/', '', trim($voyage)));
        if ($voyage === '') {
            return null;
        }

        if (preg_match('/^([A-Z]+\d+)(JB|BJ|JP|PJ)(\d{0,4})$/', $voyage, $match)) {
            $route = in_array($match[2], ['JB', 'BJ'], true) ? 'JB-BJ' : 'JP-PJ';

            return $match[1].$route.$match[3];
        }

        return $voyage;
    }
}
