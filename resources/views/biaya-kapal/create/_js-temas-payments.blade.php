    function updateTemasDpSelection(section) {
        const select = section.querySelector('.temas-dp-reference');
        const selected = [...select.selectedOptions].filter(option => option.value);
        const first = selected[0];
        section.dataset.dpAmount = selected.reduce((sum, option) => sum + Math.round(Number(option.dataset.amount || 0) * 100), 0) / 100;
        [...select.options].forEach(option => {
            option.disabled = !option.value || Boolean(first && (option.dataset.kapal !== first.dataset.kapal || option.dataset.voyage !== first.dataset.voyage));
        });
        return first;
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
            : isSettlement ? 'Pilih DP, lalu isi biaya akhir per kontainer. Pelunasan = total biaya akhir dikurangi jumlah seluruh DP yang dipilih.'
            : 'Isi biaya per kontainer. Seluruh nilai tagihan dicatat sebagai pembayaran langsung.';
        section.querySelector('.temas-tax-help').classList.toggle('hidden', isSettlement);
        ['pph', 'ppn', 'materai', 'admin', 'adjustment'].forEach(key => {
            const display = section.querySelector('.' + key + '-display-temas');
            display.parentElement.classList.toggle('hidden', isSettlement);
            display.parentElement.querySelectorAll('input').forEach(input => input.disabled = isSettlement);
        });
        section.querySelector('.grand-total-display-temas').parentElement.querySelector('p').classList.toggle('hidden', isSettlement);
        section.querySelector('.kapal-select-temas').disabled = isSettlement;
        const voyageSelect = section.querySelector('.voyage-select-temas');
        const voyageInput = section.querySelector('.voyage-input-temas');
        voyageSelect.disabled = isSettlement || !voyageInput.classList.contains('hidden') || !section.querySelector('.kapal-select-temas').value;
        voyageInput.disabled = isSettlement || voyageInput.classList.contains('hidden');
        section.querySelector('.voyage-manual-btn-temas').disabled = isSettlement;
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
        status.textContent = 'Memuat DP yang belum dilunasi...';
        try {
            const response = await fetch('{{ url("biaya-kapal/temas-dp-candidates") }}', {headers: {'Accept': 'application/json'}});
            if (!response.ok) throw new Error('DP');
            const data = await response.json();
            if (!section.isConnected || request !== section.dpRequest) return;
            const selected = [...select.selectedOptions].filter(o => o.value).map(o => o.cloneNode(true));
            select.replaceChildren(new Option('Pilih DP yang akan dilunasi', ''));
            (data.data || []).forEach(dp => {
                const option = new Option(dp.label, dp.id);
                option.dataset.amount = dp.nominal_dibayar;
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
            status.textContent = data.data?.length ? 'Pilih DP sesuai kapal dan voyage.' : 'Tidak ada DP yang belum dilunasi.';
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
        const references = data.dp_references || (data.dp_stage_id ? [{id: data.dp_stage_id, nominal_dibayar: data.dp_diperhitungkan, kapal: data.kapal, voyage: data.voyage}] : []);
        references.forEach(dp => {
            const option = new Option('DP #' + dp.id + ' ' + dp.kapal + ' / ' + dp.voyage + ' - ' + temasMoney(dp.nominal_dibayar), dp.id, false, true);
            Object.assign(option.dataset, {amount: dp.nominal_dibayar, kapal: dp.kapal, voyage: dp.voyage});
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
