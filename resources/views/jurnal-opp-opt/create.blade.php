@extends('layouts.app')

@section('title', 'Tambah Jurnal OPT/OPP - AYPSIS')
@section('page_title', 'Tambah Jurnal OPT/OPP')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Transaksi</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $mode === 'voyage' ? 'Catat transaksi dengan referensi manifest kapal.' : 'Catat penambahan saldo atau kredit Jurnal OPT/OPP.' }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('jurnal-opp-opt.store') }}">
            @csrf
            @include('jurnal-opp-opt._form', ['submitLabel' => 'Simpan Transaksi'])
        </form>
    </div>
</div>
@endsection
