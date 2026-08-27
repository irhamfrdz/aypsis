@csrf

<div class="space-y-4">
    <!-- Lokasi -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Lokasi <span class="text-red-500">*</span>
        </label>
        <select name="lokasi" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('lokasi') border-red-500 @enderror" required>
            <option value="">-- Pilih Lokasi --</option>
            <option value="Batam" {{ old('lokasi', $item->lokasi ?? '') == 'Batam' ? 'selected' : '' }}>Batam</option>
            <option value="Jakarta" {{ old('lokasi', $item->lokasi ?? '') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
        </select>
        @error('lokasi')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Size -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Size
        </label>
        <select name="size" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size') border-red-500 @enderror">
            <option value="">-- Pilih Size --</option>
            <option value="20ft" {{ old('size', $item->size ?? '') == '20ft' ? 'selected' : '' }}>20ft</option>
            <option value="40ft" {{ old('size', $item->size ?? '') == '40ft' ? 'selected' : '' }}>40ft</option>
        </select>
        @error('size')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nominal -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Nominal <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
            <input type="text" 
                   name="nominal" 
                   id="nominal_input"
                   value="{{ old('nominal', isset($item) ? number_format($item->nominal, 0, ',', '.') : '') }}" 
                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nominal') border-red-500 @enderror"
                   placeholder="0"
                   required>
        </div>
        @error('nominal')
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
               name="status" 
               id="status" 
               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
               {{ old('status', $item->status ?? true) ? 'checked' : '' }}>
        <label for="status" class="ml-2 text-sm text-gray-700">
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
        <a href="{{ route('master.pricelist-buruh-bongkar.index') }}" 
           class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition font-medium">
            Batal
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nominalInput = document.getElementById('nominal_input');
    
    if (nominalInput) {
        // Format input saat mengetik
        nominalInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            e.target.value = value;
        });
        
        // Convert to plain number before submit
        nominalInput.closest('form').addEventListener('submit', function(e) {
            const plainValue = nominalInput.value.replace(/\./g, '');
            nominalInput.value = plainValue;
        });
    }
});
</script>
