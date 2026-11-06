@extends('layouts.app')

@section('title', 'Master Data ABK - Daftar Kapal')
@section('page_title', 'Master Data ABK')

@section('content')
<div class="mx-auto max-w-full px-4">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 pt-6 sm:px-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Master Data ABK</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Daftar Kapal</h1>
                    <p class="mt-1 text-sm text-gray-500">Daftar kapal yang tersedia pada Master Kapal.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-right">
                    <p class="text-xs font-medium text-gray-500">Kapal ditemukan</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($kapals->total(), 0, ',', '.') }}</p>
                </div>
            </div>
            <nav class="mt-6 flex gap-2" aria-label="Tampilan Master Data ABK">
                <a href="{{ route('master.karyawan.abk-index', ['tampilan' => 'abk']) }}" class="border-b-2 border-transparent px-4 py-3 text-sm font-medium text-gray-500 transition hover:border-gray-300 hover:text-gray-900">Data ABK</a>
                <span class="border-b-2 border-blue-600 px-4 py-3 text-sm font-semibold text-blue-700" aria-current="page">Daftar Kapal</span>
            </nav>
        </div>
        <div class="bg-gray-50 px-5 py-5 sm:px-7">
            <form method="GET" action="{{ route('master.karyawan.abk-index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <input type="hidden" name="tampilan" value="kapal">
                <label for="cari-kapal" class="sr-only">Cari kapal</label>
                <div class="relative w-full sm:max-w-md">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input id="cari-kapal" type="search" name="cari_kapal" value="{{ request('cari_kapal') }}" placeholder="Cari nama, kode, atau pelayaran..." class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-11 pr-4 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Cari Kapal</button>
                    @if(request()->filled('cari_kapal'))<a href="{{ route('master.karyawan.abk-index', ['tampilan' => 'kapal']) }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100">Reset</a>@endif
                </div>
            </form>
        </div>
        <div class="bg-gray-50 px-5 pb-6 sm:px-7">
            @if(session('success'))<div role="status" class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @forelse($kapals as $kapal)
                <article class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-blue-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-lg text-blue-600"><i class="fas fa-ship" aria-hidden="true"></i></span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ strtolower($kapal->status ?? '') === 'aktif' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}"><span class="h-1.5 w-1.5 rounded-full {{ strtolower($kapal->status ?? '') === 'aktif' ? 'bg-green-500' : 'bg-gray-400' }}"></span>{{ ucfirst($kapal->status ?: '-') }}</span>
                    </div>
                    <h2 class="mt-5 break-words text-base font-bold text-gray-900">{{ $kapal->nama_kapal }}</h2>
                    <p class="mt-1 break-words text-sm text-gray-500">{{ $kapal->pelayaran ?: 'Pelayaran belum tersedia' }}</p>
                    <dl class="mt-5 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4 text-sm">
                        <div><dt class="text-xs font-medium text-gray-500">Kode</dt><dd class="mt-1 break-words font-semibold text-gray-800">{{ $kapal->kode ?: '-' }}</dd></div>
                        <div><dt class="text-xs font-medium text-gray-500">Kode Kapal</dt><dd class="mt-1 break-words font-semibold text-gray-800">{{ $kapal->kode_kapal ?: '-' }}</dd></div>
                    </dl>
                    <div class="mt-5 border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-sm font-semibold text-gray-800">ABK di kapal</h3>
                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ $kapal->abk->count() }} ABK</span>
                        </div>
                        @if($kapal->abk->isNotEmpty())
                            <ul class="mt-3 space-y-2">
                                @foreach($kapal->abk as $crew)
                                <li class="flex items-center justify-between gap-2 rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="min-w-0 break-words text-sm text-gray-800">{{ $crew->nama_lengkap }} <span class="text-xs text-gray-500">{{ $crew->nik ? '('.$crew->nik.')' : '' }}</span></span>
                                    @can('master-karyawan-abk-update')
                                    <form method="POST" action="{{ route('master.karyawan.abk-kapal.remove', [$kapal, $crew]) }}" onsubmit="return confirm('Lepaskan ABK dari kapal ini?')">@csrf @method('DELETE')<button type="submit" class="shrink-0 text-xs font-semibold text-red-600 hover:text-red-800" aria-label="Lepaskan {{ $crew->nama_lengkap }}">Lepas</button></form>
                                    @endcan
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-3 text-sm text-gray-500">Belum ada ABK yang ditempatkan.</p>
                        @endif
                        @can('master-karyawan-abk-update')
                        <form method="POST" action="{{ route('master.karyawan.abk-kapal.assign', $kapal) }}" class="mt-4 space-y-2">
                            @csrf
                            <label for="abk-{{ $kapal->id }}" class="text-xs font-medium text-gray-600">Pilih ABK aktif</label>
                            <select id="abk-{{ $kapal->id }}" name="karyawan_id" required class="abk-search-select block w-full rounded-lg border border-gray-300 bg-white text-sm" @disabled($availableAbk->isEmpty())>
                                <option value="">{{ $availableAbk->isEmpty() ? 'Tidak ada ABK tersedia' : 'Pilih ABK' }}</option>
                                @foreach($availableAbk as $crew)<option value="{{ $crew->id }}">{{ $crew->nama_lengkap }}{{ $crew->nik ? ' ('.$crew->nik.')' : '' }}</option>@endforeach
                            </select>
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50" @disabled($availableAbk->isEmpty())><i class="fas fa-plus mr-1" aria-hidden="true"></i> Tambahkan ABK</button>
                        </form>
                        @endcan
                    </div>
                </article>
                @empty
                <p class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white px-5 py-10 text-center text-sm text-gray-500">Tidak ada kapal yang ditemukan.</p>
                @endforelse
            </div>
        </div>
        <div class="px-5 py-4">{{ $kapals->links() }}</div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .select2-container .select2-selection--single.abk-selection { height: 40px; border: 1px solid #d1d5db; border-radius: 0.5rem; }
    .select2-container .abk-selection .select2-selection__rendered { line-height: 38px; padding-left: 12px; font-size: 0.875rem; }
    .select2-container .abk-selection .select2-selection__arrow { height: 38px; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.jQuery || !jQuery.fn.select2) return;
        jQuery('.abk-search-select').select2({
            width: '100%',
            placeholder: 'Cari nama atau NIK ABK',
            language: { noResults: () => 'ABK tidak ditemukan' },
        }).each(function () {
            const select = this;
            jQuery(select).next('.select2').find('.select2-selection').addClass('abk-selection');
            select.addEventListener('invalid', function (event) {
                event.preventDefault();
                jQuery(select).select2('open');
            });
        });
    });
</script>
@endpush
