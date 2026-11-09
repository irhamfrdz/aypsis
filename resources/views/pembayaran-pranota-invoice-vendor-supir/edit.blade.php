@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Invoice Vendor Supir')
@section('content')
<div class="mx-auto max-w-6xl rounded-lg bg-white p-4 shadow-lg">
    <div class="mb-4 flex items-center justify-between">
        <div><h1 class="text-xl font-bold text-gray-800">Edit Pembayaran Pranota Vendor Supir</h1><p class="text-sm text-gray-500">{{ $pembayaran->nomor_pembayaran }} · {{ $pembayaran->vendor->nama_vendor ?? '-' }}</p></div>
        <a href="{{ route('pembayaran-pranota-invoice-vendor-supir.index') }}" class="rounded border px-3 py-2 text-sm">Kembali</a>
    </div>
    @if(session('error'))<div class="mb-3 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="mb-3 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
    <form id="editPaymentForm" method="POST" action="{{ route('pembayaran-pranota-invoice-vendor-supir.update', $pembayaran->id) }}" class="space-y-4">
        @csrf @method('PUT')
        <section class="grid gap-3 rounded border bg-gray-50 p-4 md:grid-cols-3">
            <label class="text-sm">Nomor pembayaran<input value="{{ $pembayaran->nomor_pembayaran }}" disabled class="mt-1 w-full rounded border-gray-300 bg-gray-100"></label>
            <label class="text-sm">Tanggal kas<input type="date" name="tanggal_pembayaran" required value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran?->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300"></label>
            <label class="text-sm">No. Accurate<input name="nomor_accurate" value="{{ old('nomor_accurate', $pembayaran->nomor_accurate) }}" class="mt-1 w-full rounded border-gray-300"></label>
            <label class="text-sm">Bank<select name="bank" required class="mt-1 w-full rounded border-gray-300">@foreach($akunCoa as $akun)<option value="{{ $akun->nama_akun }}" @selected(old('bank', $pembayaran->bank) === $akun->nama_akun)>{{ $akun->nama_akun }}</option>@endforeach</select></label>
            <label class="text-sm">Jenis transaksi<select name="metode_pembayaran" required class="mt-1 w-full rounded border-gray-300"><option value="debit" @selected(old('metode_pembayaran', $pembayaran->metode_pembayaran)==='debit')>Debit</option><option value="kredit" @selected(old('metode_pembayaran', $pembayaran->metode_pembayaran)==='kredit')>Kredit</option></select></label>
        </section>
        <section class="rounded border p-4">
            <div class="mb-3 flex items-center justify-between"><div><h2 class="font-semibold">Rincian akun biaya COA</h2><p class="text-xs text-gray-500">Nominal seluruh akun harus sama dengan total pembayaran.</p></div><button id="addExpenseAccount" type="button" class="rounded bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700">+ Tambah akun</button></div>
            <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-2 text-left">Akun COA</th><th class="p-2 text-right">Nominal</th><th class="p-2 text-left">Memo</th><th class="p-2"></th></tr></thead><tbody id="expenseAccountRows">
                @forelse($expenseItems as $index => $expenseItem)
                    <tr class="expense-account-row border-t"><td class="p-2"><select name="akun_biaya[{{ $index }}][akun_coa_id]" required class="w-full rounded border-gray-300"><option value="">Pilih akun</option>@foreach($akunBiaya as $akun)<option value="{{ $akun->id }}" @selected((string) old("akun_biaya.$index.akun_coa_id", $expenseItem->akun_coa_id) === (string) $akun->id)>{{ $akun->nomor_akun }} · {{ $akun->nama_akun }}</option>@endforeach</select></td><td class="p-2"><input type="number" min="0.01" step="0.01" name="akun_biaya[{{ $index }}][nominal]" value="{{ old("akun_biaya.$index.nominal", $expenseItem->nominal) }}" required class="expense-account-amount w-40 rounded border-gray-300 text-right"></td><td class="p-2"><input name="akun_biaya[{{ $index }}][memo]" value="{{ old("akun_biaya.$index.memo", $expenseItem->memo) }}" maxlength="500" class="w-full rounded border-gray-300"></td><td class="p-2"><button type="button" class="remove-expense-account rounded px-2 py-1 text-red-600 hover:bg-red-50">Hapus</button></td></tr>
                @empty
                    <tr class="expense-account-row border-t"><td class="p-2"><select name="akun_biaya[0][akun_coa_id]" required class="w-full rounded border-gray-300"><option value="">Pilih akun</option>@foreach($akunBiaya as $akun)<option value="{{ $akun->id }}">{{ $akun->nomor_akun }} · {{ $akun->nama_akun }}</option>@endforeach</select></td><td class="p-2"><input type="number" min="0.01" step="0.01" name="akun_biaya[0][nominal]" value="{{ $pembayaran->total_tagihan_setelah_penyesuaian }}" required class="expense-account-amount w-40 rounded border-gray-300 text-right"></td><td class="p-2"><input name="akun_biaya[0][memo]" maxlength="500" class="w-full rounded border-gray-300"></td><td class="p-2"><button type="button" class="remove-expense-account rounded px-2 py-1 text-red-600 hover:bg-red-50">Hapus</button></td></tr>
                @endforelse
            </tbody></table></div>
            <p class="mt-2 text-right text-xs text-gray-600">Total akun biaya: <strong id="expenseTotal">Rp 0</strong></p>
            <template id="expenseAccountTemplate"><tr class="expense-account-row border-t"><td class="p-2"><select name="akun_biaya[__INDEX__][akun_coa_id]" required class="w-full rounded border-gray-300"><option value="">Pilih akun</option>@foreach($akunBiaya as $akun)<option value="{{ $akun->id }}">{{ $akun->nomor_akun }} · {{ $akun->nama_akun }}</option>@endforeach</select></td><td class="p-2"><input type="number" min="0.01" step="0.01" name="akun_biaya[__INDEX__][nominal]" required class="expense-account-amount w-40 rounded border-gray-300 text-right"></td><td class="p-2"><input name="akun_biaya[__INDEX__][memo]" maxlength="500" class="w-full rounded border-gray-300"></td><td class="p-2"><button type="button" class="remove-expense-account rounded px-2 py-1 text-red-600">Hapus</button></td></tr></template>
        </section>
        <section class="overflow-hidden rounded border">
            <div class="bg-gray-50 p-3"><h2 class="font-semibold">Pranota pembayaran</h2><p class="text-xs text-gray-500">Pilih pranota dan sesuaikan nominal bayar.</p></div>
            <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-3 text-left">Pilih</th><th class="p-3 text-left">No. Pranota</th><th class="p-3 text-left">Tanggal</th><th class="p-3 text-right">Total tagihan</th><th class="p-3 text-right">Nominal bayar</th></tr></thead><tbody>
            @foreach($pranotas as $pranota)
                @php($item = $selectedItems->get($pranota->id))
                <tr class="border-t"><td class="p-3"><input class="pranota-check" type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" @checked($item)></td><td class="p-3">{{ $pranota->no_pranota }}</td><td class="p-3">{{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') : '-' }}</td><td class="p-3 text-right">Rp {{ number_format($pranota->grand_total ?? $pranota->total_nominal,0,',','.') }}</td><td class="p-3"><input class="pranota-amount w-36 rounded border-gray-300 text-right" type="number" min="0.01" step="0.01" name="nominal_bayar[{{ $pranota->id }}]" value="{{ old('nominal_bayar.'.$pranota->id, $item->nominal ?? ($pranota->grand_total ?? $pranota->total_nominal)) }}"></td></tr>
            @endforeach
            </tbody></table></div>
        </section>
        <section class="grid gap-3 rounded border bg-gray-50 p-4 md:grid-cols-3">
            <label class="text-sm">Penyesuaian (+/-)<input id="adjustment" type="number" step="0.01" name="total_tagihan_penyesuaian" value="{{ old('total_tagihan_penyesuaian', $pembayaran->total_tagihan_penyesuaian) }}" class="mt-1 w-full rounded border-gray-300"></label>
            <label class="text-sm">Alasan penyesuaian<input name="alasan_penyesuaian" value="{{ old('alasan_penyesuaian', $pembayaran->alasan_penyesuaian) }}" class="mt-1 w-full rounded border-gray-300"></label>
            <label class="text-sm">Keterangan<textarea name="keterangan" class="mt-1 w-full rounded border-gray-300">{{ old('keterangan', $pembayaran->keterangan) }}</textarea></label>
            <div class="md:col-span-3 text-right"><span class="text-sm text-gray-600">Total bayar: </span><strong id="totalDisplay" class="text-lg text-indigo-700"></strong></div>
        </section>
        <div class="flex justify-end gap-2"><a href="{{ route('pembayaran-pranota-invoice-vendor-supir.index') }}" class="rounded border px-4 py-2 text-sm">Batal</a><button class="rounded bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Perubahan</button></div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.getElementById('expenseAccountRows');
    document.getElementById('addExpenseAccount').addEventListener('click', () => {
        const html = document.getElementById('expenseAccountTemplate').innerHTML.replaceAll('__INDEX__', Date.now());
        rows.insertAdjacentHTML('beforeend', html);
    });
    rows.addEventListener('click', event => {
        if (event.target.closest('.remove-expense-account')) {
            if (rows.querySelectorAll('.expense-account-row').length > 1) event.target.closest('.expense-account-row').remove();
        }
    });
    const updateTotal = () => {
        let total = 0;
        document.querySelectorAll('.pranota-check:checked').forEach(check => {
            const input = document.querySelector(`[name="nominal_bayar[${check.value}]"]`);
            total += Number(input?.value || 0);
        });
        total += Number(document.getElementById('adjustment').value || 0);
        document.getElementById('totalDisplay').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        const expenseTotal = [...document.querySelectorAll('.expense-account-amount')].reduce((sum, input) => sum + Number(input.value || 0), 0);
        document.getElementById('expenseTotal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(expenseTotal);
    };
    document.addEventListener('input', event => {
        if (event.target.matches('.pranota-check, .pranota-amount, #adjustment, .expense-account-amount')) updateTotal();
    });
    document.querySelectorAll('.pranota-check').forEach(input => input.addEventListener('change', updateTotal));
    updateTotal();
});
</script>
@endsection
