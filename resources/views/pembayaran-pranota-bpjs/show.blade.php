@extends('layouts.app')
@section('title', 'Detail Pembayaran Pranota BPJS')
@section('page_title', 'Detail Pembayaran Pranota BPJS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4"><h1 class="text-2xl font-bold text-gray-800">{{ $payment->nomor_pembayaran }}</h1><div class="flex flex-wrap items-center gap-2">
        @can('pembayaran-pranota-bpjs-create')
            <form method="POST" action="{{ route('pembayaran-pranota-bpjs.sync-coa', $payment) }}" onsubmit="this.querySelector('button').disabled = true;">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"><i class="fas fa-rotate" aria-hidden="true"></i>SYNC COA</button>
            </form>
        @endcan
        <a href="{{ route('pembayaran-pranota-bpjs.index') }}" class="px-4 py-2 border rounded-lg">Kembali</a></div></div>
    @include('pembayaran-pranota-bpjs.partials.messages')
    <div class="bg-white border rounded-xl p-6 grid md:grid-cols-2 gap-5 text-sm">
        <div><p class="text-gray-500">Tanggal Pembayaran</p><p class="font-semibold">{{ $payment->tanggal_pembayaran->format('d/m/Y') }}</p></div>
        <div><p class="text-gray-500">Status</p><p class="text-green-700 font-semibold">Lunas</p></div>
        <div><p class="text-gray-500">Akun Bank</p>
            @forelse($payment->bank_allocations ?? [] as $allocation)
                <div class="mt-2"><p>{{ $allocation['nomor_akun'] }} - {{ $allocation['nama_akun'] }}</p><p class="font-semibold">Rp {{ number_format((float) $allocation['subtotal'], 2, ',', '.') }}</p></div>
            @empty
                <p>{{ $payment->bank?->nama_akun ?? '-' }}</p>
            @endforelse
        </div>
        <div id="edit-bank" class="scroll-mt-6"><p class="text-gray-500">Bank yang Digunakan</p><p class="font-medium">{{ $payment->akunCoa ? $payment->akunCoa->nomor_akun.' - '.$payment->akunCoa->nama_akun : '-' }}</p>
            @can('pembayaran-pranota-bpjs-create')
            <form method="POST" action="{{ route('pembayaran-pranota-bpjs.update', $payment) }}" class="mt-3 space-y-2 rounded-lg border border-gray-200 bg-gray-50 p-3" onsubmit="return confirm('Simpan perubahan Bank yang Digunakan? Jurnal kredit pembayaran akan dipindahkan ke rekening terpilih dan saldo rekening lama serta baru akan disesuaikan.');">
                @csrf
                @method('PATCH')
                <input type="hidden" name="expected_akun_coa_id" value="{{ $payment->akun_coa_id }}">
                <label for="edit-akun-coa-id" class="block text-xs font-semibold text-gray-700">Pilih bank yang digunakan</label>
                <select id="edit-akun-coa-id" name="akun_coa_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" aria-describedby="edit-bank-description">
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" @selected(old('akun_coa_id', $payment->akun_coa_id) == $account->id)>{{ $account->nomor_akun }} - {{ $account->nama_akun }}</option>
                    @endforeach
                </select>
                <p id="edit-bank-description" class="text-xs leading-relaxed text-gray-500">Perubahan rekening menyesuaikan jurnal dan saldo bank pembayaran ini. Total pembayaran tetap sama.</p>
                <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">Simpan Bank</button>
            </form>
            @endcan
        </div>
        <div><p class="text-gray-500">Nomor Accurate</p>
            @can('pembayaran-pranota-bpjs-create')
                <form method="POST" action="{{ route('pembayaran-pranota-bpjs.update', $payment) }}" class="mt-2 flex flex-wrap items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <label for="nomor-accurate" class="sr-only">Nomor Accurate</label>
                    <input id="nomor-accurate" name="nomor_accurate" value="{{ old('nomor_accurate', $payment->nomor_accurate) }}" maxlength="255" placeholder="Masukkan nomor Accurate" class="rounded-lg border-gray-300 text-sm">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan</button>
                </form>
            @else
                <p>{{ $payment->nomor_accurate ?? '-' }}</p>
            @endcan
        </div>
        <div><p class="text-gray-500">Dibuat Oleh</p><p>{{ $payment->createdBy?->name ?? '-' }}</p></div>
        <div class="md:col-span-2"><p class="text-gray-500">Keterangan</p><p class="whitespace-pre-line">{{ $payment->keterangan ?? '-' }}</p></div>
    </div>
    <div class="bg-white border rounded-xl overflow-x-auto"><table class="w-full text-sm text-left"><thead class="bg-gray-50"><tr><th class="p-4">Nomor Pranota</th><th class="p-4">Periode</th><th class="p-4 text-right">Dibayar</th></tr></thead><tbody class="divide-y">
        @foreach($payment->pranotas as $pranota)
        <tr><td class="p-4">@can('pranota-bpjs-view')<a class="text-blue-600" href="{{ route('pranota-bpjs.show', $pranota) }}">{{ $pranota->nomor_pranota }}</a>@else{{ $pranota->nomor_pranota }}@endcan</td><td class="p-4">{{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</td><td class="p-4 text-right">Rp {{ number_format($pranota->pivot->subtotal, 2, ',', '.') }}</td></tr>
        @endforeach
    </tbody><tfoot class="bg-green-50 font-bold"><tr><td colspan="2" class="p-4">Total Pembayaran</td><td class="p-4 text-right">Rp {{ number_format($payment->total_pembayaran, 2, ',', '.') }}</td></tr></tfoot></table></div>
    @if($parts->isNotEmpty())
    <div class="bg-white border rounded-xl overflow-x-auto">
        <h2 class="p-4 font-semibold border-b">Rincian Tipe yang Dibayar</h2>
        <table class="w-full text-sm text-left"><thead class="bg-gray-50"><tr><th class="p-4">Tipe</th><th class="p-4">Akun Bank yang Digunakan</th><th class="p-4 text-right">Nominal</th><th class="p-4 text-right">TUNJANGAN</th><th class="p-4 text-right">BIAYA</th><th class="p-4 text-right">HUTANG</th></tr></thead><tbody class="divide-y">
        @foreach($parts as $part)
            @php($amounts = $journalAmounts[$part->komponen.'|'.$part->tipe] ?? ['tunjangan' => 0, 'biaya' => 0, 'hutang' => 0])
            <tr><td class="p-4">{{ $part->tipe }}</td><td class="p-4">{{ $accountLabels[$part->komponen.'|'.$part->tipe] ?? '-' }}</td><td class="p-4 text-right">Rp {{ number_format($part->subtotal, 2, ',', '.') }}</td><td class="p-4 text-right">Rp {{ number_format($amounts['tunjangan'], 2, ',', '.') }}</td><td class="p-4 text-right">Rp {{ number_format($amounts['biaya'], 2, ',', '.') }}</td><td class="p-4 text-right">Rp {{ number_format($amounts['hutang'], 2, ',', '.') }}</td></tr>
        @endforeach
        </tbody></table>
    </div>
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5 text-sm text-blue-900">
        <h3 class="mb-2 font-semibold">Catatan</h3>
        <p class="leading-relaxed">Jika menggunakan Akun Bank Jakarta, maka pada Biaya gunakan akun Biaya BPJS Jakarta. Sebaliknya, jika menggunakan Akun Bank Batam, maka pada Biaya gunakan akun Biaya BPJS Batam. Sedangkan Hutang menggunakan akun yang sama untuk Jakarta dan Batam, yaitu Utang Biaya BPJS.</p>
        <dl class="mt-4 space-y-2">
            <div><dt class="inline font-semibold">Akun Biaya BPJS Jakarta:</dt> <dd class="inline">6101.006 - Biaya BPJS Jakarta</dd></div>
            <div><dt class="inline font-semibold">Akun Biaya BPJS Batam:</dt> <dd class="inline">6201.006 - Biaya BPJS Batam</dd></div>
            <div><dt class="inline font-semibold">Akun Hutang BPJS:</dt> <dd class="inline">2103.002 - Utang Biaya BPJS</dd></div>
        </dl>
    </div>
    @endif
</div>
@endsection
