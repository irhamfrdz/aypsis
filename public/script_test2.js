
// Helper to parse JSON response and handle HTML errors gracefully
function parseJsonResponse(response) {
    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
        return response.json().then(data => {
            if (!response.ok) {
                throw data;
            }
            return data;
        });
    } else {
        return response.text().then(text => {
            let errorMessage = `HTTP Error ${response.status}: ${response.statusText}`;
            if (response.status === 419) {
                errorMessage = 'Sesi Anda telah berakhir atau token keamanan kedaluwarsa. Silakan refresh halaman dan coba lagi.';
            } else if (response.status === 403) {
                errorMessage = 'Anda tidak memiliki izin (permission) untuk melakukan tindakan ini.';
            } else if (response.status === 500) {
                errorMessage = 'Terjadi kesalahan internal pada server. Silakan hubungi administrator.';
                if (text.includes('class="exception-message-wrapper"')) {
                    const match = text.match(/<h1 class="break-words[^>]*>([\s\S]*?)<\/h1>/);
                    if (match && match[1]) {
                        errorMessage += `\n\nDetail: ${match[1].trim()}`;
                    }
                }
            }
            throw { message: errorMessage };
        });
    }
}

// Data for destinations based on location
const destinations = {
    jakarta: [],
    batam: []
};

// Function to update Tujuan Pengiriman options based on selected Lokasi
function updateDestinationOptions(modalType) {
    const prefix = modalType === 'create' ? 'modal_' : 'edit_modal_';
    const lokasiSelect = document.getElementById(prefix + 'lokasi');
    const tujuanSelect = document.getElementById(prefix + 'tujuan_pengambilan');
    const feWrapper = document.getElementById(prefix + 'f_e_wrapper');
    
    if (!lokasiSelect || !tujuanSelect) return;
    
    const selectedLokasi = lokasiSelect.value;
    const availableDestinations = destinations[selectedLokasi] || [];
    
    // Show/hide F/E wrapper based on location
    if (feWrapper) {
        if (selectedLokasi === 'batam') {
            feWrapper.classList.remove('hidden');
        } else {
            feWrapper.classList.add('hidden');
        }
    }
    
    // Remember current selection if any
    const currentValue = tujuanSelect.value;
    
    // Clear and add default option
    tujuanSelect.innerHTML = '<option value="">Pilih tujuan pengiriman</option>';
    
    // Add new options
    availableDestinations.forEach(dest => {
        const option = document.createElement('option');
        option.value = dest.value;
        option.text = dest.label;
        
        if (selectedLokasi === 'batam') {
            option.setAttribute('data-uj20-full', dest.uj20_full);
            option.setAttribute('data-uj20-empty', dest.uj20_empty);
            option.setAttribute('data-uj40-full', dest.uj40_full);
            option.setAttribute('data-uj40-empty', dest.uj40_empty);
            option.setAttribute('data-ring', dest.ring || '');
        } else {
            option.setAttribute('data-uang-jalan-20', dest.uj20);
            option.setAttribute('data-uang-jalan-40', dest.uj40);
            option.setAttribute('data-ring', '');
        }
        
        if (dest.value === currentValue) {
            option.selected = true;
        }
        
        tujuanSelect.appendChild(option);
    });
}
// Toggle dropdown menu for action buttons
function toggleDropdown(dropdownId) {
    // Close all other dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== dropdownId) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Toggle the clicked dropdown
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Buat Surat Jalan Manual - Open modal without BL data
function buatSuratJalanManual() {
    // Show modal
    document.getElementById('modalBuatSuratJalan').classList.remove('hidden');
    
    // Clear Manifest and BL IDs
    document.getElementById('modal_manifest_id').value = '';
    document.getElementById('modal_bl_id').value = '';
    
    // Set default values
    document.getElementById('modal_nama_kapal').value = '{{ $selectedKapal }}';
    document.getElementById('modal_no_voyage').value = '{{ $selectedVoyage }}';
    
    // Nomor surat jalan dikosongkan
    document.getElementById('modal_nomor_surat_jalan').value = '';
    
    // Set default tanggal to today
    document.getElementById('modal_pengirim').value = '';
    document.getElementById('modal_tanggal_surat_jalan').value = new Date().toISOString().split('T')[0];
    
    // Clear all other fields
    document.getElementById('modal_no_bl').value = '';
    document.getElementById('modal_no_kontainer').value = '';
    document.getElementById('modal_no_seal').value = '';
    document.getElementById('modal_size').value = '';
    document.getElementById('modal_jenis_barang').value = '';
    document.getElementById('modal_penerima').value = '';
    document.getElementById('modal_tujuan_alamat').value = '';
    document.getElementById('modal_lokasi').value = 'batam';
    
    // Remove readonly from fields
    document.getElementById('modal_no_bl').removeAttribute('readonly');
    document.getElementById('modal_no_kontainer').removeAttribute('readonly');
    document.getElementById('modal_no_seal').removeAttribute('readonly');
    document.getElementById('modal_size').removeAttribute('readonly');
    document.getElementById('modal_jenis_barang').removeAttribute('readonly');
    
    // Setup auto-fill and calculations
    setupModalSupirAutoFill();
    setupModalUangJalanCalculation('');
    setupModalLanjutMuatToggle();

    // Add listener for lokasi change
    const lokasiSelect = document.getElementById('modal_lokasi');
    if (lokasiSelect) {
        // Trigger initial update for destinations
        updateDestinationOptions('create');
        
        // Remove existing listener if any to avoid duplicates
        lokasiSelect.onchange = () => {
            updateDestinationOptions('create');
        };
    }
}

// Buat Surat Jalan function - Open modal and populate with Manifest data
function buatSuratJalan(manifestId) {
    // Show modal
    document.getElementById('modalBuatSuratJalan').classList.remove('hidden');
    
    // Fetch Manifest data
    fetch(`{{ route('api.manifest-batam.show', ['id' => ':id'], false) }}`.replace(':id', manifestId))
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response was not JSON');
            }
            return response.json();
        })
        .then(data => {
            // Populate hidden Manifest ID
            document.getElementById('modal_manifest_id').value = manifestId;
            
            // Populate nama kapal and no voyage from Manifest data
            if (data.nama_kapal) {
                document.getElementById('modal_nama_kapal').value = data.nama_kapal;
            }
            if (data.no_voyage) {
                document.getElementById('modal_no_voyage').value = data.no_voyage;
            }
            
            // Nomor surat jalan dikosongkan
            document.getElementById('modal_nomor_surat_jalan').value = '';
            document.getElementById('modal_lokasi').value = 'batam';
            
            // Set default tanggal to today
            document.getElementById('modal_tanggal_surat_jalan').value = new Date().toISOString().split('T')[0];
            
            // Populate Manifest data fields (readonly)
            document.getElementById('modal_no_bl').value = data.nomor_bl || '';
            document.getElementById('modal_no_kontainer').value = data.nomor_kontainer || '';
            document.getElementById('modal_no_seal').value = data.no_seal || '';
            // Populate size with normalization
            const sizeSelect = document.getElementById('modal_size');
            const sizeVal = data.size_kontainer || '';
            sizeSelect.value = sizeVal;
            if (sizeSelect.value === '' && sizeVal !== '') {
                const normSize = sizeVal.toLowerCase().replace(/\s/g, '');
                if (normSize.includes('20')) sizeSelect.value = '20ft';
                else if (normSize.includes('40')) sizeSelect.value = '40ft';
            }
            document.getElementById('modal_pengirim').value = data.pengirim || '';
            document.getElementById('modal_penerima').value = data.penerima || data.pengirim || '';
            
            // Populate term if available with robust matching
            if (data.term || data.term_nama) {
                const termSelect = document.getElementById('modal_term');
                const termToMatch = (data.term || '').toLowerCase();
                const termNamaToMatch = (data.term_nama || '').toLowerCase();
                
                // Try direct match first
                termSelect.value = data.term || '';
                
                // If not matched, try searching through options
                if (termSelect.value === '') {
                    const options = termSelect.options;
                    for (let i = 0; i < options.length; i++) {
                        const optValue = options[i].value.toLowerCase();
                        const optText = options[i].text.toLowerCase();
                        
                        if (optValue === termToMatch || 
                            optText === termToMatch || 
                            optText.includes(termToMatch) ||
                            (termNamaToMatch && optText.includes(termNamaToMatch))) {
                            termSelect.value = options[i].value;
                            break;
                        }
                    }
                }
            }
            
            // Set jenis pengiriman if available
            if (data.jenis_pengiriman) {
                document.getElementById('modal_jenis_pengiriman').value = data.jenis_pengiriman;
            }
            
            // Set alamat pengiriman if available
            if (data.alamat_pengiriman) {
                document.getElementById('modal_tujuan_alamat').value = data.alamat_pengiriman;
            }
            
            // Setup auto-fill plat when supir is selected
            setupModalSupirAutoFill();
            
            // Setup auto-calculate uang jalan
            setupModalUangJalanCalculation(data.size_kontainer);

            // Add listener for lokasi change
            const lokasiSelect = document.getElementById('modal_lokasi');
            if (lokasiSelect) {
                lokasiSelect.onchange = () => {
                    updateDestinationOptions('create');
                    if (lokasiSelect.value === 'batam') {
                        document.getElementById('modal_jenis_barang').value = data.nama_barang || '';
                    }
                };
                // Initial trigger
                updateDestinationOptions('create');
                // Check if already batam on load
                if (lokasiSelect.value === 'batam') {
                    document.getElementById('modal_jenis_barang').value = data.nama_barang || '';
                }
            }
            
            // Setup toggle lanjut muat
            setupModalLanjutMuatToggle();
        })
        .catch(error => {
            console.error('Error fetching Manifest data:', error);
            closeModal();
            alert('Gagal mengambil data Manifest. ' + (error.message || 'Silakan coba lagi atau hubungi administrator.'));
        });
}

