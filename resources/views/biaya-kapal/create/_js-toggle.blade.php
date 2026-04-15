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
        if(labuhTambatWrapper) labuhTambatWrapper.classList.add('hidden');
        if(perijinanWrapper) perijinanWrapper.classList.add('hidden');
        if(freightWrapper) freightWrapper.classList.add('hidden');
        if(meratusWrapper) meratusWrapper.classList.add('hidden');
        
        // Reset nominal input properties
        if(nominalInput) {
            nominalInput.removeAttribute('readonly');
            nominalInput.classList.remove('bg-gray-100');
        }
        
        // Reset required attributes
        if(nominalInput) nominalInput.setAttribute('required', 'required');
        if(penerimaInput) penerimaInput.setAttribute('required', 'required');

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

            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
            if (freightWrapper) freightWrapper.classList.add('hidden');
            clearAllFreightSections();
        }
        // Show OPP/OPT wrapper if "Biaya OPP/OPT" is selected
        else if (selectedText.toLowerCase().includes('opp/opt')) {
            oppOptWrapper.classList.remove('hidden');
            initializeOppOptSections();
            
            // Hide Nama Kapal and Nomor Voyage fields (already in section)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            
            // Hide BL wrapper for Biaya OPP/OPT
            blWrapper.classList.add('hidden');
            clearBlSelections();
            
            // Hide PPN/PPH fields for Biaya OPP/OPT
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            
            // Hide vendor wrapper for Biaya OPP/OPT
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            
            // Hide PPH Dokumen fields for Biaya OPP/OPT
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            pphDokumenInput.value = '0';
            grandTotalDokumenInput.value = '0';
            
            // Hide Biaya Air fields for Biaya OPP/OPT
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            
            // Show DP fields for Biaya OPP/OPT (Like Buruh)
            dpWrapper.classList.remove('hidden');
            sisaPembayaranWrapper.classList.remove('hidden');
            calculateSisaPembayaran();
            
            // Hide TKBM wrapper for Biaya OPP/OPT
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper for Biaya OPP/OPT
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Trucking wrapper for Biaya OPP/OPT
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
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

            // Hide Trucking wrapper for Biaya Listrik
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
        }
        // Show Trucking fields if "Biaya Trucking" is selected
        else if (selectedText.toLowerCase().includes('trucking')) {
            // Show Trucking multi kapal wrapper
            if (truckingWrapper) truckingWrapper.classList.remove('hidden');
            initializeTruckingSections();
            
            // Show global summary fields for Biaya Trucking
            // HIDDEN AS PER REQUEST - VALUES DERIVED FROM SECTIONS
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(pphDokumenWrapper) pphDokumenWrapper.classList.add('hidden');
            if(grandTotalDokumenWrapper) grandTotalDokumenWrapper.classList.add('hidden');
            
            // Remove required attributes for hidden fields
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) {
                // If multiple sections, global penerima might be redundant but let's keep it for now
                penerimaInput.removeAttribute('required');
                if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            }
            
            // Hide standard kapal/voyage/bl fields (already in trucking sections)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide other standard fields
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');
            
            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
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
            if (biayaMateraiInput) biayaMateraiInput.value = '0';
            if (pphDokumenInput) pphDokumenInput.value = '0';
            if (grandTotalDokumenInput) grandTotalDokumenInput.value = '0';
            nominalInput.value = '';
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
        }
        // Show fields for "Biaya Labuh Tambat"
        else if (selectedText.toLowerCase().includes('labuh tambat')) {
            // Show Biaya Labuh Tambat multi kapal wrapper
            if (labuhTambatWrapper) labuhTambatWrapper.classList.remove('hidden');
            initializeLabuhTambatSections();
            
            // Hide standard kapal/voyage/bl fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide Nomor Referensi for Biaya Labuh Tambat
            if (nomorReferensiWrapper) nomorReferensiWrapper.classList.add('hidden');
            
            // Hide standard fields
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');
            
            // Remove required attributes for hidden fields
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');
            
            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            nominalInput.value = '';
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
        }
        // Show barang wrapper if "Biaya Buruh" is selected
        else if (selectedText.toLowerCase().includes('buruh')) {
            barangWrapper.classList.remove('hidden');
            initializeKapalSections();
            
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
            
            // Hide Trucking wrapper for Biaya Buruh
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
        }
        // Show TKBM wrapper if "Biaya KTKBM" is selected
        else if (selectedText.toLowerCase().includes('ktkbm')) {
            document.getElementById('tkbm_wrapper').classList.remove('hidden');
            initializeTkbmSections();
            
            // Hide Operasional wrapper for Biaya TKBM
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
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

            // Hide Trucking wrapper for Biaya TKBM
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();

            // Hide Stuffing wrapper for Biaya TKBM
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            if (freightWrapper) freightWrapper.classList.add('hidden');
            clearAllFreightSections();
        }
        // Show Stuffing fields if "Biaya Stuffing" is selected
        else if (selectedText.toLowerCase().includes('stuffing')) {
            // Show Stuffing multi kapal wrapper
            if (stuffingWrapper) stuffingWrapper.classList.remove('hidden');
            initializeStuffingSections();
            
            // Hide standard kapal/voyage/bl fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide other standard fields
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');
            
            // Remove required attributes
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');
            
            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';

            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            if (operasionalWrapper) operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();

            // Hide Trucking wrapper
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
        }
        // Show THC fields if "Biaya THC" is selected
        else if (selectedText.toLowerCase().includes('thc')) {
            // Show THC multi kapal wrapper
            if (thcWrapper) thcWrapper.classList.remove('hidden');
            initializeTHCSections();

            // Hide standard kapal/voyage/bl fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide other standard fields
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');

            // Remove required attributes
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');

            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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

            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';

            // Hide Stuffing wrapper
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();

            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }

            // Hide Operasional wrapper
            if (operasionalWrapper) operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();

            // Hide Trucking wrapper
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();

            // Hide Perlengkapan wrapper
            if (perlengkapanWrapper) perlengkapanWrapper.classList.add('hidden');
            clearAllPerlengkapanSections();
        }
        // Show LOLO fields if "Biaya Lolo" is selected
        else if (selectedValue === 'KB043' || selectedText.toLowerCase().includes('lolo')) {
            // Show Lolo multi kapal wrapper
            if (loloWrapper) loloWrapper.classList.remove('hidden');
            initializeLoloSections();

            // Hide standard kapal/voyage/bl fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide other standard fields
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');

            // Remove required attributes
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');

            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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

            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';

            // Hide Stuffing wrapper
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();

            // Hide THC wrapper
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();

            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }

            // Hide Operasional wrapper
            if (operasionalWrapper) operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();

            // Hide Trucking wrapper
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();

            // Hide Perlengkapan wrapper
            if (perlengkapanWrapper) perlengkapanWrapper.classList.add('hidden');
            clearAllPerlengkapanSections();
            
            // Hide Labuh Tambat wrapper
            if (labuhTambatWrapper) labuhTambatWrapper.classList.add('hidden');
            clearAllLabuhTambatSections();
        }
        // Show STORAGE fields if "BIAYA STORAGE" is selected
        else if (selectedValue === 'KB044' || selectedText.toLowerCase().includes('storage')) {
            // Show Storage multi kapal wrapper
            if (storageWrapper) storageWrapper.classList.remove('hidden');
            initializeStorageSections();

            // Hide standard kapal/voyage/bl fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide other standard fields
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');

            // Remove required attributes
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');

            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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

            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';

            // Hide Stuffing, THC, LOLO, TKBM, Operasional, Trucking, Perlengkapan, Labuh Tambat
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            if (loloWrapper) loloWrapper.classList.add('hidden');
            clearAllLoloSections();
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            if (operasionalWrapper) operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            if (labuhTambatWrapper) labuhTambatWrapper.classList.add('hidden');
            clearAllLabuhTambatSections();
            if (freightWrapper) freightWrapper.classList.add('hidden');
            clearAllFreightSections();
        }
        // Show FREIGHT fields if "Biaya Freight" is selected
        else if (selectedText.toLowerCase().includes('freight')) {
            // Show Freight multi kapal wrapper
            if (freightWrapper) freightWrapper.classList.remove('hidden');
            initializeFreightSections();

            // Hide standard kapal/voyage/bl fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide other standard fields
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');

            // Remove required attributes
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');

            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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

            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';

            // Hide other wrappers
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            if (loloWrapper) loloWrapper.classList.add('hidden');
            clearAllLoloSections();
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            if (operasionalWrapper) operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
            if (perlengkapanWrapper) perlengkapanWrapper.classList.add('hidden');
            clearAllPerlengkapanSections();
            if (labuhTambatWrapper) labuhTambatWrapper.classList.add('hidden');
            clearAllLabuhTambatSections();
        }
        // Show Perlengkapan fields if "Biaya Perlengkapan" is selected
        else if (selectedText.toLowerCase().includes('perlengkapan')) {
            // Show Perlengkapan wrapper
            if (perlengkapanWrapper) perlengkapanWrapper.classList.remove('hidden');
            initializePerlengkapanSections();

            // Hide standard kapal/voyage/bl multi-select fields (perlengkapan has its own)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Show nominal, penerima, nama vendor, nomor rekening, nomor referensi  
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaWrapper) penerimaWrapper.classList.remove('hidden');
            if(penerimaInput) penerimaInput.setAttribute('required', 'required');
            if(namaVendorWrapper) namaVendorWrapper.classList.remove('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.remove('hidden');
            if(nomorReferensiWrapper) nomorReferensiWrapper.classList.remove('hidden');

            // Hide all type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            if (operasionalWrapper) operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            if (freightWrapper) freightWrapper.classList.add('hidden');
            clearAllFreightSections();
            if (perijinanWrapper) perijinanWrapper.classList.add('hidden');
            clearAllPerijinanSections();
        }
        // Show Perijinan fields if "Biaya Perijinan" is selected
        else if (selectedText.toLowerCase().includes('perijinan')) {
            // Show Perijinan wrapper
            if (perijinanWrapper) perijinanWrapper.classList.remove('hidden');
            initializePerijinanSections();

            // Hide standard kapal/voyage/bl fields (perijinan has its own in sections)
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Setup nominal input (Calculated from sections)
            if(nominalInput) {
                nominalInput.setAttribute('readonly', 'readonly');
                nominalInput.classList.add('bg-gray-100');
            }

            // Show standard fields
            if(penerimaWrapper) penerimaWrapper.classList.remove('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.remove('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.remove('hidden');
            if(nomorReferensiWrapper) nomorReferensiWrapper.classList.remove('hidden');

            // Hide all other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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

            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';

            // Hide other multi-sections
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
            if (perlengkapanWrapper) perlengkapanWrapper.classList.add('hidden');
            clearAllPerlengkapanSections();
            if (freightWrapper) freightWrapper.classList.add('hidden');
            clearAllFreightSections();
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

            // Hide Trucking wrapper for Biaya Operasional
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
        } else if (selectedText.toLowerCase().includes('penumpukan')) {
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
            
            // Hide Trucking wrapper
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();

            // Calculate initial total
            calculateTotalBiaya();
        }
        // Show MERATUS fields if "Tagihan Meratus" is selected
        else if (selectedText.toLowerCase().includes('meratus')) {
            // Show Meratus multi kapal wrapper
            if (meratusWrapper) meratusWrapper.classList.remove('hidden');
            initializeMeratusSections();

            // Hide standard kapal/voyage/bl fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide other standard fields
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) {
                namaVendorWrapper.classList.add('hidden');
                const vendorInput = document.getElementById('nama_vendor');
                if (vendorInput) vendorInput.value = '';
            }
            if(nomorRekeningWrapper) {
                nomorRekeningWrapper.classList.add('hidden');
                const rekInput = document.getElementById('nomor_rekening');
                if (rekInput) rekInput.value = '';
            }

            // Remove required attributes
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) {
                penerimaInput.removeAttribute('required');
                penerimaInput.value = '';
                if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            }

            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
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

            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';

            // Hide other wrappers
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            if (loloWrapper) loloWrapper.classList.add('hidden');
            clearAllLoloSections();
            if (storageWrapper) storageWrapper.classList.add('hidden');
            clearAllStorageSections();
            if (freightWrapper) freightWrapper.classList.add('hidden');
            clearAllFreightSections();
            if (perlengkapanWrapper) perlengkapanWrapper.classList.add('hidden');
            clearAllPerlengkapanSections();
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
            // Removed jasa_air, pph_air, grand_total_air as requested
            // Removed global summary input resets
            
            // Clear calculated total when switching away from Biaya Buruh
            nominalInput.value = '';
            
            // Show Nama Kapal and Nomor Voyage fields for other types
            kapalWrapper.classList.remove('hidden');
            voyageWrapper.classList.remove('hidden');
            
            // Show BL wrapper for other types
            blWrapper.classList.remove('hidden');

            // Hide Trucking wrapper
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();

            // Hide Stuffing wrapper
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();

            // Hide Perlengkapan wrapper
            if (perlengkapanWrapper) perlengkapanWrapper.classList.add('hidden');
            clearAllPerlengkapanSections();
            if (meratusWrapper) meratusWrapper.classList.add('hidden');
            clearAllMeratusSections();
        }
    });
