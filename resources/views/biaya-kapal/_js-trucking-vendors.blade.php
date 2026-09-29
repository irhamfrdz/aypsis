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

    document.getElementById('trucking_sections_container')?.addEventListener('change', function(event) {
        if (!event.target.matches('.trucking-kapal-select, .trucking-voyage-select')) return;

        const section = event.target.closest('.trucking-section');
        const group = section?.closest('.trucking-ship-group');
        if (!group || group.querySelector('.trucking-section') !== section) return;

        group.querySelectorAll('.trucking-section').forEach(vendorSection => {
            const vendorIndex = vendorSection.getAttribute('data-trucking-section-index');
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
