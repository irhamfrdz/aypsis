@extends('layouts.app')

@section('title', 'Tambah Kontainer')
@section('page_title', 'Tambah Kontainer')

@section('content')
    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Tambah Kontainer</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
            <ul>
                @foreach ($errors->all() as $error )
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('master.kontainer.store') }}" method="POST">
            @csrf

            @php
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="awalan_kontainer" class="block text-sm font-medium text-gray-700">Awalan Kontainer (4 digit)</label>
                    <input type="text" name="awalan_kontainer" id="awalan_kontainer" value="{{ old('awalan_kontainer') }}" class="{{ $inputClasses }}" required maxlength="4">
                    @error('awalan_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomor_seri_kontainer" class="block text-sm font-medium text-gray-700">Nomor Seri Kontainer (6 digit)</label>
                    <input type="text" name="nomor_seri_kontainer" id="nomor_seri_kontainer" value="{{ old('nomor_seri_kontainer') }}" class="{{ $inputClasses }}" required maxlength="6">
                    @error('nomor_seri_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="akhiran_kontainer" class="block text-sm font-medium text-gray-700">Akhiran Kontainer (1 digit)</label>
                    <input type="text" name="akhiran_kontainer" id="akhiran_kontainer" value="{{ old('akhiran_kontainer') }}" class="{{ $inputClasses }}" required maxlength="1">
                    @error('akhiran_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ukuran" class="block text-sm font-medium text-gray-700">Ukuran</label>
                    <select name="ukuran" id="ukuran" class="{{ $inputClasses }}" required>
                        <option value="10" {{ old('ukuran') == '10' ? 'selected' : '' }}>10 ft</option>
                        <option value="20" {{ old('ukuran') == '20' ? 'selected' : '' }}>20 ft</option>
                        <option value="40" {{ old('ukuran') == '40' ? 'selected' : '' }}>40 ft</option>
                    </select>
                    @error('ukuran')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700">Tipe Kontainer</label>
                    <select name="tipe_kontainer" id="tipe_kontainer" class="{{ $inputClasses }}" required>
                        <option value="DRY" {{ old('tipe_kontainer') == 'DRY' ? 'selected' : '' }}>DRY</option>
                        <option value="High Cube" {{ old('tipe_kontainer') == 'High Cube' ? 'selected' : '' }}>High Cube</option>
                        <option value="Reefer" {{ old('tipe_kontainer') == 'Reefer' ? 'selected' : '' }}>Reefer</option>
                        <option value="Open Top" {{ old('tipe_kontainer') == 'Open Top' ? 'selected' : '' }}>Open Top</option>
                    </select>
                    @error('tipe_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="{{ $inputClasses }}">
                        <option value="Tersedia" {{ old('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Disewa" {{ old('status') == 'Disewa' ? 'selected' : '' }}>Disewa</option>
                        <option value="Perbaikan" {{ old('status') == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="Rusak" {{ old('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="Dijual" {{ old('status') == 'Dijual' ? 'selected' : '' }}>Dijual</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pemilik_kontainer" class="block text-sm font-medium text-gray-700">Pemilik Kontainer</label>
                    <input type="text" name="pemilik_kontainer" id="pemilik_kontainer" value="{{ old('pemilik_kontainer') }}" class="{{ $inputClasses }}">
                    @error('pemilik_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tahun_pembuatan" class="block text-sm font-medium text-gray-700">Tahun Pembuatan</label>
                    <input type="text" name="tahun_pembuatan" id="tahun_pembuatan" value="{{ old('tahun_pembuatan') }}" class="{{ $inputClasses }}" placeholder="YYYY" maxlength="4" pattern="\d{4}">
                    @error('tahun_pembuatan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="kontainer_asal" class="block text-sm font-medium text-gray-700">Asal Kontainer</label>
                    <input type="text" name="kontainer_asal" id="kontainer_asal" value="{{ old('kontainer_asal') }}" class="{{ $inputClasses }}">
                    @error('kontainer_asal')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_beli" class="block text-sm font-medium text-gray-700">Tanggal Beli</label>
                    <input type="date" name="tanggal_beli" id="tanggal_beli" value="{{ old('tanggal_beli') }}" class="{{ $inputClasses }}">
                    @error('tanggal_beli')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_jual" class="block text-sm font-medium text-gray-700">Tanggal Jual</label>
                    <input type="date" name="tanggal_jual" id="tanggal_jual" value="{{ old('tanggal_jual') }}" class="{{ $inputClasses }}">
                    @error('tanggal_jual')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="kondisi_kontainer" class="block text-sm font-medium text-gray-700">Kondisi Kontainer</label>
                    <input type="text" name="kondisi_kontainer" id="kondisi_kontainer" value="{{ old('kondisi_kontainer') }}" class="{{ $inputClasses }}">
                    @error('kondisi_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_masuk_sewa" class="block text-sm font-medium text-gray-700">Tanggal Masuk Sewa</label>
                    <input type="date" name="tanggal_masuk_sewa" id="tanggal_masuk_sewa" value="{{ old('tanggal_masuk_sewa') }}" class="{{ $inputClasses }}">
                    @error('tanggal_masuk_sewa')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_selesai_sewa" class="block text-sm font-medium text-gray-700">Tanggal Selesai Sewa</label>
                    <input type="date" name="tanggal_selesai_sewa" id="tanggal_selesai_sewa" value="{{ old('tanggal_selesai_sewa') }}" class="{{ $inputClasses }}">
                    @error('tanggal_selesai_sewa')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="{{ $inputClasses }}">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan1" class="block text-sm font-medium text-gray-700">Keterangan 1</label>
                    <textarea name="keterangan1" id="keterangan1" rows="3" class="{{ $inputClasses }}">{{ old('keterangan1') }}</textarea>
                    @error('keterangan1')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan2" class="block text-sm font-medium text-gray-700">Keterangan 2</label>
                    <textarea name="keterangan2" id="keterangan2" rows="3" class="{{ $inputClasses }}">{{ old('keterangan2') }}</textarea>
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
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection

