// ============= BIAYA DOKUMEN SECTIONS MANAGEMENT =============
let dokumenSectionCounter = 0;
const dokumenSectionsContainer = document.getElementById('dokumen_sections_container');
const addDokumenSectionBtn = document.getElementById('add_dokumen_section_btn');
const addDokumenSectionBottomBtn = document.getElementById('add_dokumen_section_bottom_btn');

const initDokumenSelect2 = (el, placeholder) => {
    if (typeof jQuery.fn.select2 !== 'undefined') {
        jQuery(el).select2({
            placeholder: placeholder,
            allowClear: true,
            width: '100%'
        });
    } else {
        setTimeout(() => initDokumenSelect2(el, placeholder), 100);
    }
};

function initializeDokumenSections() {
    if (!dokumenSectionsContainer) return;
    dokumenSectionsContainer.innerHTML = '';
    dokumenSectionCounter = 0;
    
    @if(isset($biayaKapal) && $biayaKapal->dokumens && $biayaKapal->dokumens->count() > 0)
        const existingDokumens = {!! json_encode($biayaKapal->dokumens) !!};
        existingDokumens.forEach(doc => {
            addDokumenSection(doc);
        });
    @else
        addDokumenSection();
    @endif
}

function clearAllDokumenSections() {
    if (!dokumenSectionsContainer) return;
    dokumenSectionsContainer.innerHTML = '';
    dokumenSectionCounter = 0;
}

if (addDokumenSectionBtn) {
    addDokumenSectionBtn.addEventListener('click', function() {
        addDokumenSection();
    });
}

if (addDokumenSectionBottomBtn) {
    addDokumenSectionBottomBtn.addEventListener('click', function() {
        addDokumenSection();
    });
}

