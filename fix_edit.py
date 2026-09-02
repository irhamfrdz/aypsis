import re

with open('resources/views/biaya-kapal/edit/_js-opp-opt.blade.php', 'r') as f:
    content = f.read()

new_init = """
    function initializeOppOptSections() {
        if (!oppOptSectionsContainer) return;
        oppOptSectionsContainer.innerHTML = '';
        oppOptSectionCounter = 0;
        
        if (typeof existingOppOptSections !== 'undefined' && existingOppOptSections.length > 0) {
            existingOppOptSections.forEach(data => {
                const section = addOppOptSection(true); // true means isEdit to avoid default row
                const sectionIndex = section.getAttribute('data-opp-opt-section-index');
                
                const voySel = section.querySelector('.opp-opt-voyage-select');
                voySel.innerHTML = `<option value="${data.voyage}">${data.voyage}</option>`;
                voySel.value = data.voyage;
                voySel.setAttribute('data-saved-voyage', data.voyage);
                voySel.disabled = false;
                
                // Kapal & Voyage
                const kapalSel = section.querySelector('.opp-opt-kapal-select');
                if (kapalSel) {
                    kapalSel.value = data.kapal;
                }

                // Bongkaran
                const bongkaranCheckbox = section.querySelector('.opp-opt-is-bongkaran-checkbox');
                if (bongkaranCheckbox && data.is_bongkaran) {
                    bongkaranCheckbox.checked = true;
                }

                // Klasifikasi
                const klasifikasiSel = section.querySelector('.opp-opt-klasifikasi-select');
                if (klasifikasiSel && data.barang && data.barang.length > 0) {
                    klasifikasiSel.value = data.barang[0].klasifikasi || '';
                    toggleOpslagMode(sectionIndex, klasifikasiSel.value);
                }

                // Hidden values
                const totalHidden = section.querySelector('.opp-opt-section-total-hidden');
                if (totalHidden) totalHidden.value = data.total_nominal || 0;
                
                const dpHidden = section.querySelector('.opp-opt-section-dp-hidden');
                if (dpHidden) dpHidden.value = data.dp || 0;
                
                const sisaHidden = section.querySelector('.opp-opt-section-sisa-hidden');
                if (sisaHidden) sisaHidden.value = data.sisa_pembayaran || 0;

                // Barang
                if (data.barang && data.barang.length > 0) {
                    data.barang.forEach(b => {
                        addBarangToOppOptSection(sectionIndex, b.manifest_id, b);
                    });
                } else {
                    addBarangToOppOptSection(sectionIndex);
                }
            });
            calculateTotalFromAllOppOptSections();
        } else {
            addOppOptSection();
        }
    }
"""

content = re.sub(r'    function initializeOppOptSections\(\) \{[\s\S]*?    \}', new_init, content, count=1)

content = content.replace('function addOppOptSection() {', 'function addOppOptSection(isEdit = false) {')
content = content.replace('// Add first barang input as default\n        addBarangToOppOptSection(sectionIndex);', '// Add first barang input as default\n        if (!isEdit) {\n            addBarangToOppOptSection(sectionIndex);\n        }')

content = content.replace('function(sectionIndex, selectedManifestIds = []) {', 'function(sectionIndex, selectedManifestIds = [], existingValues = null) {')

content = content.replace("const isSelected = selectedManifestIds.includes(manifest.id) ? 'selected' : '';", "const selectedIdsArr = Array.isArray(selectedManifestIds) ? selectedManifestIds : (selectedManifestIds ? [selectedManifestIds] : []);\n            const isSelected = selectedIdsArr.includes(manifest.id) ? 'selected' : '';")

inject_values = """
        targetContainer.appendChild(inputGroup);
        
        // If we have existing values, apply them
        if (existingValues) {
            if (isOpslag) {
                const jenisSel = inputGroup.querySelector('.opp-opt-jenis-ukuran-select-item');
                if (jenisSel && existingValues.jenis_ukuran) jenisSel.value = existingValues.jenis_ukuran;
                
                const jumlahInput2 = inputGroup.querySelector('.opp-opt-jumlah-input-item');
                if (jumlahInput2 && existingValues.jumlah) jumlahInput2.value = existingValues.jumlah;
                
                const tarifInput2 = inputGroup.querySelector('.opp-opt-tarif-input-item');
                if (tarifInput2 && existingValues.tarif) tarifInput2.value = existingValues.tarif;
                
                const catatanInput2 = inputGroup.querySelector('.opp-opt-catatan-input-item');
                if (catatanInput2 && existingValues.catatan) catatanInput2.value = existingValues.catatan;
                
                const totalSpan = inputGroup.querySelector('.opp-opt-total-span');
                if (totalSpan && existingValues.tarif && existingValues.jumlah) {
                    totalSpan.textContent = 'Rp ' + (existingValues.tarif * existingValues.jumlah).toLocaleString('id-ID');
                }
            } else {
                const biayaSel = inputGroup.querySelector('.opp-opt-biaya-select-item');
                if (biayaSel && existingValues.klasifikasi_biaya_id) biayaSel.value = existingValues.klasifikasi_biaya_id;
                
                const tarifInput2 = inputGroup.querySelector('.opp-opt-tarif-input-item');
                if (tarifInput2 && existingValues.tarif) tarifInput2.value = existingValues.tarif;
                
                const vendorInput2 = inputGroup.querySelector('.opp-opt-vendor-input-item');
                if (vendorInput2 && existingValues.vendor) vendorInput2.value = existingValues.vendor;
                
                const catatanInput2 = inputGroup.querySelector('.opp-opt-catatan-input-item');
                if (catatanInput2 && existingValues.catatan) catatanInput2.value = existingValues.catatan;
                
                const barangSel = inputGroup.querySelector('.opp-opt-barang-select-item');
                if (barangSel && selectedManifestIds) {
                    const ids = Array.isArray(selectedManifestIds) ? selectedManifestIds : [selectedManifestIds];
                    ids.forEach(id => {
                        let opt = Array.from(barangSel.options).find(o => o.value == id);
                        if (!opt) {
                            barangSel.insertAdjacentHTML('beforeend', `<option value="${id}" selected>${existingValues.manifest_label || 'Manifest ID: ' + id}</option>`);
                        }
                    });
                }
            }
        }
"""
content = content.replace('targetContainer.appendChild(inputGroup);', inject_values)

with open('resources/views/biaya-kapal/edit/_js-opp-opt.blade.php', 'w') as f:
    f.write(content)
