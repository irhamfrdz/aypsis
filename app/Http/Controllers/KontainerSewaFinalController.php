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
use App\Models\BtmSewaPranota;
use Illuminate\Support\Facades\DB;

class KontainerSewaFinalController extends Controller
{
    private function toExcelSerial($date)
    {
        if (!$date) return "0";
        try {
            $dt = \Carbon\Carbon::parse($date);
            $baseDate = \Carbon\Carbon::create(1899, 12, 30);
            return (int)$dt->diffInDays($baseDate);
        } catch (\Exception $e) {
            return "0";
        }
    }

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
            'cart' => BtmSewaAudit::whereNull('pranota_id')->get()->map(fn($i) => [
                'id' => $i->id,
                'idp' => $i->transaction_id . '-' . $i->period_name,
                'unit' => $i->unit_number,
                'masa' => $i->period_name,
                'aypsis' => (float)$i->aypsis_nominal,
                'vendorBill' => (float)$i->vendor_nominal,
                'note' => $i->note
            ]),
            'p' => BtmSewaPranota::with('vendor')->latest()->get()->map(fn($i) => [
                'id' => $i->id,
                'nomor' => $i->nomor,
                'vendor' => $i->vendor->name ?? '',
                'no_inv' => $i->no_invoice,
                'tgl_inv' => $i->tgl_invoice,
                'total' => (float)$i->grand_total,
                'status' => $i->status
            ]),
            'audits_map' => BtmSewaAudit::with('transaction')->whereNotNull('pranota_id')->get()->map(function($i) {
                // Use stored transaction_key directly if available (most reliable)
                if ($i->transaction_key) {
                    return $i->transaction_key . '-' . $i->period_name;
                }
                // Fallback: reconstruct from transaction record
                $x = $i->transaction;
                if (!$x) {
                    $x = \App\Models\BtmSewaTransaction::where('unit_number', $i->unit_number)->orderBy('date_in', 'desc')->first();
                }
                $keyTrx = $x ? ($x->unit_number . $this->toExcelSerial($x->date_in)) : $i->unit_number; 
                return $keyTrx . '-' . $i->period_name;
            })->toArray()
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

    public function submitPranota(Request $request)
    {
        $vName = $request->vendor;
        $noInv = $request->no_invoice;
        $tglInv = $request->tgl_invoice;
        $cartData = $request->cart;

        if (empty($cartData)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong']);
        }

        $vendor = BtmSewaVendor::firstOrCreate(['name' => $vName]);
        if (!$vendor) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat/menemukan vendor: ' . $vName]);
        }

