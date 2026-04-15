    // Function to calculate nominal for Biaya Dokumen (vendor tariff × number of containers)
    function calculateDokumenNominal() {
        const selectedJenisBiaya = jenisBiayaSelect.options[jenisBiayaSelect.selectedIndex].text;
        
        // Only calculate if jenis biaya is "Biaya Dokumen"
        if (!selectedJenisBiaya.toLowerCase().includes('dokumen')) {
            return;
        }
        
        const selectedOption = vendorSelect.options[vendorSelect.selectedIndex];
        const biaya = selectedOption.getAttribute('data-biaya');
        const jumlahKontainer = Object.keys(selectedBls).length;
        
        console.log('Calculating Dokumen Nominal:');
        console.log('- Tarif vendor:', biaya);
        console.log('- Jumlah kontainer:', jumlahKontainer);
        
        if (biaya && biaya !== '' && biaya !== '0' && jumlahKontainer > 0) {
            const totalNominal = parseInt(biaya) * jumlahKontainer;
            const formattedNominal = totalNominal.toLocaleString('id-ID');
            nominalInput.value = formattedNominal;
            console.log('- Total nominal:', formattedNominal);
            
            // Calculate PPH and Grand Total after nominal is updated
            calculatePphDokumen();
        } else if (biaya && biaya !== '' && biaya !== '0') {
            // If vendor selected but no containers yet, show vendor tariff
            const formattedBiaya = parseInt(biaya).toLocaleString('id-ID');
            nominalInput.value = formattedBiaya;
            console.log('- No containers selected, showing vendor tariff:', formattedBiaya);
            
            // Calculate PPH and Grand Total after nominal is updated
            calculatePphDokumen();
        } else {
            nominalInput.value = '';
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
        }
    }

    // Auto-fill nominal from vendor selection
    vendorSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const biaya = selectedOption.getAttribute('data-biaya');
        
        console.log('Vendor selected:', this.value);
        console.log('Biaya from vendor:', biaya);
        
        const selectedJenisBiaya = jenisBiayaSelect.options[jenisBiayaSelect.selectedIndex].text;
        
        // If Biaya Dokumen, use the calculate function
        if (selectedJenisBiaya.toLowerCase().includes('dokumen')) {
            calculateDokumenNominal();
        } else {
            // For other jenis biaya, use original logic
            if (biaya && biaya !== '' && biaya !== '0') {
                // Format biaya with thousand separator
                const formattedBiaya = parseInt(biaya).toLocaleString('id-ID');
                nominalInput.value = formattedBiaya;
                nominalInput.focus();
                
                console.log('Nominal set to:', formattedBiaya);
            } else {
                // Clear nominal if no vendor selected or biaya is 0
                nominalInput.value = '';
            }
        }
    });

    // ============= JENIS BIAYA TOGGLE =============
    // Toggle barang wrapper based on jenis biaya
    jenisBiayaSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        const selectedText = selectedJenisBiaya.nama || '';
        
        // Reset visibility of standard fields
        if(nominalWrapper) nominalWrapper.classList.remove('hidden');
        if(penerimaWrapper) penerimaWrapper.classList.remove('hidden');
        if(namaVendorWrapper) namaVendorWrapper.classList.remove('hidden');
        if(nomorRekeningWrapper) nomorRekeningWrapper.classList.remove('hidden');
        if(nomorReferensiWrapper) nomorReferensiWrapper.classList.remove('hidden');
        
        // Reset required attributes
        if(nominalInput) nominalInput.setAttribute('required', 'required');
        if(penerimaInput) penerimaInput.setAttribute('required', 'required');

        if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();

            // Show vendor wrapper if "Biaya Dokumen" is selected
        if (selectedText.toLowerCase().includes('dokumen')) {
            vendorWrapper.classList.remove('hidden');
            
            // Show PPH Dokumen and Grand Total fields for Biaya Dokumen
            pphDokumenWrapper.classList.remove('hidden');
            grandTotalDokumenWrapper.classList.remove('hidden');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Show standard fields
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            blWrapper.classList.remove('hidden');
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Calculate PPH if nominal already filled
            if (nominalInput.value) {
                calculatePphDokumen();
            }
        }
        // Show PPH fields if "Biaya Listrik" is selected
        else if (selectedText.toLowerCase().includes('listrik')) {
            // Show PPH Dokumen and Grand Total fields for Biaya Listrik
            pphDokumenWrapper.classList.remove('hidden');
            grandTotalDokumenWrapper.classList.remove('hidden');
            
            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            
            // Show standard fields
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            blWrapper.classList.remove('hidden');
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Calculate PPH if nominal already filled
            if (nominalInput.value) {
                calculatePphDokumen();
            }
        }
        // Show PPH fields if "Biaya Trucking" is selected
        else if (selectedText.toLowerCase().includes('trucking')) {
            // Show PPH Dokumen and Grand Total fields for Biaya Trucking
            pphDokumenWrapper.classList.remove('hidden');
            grandTotalDokumenWrapper.classList.remove('hidden');
            
            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            
            // Show standard fields
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            blWrapper.classList.remove('hidden');
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Calculate PPH if nominal already filled
            if (nominalInput.value) {
                calculatePphDokumen();
            }
        }
        // Show THC wrapper if "Biaya THC" is selected
        else if (selectedText.toLowerCase().includes('thc')) {
            if (thcWrapper) thcWrapper.classList.remove('hidden');
            if (thcSectionsContainer && thcSectionsContainer.children.length === 0) {
                addTHCSection();
            }
            
            // Hide standard fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide normal nominal
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Labuh Tambat wrapper
            if (document.getElementById('labuh_tambat_wrapper')) {
                document.getElementById('labuh_tambat_wrapper').classList.add('hidden');
                clearAllLabuhTambatSections();
            }

            // Recalculate based on THC section totals
            calculateTotalFromAllTHCSections();
        }
        // Show fields for "Biaya Air"
        else if (selectedText.toLowerCase().includes('air')) {
            // Show Biaya Air multi kapal wrapper
            if (airWrapper) airWrapper.classList.remove('hidden');
            initializeAirSections();
            
            // Show summary fields (with null checks)
            // Removed jasa_air, pph_air, grand_total_air as requested
            
            // Hide standard kapal/voyage/bl fields (already in air sections)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide Nomor Referensi for Biaya Air
            if (nomorReferensiWrapper) nomorReferensiWrapper.classList.add('hidden');
            
            // Hide standard fields for Biaya Air
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');
            
            // Remove required attributes for hidden fields
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');
            
            // Hide other type-specific fields (with null checks)
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            if (vendorAirWrapper) vendorAirWrapper.classList.add('hidden');
            if (typeAirWrapper) typeAirWrapper.classList.add('hidden');
            if (kuantitasAirWrapper) kuantitasAirWrapper.classList.add('hidden');
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            
            // Reset values (with null checks)
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            biayaMateraiInput.value = '0';
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            nominalInput.value = '';
            // Removed global summary input resets
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
        }
        // Show LOLO wrapper if "Biaya Lolo" is selected
        else if (selectedValue === 'KB043' || selectedText.toLowerCase().includes('lolo')) {
            if (loloWrapper) loloWrapper.classList.remove('hidden');
            initializeLoloSections();
            
            // Hide standard fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide normal nominal
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Labuh Tambat wrapper
            if (document.getElementById('labuh_tambat_wrapper')) {
                document.getElementById('labuh_tambat_wrapper').classList.add('hidden');
                clearAllLabuhTambatSections();
            }

            // Hide Stuffing wrapper
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();

            // Hide THC wrapper
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
        }
                // Show Storage wrapper if "Storage" is selected
        else if (selectedText.toLowerCase().includes('storage')) {
            if (storageWrapper) storageWrapper.classList.remove('hidden');
            initializeStorageSections();
            
            // Hide standard fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide normal nominal
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Labuh Tambat wrapper
            if (document.getElementById('labuh_tambat_wrapper')) {
                document.getElementById('labuh_tambat_wrapper').classList.add('hidden');
                clearAllLabuhTambatSections();
            }

            // Hide Stuffing wrapper
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();

            // Hide THC wrapper
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            
            // Hide LOLO wrapper
            if (loloWrapper) loloWrapper.classList.add('hidden');
            clearAllLoloSections();
        }
        // Show barang wrapper if "Biaya Buruh" is selected
        else if (selectedText.toLowerCase().includes('buruh')) {
            barangWrapper.classList.remove('hidden');
            initializeKapalSections();
            
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();

            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper for Biaya Buruh
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields for Biaya Buruh
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper for Biaya Buruh
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for Biaya Buruh
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields for Biaya Buruh
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            // Removed jasa_air, pph_air, grand_total_air as requested
            
            // Show DP fields for Biaya Buruh
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
            
            // Hide TKBM wrapper for Biaya Buruh
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper for Biaya Buruh
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
        }
        // Show TKBM wrapper if "Biaya KTKBM" is selected
        else if (selectedText.toLowerCase().includes('ktkbm')) {
            document.getElementById('tkbm_wrapper').classList.remove('hidden');
            initializeTkbmSections();
            
            // Hide Operasional wrapper for Biaya TKBM
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();

            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper for Biaya TKBM
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields for Biaya TKBM
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper for Biaya TKBM
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for Biaya TKBM
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields for Biaya TKBM
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            // Removed jasa_air, pph_air, grand_total_air as requested
            
            // Hide Biaya Buruh fields for Biaya TKBM
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            
            // Show DP fields for Biaya TKBM
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
        }
        // Show operasional wrapper if "Operasional" is selected
        else if (selectedText.toLowerCase().includes('operasional')) {
            operasionalWrapper.classList.remove('hidden');
            if (operasionalSectionsContainer.children.length === 0) {
                addOperasionalSection();
            }
            
            // Hide other wrappers
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();

            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            // Removed jasa_air, pph_air, grand_total_air as requested
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Show DP fields for Biaya Operasional (Like Buruh)
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
        }
        // Show stuffing wrapper if "Stuffing" is selected
        else if (selectedText.toLowerCase().includes('stuffing')) {
            stuffingWrapper.classList.remove('hidden');
            if (stuffingSectionsContainer.children.length === 0) {
                addStuffingSection();
            }
            
            // Hide other wrappers
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();

            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            
            // Show DP fields (Like Buruh)
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
        }
        // Show Trucking wrapper if "Biaya Trucking" is selected
        else if (selectedText.toLowerCase().includes('trucking')) {
            if (truckingWrapper) truckingWrapper.classList.remove('hidden');
            if (truckingSectionsContainer && truckingSectionsContainer.children.length === 0) {
                addTruckingSection();
            }
            
            // Hide standard fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide normal nominal
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (labuhTambatWrapper) labuhTambatWrapper.classList.add('hidden');
            clearAllLabuhTambatSections();
        }
        // Show Labuh Tambat wrapper if "Biaya Labuh Tambat" is selected
        else if (selectedText.toLowerCase().includes('labuh tambat')) {
            if (labuhTambatWrapper) labuhTambatWrapper.classList.remove('hidden');
            if (labuhTambatSectionsContainer && labuhTambatSectionsContainer.children.length === 0) {
                addLabuhTambatSection();
            }
            
            // Hide standard fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide normal nominal
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
            
            // Hide global penerima/vendor fields (already per-kapal in labuh tambat section)
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(penerimaInput) penerimaInput.removeAttribute('required');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');
            if(nomorReferensiWrapper) nomorReferensiWrapper.classList.add('hidden');
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
        } else if (selectedText.toLowerCase().includes('penumpukan')) {
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();

            // Show PPN/PPH fields for Biaya Penumpukan
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            ppnWrapper.classList.remove('hidden');
            pphWrapper.classList.remove('hidden');
            totalBiayaWrapper.classList.remove('hidden');
            
            // Show Biaya Materai for Biaya Penumpukan
            biayaMateraiWrapper.classList.remove('hidden');
            
            // Hide DP fields for Biaya Penumpukan
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Hide vendor wrapper for Biaya Penumpukan
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for Biaya Penumpukan
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields for Biaya Penumpukan
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            // Removed jasa_air, pph_air, grand_total_air as requested
            
            // Auto-calculate PPN (11%) and PPH (2% dari nominal) for Biaya Penumpukan
            calculatePpnPenumpukan();
            
            // Show Nama Kapal and Nomor Voyage fields
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            
            // Show BL wrapper for Biaya Penumpukan
            blWrapper.classList.remove('hidden');
            
            // Hide TKBM wrapper for Biaya Penumpukan
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }

            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Calculate initial total
            calculateTotalBiaya();
        }
        // Show Perijinan wrapper if "Perijinan" is selected
        else if (selectedText.toLowerCase().includes('perijinan')) {
            if (perijinanWrapper) perijinanWrapper.classList.remove('hidden');
            if (perijinanSectionsContainer && perijinanSectionsContainer.children.length === 0) {
                addPerijinanSection();
            }
            
            // Hide standard fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide normal nominal
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            
            // Hide standard fields
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');
            if(nomorReferensiWrapper) nomorReferensiWrapper.classList.add('hidden');
            if(penerimaInput) penerimaInput.removeAttribute('required');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Stuffing wrapper
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            
            // Hide Trucking wrapper
            if (document.getElementById('trucking_wrapper')) document.getElementById('trucking_wrapper').classList.add('hidden');
            clearAllTruckingSections();
            
            // Hide Labuh Tambat wrapper
            if (document.getElementById('labuh_tambat_wrapper')) {
                document.getElementById('labuh_tambat_wrapper').classList.add('hidden');
                clearAllLabuhTambatSections();
            }
            
            // Hide THC wrapper
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            
            // Hide LOLO wrapper
            if (loloWrapper) loloWrapper.classList.add('hidden');
            clearAllLoloSections();
            
            // Hide Storage wrapper
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();
        } 
        // Show Meratus wrapper if "Meratus" is selected
        else if (selectedText.toLowerCase().includes('meratus')) {
            if (meratusWrapper) meratusWrapper.classList.remove('hidden');
            if (meratusSectionsContainer && meratusSectionsContainer.children.length === 0) {
                initializeMeratusSections();
            }
            
            // Hide standard fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide normal nominal
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide perijinan
            if (perijinanWrapper) perijinanWrapper.classList.add('hidden');
            clearAllPerijinanSections();
            
            // Hide Operasional
            if (operasionalWrapper) operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();

            // Hide Labuh Tambat
            if (labuhTambatWrapper) labuhTambatWrapper.classList.add('hidden');
            clearAllLabuhTambatSections();

            // Hide Trucking
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();

            // Hide Stuffing
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();

            // Hide THC
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();

            // Hide LOLO
            if (loloWrapper) loloWrapper.classList.add('hidden');
            clearAllLoloSections();

            // Hide Storage
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();
        } else {
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            
            // Hide TKBM wrapper for other types
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper for other types
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Perijinan wrapper for other types
            if (perijinanWrapper) perijinanWrapper.classList.add('hidden');
            clearAllPerijinanSections();
            
            // Hide PPN/PPH fields for other types
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide DP fields for other types
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            
            // Hide Biaya Materai for other types
            biayaMateraiWrapper.classList.add('hidden');
            biayaMateraiInput.value = '0';
            
            // Hide vendor wrapper for other types
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for other types
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            if (vendorAirWrapper) vendorAirWrapper.classList.add('hidden');
            if (typeAirWrapper) typeAirWrapper.classList.add('hidden');
            if (kuantitasAirWrapper) kuantitasAirWrapper.classList.add('hidden');
            // Removed jasa_air, pph_air, grand_total_air summary references
            
            // Clear calculated total when switching away from Biaya Buruh
            nominalInput.value = '';
            
            // Show Nama Kapal and Nomor Voyage fields for other types
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            
            // Show BL wrapper for other types
            blWrapper.classList.remove('hidden');
        }
    });
