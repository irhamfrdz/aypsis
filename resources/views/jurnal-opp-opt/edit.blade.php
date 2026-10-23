@extends('layouts.app')

@section('title', 'Ubah Jurnal OPT/OPP - AYPSIS')
@section('page_title', 'Ubah Jurnal OPT/OPP')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ubah Transaksi</h1>
        <p class="mt-1 text-sm text-gray-500">Saldo berjalan setelah transaksi ini akan dihitung ulang otomatis.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('jurnal-opp-opt.update', $jurnalOppOpt) }}">
            @csrf
            @method('PUT')
            @include('jurnal-opp-opt._form', ['submitLabel' => 'Simpan Perubahan'])
        </form>
    </div>
</div>
@endsection
