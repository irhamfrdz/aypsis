@extends('layouts.app')

@section('title', 'Tambah Transaksi Utang Karyawan - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Back Button & Title -->
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('saldo-utang-karyawan.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-sm transition-all duration-200">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Tambah Transaksi Utang</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Catat pinjaman baru atau pelunasan manual untuk karyawan</p>
        </div>
    </div>

    <!-- Alert Error -->
    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl flex items-center">
            <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Card -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm" role="alert">
            <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-8">
        <form action="{{ route('saldo-utang-karyawan.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Employee Selector -->
                <div>
                    <label for="karyawan_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Karyawan</label>
                    <select name="karyawan_id" id="karyawan_id" class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('karyawan_id') border-rose-300 @enderror">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                {{ strtoupper($karyawan->nama_lengkap) }} ({{ $karyawan->nik ?? 'TANPA NIK' }})
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan_id')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="tanggal" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Transaksi</label>
                    <input type="date" 
                           name="tanggal" 
                           id="tanggal" 
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal') border-rose-300 @enderror">
                    @error('tanggal')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="tipe" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipe Transaksi</label>
                    <select name="tipe" id="tipe" class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('tipe') border-rose-300 @enderror">
                        <option value="penambahan" {{ old('tipe') == 'penambahan' ? 'selected' : '' }}>Penambahan (Pinjaman Baru / Kasbon)</option>
                        <option value="pengurangan" {{ old('tipe') == 'pengurangan' ? 'selected' : '' }}>Pengurangan (Pembayaran Manual / Pemotongan)</option>
                    </select>
                    @error('tipe')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nominal -->
                <div>
                    <label for="nominal" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Grand Total (Rupiah)</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-xs font-bold">Rp</span>
                        </div>
                        <input type="text"
                               id="nominal" 
                               value="0,00"
                               readonly aria-describedby="grand-total-help"
                               class="block w-full pl-9 pr-3 py-2.5 text-xs text-gray-900 bg-gray-50 border border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 @error('nominal') border-rose-300 @enderror">
                    </div>
                    <p id="grand-total-help" class="mt-2 text-xs text-gray-400">Dihitung otomatis dari nominal seluruh rincian akun COA.</p>
                    @error('nominal')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="bank_coa_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Akun Bank / Kas</label>
                    <select id="bank_coa_id" name="bank_coa_id" required class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Akun Bank / Kas --</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}" @selected(old('bank_coa_id') == $account->id)>{{ $account->nomor_akun }} — {{ $account->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rincian Akun COA</h2>
                        <button id="add-coa" type="button" class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition-colors duration-200"><i class="fas fa-plus mr-1" aria-hidden="true"></i>Tambah Akun</button>
                    </div>
                    <div id="coa-rows" class="space-y-3">
                        @foreach(old('rincian_coa', [['coa_id' => '', 'memo' => '']]) as $index => $row)
                            @include('saldo-utang-supir.partials.coa-row')
                        @endforeach
                    </div>
                    <template id="coa-row-template">@include('saldo-utang-supir.partials.coa-row', ['index' => '__INDEX__', 'row' => []])</template>
                </div>

                <!-- Reference -->
                <div>
                    <label for="referensi" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Referensi / Judul</label>
                    <input type="text" 
                           name="referensi" 
                           id="referensi" 
                           value="{{ old('referensi') }}"
                           placeholder="Contoh: Pinjaman Baru, Bayar Cash, dll."
                           class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('referensi') border-rose-300 @enderror">
                    @error('referensi')
                        <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Note -->
                <div>
                    <label for="keterangan" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan Tambahan (Opsional)</label>
                    <textarea name="keterangan" 
                              id="keterangan" 
                              rows="3" 
                              placeholder="Catatan pelengkap transaksi..."
                              class="block w-full text-xs bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-2">
                <a href="{{ route('saldo-utang-karyawan.index') }}" class="px-5 py-3 border border-gray-200 text-gray-700 font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 transition-all duration-200 text-xs uppercase tracking-wider">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.getElementById('coa-rows');
    const add = document.getElementById('add-coa');
    let nextIndex = Math.max(-1, ...Array.from(rows.querySelectorAll('select'), select => Number(select.name.match(/\[(\d+)\]/)[1]))) + 1;
    function updateTotal() {
        const totalCents = Array.from(rows.querySelectorAll('.coa-amount')).reduce((total, input) => {
            const amount = Number(input.value);
            return total + (Number.isFinite(amount) && amount > 0 ? Math.round(amount * 100) : 0);
        }, 0);
        document.getElementById('nominal').value = (totalCents / 100).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    rows.addEventListener('input', updateTotal);
    function updateButtons() {
        const buttons = rows.querySelectorAll('.remove-coa');
        buttons.forEach(button => {
            button.disabled = buttons.length <= 1;
            button.classList.toggle('opacity-40', button.disabled);
        });
        add.disabled = buttons.length >= 100;
        updateTotal();
    }
    add.addEventListener('click', () => {
        if (rows.children.length >= 100) return;
        const html = document.getElementById('coa-row-template').innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        rows.insertAdjacentHTML('beforeend', html);
        updateButtons();
        rows.lastElementChild.querySelector('select').focus();
    });
    rows.addEventListener('click', event => {
        const button = event.target.closest('.remove-coa');
        if (!button || rows.children.length <= 1) return;
        button.closest('.coa-row').remove();
        updateButtons();
    });
    updateButtons();
});
</script>
@endsection
