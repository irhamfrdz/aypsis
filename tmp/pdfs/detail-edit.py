from pathlib import Path
p=Path('resources/views/pranota-ongkos-truk/analysis-preview.blade.php');s=p.read_text(encoding='utf-8-sig');s=s.replace('Nomor baris mengikuti urutan data pranota.', 'Klik No Plat pada ringkasan untuk melihat rincian plat dan tujuan tersebut. Nomor baris mengikuti urutan data pranota.')
s=s.replace('@forelse($summaries as $summary)','@forelse($summaries as $summaryIndex => $summary)')
s=s.replace("<tr><td>{{ $summary['plat'] }}</td>",'''<tr><td><button type="button" class="js-analysis-detail text-blue-700 font-bold underline hover:text-blue-900" data-detail-id="analysis-detail-{{ $summaryIndex }}" aria-controls="analysis-detail-{{ $summaryIndex }}" aria-expanded="false" aria-label="Lihat rincian {{ $summary['plat'] }} tujuan {{ $summary['tujuan'] }}">{{ $summary['plat'] }}</button></td>''')
start=s.index('<p class="text-sm text-gray-600 mb-5">');s=s[:start]+'''@foreach($summaries as $summaryIndex => $summary)
@php
    $rowNumbers = array_map('intval', explode(',', $summary['numbers']));
    $detailRows = $rows->filter(fn ($row) => in_array($row['no'], $rowNumbers, true));
@endphp
<section id="analysis-detail-{{ $summaryIndex }}" class="analysis-detail-panel" hidden aria-labelledby="analysis-detail-title-{{ $summaryIndex }}">
    <div class="flex items-center justify-between gap-3 mb-3">
        <h3 id="analysis-detail-title-{{ $summaryIndex }}" class="text-base font-bold text-gray-900" tabindex="-1">Rincian {{ $summary['plat'] }} - {{ $summary['tujuan'] }}</h3>
        <button type="button" class="js-analysis-detail-close text-sm text-blue-700 underline">Tutup rincian</button>
    </div>
    <p class="text-sm text-gray-600 mb-3">{{ $detailRows->count() }} baris untuk plat dan tujuan yang dipilih. Perbedaan angka dijelaskan berdasarkan komponen tersimpan. Alasan operasional biaya tambahan memerlukan memo atau bukti pendukung.</p>
    <div class="overflow-x-auto">
        <table class="analysis-table" style="min-width:1900px">
            <thead><tr>@foreach(['NO','TGL SJ','TGL TT','NO SURAT JALAN','NO PLAT','SIZE','RIT S','RIT K','NO BUKTI','TUJUAN','ONGKOS','UJ','NOMINAL','KETERANGAN'] as $heading)<th>{{ $heading }}</th>@endforeach</tr></thead>
            <tbody>
            @foreach($detailRows as $row)
                <tr>
                    @foreach(['no','tgl_sj','tgl_tt','sj','plat','size','rit_s','rit_k','bukti','tujuan'] as $key)<td>{{ $row[$key] }}</td>@endforeach
                    @foreach(['ongkos','uj','nominal'] as $key)<td class="analysis-money">{{ number_format($row[$key], 0, ',', '.') }}</td>@endforeach
                    <td style="min-width:400px">{{ $row['keterangan'] }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><td colspan="10"><strong>Subtotal plat dan tujuan terpilih</strong></td>@foreach(['ongkos','uj','nominal'] as $key)<td class="analysis-money"><strong>{{ number_format($detailRows->sum($key), 0, ',', '.') }}</strong></td>@endforeach<td></td></tr></tfoot>
        </table>
    </div>
    <p class="text-sm text-gray-500 mt-3">ONGKOS mengikuti master saat analisis; NOMINAL mengikuti nilai item tersimpan. Tanda - berarti data tidak tersedia.</p>
</section>
@endforeach
''';p.write_text(s,encoding='utf-8')
p=Path('resources/views/pranota-ongkos-truk/analysis-modal.blade.php');s=p.read_text(encoding='utf-8-sig');s=s.replace('#analysis-dialog::backdrop', '#analysis-dialog [hidden] { display: none !important; }\n#analysis-dialog::backdrop',1);needle="    document.getElementById('analysis-close').addEventListener";idx=s.index(needle);s=s[:idx]+'''    content.addEventListener('click', event => {
        const trigger = event.target.closest('.js-analysis-detail');
        if (trigger) {
            const panel = content.querySelector('#' + trigger.dataset.detailId);
            if (!panel) return;
            content.querySelectorAll('.analysis-detail-panel').forEach(item => { item.hidden = true; });
            content.querySelectorAll('.js-analysis-detail').forEach(item => item.setAttribute('aria-expanded', 'false'));
            panel.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            panel.querySelector('h3').focus({ preventScroll: true });
            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        const close = event.target.closest('.js-analysis-detail-close');
        if (close) {
            const panel = close.closest('.analysis-detail-panel');
            panel.hidden = true;
            const active = content.querySelector('.js-analysis-detail[aria-expanded="true"]');
            active?.setAttribute('aria-expanded', 'false');
            active?.focus();
        }
    });
'''+s[idx:];p.write_text(s,encoding='utf-8')