// Setup auto-fill plat nomor when supir is selected in modal
function setupModalSupirAutoFill() {
    const supirSelect = document.getElementById('modal_supir');
    const noPlatInput = document.getElementById('modal_no_plat');
    
    if (supirSelect && noPlatInput) {
        // Remove existing listener if any
        supirSelect.removeEventListener('change', handleModalSupirChange);
        // Add new listener
        supirSelect.addEventListener('change', handleModalSupirChange);
    }
}

function handleModalSupirChange(e) {
    const selectedOption = e.target.options[e.target.selectedIndex];
    const platNumber = selectedOption.getAttribute('data-plat');
    const noPlatInput = document.getElementById('modal_no_plat');
    
    if (platNumber && platNumber.trim() !== '') {
        noPlatInput.value = platNumber;
    }
}

// Setup auto-calculate uang jalan based on tujuan pengambilan in modal
function setupModalUangJalanCalculation(containerSize) {
    const tujuanPengambilanSelect = document.getElementById('modal_tujuan_pengambilan');
    const uangJalanNominalInput = document.getElementById('modal_uang_jalan_nominal');
    const uangJalanTypeRadios = document.querySelectorAll('input[name="uang_jalan_type"]');
    const sizeSelect = document.getElementById('modal_size');
    const tanpaUangJalanCheckbox = document.getElementById('modal_tanpa_uang_jalan');
    
    if (tanpaUangJalanCheckbox && uangJalanNominalInput) {
        tanpaUangJalanCheckbox.addEventListener('change', function() {
            if (this.checked) {
                uangJalanNominalInput.value = '0';
                uangJalanNominalInput.readOnly = true;
                uangJalanNominalInput.classList.add('bg-gray-100', 'text-gray-500');
            } else {
                uangJalanNominalInput.readOnly = false;
                uangJalanNominalInput.classList.remove('bg-gray-100', 'text-gray-500');
                calculateModalUangJalan();
            }
        });
    }

    function calculateModalUangJalan() {
        const selectedOption = tujuanPengambilanSelect.options[tujuanPengambilanSelect.selectedIndex];
        const ringInput = document.getElementById('modal_ring');
        if (ringInput) {
            ringInput.value = selectedOption ? (selectedOption.getAttribute('data-ring') || '') : '';
        }
        if (!selectedOption) return;
        if (tanpaUangJalanCheckbox && tanpaUangJalanCheckbox.checked) return;
        const uangJalan20 = parseFloat(selectedOption.getAttribute('data-uang-jalan-20')) || 0;
        const uangJalan40 = parseFloat(selectedOption.getAttribute('data-uang-jalan-40')) || 0;
        const uangJalanType = document.querySelector('input[name="uang_jalan_type"]:checked');
        const lokasiSelect = document.getElementById('modal_lokasi');
        const feType = document.querySelector('#modalBuatSuratJalan input[name="f_e"]:checked')?.value || 'Full';
        
        // Get current size from dropdown
        const currentSize = sizeSelect.value;
        const isBatam = lokasiSelect && lokasiSelect.value === 'batam';
        
        let uangJalan = 0;
        
        if (isBatam) {
            const uj20Full = parseFloat(selectedOption.getAttribute('data-uj20-full')) || 0;
            const uj20Empty = parseFloat(selectedOption.getAttribute('data-uj20-empty')) || 0;
            const uj40Full = parseFloat(selectedOption.getAttribute('data-uj40-full')) || 0;
            const uj40Empty = parseFloat(selectedOption.getAttribute('data-uj40-empty')) || 0;

            if (currentSize === '20' || currentSize === '20ft') {
                uangJalan = feType === 'Full' ? uj20Full : uj20Empty;
            } else if (currentSize === '40' || currentSize === '40ft' || currentSize === '40hc' || currentSize === '40 hc') {
                uangJalan = feType === 'Full' ? uj40Full : uj40Empty;
            } else {
                uangJalan = feType === 'Full' ? uj20Full : uj20Empty;
            }
        } else {
            const uangJalan20 = parseFloat(selectedOption.getAttribute('data-uang-jalan-20')) || 0;
            const uangJalan40 = parseFloat(selectedOption.getAttribute('data-uang-jalan-40')) || 0;
            
            // Determine uang jalan based on container size
            if (currentSize === '20' || currentSize === '20ft') {
                uangJalan = uangJalan20;
            } else if (currentSize === '40' || currentSize === '40ft' || currentSize === '40hc' || currentSize === '40 hc') {
                uangJalan = uangJalan40;
            } else {
                // Default to 20ft if size is not clear
                uangJalan = uangJalan20;
            }
        }
        
        // Apply half calculation if "setengah" is selected
        if (uangJalanType && uangJalanType.value === 'setengah') {
            uangJalan = uangJalan / 2;
        }
        
        if (uangJalan > 0) {
            uangJalanNominalInput.value = Math.round(uangJalan);
        }
    }
    
    if (tujuanPengambilanSelect && uangJalanNominalInput && sizeSelect) {
        // Remove existing listeners
        tujuanPengambilanSelect.removeEventListener('change', calculateModalUangJalan);
        sizeSelect.removeEventListener('change', calculateModalUangJalan);
        
        // Add new listeners
        tujuanPengambilanSelect.addEventListener('change', calculateModalUangJalan);
        sizeSelect.addEventListener('change', calculateModalUangJalan);
        
        uangJalanTypeRadios.forEach(radio => {
            radio.removeEventListener('change', calculateModalUangJalan);
            radio.addEventListener('change', calculateModalUangJalan);
        });

        // Add listener for F/E radio
        const feRadios = document.querySelectorAll('#modalBuatSuratJalan input[name="f_e"]');
        feRadios.forEach(radio => {
            radio.addEventListener('change', calculateModalUangJalan);
        });
    }
}

