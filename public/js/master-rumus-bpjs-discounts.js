(function () {
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('bpjs-discount-modal');
        const list = document.getElementById('bpjs-discount-list');
        const search = document.getElementById('bpjs-discount-search');
        const status = document.getElementById('bpjs-discount-status');
        const count = document.getElementById('bpjs-discount-count');
        const save = document.getElementById('bpjs-discount-save');
        let employees = [], selected = new Set(), trigger = null, generation = 0, busy = false;
        function close() {
            if (busy) return;
            generation++;
            modal.classList.add('hidden');
            trigger?.focus();
        }
        function render() {
            const query = search.value.trim().toLocaleLowerCase('id-ID');
            list.replaceChildren();
            const visible = employees.filter(employee =>
                [employee.nama_lengkap, employee.nik, employee.tipe_label, employee.group].join(' ').toLocaleLowerCase('id-ID').includes(query));
            visible.forEach(employee => {
                const label = document.createElement('label');
                label.className = 'flex items-center gap-3 py-3 border-b border-gray-100 cursor-pointer';
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'rounded border-gray-300 text-indigo-600';
                checkbox.checked = selected.has(employee.unique_id);
                checkbox.addEventListener('change', () => {
                    if (checkbox.checked) selected.add(employee.unique_id);
                    else selected.delete(employee.unique_id);
                    count.textContent = selected.size + ' karyawan tanpa diskon';
                });
                const text = document.createElement('span');
                const name = document.createElement('span');
                name.className = 'block text-sm font-semibold text-gray-800';
                name.textContent = employee.nama_lengkap;
                const info = document.createElement('span');
                info.className = 'block text-xs text-gray-500';
                info.textContent = [employee.nik || '-', employee.tipe_label, employee.group, employee.cabang].filter(Boolean).join(' · ');
                text.append(name, info);
                label.append(checkbox, text);
                list.appendChild(label);
            });
            if (!visible.length) {
                const empty = document.createElement('p');
                empty.className = 'py-5 text-sm text-gray-500';
                empty.textContent = employees.length ? 'Tidak ada hasil pencarian.' : 'Tidak ada karyawan yang sesuai group, tipe, dan cabang rumus ini.';
                list.appendChild(empty);
            }
            count.textContent = selected.size + ' karyawan tanpa diskon';
        }
        document.querySelectorAll('.bpjs-discount-exclusions-button').forEach(button => {
            button.addEventListener('click', async () => {
                trigger = button;
                const token = ++generation;
                employees = [];
                selected = new Set();
                modal.classList.remove('hidden');
                search.value = '';
                search.disabled = true;
                document.getElementById('bpjs-discount-close').focus();
                list.replaceChildren();
                count.textContent = '0 karyawan tanpa diskon';
                status.textContent = 'Memuat karyawan sesuai group...';
                save.disabled = true;
                document.getElementById('bpjs-discount-groups').textContent = '';
                try {
                    const response = await fetch(button.dataset.loadUrl + '?existing_ids=' + encodeURIComponent(button.dataset.ids),
                        { headers: { Accept: 'application/json' }, cache: 'no-store' });
                    if (!response.ok) throw new Error('Gagal memuat daftar karyawan. Tutup dan coba kembali.');
                    const data = await response.json();
                    if (token !== generation) return;
                    employees = data.employees;
                    const allowed = new Set(employees.map(employee => employee.unique_id));
                    selected = new Set(data.selected.filter(id => allowed.has(id)));
                    document.getElementById('bpjs-discount-groups').textContent = data.groups.join(', ');
                    status.textContent = selected.size < data.selected.length
                        ? 'Pilihan lama yang sudah tidak sesuai group akan dihapus saat Simpan.'
                        : 'Daftar hanya menampilkan karyawan yang sesuai group, tipe, dan cabang rumus.';
                    render();
                    save.disabled = false;
                    search.disabled = false;
                    search.focus();
                } catch (error) { if (token === generation) status.textContent = error.message; }
            });
        });
        save.addEventListener('click', async () => {
            if (busy || save.disabled) return;
            busy = true;
            save.disabled = true;
            list.inert = true;
            status.textContent = 'Menyimpan pengecualian diskon...';
            try {
                const response = await fetch(trigger.dataset.saveUrl, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                    body: JSON.stringify({ existing_ids: trigger.dataset.ids, diskon_pengecualian: [...selected] }),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || data.message || 'Gagal menyimpan pengecualian.');
                trigger.querySelector('.bpjs-discount-exclusions-count').textContent = data.count;
                status.textContent = 'Pengecualian diskon tersimpan.';
                busy = false;
                close();
            } catch (error) { status.textContent = error.message; }
            finally { busy = false; save.disabled = false; list.inert = false; }
        });
        search.addEventListener('input', render);
        document.getElementById('bpjs-discount-close').addEventListener('click', close);
        document.getElementById('bpjs-discount-cancel').addEventListener('click', close);
        modal.addEventListener('click', event => { if (event.target === modal) close(); });
        modal.addEventListener('keydown', event => {
            if (event.key === 'Escape') close();
            if (event.key === 'Tab') {
                const controls = [...modal.querySelectorAll('button, input')].filter(element => !element.disabled);
                const first = controls[0], last = controls[controls.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        });
    });
})();
