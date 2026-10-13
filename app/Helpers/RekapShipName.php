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

    public static function isValidVoyage(string $ship, string $voyage): bool
    {
        $voyage = trim($voyage);
        if ($voyage === '') {
            return false;
        }
        if (self::key($ship) !== 'sriwijaya' || strcasecmp($voyage, 'DOCK') === 0) {
            return true;
        }

        // Sriwijaya uses SR voyage numbers, optionally followed by route and year.
        return preg_match('/^SR\d+(?:[A-Z]{2}\d{2})?$/i', $voyage) === 1;
    }
}
