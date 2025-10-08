@extends('layouts.app')

@section('title', 'Edit Stock Kontainer')
@section('page_title', 'Edit Stock Kontainer')

@section('content')
    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Edit Stock Kontainer</h2>

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
        <form action="{{ route('master.stock-kontainer.update', $stockKontainer) }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
                $selectClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nomor Kontainer - Split menjadi 3 field -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontainer</label>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label for="awalan_kontainer" class="block text-xs text-gray-500 mb-1">Awalan (4 karakter)</label>
                            <input type="text" name="awalan_kontainer" id="awalan_kontainer"
                                   value="{{ old('awalan_kontainer', $stockKontainer->awalan_kontainer) }}"
                                   class="{{ $inputClasses }}"
                                   required
                                   maxlength="4"
                                   placeholder="ABCD"
                                   style="text-transform: uppercase;">
                            @error('awalan_kontainer')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nomor_seri_kontainer" class="block text-xs text-gray-500 mb-1">Nomor Seri (6 digit)</label>
                            <input type="text" name="nomor_seri_kontainer" id="nomor_seri_kontainer"
                                   value="{{ old('nomor_seri_kontainer', $stockKontainer->nomor_seri_kontainer) }}"
                                   class="{{ $inputClasses }}"
                                   required
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   placeholder="123456">
                            @error('nomor_seri_kontainer')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="akhiran_kontainer" class="block text-xs text-gray-500 mb-1">Akhiran (1 karakter)</label>
                            <input type="text" name="akhiran_kontainer" id="akhiran_kontainer"
                                   value="{{ old('akhiran_kontainer', $stockKontainer->akhiran_kontainer) }}"
                                   class="{{ $inputClasses }}"
                                   required
                                   maxlength="1"
                                   pattern="[0-9A-Z]{1}"
                                   placeholder="7"
                                   style="text-transform: uppercase;">
                            @error('akhiran_kontainer')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @error('nomor_seri_gabungan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Format: 4 huruf + 6 angka + 1 huruf/angka (contoh: ABCD123456-7)</p>
                    <p class="mt-1 text-sm text-indigo-600 font-medium">Current: {{ $stockKontainer->nomor_kontainer }}</p>
                </div>

                <div>
                    <label for="ukuran" class="block text-sm font-medium text-gray-700">Ukuran</label>
                    <select name="ukuran" id="ukuran" class="{{ $selectClasses }}">
                        <option value="">Pilih Ukuran</option>
                        <option value="20ft" {{ old('ukuran', $stockKontainer->ukuran) == '20ft' ? 'selected' : '' }}>20ft</option>
                        <option value="40ft" {{ old('ukuran', $stockKontainer->ukuran) == '40ft' ? 'selected' : '' }}>40ft</option>
                        <option value="40ft HC" {{ old('ukuran', $stockKontainer->ukuran) == '40ft HC' ? 'selected' : '' }}>40ft HC</option>
                        <option value="45ft" {{ old('ukuran', $stockKontainer->ukuran) == '45ft' ? 'selected' : '' }}>45ft</option>
                    </select>
                    @error('ukuran')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700">Tipe Kontainer</label>
                    <select name="tipe_kontainer" id="tipe_kontainer" class="{{ $selectClasses }}">
                        <option value="">Pilih Tipe</option>
                        <option value="Dry Container" {{ old('tipe_kontainer', $stockKontainer->tipe_kontainer) == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                        <option value="Reefer Container" {{ old('tipe_kontainer', $stockKontainer->tipe_kontainer) == 'Reefer Container' ? 'selected' : '' }}>Reefer Container</option>
                        <option value="Open Top" {{ old('tipe_kontainer', $stockKontainer->tipe_kontainer) == 'Open Top' ? 'selected' : '' }}>Open Top</option>
                        <option value="Flat Rack" {{ old('tipe_kontainer', $stockKontainer->tipe_kontainer) == 'Flat Rack' ? 'selected' : '' }}>Flat Rack</option>
                        <option value="Tank Container" {{ old('tipe_kontainer', $stockKontainer->tipe_kontainer) == 'Tank Container' ? 'selected' : '' }}>Tank Container</option>
                    </select>
                    @error('tipe_kontainer')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="{{ $selectClasses }}" required>
                        <option value="available" {{ old('status', $stockKontainer->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="rented" {{ old('status', $stockKontainer->status) == 'rented' ? 'selected' : '' }}>Disewa</option>
                        <option value="maintenance" {{ old('status', $stockKontainer->status) == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="damaged" {{ old('status', $stockKontainer->status) == 'damaged' ? 'selected' : '' }}>Rusak</option>
                        <option value="inactive" {{ old('status', $stockKontainer->status) == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @if($stockKontainer->hasDuplicateInKontainers())
                        <p class="mt-1 text-xs text-yellow-600 bg-yellow-50 p-2 rounded">
                            ⚠️ Nomor kontainer ini sudah ada di master kontainer. Status akan otomatis diset "Non-Aktif" jika bukan inactive.
                        </p>
                    @endif
                </div>



                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk', $stockKontainer->tanggal_masuk?->format('Y-m-d')) }}" class="{{ $inputClasses }}">
                    @error('tanggal_masuk')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700">Tanggal Keluar</label>
                    <input type="date" name="tanggal_keluar" id="tanggal_keluar" value="{{ old('tanggal_keluar', $stockKontainer->tanggal_keluar?->format('Y-m-d')) }}" class="{{ $inputClasses }}">
                    @error('tanggal_keluar')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga_sewa_per_hari" class="block text-sm font-medium text-gray-700">Harga Sewa per Hari (Rp)</label>
                    <input type="number" name="harga_sewa_per_hari" id="harga_sewa_per_hari" value="{{ old('harga_sewa_per_hari', $stockKontainer->harga_sewa_per_hari) }}" class="{{ $inputClasses }}" min="0" step="0.01">
                    @error('harga_sewa_per_hari')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga_sewa_per_bulan" class="block text-sm font-medium text-gray-700">Harga Sewa per Bulan (Rp)</label>
                    <input type="number" name="harga_sewa_per_bulan" id="harga_sewa_per_bulan" value="{{ old('harga_sewa_per_bulan', $stockKontainer->harga_sewa_per_bulan) }}" class="{{ $inputClasses }}" min="0" step="0.01">
                    @error('harga_sewa_per_bulan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pemilik" class="block text-sm font-medium text-gray-700">Pemilik</label>
                    <input type="text" name="pemilik" id="pemilik" value="{{ old('pemilik', $stockKontainer->pemilik) }}" class="{{ $inputClasses }}">
                    @error('pemilik')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nomor_seri" class="block text-sm font-medium text-gray-700">Nomor Seri</label>
                    <input type="text" name="nomor_seri" id="nomor_seri" value="{{ old('nomor_seri', $stockKontainer->nomor_seri) }}" class="{{ $inputClasses }}">
                    @error('nomor_seri')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tahun_pembuatan" class="block text-sm font-medium text-gray-700">Tahun Pembuatan</label>
                    <input type="number" name="tahun_pembuatan" id="tahun_pembuatan" value="{{ old('tahun_pembuatan', $stockKontainer->tahun_pembuatan) }}" class="{{ $inputClasses }}" min="1900" max="{{ date('Y') + 1 }}">
                    @error('tahun_pembuatan')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="{{ $inputClasses }}">{{ old('keterangan', $stockKontainer->keterangan) }}</textarea>
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
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection
