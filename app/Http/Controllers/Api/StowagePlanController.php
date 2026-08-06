<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StowagePlan;
use App\Models\Manifest;
use Illuminate\Http\Request;

class StowagePlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = StowagePlan::with(['manifest', 'createdBy'])->get();
        return response()->json($plans);
    }

    public function getShips()
    {
        $ships = Manifest::select('nama_kapal')
            ->distinct()
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '!=', '')
            ->get()
            ->pluck('nama_kapal');
            
        return response()->json($ships);
    }
    
    public function getByShip(Request $request)
    {
        $ship = $request->input('nama_kapal');
        $plans = StowagePlan::whereHas('manifest', function($q) use ($ship) {
            $q->where('nama_kapal', $ship);
        })->with('manifest')->get();
        
        return response()->json($plans);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'manifest_id' => 'required|exists:manifests,id',
            'bay' => 'nullable|string',
            'row' => 'nullable|string',
            'tier' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $stowagePlan = StowagePlan::updateOrCreate(
            ['manifest_id' => $data['manifest_id']],
            [
                'bay' => $data['bay'] ?? null,
                'row' => $data['row'] ?? null,
                'tier' => $data['tier'] ?? null,
                'notes' => $data['notes'] ?? null,
                'updated_by' => auth()->id() ?? null,
            ]
        );
        
        if ($stowagePlan->wasRecentlyCreated) {
            $stowagePlan->created_by = auth()->id() ?? null;
            $stowagePlan->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Stowage plan updated successfully',
            'data' => $stowagePlan
        ]);
    }
    
    public function getManifestsWithoutPlan(Request $request)
    {
        $ship = $request->input('nama_kapal');
        $manifests = Manifest::whereDoesntHave('stowagePlan');
        
        if ($ship) {
            $manifests->where('nama_kapal', $ship);
        }
        
        return response()->json($manifests->get());
    }
}
