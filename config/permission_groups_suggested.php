<?php

/**
 * Suggested permission groups generated from audit.
 *
 * This file is a non-destructive suggestion. Review and, if acceptable,
 * merge entries into `config/permission_groups.php` or rename this file.
 */

return [
    // Master data groups (dot notation in permission names)
    'master.karyawan' => [ 'label' => 'Master Karyawan', 'prefixes' => ['master.karyawan'] ],
    'master.kegiatan' => [ 'label' => 'Master Kegiatan', 'prefixes' => ['master.kegiatan'] ],
    'master.kontainer' => [ 'label' => 'Master Kontainer', 'prefixes' => ['master.kontainer'] ],
    'master.mobil' => [ 'label' => 'Master Mobil', 'prefixes' => ['master.mobil'] ],
    'master.permission' => [ 'label' => 'Master Permission', 'prefixes' => ['master.permission'] ],
    'master.pricelist' => [ 'label' => 'Master Pricelist Sewa Kontainer', 'prefixes' => ['master.pricelist-sewa-kontainer', 'master.pricelist-sewa-kontainer.'] ],
    'master.tujuan' => [ 'label' => 'Master Tujuan', 'prefixes' => ['master.tujuan'] ],
    'master.user' => [ 'label' => 'Master User', 'prefixes' => ['master.user'] ],

    // Permohonan and related
    'permohonan' => [ 'label' => 'Permohonan', 'prefixes' => ['permohonan', 'permohonan.'] ],

    // Supir / checkpoint
    'supir' => [ 'label' => 'Supir', 'prefixes' => ['supir.','supir-'] ],

    // System-level
    'system' => [ 'label' => 'System', 'prefixes' => ['dashboard','login','logout','admin.','storage.'] ],

    // Other singletons (can be merged into system or refined)
    'storage' => [ 'label' => 'Storage', 'prefixes' => ['storage.','storage'] ],
];
