                <!-- OPP/OPT (for Biaya OPP/OPT) - SIMILAR TO BURUH SYSTEM -->
                <div id="opp_opt_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Detail Kapal & Barang OPP/OPT <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center border border-gray-300 rounded-lg p-1 bg-white">
                                <input type="date" id="opp_opt_start_date" class="border-none focus:ring-0 text-sm py-1 px-2 text-gray-600 rounded-md">
                                <span class="text-gray-400 mx-1">-</span>
                                <input type="date" id="opp_opt_end_date" class="border-none focus:ring-0 text-sm py-1 px-2 text-gray-600 rounded-md">
                                <button type="button" id="fetch_opp_opt_by_date_btn" class="ml-2 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-md transition flex items-center gap-1" title="Tarik Kapal & Voyage">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                            <button type="button" id="add_opp_opt_section_btn" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                                <i class="fas fa-plus"></i>
                                <span>Tambah Kapal</span>
                            </button>
                        </div>
                    </div>
                    <div id="opp_opt_sections_container"></div>
                </div>
