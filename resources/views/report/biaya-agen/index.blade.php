@extends('layouts.app')

@section('title', 'Report Biaya Agen')
@section('page_title', 'Report Biaya Agen')

@section('content')
<div class="mx-auto max-w-5xl space-y-6 px-4 py-6">
    <div>
        <a href="{{ route('rekap.index') }}" class="text-sm font-medium text-indigo-700 hover:underline">&larr; Rekap dan Laporan</a>
        <h1 class="mt-3 text-2xl font-bold text-gray-900">Report Biaya Agen</h1>
        <p class="mt-1 text-sm text-gray-600">Pilih kapal atau rentang tanggal untuk melihat rincian biaya agen.</p>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <form method="GET" action="{{ route('report.biaya-agen.show') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <input type="hidden" name="mode" value="kapal">
            <h2 class="text-lg font-semibold text-gray-900">Berdasarkan kapal</h2>
            <p class="mt-1 text-sm text-gray-500">Tampilkan seluruh rincian biaya agen untuk satu kapal.</p>
            <label for="kapal" class="mt-5 block text-sm font-medium text-gray-700">Kapal</label>
            <select id="kapal" name="kapal" required class="mt-2 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Pilih kapal</option>
                @foreach($kapals as $kapal)
                    <option value="{{ $kapal }}" @selected(old('kapal') === $kapal)>{{ $kapal }}</option>
                @endforeach
            </select>
            <button type="submit" class="mt-5 w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Lihat report kapal</button>
        </form>

        <form method="GET" action="{{ route('report.biaya-agen.show') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <input type="hidden" name="mode" value="tanggal">
            <h2 class="text-lg font-semibold text-gray-900">Berdasarkan rentang tanggal</h2>
            <p class="mt-1 text-sm text-gray-500">Gunakan tanggal invoice vendor, atau tanggal biaya kapal jika tanggal vendor kosong.</p>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Dari tanggal</label>
                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" required class="mt-2 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai tanggal</label>
                    <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" required class="mt-2 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <button type="submit" class="mt-5 w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Lihat report tanggal</button>
        </form>
    </div>
</div>
@endsection
