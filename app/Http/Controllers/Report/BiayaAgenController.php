<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BiayaAgenController extends Controller
{
    public function index()
    {
        $kapals = DB::table('biaya_kapal_air as rincian')
            ->join('biaya_kapals as invoice', 'invoice.id', '=', 'rincian.biaya_kapal_id')
            ->whereNull('invoice.deleted_at')
            ->whereNotNull('rincian.kapal')
            ->whereRaw("TRIM(rincian.kapal) <> ''")
            ->distinct()
            ->pluck('rincian.kapal')
            ->map(fn ($kapal) => trim($kapal))
            ->unique(fn ($kapal) => mb_strtolower($kapal))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $jenisBiayas = DB::table('biaya_kapal_air as rincian')
            ->join('biaya_kapals as invoice', 'invoice.id', '=', 'rincian.biaya_kapal_id')
            ->whereNull('invoice.deleted_at')
            ->whereNotNull('rincian.type_keterangan')
            ->whereRaw("TRIM(rincian.type_keterangan) <> ''")
            ->distinct()
            ->pluck('rincian.type_keterangan')
            ->map(fn ($jenisBiaya) => trim($jenisBiaya))
            ->unique(fn ($jenisBiaya) => mb_strtolower($jenisBiaya))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return view('report.biaya-agen.index', compact('kapals', 'jenisBiayas'));
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mode' => ['required', Rule::in(['kapal', 'tanggal', 'gabungan'])],
            'kapal' => ['required_if:mode,kapal,gabungan', 'nullable', 'string', 'max:255'],
            'start_date' => ['required_if:mode,tanggal,gabungan', 'nullable', 'date'],
            'end_date' => ['required_if:mode,tanggal,gabungan', 'nullable', 'date', 'after_or_equal:start_date'],
            'jenis_biaya' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            throw (new ValidationException($validator))->redirectTo(route('report.biaya-agen.index'));
        }

        $validated = $validator->validated();

        $query = DB::table('biaya_kapal_air as rincian')
            ->join('biaya_kapals as invoice', 'invoice.id', '=', 'rincian.biaya_kapal_id')
            ->whereNull('invoice.deleted_at')
            ->select([
                'rincian.*',
                'invoice.nomor_invoice',
                'invoice.tanggal as tanggal_biaya_kapal',
            ]);

        if (in_array($validated['mode'], ['kapal', 'gabungan'], true)) {
            $query->whereRaw('LOWER(TRIM(rincian.kapal)) = ?', [mb_strtolower(trim($validated['kapal']))]);
        }

        if (in_array($validated['mode'], ['tanggal', 'gabungan'], true)) {
            $query->whereBetween(DB::raw('COALESCE(rincian.tanggal_invoice_vendor, invoice.tanggal)'), [
                $validated['start_date'],
                $validated['end_date'],
            ]);
        }

        if (! empty($validated['jenis_biaya'])) {
            $query->whereRaw('LOWER(TRIM(rincian.type_keterangan)) = ?', [mb_strtolower(trim($validated['jenis_biaya']))]);
        }

        $rows = $query
            ->orderByRaw('COALESCE(rincian.tanggal_invoice_vendor, invoice.tanggal)')
            ->orderBy('invoice.nomor_invoice')
            ->orderBy('rincian.id')
            ->get();

        $summary = [
            'subtotal' => $rows->sum('sub_total'),
            'pph' => $rows->sum('pph'),
            'adjustment' => $rows->sum('adjustment'),
            'grand_total' => $rows->sum('grand_total'),
        ];

        return view('report.biaya-agen.show', compact('rows', 'summary', 'validated'));
    }
}
