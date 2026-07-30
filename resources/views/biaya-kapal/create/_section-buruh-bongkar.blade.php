<div id="buruh_bongkar_wrapper" class="md:col-span-2 hidden">
    <div class="flex items-center justify-between mb-4">
        <label class="block text-sm font-medium text-gray-700">
            Detail Biaya Buruh Bongkar <span class="text-red-500">*</span>
        </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Nama Pengirim -->
        <div>
            <label for="buruh_bongkar_pengirim" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Pengirim <span class="text-red-500">*</span>
            </label>
            <select id="buruh_bongkar_pengirim" name="buruh_bongkar_pengirim" class="w-full" style="width: 100%;">
                <option value="">-- Pilih Pengirim --</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">Ketik untuk mencari pengirim dari manifest</p>
        </div>

        <!-- Start Date -->
        <div>
            <label for="buruh_bongkar_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal Mulai <span class="text-red-500">*</span>
            </label>
            <input type="date" id="buruh_bongkar_start_date" name="buruh_bongkar_start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <!-- End Date -->
        <div>
            <label for="buruh_bongkar_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal Akhir <span class="text-red-500">*</span>
            </label>
            <input type="date" id="buruh_bongkar_end_date" name="buruh_bongkar_end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>

    <div class="flex justify-end mb-6">
        <button type="button" id="btn_cari_buruh_bongkar" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition flex items-center gap-2">
            <i class="fas fa-search"></i>
            <span>Cari Data</span>
        </button>
    </div>

    <!-- Table Results -->
    <div id="buruh_bongkar_results_container" class="hidden">
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 w-10">
                            <input type="checkbox" id="buruh_bongkar_select_all" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th scope="col" class="px-6 py-3">Nomor Kontainer</th>
                        <th scope="col" class="px-6 py-3">Voyage</th>
                        <th scope="col" class="px-6 py-3">Surat Jalan Bongkaran</th>
                        <th scope="col" class="px-6 py-3">Tanggal Berangkat</th>
                    </tr>
                </thead>
                <tbody id="buruh_bongkar_table_body">
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
        <p class="mt-2 text-sm text-gray-600 font-medium">
            Total Kontainer Terpilih: <span id="buruh_bongkar_selected_count" class="text-blue-600 font-bold">0</span>
        </p>
    </div>
</div>
