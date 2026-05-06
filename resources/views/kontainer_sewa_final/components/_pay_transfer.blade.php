<div id="tab-pay-transfer" style="display:none">
    <div class="card">
        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:20px;">
            <h4 style="margin:0;">8. Pembayaran Permohonan Transfer</h4>
        </div>

        <div style="max-height: 400px; overflow-y: auto;">
            <table id="tbl-pay-transfer">
                <thead>
                    <tr>
                        <th width="40">No.</th>
                        <th>Nomor Permohonan</th>
                        <th>Vendor</th>
                        <th>Tanggal</th>
                        <th align="right">Total</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody id="body-pay-transfer"></tbody>
            </table>
        </div>
    </div>

    <div id="payment-process-zone" style="display:none; margin-top:20px;">
        <div class="card" style="border-top: 4px solid var(--success); background: #f0fdf4;">
            <h4 style="color: var(--success);">Realisasi Pembayaran</h4>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <div>
                    <input type="hidden" id="process-id">
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Nomor Permohonan</label>
                        <input type="text" id="process-no" readonly style="width:100%; background:#f1f5f9;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Pilih Bank / Kas</label>
                        <select id="process-bank" style="width:100%;">
                            <option value="">-- Pilih Bank --</option>
                            @foreach($akunCoa as $coa)
                                <option value="{{ $coa->nama_akun }}">{{ $coa->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Jenis Transaksi</label>
                        <select id="process-jenis" style="width:100%;">
                            <option value="Kredit" selected>Kredit (Uang Keluar)</option>
                            <option value="Debit">Debit (Uang Masuk / Pembatalan)</option>
                        </select>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Nomor Accurate</label>
                        <input type="text" id="process-accurate" placeholder="Contoh: JM.2024.001" style="width:100%;">
                    </div>
                </div>
            </div>
            <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
                <button class="btn btn-red" onclick="document.getElementById('payment-process-zone').style.display='none'">BATAL</button>
                <button class="btn btn-green" style="padding: 10px 40px; font-size: 14px;" onclick="submitFinalProcess()">✅ PROSES SEKARANG</button>
            </div>
        </div>
    </div>
</div>

<script>
function renderPayTransfer() {
    const body = document.getElementById('body-pay-transfer');
    if(!body) return;

    const pending = db.pending_payments || [];
    
    body.innerHTML = pending.map((x, i) => `
        <tr>
            <td align="center">${i+1}</td>
            <td><b>${x.no}</b></td>
            <td>${x.vendor}</td>
            <td>${x.tgl}</td>
            <td align="right">Rp ${fmtRibuan(x.total)}</td>
            <td><span class="st-sewa">${x.status}</span></td>
            <td>
                <button class="btn btn-blue" style="width:100%;" onclick="bukaProsesBayar(${x.id}, '${x.no}', ${x.total})">Pilih</button>
            </td>
        </tr>`).join('');
    
    if(pending.length === 0) body.innerHTML = '<tr><td colspan="7" align="center" style="padding:40px; color:#64748b;">☕ Tidak ada permohonan transfer yang perlu dibayar.</td></tr>';
}

function bukaProsesBayar(id, nomor, total) {
    document.getElementById('payment-process-zone').style.display = 'block';
    document.getElementById('process-id').value = id;
    document.getElementById('process-no').value = nomor;
    window.scrollTo({ top: document.getElementById('payment-process-zone').offsetTop, behavior: 'smooth' });
}

function submitFinalProcess() {
    const id = document.getElementById('process-id').value;
    const nomor = document.getElementById('process-no').value;
    const bank = document.getElementById('process-bank').value;
    const jenis = document.getElementById('process-jenis').value;
    const accurate = document.getElementById('process-accurate').value;

    if(!bank) return alert('Silakan pilih Bank / Kas terlebih dahulu!');
    
    if(!confirm(`Konfirmasi realisasi pembayaran untuk permohonan ${nomor}?\n\nJurnal Akuntansi akan otomatis terbentuk.`)) return;

    fetch('{{ url("/kontainer-sewa-final/pay-transfer") }}/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            nomor_accurate: accurate,
            bank: bank,
            jenis_transaksi: jenis
        })
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            alert("✅ Pembayaran berhasil diproses!");
            location.reload();
        } else {
            alert("❌ Gagal: " + d.message);
        }
    })
    .catch(e => { console.error(e); alert("🚨 Terjadi kesalahan sistem!"); });
}
</script>
