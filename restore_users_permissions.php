<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚨 EMERGENCY RESTORATION: Creating users and permissions tables...\n\n";

try {
    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Create users table
    echo "👥 Creating users table...\n";
    DB::statement("
        CREATE TABLE `users` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `karyawan_id` bigint unsigned DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `users_username_unique` (`username`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Users table created\n";
    
    // Create permissions table
    echo "🔑 Creating permissions table...\n";
    DB::statement("
        CREATE TABLE `permissions` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `permissions_name_unique` (`name`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Permissions table created\n";
    
    // Create user_permissions table
    echo "🔗 Creating user_permissions table...\n";
    DB::statement("
        CREATE TABLE `user_permissions` (
            `user_id` bigint unsigned NOT NULL,
            `permission_id` bigint unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`user_id`,`permission_id`),
            KEY `user_permissions_permission_id_foreign` (`permission_id`),
            CONSTRAINT `user_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
            CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ User_permissions table created\n";
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\n🎉 Tables successfully created!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}