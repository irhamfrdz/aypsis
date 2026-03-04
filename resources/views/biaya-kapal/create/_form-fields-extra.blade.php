
                <!-- Nominal -->
                <div id="nominal_wrapper" class="hidden">
                    <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">
                        Nominal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="nominal" 
                               name="nominal" 
                               value="{{ old('nominal') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nominal') border-red-500 @enderror"
                               placeholder="0"  
                               required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Masukkan nominal tanpa titik atau koma</p>
                    @error('nominal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PPH Dokumen (for Biaya Dokumen - 2% dari nominal) -->
                <div id="pph_dokumen_wrapper" class="hidden">
                    <label for="pph_dokumen" class="block text-sm font-medium text-gray-700 mb-2">
                        PPH (2%)
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="pph_dokumen" 
                               name="pph_dokumen" 
                               value="{{ old('pph_dokumen', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pph_dokumen') border-red-500 @enderror"
                               placeholder="0"
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-blue-600 font-medium">PPH = 2% × Nominal</p>
                    @error('pph_dokumen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grand Total Dokumen (for Biaya Dokumen - Nominal - PPH) -->
                <div id="grand_total_dokumen_wrapper" class="hidden">
                    <label for="grand_total_dokumen" class="block text-sm font-medium text-gray-700 mb-2">
                        Grand Total
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="grand_total_dokumen" 
                               name="grand_total_dokumen" 
                               value="{{ old('grand_total_dokumen', '') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-green-50 font-semibold cursor-not-allowed focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('grand_total_dokumen') border-red-500 @enderror"
                               placeholder="0"
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-green-600 font-medium">Grand Total = Nominal - PPH</p>
                    @error('grand_total_dokumen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biaya Materai (for Biaya Penumpukan) -->
                <div id="biaya_materai_wrapper" class="hidden">
                    <label for="biaya_materai" class="block text-sm font-medium text-gray-700 mb-2">
                        Biaya Materai
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="biaya_materai" 
                               name="biaya_materai" 
                               value="{{ old('biaya_materai', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('biaya_materai') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Biaya materai untuk dokumen</p>
                    @error('biaya_materai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DP (for Biaya Buruh) -->
                <div id="dp_wrapper" class="hidden">
                    <label for="dp" class="block text-sm font-medium text-gray-700 mb-2">
                        DP / Uang Muka
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="dp" 
                               name="dp" 
                               value="{{ old('dp', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dp') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Uang muka yang sudah dibayarkan</p>
                    @error('dp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sisa Pembayaran (for Biaya Buruh) -->
                <div id="sisa_pembayaran_wrapper" class="hidden">
                    <label for="sisa_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                        Sisa Pembayaran
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="sisa_pembayaran" 
                               name="sisa_pembayaran" 
                               value="{{ old('sisa_pembayaran', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed @error('sisa_pembayaran') border-red-500 @enderror"
                               placeholder="0" 
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-blue-600 font-medium">Sisa = Nominal - DP</p>
                    @error('sisa_pembayaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Penerima -->
                <div id="penerima_wrapper">
                    <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">
                        Penerima <span class="text-red-500">*</span>
                    </label>
                    <select id="penerima" 
                            name="penerima" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('penerima') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih atau ketik nama penerima --</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->nama_lengkap }}" {{ old('penerima') == $karyawan->nama_lengkap ? 'selected' : '' }}>
                                {{ $karyawan->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    @error('penerima')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PPN (for Biaya Penumpukan) -->
                <div id="ppn_wrapper" class="hidden">
                    <label for="ppn" class="block text-sm font-medium text-gray-700 mb-2">
                        PPN
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="ppn" 
                               name="ppn" 
                               value="{{ old('ppn', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ppn') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('ppn')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PPH (for Biaya Penumpukan) -->
                <div id="pph_wrapper" class="hidden">
                    <label for="pph" class="block text-sm font-medium text-gray-700 mb-2">
                        PPH
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="pph" 
                               name="pph" 
                               value="{{ old('pph', '0') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pph') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('pph')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Biaya (for Biaya Penumpukan) -->
                <div id="total_biaya_wrapper" class="hidden">
                    <label for="total_biaya" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Biaya <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="total_biaya" 
                               name="total_biaya" 
                               value="{{ old('total_biaya') }}"
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('total_biaya') border-red-500 @enderror"
                               placeholder="0"
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Total = Nominal + PPN - PPH</p>
                    @error('total_biaya')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Vendor -->
                <div id="nama_vendor_wrapper">
                    <label for="nama_vendor" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Vendor
                    </label>
                    <input type="text" 
                           id="nama_vendor" 
                           name="nama_vendor" 
                           value="{{ old('nama_vendor') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_vendor') border-red-500 @enderror"
                           placeholder="Masukkan nama vendor">
                    <p class="mt-1 text-xs text-gray-500">Nama perusahaan atau individu penerima pembayaran</p>
                    @error('nama_vendor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rekening -->
                <div id="nomor_rekening_wrapper">
                    <label for="nomor_rekening" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Rekening
                    </label>
                    <input type="text" 
                           id="nomor_rekening" 
                           name="nomor_rekening" 
                           value="{{ old('nomor_rekening') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nomor_rekening') border-red-500 @enderror"
                           placeholder="Contoh: 1234567890">
                    <p class="mt-1 text-xs text-gray-500">Nomor rekening bank penerima</p>
                    @error('nomor_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea id="keterangan" 
                              name="keterangan" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keterangan') border-red-500 @enderror"
                              placeholder="Masukkan keterangan atau catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Bukti -->
                <div class="md:col-span-2">
                    <label for="bukti" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Bukti
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="bukti" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PDF, PNG, JPG atau JPEG (Max. 2MB)</p>
                            </div>
                            <input id="bukti" 
                                   name="bukti" 
                                   type="file" 
                                   class="hidden" 
                                   accept=".pdf,.png,.jpg,.jpeg"
                                   onchange="updateFileName(this)">
                        </label>
                    </div>
                    <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
                    @error('bukti')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-blue-800">Informasi:</h4>
                        <ul class="mt-2 text-xs text-blue-700 list-disc list-inside space-y-1">
                            <li>Field yang bertanda <span class="text-red-500">*</span> wajib diisi</li>
                            <li><strong>Kapal, Voyage, dan BL mendukung multi-select</strong> - klik untuk menambahkan lebih dari satu</li>
                            <li>Gunakan tombol "Select All" untuk memilih semua atau "Clear Semua" untuk menghapus pilihan</li>
                            <li>Dropdown tetap terbuka untuk memudahkan memilih banyak item sekaligus</li>
                            <li>Nominal akan otomatis diformat dengan pemisah ribuan</li>
                            <li>Upload bukti bersifat opsional namun direkomendasikan untuk dokumentasi</li>
                            <li>Pastikan data yang diinput sudah benar sebelum menyimpan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('biaya-kapal.index') }}" 
                   class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
