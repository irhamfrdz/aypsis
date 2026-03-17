@extends('layouts.app')

@section('title', 'Kontainer Sewa Billing')
@section('page_title', 'Kontainer Sewa Billing (Baru)')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow rounded-lg border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Kontainer Sewa Billing (Modul Baru)</h2>
                <p class="text-xs text-gray-600">Modul ini terpisah dari menu lama dan menggunakan backend database sendiri.</p>
            </div>
            <button id="refreshAll" class="px-3 py-1.5 text-xs rounded bg-orange-500 hover:bg-orange-600 text-white">Refresh</button>
        </div>

        <div class="p-4">
            <div class="flex flex-wrap gap-2 mb-4" id="tabsWrap">
                <button data-tab="tab-vtz" class="tab-btn px-3 py-1.5 text-xs rounded bg-blue-600 text-white">1. Vendor / Tipe / Size</button>
                <button data-tab="tab-unit" class="tab-btn px-3 py-1.5 text-xs rounded bg-gray-100 text-gray-700">2. Master Unit</button>
                <button data-tab="tab-rate" class="tab-btn px-3 py-1.5 text-xs rounded bg-gray-100 text-gray-700">3. Master Tarif</button>
                <button data-tab="tab-trans" class="tab-btn px-3 py-1.5 text-xs rounded bg-gray-100 text-gray-700">4. Transaksi</button>
                <button data-tab="tab-bill" class="tab-btn px-3 py-1.5 text-xs rounded bg-gray-100 text-gray-700">5. Tagihan</button>
            </div>

            <div id="tab-vtz" class="tab-panel grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="border border-gray-200 rounded p-3">
                    <h3 class="text-xs font-semibold mb-2">Vendor</h3>
                    <div class="flex gap-2 mb-2"><input id="in-v" class="w-full text-xs border rounded px-2 py-1.5" placeholder="Nama vendor"><button id="btn-add-v" class="px-3 py-1.5 text-xs rounded bg-blue-600 text-white">Simpan</button></div>
                    <div id="list-v" class="text-xs text-gray-700 space-y-1"></div>
                </div>
                <div class="border border-gray-200 rounded p-3">
                    <h3 class="text-xs font-semibold mb-2">Tipe</h3>
                    <div class="flex gap-2 mb-2"><input id="in-t" class="w-full text-xs border rounded px-2 py-1.5" placeholder="Nama tipe"><button id="btn-add-t" class="px-3 py-1.5 text-xs rounded bg-blue-600 text-white">Simpan</button></div>
                    <div id="list-t" class="text-xs text-gray-700 space-y-1"></div>
                </div>
                <div class="border border-gray-200 rounded p-3">
                    <h3 class="text-xs font-semibold mb-2">Size</h3>
                    <div class="flex gap-2 mb-2"><input id="in-z" class="w-full text-xs border rounded px-2 py-1.5" placeholder="Nama size"><button id="btn-add-z" class="px-3 py-1.5 text-xs rounded bg-blue-600 text-white">Simpan</button></div>
                    <div id="list-z" class="text-xs text-gray-700 space-y-1"></div>
                </div>
            </div>

            <div id="tab-unit" class="tab-panel hidden border border-gray-200 rounded p-3">
                <h3 class="text-xs font-semibold mb-2">Master Unit</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mb-3">
                    <input id="u-no" class="text-xs border rounded px-2 py-1.5" placeholder="No Unit">
                    <select id="u-v" class="text-xs border rounded px-2 py-1.5"></select>
                    <select id="u-t" class="text-xs border rounded px-2 py-1.5"></select>
                    <select id="u-z" class="text-xs border rounded px-2 py-1.5"></select>
                    <button id="btn-add-u" class="px-3 py-1.5 text-xs rounded bg-blue-600 text-white">Simpan Unit</button>
                </div>
                <textarea id="imp-u" class="w-full text-xs border rounded px-2 py-1.5 mb-2" rows="3" placeholder="UNIT|VENDOR|TIPE|SIZE (1 baris 1 data)"></textarea>
                <button id="btn-imp-u" class="px-3 py-1.5 text-xs rounded bg-amber-500 text-white mb-3">Impor Unit</button>
                <input id="u-src" class="w-full text-xs border rounded px-2 py-1.5 mb-2" placeholder="Cari unit...">
                <div class="overflow-auto max-h-64 border rounded">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50 sticky top-0"><tr><th class="p-2 text-left">Unit</th><th class="p-2 text-left">Vendor</th><th class="p-2 text-left">Tipe/Size</th></tr></thead>
                        <tbody id="body-u"></tbody>
                    </table>
                </div>
            </div>

            <div id="tab-rate" class="tab-panel hidden border border-gray-200 rounded p-3">
                <h3 class="text-xs font-semibold mb-2">Master Tarif</h3>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-2 mb-3">
                    <select id="rt-v" class="text-xs border rounded px-2 py-1.5"></select>
                    <select id="rt-t" class="text-xs border rounded px-2 py-1.5"></select>
                    <select id="rt-z" class="text-xs border rounded px-2 py-1.5"></select>
                    <input id="rt-bln" type="number" min="0" class="text-xs border rounded px-2 py-1.5" placeholder="Tarif Bulanan">
                    <input id="rt-hr" type="number" min="0" class="text-xs border rounded px-2 py-1.5" placeholder="Tarif Harian">
                    <input id="rt-s" type="date" class="text-xs border rounded px-2 py-1.5">
                </div>
                <button id="btn-add-r" class="px-3 py-1.5 text-xs rounded bg-blue-600 text-white mb-3">Simpan Tarif</button>
                <div class="overflow-auto max-h-64 border rounded">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50 sticky top-0"><tr><th class="p-2 text-left">Vendor</th><th class="p-2 text-left">Tipe/Size</th><th class="p-2 text-right">Bulanan</th><th class="p-2 text-right">Harian</th><th class="p-2 text-left">Mulai</th></tr></thead>
                        <tbody id="body-r"></tbody>
                    </table>
                </div>
            </div>

            <div id="tab-trans" class="tab-panel hidden border border-gray-200 rounded p-3">
                <h3 class="text-xs font-semibold mb-2">Transaksi IN/OUT</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mb-3">
                    <input id="t-no" class="text-xs border rounded px-2 py-1.5" placeholder="No Unit">
                    <input id="t-in" type="date" class="text-xs border rounded px-2 py-1.5">
                    <input id="t-out" type="date" class="text-xs border rounded px-2 py-1.5">
                    <select id="t-st" class="text-xs border rounded px-2 py-1.5"><option value="B">Bulanan</option><option value="H">Harian</option></select>
                    <button id="btn-add-tr" class="px-3 py-1.5 text-xs rounded bg-blue-600 text-white">Simpan Transaksi</button>
                </div>
                <textarea id="imp-t" class="w-full text-xs border rounded px-2 py-1.5 mb-2" rows="3" placeholder="UNIT|IN|OUT|STAT(B/H)"></textarea>
                <button id="btn-imp-t" class="px-3 py-1.5 text-xs rounded bg-amber-500 text-white mb-3">Impor Transaksi</button>
                <div class="overflow-auto max-h-64 border rounded">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50 sticky top-0"><tr><th class="p-2 text-left">Unit</th><th class="p-2 text-left">In</th><th class="p-2 text-left">Out</th><th class="p-2 text-left">Mode</th></tr></thead>
                        <tbody id="body-t"></tbody>
                    </table>
                </div>
            </div>

            <div id="tab-bill" class="tab-panel hidden border border-gray-200 rounded p-3">
                <h3 class="text-xs font-semibold mb-2">Tagihan (Preview dari logika lampiran)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3">
                    <select id="bill-v" class="text-xs border rounded px-2 py-1.5"></select>
                    <input id="bill-src" class="text-xs border rounded px-2 py-1.5" placeholder="Cari no unit...">
                    <input id="adj-global" class="text-xs border rounded px-2 py-1.5" value="0" placeholder="Adj global">
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div>
                        <h4 class="text-xs font-semibold mb-2">Daftar Kandidat</h4>
                        <div class="overflow-auto max-h-64 border rounded">
                            <table class="min-w-full text-xs">
                                <thead class="bg-gray-50 sticky top-0"><tr><th class="p-2 text-left">Unit</th><th class="p-2 text-left">Periode</th><th class="p-2 text-right">Hari</th><th class="p-2 text-right">Estimasi</th><th class="p-2"></th></tr></thead>
                                <tbody id="body-atas"></tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold mb-2">Keranjang Review</h4>
                        <div class="overflow-auto max-h-64 border rounded">
                            <table class="min-w-full text-xs">
                                <thead class="bg-gray-50 sticky top-0"><tr><th class="p-2 text-left">Unit</th><th class="p-2 text-left">Periode</th><th class="p-2 text-right">BTM</th><th class="p-2 text-right">Vendor</th><th class="p-2 text-right">Selisih</th><th class="p-2"></th></tr></thead>
                                <tbody id="body-bawah"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                    <div class="p-2 border rounded bg-gray-50">Total BTM: <b id="sum-btm">0</b></div>
                    <div class="p-2 border rounded bg-gray-50">Total Vendor: <b id="sum-ven">0</b></div>
                    <div class="p-2 border rounded bg-gray-50">Selisih: <b id="sum-selisih">0</b></div>
                    <div class="p-2 border rounded bg-blue-50 text-blue-700">Grand Total: <b id="grand-total">0</b></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const routes = {
        snapshot: '{{ route('kontainer-sewa-billing.snapshot') }}',
        vendor: '{{ route('kontainer-sewa-billing.vendors.store') }}',
        type: '{{ route('kontainer-sewa-billing.types.store') }}',
        size: '{{ route('kontainer-sewa-billing.sizes.store') }}',
        unit: '{{ route('kontainer-sewa-billing.units.store') }}',
        rate: '{{ route('kontainer-sewa-billing.rates.store') }}',
        trx: '{{ route('kontainer-sewa-billing.transactions.store') }}',
        importU: '{{ route('kontainer-sewa-billing.import-units') }}',
        importT: '{{ route('kontainer-sewa-billing.import-transactions') }}',
    };

    const token = '{{ csrf_token() }}';
    const state = { vendors: [], types: [], sizes: [], units: [], rates: [], transactions: [], cart: [] };

    const fmt = (n) => Number(n || 0).toLocaleString('id-ID');
    const q = (s) => document.querySelector(s);

    function dateLabel(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return dateStr;
        const mon = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return `${String(d.getDate()).padStart(2, '0')} ${mon[d.getMonth()]} ${String(d.getFullYear()).slice(2)}`;
    }

    function toDays(a, b) {
        const ms = (new Date(b)) - (new Date(a));
        return Math.floor(ms / 86400000) + 1;
    }

    async function apiPost(url, payload) {
        const resp = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify(payload || {}),
        });
        if (!resp.ok) throw new Error('Request gagal');
        return resp.json();
    }

    async function loadSnapshot() {
        const resp = await fetch(routes.snapshot, { headers: { 'Accept': 'application/json' } });
        const data = await resp.json();
        state.vendors = data.vendors || [];
        state.types = data.types || [];
        state.sizes = data.sizes || [];
        state.units = data.units || [];
        state.rates = data.rates || [];
        state.transactions = data.transactions || [];
        renderAll();
    }

    function renderMasterLists() {
        q('#list-v').innerHTML = state.vendors.map(v => `<div>${v.name}</div>`).join('') || '<div class="text-gray-400">Belum ada data</div>';
        q('#list-t').innerHTML = state.types.map(v => `<div>${v.name}</div>`).join('') || '<div class="text-gray-400">Belum ada data</div>';
        q('#list-z').innerHTML = state.sizes.map(v => `<div>${v.name}</div>`).join('') || '<div class="text-gray-400">Belum ada data</div>';
    }

    function renderDropdowns() {
        const vOpt = state.vendors.map(v => `<option value="${v.id}">${v.name}</option>`).join('');
        const tOpt = state.types.map(v => `<option value="${v.id}">${v.name}</option>`).join('');
        const zOpt = state.sizes.map(v => `<option value="${v.id}">${v.name}</option>`).join('');
        ['#u-v','#rt-v','#bill-v'].forEach(sel => {
            const el = q(sel);
            const cur = el.value;
            el.innerHTML = '<option value="">- PILIH -</option>' + vOpt;
            el.value = cur;
        });
        ['#u-t','#rt-t'].forEach(sel => q(sel).innerHTML = '<option value="">- PILIH -</option>' + tOpt);
        ['#u-z','#rt-z'].forEach(sel => q(sel).innerHTML = '<option value="">- PILIH -</option>' + zOpt);
    }

    function renderUnits() {
        const src = (q('#u-src').value || '').toUpperCase();
        const rows = state.units.filter(u => u.unit_number.toUpperCase().includes(src));
        q('#body-u').innerHTML = rows.map(u => `<tr><td class="p-2">${u.unit_number}</td><td class="p-2">${u.vendor_name}</td><td class="p-2">${u.type_name}/${u.size_name}</td></tr>`).join('') || '<tr><td colspan="3" class="p-2 text-center text-gray-400">Tidak ada data</td></tr>';
    }

    function renderRates() {
        q('#body-r').innerHTML = state.rates.map(r => `<tr><td class="p-2">${r.vendor_name}</td><td class="p-2">${r.type_name}/${r.size_name}</td><td class="p-2 text-right">${fmt(r.monthly_rate)}</td><td class="p-2 text-right">${fmt(r.daily_rate)}</td><td class="p-2">${dateLabel(r.start_date)}</td></tr>`).join('') || '<tr><td colspan="5" class="p-2 text-center text-gray-400">Tidak ada data</td></tr>';
    }

    function renderTransactions() {
        q('#body-t').innerHTML = state.transactions.map(t => `<tr><td class="p-2">${t.unit_number}</td><td class="p-2">${dateLabel(t.date_in)}</td><td class="p-2">${dateLabel(t.date_out)}</td><td class="p-2">${t.billing_mode === 'B' ? 'Bulanan' : 'Harian'}</td></tr>`).join('') || '<tr><td colspan="4" class="p-2 text-center text-gray-400">Tidak ada data</td></tr>';
    }

    function findRate(unit, startDate) {
        const candidate = state.rates
            .filter(r => Number(r.vendor_id) === Number(unit.vendor_id) && Number(r.type_id) === Number(unit.type_id) && Number(r.size_id) === Number(unit.size_id))
            .sort((a, b) => String(b.start_date).localeCompare(String(a.start_date)));

        for (const r of candidate) {
            if (!r.start_date || r.start_date <= startDate) return r;
        }
        return candidate[0] || null;
    }

    function renderBill() {
        const vendorId = Number(q('#bill-v').value || 0);
        const src = (q('#bill-src').value || '').toUpperCase();
        if (!vendorId) {
            q('#body-atas').innerHTML = '<tr><td colspan="5" class="p-2 text-center text-gray-400">Pilih vendor terlebih dahulu</td></tr>';
            return;
        }

        let html = '';
        state.transactions.forEach(tr => {
            const unit = state.units.find(u => u.unit_number.toUpperCase() === tr.unit_number.toUpperCase());
            if (!unit || Number(unit.vendor_id) !== vendorId) return;
            if (src && !tr.unit_number.toUpperCase().includes(src)) return;

            const tIn = new Date(tr.date_in);
            const tOut = tr.date_out ? new Date(tr.date_out) : new Date();
            let curS = new Date(tIn);
            let count = 1;

            while (curS <= tOut) {
                const curE = new Date(curS);
                curE.setMonth(curE.getMonth() + 1);
                curE.setDate(curE.getDate() - 1);
                const dispE = curE >= tOut ? new Date(tOut) : new Date(curE);
                const isFull = curE < tOut;
                const days = toDays(curS, dispE);
                const label = `P${count}: ${dateLabel(curS)}-${dateLabel(dispE)}`;
                if (state.cart.find(k => k.no.toUpperCase() === tr.unit_number.toUpperCase() && k.lbl === label)) {
                    if (!isFull) break;
                    curS = new Date(curE);
                    curS.setDate(curS.getDate() + 1);
                    count += 1;
                    continue;
                }

                const rate = findRate(unit, curS.toISOString().slice(0, 10));
                const monthly = Number(rate?.monthly_rate || 0);
                const daily = Number(rate?.daily_rate || 0);
                const monthBase = curS.getMonth() === 1 ? new Date(curS.getFullYear(), 2, 0).getDate() : 30;
                const est = tr.billing_mode === 'B'
                    ? (isFull ? monthly : Math.round((monthly / monthBase) * days))
                    : daily * days;

                html += `<tr><td class="p-2">${tr.unit_number}</td><td class="p-2">${label}</td><td class="p-2 text-right">${days}</td><td class="p-2 text-right">${fmt(est)}</td><td class="p-2"><button class="px-2 py-1 text-[11px] rounded bg-blue-600 text-white" data-add='${JSON.stringify({ no: tr.unit_number, lbl: label, est, h: days }).replace(/'/g, '&apos;')}'>In</button></td></tr>`;

                if (!isFull) break;
                curS = new Date(curE);
                curS.setDate(curS.getDate() + 1);
                count += 1;
            }
        });

        q('#body-atas').innerHTML = html || '<tr><td colspan="5" class="p-2 text-center text-gray-400">Unit tidak ditemukan</td></tr>';
    }

    function renderCart() {
        q('#body-bawah').innerHTML = state.cart.map((x, i) => {
            const sel = Number(x.est || 0) - Number(x.hrgV || 0);
            return `<tr><td class="p-2">${x.no}</td><td class="p-2">${x.lbl}</td><td class="p-2 text-right">${fmt(x.est)}</td><td class="p-2 text-right"><input class="w-24 border rounded px-1 py-1 text-right" value="${x.hrgV || 0}" data-vendor='${i}'></td><td class="p-2 text-right ${sel < 0 ? 'text-red-600' : 'text-blue-600'}">${fmt(sel)}</td><td class="p-2"><button class="px-2 py-1 text-[11px] rounded bg-red-600 text-white" data-del="${i}">X</button></td></tr>`;
        }).join('') || '<tr><td colspan="6" class="p-2 text-center text-gray-400">Keranjang kosong</td></tr>';

        const totalBtm = state.cart.reduce((s, x) => s + Number(x.est || 0), 0);
        const totalVen = state.cart.reduce((s, x) => s + Number(x.hrgV || 0), 0);
        const adj = Number(q('#adj-global').value || 0);
        q('#sum-btm').textContent = fmt(totalBtm);
        q('#sum-ven').textContent = fmt(totalVen);
        q('#sum-selisih').textContent = fmt(totalBtm - totalVen + adj);
        q('#grand-total').textContent = fmt(totalVen + adj);
    }

    function renderAll() {
        renderMasterLists();
        renderDropdowns();
        renderUnits();
        renderRates();
        renderTransactions();
        renderBill();
        renderCart();
    }

    document.addEventListener('click', async (e) => {
        const tabBtn = e.target.closest('.tab-btn');
        if (tabBtn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.className = 'tab-btn px-3 py-1.5 text-xs rounded bg-gray-100 text-gray-700');
            tabBtn.className = 'tab-btn px-3 py-1.5 text-xs rounded bg-blue-600 text-white';
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            q(`#${tabBtn.dataset.tab}`).classList.remove('hidden');
            return;
        }

        if (e.target.id === 'refreshAll') {
            await loadSnapshot();
            return;
        }

        if (e.target.id === 'btn-add-v') { await apiPost(routes.vendor, { name: q('#in-v').value }); q('#in-v').value = ''; await loadSnapshot(); return; }
        if (e.target.id === 'btn-add-t') { await apiPost(routes.type, { name: q('#in-t').value }); q('#in-t').value = ''; await loadSnapshot(); return; }
        if (e.target.id === 'btn-add-z') { await apiPost(routes.size, { name: q('#in-z').value }); q('#in-z').value = ''; await loadSnapshot(); return; }

        if (e.target.id === 'btn-add-u') {
            await apiPost(routes.unit, {
                unit_number: q('#u-no').value,
                vendor_id: q('#u-v').value,
                type_id: q('#u-t').value,
                size_id: q('#u-z').value,
            });
            q('#u-no').value = '';
            await loadSnapshot();
            return;
        }

        if (e.target.id === 'btn-add-r') {
            await apiPost(routes.rate, {
                vendor_id: q('#rt-v').value,
                type_id: q('#rt-t').value,
                size_id: q('#rt-z').value,
                monthly_rate: q('#rt-bln').value || 0,
                daily_rate: q('#rt-hr').value || 0,
                start_date: q('#rt-s').value,
            });
            await loadSnapshot();
            return;
        }

        if (e.target.id === 'btn-add-tr') {
            await apiPost(routes.trx, {
                unit_number: q('#t-no').value,
                date_in: q('#t-in').value,
                date_out: q('#t-out').value || null,
                billing_mode: q('#t-st').value,
            });
            await loadSnapshot();
            return;
        }

        if (e.target.id === 'btn-imp-u') {
            await apiPost(routes.importU, { rows: q('#imp-u').value || '' });
            q('#imp-u').value = '';
            await loadSnapshot();
            return;
        }

        if (e.target.id === 'btn-imp-t') {
            await apiPost(routes.importT, { rows: q('#imp-t').value || '' });
            q('#imp-t').value = '';
            await loadSnapshot();
            return;
        }

        const addBtn = e.target.closest('[data-add]');
        if (addBtn) {
            const payload = JSON.parse(addBtn.dataset.add.replace(/&apos;/g, "'"));
            state.cart.push({ ...payload, hrgV: payload.est });
            renderBill();
            renderCart();
            return;
        }

        const delBtn = e.target.closest('[data-del]');
        if (delBtn) {
            state.cart.splice(Number(delBtn.dataset.del), 1);
            renderBill();
            renderCart();
            return;
        }
    });

    document.addEventListener('input', (e) => {
        if (e.target.id === 'u-src') renderUnits();
        if (e.target.id === 'bill-v' || e.target.id === 'bill-src') renderBill();
        if (e.target.id === 'adj-global') renderCart();
        if (e.target.matches('[data-vendor]')) {
            const idx = Number(e.target.dataset.vendor);
            state.cart[idx].hrgV = Number(e.target.value || 0);
            renderCart();
        }
    });

    loadSnapshot();
})();
</script>
@endsection
