@extends('layouts.app')

@section('title', 'Edit Kontainer')
@section('page_title', 'Edit Kontainer')

@section('content')
    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Edit Kontainer</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
            <div class="flex">
                <div>
                    <strong class="font-medium">Ada beberapa masalah dengan input Anda:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('master.kontainer.update', $kontainer->id) }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $inputClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="awalan_kontainer" class="block text-sm font-medium text-gray-700">Awalan Kontainer (4 karakter)</label>
                    <input type="text" name="awalan_kontainer" id="awalan_kontainer" value="{{ old('awalan_kontainer', $kontainer->awalan_kontainer) }}" maxlength="4" class="{{ $inputClasses }}" required>
                    @error('awalan_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomor_seri_kontainer" class="block text-sm font-medium text-gray-700">Nomor Seri (6 digit)</label>
                    <input type="text" name="nomor_seri_kontainer" id="nomor_seri_kontainer" value="{{ old('nomor_seri_kontainer', $kontainer->nomor_seri_kontainer) }}" maxlength="6" class="{{ $inputClasses }}" required>
                    @error('nomor_seri_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="akhiran_kontainer" class="block text-sm font-medium text-gray-700">Akhiran (1 karakter)</label>
                    <input type="text" name="akhiran_kontainer" id="akhiran_kontainer" value="{{ old('akhiran_kontainer', $kontainer->akhiran_kontainer) }}" maxlength="1" class="{{ $inputClasses }}" required>
                    @error('akhiran_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ukuran" class="block text-sm font-medium text-gray-700">Ukuran</label>
                    <select name="ukuran" id="ukuran" class="{{ $inputClasses }}" required>
                        <option value="20 Feet" {{ old('ukuran', $kontainer->ukuran) == '20 Feet' ? 'selected' : '' }}>20 Feet</option>
                        <option value="40 Feet" {{ old('ukuran', $kontainer->ukuran) == '40 Feet' ? 'selected' : '' }}>40 Feet</option>
                    </select>
                    @error('ukuran')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700">Tipe Kontainer</label>
                    <select name="tipe_kontainer" id="tipe_kontainer" class="{{ $inputClasses }}" required>
                        <option value="DRY" {{ old('tipe_kontainer', $kontainer->tipe_kontainer) == 'DRY' ? 'selected' : '' }}>DRY</option>
                        <option value="High Cube" {{ old('tipe_kontainer', $kontainer->tipe_kontainer) == 'High Cube' ? 'selected' : '' }}>High Cube</option>
                        <option value="Reefer" {{ old('tipe_kontainer', $kontainer->tipe_kontainer) == 'Reefer' ? 'selected' : '' }}>Reefer</option>
                        <option value="Open Top" {{ old('tipe_kontainer', $kontainer->tipe_kontainer) == 'Open Top' ? 'selected' : '' }}>Open Top</option>
                    </select>
                    @error('tipe_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="{{ $inputClasses }}">
                        <option value="Tersedia" {{ old('status', $kontainer->status) == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Disewa" {{ old('status', $kontainer->status) == 'Disewa' ? 'selected' : '' }}>Disewa</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor</label>
                    <select name="vendor" id="vendor" class="{{ $inputClasses }}">
                        <option value="">-- Pilih Vendor --</option>
                        <option value="ZONA" {{ old('vendor', $kontainer->vendor) == 'ZONA' ? 'selected' : '' }}>ZONA</option>
                        <option value="DPE" {{ old('vendor', $kontainer->vendor) == 'DPE' ? 'selected' : '' }}>DPE</option>
                    </select>
                    @error('vendor')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tahun_pembuatan" class="block text-sm font-medium text-gray-700">Tahun Pembuatan</label>
                    <input type="text" name="tahun_pembuatan" id="tahun_pembuatan" value="{{ old('tahun_pembuatan', $kontainer->tahun_pembuatan) }}" class="{{ $inputClasses }}" placeholder="YYYY" maxlength="4" pattern="\d{4}">
                    @error('tahun_pembuatan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="kontainer_asal" class="block text-sm font-medium text-gray-700">Asal Kontainer</label>
                    <input type="text" name="kontainer_asal" id="kontainer_asal" value="{{ old('kontainer_asal', $kontainer->kontainer_asal) }}" class="{{ $inputClasses }}">
                    @error('kontainer_asal')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_beli" class="block text-sm font-medium text-gray-700">Tanggal Beli</label>
                    <input type="text" name="tanggal_beli" id="tanggal_beli" value="{{ old('tanggal_beli', $kontainer->tanggal_beli?->format('d/M/Y')) }}" class="{{ $inputClasses }} datepicker" placeholder="dd/mmm/yyyy" autocomplete="off">
                    @error('tanggal_beli')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_jual" class="block text-sm font-medium text-gray-700">Tanggal Jual</label>
                    <input type="text" name="tanggal_jual" id="tanggal_jual" value="{{ old('tanggal_jual', $kontainer->tanggal_jual?->format('d/M/Y')) }}" class="{{ $inputClasses }} datepicker" placeholder="dd/mmm/yyyy" autocomplete="off">
                    @error('tanggal_jual')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="kondisi_kontainer" class="block text-sm font-medium text-gray-700">Kondisi Kontainer</label>
                    <input type="text" name="kondisi_kontainer" id="kondisi_kontainer" value="{{ old('kondisi_kontainer', $kontainer->kondisi_kontainer) }}" class="{{ $inputClasses }}">
                    @error('kondisi_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_masuk_sewa" class="block text-sm font-medium text-gray-700">Tanggal Masuk Sewa</label>
                    <input type="text" name="tanggal_masuk_sewa" id="tanggal_masuk_sewa" value="{{ old('tanggal_masuk_sewa', $kontainer->tanggal_masuk_sewa?->format('d/M/Y')) }}" class="{{ $inputClasses }} datepicker" placeholder="dd/mmm/yyyy" autocomplete="off">
                    @error('tanggal_masuk_sewa')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_selesai_sewa" class="block text-sm font-medium text-gray-700">Tanggal Selesai Sewa</label>
                    <input type="text" name="tanggal_selesai_sewa" id="tanggal_selesai_sewa" value="{{ old('tanggal_selesai_sewa', $kontainer->tanggal_selesai_sewa?->format('d/M/Y')) }}" class="{{ $inputClasses }} datepicker" placeholder="dd/mmm/yyyy" autocomplete="off">
                    @error('tanggal_selesai_sewa')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="{{ $inputClasses }}">{{ old('keterangan', $kontainer->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan1" class="block text-sm font-medium text-gray-700">Keterangan 1</label>
                    <textarea name="keterangan1" id="keterangan1" rows="3" class="{{ $inputClasses }}">{{ old('keterangan1', $kontainer->keterangan1) }}</textarea>
                    @error('keterangan1')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan2" class="block text-sm font-medium text-gray-700">Keterangan 2</label>
                    <textarea name="keterangan2" id="keterangan2" rows="3" class="{{ $inputClasses }}">{{ old('keterangan2', $kontainer->keterangan2) }}</textarea>
                    @error('keterangan2')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('master.kontainer.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-indigo-500 focus:ring-offset-2">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Perbarui
                </button>
            </div>
        </form>
    </div>

    {{-- Flatpickr CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr for all date inputs
            flatpickr('.datepicker', {
                dateFormat: 'd/M/Y',
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                    },
                    months: {
                        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                    }
                }
            });
        });
    </script>
@endsection
