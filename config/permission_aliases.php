<?php

/**
 * Permission ability -> candidate permission prefixes map.
 *
 * Add entries when Blade/routes check an ability name that doesn't match
 * existing permission names. Each ability can map to one or more candidate
 * permission name prefixes; the AuthServiceProvider will try them in order.
 */

return [
    'master-pranota-tagihan-kontainer' => ['tagihan-kontainer-sewa'],
    'pembayaran-pranota-tagihan-kontainer' => ['tagihan-kontainer-sewa'],
    'master-user' => ['master-user'],
    // Add more mappings below as needed
];
