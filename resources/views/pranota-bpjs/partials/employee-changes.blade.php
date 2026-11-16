<div class="flex justify-end mt-4 mb-4">
    <div class="inline-flex rounded-lg shadow-sm" role="group" aria-label="Perubahan Karyawan">
        <button type="button" id="employee-changes-toggle" aria-expanded="false" aria-controls="employee-changes-panel"
            class="inline-flex items-center gap-2 rounded-l-lg border border-teal-600 bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600">
            <i class="fas fa-users" aria-hidden="true"></i>
            Perubahan Karyawan
            <span id="employee-changes-count" hidden role="status" aria-live="polite" aria-atomic="true"></span>
            <i id="employee-changes-chevron" class="fas fa-chevron-down text-xs" aria-hidden="true"></i>
        </button>
        <button type="button" id="employee-changes-refresh" title="Perbarui Perubahan Karyawan" aria-label="Perbarui dan tampilkan Perubahan Karyawan"
            class="inline-flex items-center rounded-r-lg border border-teal-600 border-l-teal-500 bg-teal-600 px-3 py-2 text-white hover:bg-teal-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600">
            <i class="fas fa-sync-alt" aria-hidden="true"></i>
        </button>
    </div>
</div>
<section id="employee-changes-panel" hidden class="bg-white rounded-xl border border-gray-200 p-6 mb-6" aria-labelledby="employee-changes-title">
    <div class="flex items-center justify-between gap-4 mb-3">
        <h2 id="employee-changes-title" class="text-sm font-bold text-gray-800">Perubahan Karyawan — <span id="employee-changes-period"></span></h2>
    </div>
    <p class="text-xs text-gray-500 mb-4">Karyawan baru dan berhenti pada periode terpilih, beserta data BPJS terbaru.</p>
    <p class="ec-legend"><span class="ec-legend-dot" aria-hidden="true"></span>Warna kuning menandai perubahan yang belum dilihat. Penanda hilang saat tabel dibuka kembali.</p>
    <p id="employee-changes-status" role="status" aria-live="polite" class="text-sm text-gray-600"></p>
    <div id="employee-changes-content" class="space-y-4"></div>
    <p class="ec-note">Karyawan baru mengikuti tanggal input. Karyawan berhenti mengikuti riwayat perubahan atau tanggal berhenti dalam periode. Nominal iuran dapat dilihat melalui Hitung Semua Karyawan.</p>
