<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentCount = DB::table('permissions')->count();
        $targetCount = 300;
        $permissionsToAdd = $targetCount - $currentCount;

        if ($permissionsToAdd <= 0) {
            $this->command->info("Sudah ada {$currentCount} permissions. Tidak perlu menambahkan permissions tambahan.");
            return;
        }

        $this->command->info("Menambahkan {$permissionsToAdd} permissions tambahan untuk mencapai total {$targetCount} permissions.");

        $additionalPermissions = [];

        // Permissions untuk sistem notifikasi
        $notificationPermissions = [
            ['notification.view', 'Melihat notifikasi', 382],
            ['notification.create', 'Membuat notifikasi', 383],
            ['notification.update', 'Mengupdate notifikasi', 384],
            ['notification.delete', 'Menghapus notifikasi', 385],
            ['notification.send', 'Mengirim notifikasi', 386],
        ];

        // Permissions untuk sistem laporan
        $reportPermissions = [
            ['report.view', 'Melihat laporan', 387],
            ['report.create', 'Membuat laporan', 388],
            ['report.update', 'Mengupdate laporan', 389],
            ['report.delete', 'Menghapus laporan', 390],
            ['report.export', 'Export laporan', 391],
            ['report.print', 'Print laporan', 392],
            ['report.dashboard', 'Akses dashboard laporan', 393],
        ];

        // Permissions untuk sistem audit trail
        $auditPermissions = [
            ['audit.view', 'Melihat audit trail', 394],
            ['audit.export', 'Export audit trail', 395],
            ['audit.delete', 'Menghapus audit trail lama', 396],
        ];

        // Permissions untuk sistem backup
        $backupPermissions = [
            ['backup.create', 'Membuat backup', 397],
            ['backup.restore', 'Restore backup', 398],
            ['backup.download', 'Download backup', 399],
            ['backup.delete', 'Menghapus backup', 400],
            ['backup.schedule', 'Menjadwalkan backup', 401],
        ];

        // Permissions untuk sistem maintenance
        $maintenancePermissions = [
            ['maintenance.view', 'Melihat status maintenance', 402],
            ['maintenance.schedule', 'Menjadwalkan maintenance', 403],
            ['maintenance.execute', 'Menjalankan maintenance', 404],
            ['maintenance.cancel', 'Membatalkan maintenance', 405],
        ];

        // Permissions untuk sistem API
        $apiPermissions = [
            ['api.access', 'Akses API', 406],
            ['api.webhook.view', 'Melihat webhook', 407],
            ['api.webhook.create', 'Membuat webhook', 408],
            ['api.webhook.update', 'Mengupdate webhook', 409],
            ['api.webhook.delete', 'Menghapus webhook', 410],
        ];

        // Permissions untuk sistem integrasi
        $integrationPermissions = [
            ['integration.view', 'Melihat integrasi', 411],
            ['integration.create', 'Membuat integrasi', 412],
            ['integration.update', 'Mengupdate integrasi', 413],
            ['integration.delete', 'Menghapus integrasi', 414],
            ['integration.test', 'Test integrasi', 415],
        ];

        // Permissions untuk sistem workflow
        $workflowPermissions = [
            ['workflow.view', 'Melihat workflow', 416],
            ['workflow.create', 'Membuat workflow', 417],
            ['workflow.update', 'Mengupdate workflow', 418],
            ['workflow.delete', 'Menghapus workflow', 419],
            ['workflow.execute', 'Menjalankan workflow', 420],
        ];

        // Permissions untuk sistem dokumentasi
        $documentationPermissions = [
            ['documentation.view', 'Melihat dokumentasi', 421],
            ['documentation.create', 'Membuat dokumentasi', 422],
            ['documentation.update', 'Mengupdate dokumentasi', 423],
            ['documentation.delete', 'Menghapus dokumentasi', 424],
            ['documentation.publish', 'Publish dokumentasi', 425],
        ];

        // Permissions untuk sistem training
        $trainingPermissions = [
            ['training.view', 'Melihat training', 426],
            ['training.create', 'Membuat training', 427],
            ['training.update', 'Mengupdate training', 428],
            ['training.delete', 'Menghapus training', 429],
            ['training.enroll', 'Mendaftar training', 430],
            ['training.complete', 'Menyelesaikan training', 431],
        ];

        // Permissions untuk sistem feedback
        $feedbackPermissions = [
            ['feedback.view', 'Melihat feedback', 432],
            ['feedback.create', 'Memberikan feedback', 433],
            ['feedback.update', 'Mengupdate feedback', 434],
            ['feedback.delete', 'Menghapus feedback', 435],
            ['feedback.respond', 'Merespons feedback', 436],
        ];

        // Permissions untuk sistem knowledge base
        $knowledgePermissions = [
            ['knowledge.view', 'Melihat knowledge base', 437],
            ['knowledge.create', 'Membuat artikel knowledge base', 438],
            ['knowledge.update', 'Mengupdate artikel knowledge base', 439],
            ['knowledge.delete', 'Menghapus artikel knowledge base', 440],
            ['knowledge.publish', 'Publish artikel knowledge base', 441],
        ];

        // Permissions untuk sistem analytics
        $analyticsPermissions = [
            ['analytics.view', 'Melihat analytics', 442],
            ['analytics.create', 'Membuat report analytics', 443],
            ['analytics.update', 'Mengupdate report analytics', 444],
            ['analytics.delete', 'Menghapus report analytics', 445],
            ['analytics.export', 'Export analytics', 446],
        ];

        // Permissions untuk sistem monitoring
        $monitoringPermissions = [
            ['monitoring.view', 'Melihat monitoring', 447],
            ['monitoring.alert.view', 'Melihat alert monitoring', 448],
            ['monitoring.alert.create', 'Membuat alert monitoring', 449],
            ['monitoring.alert.update', 'Mengupdate alert monitoring', 450],
            ['monitoring.alert.delete', 'Menghapus alert monitoring', 451],
        ];

        // Permissions untuk sistem security
        $securityPermissions = [
            ['security.view', 'Melihat pengaturan security', 452],
            ['security.update', 'Mengupdate pengaturan security', 453],
            ['security.policy.view', 'Melihat security policy', 454],
            ['security.policy.create', 'Membuat security policy', 455],
            ['security.policy.update', 'Mengupdate security policy', 456],
            ['security.policy.delete', 'Menghapus security policy', 457],
        ];

        // Permissions untuk sistem compliance
        $compliancePermissions = [
            ['compliance.view', 'Melihat compliance', 458],
            ['compliance.create', 'Membuat compliance report', 459],
            ['compliance.update', 'Mengupdate compliance report', 460],
            ['compliance.delete', 'Menghapus compliance report', 461],
            ['compliance.audit', 'Melakukan compliance audit', 462],
        ];

        // Permissions untuk sistem quality assurance
        $qaPermissions = [
            ['qa.view', 'Melihat QA reports', 463],
            ['qa.create', 'Membuat QA test', 464],
            ['qa.update', 'Mengupdate QA test', 465],
            ['qa.delete', 'Menghapus QA test', 466],
            ['qa.execute', 'Menjalankan QA test', 467],
        ];

        // Permissions untuk sistem helpdesk
        $helpdeskPermissions = [
            ['helpdesk.view', 'Melihat helpdesk tickets', 468],
            ['helpdesk.create', 'Membuat helpdesk ticket', 469],
            ['helpdesk.update', 'Mengupdate helpdesk ticket', 470],
            ['helpdesk.delete', 'Menghapus helpdesk ticket', 471],
            ['helpdesk.assign', 'Assign helpdesk ticket', 472],
            ['helpdesk.close', 'Menutup helpdesk ticket', 473],
        ];

        // Permissions untuk sistem asset management
        $assetPermissions = [
            ['asset.view', 'Melihat assets', 474],
            ['asset.create', 'Membuat asset', 475],
            ['asset.update', 'Mengupdate asset', 476],
            ['asset.delete', 'Menghapus asset', 477],
            ['asset.transfer', 'Transfer asset', 478],
            ['asset.maintenance', 'Maintenance asset', 479],
        ];

        // Permissions untuk sistem inventory
        $inventoryPermissions = [
            ['inventory.view', 'Melihat inventory', 480],
            ['inventory.create', 'Membuat inventory item', 481],
            ['inventory.update', 'Mengupdate inventory item', 482],
            ['inventory.delete', 'Menghapus inventory item', 483],
            ['inventory.adjust', 'Adjust inventory', 484],
            ['inventory.transfer', 'Transfer inventory', 485],
        ];

        // Permissions untuk sistem procurement
        $procurementPermissions = [
            ['procurement.view', 'Melihat procurement', 486],
            ['procurement.create', 'Membuat procurement request', 487],
            ['procurement.update', 'Mengupdate procurement request', 488],
            ['procurement.delete', 'Menghapus procurement request', 489],
            ['procurement.approve', 'Approve procurement request', 490],
            ['procurement.reject', 'Reject procurement request', 491],
        ];

        // Permissions untuk sistem vendor management
        $vendorPermissions = [
            ['vendor.view', 'Melihat vendors', 492],
            ['vendor.create', 'Membuat vendor', 493],
            ['vendor.update', 'Mengupdate vendor', 494],
            ['vendor.delete', 'Menghapus vendor', 495],
            ['vendor.approve', 'Approve vendor', 496],
            ['vendor.blacklist', 'Blacklist vendor', 497],
        ];

        // Permissions untuk sistem contract management
        $contractPermissions = [
            ['contract.view', 'Melihat contracts', 498],
            ['contract.create', 'Membuat contract', 499],
            ['contract.update', 'Mengupdate contract', 500],
            ['contract.delete', 'Menghapus contract', 501],
            ['contract.approve', 'Approve contract', 502],
            ['contract.terminate', 'Terminate contract', 503],
        ];

        // Gabungkan semua permissions
        $allPermissions = array_merge(
            $notificationPermissions,
            $reportPermissions,
            $auditPermissions,
            $backupPermissions,
            $maintenancePermissions,
            $apiPermissions,
            $integrationPermissions,
            $workflowPermissions,
            $documentationPermissions,
            $trainingPermissions,
            $feedbackPermissions,
            $knowledgePermissions,
            $analyticsPermissions,
            $monitoringPermissions,
            $securityPermissions,
            $compliancePermissions,
            $qaPermissions,
            $helpdeskPermissions,
            $assetPermissions,
            $inventoryPermissions,
            $procurementPermissions,
            $vendorPermissions,
            $contractPermissions
        );

        // Ambil hanya permissions yang dibutuhkan
        $permissionsToInsert = array_slice($allPermissions, 0, $permissionsToAdd);

        foreach ($permissionsToInsert as $permission) {
            $additionalPermissions[] = [
                'id' => $permission[2],
                'name' => $permission[0],
                'description' => $permission[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert permissions
        if (!empty($additionalPermissions)) {
            DB::table('permissions')->insert($additionalPermissions);
            $this->command->info("Berhasil menambahkan " . count($additionalPermissions) . " permissions tambahan.");
        }

        $finalCount = DB::table('permissions')->count();
        $this->command->info("Total permissions sekarang: {$finalCount}");
    }
}
