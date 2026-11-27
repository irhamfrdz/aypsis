from pathlib import Path
p=Path('resources/views/pranota-ongkos-truk/analysis-preview.blade.php');s=p.read_text();s=s.replace('Pilih tujuan untuk membandingkan uang jalan seluruh plat dalam pranota ini.', 'Pilih atau ganti tujuan. Tabel otomatis menampilkan perbandingan uang jalan seluruh plat pada tujuan tersebut.')
s=s.replace('        <button type="submit" class="analysis-action analysis-action-primary analysis-destination-submit" @disabled(empty($destinations))>Analisis</button>\n','')
s=s.replace('@if($selectedDestination !== null)\n<div class="analysis-results"', '<div class="analysis-results"')
s=s.replace('<strong>{{ $selectedDestination }}</strong>', "<strong>{{ $selectedDestination ?? 'Belum dipilih' }}</strong>")
s=s.replace('Rincian Analisis Tujuan {{ $selectedDestination }}', "Rincian Analisis{{ $selectedDestination !== null ? ' Tujuan '.$selectedDestination : '' }}")
s=s.replace('@if(empty($summaries))', '@if($selectedDestination !== null && empty($summaries))')
s=s.replace('@foreach($rows->sortBy', '@forelse($rows->sortBy',1)
needle='            @endforeach\n            </tbody>';assert needle in s;s=s.replace(needle,'''            @empty
                <tr><td colspan="11" style="text-align:center;padding:48px 16px;color:#64748b">{{ empty($destinations) ? 'Belum ada tujuan pada pranota ini.' : 'Pilih tujuan di atas untuk menampilkan data analisis.' }}</td></tr>
            @endforelse
            </tbody>''',1)
end=s.index('\n@else\n<p class="text-sm text-gray-500">');s=s[:end]+'\n';p.write_text(s)
p=Path('resources/views/pranota-ongkos-truk/analysis-modal.blade.php');s=p.read_text();s=s.replace('    let controller;', '    let emptyResults;\n    let controller;',1)
s=s.replace('                content.innerHTML = markup;\n                pdf.href', "                content.innerHTML = markup;\n                emptyResults = content.querySelector('.analysis-results').cloneNode(true);\n                pdf.href",1)
start=s.index("    content.addEventListener('change', event => {");end=s.index("    printButton.addEventListener",start)
s=s[:start]+'''    async function loadDestination(form) {
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
'''+s[end:];p.write_text(s)
