from pathlib import Path
p=Path('app/Services/PranotaOngkosTrukAnalysisService.php');s=p.read_text();s=s.replace('    public function analyze(PranotaOngkosTruk $pranota): array','''    public function destinations(PranotaOngkosTruk $pranota): array
    {
        $pranota->load(['items.suratJalan.tujuanPengambilanRelation', 'items.suratJalanBongkaran.tujuanPengambilanRelation']);

        return $pranota->items->map(function ($item) {
            $sj = $item->type === 'SuratJalan' ? $item->suratJalan : $item->suratJalanBongkaran;

            return trim($sj?->tujuanPengambilanRelation?->ke ?? $sj?->tujuan_pengambilan ?? '-');
        })->unique(fn ($value) => mb_strtoupper($value))->sort()->values()->all();
    }

    public function analyze(PranotaOngkosTruk $pranota, ?string $destinationFilter = null): array''')
needle="        $groupKey = fn ($row) => json_encode([preg_replace('/\\s+/', '', strtoupper($row['plat'])), mb_strtoupper(trim($row['tujuan']))]);";assert needle in s;s=s.replace(needle,"""        if ($destinationFilter !== null) {
            $rows = $rows->filter(fn ($row) => mb_strtoupper(trim($row['tujuan'])) === mb_strtoupper(trim($destinationFilter)))->values();
        }
        $groupKey = fn ($row) => $destinationFilter !== null
            ? mb_strtoupper(trim($row['tujuan']))
            : json_encode([preg_replace('/\\s+/', '', strtoupper($row['plat'])), mb_strtoupper(trim($row['tujuan']))]);""")
s=s.replace("'plat' => $group->first()['plat']", "'plat' => $group->pluck('plat')->unique()->implode(', ')")
p.write_text(s)
p=Path('app/Http/Controllers/PranotaOngkosTrukController.php');s=p.read_text();start=s.index('    public function analysisPreview(');end=s.index('    public function analysis(',start);s=s[:start]+'''    public function analysisPreview($id, \\App\\Services\\PranotaOngkosTrukAnalysisService $service)
    {
        $pranota = PranotaOngkosTruk::findOrFail($id);
        $destinations = $service->destinations($pranota);
        $validated = request()->validate(['tujuan' => ['nullable', 'string', \\Illuminate\\Validation\\Rule::in($destinations)]]);
        $selectedDestination = $validated['tujuan'] ?? null;
        $analysis = $selectedDestination !== null ? $service->analyze($pranota, $selectedDestination) : null;

        return view('pranota-ongkos-truk.analysis-preview', [
            'pranota' => $pranota,
            'destinations' => $destinations,
            'selectedDestination' => $selectedDestination,
            'rows' => $analysis['rows'] ?? collect(),
            'summaries' => $analysis['summaries'] ?? [],
        ]);
    }

'''+s[end:];s=s.replace("        $analysis = $service->analyze($pranota);\n        $filename", "        $selectedDestination = request()->validate(['tujuan' => ['nullable', 'string', \\Illuminate\\Validation\\Rule::in($service->destinations($pranota))]])['tujuan'] ?? null;\n        $analysis = $service->analyze($pranota, $selectedDestination);\n        $filename",1);s=s.replace("'summaries' => $analysis['summaries'],\n        ])->setPaper", "'summaries' => $analysis['summaries'],\n            'selectedDestination' => $selectedDestination,\n        ])->setPaper",1);p.write_text(s)
p=Path('resources/views/pranota-ongkos-truk/analysis-preview.blade.php');old=p.read_text();table=old[old.index('    <div class="overflow-x-auto analysis-table-wrap">'):old.index('    <p class="text-sm text-gray-500 mt-3">')];table=table.replace('$detailRows','$rows').replace('Subtotal plat dan tujuan terpilih','Subtotal tujuan terpilih');s='''<form class="js-analysis-destination analysis-guide" action="{{ route('pranota-ongkos-truk.analysis-preview', $pranota->id) }}" method="GET" style="align-items:center;flex-wrap:wrap">
    <label for="analysis-destination" class="font-bold">Pilih tujuan</label>
    <select id="analysis-destination" name="tujuan" required class="border border-gray-300 rounded-lg text-sm" style="min-width:220px;color:#1e293b;background:white">
        <option value="">-- Pilih tujuan --</option>
        @foreach($destinations as $destination)<option value="{{ $destination }}" @selected($selectedDestination === $destination)>{{ $destination }}</option>@endforeach
    </select>
    <button type="submit" class="analysis-action analysis-action-primary" @disabled(empty($destinations))>Analisis</button>
    <span>Bandingkan UJ seluruh plat pada tujuan yang dipilih.</span>
</form>
@if($selectedDestination !== null)
<div class="analysis-results" data-destination="{{ $selectedDestination }}">
    <div class="analysis-metrics">
        <div class="analysis-metric"><span>TUJUAN</span><strong>{{ $selectedDestination }}</strong></div>
        <div class="analysis-metric"><span>CAKUPAN</span><strong>{{ $rows->count() }} <small>baris / {{ $rows->pluck('plat')->unique()->count() }} plat</small></strong></div>
        <div class="analysis-metric"><span>TOTAL UJ</span><strong><small>Rp</small> {{ number_format($rows->sum('uj'), 0, ',', '.') }}</strong></div>
    </div>
    <h3 class="text-base font-bold text-gray-900 mb-3">Rincian Analisis Tujuan {{ $selectedDestination }}</h3>
    <p class="text-sm text-gray-600 mb-3">Seluruh plat pada tujuan ini dibandingkan. Data diurutkan berdasarkan nama pengirim; nomor mengacu pada baris asli pranota. Perbedaan size dan biaya tambahan dapat memengaruhi total UJ. Alasan operasional memerlukan bukti pendukung.</p>
    @if(empty($summaries))<p class="text-sm text-gray-600 mb-3">{{ $rows->count() > 1 ? 'Total UJ sama pada seluruh baris tujuan ini.' : 'Hanya satu baris pada tujuan ini; belum ada pembanding.' }}</p>@endif
'''+table.replace('@foreach($rows as $row)',"@foreach($rows->sortBy(fn ($row) => mb_strtoupper(trim($row['pengirim'])))->values() as $row)")+'''
</div>
@else
<p class="text-sm text-gray-500">{{ empty($destinations) ? 'Belum ada tujuan pada pranota ini.' : 'Pilih tujuan lalu tekan Analisis untuk menampilkan hasil.' }}</p>
@endif
''';p.write_text(s)
