<?php

/**
 * Sistem Permission Sederhana AYPSIS
 *
 * Permission Structure:
 * - dashboard: Akses dashboard (semua user)
 * - master-data: Akses semua menu master data
 * - user-approval: Persetujuan user baru
 * - tagihan-kontainer: Menu tagihan kontainer sewa
 * - permohonan: Menu permohonan memo
 * - pranota-supir: Menu pranota memo
 * - pembayaran-pranota-supir: Menu pembayaran pranota memo
 *
 * Sub-permissions untuk master data:
 * - master-karyawan
 * - master-user
 * - master-kontainer
 * - master-pricelist-sewa-kontainer
 * - master-tujuan
 * - master-kegiatan
 * - master-permission
 * - master-mobil
 */

return [
    'modules' => [
        'dashboard' => [
            'name' => 'Dashboard',
            'description' => 'Akses halaman dashboard',
            'required' => false, // Semua user bisa akses
        ],
        'master-data' => [
            'name' => 'Master Data',
            'description' => 'Akses menu master data',
            'required' => true,
            'sub_modules' => [
                'master-karyawan' => 'Manajemen Karyawan',
                'master-user' => 'Manajemen User',
                'master-kontainer' => 'Manajemen Kontainer',
                'master-pricelist-sewa-kontainer' => 'Pricelist Sewa Kontainer',
                'master-pricelist-cat' => 'Pricelist CAT',
                'master-tujuan' => 'Manajemen Tujuan',
                'master-kegiatan' => 'Manajemen Kegiatan',
                'master-permission' => 'Manajemen Permission',
                'master-mobil' => 'Manajemen Mobil',
            ]
        ],
        'user-approval' => [
            'name' => 'Persetujuan User',
            'description' => 'Menyetujui user baru',
            'required' => true,
        ],
        'tagihan-kontainer' => [
            'name' => 'Tagihan Kontainer Sewa',
            'description' => 'Menu tagihan kontainer sewa',
            'required' => true,
        ],
        'permohonan' => [
            'name' => 'Permohonan Memo',
            'description' => 'Menu permohonan memo',
            'required' => true,
        ],
        'pranota-supir' => [
            'name' => 'Pranota Memo',
            'description' => 'Menu pranota memo',
            'required' => true,
        ],
        'pembayaran-pranota-supir' => [
            'name' => 'Pembayaran Pranota Memo',
            'description' => 'Menu pembayaran pranota memo',
            'required' => true,
        ],
    ],

    'menu_permissions' => [
        // Dashboard - semua user bisa akses
        'dashboard' => [],

        // Master Data - butuh permission master-data atau sub-module specific
        'master' => ['master-data'],

        // User Approval - butuh master-user atau user-approval
        'user-approval' => ['master-user', 'user-approval'],

        // Tagihan Kontainer - butuh tagihan-kontainer
        'tagihan-kontainer' => ['tagihan-kontainer'],

        // Permohonan - butuh permohonan
        'permohonan' => ['permohonan'],

        // Pranota Memo - butuh pranota-supir
        'pranota-supir' => ['pranota-supir'],

        // Pembayaran Pranota Memo - butuh pembayaran-pranota-supir
        'pembayaran-pranota-supir' => ['pembayaran-pranota-supir'],
    ]
];
