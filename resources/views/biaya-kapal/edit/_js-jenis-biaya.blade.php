    
    nominalInput.addEventListener('input', function(e) {
        // Remove all non-numeric characters
        let value = this.value.replace(/\D/g, '');
        
        // Format with thousand separator
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        
        this.value = value;
        
        // Recalculate based on jenis biaya
        const selectedText = selectedJenisBiaya.nama || '';
        if (selectedText.toLowerCase().includes('dokumen') || selectedText.toLowerCase().includes('listrik') || selectedText.toLowerCase().includes('trucking')) {
            calculatePphDokumen();
        } else if (selectedText.toLowerCase().includes('penumpukan')) {
            calculatePpnPenumpukan();
        } else if (selectedText.toLowerCase().includes('buruh')) {
            calculateSisaPembayaran();
        }
    });
    
    // Format DP input
    dpInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateSisaPembayaran();
    });
    
    // Format PPN input
    ppnInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Format PPH input
    pphInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Format Biaya Materai input
    biayaMateraiInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        this.value = value;
        calculateTotalBiaya();
    });
    
    // Calculate Sisa Pembayaran = Nominal - DP (for Biaya Buruh)
    function calculateSisaPembayaran() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        const dp = parseInt(dpInput.value.replace(/\D/g, '') || 0);
        
        const sisa = nominal - dp;
        sisaPembayaranInput.value = sisa > 0 ? sisa.toLocaleString('id-ID') : '0';
    }
    
    // Calculate PPH Dokumen (2% dari nominal) and Grand Total (for Biaya Dokumen)
    function calculatePphDokumen() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPH = 2% dari nominal
        const pph = Math.round(nominal * 0.02);
        pphDokumenInput.value = pph > 0 ? pph.toLocaleString('id-ID') : '0';
        
        // Grand Total = Nominal - PPH
        const grandTotal = nominal - pph;
        grandTotalDokumenInput.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '0';
    }
    
    // Calculate PPH Penumpukan (2% dari nominal) for Biaya Penumpukan
    function calculatePphPenumpukan() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPH = 2% dari nominal
        const pph = Math.round(nominal * 0.02);
        pphInput.value = pph > 0 ? pph.toLocaleString('id-ID') : '0';
        
        // Recalculate total biaya
        calculateTotalBiaya();
    }
    
    // Calculate PPN Penumpukan (11% dari nominal) for Biaya Penumpukan
    function calculatePpnPenumpukan() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        
        // PPN = 11% dari nominal
        const ppn = Math.round(nominal * 0.11);
        ppnInput.value = ppn > 0 ? ppn.toLocaleString('id-ID') : '0';
        
        // Auto-calculate PPH after PPN
        calculatePphPenumpukan();
    }
    
    // Calculate Total Biaya = Nominal + PPN + Materai - PPH
    function calculateTotalBiaya() {
        const nominal = parseInt(nominalInput.value.replace(/\D/g, '') || 0);
        const ppn = parseInt(ppnInput.value.replace(/\D/g, '') || 0);
        const pph = parseInt(pphInput.value.replace(/\D/g, '') || 0);
        const materai = parseInt(biayaMateraiInput.value.replace(/\D/g, '') || 0);
        
        const total = nominal + ppn + materai - pph;
        totalBiayaInput.value = total > 0 ? total.toLocaleString('id-ID') : '';
    }

    // Before form submit, remove formatting from all currency fields
    document.querySelector('form').addEventListener('submit', function(e) {
        nominalInput.value = nominalInput.value.replace(/\./g, '');
        ppnInput.value = ppnInput.value.replace(/\./g, '');
        pphInput.value = pphInput.value.replace(/\./g, '');
        if (dpInput.value) {
            dpInput.value = dpInput.value.replace(/\./g, '');
        }
        if (sisaPembayaranInput.value) {
            sisaPembayaranInput.value = sisaPembayaranInput.value.replace(/\./g, '');
        }
        if (totalBiayaInput.value) {
            totalBiayaInput.value = totalBiayaInput.value.replace(/\./g, '');
        }
        // Clean Biaya Dokumen fields
        if (pphDokumenInput && pphDokumenInput.value) {
            pphDokumenInput.value = pphDokumenInput.value.replace(/\./g, '');
        }
        if (grandTotalDokumenInput && grandTotalDokumenInput.value) {
            grandTotalDokumenInput.value = grandTotalDokumenInput.value.replace(/\./g, '');
        }
        // Clean Biaya Materai field
        if (biayaMateraiInput && biayaMateraiInput.value) {
            biayaMateraiInput.value = biayaMateraiInput.value.replace(/\./g, '');
        }
        // Clean Biaya Air fields
        if (jasaAirInput && jasaAirInput.value) {
            jasaAirInput.value = jasaAirInput.value.replace(/\./g, '');
        }
        if (pphAirInput && pphAirInput.value) {
            pphAirInput.value = pphAirInput.value.replace(/\./g, '');
        }
        if (grandTotalAirInput && grandTotalAirInput.value) {
            grandTotalAirInput.value = grandTotalAirInput.value.replace(/\./g, '');
        }
        // Sanitize per-section numeric hidden inputs to ensure validation accepts numbers
        document.querySelectorAll('.sub-total-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
        document.querySelectorAll('.pph-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
        document.querySelectorAll('.grand-total-value').forEach(el => {
            el.value = String(el.value).replace(/\./g, '').replace(/[^0-9\-]/g, '');
        });
    });

    // Update file name display
    function updateFileName(input) {
        const fileNameDisplay = document.getElementById('file-name');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // Convert to MB
            fileNameDisplay.innerHTML = `<i class="fas fa-file-alt mr-2 text-blue-600"></i><span class="font-medium">File terpilih:</span> ${fileName} (${fileSize} MB)`;
        } else {
            fileNameDisplay.innerHTML = '';
        }
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-red-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

