<?php

namespace App\Providers;

use App\Models\Kontainer;
use App\Models\Permission;
use App\Models\StockKontainer;
use App\Models\UangJalan;
use App\Models\User;
use App\Observers\KontainerObserver;
use App\Observers\StockKontainerObserver;
use App\Observers\UangJalanObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default pagination view to use Tailwind CSS
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');

        // Register observers
        Kontainer::observe(KontainerObserver::class);
        StockKontainer::observe(StockKontainerObserver::class);
        UangJalan::observe(UangJalanObserver::class);

        // Bypass permission checks for user 'kiky'
        Gate::before(function (User $user, string $ability) {
            $username = strtolower((string) ($user->username ?? ''));
            if ($username === 'kiky') {
                return true;
            }
        });

        // Gunakan try-catch atau cek Schema untuk menghindari error saat migrasi awal
        try {
            if (Schema::hasTable('permissions')) {
                // Ambil semua izin dari database
                $permissions = Permission::all();
                foreach ($permissions as $permission) {
                    // Definisikan Gate secara dinamis
                    Gate::define($permission->name, function (User $user) use ($permission) {
                        return $user->permissions()->where('name', $permission->name)->exists();
                    });
                }
            }
        } catch (\Exception $e) {
            // Laporkan error jika perlu, atau abaikan agar tidak mengganggu proses instalasi
        }
    }
}
