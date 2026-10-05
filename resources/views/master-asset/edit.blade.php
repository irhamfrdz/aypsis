@extends('layouts.app')

@section('title', 'Edit Asset - ' . $asset->kode_asset)
@section('page_title', 'Edit Asset')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('asset.show', $asset->id) }}" class="p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">Edit Data Asset: {{ $asset->kode_asset }}</h1>
                <p class="text-xs text-gray-500">Perbarui spesifikasi, kondisi, atau penanggung jawab asset.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('asset.show', $asset->id) }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                Lihat Detail
            </a>
            <a href="{{ route('asset.index') }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                Daftar Asset
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <p class="font-semibold text-sm mb-1">Periksa kembali formulir Anda:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('asset.update', $asset->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Section 1: Informasi Utama -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">1</div>
                <h2 class="text-sm font-bold text-gray-800">Informasi Pokok Asset</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Asset <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_asset" value="{{ old('kode_asset', $asset->kode_asset) }}" required class="w-full px-3 py-2 text-xs font-mono border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Asset <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_asset" value="{{ old('nama_asset', $asset->nama_asset) }}" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori Asset <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat }}" {{ old('kategori', $asset->kategori) == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ old('status', $asset->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kondisi Fisik <span class="text-rose-500">*</span></label>
                    <select name="kondisi" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($kondisis as $kd)
                            <option value="{{ $kd }}" {{ old('kondisi', $asset->kondisi) == $kd ? 'selected' : '' }}>{{ $kd }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Informasi Pengadaan & Finansial -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">2</div>
                <h2 class="text-sm font-bold text-gray-800">Informasi Pengadaan & Penyusutan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Perolehan</label>
                    <input type="date" name="tanggal_perolehan" value="{{ old('tanggal_perolehan', $asset->tanggal_perolehan ? $asset->tanggal_perolehan->format('Y-m-d') : '') }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Masa Manfaat (Bulan)</label>
                    <input type="number" name="masa_manfaat_bulan" value="{{ old('masa_manfaat_bulan', $asset->masa_manfaat_bulan) }}" min="0" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nilai Residu (Rp)</label>
                    <input type="number" step="any" name="nilai_residu" value="{{ old('nilai_residu', $asset->nilai_residu) }}" min="0" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Vendor / Supplier</label>
                    <input type="text" name="vendor" value="{{ old('vendor', $asset->vendor) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">No. Faktur / Invoice / PO</label>
                    <input type="text" name="nomor_faktur" value="{{ old('nomor_faktur', $asset->nomor_faktur) }}" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Section 3: Dokumen & Keterangan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">3</div>
                <h2 class="text-sm font-bold text-gray-800">Dokumen & Keterangan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Foto Asset</label>
                    @if($asset->foto && file_exists(public_path($asset->foto)))
                        <div class="mb-2 flex items-center gap-2">
                            <img src="{{ asset($asset->foto) }}" alt="Foto saat ini" class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                            <span class="text-[11px] text-gray-500">Foto saat ini tersimpan. Unggah baru untuk mengganti.</span>
                        </div>
                    @endif
                    <input type="file" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-xl cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lampiran Dokumen</label>
                    @if($asset->lampiran && file_exists(public_path($asset->lampiran)))
                        <div class="mb-2 flex items-center gap-2">
                            <a href="{{ asset($asset->lampiran) }}" target="_blank" class="inline-flex items-center text-xs font-medium text-blue-600 hover:underline gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Lihat Dokumen Saat Ini
                            </a>
                        </div>
                    @endif
                    <input type="file" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-300 rounded-xl cursor-pointer">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan / Catatan Tambahan</label>
                    <textarea name="keterangan" rows="3" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan', $asset->keterangan) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('asset.index') }}" class="px-5 py-2.5 text-xs font-semibold rounded-xl text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150">
                Perbarui Data Asset
            </button>
        </div>
    </form>
</div>
@endsection
