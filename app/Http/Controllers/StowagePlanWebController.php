<?php

namespace App\Http\Controllers;

use App\Models\StowagePlan;
use App\Models\Manifest;
use App\Models\MasterKapal;
use Illuminate\Http\Request;

class StowagePlanWebController extends Controller
{
    public function index()
    {
        $ships = Manifest::select('nama_kapal')
            ->distinct()
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '!=', '')
            ->orderBy('nama_kapal')
            ->get();
            
        return view('stowage-plan.index', compact('ships'));
    }

    public function show($shipName, Request $request)
    {
        $voyage = $request->query('voyage');
        $shipName = urldecode($shipName);

        $plans = StowagePlan::whereHas('manifest', function($q) use ($shipName, $voyage) {
            $q->where('nama_kapal', $shipName);
            if ($voyage) {
                $q->where('no_voyage', $voyage);
            }
        })->with('manifest')->get();

        $manifestQuery = Manifest::where('nama_kapal', $shipName)
            ->whereDoesntHave('stowagePlan');
            
        if ($voyage) {
            $manifestQuery->where('no_voyage', $voyage);
        }
        $manifestsWithoutPlan = $manifestQuery->get()->groupBy(function($item) {
            return $item->nomor_kontainer ?: 'UNASSIGNED_' . $item->id;
        });

        $masterKapal = MasterKapal::where('nama_kapal', $shipName)->first();
        $stowageBays = $masterKapal && $masterKapal->stowage_bays 
            ? array_filter(array_map('trim', explode(',', $masterKapal->stowage_bays))) 
            : ['01','03','05','07','09','11','13','15','17','19','21'];

        $stowageRows = $masterKapal && $masterKapal->stowage_rows 
            ? array_filter(array_map('trim', explode(',', $masterKapal->stowage_rows))) 
            : [];
            
        $stowageTiers = $masterKapal && $masterKapal->stowage_tiers 
            ? array_filter(array_map('trim', explode(',', $masterKapal->stowage_tiers))) 
            : [];

        $disabledSlots = $masterKapal && $masterKapal->disabled_slots
            ? $masterKapal->disabled_slots
            : [];

        sort($stowageBays);
        sort($stowageRows);
        sort($stowageTiers);

        return view('stowage-plan.show', compact('plans', 'manifestsWithoutPlan', 'shipName', 'voyage', 'stowageBays', 'stowageRows', 'stowageTiers', 'disabledSlots'));
    }
}