function addDokumenSection(existingData = null) {
    if (!dokumenSectionsContainer) return;
    dokumenSectionCounter++;
    const sectionIndex = dokumenSectionCounter;
    
    const section = document.createElement('div');
    section.className = 'dokumen-section mb-6 p-4 border-2 border-indigo-200 rounded-lg bg-indigo-50';
    section.setAttribute('data-dokumen-section-index', sectionIndex);
    
    let kapalOptions = '<option value="">-- Pilih Kapal --</option>';
    let initialKapal = existingData ? existingData.kapal : '';
    let initialVoyage = existingData ? existingData.voyage : '';
    let initialNominal = existingData ? parseInt(existingData.nominal).toLocaleString('id-ID') : '0';
    let initialPph = existingData ? parseInt(existingData.pph).toLocaleString('id-ID') : '0';
    let initialTotal = existingData ? parseInt(existingData.total_biaya).toLocaleString('id-ID') : '0';

    if (typeof allKapalsData !== 'undefined') {
        allKapalsData.forEach(kapal => {
            const selected = (initialKapal === kapal.nama_kapal) ? 'selected' : '';
            kapalOptions += `<option value="${kapal.nama_kapal}" ${selected}>${kapal.nama_kapal}</option>`;
        });
    }
    
    // Default manual voyage state if existing voyage is set but we haven't loaded voyages list yet
    const isManualVoyage = initialVoyage ? true : false;
    
    section.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-md font-semibold text-gray-800">Kapal ${sectionIndex} (Biaya Dokumen)</h3>
            ${sectionIndex > 1 ? `<button type="button" onclick="removeDokumenSection(${sectionIndex})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition"><i class="fas fa-trash mr-1"></i>Hapus</button>` : ''}
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kapal <span class="text-red-500">*</span></label>
                <select name="dokumen_sections[${sectionIndex}][kapal]" class="dokumen-kapal-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500" required>
                    ${kapalOptions}
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">No. Voyage <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <select name="dokumen_sections[${sectionIndex}][voyage]" class="dokumen-voyage-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 ${isManualVoyage ? 'hidden' : ''}" ${isManualVoyage ? '' : 'required disabled'}>
                        <option value="">-- Pilih Kapal Terlebih Dahulu --</option>
                    </select>
                    <input type="text" name="dokumen_sections[${sectionIndex}][voyage]" class="dokumen-voyage-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 ${isManualVoyage ? '' : 'hidden'}" ${isManualVoyage ? 'required' : 'disabled'} placeholder="Ketik No. Voyage" value="${initialVoyage}">
                    <button type="button" class="dokumen-voyage-manual-btn px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg transition" title="Input Manual / Pilih dari List">
                        <i class="fas fa-keyboard"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="border-t pt-4 mt-2 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Voyage</label>
                <!-- the UI above already has voyage, we just need the costs -->
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                    <input type="text" name="dokumen_sections[${sectionIndex}][nominal]"
                           class="dokumen-nominal-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           value="${initialNominal}">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">PPH (2%)</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                    <input type="text" name="dokumen_sections[${sectionIndex}][pph]"
                           class="dokumen-pph-input w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           value="${initialPph}">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400">Rp</span>
                    <input type="text" name="dokumen_sections[${sectionIndex}][total_biaya]"
                           class="dokumen-total-input w-full pl-10 pr-3 py-2 border border-indigo-200 rounded-lg bg-indigo-50 text-indigo-800 focus:ring-0 cursor-not-allowed"
                           value="${initialTotal}" readonly>
                </div>
            </div>
        </div>
    `;
    
    dokumenSectionsContainer.appendChild(section);
    
    const kapalSelect = section.querySelector('.dokumen-kapal-select');
    const voyageSelect = section.querySelector('.dokumen-voyage-select');
    const voyageInput = section.querySelector('.dokumen-voyage-input');
    const voyageManualBtn = section.querySelector('.dokumen-voyage-manual-btn');
    
    initDokumenSelect2(kapalSelect, '-- Pilih Kapal --');
    initDokumenSelect2(voyageSelect, '-- Pilih Kapal Terlebih Dahulu --');
    
    jQuery(kapalSelect).on('change', function() {
        const kapalId = this.value; // It is nama_kapal in the loop
        voyageSelect.innerHTML = '<option value="">-- Memuat... --</option>';
        voyageSelect.disabled = true;
        
        let actualKapalId = null;
        if(typeof allKapalsData !== 'undefined') {
            const kapalObj = allKapalsData.find(k => k.nama_kapal === kapalId);
            if(kapalObj) actualKapalId = kapalObj.id;
        }

        if(actualKapalId) {
            fetch(\`/api/kapal/\${actualKapalId}/voyages\`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Pilih Voyage --</option>';
                    if(data.success && data.data) {
                        data.data.forEach(v => {
                            options += \`<option value="\${v}">\${v}</option>\`;
                        });
                    }
                    voyageSelect.innerHTML = options;
                    voyageSelect.disabled = false;
                })
                .catch(err => {
                    console.error('Error fetching voyages:', err);
                    voyageSelect.innerHTML = '<option value="">-- Gagal memuat --</option>';
                });
        } else {
            voyageSelect.innerHTML = '<option value="">-- Pilih Kapal Terlebih Dahulu --</option>';
        }
    });

    voyageManualBtn.addEventListener('click', function() {
        if (voyageSelect.classList.contains('hidden')) {
            voyageSelect.classList.remove('hidden');
            voyageInput.classList.add('hidden');
            voyageSelect.disabled = false;
            voyageInput.disabled = true;
            voyageSelect.setAttribute('required', 'required');
            voyageInput.removeAttribute('required');
        } else {
            voyageSelect.classList.add('hidden');
            voyageInput.classList.remove('hidden');
            voyageSelect.disabled = true;
            voyageInput.disabled = false;
            voyageInput.setAttribute('required', 'required');
            voyageSelect.removeAttribute('required');
        }
    });

    const nominalInput = section.querySelector('.dokumen-nominal-input');
    const pphInput = section.querySelector('.dokumen-pph-input');
    const totalInput = section.querySelector('.dokumen-total-input');
    
    const attachFormatListeners = (input) => {
        if(!input) return;
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9]/g, '');
            if(val) {
                this.value = parseInt(val).toLocaleString('id-ID');
            } else {
                this.value = '0';
            }
            calculateDokumenSection(section);
        });
        
        input.addEventListener('focus', function() {
            if(this.value === '0') this.value = '';
        });
        
        input.addEventListener('blur', function() {
            if(this.value === '') this.value = '0';
        });
    };
    
    attachFormatListeners(nominalInput);
    attachFormatListeners(pphInput);
    
    // Auto calculate PPH based on nominal when nominal changes
    nominalInput.addEventListener('change', function() {
        let nominalValue = parseInt(this.value.replace(/\\./g, '')) || 0;
        let pphValue = Math.round(nominalValue * 0.02);
        pphInput.value = pphValue.toLocaleString('id-ID');
        calculateDokumenSection(section);
    });
}

function removeDokumenSection(index) {
    const section = document.querySelector(\`.dokumen-section[data-dokumen-section-index="\${index}"]\`);
    if (section) {
        section.remove();
        calculateAllDokumenSections();
    }
}

function calculateDokumenSection(section) {
    const nominalInput = section.querySelector('.dokumen-nominal-input');
    const pphInput = section.querySelector('.dokumen-pph-input');
    const totalInput = section.querySelector('.dokumen-total-input');
    
    const nominal = parseInt(nominalInput.value.replace(/\\./g, '')) || 0;
    const pph = parseInt(pphInput.value.replace(/\\./g, '')) || 0;
    
    const total = nominal - pph;
    totalInput.value = total > 0 ? total.toLocaleString('id-ID') : '0';
    
    calculateAllDokumenSections();
}

function calculateAllDokumenSections() {
    let grandTotal = 0;
    
    document.querySelectorAll('.dokumen-section').forEach(section => {
        const totalInput = section.querySelector('.dokumen-total-input');
        if (totalInput) {
            grandTotal += parseInt(totalInput.value.replace(/\\./g, '')) || 0;
        }
    });
    
    // Update main nominal field if it's visible or handle it
    const mainNominalInput = document.getElementById('nominal');
    if (mainNominalInput) {
        mainNominalInput.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '0';
        
        // Also trigger recalculation of main form totals
        if (typeof calculateTotals === 'function') {
            calculateTotals();
        }
    }
}
