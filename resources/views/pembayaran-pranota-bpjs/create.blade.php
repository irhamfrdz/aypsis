@extends('layouts.app')
@section('title', 'Buat Pembayaran Pranota BPJS')
@section('page_title', 'Buat Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div><h1 class="text-2xl font-bold text-gray-800">Buat Pembayaran Pranota BPJS</h1><p class="text-sm text-gray-500 mt-1">Pilih pranota untuk dilunasi. Nomor pembayaran SIS dibuat otomatis saat disimpan.</p></div>
    @include('pembayaran-pranota-bpjs.partials.messages')
    <form method="POST" action="{{ route('pembayaran-pranota-bpjs.store') }}" class="space-y-6">
        @csrf
        <div class="bg-white border rounded-xl p-6 grid md:grid-cols-2 gap-5">
            <div><label for="tanggal_pembayaran" class="block text-sm font-medium mb-2">Tanggal Pembayaran *</label><input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300"></div>
            <div><label for="nomor_accurate" class="block text-sm font-medium mb-2">Nomor Accurate</label><input id="nomor_accurate" name="nomor_accurate" value="{{ old('nomor_accurate') }}" maxlength="255" class="w-full rounded-lg border-gray-300"></div>
            <div><label for="bank_coa_id" class="block text-sm font-medium mb-2">Kas/Bank (Kredit) *</label><select id="bank_coa_id" name="bank_coa_id" required class="w-full rounded-lg border-gray-300"><option value="">Pilih kas/bank</option>@foreach($banks as $bank)<option value="{{ $bank->id }}" @selected(old('bank_coa_id') == $bank->id)>{{ $bank->nomor_akun }} - {{ $bank->nama_akun }}</option>@endforeach</select></div>
            <div><label for="akun_coa_id" class="block text-sm font-medium mb-2">Akun BPJS / Hutang BPJS (Debit) *</label><select id="akun_coa_id" name="akun_coa_id" required class="w-full rounded-lg border-gray-300"><option value="">Pilih akun lawan</option>@foreach($accounts as $account)<option value="{{ $account->id }}" @selected(old('akun_coa_id') == $account->id)>{{ $account->nomor_akun }} - {{ $account->nama_akun }}</option>@endforeach</select></div>
            <div class="md:col-span-2"><label for="keterangan" class="block text-sm font-medium mb-2">Keterangan</label><textarea id="keterangan" name="keterangan" maxlength="5000" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan') }}</textarea></div>
        </div>
        <div class="bg-white border rounded-xl overflow-x-auto">
            <div class="p-4 border-b"><h2 class="font-semibold">Pranota Belum Dibayar</h2><p class="text-sm text-gray-500">Pembayaran penuh sesuai grand total pranota. Pranota yang dilunasi akan dikunci.</p></div>
            <table class="w-full text-sm text-left"><thead class="bg-gray-50"><tr><th class="p-4">Pilih</th><th class="p-4">Nomor Pranota</th><th class="p-4">Tanggal</th><th class="p-4">Periode</th><th class="p-4 text-right">Total Tagihan</th></tr></thead><tbody class="divide-y">
                @forelse($pranotas as $pranota)
                <tr><td class="p-4"><input type="checkbox" name="pranota_ids[]" value="{{ $pranota->id }}" data-amount="{{ $pranota->grand_total }}" aria-label="Pilih {{ $pranota->nomor_pranota }}" @checked(in_array($pranota->id, old('pranota_ids', request('pranota_id') ? [request('pranota_id')] : [])))></td><td class="p-4 font-medium">{{ $pranota->nomor_pranota }}</td><td class="p-4">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td><td class="p-4">{{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</td><td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($pranota->grand_total, 2, ',', '.') }}</td></tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-gray-500">Tidak ada pranota BPJS yang dapat dibayar.</td></tr>
                @endforelse
            </tbody></table>
            <div class="p-4 bg-green-50 text-right font-bold">Total Pembayaran: <output id="payment-total" aria-live="polite">Rp 0,00</output></div>
        </div>
        <div class="flex justify-end gap-3"><a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="px-4 py-2 border rounded-lg">Kembali</a><button id="save-payment" type="submit" disabled class="px-4 py-2 bg-green-600 text-white rounded-lg disabled:opacity-50">Simpan Pembayaran</button></div>
    </form>
</div>
<script src="{{ asset('js/pembayaran-pranota-bpjs.js') }}?v={{ filemtime(public_path('js/pembayaran-pranota-bpjs.js')) }}" defer></script>
@endsection
