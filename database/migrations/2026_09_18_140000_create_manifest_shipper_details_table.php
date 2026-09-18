<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manifest_shipper_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manifest_id')->index();
            $table->unsignedBigInteger('shipper_id')->nullable()->index();
            $table->string('nomor_tanda_terima')->nullable();
            $table->text('nama_barang')->nullable();
            $table->string('pengirim')->nullable();
            $table->text('alamat_pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('notify_party')->nullable();
            $table->text('alamat_notify_party')->nullable();
            $table->decimal('tonnage', 15, 3)->nullable();
            $table->decimal('tonnage_perincian', 15, 3)->nullable();
            $table->decimal('volume', 15, 3)->nullable();
            $table->decimal('volume_perincian', 15, 3)->nullable();
            $table->string('satuan')->nullable();
            $table->string('term')->nullable();
            $table->integer('kuantitas')->nullable();
            $table->string('hs_code')->nullable();
            $table->date('penerimaan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // Preserve the existing first shipper for each FCL Booking as a detail row.
        DB::table('manifests')
            ->whereRaw("UPPER(COALESCE(tipe_kontainer, '')) = 'FCL BOOKING'")
            ->where(function ($query) {
                $query->whereNotNull('shipper_id')->orWhereNotNull('pengirim')->orWhereNotNull('penerima');
            })
            ->orderBy('id')
            ->each(function ($manifest) {
                DB::table('manifest_shipper_details')->insert([
                    'manifest_id' => $manifest->id, 'shipper_id' => $manifest->shipper_id,
                    'nomor_tanda_terima' => $manifest->nomor_tanda_terima, 'nama_barang' => $manifest->nama_barang,
                    'pengirim' => $manifest->pengirim, 'alamat_pengirim' => $manifest->alamat_pengirim,
                    'penerima' => $manifest->penerima, 'alamat_penerima' => $manifest->alamat_penerima,
                    'alamat_pengiriman' => $manifest->alamat_pengiriman, 'contact_person' => $manifest->contact_person,
                    'notify_party' => $manifest->notify_party, 'alamat_notify_party' => $manifest->alamat_notify_party,
                    'tonnage' => $manifest->tonnage, 'tonnage_perincian' => $manifest->tonnage_perincian,
                    'volume' => $manifest->volume, 'volume_perincian' => $manifest->volume_perincian,
                    'satuan' => $manifest->satuan, 'term' => $manifest->term, 'kuantitas' => $manifest->kuantitas,
                    'hs_code' => $manifest->hs_code, 'penerimaan' => $manifest->penerimaan,
                    'created_by' => $manifest->created_by, 'updated_by' => $manifest->updated_by,
                    'created_at' => $manifest->created_at, 'updated_at' => $manifest->updated_at,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('manifest_shipper_details');
    }
};
