    function updateTemasPaymentMode(section) {
        const mode = section.querySelector('.temas-payment-mode').value;
        const isDp = mode === 'dp';
        const isSettlement = mode === 'pelunasan_dp';
        const details = section.querySelector('.temas-billing-details');
        details.disabled = isDp;
        details.classList.toggle('hidden', isDp);
        section.querySelector('.temas-dp-input-wrap').classList.toggle('hidden', !isDp);
        section.querySelector('.temas-dp-amount').disabled = !isDp;
        section.querySelector('.temas-dp-amount').required = isDp;
        section.querySelector('.temas-dp-reference-wrap').classList.toggle('hidden', !isSettlement);
        section.querySelector('.temas-dp-reference').disabled = !isSettlement;
        section.querySelector('.temas-dp-reference').required = isSettlement;
        section.querySelector('.temas-payment-help').textContent = isDp
            ? 'Isi nominal DP yang dibayar. Tagihan akhir dan rincian kontainer diisi saat pelunasan.'
            : isSettlement ? 'Pilih DP, lalu isi biaya akhir per kontainer. Pelunasan = total biaya akhir dikurangi DP satu kali.'
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
        section.querySelector('.temas-dp-paid').textContent = temasMoney(isSettlement ? section.dataset.dpAmount : 0);
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
            const selected = select.selectedOptions[0]?.cloneNode(true);
            select.replaceChildren(new Option('Pilih DP yang akan dilunasi', ''));
            (data.data || []).forEach(dp => {
                const option = new Option(dp.label, dp.id);
                option.dataset.amount = dp.nominal_dibayar;
                option.dataset.kapal = dp.kapal;
                option.dataset.voyage = dp.voyage;
                select.add(option);
            });
            if (selected?.value) {
                if (![...select.options].some(o => o.value === selected.value)) select.add(selected);
                select.value = selected.value;
            }
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
        section.dataset.dpAmount = data.dp_diperhitungkan || 0;
        if (data.dp_stage_id) {
            const option = new Option('DP ' + data.kapal + ' / ' + data.voyage + ' - ' + temasMoney(data.dp_diperhitungkan), data.dp_stage_id);
            Object.assign(option.dataset, {amount: data.dp_diperhitungkan, kapal: data.kapal, voyage: data.voyage});
            section.querySelector('.temas-dp-reference').add(option);
            section.querySelector('.temas-dp-reference').value = data.dp_stage_id;
        }
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
