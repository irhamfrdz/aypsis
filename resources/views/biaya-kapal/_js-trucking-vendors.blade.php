    window.addTruckingVendorForSection = function(sourceIndex) {
        const source = document.querySelector(`.trucking-section[data-trucking-section-index="${sourceIndex}"]`);
        if (!source) return;
        const group = source.closest('.trucking-ship-group');

        const sourceKapal = source.querySelector('.trucking-kapal-select');
        const sourceVoyage = source.querySelector('.trucking-voyage-select');
        if (!sourceKapal.value || !sourceVoyage.value) {
            window.alert('Pilih kapal dan voyage terlebih dahulu sebelum menambah vendor.');
            (sourceKapal.value ? sourceVoyage : sourceKapal).focus();
            return;
        }

        const section = addTruckingSection(group);
        if (!section) return;

        const sectionIndex = section.getAttribute('data-trucking-section-index');
        section.querySelector('.trucking-kapal-select').value = sourceKapal.value;

        const voyageSelect = section.querySelector('.trucking-voyage-select');
        voyageSelect.innerHTML = sourceVoyage.innerHTML;
        voyageSelect.disabled = false;
        voyageSelect.value = sourceVoyage.value;

        loadBlsForTruckingSection(sectionIndex, sourceVoyage.value);
        applyTruckingShipSettings(group);
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        section.querySelector('.trucking-vendor-select').focus({ preventScroll: true });
    };

    function updateTruckingVendorLabels(group) {
        group.querySelectorAll('.trucking-vendor-title').forEach((title, index) => {
            title.innerHTML = `<i class="fas fa-truck mr-2"></i>Vendor Trucking ${index + 1}`;
        });
    }

    function updateTruckingShipLabels() {
        document.querySelectorAll('#trucking_sections_container .trucking-ship-title').forEach((title, index) => {
            title.innerHTML = `<i class="fas fa-ship mr-2"></i>Kapal Trucking ${index + 1}`;
        });
    }

    function toggleTruckingCargoCost(sectionIndex) {
        const section = document.querySelector(`.trucking-section[data-trucking-section-index="${sectionIndex}"]`);
        if (!section) return;

        const isCargo = section.querySelector('.trucking-vendor-select').value === 'CARGO';
        section.querySelector('.trucking-cargo-cost-wrapper').classList.toggle('hidden', !isCargo);
        section.querySelector('.trucking-cargo-cost-input').required = isCargo;
    }

    function createTruckingShipSummary(group) {
        const summary = document.createElement('div');
        summary.className = 'trucking-ship-summary mt-4 rounded-lg border border-blue-300 bg-white p-4';
        summary.innerHTML = `
            <h5 class="mb-3 font-semibold text-blue-900">Total biaya kapal ini</h5>
            <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
                <div>Total kontainer 20ft <strong class="trucking-ship-total-20ft block text-gray-900">Rp 0</strong></div>
                <div>Total kontainer 40ft <strong class="trucking-ship-total-40ft block text-gray-900">Rp 0</strong></div>
                <div>Total Cargo <strong class="trucking-ship-total-cargo block text-gray-900">Rp 0</strong></div>
                <div>Subtotal <strong class="trucking-ship-subtotal block text-gray-900">Rp 0</strong></div>
                <div>Adjustment <strong class="trucking-ship-adjustment block text-gray-900">Rp 0</strong></div>
                <div>PPh <strong class="trucking-ship-pph block text-gray-900">Rp 0</strong></div>
                <div class="rounded-lg bg-blue-600 p-3 text-white">Total Biaya <strong class="trucking-ship-total block text-lg">Rp 0</strong></div>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-4 border-t border-blue-100 pt-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adjustment Subtotal</label>
                    <input type="text" class="trucking-ship-adjustment-input mt-1 w-full rounded-lg border border-yellow-300 bg-yellow-50 px-3 py-2" value="0" inputmode="numeric">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keterangan Adjustment</label>
                    <input type="text" class="trucking-ship-notes-input mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Keterangan adjustment">
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700 md:col-span-2">
                    <input type="checkbox" class="trucking-ship-pph-half-input rounded border-gray-300 text-blue-600">
                    Gunakan PPh 0,5% (standar 2%)
                </label>
            </div>
        `;
        group.appendChild(summary);

        const adjustmentInput = summary.querySelector('.trucking-ship-adjustment-input');
        adjustmentInput.addEventListener('input', function() {
            const isNegative = this.value.trim().startsWith('-');
            const amount = parseFloat(this.value.replace(/[^0-9]/g, '')) || 0;
            this.value = (isNegative && amount > 0 ? '-' : '') + (amount ? amount.toLocaleString('id-ID') : '0');
            applyTruckingShipSettings(group);
        });
        summary.querySelector('.trucking-ship-notes-input').addEventListener('input', () => applyTruckingShipSettings(group));
        summary.querySelector('.trucking-ship-pph-half-input').addEventListener('change', () => applyTruckingShipSettings(group));
    }

    function applyTruckingShipSettings(group) {
        const summary = group.querySelector('.trucking-ship-summary');
        const adjustment = parseFloat(summary.querySelector('.trucking-ship-adjustment-input').value.replace(/\./g, '').replace(',', '.')) || 0;
        const notes = summary.querySelector('.trucking-ship-notes-input').value;
        const halfPercent = summary.querySelector('.trucking-ship-pph-half-input').checked;

        group.querySelectorAll('.trucking-section').forEach((section, index) => {
            section.querySelector('.trucking-adjustment-input').value = index === 0 ? String(adjustment) : '0';
            section.querySelector('.trucking-notes-adjustment-input').value = index === 0 ? notes : '';
            section.querySelector('.trucking-pph-half-input').checked = halfPercent;
            calculateTruckingTotals(section.getAttribute('data-trucking-section-index'));
        });
    }

    function updateTruckingShipTotals() {
        const parseAmount = value => parseFloat(String(value || '0').replace(/\./g, '').replace(',', '.')) || 0;
        const formatAmount = value => `Rp ${Math.round(value).toLocaleString('id-ID')}`;
        let grandTotal = 0;

        document.querySelectorAll('#trucking_sections_container .trucking-ship-group').forEach(group => {
            const totals = { ft20: 0, ft40: 0, cargo: 0, subtotal: 0, adjustment: 0, pph: 0, total: 0 };
            const sections = [...group.querySelectorAll('.trucking-section')];
            sections.forEach(section => {
                totals.ft20 += parseAmount(section.querySelector('.trucking-total-20ft-input').value);
                totals.ft40 += parseAmount(section.querySelector('.trucking-total-40ft-input').value);
                const sectionSubtotal = parseAmount(section.querySelector('.trucking-subtotal-input').value);
                totals.subtotal += sectionSubtotal;
                if (section.querySelector('.trucking-vendor-select').value === 'CARGO') {
                    totals.cargo += sectionSubtotal;
                }
                totals.adjustment += parseAmount(section.querySelector('.trucking-adjustment-input').value);
            });

            const summary = group.querySelector('.trucking-ship-summary');
            const percent = summary.querySelector('.trucking-ship-pph-half-input').checked ? 0.5 : 2;
            let remainingPph = Math.round((totals.subtotal + totals.adjustment) * percent / 100);
            sections.forEach((section, index) => {
                const base = parseAmount(section.querySelector('.trucking-subtotal-input').value)
                    + parseAmount(section.querySelector('.trucking-adjustment-input').value);
                const pph = index === sections.length - 1 ? remainingPph : Math.round(base * percent / 100);
                remainingPph -= pph;
                section.querySelector('.trucking-pph-input').value = Math.round(pph).toLocaleString('id-ID');
                section.querySelector('.trucking-total-input').value = Math.round(base - pph).toLocaleString('id-ID');
                totals.pph += pph;
                totals.total += base - pph;
            });

            summary.querySelector('.trucking-ship-total-20ft').textContent = formatAmount(totals.ft20);
            summary.querySelector('.trucking-ship-total-40ft').textContent = formatAmount(totals.ft40);
            summary.querySelector('.trucking-ship-total-cargo').textContent = formatAmount(totals.cargo);
            summary.querySelector('.trucking-ship-subtotal').textContent = formatAmount(totals.subtotal);
            summary.querySelector('.trucking-ship-adjustment').textContent = formatAmount(totals.adjustment);
            summary.querySelector('.trucking-ship-pph').textContent = formatAmount(totals.pph);
            summary.querySelector('.trucking-ship-total').textContent = formatAmount(totals.total);
            grandTotal += totals.total;
        });

        return grandTotal;
    }

    document.getElementById('trucking_sections_container')?.addEventListener('change', function(event) {
        if (!event.target.matches('.trucking-kapal-select, .trucking-voyage-select')) return;

        const section = event.target.closest('.trucking-section');
        const group = section?.closest('.trucking-ship-group');
        if (!group || group.querySelector('.trucking-section') !== section) return;

        group.querySelectorAll('.trucking-section').forEach(vendorSection => {
            const vendorIndex = vendorSection.getAttribute('data-trucking-section-index');
            delete vendorSection.dataset.truckingUseSavedTotals;
            vendorSection.querySelector('.trucking-subtotal-input').value = '0';
            if (event.target.matches('.trucking-kapal-select')) {
                if (vendorSection !== section) {
                    vendorSection.querySelector('.trucking-kapal-select').value = event.target.value;
                    const voyageSelect = vendorSection.querySelector('.trucking-voyage-select');
                    voyageSelect.value = '';
                    voyageSelect.disabled = true;
                }
                loadBlsForTruckingSection(vendorIndex, '');
            } else if (vendorSection !== section) {
                const voyageSelect = vendorSection.querySelector('.trucking-voyage-select');
                voyageSelect.innerHTML = event.target.innerHTML;
                voyageSelect.disabled = !event.target.value;
                voyageSelect.value = event.target.value;
                loadBlsForTruckingSection(vendorIndex, event.target.value);
            }
            calculateTruckingTotals(vendorIndex);
        });
    });
