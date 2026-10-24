<div id="add-pranota-employees-modal" class="fixed inset-0 hidden" style="z-index: 10000010" role="dialog" aria-modal="true" aria-labelledby="add-pranota-employees-title">
    <div class="absolute inset-0 bg-gray-900/70" onclick="closeTambahKaryawanPranota()"></div>
    <div class="relative mx-auto my-4 flex max-w-5xl flex-col rounded-xl bg-white shadow-xl" style="max-height: calc(100vh - 32px); width: calc(100% - 32px)">
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 p-5">
            <div>
                <h3 id="add-pranota-employees-title" class="text-lg font-bold text-gray-900">Tambah Karyawan ke Pranota</h3>
                <p id="add-pranota-employees-period" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <button type="button" onclick="closeTambahKaryawanPranota()" class="p-2 text-gray-500 hover:text-gray-800" aria-label="Tutup pilihan karyawan">✕</button>
        </div>
        <div class="flex-1 overflow-y-auto p-5" style="min-height: 0">
            <p class="mb-4 text-sm text-gray-600">Pilih rentang tanggal dan tampilkan lembur, lalu pilih karyawan yang akan ditambahkan. Hanya tanggal yang belum masuk pranota lain yang dihitung.</p>
            <div class="mb-4 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                <div class="flex-1">
                    <label for="add-pranota-start-date" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Dari</label>
                    <input id="add-pranota-start-date" type="date" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="flex-1">
                    <label for="add-pranota-end-date" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Sampai</label>
                    <input id="add-pranota-end-date" type="date" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <button id="add-pranota-apply-range" type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Tampilkan Lembur</button>
            </div>
            <p id="add-pranota-range-info" class="mb-3 text-sm text-blue-700" role="status"></p>
            <label for="add-pranota-employees-search" class="sr-only">Cari nama atau NIK karyawan</label>
            <input id="add-pranota-employees-search" type="search" placeholder="Cari nama atau NIK karyawan..." class="mb-3 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <p id="add-pranota-employees-message" class="mb-3 text-sm text-gray-600" role="status" aria-live="polite"></p>
            <div id="add-pranota-employees-list" class="space-y-2"></div>
        </div>
        <div class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-t border-gray-200 p-5">
            <p id="add-pranota-employees-total" class="text-sm font-semibold text-gray-700">0 karyawan dipilih</p>
            <div class="flex gap-2">
                <button type="button" onclick="closeTambahKaryawanPranota()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700">Batal</button>
                <button id="add-pranota-employees-submit" type="button" disabled class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Tambahkan ke Pranota</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
