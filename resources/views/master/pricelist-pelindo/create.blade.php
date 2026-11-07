@extends('layouts.app')

@section('title', 'Tambah Pricelist Pelindo')

@section('content')
<style>
    .pelindo-form-control {
        border: 1px solid #94a3b8 !important;
        background-color: #fff;
    }

    .pelindo-form-control:focus {
        border-color: #9333ea !important;
    }

    .pelindo-form-control.is-invalid {
        border-color: #ef4444 !important;
    }

    .pelindo-form-section + .pelindo-form-section {
        margin-top: 2.25rem;
    }
</style>
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6">
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('master.pricelist-pelindo.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-purple-700"><i class="fas fa-arrow-left" aria-hidden="true"></i> Kembali ke daftar</a>
        <div class="mb-7 mt-5 flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-100 text-purple-700"><i class="fas fa-tags" aria-hidden="true"></i></div>
            <div><h1 class="text-2xl font-bold text-slate-900">Tambah Pricelist Pelindo</h1><p class="mt-1 text-sm text-slate-500">Isi detail kegiatan, kontainer, dan tarif untuk membuat pricelist baru.</p></div>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">Periksa kembali kolom yang ditandai di bawah.</div>
        @endif

        <form action="{{ route('master.pricelist-pelindo.store') }}" method="POST" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf
            <div class="space-y-9 p-5 sm:p-8">
                <section class="pelindo-form-section" aria-labelledby="section-kegiatan">
                    <div class="mb-5 border-b border-slate-100 pb-4"><h2 id="section-kegiatan" class="font-semibold text-slate-900">Informasi kegiatan</h2><p class="mt-1 text-sm text-slate-500">Nama kegiatan yang akan ditampilkan pada pricelist.</p></div>
                    <label for="kegiatan" class="block text-sm font-medium text-slate-700">Kegiatan <span class="text-red-600">*</span></label>
                    <input type="text" name="kegiatan" id="kegiatan" value="{{ old('kegiatan') }}" required placeholder="Contoh: Bongkar muat kontainer" class="pelindo-form-control mt-2 block w-full rounded-lg px-4 py-3 text-sm shadow-sm focus:ring-purple-500 @error('kegiatan') is-invalid @enderror">
                    @error('kegiatan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <div class="mt-5">
                        <label for="lokasi" class="block text-sm font-medium text-slate-700">Lokasi <span class="text-red-600">*</span></label>
                        <select name="lokasi" id="lokasi" required class="pelindo-form-control mt-2 block w-full rounded-lg px-4 py-3 text-sm shadow-sm focus:ring-purple-500 @error('lokasi') is-invalid @enderror">
                            <option value="">Pilih lokasi</option>
                            <option value="Jakarta" {{ old('lokasi') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                            <option value="Batam" {{ old('lokasi') == 'Batam' ? 'selected' : '' }}>Batam</option>
                            <option value="Tanjung Pinang" {{ old('lokasi') == 'Tanjung Pinang' ? 'selected' : '' }}>Tanjung Pinang</option>
                        </select>
                        @error('lokasi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </section>

                <section class="pelindo-form-section" aria-labelledby="section-tarif">
                    <div class="mb-5 border-b border-slate-100 pb-4"><h2 id="section-tarif" class="font-semibold text-slate-900">Detail layanan & tarif</h2><p class="mt-1 text-sm text-slate-500">Ukuran hanya diperlukan untuk kontainer Empty atau Full.</p></div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div id="ukuran-container" @if(in_array(old('status_kontainer'), ['cargo', 'barang_berbahaya', 'lpg', 'besi_beton', 'mobil', 'alat_berat'], true)) hidden @endif>
                            <label for="ukuran" class="block text-sm font-medium text-slate-700">Ukuran kontainer</label>
                            <select name="ukuran" id="ukuran" class="pelindo-form-control mt-2 block w-full rounded-lg px-4 py-3 text-sm shadow-sm focus:ring-purple-500 @error('ukuran') is-invalid @enderror">
                                <option value="">Pilih ukuran kontainer</option>
                                <option value="20" {{ old('ukuran') == '20' ? 'selected' : '' }}>20 ft</option>
                                <option value="40" {{ old('ukuran') == '40' ? 'selected' : '' }}>40 ft</option>
                            </select>
                            @error('ukuran') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="status_kontainer" class="block text-sm font-medium text-slate-700">Status kontainer</label>
                            <select name="status_kontainer" id="status_kontainer" class="pelindo-form-control mt-2 block w-full rounded-lg px-4 py-3 text-sm shadow-sm focus:ring-purple-500 @error('status_kontainer') is-invalid @enderror">
                                <option value="">Pilih status kontainer</option>
                                <option value="empty" {{ old('status_kontainer') == 'empty' ? 'selected' : '' }}>Empty</option>
                                <option value="full" {{ old('status_kontainer') == 'full' ? 'selected' : '' }}>Full</option>
                                <option value="cargo" {{ old('status_kontainer') == 'cargo' ? 'selected' : '' }}>Cargo</option>
                                <option value="barang_berbahaya" {{ old('status_kontainer') == 'barang_berbahaya' ? 'selected' : '' }}>Barang Berbahaya</option>
                                <option value="lpg" {{ old('status_kontainer') == 'lpg' ? 'selected' : '' }}>LPG</option>
                                <option value="besi_beton" {{ old('status_kontainer') == 'besi_beton' ? 'selected' : '' }}>Besi Beton</option>
                                <option value="mobil" {{ old('status_kontainer') == 'mobil' ? 'selected' : '' }}>Mobil</option>
                                <option value="alat_berat" {{ old('status_kontainer') == 'alat_berat' ? 'selected' : '' }}>Alat Berat</option>
                            </select>
                            @error('status_kontainer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="tarif" class="block text-sm font-medium text-slate-700">Tarif <span class="text-red-600">*</span></label>
                            <div class="relative mt-2"><span class="pointer-events-none absolute inset-y-0 left-0 flex items-center border-r border-slate-300 px-4 text-sm font-semibold text-slate-600">Rp</span><input type="text" name="tarif" id="tarif" value="{{ old('tarif') }}" inputmode="numeric" required placeholder="0" class="pelindo-form-control block w-full rounded-lg py-3 pl-16 pr-4 text-sm shadow-sm focus:ring-purple-500 @error('tarif') is-invalid @enderror"></div>
                            <p class="mt-1 text-xs text-slate-500">Masukkan nominal dalam rupiah.</p>
                            @error('tarif') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="pelindo-form-section" aria-labelledby="section-tambahan">
                    <div class="mb-5 border-b border-slate-100 pb-4"><h2 id="section-tambahan" class="font-semibold text-slate-900">Detail tambahan</h2><p class="mt-1 text-sm text-slate-500">Tambahkan catatan dan atur ketersediaan pricelist.</p></div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-slate-700">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="4" placeholder="Catatan tambahan jika diperlukan" class="pelindo-form-control mt-2 block w-full rounded-lg px-4 py-3 text-sm shadow-sm focus:ring-purple-500 @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700">Status <span class="text-red-600">*</span></label>
                            <select name="status" id="status" required class="pelindo-form-control mt-2 block w-full rounded-lg px-4 py-3 text-sm shadow-sm focus:ring-purple-500 @error('status') is-invalid @enderror">
                                <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Pricelist baru akan aktif secara otomatis.</p>
                            @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-5 py-5 sm:flex-row sm:justify-end sm:px-8">
                <a href="{{ route('master.pricelist-pelindo.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"><i class="fas fa-save" aria-hidden="true"></i> Simpan pricelist</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tarifInput = document.getElementById('tarif');
        const statusKontainer = document.getElementById('status_kontainer');
        const ukuranContainer = document.getElementById('ukuran-container');
        const ukuranInput = document.getElementById('ukuran');

        function toggleUkuran() {
            const tanpaUkuran = ['cargo', 'barang_berbahaya', 'lpg', 'besi_beton', 'mobil', 'alat_berat'].includes(statusKontainer.value);
            ukuranContainer.hidden = tanpaUkuran;
            ukuranInput.disabled = tanpaUkuran;
            if (tanpaUkuran) ukuranInput.value = '';
        }

        toggleUkuran();
        statusKontainer.addEventListener('change', toggleUkuran);

        function formatTarif() {
            const digits = tarifInput.value.replace(/[^0-9]/g, '');
            tarifInput.value = digits ? new Intl.NumberFormat('id-ID').format(Number(digits)) : '';
        }

        formatTarif();
        tarifInput.addEventListener('input', formatTarif);
    });
</script>
@endsection
