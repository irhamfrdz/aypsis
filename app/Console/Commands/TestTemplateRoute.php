<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class TestTemplateRoute extends Command
{
    protected $signature = 'test:template-route';
    protected $description = 'Test template route access';

    public function handle()
    {
        $this->info('Testing Template Route Access...');

        try {
            // Test route resolution
            $route = Route::getRoutes()->getByName('master.stock-kontainer.template');
            if ($route) {
                $this->info("✓ Route found: " . $route->uri());
                $this->info("✓ Route controller: " . $route->getActionName());
            } else {
                $this->error("✗ Route not found");
                return;
            }

            // Test if we can create a request to this route
            $url = route('master.stock-kontainer.template');
            $this->info("✓ Generated URL: " . $url);

            $this->info("\n🎉 Template route is properly configured!");
            $this->info("You can now access: " . $url);

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
        }
    }
}
