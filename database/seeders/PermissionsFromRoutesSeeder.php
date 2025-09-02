<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use App\Models\Permission;

class PermissionsFromRoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = collect(Route::getRoutes())->map(function ($r) {
            return [
                'name' => $r->getName(),
                'uri' => $r->uri(),
                'methods' => implode('|', $r->methods()),
            ];
        })->filter(function ($r) {
            return !empty($r['name']);
        })->unique('name');

        $ignorePrefixes = ['_debugbar', 'ignition', 'admin.debug', 'telescope'];

        $count = 0;
        foreach ($routes as $r) {
            $skip = false;
            foreach ($ignorePrefixes as $p) {
                if (str_starts_with($r['name'], $p)) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            $perm = Permission::firstOrCreate(
                ['name' => $r['name']],
                ['description' => 'Izin untuk route: ' . $r['uri']]
            );
            if ($perm->wasRecentlyCreated) $count++;
        }

        $this->command->info("PermissionsFromRoutesSeeder: ensured {$routes->count()} named routes, created {$count} new permissions.");
    }
}
