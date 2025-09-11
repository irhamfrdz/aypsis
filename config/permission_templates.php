<?php

/**
 * Permission templates for quick assignment.
 *
 * Each template defines a set of permission names that should be assigned together.
 * Useful for common user roles like 'admin', 'staff', 'supervisor', etc.
 */

return [
    'admin' => [
        'label' => 'Administrator',
        'description' => 'Full access to all features',
        'permissions' => [
            // Master data
            'master-user', 'master-karyawan', 'master-kontainer', 'master-tujuan',
            'master-kegiatan', 'master-permission', 'master-mobil', 'master-pricelist-sewa-kontainer',

            // Pranota
            'pranota-supir.index', 'pranota-supir.create', 'pranota-supir.show', 'pranota-supir.edit',
            'pranota.index', 'pranota.show', 'pranota.create', 'pranota.edit',

            // Pembayaran
            'pembayaran-pranota-supir.index', 'pembayaran-pranota-supir.create',
            'pembayaran-pranota-kontainer.index', 'pembayaran-pranota-kontainer.create',

            // Tagihan
            'tagihan-kontainer-sewa.index', 'tagihan-kontainer-sewa.create',

            // Permohonan
            'permohonan.index', 'permohonan.create', 'permohonan.edit',

            // Approval
            'approval.dashboard', 'approval.create', 'approval.store',

            // Laporan
            'laporan.index',
        ],
    ],

    'staff' => [
        'label' => 'Staff',
        'description' => 'Basic operational access',
        'permissions' => [
            // Limited master data
            'master-karyawan', 'master-kontainer',

            // Pranota operations
            'pranota-supir.index', 'pranota-supir.create',
            'pranota.index', 'pranota.create',

            // Pembayaran
            'pembayaran-pranota-supir.index', 'pembayaran-pranota-supir.create',
            'pembayaran-pranota-kontainer.index', 'pembayaran-pranota-kontainer.create',

            // Tagihan
            'tagihan-kontainer-sewa.index', 'tagihan-kontainer-sewa.create',

            // Permohonan
            'permohonan.index', 'permohonan.create',
        ],
    ],

    'supervisor' => [
        'label' => 'Supervisor',
        'description' => 'Supervisory access with approval capabilities',
        'permissions' => [
            // Master data view
            'master-karyawan', 'master-kontainer', 'master-tujuan',

            // Full pranota access
            'pranota-supir.index', 'pranota-supir.create', 'pranota-supir.show', 'pranota-supir.edit',
            'pranota.index', 'pranota.show', 'pranota.create', 'pranota.edit',

            // Full pembayaran access
            'pembayaran-pranota-supir.index', 'pembayaran-pranota-supir.create', 'pembayaran-pranota-supir.edit',
            'pembayaran-pranota-kontainer.index', 'pembayaran-pranota-kontainer.create', 'pembayaran-pranota-kontainer.edit',

            // Full tagihan access
            'tagihan-kontainer-sewa.index', 'tagihan-kontainer-sewa.create', 'tagihan-kontainer-sewa.edit',

            // Full permohonan access
            'permohonan.index', 'permohonan.create', 'permohonan.edit', 'permohonan.show',

            // Approval access
            'approval.dashboard', 'approval.create', 'approval.store',

            // Laporan
            'laporan.index',
        ],
    ],

    'viewer' => [
        'label' => 'Viewer',
        'description' => 'Read-only access to reports and data',
        'permissions' => [
            // View permissions only
            'master-karyawan', 'master-kontainer', 'master-tujuan',
            'pranota-supir.index', 'pranota.index',
            'pembayaran-pranota-supir.index', 'pembayaran-pranota-kontainer.index',
            'tagihan-kontainer-sewa.index',
            'permohonan.index',
            'laporan.index',
        ],
    ],
];
