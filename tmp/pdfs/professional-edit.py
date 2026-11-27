from pathlib import Path
p=Path('resources/views/pranota-ongkos-truk/analysis-preview.blade.php');s=p.read_text();start=s.index('<div class="mb-5">');end=s.index('<h3 ',start);s=s[:start]+'''<div class="analysis-metrics">
    <div class="analysis-metric"><span>DATA PRANOTA</span><strong>{{ $rows->count() }} <small>baris transaksi</small></strong></div>
    <div class="analysis-metric"><span>PERBEDAAN UJ</span><strong>{{ count($summaries) }} <small>kelompok plat &amp; tujuan</small></strong></div>
    <div class="analysis-metric"><span>TOTAL UJ</span><strong><small>Rp</small> {{ number_format($rows->sum('uj'), 0, ',', '.') }}</strong></div>
</div>
<div class="analysis-guide"><i class="fas fa-circle-info" aria-hidden="true"></i><span>Klik <strong>No Plat</strong> untuk melihat rincian biaya pada tujuan yang dipilih. UJ mencakup uang jalan dasar, biaya tambahan, dan penyesuaian.</span></div>
'''+s[end:];s=s.replace('class="overflow-x-auto mb-5"','class="overflow-x-auto mb-5 analysis-table-wrap"').replace('class="overflow-x-auto"','class="overflow-x-auto analysis-table-wrap"');s=s.replace('js-analysis-detail text-blue-700 font-bold underline hover:text-blue-900','js-analysis-detail analysis-plate');s=s.replace('<td class="analysis-money">{{ number_format($summary', '<td class="analysis-money analysis-difference">{{ number_format($summary');p.write_text(s)
p=Path('resources/views/pranota-ongkos-truk/analysis-modal.blade.php');s=p.read_text();start=s.index('    <div class="flex items-center justify-between');end=s.index('    <div id="analysis-content"',start);s=s[:start]+'''    <div class="analysis-toolbar">
        <div class="analysis-heading">
            <div class="analysis-icon"><i class="fas fa-chart-column" aria-hidden="true"></i></div>
            <div><span class="analysis-eyebrow">PRANOTA ONGKOS TRUK</span><h2 id="analysis-title">Analisis Uang Jalan</h2><p id="analysis-pranota"></p></div>
        </div>
        <div class="analysis-actions">
            <button id="analysis-print" type="button" class="analysis-action analysis-action-primary" disabled title="Cetak ringkasan dan rincian yang sedang dipilih"><i class="fas fa-print" aria-hidden="true"></i> Print</button>
            <a id="analysis-pdf" class="analysis-action" hidden><i class="fas fa-file-pdf" aria-hidden="true"></i> Download PDF</a>
            <button id="analysis-close" type="button" class="analysis-action" aria-label="Tutup popup analisis"><i class="fas fa-xmark" aria-hidden="true"></i> Tutup</button>
        </div>
    </div>
'''+s[end:];s=s.replace('<style>','<style id="analysis-styles">',1);idx=s.index('</style>');s=s[:idx]+'''
#analysis-dialog { color:#1e293b; box-shadow:0 24px 80px #0f172a55; }
#analysis-dialog .analysis-toolbar { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:22px 26px; border-bottom:1px solid #e2e8f0; background:#fff; flex-shrink:0; }
#analysis-dialog .analysis-heading { display:flex; align-items:center; gap:14px; }
#analysis-dialog .analysis-icon { display:grid; place-items:center; width:48px; height:48px; background:#e8effa; color:#254a78; border-radius:14px; font-size:22px; }
#analysis-dialog .analysis-eyebrow { font-size:10px; letter-spacing:.14em; font-weight:700; color:#64748b; }
#analysis-dialog #analysis-title { font-size:21px; font-weight:700; margin:2px 0; }
#analysis-dialog #analysis-pranota { color:#64748b; font-size:12px; }
#analysis-dialog .analysis-actions { display:flex; gap:8px; flex-wrap:wrap; }
#analysis-dialog .analysis-action { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid #d8e0ea; border-radius:9px; background:#fff; color:#334155; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; }
#analysis-dialog .analysis-action-primary { background:#153c52; color:white; border-color:#153c52; }
#analysis-dialog .analysis-action:hover { filter:brightness(.95); }
#analysis-dialog .analysis-action:disabled { opacity:.45; cursor:wait; }
#analysis-dialog button:focus-visible, #analysis-dialog a:focus-visible { outline:3px solid #93c5fd; outline-offset:3px; }
#analysis-dialog #analysis-content { background:#f6f8fb; padding:24px 26px; }
#analysis-dialog .analysis-metrics { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin-bottom:18px; }
#analysis-dialog .analysis-metric { padding:18px 20px; border:1px solid #e2e8f0; border-radius:12px; background:white; }
#analysis-dialog .analysis-metric > span { display:block; font-size:10px; font-weight:700; letter-spacing:.08em; color:#64748b; margin-bottom:8px; }
#analysis-dialog .analysis-metric strong { font-size:25px; font-weight:700; color:#153c52; }
#analysis-dialog .analysis-metric small { font-size:12px; font-weight:400; color:#64748b; }
#analysis-dialog .analysis-guide { display:flex; gap:10px; padding:12px 16px; background:#eaf1fa; color:#355477; border-radius:9px; font-size:12px; line-height:1.6; margin-bottom:22px; }
#analysis-dialog .analysis-table-wrap { border:1px solid #dbe2e8; border-radius:10px; background:white; }
#analysis-dialog .analysis-table th { padding:13px 12px; font-size:10px; text-transform:uppercase; letter-spacing:.04em; }
#analysis-dialog .analysis-table td { padding:12px; line-height:1.6; }
#analysis-dialog .analysis-plate { display:inline-block; padding:5px 9px; background:#edf3fa; color:#214d7d; border:1px solid #d7e4f3; border-radius:6px; font-weight:700; white-space:nowrap; cursor:pointer; }
#analysis-dialog .analysis-plate[aria-expanded="true"] { background:#153c52; color:white; border-color:#153c52; }
#analysis-dialog .analysis-difference { color:#a25b16; font-weight:700; background:#fff8ec; }
#analysis-dialog .analysis-detail-panel { margin-top:24px; padding-top:22px; border-top:2px solid #dbe5ef; }
@media(max-width:700px) {
 #analysis-dialog .analysis-toolbar { align-items:flex-start; flex-direction:column; padding:16px; }
 #analysis-dialog #analysis-content { padding:16px; }
 #analysis-dialog .analysis-metrics { grid-template-columns:1fr; gap:8px; }
 #analysis-dialog .analysis-metric { padding:12px 16px; }
}
'''+s[idx:];s=s.replace("    let controller;", "    const printButton = document.getElementById('analysis-print');\n    let controller;");s=s.replace('            pdf.hidden = true;', '            pdf.hidden = true;\n            printButton.disabled = true;');s=s.replace('                pdf.hidden = false;', '                pdf.hidden = false;\n                printButton.disabled = false;');needle="    document.getElementById('analysis-close').addEventListener";idx=s.index(needle);s=s[:idx]+'''    printButton.addEventListener('click', () => {
        if (printButton.disabled) return;
        const clone = content.cloneNode(true);
        clone.removeAttribute('id');
        clone.querySelectorAll('[hidden], .js-analysis-detail-close').forEach(element => element.remove());
        clone.querySelectorAll('.js-analysis-detail').forEach(button => button.replaceWith(document.createTextNode(button.textContent)));
        const frame = document.createElement('iframe');
        frame.title = 'Cetak analisis pranota';
        frame.style.cssText = 'position:fixed;width:0;height:0;border:0;bottom:0;right:0;';
        document.body.appendChild(frame);
        const doc = frame.contentDocument;
        doc.open();
        doc.write('<!doctype html><html lang="id"><head><meta charset="UTF-8"><title>Analisis Pranota</title></head><body><main id="analysis-dialog"></main></body></html>');
        doc.close();
        const style = doc.createElement('style');
        style.textContent = document.getElementById('analysis-styles').textContent + `
            @page { size: A3 landscape; margin: 12mm; }
            body { margin:0; font-family:Arial,sans-serif; color:#1e293b; }
            #analysis-dialog { box-shadow:none; }
            #analysis-dialog h1 { font-size:20px; margin:0 0 8px; }
            #analysis-dialog h3 { font-size:14px; margin:16px 0 10px; }
            #analysis-dialog p { font-size:10px; line-height:1.5; }
            #analysis-dialog .analysis-table-wrap { overflow:visible!important; border-radius:0; }
            #analysis-dialog table { min-width:0!important; width:100%!important; table-layout:fixed; }
            #analysis-dialog .analysis-table th, #analysis-dialog .analysis-table td { font-size:8px; padding:6px 4px; overflow-wrap:anywhere; min-width:0!important; }
            #analysis-dialog .analysis-detail-panel td:last-child, #analysis-dialog .analysis-detail-panel th:last-child { width:25%; }
            #analysis-dialog .analysis-metrics { display:flex; gap:12px; }
            #analysis-dialog .analysis-metric { flex:1; }
            #analysis-dialog .analysis-guide { display:none; }
            thead { display:table-header-group; } tr { break-inside:avoid; }
            * { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        `;
        doc.head.appendChild(style);
        const report = doc.getElementById('analysis-dialog');
        const title = doc.createElement('h1');
        title.textContent = 'Analisis Uang Jalan - ' + document.getElementById('analysis-pranota').textContent;
        report.appendChild(title);
        const date = doc.createElement('p');
        date.textContent = 'PT. Alexindo YakinPrima Shipping | Dicetak: ' + new Date().toLocaleString('id-ID');
        report.appendChild(date);
        report.appendChild(doc.adoptNode(clone));
        frame.contentWindow.addEventListener('afterprint', () => frame.remove(), { once:true });
        requestAnimationFrame(() => {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        });
    });
'''+s[idx:];p.write_text(s)
