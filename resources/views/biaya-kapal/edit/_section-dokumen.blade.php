                <!-- Vendor (for Biaya Dokumen) -->
                <div id="vendor_wrapper" class="hidden">
                    <label for="vendor" class="block text-sm font-medium text-gray-700 mb-2">
                        Vendor <span class="text-red-500">*</span>
                    </label>
                    <select id="vendor" 
                            name="vendor_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($pricelistBiayaDokumen as $pricelist)
                            <option value="{{ $pricelist->id }}" 
                                    data-biaya="{{ $pricelist->biaya }}"
                                    {{ old('vendor_id') == $pricelist->id ? 'selected' : '' }}>
                                {{ $pricelist->nama_vendor }} - Rp {{ number_format($pricelist->biaya, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Pilih vendor untuk biaya dokumen</p>
                    @error('vendor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
