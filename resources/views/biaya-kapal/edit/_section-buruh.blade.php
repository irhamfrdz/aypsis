                <!-- Barang (for Biaya Buruh) - NEW MULTI KAPAL SYSTEM -->
                <div id="barang_wrapper" class="md:col-span-2 hidden">
                    
                    <!-- LOKASI SELECTOR -->
                    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Kerja</label>
                        <select name="lokasi_buruh" id="lokasi_buruh_select" class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                            <option value="jakarta">Jakarta (Detail Barang & Tenaga Kerja)</option>
                            <option value="batam">Batam (Kontainer/BL & Nominal Manual)</option>
                        </select>
                        <input type="hidden" name="lokasi" id="input_lokasi_hidden" value="jakarta">
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & Barang <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_kapal_section_btn" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="kapal_sections_container"></div>
                </div>
