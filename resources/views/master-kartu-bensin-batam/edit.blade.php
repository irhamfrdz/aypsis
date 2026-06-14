@extends('layouts.app')

@section('title', 'Edit Kartu Bensin Batam')
@section('page_title', 'Edit Kartu Bensin Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-4">
        <a href="{{ route('master-kartu-bensin-batam.index') }}" class="text-sm text-blue-600 hover:text-blue-900 flex items-center gap-2 font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Edit Detail Kartu Bensin</h3>
            <p class="text-xs text-gray-500 mt-1">Ubah rincian kartu bensin untuk cabang Batam.</p>
        </div>

        <form action="{{ route('master-kartu-bensin-batam.update', $item->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                    <span class="font-bold">Periksa inputan Anda:</span>
                </div>
                <ul class="list-disc list-inside text-sm pl-4">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nomor Kartu -->
                <div>
                    <label for="nomor_kartu" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Kartu <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_kartu" id="nomor_kartu" value="{{ old('nomor_kartu', $item->nomor_kartu) }}" required placeholder="Masukkan nomor kartu bensin"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Nama Kartu -->
                <div>
                    <label for="nama_kartu" class="block text-sm font-semibold text-gray-700 mb-2">Nama Kartu <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kartu" id="nama_kartu" value="{{ old('nama_kartu', $item->nama_kartu) }}" required placeholder="Contoh: Kartu Bensin Batam 01"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Provider -->
                <div>
                    <label for="provider" class="block text-sm font-semibold text-gray-700 mb-2">Provider / Penerbit <span class="text-red-500">*</span></label>
                    <input type="text" name="provider" id="provider" value="{{ old('provider', $item->provider) }}" required placeholder="Contoh: Pertamina, Flazz, dll."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="aktif" {{ old('status', $item->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ old('status', $item->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <!-- Mobil / Kendaraan -->
                <div>
                    <label for="mobil_id" class="block text-sm font-semibold text-gray-700 mb-2">Hubungkan Kendaraan (Opsional)</label>
                    <select name="mobil_id" id="mobil_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($mobils as $mobil)
                        <option value="{{ $mobil->id }}" {{ old('mobil_id', $item->mobil_id) == $mobil->id ? 'selected' : '' }}>
                            {{ $mobil->nomor_polisi }} @if($mobil->nickname) ({{ $mobil->nickname }}) @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Supir / Karyawan -->
                <div>
                    <label for="karyawan_id" class="block text-sm font-semibold text-gray-700 mb-2">Hubungkan Supir (Opsional)</label>
                    <select name="karyawan_id" id="karyawan_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $item->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                            {{ $karyawan->nama_lengkap }} @if($karyawan->nik) ({{ $karyawan->nik }}) @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / Catatan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" placeholder="Masukkan keterangan tambahan jika ada..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old('keterangan', $item->keterangan) }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <a href="{{ route('master-kartu-bensin-batam.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
