@extends('layouts.app')
@section('title', 'Pisah Pembayaran Pranota BPJS')
@section('page_title', 'Pisah Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-10">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="text-xs font-semibold uppercase tracking-widest text-indigo-600">Pembayaran BPJS</p><h1 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">Pembayaran Pranota BPJS</h1><p class="mt-1 text-sm text-gray-500">Pilih group dan karyawan, lalu lengkapi informasi pembayaran.</p></div>
        <a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Kembali</a>
    </div>
    @include('pembayaran-pranota-bpjs.partials.messages')
    <form method="POST" action="{{ route('pembayaran-pranota-bpjs.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="select_employees" value="1">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-sm">
            <div class="flex items-start gap-3 mb-5"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-sm font-bold text-indigo-700">1</span><div><h2 class="font-semibold text-gray-900">Pilih Group BPJS</h2><p class="text-sm text-gray-500 mt-1">Pranota <span class="font-medium text-gray-700">{{ $pranota->nomor_pranota }}</span> · Group dengan tagihan yang belum dibayar.</p></div></div>
            <label for="payment-group" class="block text-sm font-medium mb-2">Group BPJS *</label>
            <select id="payment-group" name="bpjs_group" required class="w-full max-w-xl rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Pilih Group BPJS</option>
                @foreach(collect($groups)->unique('selection_key') as $group)
                    <option value="{{ $group['selection_key'] }}">{{ $group['group'] }} — {{ $group['komponen'] === 'jkn' ? 'JKN' : 'BP Jamsostek' }}</option>
                @endforeach
            </select>
            @if(empty($groups))<p class="mt-3 text-sm text-gray-500">Tidak ada tagihan yang dapat dibayar dari pranota ini.</p>@endif
        </div>
        <div id="payment-details" hidden class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-start gap-3 p-5 border-b border-gray-200"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-sm font-bold text-indigo-700">2</span><div><h2 class="font-semibold text-gray-900">Rincian Tagihan Karyawan</h2><p class="text-sm text-gray-500 mt-1">Pilih tipe, lalu centang karyawan yang akan dibayar. Total pembayaran hanya menghitung karyawan yang dipilih.</p></div></div>
            <input type="hidden" name="pranota_ids[]" value="{{ $pranota->id }}">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="p-4">Bayar</th><th class="p-4">Tipe</th><th class="p-4 text-right">Tunjangan</th><th class="p-4 text-right">Biaya</th><th class="p-4 text-right">Hutang</th><th class="p-4 text-right">Sisa Tagihan</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($groups as $group)
                            <tr data-payment-group="{{ $group['selection_key'] }}" hidden>
                                <td class="p-4"><input type="checkbox" name="group_keys[]" value="{{ $group['key'] }}" data-selection-key="{{ $group['selection_key'] }}" data-amount="{{ $group['amount'] }}" data-credit-id="{{ $group['credit_id'] }}" data-credit-label="{{ $group['credit_label'] }}" aria-label="Bayar {{ $group['group'] }} {{ $group['tipe'] }} {{ $group['cabang_bpjs'] }}" @checked(in_array($group['key'], old('group_keys', [])))></td>
                                <td class="p-4 font-medium">{{ $group['tipe'] }}</td><td colspan="3" class="p-4 text-right text-gray-500">Rincian tampil per karyawan</td><td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($group['amount'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 p-5 border-t border-gray-200 bg-slate-50">
                <div><label for="employee-branch-filter" class="block text-sm font-medium mb-2">Filter Cabang BPJS</label><select id="employee-branch-filter" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><option value="">Semua Cabang BPJS</option></select></div>
                <div><label for="employee-name-search" class="block text-sm font-medium mb-2">Cari Nama Karyawan</label><input id="employee-name-search" type="search" placeholder="Ketik nama karyawan..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></div>
                <p id="employee-filter-summary" class="text-xs text-gray-500 sm:col-span-2" aria-live="polite"></p>
            </div>
            <div class="overflow-x-auto border-t">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="p-4"><input id="select-all-employees" type="checkbox" aria-label="Pilih semua karyawan yang ditampilkan"></th><th class="p-4">NIK</th><th class="p-4">Nama Karyawan</th><th class="p-4">Tipe</th><th class="p-4">Cabang BPJS</th><th class="p-4 text-right">Nominal Dibayar</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($groups as $group)
                            @foreach($group['employees'] as $employee)
                                <tr data-payment-employee="{{ $group['key'] }}" data-employee-name="{{ $employee['nama'] }}" data-employee-branch="{{ $employee['cabang_bpjs'] }}" hidden>
                                    <td class="p-4"><input type="checkbox" name="employee_keys[]" value="{{ $employee['key'] }}" data-group-key="{{ $group['key'] }}" data-amount="{{ $employee['amount'] }}" data-tunjangan="{{ $employee['tunjangan'] }}" data-biaya="{{ $employee['biaya'] }}" data-hutang="{{ $employee['hutang'] }}" data-credit-id="{{ $group['credit_id'] }}" data-credit-label="{{ $group['credit_label'] }}" aria-label="Bayar BPJS {{ $employee['nama'] }} NIK {{ $employee['nik'] }}" @checked(in_array($employee['key'], old('employee_keys', [])))></td>
                                    <td class="p-4 whitespace-nowrap">{{ $employee['nik'] }}</td><td class="p-4 font-medium">{{ $employee['nama'] }}</td><td class="p-4">{{ $group['tipe'] }}</td><td class="p-4">{{ $employee['cabang_bpjs'] }}</td><td class="p-4 text-right whitespace-nowrap">Rp {{ number_format($employee['amount'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p id="payment-empty-selection" hidden class="p-4 text-sm text-gray-500">Centang minimal satu tipe untuk menampilkan tagihan karyawan.</p>
            <p id="employee-filter-empty" hidden class="p-4 text-sm text-gray-500" role="status">Tidak ada karyawan yang sesuai dengan pencarian dan filter cabang.</p>
            <div class="flex flex-wrap items-center justify-end gap-x-4 gap-y-1 border-t border-emerald-100 bg-emerald-50 px-5 py-4"><span class="text-sm font-medium text-emerald-800">Total pembayaran terpilih</span><output id="payment-total" aria-live="polite" class="text-lg font-bold text-emerald-800">Rp 0,00</output></div>
            <div class="border-t bg-white p-5"><h3 class="mb-3 font-semibold text-gray-900">Rincian Jurnal Terpilih</h3><div class="overflow-x-auto"><table class="w-full text-sm text-left"><thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="p-3">Variabel</th><th class="p-3 text-right">Nominal</th></tr></thead><tbody class="divide-y"><tr><td class="p-3">TUNJANGAN</td><td id="selected-tunjangan" class="p-3 text-right">Rp 0,00</td></tr><tr><td class="p-3">BIAYA</td><td id="selected-biaya" class="p-3 text-right">Rp 0,00</td></tr><tr><td class="p-3">HUTANG</td><td id="selected-hutang" class="p-3 text-right">Rp 0,00</td></tr></tbody><tfoot class="bg-emerald-50 font-bold"><tr><td class="p-3">Grand Total</td><td id="selected-journal-grand-total" class="p-3 text-right">Rp 0,00</td></tr></tfoot></table></div></div>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 grid md:grid-cols-2 gap-5 shadow-sm">
            <div class="flex items-start gap-3 md:col-span-2"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-sm font-bold text-indigo-700">3</span><div><h2 class="font-semibold text-gray-900">Informasi Pembayaran</h2><p class="mt-1 text-sm text-gray-500">Lengkapi tanggal dan rekening pembayaran.</p></div></div>
            <div><label for="tanggal_pembayaran" class="block text-sm font-medium mb-2">Tanggal Pembayaran *</label><input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300"><p class="mt-1 text-xs text-gray-500">Dapat memilih tanggal mundur, termasuk sebelum tanggal pranota.</p></div>
            <div><label for="nomor_accurate" class="block text-sm font-medium mb-2">Nomor Accurate</label><input id="nomor_accurate" name="nomor_accurate" value="{{ old('nomor_accurate') }}" maxlength="255" class="w-full rounded-lg border-gray-300"></div>
            <div><h3 id="bank-accounts-label" class="block text-sm font-medium mb-2">Akun Bank *</h3><div id="bank_coa_id" aria-labelledby="bank-accounts-label" aria-describedby="credit-account-message" class="space-y-2" aria-live="polite"></div><p id="credit-account-message" class="text-sm text-gray-500 mt-2" aria-live="polite">Pilih tipe JKN atau Jamsostek untuk menampilkan akun jurnal.</p></div>
            <div><label for="bank-search" class="block text-sm font-medium mb-2">Bank yang Digunakan *</label><input id="bank-search" type="search" autocomplete="off" placeholder="Cari nomor atau nama bank..." class="mb-2 w-full rounded-lg border-gray-300" aria-controls="akun_coa_id"><select id="akun_coa_id" name="akun_coa_id" required class="w-full rounded-lg border-gray-300"><option value="">Pilih bank yang digunakan</option>@foreach($accounts as $account)<option value="{{ $account->id }}" data-search="{{ mb_strtolower($account->nomor_akun.' '.$account->nama_akun) }}" @selected(old('akun_coa_id') == $account->id)>{{ $account->nomor_akun }} - {{ $account->nama_akun }}</option>@endforeach</select><p id="bank-search-empty" class="mt-1 hidden text-sm text-gray-500">Bank tidak ditemukan.</p></div>
            <div class="md:col-span-2"><label for="keterangan" class="block text-sm font-medium mb-2">Keterangan</label><textarea id="keterangan" name="keterangan" maxlength="5000" rows="2" class="w-full rounded-lg border-gray-300">{{ old('keterangan') }}</textarea></div>
        </div>
        <div class="flex flex-wrap justify-end gap-3"><a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Kembali</a><button id="save-payment" type="submit" disabled class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">Simpan Pembayaran</button></div>
        </div>
    </form>
</div>
<script src="{{ asset('js/pembayaran-pranota-bpjs.js') }}?v={{ filemtime(public_path('js/pembayaran-pranota-bpjs.js')) }}" defer></script>
@endsection
