@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Edit Surat Jalan Batam</h1>
                <p class="text-xs text-gray-600 mt-1">Perbarui surat jalan Batam: {{ $suratJalan->no_surat_jalan }}</p>
            </div>
            <a href="{{ route('surat-jalan-batam.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('surat-jalan-batam.update', $suratJalan->id) }}" method="POST" class="p-4">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- No SJ & Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="text" name="no_surat_jalan" value="{{ old('no_surat_jalan', $suratJalan->no_surat_jalan) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-600 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_surat_jalan" value="{{ old('tanggal_surat_jalan', $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('Y-m-d') : '') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Section: Detail Pengiriman -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Detail Pengiriman</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                    <input type="text" name="pengirim" value="{{ old('pengirim', $suratJalan->pengirim) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <input type="text" name="jenis_barang" value="{{ old('jenis_barang', $suratJalan->jenis_barang) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <input type="text" name="tujuan_pengambilan" value="{{ old('tujuan_pengambilan', $suratJalan->tujuan_pengambilan) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text" name="tujuan_pengiriman" value="{{ old('tujuan_pengiriman', $suratJalan->tujuan_pengiriman) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <!-- Section: Transport -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Informasi Transportasi</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat Kendaraan</label>
                    <input type="text" name="no_plat" value="{{ old('no_plat', $suratJalan->no_plat) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                    <select name="supir" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih Supir</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->nama_panggilan ?: $k->nama_lengkap }}" {{ old('supir', $suratJalan->supir) == ($k->nama_panggilan ?: $k->nama_lengkap) ? 'selected' : '' }}>
                                {{ $k->nama_panggilan ?: $k->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir 2 (Opsional)</label>
                    <input type="text" name="supir2" value="{{ old('supir2', $suratJalan->supir2) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <input type="text" name="kenek" value="{{ old('kenek', $suratJalan->kenek) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <!-- Section: Kontainer -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Informasi Kontainer</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kontainer</label>
                    <input type="text" name="tipe_kontainer" value="{{ old('tipe_kontainer', $suratJalan->tipe_kontainer) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    <input type="text" name="no_kontainer" value="{{ old('no_kontainer', $suratJalan->no_kontainer) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="draft" {{ old('status', $suratJalan->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $suratJalan->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $suratJalan->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $suratJalan->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <a href="{{ route('surat-jalan-batam.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-600">Batal</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection
