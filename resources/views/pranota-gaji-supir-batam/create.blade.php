@extends('layouts.app')

@section('title', 'Buat Pranota Gaji Supir Batam')
@section('page_title', 'Buat Pranota Gaji Supir Batam')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-6">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-gray-900">Buat Pranota Gaji Supir Batam</h1><p class="mt-1 text-sm text-gray-600">Pilih gaji berstatus PENDING yang akan digabungkan.</p></div>
        <a href="{{ route('pranota-gaji-supir-batam.index') }}" class="text-sm font-medium text-indigo-700 hover:underline">Kembali</a>
    </div>
    @if($errors->any())<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <form method="POST" action="{{ route('pranota-gaji-supir-batam.store') }}" class="space-y-5">
        @csrf
        <div class="grid gap-4 rounded-xl border bg-white p-5 shadow-sm sm:grid-cols-2">
            <div><label for="tanggal_pranota" class="mb-1 block text-sm font-medium">Tanggal Pranota <span class="text-red-600">*</span></label><input id="tanggal_pranota" name="tanggal_pranota" type="date" value="{{ old('tanggal_pranota', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label for="keterangan" class="mb-1 block text-sm font-medium">Keterangan</label><input id="keterangan" name="keterangan" value="{{ old('keterangan') }}" maxlength="2000" class="w-full rounded-lg border-gray-300 text-sm"></div>
        </div>
        <div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600"><tr>
                    <th class="px-4 py-3"><input type="checkbox" id="select-all-gaji" aria-label="Pilih semua gaji"></th><th class="px-4 py-3">Supir</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3 text-right">Gaji Pokok</th><th class="px-4 py-3 text-right">Gaji Bersih</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($gajiList as $gaji)
                        <tr>
                            <td class="px-4 py-3"><input type="checkbox" name="gaji_ids[]" value="{{ $gaji->id }}" data-gaji-checkbox data-total="{{ $gaji->total_gaji }}" @checked(in_array($gaji->id, old('gaji_ids', []))) class="rounded border-gray-300 text-indigo-600"></td>
                            <td class="px-4 py-3"><span class="font-medium">{{ $gaji->karyawan?->nama_lengkap ?: 'Supir tidak tersedia' }}</span><span class="block text-xs text-gray-500">{{ $gaji->karyawan?->plat ?: '-' }}</span></td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $gaji->periode_text }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) $gaji->gaji_pokok, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $gaji->total_gaji, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Tidak ada gaji PENDING yang tersedia. <a href="{{ route('gaji-supir-batam.index') }}" class="text-indigo-700 hover:underline">Lihat daftar gaji</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border bg-white p-4 shadow-sm">
            <p class="text-sm font-semibold">Dipilih: <span id="selected-count">0</span> gaji · Total: <span id="selected-total">Rp 0</span></p>
            <button type="submit" @disabled($gajiList->isEmpty()) class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">Simpan Pranota</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const boxes = [...document.querySelectorAll('[data-gaji-checkbox]')];
    const all = document.getElementById('select-all-gaji');
    const update = () => {
        const selected = boxes.filter(box => box.checked);
        document.getElementById('selected-count').textContent = selected.length;
        document.getElementById('selected-total').textContent = new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', maximumFractionDigits: 0}).format(selected.reduce((sum, box) => sum + Number(box.dataset.total), 0));
        all.checked = boxes.length > 0 && selected.length === boxes.length;
        all.indeterminate = selected.length > 0 && selected.length < boxes.length;
    };
    boxes.forEach(box => box.addEventListener('change', update));
    all.addEventListener('change', () => { boxes.forEach(box => box.checked = all.checked); update(); });
    update();
});
</script>
@endpush
