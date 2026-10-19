<?php

namespace App\Http\Controllers;

use App\Models\ArmadaUnavailability;
use App\Models\Mobil;
use Illuminate\Http\Request;

class ArmadaAvailabilityController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'mobil_id' => ['required', 'integer', 'exists:mobils,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:255'],
        ]);
        $mobil = Mobil::findOrFail($data['mobil_id']);
        $this->checkVehicleAccess($request, $mobil);
        ArmadaUnavailability::create($data + ['created_by' => $request->user()?->id]);

        return back()->with('success', 'Periode armada tidak tersedia berhasil dicatat.');
    }

    public function destroy(Request $request, ArmadaUnavailability $availability)
    {
        $this->checkVehicleAccess($request, $availability->mobil);
        $availability->delete();

        return back()->with('success', 'Periode tidak tersedia berhasil dihapus.');
    }

    public function update(Request $request, ArmadaUnavailability $availability)
    {
        $this->checkVehicleAccess($request, $availability->mobil);
        $data = $request->validate([
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:255'],
        ]);
        $availability->update($data);

        return back()->with('success', 'Periode tidak tersedia berhasil diperbarui.');
    }

    private function checkVehicleAccess(Request $request, Mobil $mobil): void
    {
        abort_if(trim((string) $mobil->imei_gps) === '', 404);
        abort_if($request->user()?->karyawan?->cabang === 'BTM' && $mobil->lokasi !== 'BTM', 403);
    }
}
