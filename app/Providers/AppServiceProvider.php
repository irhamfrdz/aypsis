<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Observers\KontainerObserver;
use App\Observers\StockKontainerObserver;

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