(() => {
    const modal = document.getElementById('add-pranota-employees-modal');
    document.body.appendChild(modal);
    const list = document.getElementById('add-pranota-employees-list');
    const search = document.getElementById('add-pranota-employees-search');
    const message = document.getElementById('add-pranota-employees-message');
    const submit = document.getElementById('add-pranota-employees-submit');
    const startDate = document.getElementById('add-pranota-start-date');
    const endDate = document.getElementById('add-pranota-end-date');
    const applyRange = document.getElementById('add-pranota-apply-range');
    const rangeInfo = document.getElementById('add-pranota-range-info');
    const candidatesUrl = @json(route('pranota-lembur-karyawan.employee-candidates', ['id' => '__PRANOTA__']));
    const addUrl = @json(route('pranota-lembur-karyawan.add-employees', ['id' => '__PRANOTA__']));
    const rupiah = value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    let pranotaId = null, candidates = [], selected = new Set(), busy = false, loading = null, previousFocus = null, activeRange = null;
    function totals() {
        const chosen = candidates.filter(item => selected.has(item.karyawan_id));
        document.getElementById('add-pranota-employees-total').textContent = `${chosen.length} karyawan dipilih · ${rupiah(chosen.reduce((sum, item) => sum + Number(item.nominal_awal), 0))}`;
        submit.disabled = busy || !activeRange || chosen.length === 0;
    }
    const textNode = (tag, text, className = '') => {
        const element = document.createElement(tag);
        element.textContent = text;
        element.className = className;
        return element;
    };
    const dateLabel = value => value.split('-').reverse().join('/');
    const timeLabel = value => {
        const [date, time] = value.split(' ');
        return `${dateLabel(date)} ${time.slice(0, 5)}`;
    };
    function render() {
        list.replaceChildren();
        const query = search.value.trim().toLocaleLowerCase();
        const visible = candidates.filter(item => `${item.nama_lengkap} ${item.nik}`.toLocaleLowerCase().includes(query));
        visible.forEach(item => {
            const card = document.createElement('article');
            card.className = 'rounded-lg border border-gray-200 overflow-hidden';
            const label = document.createElement('label');
            label.className = 'flex items-start gap-3 p-3 cursor-pointer hover:bg-blue-50';
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = selected.has(item.karyawan_id);
            checkbox.disabled = busy;
            checkbox.className = 'mt-1';
            checkbox.addEventListener('change', () => {
                checkbox.checked ? selected.add(item.karyawan_id) : selected.delete(item.karyawan_id);
                totals();
            });
            const info = document.createElement('div');
            info.className = 'flex-1 text-sm';
            const name = document.createElement('p');
            name.className = 'font-semibold text-gray-900';
            name.textContent = `${item.nama_lengkap} (${item.nik})`;
            const detail = document.createElement('p');
            detail.className = 'mt-1 text-xs text-gray-500';
            detail.textContent = `${item.total_hari} hari · ${item.jam_lembur}`;
            const amount = document.createElement('p');
            amount.className = 'font-bold text-emerald-700';
            amount.textContent = rupiah(item.nominal_awal);
            info.append(name, detail);
            label.append(checkbox, info, amount);
            card.append(label);
            const details = document.createElement('details');
            const summary = textNode('summary', `Lihat rincian lembur per hari (${item.total_hari} hari)`, 'cursor-pointer border-t border-gray-100 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50');
            const wrapper = document.createElement('div');
            wrapper.className = 'overflow-x-auto';
            const table = document.createElement('table');
            table.className = 'min-w-full text-xs divide-y divide-gray-200';
            const head = document.createElement('thead');
            const headerRow = document.createElement('tr');
            ['Tanggal', 'Jenis Hari', 'Mulai', 'Pulang', 'Jam Dihitung', 'Uang Lembur', 'Uang Makan', 'Total'].forEach(title => headerRow.append(textNode('th', title, 'px-3 py-2 text-left whitespace-nowrap font-semibold bg-gray-50 text-gray-600')));
            head.append(headerRow);
            const body = document.createElement('tbody');
            body.className = 'divide-y divide-gray-100';
            (item.detail || []).forEach(day => {
                const row = document.createElement('tr');
                [dateLabel(day.tanggal), day.tipe_hari, timeLabel(day.waktu_lembur_masuk), timeLabel(day.waktu_lembur_pulang), `${day.durasi_jam} jam`, rupiah(day.nominal_lembur), rupiah(day.uang_makan_lembur), rupiah(day.total)]
                    .forEach(value => row.append(textNode('td', value, 'px-3 py-2 whitespace-nowrap text-gray-700')));
                body.append(row);
            });
            table.append(head, body);
            wrapper.append(table);
            details.append(summary, wrapper);
            card.append(details);
            list.append(card);
        });
        message.textContent = visible.length ? `${visible.length} karyawan tersedia.` : 'Tidak ada karyawan dengan lembur yang tersedia untuk pilihan ini.';
    }
    window.closeTambahKaryawanPranota = () => {
        if (busy) return;
        loading?.abort();
        modal.classList.add('hidden');
        previousFocus?.focus();
    };
    async function loadCandidates(useDates) {
        loading?.abort();
        loading = new AbortController();
        const signal = loading.signal;
        candidates = [];
        selected = new Set();
        activeRange = null;
        list.replaceChildren();
        totals();
        applyRange.disabled = true;
        rangeInfo.textContent = '';
        message.textContent = 'Menghitung lembur karyawan...';
        try {
            const url = new URL(candidatesUrl.replace('__PRANOTA__', pranotaId), window.location.origin);
            if (useDates) {
                url.searchParams.set('start_date', startDate.value);
                url.searchParams.set('end_date', endDate.value);
            }
            const response = await fetch(url, {headers: {'Accept': 'application/json'}, signal});
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Gagal memuat karyawan.');
            if (signal.aborted) return;
            candidates = result.data;
            startDate.value = result.start_date;
            endDate.value = result.end_date;
            rangeInfo.textContent = result.periode_berubah
                ? `Setelah penambahan, periode pranota menjadi ${result.periode_setelah_penambahan}. Data karyawan lama tetap mengikuti rentang semula.`
                : `Periode pranota: ${dateLabel(result.periode_mulai)} – ${dateLabel(result.periode_selesai)}.`;
            activeRange = {start_date: result.start_date, end_date: result.end_date};
            document.getElementById('add-pranota-employees-period').textContent = `${result.nomor_pranota} · ${result.periode}`;
            render();
            totals();
        } catch (error) {
            if (error.name !== 'AbortError') message.textContent = error.message;
        } finally {
            if (!signal.aborted) applyRange.disabled = false;
        }
    }
    window.openTambahKaryawanPranota = async id => {
        if (busy) return;
        previousFocus = document.activeElement;
        pranotaId = id;
        search.value = '';
        [startDate, endDate].forEach(input => { input.value = ''; input.min = ''; input.max = ''; });
        document.getElementById('add-pranota-employees-period').textContent = '';
        modal.classList.remove('hidden');
        search.focus();
        await loadCandidates(false);
    };
    [startDate, endDate].forEach(input => input.addEventListener('input', () => {
        loading?.abort();
        activeRange = null;
        rangeInfo.textContent = '';
        candidates = [];
        selected = new Set();
        list.replaceChildren();
        totals();
        applyRange.disabled = false;
        message.textContent = 'Rentang tanggal berubah. Klik Tampilkan Lembur untuk menghitung ulang.';
    }));
    applyRange.addEventListener('click', () => {
        if (busy || !startDate.reportValidity() || !endDate.reportValidity()) return;
        if (endDate.value < startDate.value) { message.textContent = 'Tanggal sampai harus sama dengan atau setelah tanggal dari.'; return; }
        loadCandidates(true);
    });
    search.addEventListener('input', render);
    document.addEventListener('keydown', event => {
        if (modal.classList.contains('hidden')) return;
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) window.closeTambahKaryawanPranota();
        if (event.key === 'Tab') {
            const elements = [...modal.querySelectorAll('button:not(:disabled), input:not(:disabled), summary')].filter(element => element.offsetParent !== null);
            const first = elements[0], last = elements[elements.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
        }
    });
    submit.addEventListener('click', async () => {
        if (busy || !activeRange || selected.size === 0) return;
        busy = true;
        totals();
        search.disabled = true;
        startDate.disabled = endDate.disabled = applyRange.disabled = true;
        list.querySelectorAll('input').forEach(input => input.disabled = true);
        message.textContent = 'Menambahkan karyawan dan memperbarui total pranota...';
        try {
            const response = await fetch(addUrl.replace('__PRANOTA__', pranotaId), {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({karyawan_ids: [...selected], ...activeRange}),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Gagal menambahkan karyawan.');
            window.location.reload();
        } catch (error) {
            message.textContent = error.message;
            busy = false;
            search.disabled = false;
            startDate.disabled = endDate.disabled = applyRange.disabled = false;
            list.querySelectorAll('input').forEach(input => input.disabled = false);
            totals();
        }
    });
})();
</script>
@endpush
