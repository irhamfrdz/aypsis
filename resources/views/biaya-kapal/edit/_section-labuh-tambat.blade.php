                <!-- Labuh Tambat (for Biaya Labuh Tambat) -->
                <div id="labuh_tambat_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Labuh Tambat <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_labuh_tambat_section_btn" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal/Voyage</span>
                        </button>
                    </div>
                    <div id="labuh_tambat_sections_container"></div>
                    
                    <button type="button" id="add_labuh_tambat_section_bottom_btn" class="mt-2 w-full py-2 border-2 border-dashed border-cyan-300 rounded-lg text-cyan-600 hover:bg-cyan-50 hover:border-cyan-400 transition flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kapal/Voyage Lainnya</span>
                    </button>

                    <!-- Adjustment & Final Total -->
                    <div class="mt-4 p-4 bg-slate-100 border-2 border-slate-300 rounded-lg space-y-3">
                        <div class="flex justify-between items-center text-md font-medium text-slate-700">
                            <label for="labuh_tambat_adjustment">Adjustment (Penyesuaian):</label>
                            <div class="relative w-1/3">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                <input type="text" id="labuh_tambat_adjustment" name="labuh_tambat_adjustment" 
                                    value="{{ number_format($biayaKapal->adjustment ?? 0, 0, ',', '.') }}"
                                    class="w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-500 text-right" 
                                    placeholder="0" oninput="this.value = this.value.replace(/[^-0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.'); calculateTotalFromAllLabuhTambatSections()">
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-300 flex justify-between items-center text-lg font-bold text-slate-800">
                            <span>Grand Total (Setelah Adjustment):</span>
                            <span id="labuh_tambat_all_sections_total">Rp 0</span>
                        </div>
                    </div>
                </div>
