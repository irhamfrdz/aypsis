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
                'master-pricelist-pelindo' => 'Pricelist Pelindo',
                'master-tujuan' => 'Manajemen Tujuan',
                'master-kegiatan' => 'Manajemen Kegiatan',
                'master-permission' => 'Manajemen Permission',
                'master-mobil' => 'Manajemen Mobil',
            ],
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
        'pembayaran-pranota-ongkos-truk' => [
            'name' => 'Pembayaran Pranota Ongkos Truk',
            'description' => 'Menu pembayaran pranota ongkos truk',
            'required' => true,
            'sub_modules' => [
                'pembayaran-pranota-ongkos-truk-view' => 'View Pembayaran',
                'pembayaran-pranota-ongkos-truk-create' => 'Buat Pembayaran',
                'pembayaran-pranota-ongkos-truk-edit' => 'Edit Pembayaran',
                'pembayaran-pranota-ongkos-truk-delete' => 'Hapus Pembayaran',
            ],
        ],
        'pranota-stock' => [
            'name' => 'Pranota Stock Amprahan',
            'description' => 'Menu riwayat pranota stock amprahan',
            'required' => true,
            'sub_modules' => [
                'pranota-stock-view' => 'View Riwayat',
                'pranota-stock-create' => 'Buat Pranota',
                'pranota-stock-print' => 'Cetak Pranota',
                'pranota-stock-delete' => 'Hapus Pranota',
            ],
        ],
        'pranota-perbaikan-kontainer' => [
            'name' => 'Pranota Perbaikan Kontainer',
            'description' => 'Menu riwayat pranota perbaikan kontainer',
            'required' => true,
            'sub_modules' => [
                'pranota-perbaikan-kontainer-view' => 'View Riwayat',
                'pranota-perbaikan-kontainer-create' => 'Buat Pranota',
                'pranota-perbaikan-kontainer-update' => 'Edit Pranota',
                'pranota-perbaikan-kontainer-delete' => 'Hapus Pranota',
                'pranota-perbaikan-kontainer-print' => 'Cetak Pranota',
            ],
        ],
        'tanda-terima-surat-jalan-kontainer-sewa' => [
            'name' => 'Tanda Terima SJ Kontainer Sewa',
            'description' => 'Menu tanda terima surat jalan kontainer sewa',
            'required' => true,
            'sub_modules' => [
                'tanda-terima-surat-jalan-kontainer-sewa-view' => 'View Tanda Terima',
                'tanda-terima-surat-jalan-kontainer-sewa-create' => 'Buat Tanda Terima',
                'tanda-terima-surat-jalan-kontainer-sewa-update' => 'Edit Tanda Terima',
                'tanda-terima-surat-jalan-kontainer-sewa-delete' => 'Hapus Tanda Terima',
            ],
        ],
        'tagihan-pelindo' => [
            'name' => 'Tagihan Pelindo',
            'description' => 'Menu tagihan pelindo',
            'required' => true,
            'sub_modules' => [
                'tagihan-pelindo-view' => 'View Tagihan',
                'tagihan-pelindo-create' => 'Buat Tagihan',
                'tagihan-pelindo-edit' => 'Edit Tagihan',
                'tagihan-pelindo-delete' => 'Hapus Tagihan',
            ],
        ],
        'pembelian-bbm-batam' => [
            'name' => 'Pembelian BBM Batam',
            'description' => 'Menu pembelian BBM Batam',
            'required' => true,
            'sub_modules' => [
                'pembelian-bbm-batam-view' => 'View Pembelian BBM Batam',
                'pembelian-bbm-batam-create' => 'Buat Pembelian BBM Batam',
                'pembelian-bbm-batam-edit' => 'Edit Pembelian BBM Batam',
                'pembelian-bbm-batam-delete' => 'Hapus Pembelian BBM Batam',
            ],
        ],
        'master-kartu-bensin-batam' => [
            'name' => 'Master Kartu Bensin Batam',
            'description' => 'Menu master kartu bensin Batam',
            'required' => true,
            'sub_modules' => [
                'master-kartu-bensin-batam-view' => 'View Kartu Bensin Batam',
                'master-kartu-bensin-batam-create' => 'Buat Kartu Bensin Batam',
                'master-kartu-bensin-batam-edit' => 'Edit Kartu Bensin Batam',
                'master-kartu-bensin-batam-delete' => 'Hapus Kartu Bensin Batam',
            ],
        ],
        'gaji-supir-batam' => [
            'name' => 'Gaji Supir Batam',
            'description' => 'Menu Gaji Supir Batam',
            'required' => true,
            'sub_modules' => [
                'gaji-supir-batam-view' => 'View Gaji Supir Batam',
                'gaji-supir-batam-create' => 'Buat Gaji Supir Batam',
                'gaji-supir-batam-edit' => 'Edit Gaji Supir Batam',
                'gaji-supir-batam-delete' => 'Hapus Gaji Supir Batam',
                'gaji-supir-batam-export' => 'Export Gaji Supir Batam',
            ],
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

        // Pembayaran Pranota Ongkos Truk - butuh pembayaran-pranota-ongkos-truk-view
        'pembayaran-pranota-ongkos-truk' => ['pembayaran-pranota-ongkos-truk-view'],

        // Pranota Stock Amprahan - butuh pranota-stock-view
        'pranota-stock' => ['pranota-stock-view'],

        // Pranota Perbaikan Kontainer - butuh pranota-perbaikan-kontainer-view
        'pranota-perbaikan-kontainer' => ['pranota-perbaikan-kontainer-view'],

        // Tanda Terima SJ Kontainer Sewa - butuh tanda-terima-surat-jalan-kontainer-sewa-view
        'tanda-terima-surat-jalan-kontainer-sewa' => ['tanda-terima-surat-jalan-kontainer-sewa-view'],

        // Tagihan Pelindo - butuh tagihan-pelindo-view
        'tagihan-pelindo' => ['tagihan-pelindo-view'],

        // Pembelian BBM Batam - butuh pembelian-bbm-batam-view
        'pembelian-bbm-batam' => ['pembelian-bbm-batam-view'],

        // Master Kartu Bensin Batam - butuh master-kartu-bensin-batam-view
        'master-kartu-bensin-batam' => ['master-kartu-bensin-batam-view'],

        // Gaji Supir Batam - butuh gaji-supir-batam-view
        'gaji-supir-batam' => ['gaji-supir-batam-view'],
    ],
];
