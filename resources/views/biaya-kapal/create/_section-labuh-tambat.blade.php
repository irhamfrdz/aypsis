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
                </div>
