<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('biaya_kapal_nota_returs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->onDelete('cascade');
            $table->string('no_invoice')->nullable();
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('vendor')->nullable();
            $table->string('penerima')->nullable();
            $table->string('rekening')->nullable();
            $table->json('kontainer_ids')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('biaya_materai', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->text('notes_adjustment')->nullable();
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->timestamps();
        });

        // Seed classification code
        DB::table('klasifikasi_biayas')->insertOrIgnore([
            'kode' => 'KB052',
            'nama' => 'Nota Retur',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_nota_returs');
        
        DB::table('klasifikasi_biayas')->where('kode', 'KB052')->delete();
    }
};
