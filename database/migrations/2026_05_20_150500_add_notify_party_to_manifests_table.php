<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotifyPartyToManifestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            if (! Schema::hasColumn('manifests', 'notify_party')) {
                $table->string('notify_party')->nullable()->after('alamat_penerima');
            }
            if (! Schema::hasColumn('manifests', 'alamat_notify_party')) {
                $afterCol = Schema::hasColumn('manifests', 'notify_party') ? 'notify_party' : 'alamat_penerima';
                $table->text('alamat_notify_party')->nullable()->after($afterCol);
            }
        });

        // Sync/populate existing manifests data
        try {
            $manifests = \App\Models\Manifest::all();
            foreach ($manifests as $manifest) {
                $related = $this->getRelatedNotifyParty($manifest);
                if ($related) {
                    $manifest->notify_party = $related['notify_party'];
                    $manifest->alamat_notify_party = $related['alamat_notify_party'];
                    $manifest->save();
                }
            }
        } catch (\Exception $e) {
            // Log or ignore if models aren't fully ready yet, but they should be
            Log::warning('Could not sync notify party for manifests: '.$e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            if (Schema::hasColumn('manifests', 'notify_party')) {
                $table->dropColumn('notify_party');
            }
            if (Schema::hasColumn('manifests', 'alamat_notify_party')) {
                $table->dropColumn('alamat_notify_party');
            }
        });
    }

    /**
     * Resolves notify party from related tables.
     */
    private function getRelatedNotifyParty($manifest)
    {
        $ttNo = $manifest->nomor_tanda_terima;

        // 1. Check FCL Tanda Terima via prospek
        if ($manifest->prospek && $manifest->prospek->tandaTerima) {
            $tandaTerima = $manifest->prospek->tandaTerima;
            if (! empty($tandaTerima->notify_party)) {
                return [
                    'notify_party' => $tandaTerima->notify_party,
                    'alamat_notify_party' => $tandaTerima->alamat_notify_party,
                ];
            }
        }

        // 2. Check FCL Tanda Terima by nomor_tanda_terima
        if ($ttNo) {
            $fcl = \App\Models\TandaTerima::where('no_surat_jalan', $ttNo)->first();
            if ($fcl && ! empty($fcl->notify_party)) {
                return [
                    'notify_party' => $fcl->notify_party,
                    'alamat_notify_party' => $fcl->alamat_notify_party,
                ];
            }
        }

        // 3. Check Tanda Terima Tanpa Surat Jalan
        if ($ttNo) {
            $ttsj = \App\Models\TandaTerimaTanpaSuratJalan::where('no_tanda_terima', $ttNo)
                ->orWhere('nomor_tanda_terima', $ttNo)
                ->first();
            if ($ttsj && ! empty($ttsj->notify_party)) {
                return [
                    'notify_party' => $ttsj->notify_party,
                    'alamat_notify_party' => $ttsj->alamat_notify_party,
                ];
            }
        }

        // 4. Check LCL Tanda Terima
        if ($ttNo) {
            $lcl = \App\Models\TandaTerimaLcl::where('nomor_tanda_terima', $ttNo)->first();
            if ($lcl && ! empty($lcl->notify_party)) {
                return [
                    'notify_party' => $lcl->notify_party,
                    'alamat_notify_party' => $lcl->alamat_notify_party,
                ];
            }
        }

        // 5. Check Batam equivalents
        if ($ttNo && class_exists(\App\Models\TandaTerimaBatam::class)) {
            $fclBatam = \App\Models\TandaTerimaBatam::where('no_surat_jalan', $ttNo)->first();
            if ($fclBatam && ! empty($fclBatam->notify_party)) {
                return [
                    'notify_party' => $fclBatam->notify_party,
                    'alamat_notify_party' => $fclBatam->alamat_notify_party,
                ];
            }
        }

        if ($ttNo && class_exists(\App\Models\TandaTerimaTanpaSuratJalanBatam::class)) {
            $ttsjBatam = \App\Models\TandaTerimaTanpaSuratJalanBatam::where('no_tanda_terima', $ttNo)
                ->orWhere('nomor_tanda_terima', $ttNo)
                ->first();
            if ($ttsjBatam && ! empty($ttsjBatam->notify_party)) {
                return [
                    'notify_party' => $ttsjBatam->notify_party,
                    'alamat_notify_party' => $ttsjBatam->alamat_notify_party,
                ];
            }
        }

        if ($ttNo && class_exists(\App\Models\TandaTerimaLclBatam::class)) {
            $lclBatam = \App\Models\TandaTerimaLclBatam::where('nomor_tanda_terima', $ttNo)->first();
            if ($lclBatam && ! empty($lclBatam->notify_party)) {
                return [
                    'notify_party' => $lclBatam->notify_party,
                    'alamat_notify_party' => $lclBatam->alamat_notify_party,
                ];
            }
        }

        return null;
    }
}
