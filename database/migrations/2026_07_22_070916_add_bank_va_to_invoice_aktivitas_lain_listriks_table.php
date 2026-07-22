<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_aktivitas_lain_listrik', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_id')->nullable()->after('penerima');
            $table->string('virtual_account')->nullable()->after('bank_id');
            
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_aktivitas_lain_listrik', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['bank_id', 'virtual_account']);
        });
    }
};
