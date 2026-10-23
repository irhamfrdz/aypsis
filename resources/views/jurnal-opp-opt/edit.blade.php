@extends('layouts.app')

@section('title', 'Ubah Jurnal OPT/OPP - AYPSIS')
@section('page_title', 'Ubah Jurnal OPT/OPP')

@section('content')
<div class="mx-auto max-w-5xl px-1 pb-8 sm:px-0">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Ubah Transaksi</h1>
            <p class="mt-1 text-sm text-slate-500">Saldo berjalan setelah transaksi ini akan dihitung ulang otomatis.</p>
        </div>
        <a href="{{ route('jurnal-opp-opt.index') }}" class="inline-flex items-center gap-2 self-start text-sm font-medium text-slate-600 transition hover:text-slate-900"><i class="fas fa-arrow-left"></i>Kembali ke daftar</a>
    </div>

    <form method="POST" action="{{ route('jurnal-opp-opt.update', $jurnalOppOpt) }}" class="space-y-5">
        @csrf
        @method('PUT')
        @include('jurnal-opp-opt._form', ['submitLabel' => 'Simpan Perubahan'])
    </form>
</div>
@endsection
