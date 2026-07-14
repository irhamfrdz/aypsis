
                <!-- Biaya Perijinan - MULTI SECTION SYSTEM -->
                <div id="perijinan_wrapper" class="md:col-span-2 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                <i class="fas fa-file-contract text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">Detail Kapal & Biaya Perijinan</h3>
                                <p class="text-xs text-gray-500">Tambahkan detail biaya perijinan untuk setiap kapal</p>
                            </div>
                        </div>
                        <button type="button" id="add_perijinan_section_btn" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kapal (Manual)</span>
                        </button>
                    </div>

                    <!-- Global Date Filter for Auto-Generation -->
                    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal (Filter)</label>
                                <input type="date" id="global_perijinan_dari_tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal (Filter)</label>
                                <input type="date" id="global_perijinan_sampai_tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <button type="button" id="generate_perijinan_by_date_btn" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex justify-center items-center gap-2 shadow-sm">
                                    <i class="fas fa-magic"></i>
                                    <span>Generate Otomatis</span>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1 text-blue-500"></i> Sistem akan otomatis menarik semua kapal & voyage yang aktif pada rentang tanggal tersebut.</p>
                    </div>

                    <div id="perijinan_sections_container" class="space-y-4"></div>

                    <button type="button" id="add_perijinan_section_bottom_btn" class="mt-4 w-full py-3 border-2 border-dashed border-blue-200 rounded-xl text-blue-600 hover:bg-blue-50 hover:border-blue-300 transition flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kapal Lainnya</span>
                    </button>
                </div>
