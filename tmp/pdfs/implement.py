from pathlib import Path
p=Path('app/Services/PranotaOngkosTrukAnalysisService.php');p.write_text(p.read_text(encoding='utf-8-sig'),encoding='utf-8')
p=Path('app/Http/Controllers/PranotaOngkosTrukController.php');s=p.read_text();needle='    public function destroy($id)';insert='''    public function analysis($id, \\App\\Services\\PranotaOngkosTrukAnalysisService $service)
    {
        $pranota = PranotaOngkosTruk::findOrFail($id);
        $analysis = $service->analyze($pranota);
        $filename = 'Analisis-UJ-'.preg_replace('/[^A-Za-z0-9_-]/', '-', $pranota->no_pranota).'.pdf';

        return \\Barryvdh\\DomPDF\\Facade\\Pdf::loadView('pranota-ongkos-truk.analysis-pdf', [
            'pranota' => $pranota,
            'rows' => $analysis['rows'],
            'summaries' => $analysis['summaries'],
        ])->setPaper('a3', 'landscape')->download($filename);
    }

''';assert needle in s;s=s.replace(needle,insert+needle,1);p.write_text(s)
p=Path('routes/web.php');s=p.read_text();needle="        Route::get('/{id}/print', [PranotaOngkosTrukController::class, 'print'])->name('print');";assert needle in s;s=s.replace(needle,needle+"\n        Route::get('/{id}/analysis', [PranotaOngkosTrukController::class, 'analysis'])->name('analysis');",1);p.write_text(s)
p=Path('resources/views/pranota-ongkos-truk/index.blade.php');s=p.read_text();needle='                                    <a href="{{ route(\'pranota-ongkos-truk.export\', $pranota->id) }}"';assert needle in s;s=s.replace(needle,'''                                    <a href="{{ route('pranota-ongkos-truk.analysis', $pranota->id) }}" class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors" title="Download Analisis UJ (PDF)" aria-label="Download analisis UJ pranota {{ $pranota->no_pranota }}">
                                        <i class="fas fa-magnifying-glass-chart"></i>
                                        <span class="text-xs font-semibold">Analisis</span>
                                    </a>
'''+needle,1);p.write_text(s)