</section>
@push('styles')
<style>
    #employee-changes-count:not([hidden]) { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px; padding: 0 6px; border-radius: 999px; background: #e11d48; color: #fff; font-size: 11px; font-weight: 700; line-height: 1; font-variant-numeric: tabular-nums; }
    #employee-changes-panel .ec-section { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
    #employee-changes-panel .ec-heading { display: flex; align-items: center; gap: 10px; padding: 14px 18px; margin: 0; font-size: 13px; font-weight: 600; color: #1e293b; border-bottom: 1px solid #e2e8f0; }
    #employee-changes-panel .ec-badge { display: inline-flex; align-items: center; border-radius: 6px; padding: 2px 8px; font-size: 11px; font-weight: 600; background: #f1f5f9; color: #475569; }
    #employee-changes-panel .ec-badge-new { background: #ecfdf5; color: #047857; }
    #employee-changes-panel .ec-badge-stopped { background: #fff1f2; color: #be123c; }
    #employee-changes-panel .ec-scroll { overflow: auto; max-height: 520px; }
    #employee-changes-panel .ec-table { width: 100%; text-align: left; border-collapse: separate; border-spacing: 0; font-size: 12px; color: #334155; }
    #employee-changes-panel .ec-table th { position: sticky; top: 0; z-index: 1; padding: 11px 18px; background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 600; white-space: nowrap; border-bottom: 1px solid #e2e8f0; }
    #employee-changes-panel .ec-table td { padding: 16px 18px; vertical-align: top; border-bottom: 1px solid #f1f5f9; min-width: 125px; }
    #employee-changes-panel .ec-table tr:last-child td { border-bottom: 0; }
    #employee-changes-panel .ec-table tbody tr:hover { background: #fafcfd; }
    #employee-changes-panel .ec-table tbody tr.ec-unread { background: #fffbeb; }
    #employee-changes-panel .ec-table tbody tr.ec-unread:hover { background: #fef3c7; }
    #employee-changes-panel .ec-table tr.ec-unread td:first-child { border-left: 3px solid #f59e0b; padding-left: 15px; }
    #employee-changes-panel .ec-unread-label { display: inline-flex; margin-top: 6px; border-radius: 4px; padding: 2px 6px; color: #92400e; background: #fef3c7; font-size: 10px; font-weight: 600; }
    #employee-changes-panel .ec-legend { display: flex; align-items: center; gap: 7px; margin-bottom: 16px; font-size: 11px; color: #64748b; }
    #employee-changes-panel .ec-legend-dot { width: 9px; height: 9px; flex-shrink: 0; border-radius: 3px; background: #fcd34d; }
    #employee-changes-panel .ec-primary { font-weight: 600; color: #0f172a; line-height: 1.6; }
    #employee-changes-panel .ec-identity { min-width: 180px; }
    #employee-changes-panel .ec-secondary { color: #64748b; font-size: 11px; margin-top: 4px; line-height: 1.6; }
    #employee-changes-panel .ec-number { font-variant-numeric: tabular-nums; white-space: nowrap; }
    #employee-changes-panel .ec-bpjs { min-width: 205px; }
    #employee-changes-panel .ec-meta { display: flex; gap: 12px; justify-content: space-between; margin-top: 6px; font-size: 11px; }
    #employee-changes-panel .ec-meta dt { color: #64748b; }
    #employee-changes-panel .ec-meta dd { margin: 0; text-align: right; overflow-wrap: anywhere; }
    #employee-changes-panel .ec-empty { padding: 24px 18px; color: #64748b; font-size: 12px; text-align: center; background: #fafcfd; }
    #employee-changes-panel .ec-note { margin-top: 16px; font-size: 11px; color: #64748b; line-height: 1.7; }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const month = document.getElementById('periode_bulan');
    const year = document.getElementById('periode_tahun');
    const content = document.getElementById('employee-changes-content');
    const status = document.getElementById('employee-changes-status');
    const panel = document.getElementById('employee-changes-panel');
    const toggle = document.getElementById('employee-changes-toggle');
    const countBadge = document.getElementById('employee-changes-count');
    const seenByPeriod = new Map();
    const viewerId = @json(auth()->id());
    const entryId = (type, employee) => JSON.stringify([type, employee.id, employee.created_at, employee.tanggal_berhenti]);
    function seenEntries(period) {
        if (!seenByPeriod.has(period)) {
            let stored = [];
            try {
                const value = JSON.parse(localStorage.getItem('bpjs-employee-changes-seen:' + viewerId + ':' + period) || '[]');
                if (Array.isArray(value)) stored = value.filter(item => typeof item === 'string');
            } catch (_) { /* Keep read state in memory if storage is unavailable. */ }
            seenByPeriod.set(period, new Set(stored));
        }
        return seenByPeriod.get(period);
    }
    function setOpen(open) {
        panel.hidden = !open;
        if (open) countBadge.hidden = true;
        toggle.setAttribute('aria-expanded', String(open));
        document.getElementById('employee-changes-chevron').className = 'fas text-xs fa-chevron-' + (open ? 'up' : 'down');
    }
    let sequence = 0;
    let loading = false;
    let renderedSignature = null;
    const money = value => value ? 'Rp ' + Number(value).toLocaleString('id-ID') : '—';
    function element(tag, className, text) {
        const node = document.createElement(tag);
        node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }
    function cell(row, value) {
        const td = document.createElement('td');
        if (value) td.textContent = value;
        row.append(td);
        return td;
    }
    const date = value => value ? new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
    async function load({ background = false } = {}) {
        if (background && loading) return;
        const current = ++sequence;
        const period = year.value + '-' + month.value;
        if (!background) {
            content.replaceChildren();
            countBadge.hidden = true;
            countBadge.textContent = '';
            toggle.removeAttribute('title');
        }
        if (!month.checkValidity() || !year.checkValidity() || !year.value) return;
        loading = true;
        if (!background) status.textContent = 'Memuat perubahan karyawan...';
        document.getElementById('employee-changes-period').textContent = new Date(Number(year.value), Number(month.value) - 1, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        try {
            const url = new URL(@json(route('pranota-bpjs.employee-changes')));
            url.searchParams.set('bulan', month.value);
            url.searchParams.set('tahun', year.value);
            const response = await fetch(url, { headers: { Accept: 'application/json' }, cache: 'no-store' });
            if (!response.ok) throw new Error('Gagal memuat perubahan karyawan. Klik Perbarui Perubahan untuk mencoba lagi.');
            const data = await response.json();
            if (current !== sequence) return;
            status.textContent = '';
            const signature = JSON.stringify([period, data]);
            // Preserve highlights and reading position when the server data has not changed.
            if (background && signature === renderedSignature) return;
            const scrollPositions = Array.from(content.querySelectorAll('.ec-scroll'), node => ({ top: node.scrollTop, left: node.scrollLeft }));
            content.replaceChildren();
            const seen = seenEntries(period);
            for (const [key, title] of [['baru', 'Karyawan Baru'], ['berhenti', 'Karyawan Berhenti']]) {
                const section = document.createElement('div');
                section.className = 'ec-section';
                const heading = document.createElement('h3');
                heading.className = 'ec-heading';
                heading.textContent = title;
                heading.append(element('span', 'ec-badge ' + (key === 'baru' ? 'ec-badge-new' : 'ec-badge-stopped'), data[key].length + ' orang'));
                section.append(heading);
                if (!data[key].length) {
                    const empty = document.createElement('p');
                    empty.className = 'ec-empty';
                    empty.textContent = 'Tidak ada data pada periode ini.';
                    section.append(empty);
                } else {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'ec-scroll';
                    const table = document.createElement('table');
                    table.className = 'ec-table';
                    table.setAttribute('aria-label', title);
                    const head = table.createTHead().insertRow();
                    for (const label of ['Karyawan', 'Tanggal Input', 'Tanggal Berhenti', 'BPJS Kesehatan / JKN', 'BPJS Ketenagakerjaan', 'Cabang BPJS']) {
                        const th = document.createElement('th');
                        th.scope = 'col';
                        th.textContent = label;
                        head.append(th);
                    }
                    const body = table.createTBody();
                    for (const employee of data[key]) {
                        const row = body.insertRow();
                        const unread = !seen.has(entryId(key, employee));
                        if (unread) row.classList.add('ec-unread');
                        const identity = cell(row);
                        identity.className = 'ec-identity';
                        identity.append(element('div', 'ec-primary', employee.nama_lengkap), element('div', 'ec-secondary ec-number', 'NIK ' + (employee.nik || '—')));
                        if (unread) identity.append(element('span', 'ec-unread-label', 'Perubahan baru'));
                        cell(row, date(employee.created_at)).className = 'ec-number';
                        cell(row, date(employee.tanggal_berhenti)).className = 'ec-number';
                        for (const [number, type, group, dpp, state] of [
                            ['jkn', 'tipe_jkn', 'group_jkn', 'dpp_jkn', 'status_jkn'],
                            ['no_ketenagakerjaan', 'tipe_bp_jamsostek', 'group_bp_jamsostek', 'dpp_bp_jamsostek', 'status_bp_jamsostek']
                        ]) {
                            const detail = cell(row);
                            detail.className = 'ec-bpjs';
                            detail.append(element('div', employee[number] ? 'ec-primary ec-number' : 'ec-secondary', employee[number] || 'Nomor belum diisi'));
                            detail.append(element('div', 'ec-secondary', 'Status: ' + (employee[state] || '—')));
                            const meta = document.createElement('dl');
                            for (const [label, value] of [['Tipe', employee[type]], ['Group', employee[group]], ['DPP', money(employee[dpp])]]) {
                                const pair = element('div', 'ec-meta');
                                pair.append(element('dt', '', label), element('dd', label === 'DPP' ? 'ec-primary ec-number' : '', value || '—'));
                                meta.append(pair);
                            }
                            detail.append(meta);
                        }
                        cell(row, employee.cabang_bpjs);
                    }
                    wrapper.append(table);
                    section.append(wrapper);
                }
                content.append(section);
            }
            if (background) content.querySelectorAll('.ec-scroll').forEach((node, index) => {
                node.scrollTop = scrollPositions[index]?.top || 0;
                node.scrollLeft = scrollPositions[index]?.left || 0;
            });
            renderedSignature = signature;
            const entries = ['baru', 'berhenti'].flatMap(type => data[type].map(employee =>
                entryId(type, employee)
            ));
            if (!panel.hidden) {
                entries.forEach(entry => seen.add(entry));
                try {
                    localStorage.setItem('bpjs-employee-changes-seen:' + viewerId + ':' + period, JSON.stringify([...seen]));
                } catch (_) { /* The in-memory read state still applies. */ }
            }
            const unread = entries.filter(entry => !seen.has(entry)).length;
            countBadge.textContent = String(unread);
            countBadge.hidden = !panel.hidden || unread === 0;
            countBadge.setAttribute('aria-label', unread + ' perubahan karyawan belum dilihat pada periode terpilih');
            toggle.title = unread + ' perubahan belum dilihat · ' + data.baru.length + ' karyawan baru, ' + data.berhenti.length + ' karyawan berhenti';
        } catch (error) {
            if (current === sequence) {
                status.textContent = error.message;
                toggle.title = 'Jumlah perubahan belum tersedia. Klik untuk mencoba lagi.';
            }
        } finally {
            if (current === sequence) loading = false;
        }
    }
    toggle.addEventListener('click', () => {
        setOpen(panel.hidden);
        if (!panel.hidden) load();
    });
    month.addEventListener('change', load);
    year.addEventListener('change', load);
    document.getElementById('employee-changes-refresh').addEventListener('click', () => {
        setOpen(true);
        load();
    });
    document.getElementById('btn-refresh-bpjs')?.addEventListener('click', load);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) load({ background: true });
    });
    window.addEventListener('focus', () => load({ background: true }));
    // Refresh both the open table and the notification without resetting unchanged rows.
    window.setInterval(() => {
        if (!document.hidden) load({ background: true });
    }, 15000);
    load();
});
</script>
@endpush
