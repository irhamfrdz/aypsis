<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BtmSewaVendor;
use App\Models\BtmSewaType;
use App\Models\BtmSewaSize;
use App\Models\BtmSewaUnit;
use App\Models\BtmSewaRate;
use App\Models\BtmSewaTransaction;
use App\Models\BtmSewaAudit;
use Illuminate\Support\Facades\DB;

class KontainerSewaFinalController extends Controller
{
    public function index()
    {
        // Fetch all data from DB
        $data = [
            'v' => BtmSewaVendor::orderBy('name')->get()->map(fn($i) => ['val' => $i->name, 'id' => $i->id, 'act' => true]),
            't' => BtmSewaType::orderBy('name')->get()->map(fn($i) => ['val' => $i->name, 'id' => $i->id, 'act' => true]),
            'z' => BtmSewaSize::orderBy('name')->get()->map(fn($i) => ['val' => $i->name, 'id' => $i->id, 'act' => true]),
            'u' => BtmSewaUnit::with(['vendor', 'type', 'size'])->get()->map(fn($i) => [
                'id' => $i->id, 
                'no' => $i->unit_number, 
                'v' => $i->vendor->name ?? '', 
                't' => $i->type->name ?? '', 
                'z' => $i->size->name ?? '', 
                'act' => true
            ]),
            'r' => BtmSewaRate::with(['vendor', 'type', 'size'])->get()->map(fn($i) => [
                'id' => $i->id, 
                'v' => $i->vendor->name ?? '', 
                't' => $i->type->name ?? '', 
                'z' => $i->size->name ?? '', 
                'rb' => (int)$i->monthly_rate, 
                'rh' => (int)$i->daily_rate, 
                'act' => true
            ]),
            'x' => BtmSewaTransaction::orderBy('date_in', 'desc')->get()->map(fn($i) => [
                'id' => $i->id, 
                'no' => $i->unit_number, 
                's' => $i->date_in->format('d/m/Y'), 
                'e' => $i->date_out ? $i->date_out->format('d/m/Y') : null, 
                'stT' => $i->billing_mode, 
                'act' => true
            ]),
            'cart' => BtmSewaAudit::all()->map(fn($i) => [
                'id' => $i->id,
                'idp' => $i->transaction_id . '-' . $i->period_name,
                'unit' => $i->unit_number,
                'masa' => $i->period_name,
                'aypsis' => (float)$i->aypsis_nominal,
                'vendorBill' => (float)$i->vendor_nominal,
                'note' => $i->note
            ])
        ];

        // Seed from JSON if DB is empty (initial setup)
        if ($data['v']->isEmpty() && $data['t']->isEmpty()) {
            $dataPath = base_path('test_sewa/AYPSIS_DATA.json');
            $initialData = file_exists($dataPath) ? file_get_contents($dataPath) : json_encode([]);
        } else {
            $initialData = json_encode($data);
        }

        return view('kontainer_sewa_final.index', compact('initialData'));
    }

    public function sync(Request $request)
    {
        $data = $request->input('data');
        if (!$data) return response()->json(['success' => false, 'message' => 'No data provided']);

        try {
            DB::beginTransaction();

            // Sync Vendors
            if (isset($data['v'])) {
                foreach ($data['v'] as $v) {
                    $name = $v['val'] ?? (is_string($v) ? $v : null);
                    if ($name) {
                        BtmSewaVendor::updateOrCreate(['id' => $v['id'] ?? null], ['name' => $name]);
                    }
                }
            }

            // Sync Types
            if (isset($data['t'])) {
                foreach ($data['t'] as $t) {
                    $name = $t['val'] ?? (is_string($t) ? $t : null);
                    if ($name) {
                        BtmSewaType::updateOrCreate(['id' => $t['id'] ?? null], ['name' => $name]);
                    }
                }
            }

            // Sync Sizes
            if (isset($data['z'])) {
                foreach ($data['z'] as $z) {
                    $name = $z['val'] ?? (is_string($z) ? $z : null);
                    if ($name) {
                        BtmSewaSize::updateOrCreate(['id' => $z['id'] ?? null], ['name' => $name]);
                    }
                }
            }

            // Pre-fetch all names for lookup
            $vendors = BtmSewaVendor::pluck('id', 'name')->toArray();
            $types = BtmSewaType::pluck('id', 'name')->toArray();
            $sizes = BtmSewaSize::pluck('id', 'name')->toArray();

            // Sync Units
            if (isset($data['u'])) {
                foreach ($data['u'] as $u) {
                    BtmSewaUnit::updateOrCreate(
                        ['id' => $u['id'] ?? null],
                        [
                            'unit_number' => $u['no'],
                            'vendor_id' => $vendors[$u['v']] ?? null,
                            'type_id' => $types[$u['t']] ?? null,
                            'size_id' => $sizes[$u['z']] ?? null,
                        ]
                    );
                }
            }

            // Sync Rates
            if (isset($data['r'])) {
                foreach ($data['r'] as $r) {
                    BtmSewaRate::updateOrCreate(
                        ['id' => $r['id'] ?? null],
                        [
                            'vendor_id' => $vendors[$r['v']] ?? null,
                            'type_id' => $types[$r['t']] ?? null,
                            'size_id' => $sizes[$r['z']] ?? null,
                            'monthly_rate' => $r['rb'],
                            'daily_rate' => $r['rh'],
                            'start_date' => date('Y-m-d'),
                        ]
                    );
                }
            }

            // Sync Transactions
            if (isset($data['x'])) {
                foreach ($data['x'] as $x) {
                    // Convert d/m/Y to Y-m-d
                    $dateIn = \DateTime::createFromFormat('d/m/Y', $x['s']);
                    $dateOut = $x['e'] ? \DateTime::createFromFormat('d/m/Y', $x['e']) : null;

                    BtmSewaTransaction::updateOrCreate(
                        ['id' => $x['id'] ?? null],
                        [
                            'unit_number' => $x['no'],
                            'date_in' => $dateIn ? $dateIn->format('Y-m-d') : null,
                            'date_out' => $dateOut ? $dateOut->format('Y-m-d') : null,
                            'billing_mode' => $x['stT'],
                        ]
                    );
                }
            }

            // Sync Cart (Audits)
            if (isset($data['cart'])) {
                foreach ($data['cart'] as $c) {
                    $parts = explode('-', $c['idp']);
                    $transId = $parts[0];
                    if (!is_numeric($transId)) {
                        // If transId is not numeric (prototype case), we might need to find by unit and date
                        // But for now, let's assume it's been synced and has an ID if it's in the cart.
                        // Actually, if it's the first sync, the JS won't have IDs.
                        // We'll skip audits that don't have a valid transaction link yet, 
                        // or handle them after transactions are all saved.
                        continue; 
                    }
                    
                    BtmSewaAudit::updateOrCreate(
                        ['transaction_id' => $transId, 'period_name' => $c['masa']],
                        [
                            'unit_number' => $c['unit'],
                            'aypsis_nominal' => $c['aypsis'],
                            'vendor_nominal' => $c['vendorBill'],
                            'note' => $c['note'] ?? null,
                        ]
                    );
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