// Setup toggle for lanjut muat field in modal
function setupModalLanjutMuatToggle() {
    const lanjutMuatRadios = document.querySelectorAll('input[name="lanjut_muat"]');
    const nomorSjSebelumnyaWrapper = document.getElementById('modal_nomor_sj_sebelumnya_wrapper');
    const nomorSjSebelumnyaInput = document.getElementById('modal_nomor_sj_sebelumnya');
    
    function toggleNomorSjSebelumnya() {
        const lanjutMuatValue = document.querySelector('input[name="lanjut_muat"]:checked')?.value;
        
        if (lanjutMuatValue === 'ya') {
            nomorSjSebelumnyaWrapper.style.display = 'block';
            nomorSjSebelumnyaInput.setAttribute('required', 'required');
        } else {
            nomorSjSebelumnyaWrapper.style.display = 'none';
            nomorSjSebelumnyaInput.removeAttribute('required');
            nomorSjSebelumnyaInput.value = '';
        }
    }
    
    // Add event listeners
    lanjutMuatRadios.forEach(radio => {
        radio.removeEventListener('change', toggleNomorSjSebelumnya);
        radio.addEventListener('change', toggleNomorSjSebelumnya);
    });
    
    // Initialize on load
    toggleNomorSjSebelumnya();
}

