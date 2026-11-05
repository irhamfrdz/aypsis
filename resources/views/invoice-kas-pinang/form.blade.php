@extends('layouts.app')

@php($editing = $invoice->exists)
@section('title', ($editing ? 'Ubah' : 'Tambah').' Invoice Kas Pinang')

@section('content')
<div class="mx-auto max-w-5xl p-6">
    <div class="mb-6 flex items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">{{ $editing ? 'Ubah' : 'Tambah' }} Invoice Kas Pinang</h1>
        <a href="{{ route('invoice-kas-pinang.index') }}" class="text-sm text-pink-700 hover:underline">Kembali</a>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $editing ? route('invoice-kas-pinang.update', $invoice) : route('invoice-kas-pinang.store') }}" class="rounded border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @if($editing) @method('PUT') @endif
        @if($editing)
            <p class="mb-5 text-sm text-gray-600">Nomor invoice: <strong>{{ $invoice->nomor_invoice }}</strong></p>
        @endif
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="tanggal_transaksi" class="mb-1 block text-sm font-medium">Tanggal Invoice <span class="text-red-600">*</span></label>
                <input id="tanggal_transaksi" name="tanggal_transaksi" type="date" required value="{{ old('tanggal_transaksi', $invoice->tanggal_transaksi?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="w-full rounded border-gray-300 text-sm">
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-medium">Status <span class="text-red-600">*</span></label>
                <select id="status" name="status" required class="w-full rounded border-gray-300 text-sm">
                    <option value="draft" @selected(old('status', $invoice->status ?? 'draft') === 'draft')>Draft</option>
                    <option value="terbit" @selected(old('status', $invoice->status) === 'terbit')>Terbit dan catat ke COA</option>
                </select>
            </div>
            <div>
                <label for="arus_kas" class="mb-1 block text-sm font-medium">Arus Kas <span class="text-red-600">*</span></label>
                <select id="arus_kas" name="arus_kas" required class="w-full rounded border-gray-300 text-sm">
                    <option value="keluar" @selected(old('arus_kas', $invoice->arus_kas ?? 'keluar') === 'keluar')>Kas Keluar</option>
                    <option value="masuk" @selected(old('arus_kas', $invoice->arus_kas) === 'masuk')>Kas Masuk</option>
                </select>
            </div>
            <div>
                <label for="akun_kas_id" class="mb-1 block text-sm font-medium">Akun Kas/Bank <span class="text-red-600">*</span></label>
                <select id="akun_kas_id" name="akun_kas_id" required class="w-full rounded border-gray-300 text-sm">
                    <option value="">Pilih akun kas</option>
                    @foreach($akunKas as $akun)
                        <option value="{{ $akun->id }}" @selected((string) old('akun_kas_id', $invoice->akun_kas_id) === (string) $akun->id)>{{ $akun->nomor_akun }} - {{ $akun->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="akun_lawan_id" class="mb-1 block text-sm font-medium">Akun Lawan <span class="text-red-600">*</span></label>
                <select id="akun_lawan_id" name="akun_lawan_id" required class="w-full rounded border-gray-300 text-sm">
                    <option value="">Pilih akun lawan</option>
                    @foreach($akunLawan as $akun)
                        <option value="{{ $akun->id }}" @selected((string) old('akun_lawan_id', $invoice->akun_lawan_id) === (string) $akun->id)>{{ $akun->nomor_akun }} - {{ $akun->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="pihak_terkait" class="mb-1 block text-sm font-medium">Pihak Terkait</label>
                <input id="pihak_terkait" name="pihak_terkait" value="{{ old('pihak_terkait', $invoice->pihak_terkait) }}" class="w-full rounded border-gray-300 text-sm">
            </div>
            <div>
                <label for="referensi" class="mb-1 block text-sm font-medium">Referensi</label>
                <input id="referensi" name="referensi" value="{{ old('referensi', $invoice->referensi) }}" class="w-full rounded border-gray-300 text-sm">
            </div>
        </div>
        <p class="mt-3 text-xs text-gray-500">Status Draft tidak membuat jurnal COA. Saat Terbit, kas masuk mendebit akun kas; kas keluar mengkredit akun kas.</p>

        <div class="mt-7 flex items-center justify-between gap-2">
            <h2 class="text-lg font-semibold">Rincian Invoice</h2>
            <button id="add-item" type="button" class="rounded border border-pink-600 px-3 py-2 text-sm text-pink-700 hover:bg-pink-50">Tambah Rincian</button>
        </div>
        @php
            $rows = old('items', $editing ? $invoice->items->map(fn ($item) => $item->only(['uraian', 'jumlah', 'satuan', 'harga_satuan']))->toArray() : []);
            if (empty($rows)) $rows = [['uraian' => '', 'jumlah' => 1, 'satuan' => '', 'harga_satuan' => '']];
        @endphp
        <div id="items" class="mt-3 space-y-3">
            @foreach($rows as $index => $row)
                <div class="item-row grid gap-3 rounded border bg-gray-50 p-3 md:grid-cols-12">
                    <div class="md:col-span-5"><label class="block text-xs font-medium">Uraian *</label><input name="items[{{ $index }}][uraian]" value="{{ $row['uraian'] ?? '' }}" required maxlength="255" class="w-full rounded border-gray-300 text-sm"></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium">Jumlah *</label><input name="items[{{ $index }}][jumlah]" value="{{ $row['jumlah'] ?? 1 }}" type="number" min="0.01" step="0.01" required class="w-full rounded border-gray-300 text-sm"></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium">Satuan</label><input name="items[{{ $index }}][satuan]" value="{{ $row['satuan'] ?? '' }}" maxlength="50" class="w-full rounded border-gray-300 text-sm"></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium">Harga Satuan *</label><input name="items[{{ $index }}][harga_satuan]" value="{{ $row['harga_satuan'] ?? '' }}" type="number" min="0" step="0.01" required class="w-full rounded border-gray-300 text-sm"></div>
                    <div class="flex items-end md:col-span-1"><button type="button" class="remove-item rounded px-2 py-2 text-sm text-red-700 hover:bg-red-50" aria-label="Hapus rincian">Hapus</button></div>
                </div>
            @endforeach
        </div>
        <div class="mt-3 text-right text-sm font-semibold">Perkiraan total: <span id="estimated-total">Rp 0,00</span></div>
        <div class="mt-5">
            <label for="catatan" class="mb-1 block text-sm font-medium">Catatan</label>
            <textarea id="catatan" name="catatan" rows="3" class="w-full rounded border-gray-300 text-sm">{{ old('catatan', $invoice->catatan) }}</textarea>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('invoice-kas-pinang.index') }}" class="rounded border px-4 py-2 text-sm">Batal</a>
            <button class="rounded bg-pink-600 px-4 py-2 text-sm font-medium text-white hover:bg-pink-700">Simpan Invoice</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const items = document.getElementById('items');
    const total = document.getElementById('estimated-total');
    let nextIndex = {{ count($rows) ? max(array_keys($rows)) + 1 : 0 }};
    const updateTotal = () => {
        let amount = 0;
        items.querySelectorAll('.item-row').forEach(row => {
            const quantity = Number(row.querySelector('[name$="[jumlah]"]').value) || 0;
            const price = Number(row.querySelector('[name$="[harga_satuan]"]').value) || 0;
            amount += Math.round(quantity * price * 100) / 100;
        });
        total.textContent = new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR'}).format(amount);
    };
    document.getElementById('add-item').addEventListener('click', () => {
        if (items.querySelectorAll('.item-row').length >= 50) return;
        const row = items.querySelector('.item-row').cloneNode(true);
        row.querySelectorAll('input').forEach(input => {
            input.name = input.name.replace(/items\[\d+\]/, `items[${nextIndex}]`);
            input.value = input.name.endsWith('[jumlah]') ? '1' : '';
        });
        nextIndex++;
        items.appendChild(row);
        updateTotal();
    });
    items.addEventListener('click', event => {
        if (!event.target.classList.contains('remove-item')) return;
        if (items.querySelectorAll('.item-row').length <= 1) return;
        event.target.closest('.item-row').remove();
        updateTotal();
    });
    items.addEventListener('input', updateTotal);
    updateTotal();
});
</script>
@endsection
