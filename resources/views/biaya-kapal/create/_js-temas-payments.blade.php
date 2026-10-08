    function updateTemasDpSelection(section) {
        const select = section.querySelector('.temas-dp-reference');
        const selected = [...select.selectedOptions].filter(option => option.value);
        const first = selected[0];
        section.dataset.dpAmount = selected.reduce((sum, option) => sum + Math.round(Number(option.dataset.amount || 0) * 100), 0) / 100;
        [...select.options].forEach(option => {
            option.disabled = !option.value;
        });
        return first;
    }

    function calculateTemasDpUsageForAllSections() {
        const balances = new Map();
        document.querySelectorAll('.temas-section').forEach(section => {
            if (section.querySelector('.temas-payment-mode')?.value !== 'pelunasan_dp') return;
            const select = section.querySelector('.temas-dp-reference');
            const selected = [...(select.selectedOptions || [])].filter(option => option.value)
                .sort((a, b) => (a.dataset.date || '9999-12-31').localeCompare(b.dataset.date || '9999-12-31') || Number(a.value) - Number(b.value));
            const total = Math.round(Number(section.querySelector('.grand-total-value-temas').value || 0) * 100);
            let available = 0;
            let used = 0;
            selected.forEach(option => {
                const balance = balances.has(option.value) ? balances.get(option.value) : Math.round(Number(option.dataset.amount || 0) * 100);
                available += balance;
                const consumed = Math.min(balance, Math.max(0, total - used));
                used += consumed;
                balances.set(option.value, balance - consumed);
            });
            section.dataset.dpAmount = available / 100;
            const paid = section.querySelector('.temas-dp-paid');
            if (paid) paid.textContent = temasMoney(available / 100);
            section.querySelector('.temas-settlement-dp').textContent = '- ' + temasMoney(used / 100);
            const remaining = section.querySelector('.temas-settlement-balance');
            if (remaining) remaining.textContent = temasMoney((available - used) / 100);
            section.querySelector('.temas-cash-value').value = Math.max(0, total - used) / 100;
            section.querySelector('.temas-cash-display').textContent = temasMoney(Math.max(0, total - used) / 100);
            select.setCustomValidity(selected.length && available <= 0 ? 'Saldo DP sudah habis atau digunakan pada bagian sebelumnya.' : (total <= 0 ? 'Tagihan pelunasan harus lebih dari nol.' : ''));
        });
    }

    function updateTemasPaymentMode(section) {
        const mode = section.querySelector('.temas-payment-mode').value;
        const isDp = mode === 'dp';
        const isSettlement = mode === 'pelunasan_dp';
        section.querySelector('.temas-account-label').textContent = isDp ? 'Nomor Virtual Account' : 'Nomor Rekening';
        section.querySelector('.temas-account-input').placeholder = isDp ? 'Masukkan nomor virtual account' : 'Masukkan nomor rekening';
        const details = section.querySelector('.temas-billing-details');
        details.disabled = isDp;
        details.classList.toggle('hidden', isDp);
        section.querySelector('.temas-dp-input-wrap').classList.toggle('hidden', !isDp);
        section.querySelector('.temas-dp-metadata').classList.toggle('hidden', !isDp);
        section.querySelector('.temas-dp-date').disabled = !isDp;
        section.querySelector('.temas-dp-date').required = isDp;
        section.querySelector('.temas-dp-bank').disabled = !isDp;
        section.querySelector('.temas-dp-bank').required = isDp;
        section.querySelector('.temas-dp-description').disabled = !isDp;
        section.querySelector('.temas-dp-amount').disabled = !isDp;
        section.querySelector('.temas-dp-amount').required = isDp;
        section.querySelector('.temas-dp-reference-wrap').classList.toggle('hidden', !isSettlement);
        section.querySelector('.temas-dp-reference').disabled = !isSettlement;
        section.querySelector('.temas-dp-reference').required = isSettlement;
        section.querySelector('.temas-payment-help').textContent = isDp
            ? 'Isi nominal DP yang dibayar. Tagihan akhir dan rincian kontainer diisi saat pelunasan.'
            : isSettlement ? 'Pilih kapal/voyage tagihan dan referensi saldo DP. Saldo bisa berasal dari kapal/voyage berbeda. Kelebihan DP tetap tersedia untuk tagihan berikutnya.'
            : 'Isi biaya per kontainer. Seluruh nilai tagihan dicatat sebagai pembayaran langsung.';
        section.querySelector('.temas-tax-help').classList.toggle('hidden', isSettlement);
        ['pph', 'ppn', 'materai', 'admin', 'adjustment'].forEach(key => {
            const display = section.querySelector('.' + key + '-display-temas');
            display.parentElement.classList.toggle('hidden', isSettlement);
            display.parentElement.querySelectorAll('input').forEach(input => input.disabled = isSettlement);
        });
        section.querySelector('.grand-total-display-temas').parentElement.querySelector('p').classList.toggle('hidden', isSettlement);
        section.querySelector('.kapal-select-temas').disabled = false;
        const voyageSelect = section.querySelector('.voyage-select-temas');
        const voyageInput = section.querySelector('.voyage-input-temas');
        voyageSelect.disabled = !voyageInput.classList.contains('hidden') || !section.querySelector('.kapal-select-temas').value;
        voyageInput.disabled = voyageInput.classList.contains('hidden');
        section.querySelector('.voyage-manual-btn-temas').disabled = false;
        const selectedDp = isSettlement && Boolean(section.querySelector('.temas-dp-reference').value);
        section.querySelector('.temas-dp-summary').classList.toggle('hidden', !selectedDp);
        section.querySelector('.temas-dp-paid').textContent = temasMoney(selectedDp ? section.dataset.dpAmount : 0);
        section.querySelector('.temas-settlement-breakdown').classList.toggle('hidden', !isSettlement);
        calculateTemasSectionTotal(Number(section.dataset.sectionIndex));
    }

    async function loadTemasDps(section) {
        const select = section.querySelector('.temas-dp-reference');
        const status = section.querySelector('.temas-dp-status');
        const request = section.dpRequest = (section.dpRequest || 0) + 1;
        status.textContent = 'Memuat saldo DP...';
        try {
            const invoiceQuery = section.dataset.invoiceId ? '?biaya_kapal_id=' + encodeURIComponent(section.dataset.invoiceId) : '';
            const response = await fetch('{{ url("biaya-kapal/temas-dp-candidates") }}' + invoiceQuery, {headers: {'Accept': 'application/json'}});
            if (!response.ok) throw new Error('DP');
            const data = await response.json();
            if (!section.isConnected || request !== section.dpRequest) return;
            const selected = [...select.selectedOptions].filter(o => o.value).map(o => o.cloneNode(true));
            select.replaceChildren(new Option('Pilih DP yang akan dilunasi', ''));
            (data.data || []).forEach(dp => {
                const option = new Option(dp.label, dp.id);
                option.dataset.amount = dp.saldo_dp ?? dp.nominal_dibayar;
                option.dataset.date = dp.tanggal_dp || '';
                option.dataset.kapal = dp.kapal;
                option.dataset.voyage = dp.voyage;
                select.add(option);
            });
            selected.forEach(saved => {
                let option = [...select.options].find(o => o.value === saved.value);
                if (!option) { select.add(saved); option = saved; }
                option.selected = true;
            });
            updateTemasDpSelection(section);
            updateTemasPaymentMode(section);
            status.textContent = data.data?.length ? 'Saldo DP bisa digunakan lintas kapal dan voyage.' : 'Tidak ada DP dengan saldo tersisa.';
        } catch (error) {
            if (section.isConnected && request === section.dpRequest) status.textContent = 'Daftar DP gagal dimuat. Klik Muat ulang daftar DP.';
        }
    }

    function hydrateTemasSection(section, data) {
        const kapal = section.querySelector('.kapal-select-temas');
        if (![...kapal.options].some(o => o.value === data.kapal)) kapal.add(new Option(data.kapal, data.kapal));
        kapal.value = data.kapal;
        section.querySelector('.voyage-select-temas').replaceChildren(new Option(data.voyage, data.voyage));
        section.querySelector('.voyage-select-temas').disabled = false;
        section.querySelector('.voyage-input-temas').value = data.voyage;
        section.querySelector('.temas-payment-mode').value = data.payment_mode || 'lunas';
        section.querySelector('.temas-dp-amount').value = data.nominal_dibayar || '';
        section.querySelector('.temas-dp-date').value = data.tanggal_dp || '';
        section.querySelector('.temas-dp-bank').value = data.nama_bank || '';
        section.querySelector('.temas-dp-description').value = data.keterangan_dp || '';
        section.dataset.dpAmount = data.dp_diperhitungkan || 0;
        section.dataset.invoiceId = data.biaya_kapal_id || '';
        const references = data.dp_references || (data.dp_stage_id ? [{id: data.dp_stage_id, nominal_dibayar: data.dp_diperhitungkan, kapal: data.kapal, voyage: data.voyage}] : []);
        references.forEach(dp => {
            const balance = dp.saldo_dp ?? dp.nominal_dibayar;
            const option = new Option('DP #' + dp.id + ' ' + dp.kapal + ' / ' + dp.voyage + ' - Saldo ' + temasMoney(balance), dp.id, false, true);
            Object.assign(option.dataset, {amount: balance, date: dp.tanggal_dp || '', kapal: dp.kapal, voyage: dp.voyage});
            section.querySelector('.temas-dp-reference').add(option);
        });
        updateTemasDpSelection(section);
        const index = section.dataset.sectionIndex;
        ['penerima', 'nomor_rekening', 'nomor_referensi', 'tanggal_invoice_vendor', 'keterangan'].forEach(field => {
            section.querySelector('[name="temas[' + index + '][' + field + ']"]').value = data[field] || '';
        });
        for (const [key, field] of [['pph', 'pph'], ['ppn', 'ppn'], ['materai', 'biaya_materai'], ['admin', 'biaya_admin'], ['adjustment', 'adjustment']]) {
            section.querySelector('.' + key + '-value-temas').value = data[field] || 0;
            const display = section.querySelector('.' + key + '-display-temas');
            display.value = temasMoney(data[field]);
            if (key === 'pph' || key === 'ppn') {
                display.setAttribute('data-manual-' + key, 'true');
                section.querySelector('.' + key + '-active-temas').checked = Boolean(data[key + '_active']);
            }
        }
        if (data.payment_mode !== 'dp') {
            section.querySelector('.temas-container-cards').replaceChildren();
            const cards = new Map();
            (data.types || []).forEach(item => {
                const number = item.nomor_kontainer || '';
                const nomorBl = item.nomor_bl || ('legacy-' + (item.bl_id || number));
                let card = cards.get(nomorBl);
                let row;
                if (!card) {
                    card = addTemasContainer(section);
                    cards.set(nomorBl, card);
                    card.dataset.nomorBl = item.nomor_bl || '';
                    card.dataset.containerNumbers = number;
                    card.querySelector('.temas-container-size').value = temasSize(item.size);
                    card.dataset.blId = item.bl_id || '';
                    refreshTemasOptions(section, card);
                    row = card.querySelector('.temas-type-item');
                } else row = addTemasCost(section, card);
                const select = row.querySelector('.type-select-temas');
                const type = String(item.type_id || 'MANUAL');
                const locationSelect = row.querySelector('.lokasi-select-temas');
                if (item.lokasi && ![...locationSelect.options].some(option => option.value === item.lokasi)) {
                    locationSelect.add(new Option(item.lokasi, item.lokasi));
                }
                locationSelect.value = item.lokasi || '';
                refreshTemasCostTypes(row, type);
                if (![...select.options].some(o => o.value === type)) select.add(new Option(item.manual_name, type));
                select.value = type;
                row.querySelector('.temas-manual-label').classList.toggle('hidden', type !== 'MANUAL');
                row.querySelector('.type-manual-input-temas').value = item.manual_name || '';
                row.querySelector('.type-manual-input-temas').required = type === 'MANUAL';
                const savedPerContainer = item.is_per_container;
                row.querySelector('.temas-per-container').checked = savedPerContainer === null || savedPerContainer === undefined
                    ? !temasIsSingleCharge(item.manual_name)
                    : Boolean(savedPerContainer);
                row.dataset.perContainerTouched = 'true';
                row.querySelector('.price-input-temas').value = item.harga;
                row.querySelector('.quantity-input-temas').value = item.kuantitas || 1;
                row.querySelectorAll('.temas-activity').forEach((box, i) => box.checked = Boolean(i ? item.is_bongkar : item.is_muat));
            });
        }
    }
