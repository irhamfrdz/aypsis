
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
