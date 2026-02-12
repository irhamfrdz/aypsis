<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAmprahanUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_amprahan_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_amprahan_id')->constrained()->onDelete('cascade');
            $table->foreignId('penerima_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal_pengambilan');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_amprahan_usages');
    }
}