        try {
            return DB::transaction(function() use ($vendor, $noInv, $tglInv, $cartData) {
                // Generate Nomor
                $last = BtmSewaPranota::where('nomor', 'like', 'PTS-BTM-' . date('Y') . '-%')->latest()->first();
                $num = 1;
                if ($last) {
                    $parts = explode('-', $last->nomor);
                    $num = (int)end($parts) + 1;
                }
                $nomor = 'PTS-BTM-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);

                $totalAypsis = collect($cartData)->sum('aypsis');
                $totalVendor = collect($cartData)->sum('vendorBill');
                $dpp = $totalVendor;
                $ppn = round($dpp * 0.11);
                $pph = round($dpp * 0.02);
                $grand = $dpp + $ppn - $pph;

                $pranota = BtmSewaPranota::create([
                    'nomor' => $nomor,
                    'vendor_id' => $vendor->id,
                    'no_invoice' => $noInv,
                    'tgl_invoice' => $tglInv,
                    'total_aypsis' => $totalAypsis,
                    'total_vendor_bill' => $totalVendor,
                    'dpp' => $dpp,
                    'ppn' => $ppn,
                    'pph' => $pph,
                    'grand_total' => $grand,
                    'status' => 'PENDING'
                ]);

                foreach ($cartData as $c) {
                    $idp = $c['idp'] ?? '';
                    $masa = $c['masa'] ?? '';

                    // Extract transaction_key (the idInduk prefix) from idp by stripping the period_name suffix
                    $transactionKey = (strlen($idp) > strlen($masa) + 1)
                        ? substr($idp, 0, strlen($idp) - strlen($masa) - 1)
                        : null;

                    // Find the correct DB transaction using the excel serial embedded in transaction_key
                    $transId = null;
                    if ($transactionKey) {
                        $unitNumber = $c['unit'] ?? '';
                        $serialStr = substr($transactionKey, strlen($unitNumber));
                        if (is_numeric($serialStr)) {
                            $excelSerial = (int)$serialStr;
                            $base = \Carbon\Carbon::create(1899, 12, 30);
                            $transactions = BtmSewaTransaction::where('unit_number', $unitNumber)->get();
                            foreach ($transactions as $trx) {
                                if ((int)\Carbon\Carbon::parse($trx->date_in)->diffInDays($base) === $excelSerial) {
                                    $transId = $trx->id;
                                    break;
                                }
                            }
                        }
                    }

                    // Fallback: latest transaction for this unit
                    if (!$transId) {
                        $transId = BtmSewaTransaction::where('unit_number', $c['unit'])->latest()->first()?->id;
                    }

                    BtmSewaAudit::updateOrCreate(
                        ['transaction_id' => $transId, 'period_name' => $c['masa'], 'unit_number' => $c['unit']],
                        [
                            'transaction_key' => $transactionKey,
                            'aypsis_nominal' => $c['aypsis'],
                            'vendor_nominal' => $c['vendorBill'],
                            'note' => $c['note'] ?? null,
                            'pranota_id' => $pranota->id,
                            'is_approved' => true
                        ]
                    );
                }

                return response()->json(['success' => true, 'nomor' => $nomor]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function printPranota($id)
    {
        $pranota = BtmSewaPranota::with(['vendor', 'audits.transaction'])->findOrFail($id);
        return view('kontainer_sewa_final.print', compact('pranota'));
    }

    public function showPranota($id)
    {
        $pranota = BtmSewaPranota::with(['vendor', 'audits.transaction'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pranota->id,
                'nomor' => $pranota->nomor,
                'vendor' => $pranota->vendor->name ?? '',
                'no_invoice' => $pranota->no_invoice,
                'tgl_invoice' => $pranota->tgl_invoice,
                'status' => $pranota->status,
                'audits' => $pranota->audits->map(fn($a) => [
                    'id' => $a->id,
                    'unit' => $a->unit_number,
                    'masa' => $a->period_name,
                    'aypsis' => (float)$a->aypsis_nominal,
                    'vendorBill' => (float)$a->vendor_nominal,
                    'note' => $a->note
                ])
            ]
        ]);
    }

    public function updatePranota(Request $request, $id)
    {
        $pranota = BtmSewaPranota::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            $pranota->update([
                'no_invoice' => $request->no_invoice,
                'tgl_invoice' => $request->tgl_invoice,
                'status' => $request->status ?? $pranota->status
            ]);
            
            // If items are provided, we could update them too, but for simplicity let's stick to header first.
            // Actually, if we allow deleting items from pranota:
            if ($request->has('remove_audit_ids')) {
                BtmSewaAudit::whereIn('id', $request->remove_audit_ids)
                    ->where('pranota_id', $id)
                    ->update(['pranota_id' => null, 'is_approved' => false]);
            }
            
            // Recalculate totals
            $audits = BtmSewaAudit::where('pranota_id', $id)->get();
            $totalAypsis = $audits->sum('aypsis_nominal');
            $totalVendor = $audits->sum('vendor_nominal');
            $dpp = $totalVendor;
            $ppn = round($dpp * 0.11);
            $pph = round($dpp * 0.02);
            $grand = $dpp + $ppn - $pph;
            
            $pranota->update([
                'total_aypsis' => $totalAypsis,
                'total_vendor_bill' => $totalVendor,
                'dpp' => $dpp,
                'ppn' => $ppn,
                'pph' => $pph,
                'grand_total' => $grand,
            ]);
            
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroyPranota($id)
    {
        $pranota = BtmSewaPranota::findOrFail($id);
        try {
            DB::beginTransaction();
            // Unlock all audits
            BtmSewaAudit::where('pranota_id', $id)->update(['pranota_id' => null, 'is_approved' => false]);
            $pranota->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
