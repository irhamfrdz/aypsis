<dialog id="analysis-dialog" aria-labelledby="analysis-title" style="width:96vw;max-width:1600px;max-height:92vh;padding:0;border:0;border-radius:16px;">
    <div class="analysis-toolbar">
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
    <div id="analysis-content" class="p-5" style="overflow:auto;max-height:calc(92vh - 95px);" aria-live="polite" aria-busy="false"></div>
</dialog>
<style id="analysis-styles">
#analysis-dialog [hidden] { display: none !important; }
#analysis-dialog::backdrop { background: rgba(15, 23, 42, .6); }
#analysis-dialog[open] { display: flex; flex-direction: column; }
#analysis-dialog .analysis-table { width: 100%; border-collapse: collapse; font-size: 12px; }
#analysis-dialog .analysis-table th { background: #153c52; color: white; text-align: left; }
#analysis-dialog .analysis-table th, #analysis-dialog .analysis-table td { padding: 10px; border: 1px solid #dbe2e8; vertical-align: top; }
#analysis-dialog .analysis-table tbody tr:nth-child(even) { background: #f8fafc; }
#analysis-dialog .analysis-table .analysis-money { text-align: right; white-space: nowrap; }

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
#analysis-dialog .analysis-destination-card { background:white; border:1px solid #dbe4ed; border-radius:14px; padding:22px; margin-bottom:24px; }
#analysis-dialog .analysis-destination-intro { display:flex; align-items:flex-start; gap:12px; margin-bottom:20px; }
#analysis-dialog .analysis-destination-icon { display:grid; place-items:center; background:#edf3fa; color:#254a78; width:40px; height:40px; border-radius:10px; flex-shrink:0; }
#analysis-dialog .analysis-destination-intro h3 { margin:0 0 5px; font-size:15px; font-weight:700; color:#18384d; }
#analysis-dialog .analysis-destination-intro p { margin:0; font-size:12px; color:#64748b; line-height:1.6; }
#analysis-dialog .analysis-destination-controls { display:flex; align-items:flex-start; gap:12px; }
#analysis-dialog .analysis-destination-field { flex:1; min-width:0; max-width:600px; }
#analysis-dialog .analysis-destination-field label { display:block; font-size:12px; font-weight:600; margin-bottom:8px; color:#334155; }
#analysis-dialog .analysis-destination-field label span { color:#b45309; }
#analysis-dialog .analysis-destination-field select { display:block; width:100%; min-height:46px; border:1px solid #cbd5e1; border-radius:9px; background-color:#fff; color:#1e293b; font-size:14px; padding:10px 36px 10px 13px; cursor:pointer; }
#analysis-dialog .analysis-destination-field select:focus { outline:3px solid #dbeafe; outline-offset:1px; border-color:#4877a8; }
#analysis-dialog .analysis-destination-field select:disabled { background-color:#f1f5f9; cursor:not-allowed; color:#94a3b8; }
#analysis-dialog .analysis-destination-field p { font-size:11px; margin:7px 0 0; color:#64748b; }
#analysis-dialog .analysis-destination-submit { margin-top:26px; min-height:46px; justify-content:center; padding:10px 24px; }
@media(max-width:700px) {
 #analysis-dialog .analysis-destination-card { padding:16px; }
 #analysis-dialog .analysis-destination-controls { flex-direction:column; }
 #analysis-dialog .analysis-destination-field { width:100%; max-width:none; }
 #analysis-dialog .analysis-destination-submit { margin-top:0; width:100%; }
 #analysis-dialog .analysis-toolbar { align-items:flex-start; flex-direction:column; padding:16px; }
 #analysis-dialog #analysis-content { padding:16px; }
 #analysis-dialog .analysis-metrics { grid-template-columns:1fr; gap:8px; }
 #analysis-dialog .analysis-metric { padding:12px 16px; }
}
</style>
<script>
(() => {
    const dialog = document.getElementById('analysis-dialog');
    const content = document.getElementById('analysis-content');
    const pdf = document.getElementById('analysis-pdf');
    const printButton = document.getElementById('analysis-print');
    let emptyResults;
    let controller;
    let opener;
    let previousOverflow;
    document.querySelectorAll('.js-analysis').forEach(button => {
        button.addEventListener('click', async () => {
            opener = button;
            controller?.abort();
            const request = new AbortController();
            controller = request;
            document.getElementById('analysis-pranota').textContent = button.dataset.pranota;
            pdf.hidden = true;
            printButton.disabled = true;
            pdf.removeAttribute('href');
            content.textContent = 'Memuat data analisis...';
            content.setAttribute('aria-busy', 'true');
            previousOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            dialog.showModal();
            try {
                const response = await fetch(button.dataset.previewUrl, {
                    signal: request.signal,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                });
                if (!response.ok || response.redirected) throw new Error('load');
                const markup = await response.text();
                if (request.signal.aborted || !dialog.open) return;
                content.innerHTML = markup;
                emptyResults = content.querySelector('.analysis-results').cloneNode(true);
                pdf.href = button.dataset.pdfUrl;
                pdf.hidden = true;
                printButton.disabled = true;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    content.textContent = 'Analisis gagal dimuat. Tutup popup lalu coba kembali. Jika sesi berakhir, muat ulang halaman dan masuk kembali.';
                }
            } finally {
                if (controller === request) content.setAttribute('aria-busy', 'false');
            }
        });
    });
    async function loadDestination(form) {
        controller?.abort();
        const request = new AbortController();
        controller = request;
        const destination = form.elements.tujuan.value;
        printButton.disabled = true;
        pdf.hidden = true;
        pdf.removeAttribute('href');
        const results = emptyResults.cloneNode(true);
        content.querySelector('.analysis-results').replaceWith(results);
        content.setAttribute('aria-busy', destination ? 'true' : 'false');
        if (!destination) return;
        const cell = results.querySelector('tbody td');
        cell.textContent = 'Memuat analisis tujuan ' + destination + '...';
        const url = new URL(form.action);
        url.searchParams.set('tujuan', destination);
        try {
            const response = await fetch(url, { signal:request.signal, headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'text/html' } });
            if (!response.ok || response.redirected) throw new Error('load');
            const markup = await response.text();
            if (request.signal.aborted || !dialog.open) return;
            const parsed = new DOMParser().parseFromString(markup, 'text/html');
            const updated = parsed.querySelector('.analysis-results');
            if (!updated) throw new Error('load');
            results.replaceWith(updated);
            const pdfUrl = new URL(opener.dataset.pdfUrl, window.location.href);
            pdfUrl.searchParams.set('tujuan', destination);
            pdf.href = pdfUrl.href;
            pdf.hidden = false;
            printButton.disabled = false;
        } catch (error) {
            if (error.name !== 'AbortError' && controller === request) {
                cell.textContent = 'Analisis gagal dimuat. Pilih kembali tujuan untuk mencoba lagi atau muat ulang halaman jika sesi berakhir.';
                cell.setAttribute('role', 'alert');
            }
        } finally {
            if (controller === request) content.setAttribute('aria-busy', 'false');
        }
    }
    content.addEventListener('change', event => {
        if (event.target.id === 'analysis-destination') loadDestination(event.target.form);
    });
    content.addEventListener('submit', event => {
        const form = event.target.closest('.js-analysis-destination');
        if (!form) return;
        event.preventDefault();
        if (form.reportValidity()) loadDestination(form);
    });
    printButton.addEventListener('click', () => {
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
            @page { size: A4 portrait; margin: 10mm; }
            body { margin:0; font-family:Arial,sans-serif; color:#1e293b; }
            #analysis-dialog { box-shadow:none; }
            #analysis-dialog h1 { font-size:20px; margin:0 0 8px; }
            #analysis-dialog h3 { font-size:14px; margin:16px 0 10px; }
            #analysis-dialog p { font-size:10px; line-height:1.5; }
            #analysis-dialog .analysis-table-wrap { overflow:visible!important; border-radius:0; }
            #analysis-dialog table { min-width:0!important; width:100%!important; table-layout:fixed; }
            #analysis-dialog .analysis-table th, #analysis-dialog .analysis-table td { font-size:7px; padding:4px 2px; overflow-wrap:anywhere; min-width:0!important; letter-spacing:0; }
            #analysis-dialog .analysis-table .analysis-money { white-space:normal; }
            #analysis-dialog .analysis-print-detail td:last-child, #analysis-dialog .analysis-print-detail th:last-child { width:25%; }
            #analysis-dialog .analysis-table { margin-bottom:16px; }
            #analysis-dialog .analysis-metrics { display:flex; gap:12px; }
            #analysis-dialog .analysis-metric { flex:1; }
            #analysis-dialog .analysis-guide { display:none; }
            thead { display:table-header-group; } tr { break-inside:avoid; }
            * { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        `;
        doc.head.appendChild(style);
        const report = doc.getElementById('analysis-dialog');
        clone.querySelectorAll('.analysis-table').forEach(table => {
            if (table.closest('.analysis-results')) table.classList.add('analysis-print-detail');
            report.appendChild(doc.adoptNode(table));
        });
        frame.contentWindow.addEventListener('afterprint', () => frame.remove(), { once:true });
        requestAnimationFrame(() => {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        });
    });
    document.getElementById('analysis-close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', event => {
        const rect = dialog.getBoundingClientRect();
        if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) dialog.close();
    });
    dialog.addEventListener('close', () => {
        controller?.abort();
        document.body.style.overflow = previousOverflow ?? '';
        opener?.focus();
    });
})();
</script>
