from pathlib import Path
p=Path('app/Http/Controllers/PranotaOngkosTrukController.php');s=p.read_text();needle='    public function analysis($id,';idx=s.index(needle);s=s[:idx]+'''    public function analysisPreview($id, \\App\\Services\\PranotaOngkosTrukAnalysisService $service)
    {
        $pranota = PranotaOngkosTruk::findOrFail($id);
        $analysis = $service->analyze($pranota);

        return view('pranota-ongkos-truk.analysis-preview', [
            'pranota' => $pranota,
            'rows' => $analysis['rows'],
            'summaries' => $analysis['summaries'],
        ]);
    }

'''+s[idx:];p.write_text(s)
p=Path('routes/web.php');s=p.read_text();needle="        Route::get('/{id}/analysis', [PranotaOngkosTrukController::class, 'analysis'])->name('analysis');";assert needle in s;s=s.replace(needle,needle+"\n        Route::get('/{id}/analysis-preview', [PranotaOngkosTrukController::class, 'analysisPreview'])->name('analysis-preview');",1);p.write_text(s)
p=Path('resources/views/pranota-ongkos-truk/index.blade.php');s=p.read_text();start=s.index('                                    <a href="{{ route(\'pranota-ongkos-truk.analysis\'');end=s.index('                                    </a>',start)+len('                                    </a>');s=s[:start]+'''                                    <button type="button" class="js-analysis p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors" title="Lihat Analisis UJ" aria-haspopup="dialog" data-preview-url="{{ route('pranota-ongkos-truk.analysis-preview', $pranota->id) }}" data-pdf-url="{{ route('pranota-ongkos-truk.analysis', $pranota->id) }}" data-pranota="{{ $pranota->no_pranota }}">
                                        <i class="fas fa-magnifying-glass-chart"></i>
                                        <span class="text-xs font-semibold">Analisis</span>
                                    </button>'''+s[end:];s=s.replace('@endsection','''@include('pranota-ongkos-truk.analysis-modal')
@endsection''');p.write_text(s)
