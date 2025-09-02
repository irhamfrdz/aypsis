<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kontainer;
use App\Models\Permohonan;

class SupirCheckpointController extends Controller
{
    // Menampilkan form checkpoint supir
    public function create(Permohonan $permohonan)
    {
        // Default: no kontainerList
        $kontainerList = null;

        // If this permohonan is a pickup of sewa (tarik / pengambilan), prefer to show
        // kontainers that were approved and are linked to the same tagihan group.
        $kegiatan = strtolower($permohonan->kegiatan ?? '');

        if (in_array($kegiatan, ['tarik kontainer sewa', 'pengambilan', 'pengambilan_sewa'])) {
            // find permohonan group by vendor + tanggal_memo (same group used for tagihan)
            $vendor = $permohonan->vendor_perusahaan;
            $tanggal = $permohonan->tanggal_memo ? date('Y-m-d', strtotime($permohonan->tanggal_memo)) : null;

            if ($vendor && $tanggal) {
                $permIds = \DB::table('permohonans')
                    ->where('vendor_perusahaan', $vendor)
                    ->whereDate('tanggal_memo', $tanggal)
                    ->pluck('id')
                    ->toArray();

                if (!empty($permIds)) {
                    // select kontainers that are linked via permohonan_kontainers to these permohonan ids
                    $kontainerList = \DB::table('permohonan_kontainers')
                        ->join('kontainers', 'permohonan_kontainers.kontainer_id', '=', 'kontainers.id')
                        ->whereIn('permohonan_kontainers.permohonan_id', $permIds)
                        ->select('kontainers.*')
                        ->distinct()
                        ->get();
                }
            }
        } else {
            // fallback: provide full list (for vendors that require manual input)
            $kontainerList = Kontainer::all();
        }

        return view('supir.checkpoint-create', compact('permohonan', 'kontainerList'));
    }

    // Fungsi lain sesuai kebutuhan
}
