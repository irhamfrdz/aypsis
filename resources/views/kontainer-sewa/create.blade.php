                <!-- ...existing code... -->
@extends('layouts.app')

@section('title', 'Tambah Kontainer Sewa')
@section('page_title', 'Tambah Kontainer Sewa')

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-4">Formulir Kontainer Sewa Baru</h2>

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('kontainer-sewa.store') }}" method="POST">
        @csrf

        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontainer Sewa</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nomor Tagihan Kontainer (otomatis) -->
                <div>
                    <label for="nomor_tagihan" class="block text-sm font-medium text-gray-700">Nomor Tagihan Kontainer <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_tagihan" id="nomor_tagihan" value="{{ old('nomor_tagihan') }}" class="{{ $inputClasses }} bg-gray-200" maxlength="15" required readonly>
                    <small class="text-gray-500">Format: TK + nomor cetakan (1 digit) + tahun (2 digit) + bulan (2 digit) + running number (6 digit)</small>
                    @error('nomor_tagihan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Vendor -->
                <div>
                    <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor <span class="text-red-500">*</span></label>
                    <select name="vendor" id="vendor" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Vendor --</option>
                        <option value="ZONA" {{ old('vendor') == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                        <option value="DPE" {{ old('vendor') == 'DPE' ? 'selected' : '' }}>DPE</option>
                    </select>
                    @error('vendor')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Tarif -->
                <div>
                    <label for="tarif" class="block text-sm font-medium text-gray-700">Tarif <span class="text-red-500">*</span></label>
                    <select name="tarif" id="tarif" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Tarif --</option>
                        <option value="Bulanan" {{ old('tarif') == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="Harian" {{ old('tarif') == 'Harian' ? 'selected' : '' }}>Harian</option>
                    </select>
                    @error('tarif')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Ukuran Kontainer -->
                <div>
                    <label for="ukuran_kontainer" class="block text-sm font-medium text-gray-700">Ukuran Kontainer <span class="text-red-500">*</span></label>
                    <select name="ukuran_kontainer" id="ukuran_kontainer" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Ukuran --</option>
                        <option value="20" {{ old('ukuran_kontainer') == '20' ? 'selected' : '' }}>20 ft</option>
                        <option value="40" {{ old('ukuran_kontainer') == '40' ? 'selected' : '' }}>40 ft</option>
                    </select>
                    @error('ukuran_kontainer')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Nomor Kontainer (nullable) -->
                <div>
                    <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700">Nomor Kontainer</label>
                    <input type="text" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer') }}" class="{{ $inputClasses }}" autocomplete="off">
                    <small class="text-gray-500">Boleh dikosongkan jika belum ada nomor kontainer.</small>
                    @error('nomor_kontainer')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Jumlah Kontainer yang Akan Dipesan -->
                <div>
                    <label for="jumlah_kontainer" class="block text-sm font-medium text-gray-700">Jumlah Kontainer yang Akan Dipesan <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_kontainer" id="jumlah_kontainer" value="{{ old('jumlah_kontainer') }}" class="{{ $inputClasses }}" min="1" required>
                    @error('jumlah_kontainer')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Total Biaya Setelah Penyesuaian -->
                <div>
                    <label for="total_biaya" class="block text-sm font-medium text-gray-700">Total Biaya Setelah Penyesuaian <span class="text-red-500">*</span></label>
                    <input type="text" name="total_biaya" id="total_biaya" value="{{ old('total_biaya') }}" class="{{ $inputClasses }}" required autocomplete="off">
                    @error('total_biaya')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Penyesuaian -->
                <div>
                    <label for="penyesuaian" class="block text-sm font-medium text-gray-700">Penyesuaian</label>
                    <input type="text" name="penyesuaian" id="penyesuaian" value="{{ old('penyesuaian') }}" class="{{ $inputClasses }}" autocomplete="off">
                    @error('penyesuaian')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Alasan Penyesuaian -->
                <div class="md:col-span-2">
                    <label for="alasan_penyesuaian" class="block text-sm font-medium text-gray-700">Alasan Penyesuaian</label>
                    <textarea name="alasan_penyesuaian" id="alasan_penyesuaian" rows="2" class="{{ $inputClasses }}">{{ old('alasan_penyesuaian') }}</textarea>
                    @error('alasan_penyesuaian')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Lampiran File/Gambar -->
                <div class="md:col-span-2">
                    <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran File/Gambar</label>
                    <input type="file" name="lampiran" id="lampiran" class="{{ $inputClasses }}" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                    <small class="text-gray-500">Bisa upload gambar, PDF, Word, atau Excel.</small>
                    @error('lampiran')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Nomor Kontainer (nullable) -->
                <div>
                    <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700">Nomor Kontainer</label>
                    <input type="text" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer') }}" class="{{ $inputClasses }}" autocomplete="off">
                    <small class="text-gray-500">Boleh dikosongkan jika belum ada nomor kontainer.</small>
                    @error('nomor_kontainer')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Jumlah Kontainer yang Akan Dipesan -->
                <div>
                    <label for="jumlah_kontainer" class="block text-sm font-medium text-gray-700">Jumlah Kontainer yang Akan Dipesan <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_kontainer" id="jumlah_kontainer" value="{{ old('jumlah_kontainer') }}" class="{{ $inputClasses }}" min="1" required>
                    @error('jumlah_kontainer')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Nomor Tagihan Kontainer (otomatis) -->
                <div>
                    <label for="nomor_tagihan" class="block text-sm font-medium text-gray-700">Nomor Tagihan Kontainer <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_tagihan" id="nomor_tagihan" value="{{ old('nomor_tagihan') }}" class="{{ $inputClasses }} bg-gray-200" maxlength="15" required readonly>
                    <small class="text-gray-500">Format: TK + nomor cetakan (1 digit) + tahun (2 digit) + bulan (2 digit) + running number (6 digit)</small>
                    @error('nomor_tagihan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Nomor cetakan default 1, bisa diganti jika ada input lain
                    var nomorCetakan = 1;
                    var now = new Date();
                    var tahun = now.getFullYear().toString().slice(-2);
                    var bulan = (now.getMonth()+1).toString().padStart(2, '0');
                    // Running number bisa didapat dari backend, sementara random 6 digit
                    var running = Math.floor(100000 + Math.random() * 900000);
                    var nomorTagihan = `TK${nomorCetakan}${tahun}${bulan}${running}`;
                    document.getElementById('nomor_tagihan').value = nomorTagihan;
                });
                </script>
                <!-- Vendor -->
                <div>
                    <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor <span class="text-red-500">*</span></label>
                    <select name="vendor" id="vendor" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Vendor --</option>
                        <option value="ZONA" {{ old('vendor') == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                        <option value="DPE" {{ old('vendor') == 'DPE' ? 'selected' : '' }}>DPE</option>
                    </select>
                    @error('vendor')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Tarif -->
                <div>
                    <label for="tarif" class="block text-sm font-medium text-gray-700">Tarif <span class="text-red-500">*</span></label>
                    <select name="tarif" id="tarif" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Tarif --</option>
                        <option value="Bulanan" {{ old('tarif') == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="Harian" {{ old('tarif') == 'Harian' ? 'selected' : '' }}>Harian</option>
                    </select>
                    @error('tarif')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Ukuran Kontainer -->
                <div>
                    <label for="ukuran_kontainer" class="block text-sm font-medium text-gray-700">Ukuran Kontainer <span class="text-red-500">*</span></label>
                    <select name="ukuran_kontainer" id="ukuran_kontainer" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Ukuran --</option>
                        <option value="20" {{ old('ukuran_kontainer') == '20' ? 'selected' : '' }}>20 ft</option>
                        <option value="40" {{ old('ukuran_kontainer') == '40' ? 'selected' : '' }}>40 ft</option>
                    </select>
                    @error('ukuran_kontainer')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="{{ $inputClasses }}">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>
        <div class="flex justify-end space-x-4">
            <a href="{{ route('kontainer-sewa.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