// Handle form submit with validation and loading state
function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = document.getElementById('formBuatSuratJalan');
    const submitBtn = document.getElementById('btnSubmitModal');
    const submitText = document.getElementById('btnSubmitText');
    const submitLoading = document.getElementById('btnSubmitLoading');
    
    // Validate required fields
    const nomorSuratJalan = document.getElementById('modal_nomor_surat_jalan').value.trim();
    const tanggalSuratJalan = document.getElementById('modal_tanggal_surat_jalan').value.trim();
    
    if (!nomorSuratJalan) {
        showModalAlert('Field Wajib Diisi!', 'Nomor Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('modal_nomor_surat_jalan').focus();
        return false;
    }
    
    if (!tanggalSuratJalan) {
        showModalAlert('Field Wajib Diisi!', 'Tanggal Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('modal_tanggal_surat_jalan').focus();
        return false;
    }
    
    // Validate nomor surat jalan sebelumnya if lanjut muat is 'ya'
    const lanjutMuat = document.querySelector('input[name="lanjut_muat"]:checked')?.value;
    if (lanjutMuat === 'ya') {
        const nomorSjSebelumnya = document.getElementById('modal_nomor_sj_sebelumnya').value.trim();
        if (!nomorSjSebelumnya) {
            showModalAlert('Field Wajib Diisi!', 'Nomor Surat Jalan Sebelumnya harus diisi jika memilih lanjut muat.', 'error');
            document.getElementById('modal_nomor_sj_sebelumnya').focus();
            return false;
        }
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    // Submit form via AJAX
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(parseJsonResponse)
    .then(data => {
        // Success - redirect with success message
        if (data.redirect) {
            // Check if URL already has query parameters
            const separator = data.redirect.includes('?') ? '&' : '?';
            window.location.href = data.redirect + separator + 'success=1';
        } else {
            // Reload page to show success message
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Reset button state
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        
        // Show error message
        let errorMessage = '';
        let errorTitle = 'Validasi Gagal!';
        
        if (error.errors && Object.keys(error.errors).length > 0) {
            // Laravel validation errors - format as list
            errorTitle = 'Validasi Gagal! Silakan periksa kembali data yang diinput:';
            const errorItems = [];
            
            for (const [field, messages] of Object.entries(error.errors)) {
                const fieldLabel = getFieldLabel(field);
                messages.forEach(msg => {
                    errorItems.push(`<li class="ml-4"><strong>${fieldLabel}:</strong> ${msg}</li>`);
                });
            }
            
            errorMessage = `<ul class="list-disc mt-2 text-sm">${errorItems.join('')}</ul>`;
        } else if (error.message) {
            errorMessage = error.message;
        } else {
            errorTitle = 'Terjadi Kesalahan!';
            errorMessage = 'Gagal menyimpan surat jalan. Silakan coba lagi atau hubungi administrator.';
        }
        
        showModalAlert(errorTitle, errorMessage, 'error');
    });
    
    return false;
}

// Get field label in Indonesian
function getFieldLabel(fieldName) {
    const labels = {
        'nomor_surat_jalan': 'Nomor Surat Jalan',
        'lokasi': 'Lokasi',
        'tanggal_surat_jalan': 'Tanggal Surat Jalan',
        'term': 'Term',
        'aktifitas': 'Aktifitas',
        'penerima': 'Penerima',
        'pengirim': 'Pengirim',
        'jenis_barang': 'Jenis Barang',
        'tujuan_alamat': 'Tujuan Alamat',
        'tujuan_pengambilan': 'Tujuan Pengambilan',
        'tujuan_pengiriman': 'Tujuan Pengiriman',
        'jenis_pengiriman': 'Jenis Pengiriman',
        'tanggal_ambil_barang': 'Tanggal Ambil Barang',
        'supir': 'Supir',
        'no_plat': 'No Plat',
        'kenek': 'Kenek',
        'krani': 'Krani',
        'no_kontainer': 'No Kontainer',
        'no_seal': 'No Seal',
        'no_bl': 'Nomor BL',
        'size': 'Size Kontainer',
        'karton': 'Karton',
        'plastik': 'Plastik',
        'terpal': 'Terpal',
        'rit': 'RIT',
        'uang_jalan_type': 'Tipe Uang Jalan',
        'uang_jalan_nominal': 'Nominal Uang Jalan',
        'lanjut_muat': 'Lanjut Muat',
        'nomor_sj_sebelumnya': 'Nomor Surat Jalan Sebelumnya',
        'nama_kapal': 'Nama Kapal',
        'no_voyage': 'No Voyage',
        'bl_id': 'BL ID'
    };
    
    return labels[fieldName] || fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

// Show alert inside modal
function showModalAlert(title, message, type = 'error') {
    // Remove existing alert if any
    const existingAlert = document.querySelector('.modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `modal-alert mb-4 px-4 py-3 rounded-lg ${
        type === 'error' 
            ? 'bg-red-50 border border-red-200 text-red-800' 
            : 'bg-green-50 border border-green-200 text-green-800'
    }`;
    
    alertDiv.innerHTML = `
        <div class="flex items-start w-full">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'error' 
                    ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                }
            </svg>
            <div class="flex-1">
                <div class="font-semibold mb-1">${title}</div>
                <div class="text-sm">${message}</div>
            </div>
            <button type="button" class="ml-3 flex-shrink-0 ${type === 'error' ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'}" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;
    
    const modalBody = document.querySelector('#formBuatSuratJalan');
    modalBody.insertBefore(alertDiv, modalBody.firstChild);
    
    // Auto-scroll to top of modal to show alert
    const modalContent = document.querySelector('#modalBuatSuratJalan .max-h-\\[70vh\\]');
    if (modalContent) {
        modalContent.scrollTop = 0;
    }
}

// Close modal function
function closeModal() {
    document.getElementById('modalBuatSuratJalan').classList.add('hidden');
    document.getElementById('formBuatSuratJalan').reset();
    
    // Reset lanjut muat field
    const nomorSjSebelumnyaWrapper = document.getElementById('modal_nomor_sj_sebelumnya_wrapper');
    const nomorSjSebelumnyaInput = document.getElementById('modal_nomor_sj_sebelumnya');
    if (nomorSjSebelumnyaWrapper) {
        nomorSjSebelumnyaWrapper.style.display = 'none';
    }
    if (nomorSjSebelumnyaInput) {
        nomorSjSebelumnyaInput.value = '';
        nomorSjSebelumnyaInput.removeAttribute('required');
    }
    
    // Reset button state
    const submitBtn = document.getElementById('btnSubmitModal');
    const submitText = document.getElementById('btnSubmitText');
    const submitLoading = document.getElementById('btnSubmitLoading');
    
    submitBtn.disabled = false;
    submitText.classList.remove('hidden');
    submitLoading.classList.add('hidden');
    
    // Remove any alerts
    const existingAlert = document.querySelector('.modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('modalBuatSuratJalan');
    if (event.target === modal) {
        closeModal();
    }
});

// Print SJ function - Print directly from BL data
function printSJ(blId) {
    // Open print page in new window/tab
    window.open('/surat-jalan-bongkaran-batam/print-from-bl/' + blId, '_blank');
}

// Print BA function - Print Berita Acara directly from BL data
function printBA(blId) {
    // Open print BA page in new window/tab
    window.open('/surat-jalan-bongkaran-batam/print-ba/' + blId, '_blank');
}

// Functions for Surat Jalan Bongkaran mode
function editSuratJalan(suratJalanId) {
    console.log('editSuratJalan called with ID:', suratJalanId);
    // Open edit modal and populate with Surat Jalan data
    openEditModal(suratJalanId);
}

// Edit Surat Jalan from BL (when BL already has Surat Jalan)
function editSuratJalanFromBL(suratJalanId) {
    console.log('editSuratJalanFromBL called with ID:', suratJalanId);
    // Open edit modal and populate with Surat Jalan data
    openEditModal(suratJalanId);
}

// Open edit modal and fetch surat jalan data
function openEditModal(suratJalanId) {
    console.log('openEditModal called with ID:', suratJalanId);
    // Show modal
    const modal = document.getElementById('modalEditSuratJalan');
    if (!modal) {
        console.error('ERROR: modalEditSuratJalan element not found in DOM!');
        alert('Error: Modal element not found in page.');
        return;
    }
    modal.classList.remove('hidden');
    console.log('Modal element hidden class removed. Style display status:', modal.style.display);
    
    const fetchUrl = `{{ route('api.surat-jalan-bongkaran-batam.show', ['id' => ':id'], false) }}`.replace(':id', suratJalanId);
    console.log('Starting fetch request to URL:', fetchUrl);

    // Fetch Surat Jalan data
    fetch(fetchUrl)
        .then(response => {
            console.log('Received response from server. Status:', response.status, 'OK:', response.ok);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response was not JSON');
            }
            return response.json();
        })
        .then(data => {
            // Populate hidden ID
            document.getElementById('edit_modal_surat_jalan_id').value = suratJalanId;
            document.getElementById('edit_modal_manifest_id').value = data.manifest_id || '';
            document.getElementById('edit_modal_bl_id').value = data.bl_id || '';
            
            // Populate nama kapal and no voyage from surat jalan data
            if (data.nama_kapal) {
                document.getElementById('edit_modal_nama_kapal').value = data.nama_kapal;
            }
            if (data.no_voyage) {
                document.getElementById('edit_modal_no_voyage').value = data.no_voyage;
            }
            
            // Set form action URL
            document.getElementById('formEditSuratJalan').action = `/surat-jalan-bongkaran-batam/${suratJalanId}`;
            
            // Populate form fields
            document.getElementById('edit_modal_nomor_surat_jalan').value = data.nomor_surat_jalan || '';
            document.getElementById('edit_modal_lokasi').value = data.lokasi || '';
            document.getElementById('edit_modal_tanggal_surat_jalan').value = data.tanggal_surat_jalan || '';
            document.getElementById('edit_modal_ring').value = data.ring || '';
            
            // Populate term with robust matching
            const editTermSelect = document.getElementById('edit_modal_term');
            const editTermToMatch = (data.term || '').toLowerCase();
            const editTermNamaToMatch = (data.term_nama || '').toLowerCase();
            
            editTermSelect.value = data.term || '';
            if (editTermSelect.value === '') {
                const options = editTermSelect.options;
                for (let i = 0; i < options.length; i++) {
                    const optValue = options[i].value.toLowerCase();
                    const optText = options[i].text.toLowerCase();
                    if (optValue === editTermToMatch || 
                        optText === editTermToMatch || 
                        optText.includes(editTermToMatch) ||
                        (editTermNamaToMatch && optText.includes(editTermNamaToMatch))) {
                        editTermSelect.value = options[i].value;
                        break;
                    }
                }
            }
            document.getElementById('edit_modal_aktifitas').value = data.aktifitas || '';
            document.getElementById('edit_modal_pengirim').value = data.pengirim || '';
            document.getElementById('edit_modal_penerima').value = data.penerima || '';
            document.getElementById('edit_modal_jenis_barang').value = data.jenis_barang || '';
            document.getElementById('edit_modal_tujuan_alamat').value = data.tujuan_alamat || '';
            document.getElementById('edit_modal_tujuan_pengambilan').value = data.tujuan_pengambilan || '';
            document.getElementById('edit_modal_jenis_pengiriman').value = data.jenis_pengiriman || '';
            document.getElementById('edit_modal_tanggal_ambil_barang').value = data.tanggal_ambil_barang || '';
            
            document.getElementById('edit_modal_supir').value = data.supir || '';
            document.getElementById('edit_modal_no_plat').value = data.no_plat || '';
            document.getElementById('edit_modal_kenek').value = data.kenek || '';
            document.getElementById('edit_modal_krani').value = data.krani || '';
            
            document.getElementById('edit_modal_no_kontainer').value = data.no_kontainer || '';
            document.getElementById('edit_modal_no_seal').value = data.no_seal || '';
            document.getElementById('edit_modal_no_bl').value = data.no_bl || '';
            // Populate size with normalization
            const editSizeSelect = document.getElementById('edit_modal_size');
            const editSizeVal = data.size || '';
            editSizeSelect.value = editSizeVal;
            if (editSizeSelect.value === '' && editSizeVal !== '') {
                const normSize = editSizeVal.toLowerCase().replace(/\s/g, '');
                if (normSize.includes('20')) editSizeSelect.value = '20ft';
                else if (normSize.includes('40')) editSizeSelect.value = '40ft';
            }
            
            // Set radio buttons
            // Set radio buttons safely
            if (data.karton) {
                const el = document.querySelector(`#modalEditSuratJalan input[name="karton"][value="${data.karton}"]`);
                if (el) el.checked = true;
            }
            if (data.plastik) {
                const el = document.querySelector(`#modalEditSuratJalan input[name="plastik"][value="${data.plastik}"]`);
                if (el) el.checked = true;
            }
            if (data.terpal) {
                const el = document.querySelector(`#modalEditSuratJalan input[name="terpal"][value="${data.terpal}"]`);
                if (el) el.checked = true;
            }
            if (data.rit) {
                const el = document.querySelector(`#modalEditSuratJalan input[name="rit"][value="${data.rit}"]`);
                if (el) el.checked = true;
            }
            if (data.uang_jalan_type) {
                const el = document.querySelector(`#modalEditSuratJalan input[name="uang_jalan_type"][value="${data.uang_jalan_type.toLowerCase()}"]`) ||
                           document.querySelector(`#modalEditSuratJalan input[name="uang_jalan_type"][value="${data.uang_jalan_type}"]`);
                if (el) el.checked = true;
            }
            if (data.f_e) {
                const feRadio = document.querySelector(`#modalEditSuratJalan input[name="f_e"][value="${data.f_e}"]`);
                if (feRadio) feRadio.checked = true;
            }
            
            // Convert to integer to remove decimal places
            const nominalValue = data.uang_jalan_nominal ? Math.round(parseFloat(data.uang_jalan_nominal)) : '';
            document.getElementById('edit_modal_uang_jalan_nominal').value = nominalValue;
            
            const editTanpaUangJalanCheckbox = document.getElementById('edit_modal_tanpa_uang_jalan');
            if (editTanpaUangJalanCheckbox) {
                editTanpaUangJalanCheckbox.checked = (data.tanpa_uang_jalan == 1);
                const uangJalanInput = document.getElementById('edit_modal_uang_jalan_nominal');
                if (editTanpaUangJalanCheckbox.checked) {
                    uangJalanInput.readOnly = true;
                    uangJalanInput.classList.add('bg-gray-100', 'text-gray-500');
                } else {
                    uangJalanInput.readOnly = false;
                    uangJalanInput.classList.remove('bg-gray-100', 'text-gray-500');
                }
            }
            
            // Update destinations based on fetched lokasi
            updateDestinationOptions('edit');
            // Restore selection after update because updateDestinationOptions clears it
            if (data.tujuan_pengambilan) {
                document.getElementById('edit_modal_tujuan_pengambilan').value = data.tujuan_pengambilan;
            }

            // Add listener for lokasi change in edit modal
            const editLokasiSelect = document.getElementById('edit_modal_lokasi');
            if (editLokasiSelect) {
                editLokasiSelect.onchange = () => {
                    updateDestinationOptions('edit');
                    if (editLokasiSelect.value === 'batam') {
                        document.getElementById('edit_modal_jenis_barang').value = data.nama_barang_manifest || '';
                    }
                };
            }

            // Setup auto-fill and auto-calculate functions
            setupEditModalSupirAutoFill();
            setupEditModalUangJalanCalculation(data.size);
        })
        .catch(error => {
            console.error('Error fetching Surat Jalan data:', error);
            alert('Error saat memuat data: ' + error.message);
            closeEditModal();
            
            // Show more detailed error message
            let errorMsg = 'Gagal mengambil data Surat Jalan. ';
            if (error.message) {
                errorMsg += error.message;
            } else if (error.error) {
                errorMsg += error.error;
            } else {
                errorMsg += 'Silakan coba lagi atau hubungi administrator.';
            }
            errorMsg += '\n\nID: ' + suratJalanId;
            
            alert(errorMsg);
        });
}

// Setup auto-fill plat nomor when supir is selected in edit modal
function setupEditModalSupirAutoFill() {
    const supirSelect = document.getElementById('edit_modal_supir');
    const noPlatInput = document.getElementById('edit_modal_no_plat');
    
    if (supirSelect && noPlatInput) {
        supirSelect.removeEventListener('change', handleEditModalSupirChange);
        supirSelect.addEventListener('change', handleEditModalSupirChange);
    }
}

function handleEditModalSupirChange(e) {
    const selectedOption = e.target.options[e.target.selectedIndex];
    const platNumber = selectedOption.getAttribute('data-plat');
    const noPlatInput = document.getElementById('edit_modal_no_plat');
    
    if (platNumber && platNumber.trim() !== '') {
        noPlatInput.value = platNumber;
    }
}

// Setup auto-calculate uang jalan in edit modal
function setupEditModalUangJalanCalculation(containerSize) {
    const tujuanPengambilanSelect = document.getElementById('edit_modal_tujuan_pengambilan');
    const uangJalanNominalInput = document.getElementById('edit_modal_uang_jalan_nominal');
    const uangJalanTypeRadios = document.querySelectorAll('#modalEditSuratJalan input[name="uang_jalan_type"]');
    const sizeSelect = document.getElementById('edit_modal_size');
    const tanpaUangJalanCheckbox = document.getElementById('edit_modal_tanpa_uang_jalan');
    
    if (tanpaUangJalanCheckbox && uangJalanNominalInput) {
        tanpaUangJalanCheckbox.addEventListener('change', function() {
            if (this.checked) {
                uangJalanNominalInput.value = '0';
                uangJalanNominalInput.readOnly = true;
                uangJalanNominalInput.classList.add('bg-gray-100', 'text-gray-500');
            } else {
                uangJalanNominalInput.readOnly = false;
                uangJalanNominalInput.classList.remove('bg-gray-100', 'text-gray-500');
                calculateEditModalUangJalan();
            }
        });
    }
    
    function calculateEditModalUangJalan() {
        const selectedOption = tujuanPengambilanSelect.options[tujuanPengambilanSelect.selectedIndex];
        const ringInput = document.getElementById('edit_modal_ring');
        if (ringInput) {
            ringInput.value = selectedOption ? (selectedOption.getAttribute('data-ring') || '') : '';
        }
        if (!selectedOption) return;
        if (tanpaUangJalanCheckbox && tanpaUangJalanCheckbox.checked) return;
        const uangJalan20 = parseFloat(selectedOption.getAttribute('data-uang-jalan-20')) || 0;
        const uangJalan40 = parseFloat(selectedOption.getAttribute('data-uang-jalan-40')) || 0;
        const uangJalanType = document.querySelector('#modalEditSuratJalan input[name="uang_jalan_type"]:checked');
        const lokasiSelect = document.getElementById('edit_modal_lokasi');
        const feType = document.querySelector('#modalEditSuratJalan input[name="f_e"]:checked')?.value || 'Full';
        
        const currentSize = sizeSelect.value;
        const isBatam = lokasiSelect && lokasiSelect.value === 'batam';
        let uangJalan = 0;
        
        if (isBatam) {
            const uj20Full = parseFloat(selectedOption.getAttribute('data-uj20-full')) || 0;
            const uj20Empty = parseFloat(selectedOption.getAttribute('data-uj20-empty')) || 0;
            const uj40Full = parseFloat(selectedOption.getAttribute('data-uj40-full')) || 0;
            const uj40Empty = parseFloat(selectedOption.getAttribute('data-uj40-empty')) || 0;

            if (currentSize === '20' || currentSize === '20ft') {
                uangJalan = feType === 'Full' ? uj20Full : uj20Empty;
            } else if (currentSize === '40' || currentSize === '40ft' || currentSize === '40hc' || currentSize === '40 hc') {
                uangJalan = feType === 'Full' ? uj40Full : uj40Empty;
            } else {
                uangJalan = feType === 'Full' ? uj20Full : uj20Empty;
            }
        } else {
            const uangJalan20 = parseFloat(selectedOption.getAttribute('data-uang-jalan-20')) || 0;
            const uangJalan40 = parseFloat(selectedOption.getAttribute('data-uang-jalan-40')) || 0;
            
            if (currentSize === '20' || currentSize === '20ft') {
                uangJalan = uangJalan20;
            } else if (currentSize === '40' || currentSize === '40ft' || currentSize === '40hc' || currentSize === '40 hc') {
                uangJalan = uangJalan40;
            } else {
                uangJalan = uangJalan20;
            }
        }
        
        if (uangJalanType && uangJalanType.value === 'setengah') {
            uangJalan = uangJalan / 2;
        }
        
        if (uangJalan > 0) {
            uangJalanNominalInput.value = Math.round(uangJalan);
        }
    }
    
    if (tujuanPengambilanSelect && uangJalanNominalInput && sizeSelect) {
        tujuanPengambilanSelect.removeEventListener('change', calculateEditModalUangJalan);
        sizeSelect.removeEventListener('change', calculateEditModalUangJalan);
        
        tujuanPengambilanSelect.addEventListener('change', calculateEditModalUangJalan);
        sizeSelect.addEventListener('change', calculateEditModalUangJalan);
        
        uangJalanTypeRadios.forEach(radio => {
            radio.removeEventListener('change', calculateEditModalUangJalan);
            radio.addEventListener('change', calculateEditModalUangJalan);
        });

        // Add listener for F/E radio in edit modal
        const feRadios = document.querySelectorAll('#modalEditSuratJalan input[name="f_e"]');
        feRadios.forEach(radio => {
            radio.addEventListener('change', calculateEditModalUangJalan);
        });
    }
}

// Handle edit form submit
function handleEditFormSubmit(event) {
    event.preventDefault();
    
    const form = document.getElementById('formEditSuratJalan');
    const submitBtn = document.getElementById('btnSubmitEditModal');
    const submitText = document.getElementById('btnSubmitEditText');
    const submitLoading = document.getElementById('btnSubmitEditLoading');
    
    // Validate required fields
    const nomorSuratJalan = document.getElementById('edit_modal_nomor_surat_jalan').value.trim();
    const tanggalSuratJalan = document.getElementById('edit_modal_tanggal_surat_jalan').value.trim();
    
    if (!nomorSuratJalan) {
        showEditModalAlert('Field Wajib Diisi!', 'Nomor Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('edit_modal_nomor_surat_jalan').focus();
        return false;
    }
    
    if (!tanggalSuratJalan) {
        showEditModalAlert('Field Wajib Diisi!', 'Tanggal Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('edit_modal_tanggal_surat_jalan').focus();
        return false;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    // Submit form via AJAX
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(parseJsonResponse)
    .then(data => {
        // Success - redirect with success message
        if (data.redirect) {
            // Check if URL already has query parameters
            const separator = data.redirect.includes('?') ? '&' : '?';
            window.location.href = data.redirect + separator + 'success=1';
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Reset button state
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        
        // Show error message
        let errorMessage = '';
        let errorTitle = 'Validasi Gagal!';
        
        if (error.errors && Object.keys(error.errors).length > 0) {
            errorTitle = 'Validasi Gagal! Silakan periksa kembali data yang diinput:';
            const errorItems = [];
            
            for (const [field, messages] of Object.entries(error.errors)) {
                const fieldLabel = getFieldLabel(field);
                messages.forEach(msg => {
                    errorItems.push(`<li class="ml-4"><strong>${fieldLabel}:</strong> ${msg}</li>`);
                });
            }
            
            errorMessage = `<ul class="list-disc mt-2 text-sm">${errorItems.join('')}</ul>`;
        } else if (error.message) {
            errorMessage = error.message;
        } else {
            errorTitle = 'Terjadi Kesalahan!';
            errorMessage = 'Gagal mengupdate surat jalan. Silakan coba lagi atau hubungi administrator.';
        }
        
        showEditModalAlert(errorTitle, errorMessage, 'error');
    });
    
    return false;
}

// Show alert inside edit modal
function showEditModalAlert(title, message, type = 'error') {
    const existingAlert = document.querySelector('#modalEditSuratJalan .modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `modal-alert mb-4 px-4 py-3 rounded-lg ${
        type === 'error' 
            ? 'bg-red-50 border border-red-200 text-red-800' 
            : 'bg-green-50 border border-green-200 text-green-800'
    }`;
    
    alertDiv.innerHTML = `
        <div class="flex items-start w-full">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'error' 
                    ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                }
            </svg>
            <div class="flex-1">
                <div class="font-semibold mb-1">${title}</div>
                <div class="text-sm">${message}</div>
            </div>
            <button type="button" class="ml-3 flex-shrink-0 ${type === 'error' ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'}" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;
    
    const modalBody = document.querySelector('#formEditSuratJalan');
    modalBody.insertBefore(alertDiv, modalBody.firstChild);
    
    const modalContent = document.querySelector('#modalEditSuratJalan .max-h-\\[70vh\\]');
    if (modalContent) {
        modalContent.scrollTop = 0;
    }
}

// Close edit modal
function closeEditModal() {
    document.getElementById('modalEditSuratJalan').classList.add('hidden');
    document.getElementById('formEditSuratJalan').reset();
    
    const submitBtn = document.getElementById('btnSubmitEditModal');
    const submitText = document.getElementById('btnSubmitEditText');
    const submitLoading = document.getElementById('btnSubmitEditLoading');
    
    submitBtn.disabled = false;
    submitText.classList.remove('hidden');
    submitLoading.classList.add('hidden');
    
    const existingAlert = document.querySelector('#modalEditSuratJalan .modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
}

// Close edit modal when clicking outside
document.addEventListener('click', function(event) {
    const editModal = document.getElementById('modalEditSuratJalan');
    if (event.target === editModal) {
        closeEditModal();
    }
});

function printSJBongkaran(suratJalanId) {
    // Print existing surat jalan bongkaran
    window.open('/surat-jalan-bongkaran-batam/' + suratJalanId + '/print', '_blank');
}

function deleteSuratJalan(suratJalanId) {
    if (confirm('Apakah Anda yakin ingin menghapus surat jalan ini?')) {
        // Create a form to submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/surat-jalan-bongkaran-batam/' + suratJalanId;
        form.style.display = 'none';

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);

        // Add DELETE method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}

// ============================================================
// BULK CREATE SURAT JALAN FUNCTIONS
// ============================================================

let bulkParsedRows = [];

function buatSuratJalanMassal() {
    document.getElementById('modalBuatSuratJalanMassal').classList.remove('hidden');
    document.getElementById('bulkTextarea').value = '';
    document.getElementById('bulkPreviewContainer').classList.add('hidden');
    document.getElementById('bulkPreviewBody').innerHTML = '';
    document.getElementById('bulkParseInfo').textContent = '';
    document.getElementById('btnSubmitBulk').disabled = true;
    document.getElementById('bulkModalAlertArea').innerHTML = '';
    bulkParsedRows = [];
}

function closeBulkModal() {
    document.getElementById('modalBuatSuratJalanMassal').classList.add('hidden');
    bulkParsedRows = [];
}

function parseBulkData() {
    const textarea = document.getElementById('bulkTextarea');
    const rawText = textarea.value.trim();

    if (!rawText) {
        showBulkAlert('Data Kosong', 'Silakan paste atau ketik data surat jalan terlebih dahulu.', 'error');
        return;
    }

    const lines = rawText.split('\n').filter(line => line.trim() !== '');
    const columnKeys = [
        'nomor_surat_jalan', 'tanggal_surat_jalan', 'no_kontainer',
        'supir', 'no_plat', 'kenek', 'krani', 'aktifitas'
    ];

    bulkParsedRows = [];
    const tbody = document.getElementById('bulkPreviewBody');
    tbody.innerHTML = '';

    let warnings = [];

    lines.forEach((line, index) => {
        const cols = line.split(';');
        const row = {};

        columnKeys.forEach((key, colIndex) => {
            row[key] = (cols[colIndex] || '').trim();
        });

        if (!row.nomor_surat_jalan) {
            warnings.push(`Baris ${index + 1}: Nomor Surat Jalan kosong, baris ini akan diabaikan.`);
            return;
        }

        bulkParsedRows.push(row);

        // Build preview row
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';

        const cellValues = [
            bulkParsedRows.length,
            row.nomor_surat_jalan,
            row.tanggal_surat_jalan || '<span class="text-gray-400 italic">hari ini</span>',
            row.no_kontainer || '-',
            row.supir || '-',
            row.no_plat || '-',
            row.kenek || '-',
            row.krani || '-',
            row.aktifitas || '-'
        ];

        cellValues.forEach((val, i) => {
            const td = document.createElement('td');
            td.className = 'px-3 py-2 whitespace-nowrap';
            if (i === 1) {
                td.className += ' font-semibold text-indigo-700';
            }
            td.innerHTML = val;
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    });

    // Show preview
    const previewContainer = document.getElementById('bulkPreviewContainer');
    const parseInfo = document.getElementById('bulkParseInfo');
    const submitBtn = document.getElementById('btnSubmitBulk');

    if (bulkParsedRows.length > 0) {
        previewContainer.classList.remove('hidden');
        parseInfo.innerHTML = `<span class="text-green-600 font-semibold">${bulkParsedRows.length} baris valid</span>` +
            (warnings.length > 0 ? ` | <span class="text-amber-600">${warnings.length} peringatan</span>` : '');
        submitBtn.disabled = false;

        if (warnings.length > 0) {
            showBulkAlert('Peringatan', warnings.join('<br>'), 'warning');
        } else {
            document.getElementById('bulkModalAlertArea').innerHTML = '';
        }
    } else {
        previewContainer.classList.add('hidden');
        parseInfo.innerHTML = '<span class="text-red-600 font-semibold">Tidak ada baris valid ditemukan</span>';
        submitBtn.disabled = true;
        showBulkAlert('Tidak Ada Data', 'Tidak ada baris dengan Nomor Surat Jalan yang valid. Pastikan format data sesuai panduan.', 'error');
    }
}

function submitBulkSuratJalan() {
    if (bulkParsedRows.length === 0) {
        showBulkAlert('Error', 'Tidak ada data untuk disimpan. Silakan parse data terlebih dahulu.', 'error');
        return;
    }

    const submitBtn = document.getElementById('btnSubmitBulk');
    const submitText = document.getElementById('btnBulkSubmitText');
    const submitLoading = document.getElementById('btnBulkSubmitLoading');

    // Loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');

    const payload = {
        nama_kapal: '{{ $selectedKapal }}',
        no_voyage: '{{ $selectedVoyage }}',
        lokasi: document.getElementById('bulk_lokasi').value,
        rows: bulkParsedRows,
        _token: '{{ csrf_token() }}'
    };

    fetch('{{ route("surat-jalan-bongkaran-batam.store-bulk", [], false) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        // Reset loading
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');

        if (data.success) {
            let msg = data.message;
            if (data.errors && data.errors.length > 0) {
                msg += '<br><br><strong>Detail error:</strong><br>' + data.errors.join('<br>');
                showBulkAlert('Sebagian Berhasil', msg, 'warning');
            } else {
                showBulkAlert('Berhasil!', msg, 'success');
            }

            // Redirect after 2 seconds
            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            }, 2000);
        } else {
            let errorMsg = data.message || 'Gagal menyimpan data.';
            if (data.errors && data.errors.length > 0) {
                errorMsg += '<br><br><strong>Detail:</strong><br>' + data.errors.join('<br>');
            }
            showBulkAlert('Gagal', errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Bulk submit error:', error);
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        showBulkAlert('Error', 'Terjadi kesalahan jaringan. Silakan coba lagi.', 'error');
    });
}

function showBulkAlert(title, message, type = 'error') {
    const alertArea = document.getElementById('bulkModalAlertArea');

    const colorMap = {
        'error': { bg: 'bg-red-50', border: 'border-red-200', text: 'text-red-800', btn: 'text-red-600 hover:text-red-800' },
        'success': { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-800', btn: 'text-green-600 hover:text-green-800' },
        'warning': { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-800', btn: 'text-amber-600 hover:text-amber-800' }
    };
    const colors = colorMap[type] || colorMap['error'];

    alertArea.innerHTML = `
        <div class="mb-4 px-4 py-3 rounded-lg ${colors.bg} ${colors.border} border ${colors.text}">
            <div class="flex items-start">
                <div class="flex-1">
                    <div class="font-semibold mb-1">${title}</div>
                    <div class="text-sm">${message}</div>
                </div>
                <button type="button" class="ml-3 flex-shrink-0 ${colors.btn}" onclick="this.closest('.mb-4').remove()">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
}



