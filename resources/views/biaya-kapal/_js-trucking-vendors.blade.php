    window.addTruckingVendorForSection = function(sourceIndex) {
        const source = document.querySelector(`.trucking-section[data-trucking-section-index="${sourceIndex}"]`);
        if (!source) return;

        const sourceKapal = source.querySelector('.trucking-kapal-select');
        const sourceVoyage = source.querySelector('.trucking-voyage-select');
        if (!sourceKapal.value || !sourceVoyage.value) {
            window.alert('Pilih kapal dan voyage terlebih dahulu sebelum menambah vendor.');
            (sourceKapal.value ? sourceVoyage : sourceKapal).focus();
            return;
        }

        const section = addTruckingSection();
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
