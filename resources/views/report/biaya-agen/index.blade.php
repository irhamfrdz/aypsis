@extends('layouts.app')

@section('title', 'Report Biaya Agen')
@section('page_title', 'Report Biaya Agen')

@section('content')
<div class="mx-auto max-w-6xl px-2 py-4 sm:px-4 sm:py-6">
    <div class="mb-8">
        <a href="{{ route('rekap.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-700 hover:text-indigo-900">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            Rekap dan Laporan
        </a>
        <div class="mt-5 flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-xl text-indigo-700 sm:h-14 sm:w-14">
                <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Report Biaya Agen</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600 sm:text-base">Pilih cara melihat rincian biaya agen sesuai kebutuhan Anda.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
            <p class="font-semibold">Periksa kembali pilihan report:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <form method="GET" action="{{ route('report.biaya-agen.show') }}" class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <input type="hidden" name="mode" value="kapal">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-lg text-indigo-600">
                    <i class="fas fa-ship" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 sm:text-xl">Berdasarkan kapal</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Lihat seluruh rincian biaya agen untuk satu kapal.</p>
                </div>
            </div>

            <div class="mt-8">
                <label for="kapal" class="mb-2 block text-sm font-semibold text-gray-800">Pilih kapal <span class="text-red-600">*</span></label>
                <select id="kapal" name="kapal" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    <option value="">Pilih kapal</option>
                    @foreach($kapals as $kapal)
                        <option value="{{ $kapal }}" @selected(old('kapal') === $kapal)>{{ $kapal }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4">
                <label for="jenis_biaya_kapal" class="mb-2 block text-sm font-semibold text-gray-800">Jenis biaya</label>
                <select id="jenis_biaya_kapal" name="jenis_biaya" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    <option value="">Semua jenis biaya</option>
                    @foreach($jenisBiayas as $jenisBiaya)
                        <option value="{{ $jenisBiaya }}" @selected(old('jenis_biaya') === $jenisBiaya)>{{ $jenisBiaya }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-auto pt-6">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Lihat report kapal
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        </form>

        <form method="GET" action="{{ route('report.biaya-agen.show') }}" class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <input type="hidden" name="mode" value="tanggal">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-lg text-indigo-600">
                    <i class="fas fa-calendar-days" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 sm:text-xl">Berdasarkan tanggal</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Lihat rincian biaya agen dalam rentang tanggal tertentu.</p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 2xl:grid-cols-2">
                <div class="min-w-0">
                    <label for="start_date" class="mb-2 block text-sm font-semibold text-gray-800">Dari tanggal <span class="text-red-600">*</span></label>
                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" required class="block w-full min-w-0 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                </div>
                <div class="min-w-0">
                    <label for="end_date" class="mb-2 block text-sm font-semibold text-gray-800">Sampai tanggal <span class="text-red-600">*</span></label>
                    <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" required class="block w-full min-w-0 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                </div>
            </div>
            <div class="mt-4">
                <label for="jenis_biaya_tanggal" class="mb-2 block text-sm font-semibold text-gray-800">Jenis biaya</label>
                <select id="jenis_biaya_tanggal" name="jenis_biaya" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    <option value="">Semua jenis biaya</option>
                    @foreach($jenisBiayas as $jenisBiaya)
                        <option value="{{ $jenisBiaya }}" @selected(old('jenis_biaya') === $jenisBiaya)>{{ $jenisBiaya }}</option>
                    @endforeach
                </select>
            </div>
            <p class="mt-3 text-xs leading-5 text-gray-500">Menggunakan tanggal invoice vendor. Jika kosong, tanggal biaya kapal yang digunakan.</p>

            <div class="mt-auto pt-6">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Lihat report tanggal
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        </form>

        <form method="GET" action="{{ route('report.biaya-agen.show') }}" class="rounded-2xl border border-indigo-200 bg-white p-6 shadow-sm sm:p-8 lg:col-span-2">
            <input type="hidden" name="mode" value="gabungan">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-lg text-indigo-600">
                    <i class="fas fa-filter" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 sm:text-xl">Kapal dan rentang tanggal</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Lihat rincian satu kapal hanya dalam periode yang dipilih.</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="min-w-0 sm:col-span-2 lg:col-span-1">
                    <label for="kapal_gabungan" class="mb-2 block text-sm font-semibold text-gray-800">Pilih kapal <span class="text-red-600">*</span></label>
                    <select id="kapal_gabungan" name="kapal" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="">Pilih kapal</option>
                        @foreach($kapals as $kapal)
                            <option value="{{ $kapal }}" @selected(old('kapal') === $kapal)>{{ $kapal }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0">
                    <label for="start_date_gabungan" class="mb-2 block text-sm font-semibold text-gray-800">Dari tanggal <span class="text-red-600">*</span></label>
                    <input id="start_date_gabungan" name="start_date" type="date" value="{{ old('start_date') }}" required class="block w-full min-w-0 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                </div>
                <div class="min-w-0">
                    <label for="end_date_gabungan" class="mb-2 block text-sm font-semibold text-gray-800">Sampai tanggal <span class="text-red-600">*</span></label>
                    <input id="end_date_gabungan" name="end_date" type="date" value="{{ old('end_date') }}" required class="block w-full min-w-0 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                </div>
            </div>
            <div class="mt-4">
                <label for="jenis_biaya_gabungan" class="mb-2 block text-sm font-semibold text-gray-800">Jenis biaya</label>
                <select id="jenis_biaya_gabungan" name="jenis_biaya" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    <option value="">Semua jenis biaya</option>
                    @foreach($jenisBiayas as $jenisBiaya)
                        <option value="{{ $jenisBiaya }}" @selected(old('jenis_biaya') === $jenisBiaya)>{{ $jenisBiaya }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs leading-5 text-gray-500">Tanggal invoice vendor digunakan; jika kosong, tanggal biaya kapal.</p>
                <button type="submit" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Lihat report gabungan
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
