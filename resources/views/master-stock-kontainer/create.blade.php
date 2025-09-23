@extends('layouts.app')

@section('title', 'Tambah Stock Kontainer')
@section('page_title', 'Tambah Stock Kontainer')

@section('content')
    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Tambah Stock Kontainer</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
            <ul>
                @foreach ($errors->all() as $error )
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('master.stock-kontainer.store') }}" method="POST">
            @csrf

            @php
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
                $selectClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700">Nomor Kontainer</label>
                    <input type="text" name="nomor_kontainer" id="nomor_kontainer" value="{{ old('nomor_kontainer') }}" class="{{ $inputClasses }}" required placeholder="Contoh: ABCU1234567">
                    @error('nomor_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ukuran" class="block text-sm font-medium text-gray-700">Ukuran</label>
                    <select name="ukuran" id="ukuran" class="{{ $selectClasses }}">
                        <option value="">Pilih Ukuran</option>
                        <option value="20ft" {{ old('ukuran') == '20ft' ? 'selected' : '' }}>20ft</option>
                        <option value="40ft" {{ old('ukuran') == '40ft' ? 'selected' : '' }}>40ft</option>
                        <option value="40ft HC" {{ old('ukuran') == '40ft HC' ? 'selected' : '' }}>40ft HC</option>
                        <option value="45ft" {{ old('ukuran') == '45ft' ? 'selected' : '' }}>45ft</option>
                    </select>
                    @error('ukuran')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700">Tipe Kontainer</label>
                    <select name="tipe_kontainer" id="tipe_kontainer" class="{{ $selectClasses }}">
                        <option value="">Pilih Tipe</option>
                        <option value="Dry Container" {{ old('tipe_kontainer') == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                        <option value="Reefer Container" {{ old('tipe_kontainer') == 'Reefer Container' ? 'selected' : '' }}>Reefer Container</option>
                        <option value="Open Top" {{ old('tipe_kontainer') == 'Open Top' ? 'selected' : '' }}>Open Top</option>
                        <option value="Flat Rack" {{ old('tipe_kontainer') == 'Flat Rack' ? 'selected' : '' }}>Flat Rack</option>
                        <option value="Tank Container" {{ old('tipe_kontainer') == 'Tank Container' ? 'selected' : '' }}>Tank Container</option>
                    </select>
                    @error('tipe_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="{{ $selectClasses }}" required>
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>Disewa</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="damaged" {{ old('status') == 'damaged' ? 'selected' : '' }}>Rusak</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kondisi" class="block text-sm font-medium text-gray-700">Kondisi</label>
                    <select name="kondisi" id="kondisi" class="{{ $selectClasses }}" required>
                        <option value="baik" {{ old('kondisi', 'baik') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak_ringan" {{ old('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak_berat" {{ old('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('kondisi')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi Penyimpanan</label>
                    <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}" class="{{ $inputClasses }}" placeholder="Contoh: Gudang A, Blok 1">
                    @error('lokasi')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="{{ $inputClasses }}">
                    @error('tanggal_masuk')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga_sewa_per_hari" class="block text-sm font-medium text-gray-700">Harga Sewa per Hari (Rp)</label>
                    <input type="number" name="harga_sewa_per_hari" id="harga_sewa_per_hari" value="{{ old('harga_sewa_per_hari') }}" class="{{ $inputClasses }}" min="0" step="0.01" placeholder="0.00">
                    @error('harga_sewa_per_hari')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga_sewa_per_bulan" class="block text-sm font-medium text-gray-700">Harga Sewa per Bulan (Rp)</label>
                    <input type="number" name="harga_sewa_per_bulan" id="harga_sewa_per_bulan" value="{{ old('harga_sewa_per_bulan') }}" class="{{ $inputClasses }}" min="0" step="0.01" placeholder="0.00">
                    @error('harga_sewa_per_bulan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pemilik" class="block text-sm font-medium text-gray-700">Pemilik</label>
                    <input type="text" name="pemilik" id="pemilik" value="{{ old('pemilik') }}" class="{{ $inputClasses }}" placeholder="Nama pemilik kontainer">
                    @error('pemilik')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nomor_seri" class="block text-sm font-medium text-gray-700">Nomor Seri</label>
                    <input type="text" name="nomor_seri" id="nomor_seri" value="{{ old('nomor_seri') }}" class="{{ $inputClasses }}" placeholder="Nomor seri kontainer">
                    @error('nomor_seri')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tahun_pembuatan" class="block text-sm font-medium text-gray-700">Tahun Pembuatan</label>
                    <input type="number" name="tahun_pembuatan" id="tahun_pembuatan" value="{{ old('tahun_pembuatan') }}" class="{{ $inputClasses }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                    @error('tahun_pembuatan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="{{ $inputClasses }}" placeholder="Keterangan tambahan tentang kontainer">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('master.stock-kontainer.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
