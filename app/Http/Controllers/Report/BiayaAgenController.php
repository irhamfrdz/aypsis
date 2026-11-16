<?php

namespace App\Http\Controllers\Report;

use App\Exports\BiayaAgenReportExport;
use App\Http\Controllers\Controller;
use App\Services\BiayaAgenTripCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

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
        return view('report.biaya-agen.show', $this->reportData($request));
    }

    public function print(Request $request)
    {
        return view('report.biaya-agen.print', $this->reportData($request));
    }

    public function export(Request $request)
    {
        $data = $this->reportData($request);

        return Excel::download(new BiayaAgenReportExport($data), 'report-biaya-agen-'.now()->format('Ymd-His').'.xlsx');
    }

    private function reportData(Request $request): array
    {
        $input = $request->all();
        if (is_string($input['jenis_biaya'] ?? null)) {
            $input['jenis_biaya'] = trim($input['jenis_biaya']) === '' ? [] : [$input['jenis_biaya']];
        }

        $validator = Validator::make($input, [
            'mode' => ['required', Rule::in(['kapal', 'tanggal', 'gabungan'])],
            'kapal' => ['required_if:mode,kapal,gabungan', 'nullable', 'string', 'max:255'],
            'start_date' => ['required_if:mode,tanggal,gabungan', 'nullable', 'date'],
            'end_date' => ['required_if:mode,tanggal,gabungan', 'nullable', 'date', 'after_or_equal:start_date'],
            'jenis_biaya' => ['nullable', 'array'],
            'jenis_biaya.*' => ['string', 'max:255'],
        ]);

        if ($validator->fails()) {
            throw (new ValidationException($validator))->redirectTo(route('report.biaya-agen.index'));
        }

        $validated = $validator->validated();
        $validated['jenis_biaya'] = collect($validated['jenis_biaya'] ?? [])
            ->map(fn ($jenisBiaya) => trim($jenisBiaya))
            ->filter()
            ->unique(fn ($jenisBiaya) => mb_strtolower($jenisBiaya))
            ->values()
            ->all();

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

        if ($validated['jenis_biaya'] !== []) {
            $query->whereIn(DB::raw('LOWER(TRIM(rincian.type_keterangan))'), array_map(fn ($jenisBiaya) => mb_strtolower($jenisBiaya), $validated['jenis_biaya']));
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

        $tripCounter = new BiayaAgenTripCounter;
        $groups = $rows->groupBy(fn ($row) => mb_strtolower(trim((string) $row->kapal)) ?: '__tanpa_kapal__')
            ->map(function ($shipRows) {
                return [
                    'kapal' => trim((string) $shipRows->first()->kapal) ?: 'Tanpa kapal',
                    'rows' => $shipRows,
                    'trip_count' => 0,
                    'subtotal' => $shipRows->sum('sub_total'),
                    'pph' => $shipRows->sum('pph'),
                    'adjustment' => $shipRows->sum('adjustment'),
                    'grand_total' => $shipRows->sum('grand_total'),
                ];
            })->sortBy('kapal', SORT_NATURAL | SORT_FLAG_CASE)->values();

        $shipKey = fn ($name) => preg_replace('/[^A-Z0-9]/', '', mb_strtoupper(trim((string) $name)));
        $shipKeys = $groups->pluck('kapal')->map($shipKey)->filter()->unique()->values()->all();
        if ($shipKeys !== []) {
            $manifestQuery = DB::table('manifests')
                ->select('nama_kapal', 'no_voyage as voyage')
                ->whereNotNull('no_voyage')
                ->whereRaw("TRIM(no_voyage) <> ''")
                ->whereIn(DB::raw("UPPER(REPLACE(REPLACE(TRIM(nama_kapal), '.', ''), ' ', ''))"), $shipKeys)
                ->distinct();

            if (in_array($validated['mode'], ['tanggal', 'gabungan'], true)) {
                $manifestQuery->whereBetween(DB::raw('COALESCE(tanggal_berangkat, tanggal_mulai_berlayar, tanggal_muat)'), [
                    $validated['start_date'], $validated['end_date'],
                ]);
            }

            $manifestVoyages = $manifestQuery->get()->groupBy(fn ($row) => $shipKey($row->nama_kapal));
            $groups = $groups->map(function ($group) use ($shipKey, $manifestVoyages, $tripCounter) {
                $group['trip_count'] = $tripCounter->count($manifestVoyages->get($shipKey($group['kapal']), collect()));

                return $group;
            });
        }

        return compact('rows', 'groups', 'summary', 'validated');
    }
}
