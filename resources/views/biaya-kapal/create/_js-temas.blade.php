
    // Container cards submit aligned arrays understood by the existing store action.
    let temasSectionCounter = 0;
    const temasEscape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c]));
    const temasMoney = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    const temasSize = value => {
        const match = String(value || '').match(/20|40|45/);
        return match ? match[0] + 'ft' : String(value || '');
    };
    const temasInputClass = 'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500';

    function clearAllTemasSections() {
        if (temasSectionsContainer) temasSectionsContainer.replaceChildren();
        if (nominalInput) nominalInput.value = '';
    }
    function initializeTemasSections() {
        clearAllTemasSections();
        addTemasSection();
    }
    if (addTemasSectionBtn) addTemasSectionBtn.addEventListener('click', () => addTemasSection());

    function addTemasSection(data = null) {
        const sectionIndex = ++temasSectionCounter;
        const section = document.createElement('div');
        section.className = 'temas-section mb-6 p-4 border border-blue-200 rounded-xl bg-white';
        section.dataset.sectionIndex = sectionIndex;
        section.temasContainers = [];
        section.innerHTML = `
            <div class="flex items-center justify-between gap-3 mb-4">
                <h4 class="font-semibold text-blue-900">Kapal / Voyage ${sectionIndex}</h4>
                <button type="button" onclick="removeTemasSection(${sectionIndex})" class="text-sm text-red-600 hover:underline">Hapus kapal</button>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Mode pembayaran TEMAS
                    <select name="temas[${sectionIndex}][payment_mode]" class="temas-payment-mode ${temasInputClass} mt-1">
                        <option value="lunas">Bayar langsung / Lunas</option>
                        <option value="dp">DP / Uang muka</option>
                        <option value="pelunasan_dp">Pelunasan DP</option>
                    </select>
                </label>
                <p class="temas-payment-help text-sm text-blue-800"></p>
                <label class="temas-dp-input-wrap hidden block text-sm">Nominal DP dibayar (Rp)
                    <input type="number" min="0.01" step="0.01" name="temas[${sectionIndex}][nominal_dibayar]" class="temas-dp-amount ${temasInputClass} mt-1" disabled>
                </label>
                <div class="temas-dp-reference-wrap hidden">
                    <label class="block text-sm">Referensi DP
                        <select name="temas[${sectionIndex}][dp_stage_id]" class="temas-dp-reference ${temasInputClass} mt-1" disabled><option value="">Pilih DP yang akan dilunasi</option></select>
                    </label>
                    <button type="button" class="temas-reload-dp text-sm text-blue-700 mt-1">Muat ulang daftar DP</button>
                    <p class="temas-dp-status text-sm text-gray-600" role="status"></p>
                    <p class="text-sm mt-2">DP sudah dibayar: <strong class="temas-dp-paid">Rp 0</strong></p>
                </div>
            </div>
            <h5 class="font-semibold text-gray-800 mb-3">1. Pilih perjalanan kapal</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <label class="text-sm text-gray-700">Nama kapal
                    <select name="temas[${sectionIndex}][kapal]" class="kapal-select-temas ${temasInputClass} mt-1" required>
                        <option value="">Pilih kapal</option>
                        ${allKapalsData.map(k => '<option value="' + temasEscape(k.nama_kapal) + '">' + temasEscape(k.nama_kapal) + '</option>').join('')}
                    </select>
                </label>
                <div>
                    <label class="text-sm text-gray-700">Nomor voyage
                        <select name="temas[${sectionIndex}][voyage]" class="voyage-select-temas ${temasInputClass} mt-1" required disabled><option value="">Pilih kapal terlebih dahulu</option></select>
                        <input name="temas[${sectionIndex}][voyage]" class="voyage-input-temas ${temasInputClass} mt-1 hidden" placeholder="Ketik nomor voyage" required disabled>
                    </label>
                    <button type="button" class="voyage-manual-btn-temas mt-2 text-sm text-blue-700">Ketik voyage manual</button>
                </div>
            </div>
            <fieldset class="temas-billing-details">
            <h5 class="font-semibold text-gray-800">2. Isi biaya per kontainer</h5>
            <p class="text-sm text-gray-500 mt-1 mb-3">Tambahkan kontainer yang ditagihkan, lalu isi biayanya. Satu kontainer dapat memiliki beberapa jenis biaya.</p>
            <p class="temas-status text-sm text-blue-800 bg-blue-50 rounded-lg p-3 mb-3" role="status" aria-live="polite">Pilih kapal dan voyage untuk memuat kontainer dari manifest.</p>
            <div class="temas-container-cards space-y-4"></div>
            <button type="button" class="add-container-temas mt-3 mb-6 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">+ Tambah kontainer</button>
            <h5 class="font-semibold text-gray-800 border-t pt-4">3. Periksa total tagihan</h5>
            <p class="temas-tax-help text-sm text-gray-500 mt-1 mb-3">Pajak, materai, admin, dan penyesuaian berlaku untuk seluruh kontainer pada kapal / voyage ini.</p>
            <p class="temas-count text-sm text-blue-700 mb-3"></p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total biaya semua kontainer</label>
                    <input type="text" class="sub-total-display-temas w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="temas[${sectionIndex}][sub_total]" class="sub-total-value-temas" value="0">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">PPH (2%)</label>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" name="temas[${sectionIndex}][pph_active]" class="pph-active-temas w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" checked onchange="calculateTemasSectionTotal(${sectionIndex})">
                            <span class="text-[10px] text-gray-600 font-medium cursor-pointer" onclick="this.previousElementSibling.click()">Aktifkan</span>
                        </div>
                    </div>
                    <input type="text" class="pph-display-temas w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="Rp 0">
                    <input type="hidden" name="temas[${sectionIndex}][pph]" class="pph-value-temas" value="0">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">PPN (11%)</label>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" name="temas[${sectionIndex}][ppn_active]" class="ppn-active-temas w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" onchange="calculateTemasSectionTotal(${sectionIndex})">
                            <span class="text-[10px] text-gray-600 font-medium cursor-pointer" onclick="this.previousElementSibling.click()">Aktifkan</span>
                        </div>
                    </div>
                    <input type="text" class="ppn-display-temas w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="Rp 0">
                    <input type="hidden" name="temas[${sectionIndex}][ppn]" class="ppn-value-temas" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                    <input type="text" class="materai-display-temas w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="temas[${sectionIndex}][biaya_materai]" class="materai-value-temas" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penyesuaian (minus untuk potongan)</label>
                    <input type="text" class="adjustment-display-temas w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="temas[${sectionIndex}][adjustment]" class="adjustment-value-temas" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Admin</label>
                    <input type="text" class="admin-display-temas w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="temas[${sectionIndex}][biaya_admin]" class="admin-value-temas" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total tagihan kapal ini</label>
                    <input type="text" class="grand-total-display-temas w-full px-3 py-2 border border-gray-300 rounded-lg bg-emerald-50 font-semibold cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="temas[${sectionIndex}][grand_total]" class="grand-total-value-temas" value="0">
                    <p class="text-xs text-gray-500 mt-2">Biaya kontainer + PPN − PPH + materai + admin + penyesuaian.</p>
                </div>
            </div>
            </fieldset>
            <div class="bg-emerald-50 rounded-lg p-4 mb-4 flex justify-between gap-3">
                <span>Nominal transaksi ini</span><strong class="temas-cash-display">Rp 0</strong>
                <input type="hidden" class="temas-cash-value" value="0">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="temas[${sectionIndex}][nomor_referensi]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan No. Referensi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                    <input type="text" name="temas[${sectionIndex}][penerima]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama penerima">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="temas[${sectionIndex}][nomor_rekening]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nomor rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice Vendor</label>
                    <input type="date" name="temas[${sectionIndex}][tanggal_invoice_vendor]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="temas[${sectionIndex}][keterangan]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Catatan opsional..."></textarea>
                </div>
            </div>
        `;
        

        temasSectionsContainer.appendChild(section);
        const kapalSelect = section.querySelector('.kapal-select-temas');
        const voyageSelect = section.querySelector('.voyage-select-temas');
        const voyageInput = section.querySelector('.voyage-input-temas');
        kapalSelect.addEventListener('change', () => loadVoyagesForTemasSection(section));
        voyageSelect.addEventListener('change', () => loadTemasContainers(section));
        voyageInput.addEventListener('change', () => loadTemasContainers(section));
        section.querySelector('.voyage-manual-btn-temas').addEventListener('click', function() {
            const manual = voyageInput.disabled;
            voyageInput.disabled = !manual;
            voyageInput.classList.toggle('hidden', !manual);
            voyageSelect.disabled = manual || !kapalSelect.value;
            voyageSelect.classList.toggle('hidden', manual);
            this.textContent = manual ? 'Pilih voyage dari daftar' : 'Ketik voyage manual';
            loadTemasContainers(section);
        });
        section.querySelector('.add-container-temas').addEventListener('click', () => addTemasContainer(section));
        section.querySelector('.temas-payment-mode').addEventListener('change', () => {
            updateTemasPaymentMode(section);
            if (section.querySelector('.temas-payment-mode').value === 'pelunasan_dp') loadTemasDps(section);
        });
        section.querySelector('.temas-dp-amount').addEventListener('input', () => calculateTemasSectionTotal(sectionIndex));
        section.querySelector('.temas-reload-dp').addEventListener('click', () => loadTemasDps(section));
        section.querySelector('.temas-dp-reference').addEventListener('change', () => {
            const option = section.querySelector('.temas-dp-reference').selectedOptions[0];
            section.dataset.dpAmount = option?.dataset.amount || '0';
            if (option?.value) {
                if (![...kapalSelect.options].some(o => o.value === option.dataset.kapal)) kapalSelect.add(new Option(option.dataset.kapal, option.dataset.kapal));
                kapalSelect.value = option.dataset.kapal;
                voyageInput.value = option.dataset.voyage;
                voyageSelect.replaceChildren(new Option(option.dataset.voyage, option.dataset.voyage));
                loadTemasContainers(section);
            }
            updateTemasPaymentMode(section);
        });
        addTemasContainer(section);
        // PPH Manual edit listener
        const pphDisplay = section.querySelector('.pph-display-temas');
        const pphValue = section.querySelector('.pph-value-temas');
        pphDisplay.addEventListener('input', function() {
            this.setAttribute('data-manual-pph', 'true');
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                pphValue.value = val;
            } else {
                this.value = 'Rp 0';
                pphValue.value = 0;
            }
            calculateTemasSectionTotal(sectionIndex);
        });
 
        // PPN Manual edit listener
        const ppnDisplay = section.querySelector('.ppn-display-temas');
        const ppnValue = section.querySelector('.ppn-value-temas');
        ppnDisplay.addEventListener('input', function() {
            this.setAttribute('data-manual-ppn', 'true');
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                ppnValue.value = val;
            } else {
                this.value = 'Rp 0';
                ppnValue.value = 0;
            }
            calculateTemasSectionTotal(sectionIndex);
        });
 
        // Materai Manual edit listener
        const materaiDisplay = section.querySelector('.materai-display-temas');
        const materaiValue = section.querySelector('.materai-value-temas');
        materaiDisplay.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                materaiValue.value = val;
            } else {
                this.value = 'Rp 0';
                materaiValue.value = 0;
            }
            calculateTemasSectionTotal(sectionIndex);
        });

        // Admin Manual edit listener
        const adminDisplay = section.querySelector('.admin-display-temas');
        const adminValue = section.querySelector('.admin-value-temas');
        adminDisplay.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                adminValue.value = val;
            } else {
                this.value = 'Rp 0';
                adminValue.value = 0;
            }
            calculateTemasSectionTotal(sectionIndex);
        });
 
        // Adjustment Manual edit listener
        const adjustmentDisplay = section.querySelector('.adjustment-display-temas');
        const adjustmentValue = section.querySelector('.adjustment-value-temas');
        adjustmentDisplay.addEventListener('input', function() {
            // Allow negative prefix for adjustment
            let isNegative = this.value.includes('-');
            let val = this.value.replace(/\D/g, '');
            
            if (val) {
                let numVal = parseInt(val) * (isNegative ? -1 : 1);
                this.value = (isNegative ? '- Rp ' : 'Rp ') + parseInt(val).toLocaleString('id-ID');
                adjustmentValue.value = numVal;
            } else {
                this.value = 'Rp 0';
                adjustmentValue.value = 0;
            }
            calculateTemasSectionTotal(sectionIndex);
        });
        
        if (data) hydrateTemasSection(section, data);
        updateTemasPaymentMode(section);
        return section;
    }

    @include('biaya-kapal.create._js-temas-payments')

    function activeTemasVoyage(section) {
        const input = section.querySelector('.voyage-input-temas');
        return input.disabled ? section.querySelector('.voyage-select-temas').value : input.value.trim();
    }
    function invalidateTemasManifest(section) {
        section.manifestRequest = (section.manifestRequest || 0) + 1;
        section.temasContainers = [];
        section.querySelectorAll('.temas-container-card').forEach(card => refreshTemasOptions(section, card));
        calculateTemasSectionTotal(Number(section.dataset.sectionIndex));
    }
    async function loadVoyagesForTemasSection(section) {
        invalidateTemasManifest(section);
        const kapal = section.querySelector('.kapal-select-temas').value;
        const select = section.querySelector('.voyage-select-temas');
        const request = section.voyageRequest = (section.voyageRequest || 0) + 1;
        select.disabled = true;
        select.innerHTML = '<option value="">Pilih kapal terlebih dahulu</option>';
        section.querySelector('.temas-status').textContent = 'Pilih voyage untuk memuat manifest. Periksa kembali kontainer yang sudah diisi jika perjalanan berubah.';
        if (!kapal) return;
        select.innerHTML = '<option value="">Memuat voyage...</option>';
        try {
            const response = await fetch('{{ url("biaya-kapal/get-voyages") }}/' + encodeURIComponent(kapal));
            if (!response.ok) throw new Error('voyages');
            const data = await response.json();
            if (!data.success) throw new Error('voyages');
            if (!section.isConnected || section.voyageRequest !== request) return;
            select.innerHTML = '<option value="">Pilih voyage</option><option value="DOCK">DOCK</option>' +
                (data.voyages || []).filter(v => v !== 'DOCK').map(v => '<option value="' + temasEscape(v) + '">' + temasEscape(v) + '</option>').join('');
        } catch (error) {
            if (!section.isConnected || section.voyageRequest !== request) return;
            select.innerHTML = '<option value="">Daftar gagal dimuat</option><option value="DOCK">DOCK</option>';
            section.querySelector('.temas-status').textContent = 'Voyage gagal dimuat. Pilih ulang kapal untuk mencoba lagi, atau ketik voyage manual.';
        }
        if (!section.isConnected || section.voyageRequest !== request) return;
        select.disabled = section.querySelector('.temas-payment-mode').value === 'pelunasan_dp' || !section.querySelector('.voyage-input-temas').disabled;
        if (!section.querySelector('.voyage-input-temas').disabled) loadTemasContainers(section);
    }
    async function loadTemasContainers(section) {
        invalidateTemasManifest(section);
        const request = section.manifestRequest;
        const kapal = section.querySelector('.kapal-select-temas').value;
        const voyage = activeTemasVoyage(section);
        const status = section.querySelector('.temas-status');
        if (!kapal || !voyage) {
            status.textContent = 'Pilih kapal dan voyage. Nomor kontainer juga dapat diketik manual.';
            return;
        }
        status.textContent = 'Memuat daftar kontainer dari manifest...';
        try {
            const response = await fetch('{{ url("biaya-kapal/get-container-counts") }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({kapal, voyage})
            });
            if (!response.ok) throw new Error('manifest');
            const data = await response.json();
            if (!data.success) throw new Error('manifest');
            if (!section.isConnected || section.manifestRequest !== request) return;
            section.temasContainers = data.containers_data || [];
            status.textContent = section.temasContainers.length
                ? section.temasContainers.length + ' kontainer tersedia · Rute: ' + (data.pelabuhan_asal || '-') + ' → ' + (data.pelabuhan_tujuan || '-') + '. Pilih kontainer yang ditagihkan.'
                : 'Belum ada kontainer pada manifest ini. Ketik nomor kontainer manual.';
            section.querySelectorAll('.temas-container-card').forEach(card => refreshTemasOptions(section, card));
            calculateTemasSectionTotal(Number(section.dataset.sectionIndex));
        } catch (error) {
            if (!section.isConnected || section.manifestRequest !== request) return;
            status.textContent = 'Manifest gagal dimuat. Pilih ulang voyage untuk mencoba lagi, atau ketik nomor kontainer manual.';
        }
    }
    function refreshTemasOptions(section, card) {
        const select = card.querySelector('.temas-manifest-select');
        select.innerHTML = '<option value="">Pilih dari manifest</option>' + section.temasContainers.map(c =>
            '<option value="' + temasEscape(c.nomor_kontainer) + '">' + temasEscape(c.nomor_kontainer) + ' (' + temasEscape(temasSize(c.size)) + ')</option>').join('');
        select.disabled = !section.temasContainers.length;
        const number = card.querySelector('.temas-container-number').value.trim().toUpperCase();
        const match = section.temasContainers.find(c => String(c.nomor_kontainer).trim().toUpperCase() === number);
        select.value = match ? match.nomor_kontainer : '';
        card.dataset.blId = match ? match.id : '';
        if (match) card.querySelector('.temas-container-size').value = temasSize(match.size);
    }
    function addTemasContainer(section) {
        const card = document.createElement('div');
        card.className = 'temas-container-card border border-gray-200 rounded-lg p-4 bg-gray-50';
        card.innerHTML = `
            <div class="flex justify-between items-center gap-3 mb-3">
                <strong class="temas-container-title text-gray-800">Kontainer</strong>
                <button type="button" class="remove-container-temas text-sm text-red-600 hover:underline">Hapus kontainer</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <label class="text-sm text-gray-700">Kontainer dari manifest<select class="temas-manifest-select ${temasInputClass} mt-1"></select></label>
                <label class="text-sm text-gray-700">Nomor kontainer<input class="temas-container-number ${temasInputClass} mt-1 uppercase" placeholder="Contoh: TEMU1234567" maxlength="100" required></label>
                <label class="text-sm text-gray-700">Ukuran<select class="temas-container-size ${temasInputClass} mt-1" required><option value="">Pilih ukuran</option><option value="20ft">20ft</option><option value="40ft">40ft</option><option value="45ft">45ft</option></select></label>
            </div>
            <div class="temas-cost-list space-y-3"></div>
            <div class="flex flex-wrap justify-between items-center gap-3 mt-3">
                <button type="button" class="add-cost-temas text-sm text-blue-700 hover:underline">+ Tambah biaya untuk kontainer ini</button>
                <span class="text-sm">Total kontainer: <strong class="temas-container-total">Rp 0</strong></span>
            </div>
        `;
        section.querySelector('.temas-container-cards').appendChild(card);
        refreshTemasOptions(section, card);
        card.querySelector('.temas-manifest-select').addEventListener('change', event => {
            if (!event.target.value) return;
            card.querySelector('.temas-container-number').value = event.target.value;
            refreshTemasOptions(section, card);
        });
        card.querySelector('.temas-container-number').addEventListener('input', () => refreshTemasOptions(section, card));
        card.querySelector('.add-cost-temas').addEventListener('click', () => addTemasCost(section, card));
        card.querySelector('.remove-container-temas').addEventListener('click', () => {
            if (!confirm('Hapus kontainer ini beserta seluruh biayanya?')) return;
            card.remove();
            calculateTemasSectionTotal(Number(section.dataset.sectionIndex));
        });
        ['input', 'change'].forEach(event => card.addEventListener(event, () => calculateTemasSectionTotal(Number(section.dataset.sectionIndex))));
        addTemasCost(section, card);
        return card;
    }
    function addTemasCost(section, card) {
        const field = key => 'temas[' + section.dataset.sectionIndex + '][' + key + '][]';
        const row = document.createElement('div');
        row.className = 'temas-type-item border border-gray-200 rounded-lg p-3 bg-white';
        row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="text-sm text-gray-700">Jenis biaya
                    <select name="${field('types')}" class="type-select-temas ${temasInputClass} mt-1" required>
                        <option value="">Pilih jenis biaya</option><option value="MANUAL">Tulis biaya manual</option>
                        ${pricelistTemasData.map(item => '<option value="' + temasEscape(item.id) + '">' + temasEscape(item.jenis_biaya) + (item.size ? ' · ' + temasEscape(item.size) : '') + (item.lokasi ? ' · ' + temasEscape(item.lokasi) : '') + ' · ' + temasMoney(item.harga) + '</option>').join('')}
                    </select>
                </label>
                <label class="temas-manual-label hidden text-sm text-gray-700">Nama biaya manual<input name="${field('manual_names')}" class="type-manual-input-temas ${temasInputClass} mt-1" maxlength="255" placeholder="Contoh: Biaya penanganan"></label>
                <label class="text-sm text-gray-700">Biaya kontainer ini (Rp)<input type="number" name="${field('custom_prices')}" class="price-input-temas ${temasInputClass} mt-1" min="0" step="0.01" placeholder="0" required></label>
                <label class="text-sm text-gray-700">Lokasi<select name="${field('lokasi_items')}" class="lokasi-select-temas ${temasInputClass} mt-1"><option value="">Pilih lokasi (opsional)</option><option>Jakarta</option><option>Batam</option><option>Pinang</option></select></label>
            </div>
            <input type="hidden" name="${field('nomor_kontainers')}" class="temas-row-number">
            <input type="hidden" name="${field('bl_ids')}" class="temas-row-bl">
            <input type="hidden" name="${field('size_items')}" class="temas-row-size">
            <input type="hidden" name="${field('quantities')}" class="quantity-input-temas" value="1">
            <div class="flex flex-wrap items-center gap-4 mt-3 text-sm">
                <span class="text-gray-500">Kegiatan:</span>
                ${[['is_muat', 'Muat'], ['is_bongkar', 'Bongkar']].map(([key, label]) => '<label class="flex items-center gap-2"><input type="hidden" name="' + field(key) + '" value="0"><input type="checkbox" class="temas-activity"> ' + label + '</label>').join('')}
                <button type="button" class="remove-cost-temas text-red-600 hover:underline ml-auto">Hapus biaya</button>
            </div>
        `;
        card.querySelector('.temas-cost-list').appendChild(row);
        row.querySelector('.type-select-temas').addEventListener('change', event => {
            const manual = event.target.value === 'MANUAL';
            row.querySelector('.temas-manual-label').classList.toggle('hidden', !manual);
            row.querySelector('.type-manual-input-temas').required = manual;
            const item = pricelistTemasData.find(item => String(item.id) === event.target.value);
            row.querySelector('.price-input-temas').value = item ? Number(item.harga) || 0 : '';
            row.querySelector('.lokasi-select-temas').value = item ? item.lokasi || '' : '';
        });
        row.querySelector('.remove-cost-temas').addEventListener('click', () => {
            if (card.querySelectorAll('.temas-type-item').length === 1) {
                if (!confirm('Hapus kontainer ini beserta rincian biayanya?')) return;
                card.remove();
            } else row.remove();
            calculateTemasSectionTotal(Number(section.dataset.sectionIndex));
        });
        calculateTemasSectionTotal(Number(section.dataset.sectionIndex));
        return row;
    }
    window.removeTemasSection = function(index) {
        const section = temasSectionsContainer.querySelector('[data-section-index="' + index + '"]');
        if (!section || !confirm('Hapus kapal ini beserta seluruh kontainer dan biayanya?')) return;
        section.remove();
        if (!temasSectionsContainer.children.length) addTemasSection();
        calculateTotalFromAllTemasSections();
    };
    function calculateTemasSectionTotal(sectionIndex) {
        const section = document.querySelector(`.temas-section[data-section-index="${sectionIndex}"]`);
        if (!section) return;
        const mode = section.querySelector('.temas-payment-mode')?.value || 'lunas';
        if (mode === 'dp') {
            section.querySelector('.kapal-select-temas').setCustomValidity('');
            const amount = Number(section.querySelector('.temas-dp-amount').value) || 0;
            section.querySelector('.temas-cash-value').value = amount;
            section.querySelector('.temas-cash-display').textContent = temasMoney(amount);
            calculateTotalFromAllTemasSections();
            return;
        }
        

        const cards = [...section.querySelectorAll('.temas-container-card')];
        const numbers = cards.map(card => card.querySelector('.temas-container-number').value.trim().toUpperCase());
        cards.forEach((card, index) => {
            const number = numbers[index];
            card.querySelector('.temas-container-number').setCustomValidity(number && numbers.filter(n => n === number).length > 1 ? 'Kontainer ini sudah ditambahkan. Tambahkan biaya pada kontainer yang sama.' : '');
            const size = card.querySelector('.temas-container-size').value;
            card.querySelector('.temas-container-title').textContent = 'Kontainer ' + (index + 1) + (number ? ' · ' + number : '');
            let total = 0;
            card.querySelectorAll('.temas-type-item').forEach(row => {
                row.querySelector('.temas-row-number').value = number;
                row.querySelector('.temas-row-bl').value = card.dataset.blId || '';
                row.querySelector('.temas-row-size').value = size;
                row.querySelectorAll('.temas-activity').forEach(box => box.previousElementSibling.value = box.checked ? '1' : '0');
                const select = row.querySelector('.type-select-temas');
                const tariff = pricelistTemasData.find(item => String(item.id) === select.value);
                select.setCustomValidity(tariff && tariff.size && size && temasSize(tariff.size) !== size ? 'Ukuran tarif berbeda dengan kontainer. Pilih tarif sesuai ukuran atau tulis biaya manual.' : '');
                total += Number(row.querySelector('.price-input-temas').value) || 0;
            });
            card.querySelector('.temas-container-total').textContent = temasMoney(total);
        });
        section.querySelector('.kapal-select-temas').setCustomValidity(cards.length ? '' : 'Tambahkan minimal satu kontainer beserta biayanya.');
        section.querySelector('.temas-count').textContent = cards.length + ' kontainer · ' + section.querySelectorAll('.temas-type-item').length + ' rincian biaya';

        const typeItems = section.querySelectorAll('.temas-type-item');
        
        let subTotal = 0;
        typeItems.forEach(item => {
            const price = parseFloat(item.querySelector('.price-input-temas').value) || 0;
            const qty = parseFloat(item.querySelector('.quantity-input-temas').value) || 0;
            subTotal += (price * qty);
        });
        
        section.querySelector('.sub-total-display-temas').value = subTotal > 0 ? `Rp ${subTotal.toLocaleString('id-ID')}` : 'Rp 0';
        section.querySelector('.sub-total-value-temas').value = subTotal;
        
        const pphActive = section.querySelector('.pph-active-temas').checked;
        const pphDisplay = section.querySelector('.pph-display-temas');
        const pphValue = section.querySelector('.pph-value-temas');
        pphDisplay.readOnly = !pphActive;
        
        let pph = 0;
        if (pphDisplay.hasAttribute('data-manual-pph')) {
            pph = parseFloat(pphValue.value) || 0;
        } else {
            pph = Math.round(subTotal * 0.02);
            pphDisplay.value = pph > 0 ? `Rp ${pph.toLocaleString('id-ID')}` : 'Rp 0';
            pphValue.value = pph;
        }
 
        // PPN Calculation
        const ppnActive = section.querySelector('.ppn-active-temas').checked;
        const ppnDisplay = section.querySelector('.ppn-display-temas');
        const ppnValue = section.querySelector('.ppn-value-temas');
        ppnDisplay.readOnly = !ppnActive;
        
        let ppn = 0;
        if (ppnDisplay.hasAttribute('data-manual-ppn')) {
            ppn = parseFloat(ppnValue.value) || 0;
        } else {
            ppn = Math.round(subTotal * 0.11);
            ppnDisplay.value = ppn > 0 ? `Rp ${ppn.toLocaleString('id-ID')}` : 'Rp 0';
            ppnValue.value = ppn;
        }
 
        // Update Styling based on active state
        if (pphActive) {
            pphDisplay.classList.remove('bg-gray-100', 'text-gray-400');
            pphDisplay.classList.add('bg-white');
        } else {
            pphDisplay.classList.add('bg-gray-100', 'text-gray-400');
            pphDisplay.classList.remove('bg-white');
        }
 
        if (ppnActive) {
            ppnDisplay.classList.remove('bg-gray-100', 'text-gray-400');
            ppnDisplay.classList.add('bg-white');
        } else {
            ppnDisplay.classList.add('bg-gray-100', 'text-gray-400');
            ppnDisplay.classList.remove('bg-white');
        }
        
        const pphForCalculation = pphActive ? pph : 0;
        const ppnForCalculation = ppnActive ? ppn : 0;
        const materaiValue = parseFloat(section.querySelector('.materai-value-temas').value) || 0;
        const adminValue = parseFloat(section.querySelector('.admin-value-temas').value) || 0;
        const adjustmentValue = parseFloat(section.querySelector('.adjustment-value-temas').value) || 0;
        
        const grandTotal = mode === 'pelunasan_dp' ? subTotal : subTotal + ppnForCalculation - pphForCalculation + materaiValue + adminValue + adjustmentValue;
        
        section.querySelector('.grand-total-display-temas').value = temasMoney(grandTotal);
        section.querySelector('.grand-total-value-temas').value = grandTotal;
        const advance = mode === 'pelunasan_dp' ? Number(section.dataset.dpAmount || 0) : 0;
        const dpSelect = section.querySelector('.temas-dp-reference');
        if (dpSelect) dpSelect.setCustomValidity(mode === 'pelunasan_dp' && grandTotal < advance ? 'Tagihan akhir tidak boleh lebih kecil dari DP.' : '');
        const cashInput = section.querySelector('.temas-cash-value');
        if (cashInput) {
            cashInput.value = Math.max(0, Math.round((grandTotal - advance) * 100) / 100);
            section.querySelector('.temas-cash-display').textContent = temasMoney(cashInput.value);
        }
        
        calculateTotalFromAllTemasSections();
    }
    
    function calculateTotalFromAllTemasSections() {
        let grandTotalAll = 0;
        document.querySelectorAll('.temas-section').forEach(section => {
            grandTotalAll += parseFloat((section.querySelector('.temas-cash-value') || section.querySelector('.grand-total-value-temas')).value) || 0;
        });
        
        if (nominalInput) {
            nominalInput.value = grandTotalAll.toLocaleString('id-ID');
        }
    }
