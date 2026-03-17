<?php

namespace App\Http\Controllers;

use App\Models\BtmSewaRate;
use App\Models\BtmSewaSize;
use App\Models\BtmSewaTransaction;
use App\Models\BtmSewaType;
use App\Models\BtmSewaUnit;
use App\Models\BtmSewaVendor;
use Illuminate\Http\Request;

class BtmKontainerSewaController extends Controller
{
    public function index()
    {
        return view('btm-kontainer-sewa.index');
    }

    public function snapshot()
    {
        $vendors = BtmSewaVendor::orderBy('name')->get(['id', 'name']);
        $types = BtmSewaType::orderBy('name')->get(['id', 'name']);
        $sizes = BtmSewaSize::orderBy('name')->get(['id', 'name']);

        $units = BtmSewaUnit::query()
            ->join('btm_sewa_vendors', 'btm_sewa_units.vendor_id', '=', 'btm_sewa_vendors.id')
            ->join('btm_sewa_types', 'btm_sewa_units.type_id', '=', 'btm_sewa_types.id')
            ->join('btm_sewa_sizes', 'btm_sewa_units.size_id', '=', 'btm_sewa_sizes.id')
            ->orderBy('btm_sewa_units.unit_number')
            ->get([
                'btm_sewa_units.id',
                'btm_sewa_units.unit_number',
                'btm_sewa_units.vendor_id',
                'btm_sewa_units.type_id',
                'btm_sewa_units.size_id',
                'btm_sewa_vendors.name as vendor_name',
                'btm_sewa_types.name as type_name',
                'btm_sewa_sizes.name as size_name',
            ]);

        $rates = BtmSewaRate::query()
            ->join('btm_sewa_vendors', 'btm_sewa_rates.vendor_id', '=', 'btm_sewa_vendors.id')
            ->join('btm_sewa_types', 'btm_sewa_rates.type_id', '=', 'btm_sewa_types.id')
            ->join('btm_sewa_sizes', 'btm_sewa_rates.size_id', '=', 'btm_sewa_sizes.id')
            ->orderByDesc('btm_sewa_rates.start_date')
            ->get([
                'btm_sewa_rates.id',
                'btm_sewa_rates.vendor_id',
                'btm_sewa_rates.type_id',
                'btm_sewa_rates.size_id',
                'btm_sewa_rates.monthly_rate',
                'btm_sewa_rates.daily_rate',
                'btm_sewa_rates.start_date',
                'btm_sewa_vendors.name as vendor_name',
                'btm_sewa_types.name as type_name',
                'btm_sewa_sizes.name as size_name',
            ]);

        $transactions = BtmSewaTransaction::orderByDesc('date_in')->get([
            'id',
            'unit_number',
            'date_in',
            'date_out',
            'billing_mode',
        ]);

        return response()->json([
            'vendors' => $vendors,
            'types' => $types,
            'sizes' => $sizes,
            'units' => $units,
            'rates' => $rates,
            'transactions' => $transactions,
        ]);
    }

    public function storeVendor(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $vendor = BtmSewaVendor::firstOrCreate([
            'name' => strtoupper(trim($data['name'])),
        ]);

        return response()->json(['success' => true, 'item' => $vendor]);
    }

    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type = BtmSewaType::firstOrCreate([
            'name' => strtoupper(trim($data['name'])),
        ]);

        return response()->json(['success' => true, 'item' => $type]);
    }

    public function storeSize(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $size = BtmSewaSize::firstOrCreate([
            'name' => strtoupper(trim($data['name'])),
        ]);

        return response()->json(['success' => true, 'item' => $size]);
    }

    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer|exists:btm_sewa_units,id',
            'unit_number' => 'required|string|max:255',
            'vendor_id' => 'required|integer|exists:btm_sewa_vendors,id',
            'type_id' => 'required|integer|exists:btm_sewa_types,id',
            'size_id' => 'required|integer|exists:btm_sewa_sizes,id',
        ]);

        $payload = [
            'unit_number' => strtoupper(trim($data['unit_number'])),
            'vendor_id' => $data['vendor_id'],
            'type_id' => $data['type_id'],
            'size_id' => $data['size_id'],
        ];

        if (!empty($data['id'])) {
            $unit = BtmSewaUnit::findOrFail($data['id']);
            $unit->update($payload);
        } else {
            $unit = BtmSewaUnit::create($payload);
        }

        return response()->json(['success' => true, 'item' => $unit]);
    }

    public function storeRate(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|integer|exists:btm_sewa_vendors,id',
            'type_id' => 'required|integer|exists:btm_sewa_types,id',
            'size_id' => 'required|integer|exists:btm_sewa_sizes,id',
            'monthly_rate' => 'required|numeric|min:0',
            'daily_rate' => 'required|numeric|min:0',
            'start_date' => 'required|date',
        ]);

        $rate = BtmSewaRate::create($data);

        return response()->json(['success' => true, 'item' => $rate]);
    }

    public function storeTransaction(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer|exists:btm_sewa_transactions,id',
            'unit_number' => 'required|string|max:255',
            'date_in' => 'required|date',
            'date_out' => 'nullable|date|after_or_equal:date_in',
            'billing_mode' => 'required|in:B,H',
        ]);

        $payload = [
            'unit_number' => strtoupper(trim($data['unit_number'])),
            'date_in' => $data['date_in'],
            'date_out' => $data['date_out'] ?? null,
            'billing_mode' => $data['billing_mode'],
        ];

        if (!empty($data['id'])) {
            $trx = BtmSewaTransaction::findOrFail($data['id']);
            $trx->update($payload);
        } else {
            $trx = BtmSewaTransaction::create($payload);
        }

        return response()->json(['success' => true, 'item' => $trx]);
    }

    public function importUnits(Request $request)
    {
        $data = $request->validate([
            'rows' => 'required|string',
        ]);

        $lines = preg_split('/\r\n|\r|\n/', trim($data['rows']));
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 4) {
                continue;
            }

            [$unitNumber, $vendorName, $typeName, $sizeName] = $parts;

            $vendor = BtmSewaVendor::firstOrCreate(['name' => strtoupper($vendorName)]);
            $type = BtmSewaType::firstOrCreate(['name' => strtoupper($typeName)]);
            $size = BtmSewaSize::firstOrCreate(['name' => strtoupper($sizeName)]);

            BtmSewaUnit::updateOrCreate(
                ['unit_number' => strtoupper($unitNumber)],
                [
                    'vendor_id' => $vendor->id,
                    'type_id' => $type->id,
                    'size_id' => $size->id,
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function importTransactions(Request $request)
    {
        $data = $request->validate([
            'rows' => 'required|string',
        ]);

        $lines = preg_split('/\r\n|\r|\n/', trim($data['rows']));
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 2) {
                continue;
            }

            $unit = strtoupper($parts[0]);
            $dateIn = $this->normalizeDate($parts[1]);
            $dateOut = isset($parts[2]) ? $this->normalizeDate($parts[2]) : null;
            $mode = isset($parts[3]) ? strtoupper($parts[3]) : 'B';
            if (!in_array($mode, ['B', 'H'], true)) {
                $mode = 'B';
            }

            if (!$dateIn) {
                continue;
            }

            BtmSewaTransaction::create([
                'unit_number' => $unit,
                'date_in' => $dateIn,
                'date_out' => $dateOut,
                'billing_mode' => $mode,
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function normalizeDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
            return sprintf('%04d-%02d-%02d', (int) $matches[3], (int) $matches[2], (int) $matches[1]);
        }

        return null;
    }
}
