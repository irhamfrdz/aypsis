<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\PengisianTanggalDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengisianTanggalDokumenController extends Controller
{
    private const DATE_FIELDS = [
        'tanggal_keluar_gate_tujuan',
        'tanggal_keluar_gudang_ayp',
        'tanggal_tiba_gudang_customer',
        'tanggal_keluar_gudang_customer',
        'tanggal_masuk_gate_tujuan',

        'tanggal_terima_dokumen_jkt_customer',
        'tanggal_terima_dokumen_packinglist',
        'tanggal_dokumen_invoice_faktur_pajak',
        'tanggal_kirim_dokumen_ppjk',
        'tanggal_terbit_sppb_ppftz',
        'tanggal_terima_dokumen_dari_ppjk',
        'tanggal_kirim_dokumen_jakarta',
        'tanggal_terima_sj_cabang',
        'tanggal_terima_ppftz_cabang',
    ];

    public function index()
    {
        $records = PengisianTanggalDokumen::with('cabang')
            ->when(request('kapal_id'), fn ($query, $kapalId) => $query->where('kapal_id', $kapalId))
            ->when(request('no_voyage'), fn ($query, $voyage) => $query->where('no_voyage', $voyage))
            ->when(request('search'), fn ($query, $search) => $query->where('nomor_dokumen', 'like', "%{$search}%"))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        $kapals = MasterKapal::whereIn('nama_kapal', DB::table('manifests')->select('nama_kapal')->distinct())
            ->orderBy('nama_kapal')->get();

        return view('pengisian-tanggal-dokumen.index', compact('records', 'kapals'));
    }

    public function create()
    {
        $kapals = MasterKapal::whereIn('nama_kapal', DB::table('manifests')->select('nama_kapal')->distinct())
            ->orderBy('nama_kapal')->get();

        return view('pengisian-tanggal-dokumen.form', [
            'record' => new PengisianTanggalDokumen(),
            'kapals' => $kapals,
            'cabangs' => \App\Models\Cabang::orderBy('nama_cabang')->get(),
            'formAction' => route('pengisian-tanggal-dokumen.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function edit(PengisianTanggalDokumen $pengisianTanggalDokumen)
    {
        $kapals = MasterKapal::whereIn('nama_kapal', DB::table('manifests')->select('nama_kapal')->distinct())
            ->orderBy('nama_kapal')->get();

        return view('pengisian-tanggal-dokumen.form', [
            'record' => $pengisianTanggalDokumen,
            'kapals' => $kapals,
            'cabangs' => \App\Models\Cabang::orderBy('nama_cabang')->get(),
            'formAction' => route('pengisian-tanggal-dokumen.update', $pengisianTanggalDokumen),
            'formMethod' => 'PUT',
        ]);
    }

    public function store(Request $request)
    {
        return $this->saveRecord($request, new PengisianTanggalDokumen());
    }

    public function update(Request $request, PengisianTanggalDokumen $pengisianTanggalDokumen)
    {
        return $this->saveRecord($request, $pengisianTanggalDokumen);
    }

    public function getVoyages(Request $request)
    {
        $kapal = MasterKapal::findOrFail($request->integer('kapal_id'));
        $voyages = DB::table('manifests')->where('nama_kapal', $kapal->nama_kapal)
            ->whereNotNull('no_voyage')->where('no_voyage', '!=', '')
            ->distinct()->orderByDesc('no_voyage')->pluck('no_voyage');

        return response()->json(['voyages' => $voyages]);
    }

    public function getDocuments(Request $request)
    {
        $validated = $request->validate([
            'kapal_id' => ['required', 'exists:master_kapals,id'],
            'no_voyage' => ['required', 'string'],
        ]);
        $kapal = MasterKapal::findOrFail($validated['kapal_id']);
        $base = DB::table('manifests')->where('nama_kapal', $kapal->nama_kapal)
            ->where('no_voyage', $validated['no_voyage']);

        $documents = collect([
            ['column' => 'nomor_bl', 'type' => 'BL'],
            ['column' => 'nomor_tanda_terima', 'type' => 'TANDA_TERIMA'],
        ])->flatMap(function (array $documentType) use ($base) {
            $column = $documentType['column'];

            return (clone $base)->whereNotNull($column)->where($column, '!=', '')
                ->select($column, 'nomor_kontainer')->orderBy($column)->get()
                ->groupBy($column)
                ->map(fn ($rows, $number) => [
                    'type' => $documentType['type'],
                    'number' => $number,
                    'containers' => $rows->pluck('nomor_kontainer')->filter()->unique()->values(),
                ])->values();
        })->values();

        return response()->json(['documents' => $documents]);
    }

    private function saveRecord(Request $request, PengisianTanggalDokumen $record)
    {
        $validated = $request->validate([
            'cabang_id' => ['required', 'integer', 'exists:cabangs,id'],
            'kapal_id' => ['required', 'exists:master_kapals,id'],
            'no_voyage' => ['required', 'string', 'max:255'],
            'jenis_dokumen' => ['required', Rule::in(['BL', 'TANDA_TERIMA'])],
            'nomor_dokumen' => ['required', 'string', 'max:255'],
            ...array_fill_keys(self::DATE_FIELDS, ['nullable', 'date']),
        ]);

        $cabang = \App\Models\Cabang::findOrFail($validated['cabang_id']);
        if (preg_match('/\b(jakarta|jkt)\b/i', trim($cabang->nama_cabang))) {
            // Preserve other branches' dates when Jakarta updates this document.
            foreach (array_diff(self::DATE_FIELDS, [
                'tanggal_kirim_dokumen_jakarta', 'tanggal_terima_sj_cabang', 'tanggal_terima_ppftz_cabang',
            ]) as $field) {
                unset($validated[$field]);
            }
        }

        if (preg_match('/\bbatam\b/i', trim($cabang->nama_cabang))) {
            foreach (['tanggal_kirim_dokumen_jakarta', 'tanggal_terima_sj_cabang', 'tanggal_terima_ppftz_cabang'] as $field) {
                unset($validated[$field]);
            }
        }

        $kapal = MasterKapal::findOrFail($validated['kapal_id']);
        $exists = DB::table('manifests')->where('nama_kapal', $kapal->nama_kapal)
            ->where('no_voyage', $validated['no_voyage'])
            ->where($validated['jenis_dokumen'] === 'BL' ? 'nomor_bl' : 'nomor_tanda_terima', $validated['nomor_dokumen'])
            ->exists();

        if (! $exists) {
            return back()->withInput()->withErrors(['nomor_dokumen' => 'Nomor dokumen tidak ditemukan pada kapal dan voyage yang dipilih.']);
        }

        $validated['nama_kapal'] = $kapal->nama_kapal;
        $validated['identifier_hash'] = hash('sha256', implode('|', [
            $kapal->id,
            mb_strtoupper(trim($validated['no_voyage'])),
            $validated['jenis_dokumen'],
            mb_strtoupper(trim($validated['nomor_dokumen'])),
        ]));
        $record->fill($validated)->save();

        return redirect()->route('pengisian-tanggal-dokumen.index')
            ->with('success', 'Tanggal dokumen berhasil disimpan.');
    }
}
