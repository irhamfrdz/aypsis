<div id="tab-pembayaran" style="display:none">
    <div class="card">
        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:20px;">
            <h4 style="margin:0;">7. Pembayaran Pranota</h4>
            <div style="display:flex; gap:10px; margin-left:auto;">
                <input type="text" id="src-pay-pranota" placeholder="Cari Pranota..." oninput="renderPayPranota()" style="width:250px;">
            </div>
        </div>

        <div style="max-height: 400px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px;">
            <table style="margin-top:0;">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" id="check-all-pranota" onclick="toggleAllPranota(this)"></th>
                        <th>Nomor Pranota</th>
                        <th>Vendor</th>
                        <th>No Invoice</th>
                        <th>Tanggal Inv</th>
                        <th align="right">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="body-pay-pranota"></tbody>
            </table>
        </div>
    </div>

    <div id="payment-form-zone" style="display:none;">
        <div class="card" style="border-top: 4px solid var(--primary);">
            <h4>Detail Pembayaran</h4>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Nomor Pembayaran</label>
                        <div style="display:flex; gap:5px;">
                            <input type="text" id="pay-nomor" readonly style="flex:1; background:#f1f5f9;">
                            <button class="btn btn-blue" onclick="getNewPayNomor()">🔄</button>
                        </div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Tanggal Pembayaran</label>
                        <input type="date" id="pay-tanggal" value="{{ date('Y-m-d') }}" style="width:100%;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Bank / Kas</label>
                        <select id="pay-bank" style="width:100%;">
                            <option value="">-- Pilih Bank --</option>
                            @foreach($akunCoa as $coa)
                                <option value="{{ $coa->nama_akun }}">{{ $coa->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Jenis Transaksi</label>
                        <select id="pay-jenis" style="width:100%;">
                            <option value="Kredit" selected>Kredit (Uang Keluar)</option>
                            <option value="Debit">Debit (Uang Masuk / Pembatalan)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Nomor Accurate</label>
                        <input type="text" id="pay-accurate" placeholder="Contoh: JM.2024.001" style="width:100%;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Keterangan</label>
                        <textarea id="pay-ket" rows="3" style="width:100%; resize:none;"></textarea>
                    </div>
                    <div style="padding:15px; background: #f8fafc; border-radius:8px; border: 1px dashed var(--border-color);">
                        <div style="display:flex; justify-content:between; margin-bottom:8px;">
                            <span>Total Terpilih:</span>
                            <span id="sum-selected" style="font-weight:700;">Rp 0</span>
                        </div>
                        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:8px;">
                            <span>Penyesuaian:</span>
                            <input type="text" id="pay-adj" value="0" oninput="inputRibuan(this);calcGrandTotal()" style="width:120px; text-align:right; padding:4px 8px;">
                        </div>
                        <div id="adj-note-zone" style="display:none; margin-bottom:8px;">
                            <input type="text" id="pay-adj-note" placeholder="Alasan penyesuaian..." style="width:100%; font-size:11px;">
                        </div>
                        <hr style="border:0; border-top:1px solid #ddd; margin:10px 0;">
                        <div style="display:flex; justify-content:between; font-size:1.1rem; font-weight:800; color:var(--primary);">
                            <span>GRAND TOTAL:</span>
                            <span id="sum-grand">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
                <button class="btn btn-red" onclick="resetPayForm()">Batal</button>
                <button class="btn btn-green" style="padding: 10px 30px;" onclick="submitFinalPayment()">SIMPAN PEMBAYARAN</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPranotaIds = [];

function renderPayPranota() {
    const s = (document.getElementById('src-pay-pranota')?.value || "").toUpperCase();
    const body = document.getElementById('body-pay-pranota');
    if(!body) return;

    // Filter pranota yang statusnya PENDING (belum bayar)
    const pendingP = db.p.filter(x => x.status === 'PENDING' && x.nomor.includes(s));
    
    body.innerHTML = pendingP.map(x => `
        <tr style="${selectedPranotaIds.includes(x.id) ? 'background:#eff6ff' : ''}">
            <td align="center"><input type="checkbox" class="cb-pranota" value="${x.id}" ${selectedPranotaIds.includes(x.id) ? 'checked' : ''} onchange="toggleSelectPranota(${x.id}, this)"></td>
            <td><b>${x.nomor}</b></td>
            <td>${x.vendor}</td>
            <td>${x.no_inv||'-'}</td>
            <td>${x.tgl_inv||'-'}</td>
            <td align="right">${fmtRibuan(x.total)}</td>
            <td><span class="st-sewa">PENDING</span></td>
        </tr>`).join('');
    
    if(pendingP.length === 0) body.innerHTML = '<tr><td colspan="7" align="center" style="padding:20px; color:#64748b;">Tidak ada pranota pending.</td></tr>';
}

function toggleSelectPranota(id, cb) {
    if(cb.checked) {
        if(!selectedPranotaIds.includes(id)) selectedPranotaIds.push(id);
    } else {
        selectedPranotaIds = selectedPranotaIds.filter(i => i !== id);
    }
    
    if(selectedPranotaIds.length > 0) {
        document.getElementById('payment-form-zone').style.display = 'block';
        if(!document.getElementById('pay-nomor').value) getNewPayNomor();
        calcGrandTotal();
    } else {
        document.getElementById('payment-form-zone').style.display = 'none';
    }
    renderPayPranota();
}

function toggleAllPranota(cb) {
    const cbs = document.querySelectorAll('.cb-pranota');
    cbs.forEach(c => {
        c.checked = cb.checked;
        toggleSelectPranota(parseInt(c.value), c);
    });
}

function calcGrandTotal() {
    let sum = 0;
    selectedPranotaIds.forEach(id => {
        const p = db.p.find(item => item.id === id);
        if(p) sum += p.total;
    });

    const adj = cleanNum(document.getElementById('pay-adj').value);
    document.getElementById('adj-note-zone').style.display = adj !== 0 ? 'block' : 'none';
    
    const grand = sum + adj;
    document.getElementById('sum-selected').innerText = 'Rp ' + fmtRibuan(sum);
    document.getElementById('sum-grand').innerText = 'Rp ' + fmtRibuan(grand);
}

function getNewPayNomor() {
    fetch('{{ route('kontainer-sewa-final.generate-payment-number') }}')
    .then(r => r.json())
    .then(d => { if(d.success) document.getElementById('pay-nomor').value = d.nomor; });
}

function resetPayForm() {
    selectedPranotaIds = [];
    document.getElementById('check-all-pranota').checked = false;
    document.getElementById('payment-form-zone').style.display = 'none';
    document.getElementById('pay-adj').value = '0';
    document.getElementById('pay-adj-note').value = '';
    document.getElementById('pay-ket').value = '';
    document.getElementById('pay-accurate').value = '';
    renderPayPranota();
}

function submitFinalPayment() {
    const data = {
        pranota_ids: selectedPranotaIds,
        nomor_pembayaran: document.getElementById('pay-nomor').value,
        nomor_accurate: document.getElementById('pay-accurate').value,
        tanggal_pembayaran: document.getElementById('pay-tanggal').value,
        bank: document.getElementById('pay-bank').value,
        jenis_transaksi: document.getElementById('pay-jenis').value,
        total_pembayaran: selectedPranotaIds.reduce((acc, id) => acc + (db.p.find(p => p.id === id)?.total || 0), 0),
        total_penyesuaian: cleanNum(document.getElementById('pay-adj').value),
        grand_total: cleanNum(document.getElementById('sum-grand').innerText),
        alasan_penyesuaian: document.getElementById('pay-adj-note').value,
        keterangan: document.getElementById('pay-ket').value
    };

    if(!data.bank) return alert('Pilih Bank / Kas terlebih dahulu!');
    if(data.total_penyesuaian !== 0 && !data.alasan_penyesuaian) return alert('Alasan penyesuaian wajib diisi!');
    if(!confirm('Konfirmasi simpan pembayaran senilai Rp ' + fmtRibuan(data.grand_total) + '?')) return;

    fetch('{{ route('kontainer-sewa-final.submit-payment') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            alert(d.message);
            location.reload();
        } else {
            alert(d.message);
        }
    })
    .catch(e => { console.error(e); alert('Error sistem!'); });
}
</script>
