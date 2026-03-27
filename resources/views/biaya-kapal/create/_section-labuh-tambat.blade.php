                <!-- Detail Kapal Labuh Tambat (for Biaya Labuh Tambat) -->
                <div id="labuh_tambat_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & Labuh Tambat <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="add_labuh_tambat_section_btn" 
                            style="padding: 8px 16px; background-color: #0891b2; color: white; font-size: 0.875rem; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#0e7490'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
                            onmouseout="this.style.backgroundColor='#0891b2'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)';">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal</span>
                        </button>
                    </div>
                    <div id="labuh_tambat_sections_container"></div>

                    <!-- Adjustment & Final Total -->
                    <div class="mt-4 p-4 bg-slate-100 border-2 border-slate-300 rounded-lg space-y-3">
                        <div class="flex justify-between items-center text-md font-medium text-slate-700">
                            <label for="labuh_tambat_adjustment">Adjustment (Penyesuaian):</label>
                            <div class="relative w-1/3">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                <input type="text" id="labuh_tambat_adjustment" name="labuh_tambat_adjustment" 
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
