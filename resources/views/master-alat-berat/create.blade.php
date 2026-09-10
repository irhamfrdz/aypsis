@extends('layouts.app')

@section('title', 'Tambah Alat Berat')
@section('page_title', 'Tambah Alat Berat')

@section('content')
@php
    $inputClasses = 'mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500';
    $labelClasses = 'block text-sm font-medium text-gray-700';
@endphp
<div class="mx-auto max-w-6xl space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-indigo-700 to-indigo-500 px-6 py-6 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between sm:px-8">
        <div class="flex items-center gap-4"><div class="rounded-xl bg-white/15 p-3"><i class="fa-solid fa-truck-monster text-2xl"></i></div><div><p class="text-sm text-indigo-100">Master Data</p><h2 class="text-2xl font-bold">Tambah Alat Berat</h2><p class="mt-1 text-sm text-indigo-100">Lengkapi identitas, sertifikat, dan informasi operasional alat.</p></div></div>
        <a href="{{ route('master.alat-berat.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/30 hover:bg-white/20"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('master.alat-berat.store') }}" method="POST" class="alat-berat-form space-y-6">
        @csrf
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8"><div class="mb-6 border-b border-gray-100 pb-4"><h3 class="font-semibold text-gray-900">Identitas Alat</h3><p class="mt-1 text-sm text-gray-500">Informasi dasar alat berat yang didaftarkan.</p></div><div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <!-- Kode Alat -->
            <div>
                <label for="kode_alat" class="{{ $labelClasses }}">Kode Alat <span class="text-red-500">*</span></label>
                <input type="text" name="kode_alat" id="kode_alat" value="{{ old('kode_alat', $nextKode) }}" class="{{ $inputClasses }} bg-gray-100" readonly>
            </div>

            <!-- Nama -->
            <div>
                <label for="nama" class="{{ $labelClasses }}">Nama Alat <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" class="{{ $inputClasses }}" placeholder="Contoh: Excavator PC200" required>
            </div>

            <!-- Nickname -->
            <div>
                <label for="nickname" class="block text-sm font-medium text-gray-700">Nickname</label>
                <input type="text" name="nickname" id="nickname" value="{{ old('nickname') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: EX-01">
            </div>

            <!-- Warna -->
            <div>
                <label for="warna" class="block text-sm font-medium text-gray-700">Warna</label>
                <input type="text" name="warna" id="warna" value="{{ old('warna') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Kuning, Merah">
            </div>

            <!-- Jenis -->
            <div>
                <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis</label>
                <input type="text" name="jenis" id="jenis" value="{{ old('jenis') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Excavator, Forklift">
            </div>

            <!-- Merk -->
            <div>
                <label for="merk" class="block text-sm font-medium text-gray-700">Merek</label>
                <input type="text" name="merk" id="merk" value="{{ old('merk') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Komatsu, Caterpillar">
            </div>

            <!-- Tipe -->
            <div>
                <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe/Model</label>
                <input type="text" name="tipe" id="tipe" value="{{ old('tipe') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: PC200-8">
            </div>

            <!-- Kapasitas -->
            <div>
                <label for="kapasitas" class="block text-sm font-medium text-gray-700">Kapasitas</label>
                <input type="text" name="kapasitas" id="kapasitas" value="{{ old('kapasitas') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: 20 Ton, 50 Ton">
            </div>

            <!-- Nomor Seri -->
            <div>
                <label for="nomor_seri" class="block text-sm font-medium text-gray-700">Nomor Seri / Rangka</label>
                <input type="text" name="nomor_seri" id="nomor_seri" value="{{ old('nomor_seri') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="md:col-span-2 mt-2 rounded-xl border border-indigo-100 bg-indigo-50/50 p-4"><h3 class="font-semibold text-gray-900"><i class="fa-solid fa-certificate mr-2 text-indigo-600"></i>Sertifikat SIA</h3><p class="mt-1 text-sm text-gray-500">Informasi masa berlaku sertifikat alat.</p></div>
            <!-- Sertifikat SIA -->
            <div>
                <label for="nomor_sertifikat_sia" class="block text-sm font-medium text-gray-700">Nomor Sertifikat SIA</label>
                <input type="text" name="nomor_sertifikat_sia" id="nomor_sertifikat_sia" value="{{ old('nomor_sertifikat_sia') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Nomor sertifikat SIA">
            </div>

            <div>
                <label for="tanggal_terbit_sertifikat_sia" class="block text-sm font-medium text-gray-700">Tanggal Terbit Sertifikat SIA</label>
                <input type="date" name="tanggal_terbit_sertifikat_sia" id="tanggal_terbit_sertifikat_sia" value="{{ old('tanggal_terbit_sertifikat_sia') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="tanggal_kadaluarsa_sertifikat_sia" class="block text-sm font-medium text-gray-700">Tanggal Kadaluarsa Sertifikat SIA</label>
                <input type="date" name="tanggal_kadaluarsa_sertifikat_sia" id="tanggal_kadaluarsa_sertifikat_sia" value="{{ old('tanggal_kadaluarsa_sertifikat_sia') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="biaya_sertifikat_sia" class="block text-sm font-medium text-gray-700">Biaya Sertifikat SIA <span class="text-xs font-normal text-gray-500">(periode berlaku)</span></label>
                <div class="relative mt-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500">Rp</span>
                    <input type="number" step="0.01" min="0" name="biaya_sertifikat_sia" id="biaya_sertifikat_sia" value="{{ old('biaya_sertifikat_sia') }}" class="mt-1 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0">
                </div>
                <p class="mt-1 text-xs text-gray-500">Nominal ini berlaku untuk periode tanggal terbit sampai tanggal kadaluarsa.</p>
            </div>



            <div class="md:col-span-2 mt-2 border-t border-gray-100 pt-5"><h3 class="font-semibold text-gray-900">Operasional</h3><p class="mt-1 text-sm text-gray-500">Atur lokasi, tarif, dan status alat.</p></div>
            <!-- Lokasi -->
            <div>
                <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi Saat Ini</label>
                <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Gudang A, Site B">
            </div>

            <!-- Tarif Harian -->
            <div>
                <label for="tarif_harian" class="block text-sm font-medium text-gray-700">Tarif Harian</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" step="0.01" name="tarif_harian" id="tarif_harian" value="{{ old('tarif_harian') }}" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0.00">
                </div>
            </div>

            <!-- Tarif Bulanan -->
            <div>
                <label for="tarif_bulanan" class="block text-sm font-medium text-gray-700">Tarif Bulanan</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" step="0.01" name="tarif_bulanan" id="tarif_bulanan" value="{{ old('tarif_bulanan') }}" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0.00">
                </div>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('master.alat-berat.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .alat-berat-form input:not([type="hidden"]),
    .alat-berat-form select,
    .alat-berat-form textarea {
        min-height: 44px;
        border: 1px solid #d7dee9;
        border-radius: 0.75rem;
        background-color: #f8fafc;
        color: #1f2937;
        font-size: 0.875rem;
        line-height: 1.25rem;
        transition: border-color 150ms ease, box-shadow 150ms ease, background-color 150ms ease;
    }
    .alat-berat-form input:not([type="hidden"]),
    .alat-berat-form select { padding: 0.65rem 0.85rem; }
    .alat-berat-form input.pl-10 { padding-left: 2.5rem; }
    .alat-berat-form textarea { min-height: 104px; padding: 0.75rem 0.85rem; resize: vertical; }
    .alat-berat-form input:not([type="hidden"]):hover,
    .alat-berat-form select:hover,
    .alat-berat-form textarea:hover { border-color: #a5b4fc; background-color: #fff; }
    .alat-berat-form input:not([type="hidden"]):focus,
    .alat-berat-form select:focus,
    .alat-berat-form textarea:focus { border-color: #6366f1; background-color: #fff; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.14); outline: none; }
    .alat-berat-form input[readonly] { cursor: not-allowed; border-color: #e5e7eb; background-color: #f1f5f9; color: #64748b; }
    .alat-berat-form input::placeholder,
    .alat-berat-form textarea::placeholder { color: #94a3b8; }
</style>
@endpush
