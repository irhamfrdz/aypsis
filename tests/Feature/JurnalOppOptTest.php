<?php

namespace Tests\Feature;

use App\Http\Controllers\JurnalOppOptController;
use App\Models\JurnalOppOpt;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class JurnalOppOptTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Model::unsetEventDispatcher();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('jurnal_opp_opts', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_referensi')->nullable();
            $table->text('keterangan');
            $table->decimal('saldo', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->decimal('saldo_berjalan', 18, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('jurnal_opp_opts');
        Schema::dropIfExists('users');
        Model::setEventDispatcher(app('events'));

        parent::tearDown();
    }

    public function test_saldo_berjalan_is_recalculated_when_an_older_entry_changes(): void
    {
        $user = User::query()->create([
            'username' => 'jurnal-tester',
            'password' => bcrypt('password'),
        ]);
        Auth::login($user);
        $controller = app(JurnalOppOptController::class);

        $controller->store($this->request([
            'tanggal' => '2026-10-02',
            'nomor_referensi' => 'KR-001',
            'keterangan' => 'Kredit pertama',
            'saldo' => 0,
            'kredit' => 250,
        ]));
        $controller->store($this->request([
            'tanggal' => '2026-10-01',
            'nomor_referensi' => 'SA-001',
            'keterangan' => 'Saldo awal',
            'saldo' => 1000,
            'kredit' => 0,
        ]));

        $entries = JurnalOppOpt::query()->orderBy('tanggal')->orderBy('id')->get();
        $this->assertSame('1000.00', $entries[0]->saldo_berjalan);
        $this->assertSame('750.00', $entries[1]->saldo_berjalan);

        $controller->update($this->request([
            'tanggal' => '2026-10-01',
            'nomor_referensi' => 'SA-001',
            'keterangan' => 'Saldo awal diperbarui',
            'saldo' => 1200,
            'kredit' => 0,
        ]), $entries[0]);

        $this->assertSame('1200.00', $entries[0]->fresh()->saldo_berjalan);
        $this->assertSame('950.00', $entries[1]->fresh()->saldo_berjalan);
    }

    public function test_journal_table_is_independent_from_biaya_kapal(): void
    {
        $this->assertFalse(Schema::hasColumn('jurnal_opp_opts', 'biaya_kapal_id'));
        $this->assertFalse(Schema::hasColumn('jurnal_opp_opts', 'biaya_kapal_opp_opt_id'));
    }

    private function request(array $data): Request
    {
        return Request::create('/', 'POST', $data);
    }
}
