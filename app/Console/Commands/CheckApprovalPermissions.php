<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckApprovalPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:approval-permissions {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check approval permissions in database and test conversion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Checking Approval Permissions ===');

        // Check if approval permissions exist
        $approvalPerms = \App\Models\Permission::where('name', 'like', 'approval-tugas%')->get();

        $this->info('Found ' . $approvalPerms->count() . ' approval permissions:');
        foreach ($approvalPerms as $perm) {
            $this->line("ID: {$perm->id} - Name: {$perm->name}");
        }

        // Test matrix conversion
        $testMatrix = [
            'approval-tugas-1' => [
                'view' => '1',
                'approve' => '1'
            ],
            'approval-tugas-2' => [
                'view' => '1'
            ]
        ];

        $this->info("\nTest matrix data:");
        $this->line(json_encode($testMatrix, JSON_PRETTY_PRINT));

        // Test conversion
        $controller = new \App\Http\Controllers\UserController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('convertMatrixPermissionsToIds');
        $method->setAccessible(true);

        $permissionIds = $method->invoke($controller, $testMatrix);

        $this->info("\nConverted permission IDs:");
        $this->line(json_encode($permissionIds));

        // Test with user if provided
        $userId = $this->argument('user_id');
        if ($userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $this->info("\nTesting with user: {$user->username}");

                // Assign permissions to user
                $user->permissions()->sync($permissionIds);
                $this->info("Permissions assigned to user successfully");

                // Verify assignment
                $userPermissions = $user->permissions()->get();
                $this->info("User now has " . $userPermissions->count() . " permissions:");
                foreach ($userPermissions as $perm) {
                    $this->line("- {$perm->name}");
                }

                // Test conversion back to matrix
                $convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
                $convertToMatrixMethod->setAccessible(true);

                $permissionNames = $userPermissions->pluck('name')->toArray();
                $matrix = $convertToMatrixMethod->invoke($controller, $permissionNames);

                $this->info("\nConverted back to matrix:");
                $this->line(json_encode($matrix, JSON_PRETTY_PRINT));

            } else {
                $this->error("User with ID {$userId} not found");
            }
        }

        $this->info("\n=== Test Completed ===");
    }
}
