<?php

/**
 * AUTO FIX ROUTES PERMISSIONS
 * This script will add permission middleware to unprotected routes
 */

// Priority routes that need fixing (excluding onboarding routes)
$routeFixes = [
    // Dashboard
    'dashboard' => [
        'middleware' => 'can:dashboard',
        'pattern' => "Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');"
    ],

    // Master Tujuan
    'master.tujuan' => [
        'middleware' => 'can:master-tujuan',
        'routes' => [
            'master/tujuan' => ['get' => 'view', 'post' => 'create'],
            'master/tujuan/create' => ['get' => 'create'],
            'master/tujuan/{tujuan}' => ['get' => 'view', 'put|patch' => 'update', 'delete' => 'delete'],
            'master/tujuan/{tujuan}/edit' => ['get' => 'update']
        ]
    ],

    // Master Divisi
    'master.divisi' => [
        'middleware' => 'can:master-divisi',
        'routes' => [
            'master/divisi' => ['get' => 'view', 'post' => 'create'],
            'master/divisi/create' => ['get' => 'create'],
            'master/divisi/{divisi}' => ['get' => 'view', 'put|patch' => 'update', 'delete' => 'delete'],
            'master/divisi/{divisi}/edit' => ['get' => 'update'],
            'master/divisi/import' => ['post' => 'create'],
            'master/divisi/download-template' => ['get' => 'view']
        ]
    ],

    // Master Pajak
    'master.pajak' => [
        'middleware' => 'can:master-pajak',
        'routes' => [
            'master/pajak' => ['get' => 'view', 'post' => 'create'],
            'master/pajak/create' => ['get' => 'create'],
            'master/pajak/{pajak}' => ['get' => 'view', 'put|patch' => 'update', 'delete' => 'delete'],
            'master/pajak/{pajak}/edit' => ['get' => 'update'],
            'master/pajak/import' => ['post' => 'create'],
            'master/pajak/download-template' => ['get' => 'view']
        ]
    ],

    // Master Cabang
    'master.cabang' => [
        'middleware' => 'can:master-cabang',
        'routes' => [
            'master/cabang' => ['get' => 'view', 'post' => 'create'],
            'master/cabang/create' => ['get' => 'create'],
            'master/cabang/{cabang}' => ['get' => 'view', 'put|patch' => 'update', 'delete' => 'delete'],
            'master/cabang/{cabang}/edit' => ['get' => 'update']
        ]
    ],

    // Master COA
    'master-coa' => [
        'middleware' => 'can:master-coa',
        'routes' => [
            'master/coa' => ['get' => 'view', 'post' => 'create'],
            'master/coa/create' => ['get' => 'create'],
            'master/coa/{coa}' => ['get' => 'view', 'put|patch' => 'update', 'delete' => 'delete'],
            'master/coa/{coa}/edit' => ['get' => 'update'],
            'master/coa/import' => ['post' => 'create'],
            'master/coa/download-template' => ['get' => 'view']
        ]
    ],

    // Master Bank
    'master-bank' => [
        'middleware' => 'can:master-bank',
        'routes' => [
            'master/bank' => ['get' => 'view', 'post' => 'create'],
            'master/bank/create' => ['get' => 'create'],
            'master/bank/{bank}' => ['get' => 'view', 'put|patch' => 'update', 'delete' => 'delete'],
            'master/bank/{bank}/edit' => ['get' => 'update'],
            'master/bank/import' => ['post' => 'create'],
            'master/bank/download-template' => ['get' => 'view']
        ]
    ]
];

echo "ROUTE PERMISSION FIXES NEEDED:\n";
echo "=====================================\n\n";

foreach ($routeFixes as $module => $config) {
    echo "🔧 {$module}:\n";
    if (isset($config['routes'])) {
        foreach ($config['routes'] as $route => $methods) {
            foreach ($methods as $method => $action) {
                echo "   - {$method} {$route} → can:{$module}-{$action}\n";
            }
        }
    } elseif (isset($config['pattern'])) {
        echo "   - {$config['pattern']}\n";
        echo "   → Add: {$config['middleware']}\n";
    }
    echo "\n";
}

echo "\nTo fix these routes, we need to:\n";
echo "1. Add middleware to each route manually\n";
echo "2. Or convert to resource routes with middleware array\n";
echo "3. Update any missing permission seeds\n\n";

echo "Example fix pattern:\n";
echo "Route::get('master/tujuan', [TujuanController::class, 'index'])\n";
echo "     ->name('master.tujuan.index')\n";
echo "     ->middleware('can:master-tujuan-view');\n";
