@extends('layouts.app')

@section('title', 'Laporan Harian Kas Truck')
@section('page_title', 'Laporan Harian Kas Truck')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h1 class="text-xl font-bold text-slate-900">Laporan Harian Kas Truck</h1>
                <p class="mt-1 text-sm text-slate-600">Pilih tanggal pembayaran untuk mengunduh uang jalan yang sudah dibayar.</p>
            </div>

            <form method="GET" action="{{ route('laporan-harian-kas-truck.export') }}" class="space-y-6 p-6">
                <div>
                    <label for="tanggal" class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Pembayaran</label>
                    <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $tanggal) }}" required
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('tanggal')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    <strong>{{ number_format($jumlahData, 0, ',', '.') }} data</strong> uang jalan berstatus sudah dibayar ditemukan pada tanggal yang dipilih saat halaman dibuka.
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 font-semibold text-white hover:bg-emerald-700">
                    <i class="fas fa-file-excel mr-2"></i> Download Excel
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
