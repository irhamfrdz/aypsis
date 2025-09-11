<?php

/**
 * Permission groups definition.
 *
 * Each group has a human label and one or more prefixes. Any permission whose
 * name starts with a prefix will be considered part of the group in the UI.
 *
 * Edit this file to tune grouping to your project terminology.
 */

return [
    'master' => [
        'label' => 'Master Data',
        'prefixes' => ['master-'],
    ],

    'pranota' => [
        'label' => 'Pranota',
        'prefixes' => ['pranota-'],
    ],

    'pembayaran' => [
        'label' => 'Pembayaran',
        'prefixes' => ['pembayaran-'],
    ],

    'approval' => [
        'label' => 'Approval',
        'prefixes' => ['approval-'],
    ],

    'laporan' => [
        'label' => 'Laporan',
        'prefixes' => ['laporan-'],
    ],

    'tagihan-kontainer' => [
        'label' => 'Tagihan Kontainer',
        'prefixes' => ['tagihan-kontainer-sewa', 'tagihan-kontainer'],
    ],

    'permohonan' => [
        'label' => 'Permohonan',
        'prefixes' => ['permohonan-'],
    ],
];
