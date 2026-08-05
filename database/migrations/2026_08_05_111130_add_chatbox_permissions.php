<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'chatbox-view' => 'View Chatbox',
            'chatbox-create' => 'Buat Chat/FAQ',
            'chatbox-edit' => 'Edit Chat/FAQ',
            'chatbox-delete' => 'Hapus Chat/FAQ',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate(['name' => $key]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'chatbox-view',
            'chatbox-create',
            'chatbox-edit',
            'chatbox-delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
