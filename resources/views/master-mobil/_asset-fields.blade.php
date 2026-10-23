<fieldset class="mb-6">
    <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Asset</legend>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="tanggal_tanda_terima_asset" class="block text-sm font-medium text-gray-700">Tanggal Tanda Terima Asset</label>
            <input type="date" name="tanggal_tanda_terima_asset" id="tanggal_tanda_terima_asset" value="{{ old('tanggal_tanda_terima_asset', ($mobil ?? null)?->tanggal_tanda_terima_asset?->format('Y-m-d')) }}" class="{{ $inputClasses }}">
            @error('tanggal_tanda_terima_asset')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="kategori_asset" class="block text-sm font-medium text-gray-700">Kategori Asset</label>
            <select name="kategori_asset" id="kategori_asset" class="{{ $inputClasses }}">
                <option value="">-- Pilih Kategori Asset --</option>
                @foreach(['Elektronik', 'Kendaraan', 'Alat Berat'] as $kategori)
                    <option value="{{ $kategori }}" @selected(old('kategori_asset', ($mobil ?? null)?->kategori_asset) === $kategori)>{{ $kategori }}</option>
                @endforeach
            </select>
            @error('kategori_asset')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="status_operasional" class="block text-sm font-medium text-gray-700">Status Operasional</label>
            <select name="status_operasional" id="status_operasional" class="{{ $inputClasses }}">
                <option value="">-- Pilih Status Operasional --</option>
                @foreach(['Tersedia', 'Digunakan', 'Dalam Pemeliharaan', 'Dipinjam', 'Dijual'] as $status)
                    <option value="{{ $status }}" @selected(old('status_operasional', ($mobil ?? null)?->status_operasional) === $status)>{{ $status }}</option>
                @endforeach
            </select>
            @error('status_operasional')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="kondisi_fisik" class="block text-sm font-medium text-gray-700">Kondisi Fisik</label>
            <select name="kondisi_fisik" id="kondisi_fisik" class="{{ $inputClasses }}">
                <option value="">-- Pilih Kondisi Fisik --</option>
                @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat', 'Afkir'] as $kondisi)
                    <option value="{{ $kondisi }}" @selected(old('kondisi_fisik', ($mobil ?? null)?->kondisi_fisik) === $kondisi)>{{ $kondisi }}</option>
                @endforeach
            </select>
            @error('kondisi_fisik')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</fieldset>
