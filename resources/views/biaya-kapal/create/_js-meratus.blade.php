
    // Container cards submit aligned arrays understood by the existing store action.
    let meratusSectionCounter = 0;
    const meratusEscape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c]));
    const meratusMoney = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    const meratusSize = value => {
        const match = String(value || '').match(/20|40|45/);
        return match ? match[0] + 'ft' : String(value || '');
    };
    const meratusInputClass = 'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500';

    function clearAllMeratusSections() {
        if (meratusSectionsContainer) meratusSectionsContainer.replaceChildren();
        if (nominalInput) nominalInput.value = '';
    }
    function initializeMeratusSections() {
        clearAllMeratusSections();
        addMeratusSection();
    }
    if (addMeratusSectionBtn) addMeratusSectionBtn.addEventListener('click', () => {
        const source = meratusSectionsContainer?.querySelector('.meratus-section:last-child');
        const section = addMeratusSection();
        if (!source || source.querySelector('.meratus-payment-mode').value !== 'dp') return;

        section.querySelector('.meratus-payment-mode').value = 'dp';
        section.querySelector('.meratus-dp-date').value = source.querySelector('.meratus-dp-date').value;
        section.querySelector('.meratus-dp-bank').value = source.querySelector('.meratus-dp-bank').value;
        section.querySelector('.meratus-dp-description').value = source.querySelector('.meratus-dp-description').value;
        section.querySelector('.meratus-account-input').value = source.querySelector('.meratus-account-input').value;
        const sourceRecipient = source.querySelector('[name$="[penerima]"]');
        const targetRecipient = section.querySelector('[name$="[penerima]"]');
        if (sourceRecipient && targetRecipient) targetRecipient.value = sourceRecipient.value;
        updateMeratusPaymentMode(section);
    });

    function addMeratusSection(data = null) {
        const sectionIndex = ++meratusSectionCounter;
        const section = document.createElement('div');
        section.className = 'meratus-section mb-6 p-4 border border-blue-200 rounded-xl bg-white';
        section.dataset.sectionIndex = sectionIndex;
        section.meratusContainers = [];
        section.innerHTML = `
            <div class="flex items-center justify-between gap-3 mb-4">
                <h4 class="font-semibold text-blue-900">Kapal / Voyage ${sectionIndex}</h4>
                <button type="button" onclick="removeMeratusSection(${sectionIndex})" class="text-sm text-red-600 hover:underline">Hapus kapal</button>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Mode pembayaran MERATUS
                    <select name="meratus[${sectionIndex}][payment_mode]" class="meratus-payment-mode ${meratusInputClass} mt-1">
                        <option value="lunas">Bayar langsung / Lunas</option>
                        <option value="dp">DP / Uang muka</option>
                        <option value="pelunasan_dp">Pelunasan DP</option>
                    </select>
                </label>
                <p class="meratus-payment-help text-sm text-blue-800"></p>
                <label class="meratus-dp-input-wrap hidden block text-sm">Nominal DP dibayar (Rp)
                    <input type="number" min="0.01" step="0.01" name="meratus[${sectionIndex}][nominal_dibayar]" class="meratus-dp-amount ${meratusInputClass} mt-1" disabled>
                </label>
                <div class="meratus-dp-metadata hidden grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="block text-sm">Tanggal DP
                        <input type="date" name="meratus[${sectionIndex}][tanggal_dp]" class="meratus-dp-date ${meratusInputClass} mt-1" disabled>
                    </label>
                    <label class="block text-sm">Nama Bank
                        <input type="text" name="meratus[${sectionIndex}][nama_bank]" class="meratus-dp-bank ${meratusInputClass} mt-1" placeholder="Contoh: BCA, Mandiri" disabled>
                    </label>
                    <label class="block text-sm md:col-span-2">Keterangan DP
                        <textarea name="meratus[${sectionIndex}][keterangan_dp]" class="meratus-dp-description ${meratusInputClass} mt-1" rows="2" placeholder="Keterangan pembayaran DP (opsional)" disabled></textarea>
                    </label>
                </div>
                <div class="meratus-dp-reference-wrap hidden">
                    <label class="block text-sm">Referensi DP
                        <select name="meratus[${sectionIndex}][dp_stage_ids][]" multiple size="5" class="meratus-dp-reference ${meratusInputClass} mt-1" disabled><option value="">Pilih DP yang akan dilunasi</option></select>
                    </label>
                    <p class="text-xs text-gray-600 mt-1">Pilih satu atau lebih saldo DP dari kapal/voyage mana pun (tahan Ctrl untuk memilih beberapa). Saldo DP paling lama digunakan terlebih dahulu.</p>
                    <button type="button" class="meratus-reload-dp text-sm text-blue-700 mt-1">Muat ulang daftar DP</button>
                    <p class="meratus-dp-status text-sm text-gray-600" role="status"></p>
                    <div class="meratus-dp-summary hidden mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-amber-700">Saldo DP tersedia</p>
                        <p class="meratus-dp-paid mt-1 text-lg font-bold text-amber-900">Rp 0</p>
                        <p class="text-xs text-amber-700 mt-1">Saldo dipakai sesuai nilai tagihan. Sisanya bisa digunakan pada tagihan kapal/voyage lain.</p>
                    </div>
                </div>
            </div>
            <h5 class="font-semibold text-gray-800 mb-3">1. Pilih perjalanan kapal</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <label class="text-sm text-gray-700">Nama kapal
                    <select name="meratus[${sectionIndex}][kapal]" class="kapal-select-meratus ${meratusInputClass} mt-1" required>
                        <option value="">Pilih kapal</option>
                        ${allKapalsData.map(k => '<option value="' + meratusEscape(k.nama_kapal) + '">' + meratusEscape(k.nama_kapal) + '</option>').join('')}
                    </select>
                </label>
                <div>
                    <label class="text-sm text-gray-700">Nomor voyage
                        <select name="meratus[${sectionIndex}][voyage]" class="voyage-select-meratus ${meratusInputClass} mt-1" required disabled><option value="">Pilih kapal terlebih dahulu</option></select>
                        <input name="meratus[${sectionIndex}][voyage]" class="voyage-input-meratus ${meratusInputClass} mt-1 hidden" placeholder="Ketik nomor voyage" required disabled>
                    </label>
                    <button type="button" class="voyage-manual-btn-meratus mt-2 text-sm text-blue-700">Ketik voyage manual</button>
                </div>
            </div>
            <fieldset class="meratus-billing-details">
            <h5 class="font-semibold text-gray-800">2. Isi biaya per nomor BL atau kontainer</h5>
            <p class="text-sm text-gray-500 mt-1 mb-3">Pilih nomor BL, lalu tentukan setiap biaya berlaku sekali per BL, sama untuk semua kontainer, atau untuk satu kontainer tertentu. Tambahkan rincian biaya lagi jika nominal tiap kontainer berbeda.</p>
            <p class="meratus-status text-sm text-blue-800 bg-blue-50 rounded-lg p-3 mb-3" role="status" aria-live="polite">Pilih kapal dan voyage untuk memuat nomor BL dan kontainer dari manifest.</p>
            <div class="meratus-container-cards space-y-4"></div>
            <button type="button" class="add-container-meratus mt-3 mb-6 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">+ Tambah nomor BL</button>
            <h5 class="font-semibold text-gray-800 border-t pt-4">3. Periksa total tagihan</h5>
            <p class="meratus-tax-help text-sm text-gray-500 mt-1 mb-3">Pajak, materai, admin, dan penyesuaian berlaku untuk seluruh BL pada kapal / voyage ini.</p>
            <p class="meratus-count text-sm text-blue-700 mb-3"></p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total biaya semua BL dan kontainer</label>
                    <input type="text" class="sub-total-display-meratus w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="meratus[${sectionIndex}][sub_total]" class="sub-total-value-meratus" value="0">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">PPH (2%)</label>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" name="meratus[${sectionIndex}][pph_active]" class="pph-active-meratus w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" checked onchange="calculateMeratusSectionTotal(${sectionIndex})">
                            <span class="text-[10px] text-gray-600 font-medium cursor-pointer" onclick="this.previousElementSibling.click()">Aktifkan</span>
                        </div>
                    </div>
                    <input type="text" class="pph-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][pph]" class="pph-value-meratus" value="0">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">PPN (11%)</label>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" name="meratus[${sectionIndex}][ppn_active]" class="ppn-active-meratus w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" onchange="calculateMeratusSectionTotal(${sectionIndex})">
                            <span class="text-[10px] text-gray-600 font-medium cursor-pointer" onclick="this.previousElementSibling.click()">Aktifkan</span>
                        </div>
                    </div>
                    <input type="text" class="ppn-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors duration-200" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][ppn]" class="ppn-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Materai</label>
                    <input type="text" class="materai-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][biaya_materai]" class="materai-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penyesuaian (minus untuk potongan)</label>
                    <input type="text" class="adjustment-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][adjustment]" class="adjustment-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Admin</label>
                    <input type="text" class="admin-display-meratus w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="Rp 0">
                    <input type="hidden" name="meratus[${sectionIndex}][biaya_admin]" class="admin-value-meratus" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total tagihan kapal ini</label>
                    <input type="text" class="grand-total-display-meratus w-full px-3 py-2 border border-gray-300 rounded-lg bg-emerald-50 font-semibold cursor-not-allowed" value="Rp 0" readonly>
                    <input type="hidden" name="meratus[${sectionIndex}][grand_total]" class="grand-total-value-meratus" value="0">
                    <p class="text-xs text-gray-500 mt-2">Biaya BL dan kontainer + PPN − PPH + materai + admin + penyesuaian.</p>
                </div>
            </div>
            </fieldset>
            <div class="bg-emerald-50 rounded-lg p-4 mb-4">
                <div class="meratus-settlement-breakdown hidden space-y-2 mb-3 pb-3 border-b border-emerald-200">
                    <div class="flex justify-between gap-3 text-sm">
                        <span>Total tagihan</span><span class="meratus-settlement-total">Rp 0</span>
                    </div>
                    <div class="flex justify-between gap-3 text-sm font-medium text-red-700">
                        <span>DP digunakan</span><span class="meratus-settlement-dp">- Rp 0</span>
                    </div>
                    <div class="flex justify-between gap-3 text-sm text-blue-800">
                        <span>Sisa saldo DP</span><span class="meratus-settlement-balance">Rp 0</span>
                    </div>
                </div>
                <div class="flex justify-between gap-3">
                    <span>Nominal transaksi ini</span><strong class="meratus-cash-display">Rp 0</strong>
                </div>
                <input type="hidden" class="meratus-cash-value" value="0">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi</label>
                    <input type="text" name="meratus[${sectionIndex}][nomor_referensi]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan No. Referensi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                    <input type="text" name="meratus[${sectionIndex}][penerima]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama penerima">
                </div>
                <div>
                    <label class="meratus-account-label block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="meratus[${sectionIndex}][nomor_rekening]" class="meratus-account-input w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nomor rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice Vendor</label>
                    <input type="date" name="meratus[${sectionIndex}][tanggal_invoice_vendor]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="meratus[${sectionIndex}][keterangan]" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Catatan opsional..."></textarea>
                </div>
            </div>
        `;


        meratusSectionsContainer.appendChild(section);
        const kapalSelect = section.querySelector('.kapal-select-meratus');
        const voyageSelect = section.querySelector('.voyage-select-meratus');
        const voyageInput = section.querySelector('.voyage-input-meratus');
        kapalSelect.addEventListener('change', () => loadVoyagesForMeratusSection(section));
        voyageSelect.addEventListener('change', () => loadMeratusContainers(section));
        voyageInput.addEventListener('change', () => loadMeratusContainers(section));
        section.querySelector('.voyage-manual-btn-meratus').addEventListener('click', function() {
            const manual = voyageInput.disabled;
            voyageInput.disabled = !manual;
            voyageInput.classList.toggle('hidden', !manual);
            voyageSelect.disabled = manual || !kapalSelect.value;
            voyageSelect.classList.toggle('hidden', manual);
            this.textContent = manual ? 'Pilih voyage dari daftar' : 'Ketik voyage manual';
            loadMeratusContainers(section);
        });
        section.querySelector('.add-container-meratus').addEventListener('click', () => addMeratusContainer(section));
        section.querySelector('.meratus-payment-mode').addEventListener('change', () => {
            updateMeratusPaymentMode(section);
            if (section.querySelector('.meratus-payment-mode').value === 'pelunasan_dp') loadMeratusDps(section);
        });
        section.querySelector('.meratus-dp-amount').addEventListener('input', () => calculateMeratusSectionTotal(sectionIndex));
        section.querySelector('.meratus-reload-dp').addEventListener('click', () => loadMeratusDps(section));
        section.querySelector('.meratus-dp-reference').addEventListener('change', () => {
            updateMeratusDpSelection(section);
            updateMeratusPaymentMode(section);
        });
        addMeratusContainer(section);
        // PPH Manual edit listener
        const pphDisplay = section.querySelector('.pph-display-meratus');
        const pphValue = section.querySelector('.pph-value-meratus');
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
            calculateMeratusSectionTotal(sectionIndex);
        });

        // PPN Manual edit listener
        const ppnDisplay = section.querySelector('.ppn-display-meratus');
        const ppnValue = section.querySelector('.ppn-value-meratus');
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
            calculateMeratusSectionTotal(sectionIndex);
        });

        // Materai Manual edit listener
        const materaiDisplay = section.querySelector('.materai-display-meratus');
        const materaiValue = section.querySelector('.materai-value-meratus');
        materaiDisplay.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                materaiValue.value = val;
            } else {
                this.value = 'Rp 0';
                materaiValue.value = 0;
            }
            calculateMeratusSectionTotal(sectionIndex);
        });

        // Admin Manual edit listener
        const adminDisplay = section.querySelector('.admin-display-meratus');
        const adminValue = section.querySelector('.admin-value-meratus');
        adminDisplay.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = 'Rp ' + parseInt(val).toLocaleString('id-ID');
                adminValue.value = val;
            } else {
                this.value = 'Rp 0';
                adminValue.value = 0;
            }
            calculateMeratusSectionTotal(sectionIndex);
        });

        // Adjustment Manual edit listener
        const adjustmentDisplay = section.querySelector('.adjustment-display-meratus');
        const adjustmentValue = section.querySelector('.adjustment-value-meratus');
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
            calculateMeratusSectionTotal(sectionIndex);
        });

        if (data) hydrateMeratusSection(section, data);
        updateMeratusPaymentMode(section);
        return section;
    }

    @include('biaya-kapal.create._js-meratus-payments')

    function activeMeratusVoyage(section) {
        const input = section.querySelector('.voyage-input-meratus');
        return input.disabled ? section.querySelector('.voyage-select-meratus').value : input.value.trim();
    }
    function invalidateMeratusManifest(section) {
        section.manifestRequest = (section.manifestRequest || 0) + 1;
        section.meratusContainers = [];
        section.querySelectorAll('.meratus-container-card').forEach(card => refreshMeratusOptions(section, card));
        calculateMeratusSectionTotal(Number(section.dataset.sectionIndex));
    }
    async function loadVoyagesForMeratusSection(section) {
        invalidateMeratusManifest(section);
        const kapal = section.querySelector('.kapal-select-meratus').value;
        const select = section.querySelector('.voyage-select-meratus');
        const request = section.voyageRequest = (section.voyageRequest || 0) + 1;
        select.disabled = true;
        select.innerHTML = '<option value="">Pilih kapal terlebih dahulu</option>';
        section.querySelector('.meratus-status').textContent = 'Pilih voyage untuk memuat manifest. Periksa kembali kontainer yang sudah diisi jika perjalanan berubah.';
        if (!kapal) return;
        select.innerHTML = '<option value="">Memuat voyage...</option>';
        try {
            const response = await fetch('{{ url("biaya-kapal/get-voyages") }}/' + encodeURIComponent(kapal));
            if (!response.ok) throw new Error('voyages');
            const data = await response.json();
            if (!data.success) throw new Error('voyages');
            if (!section.isConnected || section.voyageRequest !== request) return;
            select.innerHTML = '<option value="">Pilih voyage</option><option value="DOCK">DOCK</option>' +
                (data.voyages || []).filter(v => v !== 'DOCK').map(v => '<option value="' + meratusEscape(v) + '">' + meratusEscape(v) + '</option>').join('');
        } catch (error) {
            if (!section.isConnected || section.voyageRequest !== request) return;
            select.innerHTML = '<option value="">Daftar gagal dimuat</option><option value="DOCK">DOCK</option>';
            section.querySelector('.meratus-status').textContent = 'Voyage gagal dimuat. Pilih ulang kapal untuk mencoba lagi, atau ketik voyage manual.';
        }
        if (!section.isConnected || section.voyageRequest !== request) return;
        select.disabled = !section.querySelector('.voyage-input-meratus').disabled;
        if (!section.querySelector('.voyage-input-meratus').disabled) loadMeratusContainers(section);
    }
    async function loadMeratusContainers(section) {
        invalidateMeratusManifest(section);
        const request = section.manifestRequest;
        const kapal = section.querySelector('.kapal-select-meratus').value;
        const voyage = activeMeratusVoyage(section);
        const status = section.querySelector('.meratus-status');
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
            section.meratusContainers = data.containers_data || [];
            const blCount = new Set(section.meratusContainers.map(c => c.nomor_bl).filter(Boolean)).size;
            status.textContent = section.meratusContainers.length
                ? blCount + ' nomor BL / ' + section.meratusContainers.length + ' kontainer tersedia · Rute: ' + (data.pelabuhan_asal || '-') + ' → ' + (data.pelabuhan_tujuan || '-') + '.'
                : 'Belum ada nomor BL dan kontainer pada manifest ini.';
            section.querySelectorAll('.meratus-container-card').forEach(card => refreshMeratusOptions(section, card));
            calculateMeratusSectionTotal(Number(section.dataset.sectionIndex));
        } catch (error) {
            if (!section.isConnected || section.manifestRequest !== request) return;
            status.textContent = 'Manifest gagal dimuat. Pilih ulang voyage untuk mencoba lagi, atau ketik nomor kontainer manual.';
        }
    }
    function refreshMeratusOptions(section, card) {
        const blSelect = card.querySelector('.meratus-bl-select');
        const containerSelect = card.querySelector('.meratus-manifest-select');
        let currentBl = blSelect.value || card.dataset.nomorBl || '';
        if (!currentBl && card.dataset.blId) {
            const legacyMatch = section.meratusContainers.find(c => String(c.id) === String(card.dataset.blId));
            if (legacyMatch?.nomor_bl) currentBl = legacyMatch.nomor_bl;
        }
        const bls = [...new Set(section.meratusContainers.map(c => c.nomor_bl).filter(Boolean))];
        blSelect.innerHTML = '<option value="">Pilih nomor BL</option>' + bls.map(bl => {
            const variants = [...new Set(section.meratusContainers
                .filter(c => String(c.nomor_bl) === bl)
                .map(c => c.nomor_bl_asli)
                .filter(Boolean))];
            const label = variants.length && (variants.length > 1 || variants[0] !== bl)
                ? bl + ' (' + variants.join(', ') + ')'
                : bl;
            return '<option value="' + meratusEscape(bl) + '">' + meratusEscape(label) + '</option>';
        }).join('');
        if (currentBl && !bls.includes(currentBl)) blSelect.add(new Option(currentBl + ' (data tersimpan)', currentBl));
        blSelect.value = currentBl;
        blSelect.disabled = !section.meratusContainers.length && !currentBl;

        const matches = currentBl ? section.meratusContainers.filter(c => String(c.nomor_bl) === currentBl) : [];
        containerSelect.innerHTML = matches.map(c => '<option selected value="' + meratusEscape(c.nomor_kontainer) + '">' +
            meratusEscape(c.nomor_kontainer) + ' (' + meratusEscape(meratusSize(c.size)) + ')</option>').join('');
        containerSelect.disabled = !matches.length;
        card.dataset.nomorBl = currentBl;
        card.dataset.blId = matches[0]?.id || card.dataset.blId || '';
        card.dataset.containerNumbers = matches.length
            ? matches.map(c => c.nomor_kontainer).join(', ')
            : (card.dataset.containerNumbers || '');
        if (matches[0]) card.querySelector('.meratus-container-size').value = meratusSize(matches[0].size);
        card.querySelector('.meratus-container-summary').textContent = matches.length
            ? matches.length + ' kontainer otomatis terpilih untuk BL ini.'
            : (currentBl ? 'Kontainer dari data tersimpan: ' + (card.dataset.containerNumbers || '-') : 'Pilih nomor BL terlebih dahulu.');
        card.querySelectorAll('.meratus-type-item').forEach(row => refreshMeratusCostContainers(section, card, row));
    }
    function refreshMeratusCostContainers(section, card, row) {
        const select = row.querySelector('.meratus-cost-container');
        const numbers = [...new Set((card.dataset.containerNumbers || '').split(',').map(value => value.trim()).filter(Boolean))];
        const selected = row.dataset.selectedContainer || select.value;
        select.innerHTML = '<option value="">Semua kontainer (tarif sama)</option>' + numbers.map(number => {
            const manifest = section.meratusContainers.find(item => String(item.nomor_bl) === card.dataset.nomorBl && item.nomor_kontainer === number);
            return '<option value="' + meratusEscape(number) + '">' + meratusEscape(number) +
                (manifest ? ' (' + meratusEscape(meratusSize(manifest.size)) + ')' : '') + '</option>';
        }).join('');
        select.value = numbers.includes(selected) ? selected : '';
        row.dataset.selectedContainer = select.value;
        const perContainer = row.querySelector('.meratus-per-container').checked;
        select.disabled = !perContainer;
        select.parentElement.classList.toggle('hidden', !perContainer);
    }
    function addMeratusContainer(section) {
        const card = document.createElement('div');
        card.className = 'meratus-container-card border border-gray-200 rounded-lg p-4 bg-gray-50';
        card.innerHTML = `
            <div class="flex justify-between items-center gap-3 mb-3">
                <strong class="meratus-container-title text-gray-800">Nomor BL</strong>
                <button type="button" class="remove-container-meratus text-sm text-red-600 hover:underline">Hapus BL</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <label class="text-sm text-gray-700">Nomor BL<select class="meratus-bl-select ${meratusInputClass} mt-1" required></select></label>
                <label class="text-sm text-gray-700">Kontainer pada BL<select class="meratus-manifest-select ${meratusInputClass} mt-1 h-24" multiple disabled></select></label>
                <label class="text-sm text-gray-700">Ukuran tarif<select class="meratus-container-size ${meratusInputClass} mt-1" required><option value="">Pilih ukuran</option><option value="20ft">20ft</option><option value="40ft">40ft</option><option value="45ft">45ft</option></select></label>
            </div>
            <p class="meratus-container-summary text-xs text-blue-700 mb-3"></p>
            <div class="meratus-cost-list space-y-3"></div>
            <div class="flex flex-wrap justify-between items-center gap-3 mt-3">
                <button type="button" class="add-cost-meratus text-sm text-blue-700 hover:underline">+ Tambah biaya untuk BL ini</button>
                <span class="text-sm">Total BL: <strong class="meratus-container-total">Rp 0</strong></span>
            </div>
        `;
        section.querySelector('.meratus-container-cards').appendChild(card);
        refreshMeratusOptions(section, card);
        card.querySelector('.meratus-bl-select').addEventListener('change', event => {
            card.dataset.nomorBl = event.target.value;
            card.dataset.blId = '';
            card.dataset.containerNumbers = '';
            card.querySelectorAll('.meratus-type-item').forEach(row => {
                row.dataset.selectedContainer = '';
                row.dataset.savedBlId = '';
                row.dataset.savedSize = '';
            });
            refreshMeratusOptions(section, card);
        });
        card.querySelector('.add-cost-meratus').addEventListener('click', () => addMeratusCost(section, card));
        card.querySelector('.remove-container-meratus').addEventListener('click', () => {
            if (!confirm('Hapus nomor BL ini beserta seluruh biayanya?')) return;
            card.remove();
            calculateMeratusSectionTotal(Number(section.dataset.sectionIndex));
        });
        ['input', 'change'].forEach(event => card.addEventListener(event, () => calculateMeratusSectionTotal(Number(section.dataset.sectionIndex))));
        addMeratusCost(section, card);
        return card;
    }
    function refreshMeratusCostTypes(row, preferredValue = null) {
        const typeSelect = row.querySelector('.type-select-meratus');
        const location = row.querySelector('.lokasi-select-meratus').value.trim();
        const selectedValue = preferredValue ?? typeSelect.value;
        const normalizedLocation = location.toLocaleLowerCase('id-ID');
        const availableItems = location
            ? pricelistMeratusData.filter(item => !String(item.lokasi || '').trim() || String(item.lokasi).trim().toLocaleLowerCase('id-ID') === normalizedLocation)
            : [];

        typeSelect.innerHTML = '<option value="">' + (location ? 'Pilih jenis biaya' : 'Pilih lokasi terlebih dahulu') + '</option>' +
            '<option value="MANUAL">Tulis biaya manual</option>' +
            availableItems.map(item => '<option value="' + meratusEscape(item.id) + '">' + meratusEscape(item.jenis_biaya) +
                (item.size ? ' · ' + meratusEscape(item.size) : '') + (item.lokasi ? ' · ' + meratusEscape(item.lokasi) : '') +
                ' · ' + meratusMoney(item.harga) + '</option>').join('');

        const selectionAvailable = [...typeSelect.options].some(option => option.value === selectedValue);
        typeSelect.value = selectionAvailable ? selectedValue : '';
        const manual = typeSelect.value === 'MANUAL';
        row.querySelector('.meratus-manual-label').classList.toggle('hidden', !manual);
        row.querySelector('.type-manual-input-meratus').required = manual;
        if (selectedValue && !selectionAvailable) row.querySelector('.price-input-meratus').value = '';
    }
    function meratusIsSingleCharge(costName) {
        let normalized = String(costName || '').toUpperCase().replace(/[^A-Z0-9]+/g, ' ').trim().replace(/\s+/g, ' ');
        if (normalized.startsWith('BIAYA ')) normalized = normalized.slice(6).trim();
        return ['ADM DO', 'DO KONTAINER', 'MATERAI'].includes(normalized);
    }
    function addMeratusCost(section, card) {
        const field = key => 'meratus[' + section.dataset.sectionIndex + '][' + key + '][]';
        const row = document.createElement('div');
        row.className = 'meratus-type-item border border-gray-200 rounded-lg p-3 bg-white';
        row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="text-sm text-gray-700">Lokasi
                    <select name="${field('lokasi_items')}" class="lokasi-select-meratus ${meratusInputClass} mt-1" required>
                        <option value="">Pilih lokasi terlebih dahulu</option>
                        ${[...new Set(['Jakarta', 'Batam', 'Pinang', ...pricelistMeratusData.map(item => String(item.lokasi || '').trim()).filter(Boolean)])].map(location => '<option value="' + meratusEscape(location) + '">' + meratusEscape(location) + '</option>').join('')}
                    </select>
                </label>
                <label class="text-sm text-gray-700">Jenis biaya
                    <select name="${field('types')}" class="type-select-meratus ${meratusInputClass} mt-1" required>
                        <option value="">Pilih lokasi terlebih dahulu</option><option value="MANUAL">Tulis biaya manual</option>
                    </select>
                </label>
                <label class="meratus-manual-label hidden text-sm text-gray-700">Nama biaya manual<input name="${field('manual_names')}" class="type-manual-input-meratus ${meratusInputClass} mt-1" maxlength="255" placeholder="Contoh: Biaya penanganan"></label>
                <label class="text-sm text-gray-700">Tarif biaya (Rp)<input type="number" name="${field('custom_prices')}" class="price-input-meratus ${meratusInputClass} mt-1" min="0" step="0.01" placeholder="0" required></label>
            </div>
            <input type="hidden" name="${field('nomor_kontainers')}" class="meratus-row-number">
            <input type="hidden" name="${field('nomor_bls')}" class="meratus-row-nomor-bl">
            <input type="hidden" name="${field('bl_ids')}" class="meratus-row-bl">
            <input type="hidden" name="${field('size_items')}" class="meratus-row-size">
            <input type="hidden" name="${field('quantities')}" class="quantity-input-meratus" value="1">
            <input type="hidden" name="${field('per_containers')}" class="meratus-row-per-container" value="1">
            <div class="flex flex-wrap items-center gap-4 mt-3 text-sm">
                <span class="meratus-cost-calculation text-blue-700 font-medium">Dihitung setelah nomor BL dipilih</span>
                <label class="flex items-center gap-2 font-medium text-gray-700">
                    <input type="checkbox" class="meratus-per-container w-4 h-4 rounded text-blue-600 focus:ring-blue-500" checked>
                    Biaya per kontainer
                </label>
                <label class="meratus-cost-container-wrap text-gray-700">Berlaku untuk
                    <select class="meratus-cost-container ${meratusInputClass} mt-1"></select>
                </label>
                <span class="text-gray-500">Kegiatan:</span>
                ${[['is_muat', 'Muat'], ['is_bongkar', 'Bongkar']].map(([key, label]) => '<label class="flex items-center gap-2"><input type="hidden" name="' + field(key) + '" value="0"><input type="checkbox" class="meratus-activity"> ' + label + '</label>').join('')}
                <button type="button" class="remove-cost-meratus text-red-600 hover:underline ml-auto">Hapus biaya</button>
            </div>
        `;
        card.querySelector('.meratus-cost-list').appendChild(row);
        const perContainerCheckbox = row.querySelector('.meratus-per-container');
        perContainerCheckbox.addEventListener('change', () => {
            row.dataset.perContainerTouched = 'true';
            refreshMeratusCostContainers(section, card, row);
        });
        row.querySelector('.meratus-cost-container').addEventListener('change', event => {
            row.dataset.selectedContainer = event.target.value;
        });
        refreshMeratusCostContainers(section, card, row);
        refreshMeratusCostTypes(row);
        row.querySelector('.lokasi-select-meratus').addEventListener('change', () => {
            refreshMeratusCostTypes(row);
            if (!row.querySelector('.type-select-meratus').value) {
                row.dataset.perContainerTouched = '';
                perContainerCheckbox.checked = true;
                refreshMeratusCostContainers(section, card, row);
            }
        });
        row.querySelector('.type-select-meratus').addEventListener('change', event => {
            const manual = event.target.value === 'MANUAL';
            row.querySelector('.meratus-manual-label').classList.toggle('hidden', !manual);
            row.querySelector('.type-manual-input-meratus').required = manual;
            const item = pricelistMeratusData.find(item => String(item.id) === event.target.value);
            row.querySelector('.price-input-meratus').value = item ? Number(item.harga) || 0 : '';
            row.dataset.perContainerTouched = '';
            perContainerCheckbox.checked = !meratusIsSingleCharge(item?.jenis_biaya || row.querySelector('.type-manual-input-meratus').value);
            refreshMeratusCostContainers(section, card, row);
        });
        row.querySelector('.type-manual-input-meratus').addEventListener('input', event => {
            if (!row.dataset.perContainerTouched) {
                perContainerCheckbox.checked = !meratusIsSingleCharge(event.target.value);
                refreshMeratusCostContainers(section, card, row);
            }
        });
        row.querySelector('.remove-cost-meratus').addEventListener('click', () => {
            if (card.querySelectorAll('.meratus-type-item').length === 1) {
                if (!confirm('Hapus nomor BL ini beserta rincian biayanya?')) return;
                card.remove();
            } else row.remove();
            calculateMeratusSectionTotal(Number(section.dataset.sectionIndex));
        });
        calculateMeratusSectionTotal(Number(section.dataset.sectionIndex));
        return row;
    }
    window.removeMeratusSection = function(index) {
        const section = meratusSectionsContainer.querySelector('[data-section-index="' + index + '"]');
        if (!section || !confirm('Hapus kapal ini beserta seluruh BL dan biayanya?')) return;
        section.remove();
        if (!meratusSectionsContainer.children.length) addMeratusSection();
        calculateTotalFromAllMeratusSections();
    };
    function calculateMeratusSectionTotal(sectionIndex) {
        const section = document.querySelector(`.meratus-section[data-section-index="${sectionIndex}"]`);
        if (!section) return;
        const mode = section.querySelector('.meratus-payment-mode')?.value || 'lunas';
        if (mode === 'dp') {
            section.querySelector('.kapal-select-meratus').setCustomValidity('');
            const amount = Number(section.querySelector('.meratus-dp-amount').value) || 0;
            section.querySelector('.meratus-cash-value').value = amount;
            section.querySelector('.meratus-cash-display').textContent = meratusMoney(amount);
            calculateTotalFromAllMeratusSections();
            return;
        }


        const cards = [...section.querySelectorAll('.meratus-container-card')];
        const numbers = cards.map(card => (card.dataset.nomorBl || '').trim().toUpperCase());
        cards.forEach((card, index) => {
            const nomorBl = numbers[index];
            const number = card.dataset.containerNumbers || '';
            const containerCount = Math.max(1, [...new Set(number.split(',').map(value => value.trim().toUpperCase()).filter(Boolean))].length);
            card.querySelector('.meratus-bl-select').setCustomValidity(nomorBl && numbers.filter(n => n === nomorBl).length > 1 ? 'Nomor BL ini sudah ditambahkan. Tambahkan biaya pada kartu BL yang sama.' : '');
            const size = card.querySelector('.meratus-container-size').value;
            card.querySelector('.meratus-container-title').textContent = 'BL ' + (index + 1) + (nomorBl ? ' · ' + nomorBl : '');
            let total = 0;
            card.querySelectorAll('.meratus-type-item').forEach(row => {
                const isPerContainer = row.querySelector('.meratus-per-container').checked;
                const selectedContainer = isPerContainer ? row.querySelector('.meratus-cost-container').value : '';
                const rowNumber = selectedContainer || number;
                const manifest = selectedContainer && section.meratusContainers.find(item => String(item.nomor_bl) === nomorBl && item.nomor_kontainer === selectedContainer);
                const rowSize = manifest ? meratusSize(manifest.size) : (selectedContainer && row.dataset.savedSize || size);
                row.querySelector('.meratus-row-number').value = rowNumber;
                row.querySelector('.meratus-row-nomor-bl').value = nomorBl;
                row.querySelector('.meratus-row-bl').value = manifest?.id || (selectedContainer && row.dataset.savedBlId) || card.dataset.blId || '';
                row.querySelector('.meratus-row-size').value = rowSize;
                row.querySelectorAll('.meratus-activity').forEach(box => box.previousElementSibling.value = box.checked ? '1' : '0');
                const select = row.querySelector('.type-select-meratus');
                const tariff = pricelistMeratusData.find(item => String(item.id) === select.value);
                select.setCustomValidity(tariff && tariff.size && rowSize && meratusSize(tariff.size) !== rowSize ? 'Ukuran tarif berbeda dengan kontainer. Pilih tarif sesuai ukuran atau tulis biaya manual.' : '');
                const quantity = isPerContainer && !selectedContainer ? containerCount : 1;
                row.querySelector('.quantity-input-meratus').value = quantity;
                row.querySelector('.meratus-row-per-container').value = isPerContainer ? '1' : '0';
                row.querySelector('.meratus-cost-calculation').textContent = isPerContainer
                    ? (selectedContainer ? 'Tarif untuk ' + selectedContainer : 'Tarif × ' + containerCount + ' kontainer')
                    : 'Dihitung 1× per BL';
                total += (Number(row.querySelector('.price-input-meratus').value) || 0) * quantity;
            });
            card.querySelector('.meratus-container-total').textContent = meratusMoney(total);
        });
        section.querySelector('.kapal-select-meratus').setCustomValidity(cards.length ? '' : 'Tambahkan minimal satu nomor BL beserta biayanya.');
        section.querySelector('.meratus-count').textContent = cards.length + ' nomor BL · ' + section.querySelectorAll('.meratus-type-item').length + ' rincian biaya';

        const typeItems = section.querySelectorAll('.meratus-type-item');

        let subTotal = 0;
        typeItems.forEach(item => {
            const price = parseFloat(item.querySelector('.price-input-meratus').value) || 0;
            const qty = parseFloat(item.querySelector('.quantity-input-meratus').value) || 0;
            subTotal += (price * qty);
        });

        section.querySelector('.sub-total-display-meratus').value = subTotal > 0 ? `Rp ${subTotal.toLocaleString('id-ID')}` : 'Rp 0';
        section.querySelector('.sub-total-value-meratus').value = subTotal;

        const pphActive = section.querySelector('.pph-active-meratus').checked;
        const pphDisplay = section.querySelector('.pph-display-meratus');
        const pphValue = section.querySelector('.pph-value-meratus');
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
        const ppnActive = section.querySelector('.ppn-active-meratus').checked;
        const ppnDisplay = section.querySelector('.ppn-display-meratus');
        const ppnValue = section.querySelector('.ppn-value-meratus');
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
        const materaiValue = parseFloat(section.querySelector('.materai-value-meratus').value) || 0;
        const adminValue = parseFloat(section.querySelector('.admin-value-meratus').value) || 0;
        const adjustmentValue = parseFloat(section.querySelector('.adjustment-value-meratus').value) || 0;

        const grandTotal = mode === 'pelunasan_dp' ? subTotal : subTotal + ppnForCalculation - pphForCalculation + materaiValue + adminValue + adjustmentValue;

        section.querySelector('.grand-total-display-meratus').value = meratusMoney(grandTotal);
        section.querySelector('.grand-total-value-meratus').value = grandTotal;
        const advance = mode === 'pelunasan_dp' ? Number(section.dataset.dpAmount || 0) : 0;
        const settlementBreakdown = section.querySelector('.meratus-settlement-breakdown');
        settlementBreakdown.classList.toggle('hidden', mode !== 'pelunasan_dp');
        section.querySelector('.meratus-settlement-total').textContent = meratusMoney(grandTotal);
        section.querySelector('.meratus-settlement-dp').textContent = '- ' + meratusMoney(Math.min(grandTotal, advance));
        const dpSelect = section.querySelector('.meratus-dp-reference');
        if (dpSelect) dpSelect.setCustomValidity('');
        const cashInput = section.querySelector('.meratus-cash-value');
        if (cashInput) {
            cashInput.value = Math.max(0, Math.round((grandTotal - advance) * 100) / 100);
            section.querySelector('.meratus-cash-display').textContent = meratusMoney(cashInput.value);
        }

        calculateTotalFromAllMeratusSections();
    }

    function calculateTotalFromAllMeratusSections() {
        calculateMeratusDpUsageForAllSections();
        let grandTotalAll = 0;
        document.querySelectorAll('.meratus-section').forEach(section => {
            grandTotalAll += parseFloat((section.querySelector('.meratus-cash-value') || section.querySelector('.grand-total-value-meratus')).value) || 0;
        });

        if (nominalInput) {
            nominalInput.value = grandTotalAll.toLocaleString('id-ID');
        }
    }
