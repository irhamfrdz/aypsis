@csrf

<div class="space-y-4">
    <!-- Barang -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Nama Barang <span class="text-red-500">*</span>
        </label>
        <input type="text" 
               name="barang" 
               value="{{ old('barang', $item->barang ?? '') }}" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('barang') border-red-500 @enderror"
               placeholder="Contoh: Bongkar Muat"
               required>
        @error('barang')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Size -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Size
        </label>
        <input type="text" 
               name="size" 
               value="{{ old('size', $item->size ?? '') }}" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size') border-red-500 @enderror"
               placeholder="Contoh: 20', 40', dll">
        @error('size')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tipe -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Tipe
        </label>
        <select name="tipe" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tipe') border-red-500 @enderror">
            <option value="">-- Pilih Tipe --</option>
            <option value="Full" {{ old('tipe', $item->tipe ?? '') == 'Full' ? 'selected' : '' }}>Full</option>
            <option value="Empty" {{ old('tipe', $item->tipe ?? '') == 'Empty' ? 'selected' : '' }}>Empty</option>
        </select>
        @error('tipe')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tarif -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Tarif <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
            <input type="text" 
                   name="tarif" 
                   id="tarif_input"
                   value="{{ old('tarif', isset($item) ? number_format($item->tarif, 0, ',', '.') : '') }}" 
                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tarif') border-red-500 @enderror"
                   placeholder="0"
                   required>
        </div>
        @error('tarif')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Keterangan -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Keterangan
        </label>
        <textarea name="keterangan" 
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keterangan') border-red-500 @enderror"
                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $item->keterangan ?? '') }}</textarea>
        @error('keterangan')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Aktif -->
    <div class="flex items-center">
        <input type="checkbox" 
               name="is_active" 
               id="is_active" 
               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
               {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
        <label for="is_active" class="ml-2 text-sm text-gray-700">
            Aktif
        </label>
    </div>

    <!-- Buttons -->
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" 
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Simpan
        </button>
        <a href="{{ route('master.pricelist-buruh.index') }}" 
           class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition font-medium">
            Batal
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tarifInput = document.getElementById('tarif_input');
    
    if (tarifInput) {
        // Format input saat mengetik
        tarifInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            e.target.value = value;
        });
        
        // Convert to plain number before submit
        tarifInput.closest('form').addEventListener('submit', function(e) {
            const plainValue = tarifInput.value.replace(/\./g, '');
            tarifInput.value = plainValue;
        });
    }
});
</script>
