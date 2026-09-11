@extends('layouts.app')
@section('title', 'Dashboard Pemakaian Barang')
@section('page_title', 'Dashboard Pemakaian Barang')
@section('content')
<div class="container mx-auto px-4 py-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pilih Kategori dan Periode Pemakaian</h1>
        <p class="text-sm text-gray-500 mt-1">Valuasi pemakaian Stock Amprahan berdasarkan kategori pemakai dan tanggal pengambilan.</p>
        <a href="{{ route('stock-amprahan.index') }}" class="text-sm text-indigo-600 hover:underline">Kembali ke Stock Amprahan</a>
    </div>
    <form method="GET" action="{{ route('stock-amprahan.dashboard-pemakaian.hasil') }}" class="bg-white border rounded-xl p-5 shadow-sm">
        @if($errors->any())
            <div role="alert" class="mb-4 text-sm text-red-700 bg-red-50 p-3 rounded-lg">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="kategori_pemakai" class="block text-sm font-semibold mb-2">Kategori Pemakai</label>
                <select id="kategori_pemakai" name="kategori_pemakai" required class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Pilih kategori pemakai</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" @selected(old('kategori_pemakai', request('kategori_pemakai')) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from_date" class="block text-sm font-semibold mb-2">Tanggal Dari</label>
                <input type="date" id="from_date" name="from_date" required value="{{ old('from_date', request('from_date', now()->startOfMonth()->format('Y-m-d'))) }}" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-semibold mb-2">Tanggal Ke</label>
                <input type="date" id="to_date" name="to_date" required value="{{ old('to_date', request('to_date', now()->format('Y-m-d'))) }}" class="w-full border-gray-300 rounded-lg text-sm">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-5">
            <a href="{{ route('stock-amprahan.dashboard-pemakaian') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-sm">Reset</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">Tampilkan Data</button>
        </div>
    </form>
</div>
@endsection
