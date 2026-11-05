<?php

namespace Tests\Feature;

use App\Http\Controllers\NaikKapalController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class NaikKapalVoyageCleanupTest extends TestCase
{
    public function test_cleanup_requires_confirmation_and_deletes_only_sp18jb26(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('naik_kapal', function (Blueprint $table) {
            $table->id();
            $table->string('no_voyage');
        });
        DB::table('naik_kapal')->insert([
            ['no_voyage' => 'SP18JB26'],
            ['no_voyage' => 'SP18JB26'],
            ['no_voyage' => 'OTHER'],
        ]);

        $controller = new NaikKapalController;
        try {
            $controller->destroyVoyageSp18jb26(Request::create('/cleanup', 'DELETE', ['confirmation' => 'OTHER']));
            $this->fail('Confirmation should be required.');
        } catch (ValidationException $e) {
            $this->assertSame(3, DB::table('naik_kapal')->count());
        }

        $controller->destroyVoyageSp18jb26(Request::create('/cleanup', 'DELETE', ['confirmation' => 'SP18JB26']));

        $this->assertSame(0, DB::table('naik_kapal')->where('no_voyage', 'SP18JB26')->count());
        $this->assertSame(1, DB::table('naik_kapal')->where('no_voyage', 'OTHER')->count());
    }
}
