@extends('layouts.app')
@section('title', 'Dashboard Pemakaian Barang')
@section('page_title', 'Dashboard Pemakaian Barang')

@push('styles')
<style>
    body:has(.usage-dashboard) { font-family: Arial, sans-serif; font-size: 12px; }
    body:has(.usage-dashboard) #page-content { background: #F5F7FA; }
    body:has(.usage-dashboard) > header h1 { font-family: Arial, sans-serif; font-size: 20px; }
    body:has(.usage-dashboard) #sidebar :is(a, input, .menu-text),
    body:has(.usage-dashboard) > header :is(button, a, span:not([class*="fa-"])) { font-family: Arial, sans-serif; font-size: 12px; }
    .usage-dashboard {
        --usage-navy: #1E3A5F; --usage-line: #e1e6ed;
        font: 12px/1.5 Arial, sans-serif; color: #344256; padding: 20px;
        background: #fff; border: 1px solid var(--usage-line); border-radius: 8px;
        box-shadow: 0 2px 6px rgb(30 58 95 / 4%); width: 100%; min-width: 0;
    }
    .usage-dashboard :is(p, span, a, button, input, label, legend) { font-family: Arial, sans-serif; font-size: 12px; }
    .usage-dashboard h1 { font: 600 20px/1.4 Arial, sans-serif; letter-spacing: 0; color: var(--usage-navy); }
    .usage-dashboard .usage-header { padding-bottom: 18px; margin-bottom: 18px; border-bottom: 1px solid #edf0f4; }
    .usage-dashboard .usage-header > a { float: right; margin: 3px 0 8px 20px; color: #6b7c92; }
    .usage-dashboard .usage-header > a:hover { color: var(--usage-navy); }
    .usage-dashboard .usage-header > p { margin-top: 4px; color: #748091; max-width: none; }
    .usage-dashboard .usage-form { padding: 0; border: 0; border-radius: 0; box-shadow: none; }
    .usage-dashboard .usage-fields > * + * { margin-top: 20px; }
    .usage-dashboard fieldset { min-width: 0; }
    .usage-dashboard legend { font-weight: 600; color: var(--usage-navy); }
    .usage-dashboard legend > span { width: 22px; height: 22px; background: #edf2f8; color: var(--usage-navy); border-radius: 5px; margin-right: 8px; }
    .usage-dashboard fieldset > p { margin: 8px 0 12px; color: #748091; }
    .usage-dashboard .usage-categories { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; }
    .usage-dashboard .usage-categories label { min-width: 0; }
    .usage-dashboard .usage-categories input { top: 12px; right: 12px; width: 13px; height: 13px; color: var(--usage-navy); border-color: #bac5d3; }
    .usage-dashboard .usage-category {
        display: grid; grid-template-columns: 28px minmax(0, 1fr); align-content: start; gap: 5px 8px;
        border: 1px solid var(--usage-line); border-radius: 8px; padding: 12px 30px 12px 12px;
        background: #fff; transition: none;
    }
    .usage-dashboard .usage-category-icon { grid-column: 1; grid-row: 1 / span 2; width: 28px; height: 28px; border-radius: 5px; margin: 0; background: #f1f4f8; color: #6c829e; }
    .usage-dashboard .usage-category-icon i { font-size: 13px; }
    .usage-dashboard .usage-category-name { grid-column: 2; color: var(--usage-navy); font-weight: 600; line-height: 1.5; }
    .usage-dashboard .usage-category-description { grid-column: 2; margin: 0; color: #7b8797; line-height: 1.6; }
    .usage-dashboard .usage-category:hover { border-color: #9eafc4; }
    .usage-dashboard .usage-categories input:checked + .usage-category { border-color: var(--usage-navy); background: #f4f7fb; box-shadow: 0 0 0 1px var(--usage-navy); }
    .usage-dashboard .usage-categories input:checked + .usage-category .usage-category-icon { background: #e5ecf5; color: var(--usage-navy); }
    .usage-dashboard .usage-categories input:focus-visible + .usage-category { outline: 2px solid #718cac; outline-offset: 3px; box-shadow: none; }
    .usage-dashboard fieldset.border-t { border-color: #edf0f4; padding-top: 16px; }
    .usage-dashboard .usage-period { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .usage-dashboard .usage-period label { font-weight: 500; color: #536379; margin-bottom: 6px; }
    .usage-dashboard .usage-period input { min-height: 36px; padding: 8px 10px; border: 1px solid #d5dde7; border-radius: 5px; background: #fff; box-shadow: none; color: #344256; }
    .usage-dashboard .usage-date { position: relative; }
    .usage-dashboard .usage-date-display { display: none; position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #344256; pointer-events: none; }
    .usage-dashboard .usage-date.is-formatted .usage-date-display { display: block; }
    .usage-dashboard .usage-date.is-formatted input { color: transparent; }
    .usage-dashboard .usage-date.is-formatted input::-webkit-datetime-edit { opacity: 0; }
    .usage-dashboard fieldset > p:last-child:not(:first-of-type) { margin: 10px 0 0; }
    .usage-dashboard fieldset > p i { color: #8a9bb0; }
    .usage-dashboard .usage-footer { border-color: #edf0f4; padding-top: 16px; margin-top: 20px; gap: 12px; }
    .usage-dashboard .usage-footer > p { color: #7b8797; }
    .usage-dashboard .usage-footer > div { gap: 8px; }
    .usage-dashboard .usage-footer :is(a, button) { min-height: 34px; padding: 7px 14px; border-radius: 5px; box-shadow: none; font-weight: 500; }
    .usage-dashboard .usage-footer a { border: 1px solid #cdd7e4; color: var(--usage-navy); background: #fff; }
    .usage-dashboard .usage-footer a:hover { background: #f3f6fa; }
    .usage-dashboard .usage-footer button { border: 1px solid var(--usage-navy); background: var(--usage-navy); color: #fff; }
    .usage-dashboard .usage-footer button:hover { background: #2b4c75; }
    .usage-dashboard :is(a, button, input):focus-visible { outline: 2px solid #718cac; outline-offset: 2px; box-shadow: none; }
    .usage-dashboard [role="alert"] { font-size: 12px; border: 1px solid #efd7d7; border-radius: 6px; background: #fbeded; color: #ad3d3d; }
    @media (max-width: 1400px) {
        .usage-dashboard .usage-categories { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 1000px) {
        .usage-dashboard .usage-categories { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .usage-dashboard .usage-header > a { float: none; margin: 0 0 10px; }
    }
    @media (max-width: 640px) {
        .usage-dashboard { padding: 16px; }
        .usage-dashboard .usage-footer { align-items: stretch; }
        .usage-dashboard .usage-footer > div { justify-content: flex-end; }
        .usage-dashboard .usage-category { grid-template-columns: 24px minmax(0, 1fr); padding-left: 10px; padding-right: 26px; gap: 5px 6px; }
        .usage-dashboard .usage-category-icon { width: 24px; height: 24px; }
        .usage-dashboard .usage-categories { gap: 10px; }
    }
    @media (max-width: 480px) {
        .usage-dashboard .usage-categories { grid-template-columns: 1fr; }
        .usage-dashboard .usage-period { grid-template-columns: 1fr; gap: 12px; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.usage-dashboard [data-usage-date]').forEach(function(control) {
        const input = control.querySelector('input[type="date"]');
        const display = control.querySelector('.usage-date-display');
        function updateDisplay() {
            if (!input.value) {
                display.textContent = 'dd/mmm/yyyy';
            } else {
                const parts = input.value.split('-').map(Number);
                display.textContent = new Intl.DateTimeFormat('en-GB', {
                    day: '2-digit', month: 'short', year: 'numeric'
                }).format(new Date(parts[0], parts[1] - 1, parts[2])).replace(/ /g, '/').replace('Sept', 'Sep');
            }
            control.classList.add('is-formatted');
        }
        input.addEventListener('input', updateDisplay);
        input.addEventListener('change', updateDisplay);
        updateDisplay();
    });
</script>
@endpush

@section('content')
<div class="usage-dashboard">
    <div class="usage-header">
        <a href="{{ route('stock-amprahan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-6"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke Stock Amprahan</a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Dashboard Pemakaian Barang</h1>
        <p class="text-sm sm:text-base text-gray-500 mt-3 max-w-2xl leading-relaxed">Pilih kategori pemakai dan periode untuk melihat rincian serta total nilai pemakaian barang.</p>
    </div>
    <form method="GET" action="{{ route('stock-amprahan.dashboard-pemakaian.hasil') }}" class="usage-form bg-white border border-gray-200 rounded-2xl p-5 sm:p-8 shadow-sm">
        @if($errors->any())
            <div role="alert" class="mb-4 text-sm text-red-700 bg-red-50 p-3 rounded-lg">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif
        <div class="usage-fields space-y-8">
            <fieldset>
                <legend class="text-base font-semibold text-gray-900"><span class="inline-flex items-center justify-center w-8 h-8 mr-2 rounded-full bg-indigo-50 text-indigo-700 text-sm" aria-hidden="true">1</span>Pilih kategori pemakai</legend>
                <p class="text-sm text-gray-500 mt-2 mb-5">Pilih salah satu kategori di bawah ini.</p>
                @php
                    $details = [
                        'penerima' => ['fa-users', 'Barang yang diterima karyawan'],
                        'kendaraan' => ['fa-truck', 'Kebutuhan kendaraan dan truck'],
                        'alat_berat' => ['fa-tractor', 'Operasional alat berat'],
                        'kapal' => ['fa-ship', 'Kapal dengan pelayaran PT. ALEXINDO YAKIN PRIMA'],
                        'kantor' => ['fa-building', 'Perlengkapan dan kebutuhan kantor'],
                    ];
                @endphp
                <div class="usage-categories grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    @foreach($categories as $value => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="kategori_pemakai" value="{{ $value }}" required @checked(old('kategori_pemakai', request('kategori_pemakai')) === $value) class="peer absolute top-4 right-4 w-4 h-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="usage-category block h-full rounded-xl border border-gray-200 p-5 transition-colors hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:ring-1 peer-checked:ring-indigo-600 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 peer-focus-visible:ring-offset-2">
                                <span class="usage-category-icon inline-flex items-center justify-center w-11 h-11 bg-indigo-100 text-indigo-700 rounded-xl mb-4"><i class="fas {{ $details[$value][0] }} text-lg" aria-hidden="true"></i></span>
                                <span class="usage-category-name block text-sm font-semibold text-gray-900">{{ $label }}</span>
                                <span class="usage-category-description block text-xs text-gray-500 leading-relaxed mt-2">{{ $details[$value][1] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
            <fieldset class="border-t border-gray-100 pt-6">
                <legend class="text-base font-semibold text-gray-900"><span class="inline-flex items-center justify-center w-8 h-8 mr-2 rounded-full bg-indigo-50 text-indigo-700 text-sm" aria-hidden="true">2</span>Tentukan periode pemakaian</legend>
                <p class="text-sm text-gray-500 mb-5">Periode mengikuti tanggal pengambilan barang.</p>
                <div class="usage-period grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="from_date" class="block text-sm font-semibold mb-2">Tanggal Dari</label>
                <div class="usage-date" data-usage-date>
                    <span class="usage-date-display" aria-hidden="true"></span>
                    <input type="date" id="from_date" name="from_date" required value="{{ old('from_date', request('from_date', now()->startOfMonth()->format('Y-m-d'))) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label for="to_date" class="block text-sm font-semibold mb-2">Tanggal Ke</label>
                <div class="usage-date" data-usage-date>
                    <span class="usage-date-display" aria-hidden="true"></span>
                    <input type="date" id="to_date" name="to_date" required value="{{ old('to_date', request('to_date', now()->format('Y-m-d'))) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
                </div>
                <p class="text-xs text-gray-500 mt-4"><i class="fas fa-circle-info text-indigo-500 mr-1" aria-hidden="true"></i>Data pada tanggal awal dan akhir ikut ditampilkan.</p>
            </fieldset>
        </div>
        <div class="usage-footer flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-8 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-500">Rincian pemakaian ditampilkan di halaman berikutnya.</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('stock-amprahan.dashboard-pemakaian') }}" class="px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 text-sm">Reset</a>
                <button type="submit" class="flex-1 sm:flex-none px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tampilkan Data <i class="fas fa-arrow-right ml-2" aria-hidden="true"></i></button>
            </div>
        </div>
    </form>
</div>
@endsection
