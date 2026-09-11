@extends('layouts.app')
@section('title', 'Dashboard Pemakaian Barang')
@section('page_title', 'Dashboard Pemakaian Barang')
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <div>
        <a href="{{ route('stock-amprahan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-6"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke Stock Amprahan</a>
        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600 mb-3">Dashboard Pemakaian Barang</p>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Pilih kategori dan periode pemakaian</h1>
        <p class="text-sm sm:text-base text-gray-500 mt-3 max-w-2xl leading-relaxed">Telusuri barang yang digunakan dan total nilai pemakaiannya. Mulai dengan memilih kategori pemakai dan tanggal pengambilan.</p>
    </div>
    <form method="GET" action="{{ route('stock-amprahan.dashboard-pemakaian.hasil') }}" class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-8 shadow-sm">
        @if($errors->any())
            <div role="alert" class="mb-4 text-sm text-red-700 bg-red-50 p-3 rounded-lg">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif
        <div class="space-y-8">
            <fieldset>
                <legend class="text-base font-semibold text-gray-900"><span class="inline-flex items-center justify-center w-8 h-8 mr-2 rounded-full bg-indigo-50 text-indigo-700 text-sm" aria-hidden="true">1</span>Pilih kategori pemakai</legend>
                <p class="text-sm text-gray-500 mt-2 mb-5">Pilih salah satu kategori di bawah ini.</p>
                @php
                    $details = [
                        'penerima' => ['fa-users', 'Barang yang diterima karyawan'],
                        'kendaraan' => ['fa-truck', 'Kebutuhan kendaraan dan truck'],
                        'alat_berat' => ['fa-tractor', 'Operasional alat berat'],
                        'kapal' => ['fa-ship', 'Kebutuhan operasional kapal'],
                        'kantor' => ['fa-building', 'Perlengkapan dan kebutuhan kantor'],
                    ];
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    @foreach($categories as $value => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="kategori_pemakai" value="{{ $value }}" required @checked(old('kategori_pemakai', request('kategori_pemakai')) === $value) class="peer absolute top-4 right-4 w-4 h-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="block h-full rounded-xl border border-gray-200 p-5 transition-colors hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:ring-1 peer-checked:ring-indigo-600 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 peer-focus-visible:ring-offset-2">
                                <span class="inline-flex items-center justify-center w-11 h-11 bg-indigo-100 text-indigo-700 rounded-xl mb-4"><i class="fas {{ $details[$value][0] }} text-lg" aria-hidden="true"></i></span>
                                <span class="block text-sm font-semibold text-gray-900">{{ $label }}</span>
                                <span class="block text-xs text-gray-500 leading-relaxed mt-2">{{ $details[$value][1] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
            <fieldset class="border-t border-gray-100 pt-6">
                <legend class="text-base font-semibold text-gray-900"><span class="inline-flex items-center justify-center w-8 h-8 mr-2 rounded-full bg-indigo-50 text-indigo-700 text-sm" aria-hidden="true">2</span>Tentukan periode pemakaian</legend>
                <p class="text-sm text-gray-500 mb-5">Periode mengikuti tanggal pengambilan barang.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="from_date" class="block text-sm font-semibold mb-2">Tanggal Dari</label>
                <input type="date" id="from_date" name="from_date" required value="{{ old('from_date', request('from_date', now()->startOfMonth()->format('Y-m-d'))) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-semibold mb-2">Tanggal Ke</label>
                <input type="date" id="to_date" name="to_date" required value="{{ old('to_date', request('to_date', now()->format('Y-m-d'))) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500">
            </div>
                </div>
                <p class="text-xs text-gray-500 mt-4"><i class="fas fa-circle-info text-indigo-500 mr-1" aria-hidden="true"></i>Data pada tanggal awal dan akhir ikut ditampilkan.</p>
            </fieldset>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-8 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-500">Rincian pemakaian ditampilkan di halaman berikutnya.</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('stock-amprahan.dashboard-pemakaian') }}" class="px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 text-sm">Reset</a>
                <button type="submit" class="flex-1 sm:flex-none px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tampilkan Data <i class="fas fa-arrow-right ml-2" aria-hidden="true"></i></button>
            </div>
        </div>
    </form>
</div>
@endsection
