from pathlib import Path
p=Path('resources/views/pranota-ongkos-truk/analysis-modal.blade.php');s=p.read_text();s=s.replace("                pdf.hidden = false;\n                printButton.disabled = false;", "                pdf.hidden = true;\n                printButton.disabled = true;",1);start=s.index("    content.addEventListener('click', event => {");end=s.index("    printButton.addEventListener",start);s=s[:start]+'''    content.addEventListener('change', event => {
        if (event.target.id !== 'analysis-destination') return;
        content.querySelector('.analysis-results')?.remove();
        printButton.disabled = true;
        pdf.hidden = true;
    });
    content.addEventListener('submit', async event => {
        const form = event.target.closest('.js-analysis-destination');
        if (!form) return;
        event.preventDefault();
        if (!form.reportValidity()) return;
        const destination = new FormData(form).get('tujuan');
        const url = new URL(form.action);
        url.searchParams.set('tujuan', destination);
        controller?.abort();
        const request = new AbortController();
        controller = request;
        const submit = form.querySelector('button[type="submit"]');
        submit.disabled = true;
        submit.textContent = 'Menganalisis...';
        content.setAttribute('aria-busy', 'true');
        printButton.disabled = true;
        pdf.hidden = true;
        try {
            const response = await fetch(url, { signal:request.signal, headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'text/html' } });
            if (!response.ok || response.redirected) throw new Error('load');
            const markup = await response.text();
            if (request.signal.aborted || !dialog.open) return;
            content.innerHTML = markup;
            const pdfUrl = new URL(opener.dataset.pdfUrl, window.location.href);
            pdfUrl.searchParams.set('tujuan', destination);
            pdf.href = pdfUrl.href;
            pdf.hidden = false;
            printButton.disabled = false;
        } catch (error) {
            if (error.name !== 'AbortError') {
                let message = content.querySelector('.analysis-error');
                if (!message) { message = document.createElement('p'); message.className = 'analysis-error'; message.setAttribute('role', 'alert'); content.appendChild(message); }
                message.textContent = 'Analisis gagal dimuat. Silakan coba kembali atau muat ulang halaman jika sesi berakhir.';
            }
        } finally {
            if (controller === request) {
                content.setAttribute('aria-busy', 'false');
                submit.disabled = false;
                submit.textContent = 'Analisis';
            }
        }
    });
'''+s[end:];s=s.replace("if (table.closest('.analysis-detail-panel'))", "if (table.closest('.analysis-results'))");p.write_text(s)
p=Path('resources/views/pranota-ongkos-truk/analysis-pdf.blade.php');s=p.read_text();s=s.replace("Cakupan: {{ $rows->count() }}", "@if($selectedDestination ?? null)Tujuan terpilih: <strong>{{ $selectedDestination }}</strong><br>@endif\nCakupan: {{ $rows->count() }}");s=s.replace('Perbandingan dilakukan untuk plat dan tujuan yang sama.', "{{ ($selectedDestination ?? null) ? 'Perbandingan dilakukan pada seluruh plat dengan tujuan terpilih.' : 'Perbandingan dilakukan untuk plat dan tujuan yang sama.' }}")
s=s.replace('Tidak ditemukan perbedaan total UJ pada plat dan tujuan yang sama.', 'Tidak ditemukan perbedaan total UJ pada kelompok yang dibandingkan.')
start=s.index('@if(is_array($pranota->adjustments)');s=s[:start]+'@unless($selectedDestination ?? null)\n'+s[start:];s=s.replace('</div>\n</body>', '@endunless\n</div>\n</body>');p.write_text(s)
