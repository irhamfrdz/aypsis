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
        $manifestIds = $request->input('manifest_ids', []);
        if ($request->has('manifest_id')) {
            $manifestIds[] = $request->input('manifest_id');
        }
        $request->merge(['manifest_ids' => $manifestIds]);

        $data = $request->validate([
            'manifest_ids' => 'required|array|min:1',
            'manifest_ids.*' => 'exists:manifests,id',
            'bay' => 'nullable|string',
            'row' => 'nullable|string',
            'tier' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        foreach ($data['manifest_ids'] as $mId) {
            $stowagePlan = StowagePlan::updateOrCreate(
                ['manifest_id' => $mId],
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
        }

        return response()->json([
            'success' => true,
            'message' => 'Stowage plan updated successfully',
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

    public function cancel(Request $request)
    {
        $data = $request->validate([
            'bay' => 'required|string',
            'row' => 'required|string',
            'tier' => 'required|string',
            'nama_kapal' => 'required|string',
            'no_voyage' => 'nullable|string',
        ]);

        $plansQuery = StowagePlan::where('bay', $data['bay'])
            ->where('row', $data['row'])
            ->where('tier', $data['tier'])
            ->whereHas('manifest', function($q) use ($data) {
                $q->where('nama_kapal', $data['nama_kapal']);
                if (!empty($data['no_voyage'])) {
                    $q->where('no_voyage', $data['no_voyage']);
                }
            });

        $plansQuery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stowage plan canceled successfully',
        ]);
    }
}
