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
                <!-- Nomor Kontainer - Split menjadi 3 field -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontainer</label>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label for="awalan_kontainer" class="block text-xs text-gray-500 mb-1">Awalan (4 karakter)</label>
                            <input type="text" name="awalan_kontainer" id="awalan_kontainer"
                                   value="{{ old('awalan_kontainer') }}"
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
                                   value="{{ old('nomor_seri_kontainer') }}"
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
                                   value="{{ old('akhiran_kontainer') }}"
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
                </div>

                <div>
                    <label for="ukuran" class="block text-sm font-medium text-gray-700">Ukuran</label>
                    <select name="ukuran" id="ukuran" class="{{ $selectClasses }}">
                        <option value="">Pilih Ukuran</option>
                        <option value="20ft" {{ old('ukuran') == '20ft' ? 'selected' : '' }}>20ft</option>
                        <option value="40ft" {{ old('ukuran') == '40ft' ? 'selected' : '' }}>40ft</option>
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
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Status akan otomatis diset "Non-Aktif" jika nomor kontainer sudah ada di master kontainer</p>
                </div>





                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="{{ $inputClasses }}">
                    @error('tanggal_masuk')
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const awalanInput = document.getElementById('awalan_kontainer');
        const nomorSeriInput = document.getElementById('nomor_seri_kontainer');
        const akhiranInput = document.getElementById('akhiran_kontainer');

        // Auto uppercase untuk awalan dan akhiran
        awalanInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '');
            updatePreview();
        });

        akhiranInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            updatePreview();
        });

        // Only numbers untuk nomor seri
        nomorSeriInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            updatePreview();
        });

        // Function untuk update preview
        function updatePreview() {
            const awalan = awalanInput.value;
            const nomorSeri = nomorSeriInput.value;
            const akhiran = akhiranInput.value;

            if (awalan && nomorSeri && akhiran) {
                const fullNumber = awalan + nomorSeri + akhiran;

                // Update preview di bawah field
                let previewElement = document.getElementById('nomor-preview');
                if (!previewElement) {
                    previewElement = document.createElement('p');
                    previewElement.id = 'nomor-preview';
                    previewElement.className = 'mt-2 text-sm text-indigo-600 font-medium';
                    document.querySelector('label[class*="mb-2"]').parentNode.appendChild(previewElement);
                }
                previewElement.textContent = 'Preview: ' + fullNumber;
            }
        }

        // Validasi form sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const awalan = awalanInput.value;
            const nomorSeri = nomorSeriInput.value;
            const akhiran = akhiranInput.value;

            if (awalan.length !== 4) {
                e.preventDefault();
                alert('Awalan kontainer harus 4 karakter');
                awalanInput.focus();
                return;
            }

            if (nomorSeri.length !== 6) {
                e.preventDefault();
                alert('Nomor seri kontainer harus 6 digit');
                nomorSeriInput.focus();
                return;
            }

            if (akhiran.length !== 1) {
                e.preventDefault();
                alert('Akhiran kontainer harus 1 karakter');
                akhiranInput.focus();
                return;
            }
        });
    });
    </script>
@endsection
