<?php

namespace App\Helpers;

class RekapShipName
{
    public static function key(?string $name): string
    {
        $key = preg_replace('/[^a-z0-9]/', '', preg_replace('/^km[.\s]+/i', '', strtolower(trim((string) $name))));

        // Historical transactions use both names for the same vessel.
        return $key === 'sriwijayaraya' ? 'sriwijaya' : $key;
    }

    public static function likePattern(string $name): string
    {
        if (self::key($name) === 'sriwijaya') {
            return '%sriwijaya%';
        }

        return '%'.preg_replace('/[^a-z0-9]+/i', '%', $name).'%';
    }
}
