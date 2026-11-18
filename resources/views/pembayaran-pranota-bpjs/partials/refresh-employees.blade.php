<form method="POST" action="{{ route('pembayaran-pranota-bpjs.refresh-employees', $payment) }}" onsubmit="if (!confirm('Perbarui Group dan Tipe BPJS karyawan pada pembayaran {{ $payment->nomor_pembayaran }} dari master terbaru? Nominal, jurnal, dan saldo pembayaran tetap.')) return false; this.querySelector('button').disabled = true;">
    @csrf
    <button type="submit" class="bpjs-btn inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-indigo-700 disabled:opacity-50"><i class="fa-solid fa-rotate" aria-hidden="true"></i>Perbarui Data Karyawan</button>
</form>
