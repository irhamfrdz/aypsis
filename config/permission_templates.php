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
            'biaya-bensin-view', 'biaya-bensin-create', 'biaya-bensin-update', 'biaya-bensin-delete',

            // Pranota
            'pranota.index', 'pranota.show', 'pranota.create', 'pranota.edit',


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
            'pranota.index', 'pranota.create',


            'biaya-bensin-view', 'biaya-bensin-create',
        ],
    ],

    'supervisor' => [
        'label' => 'Supervisor',
        'description' => 'Supervisory access with approval capabilities',
        'permissions' => [
            // Master data view
            'master-karyawan', 'master-kontainer', 'master-tujuan',

            // Full pranota access
            'pranota.index', 'pranota.show', 'pranota.create', 'pranota.edit',


            'biaya-bensin-view', 'biaya-bensin-create', 'biaya-bensin-update', 'biaya-bensin-delete',

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
            'pranota.index',
            'biaya-bensin-view',
            'laporan.index',
        ],
    ],
];
