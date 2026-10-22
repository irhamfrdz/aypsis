@extends('layouts.app')

@section('title', 'Buat Permohonan Pinjaman Karyawan - AYPSIS')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-6">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('permohonan-pinjaman-karyawan.index') }}" class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-black text-gray-900">Buat Permohonan Pinjaman</h1>
            <p class="text-sm text-gray-500">Permohonan baru akan masuk ke antrean persetujuan.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('permohonan-pinjaman-karyawan.store') }}" method="POST" class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="karyawan_id" class="mb-1 block text-sm font-semibold text-gray-700">Karyawan <span class="text-red-500">*</span></label>
                <select name="karyawan_id" id="karyawan_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" @selected((string) old('karyawan_id') === (string) $karyawan->id)>
                            {{ $karyawan->nik ? $karyawan->nik.' - ' : '' }}{{ $karyawan->nama_lengkap ?: $karyawan->nama_panggilan }}{{ $karyawan->divisi ? ' ('.$karyawan->divisi.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tanggal_permohonan" class="mb-1 block text-sm font-semibold text-gray-700">Tanggal Permohonan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_permohonan" id="tanggal_permohonan" value="{{ old('tanggal_permohonan', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="tanggal_dibutuhkan" class="mb-1 block text-sm font-semibold text-gray-700">Tanggal Dibutuhkan</label>
                <input type="date" name="tanggal_dibutuhkan" id="tanggal_dibutuhkan" value="{{ old('tanggal_dibutuhkan') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="nominal" class="mb-1 block text-sm font-semibold text-gray-700">Nominal Pinjaman <span class="text-red-500">*</span></label>
                <input type="number" name="nominal" id="nominal" value="{{ old('nominal') }}" min="1" step="0.01" required placeholder="Contoh: 5000000" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="tenor_bulan" class="mb-1 block text-sm font-semibold text-gray-700">Tenor (Bulan) <span class="text-red-500">*</span></label>
                <input type="number" name="tenor_bulan" id="tenor_bulan" value="{{ old('tenor_bulan', 1) }}" min="1" max="120" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="md:col-span-2 rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                <div class="text-xs font-bold uppercase text-indigo-700">Estimasi cicilan per bulan</div>
                <div id="estimasiCicilan" class="mt-1 text-xl font-black text-indigo-900">Rp 0</div>
            </div>
            <div class="md:col-span-2">
                <label for="tujuan_pinjaman" class="mb-1 block text-sm font-semibold text-gray-700">Tujuan Pinjaman <span class="text-red-500">*</span></label>
                <textarea name="tujuan_pinjaman" id="tujuan_pinjaman" rows="4" required maxlength="5000" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jelaskan kebutuhan pinjaman...">{{ old('tujuan_pinjaman') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label for="catatan_pemohon" class="mb-1 block text-sm font-semibold text-gray-700">Catatan Tambahan</label>
                <textarea name="catatan_pemohon" id="catatan_pemohon" rows="3" maxlength="5000" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('catatan_pemohon') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-2 border-t border-gray-100 pt-5">
            <a href="{{ route('permohonan-pinjaman-karyawan.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Ajukan Permohonan</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nominal = document.getElementById('nominal');
    const tenor = document.getElementById('tenor_bulan');
    const output = document.getElementById('estimasiCicilan');
    const update = () => {
        const amount = Number(nominal.value) || 0;
        const months = Math.max(Number(tenor.value) || 1, 1);
        output.textContent = 'Rp ' + Math.ceil(amount / months).toLocaleString('id-ID');
    };
    nominal.addEventListener('input', update);
    tenor.addEventListener('input', update);
    update();
});
</script>
@endsection
