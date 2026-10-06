<!-- Klaim (for Biaya Klaim) -->
<div id="klaim_wrapper" class="md:col-span-2 hidden">
    <!-- Header Section Klaim -->
    <div class="mb-4 p-4 bg-gradient-to-r from-rose-50 via-pink-50 to-rose-50 border-2 border-rose-200 rounded-xl shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-rose-900 flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar text-rose-600"></i>
                    Rincian Klaim Kontainer (Multi-Kapal &amp; Multi-Voyage)
                </h3>
                <p class="text-xs text-rose-700 mt-0.5">
                    Pilih kapal &amp; voyage untuk memuat kontainer dari manifest, lalu tentukan biaya klaim perkontainer. Bisa menambahkan lebih dari satu kapal.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <!-- Bulk default nominal -->
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 border border-rose-200 rounded-lg shadow-sm">
                    <span class="text-xs font-semibold text-gray-600">Tarif Cepat:</span>
                    <div class="relative w-28">
                        <span class="absolute left-2 top-1 text-xs text-gray-400 font-bold">Rp</span>
                        <input type="text" id="klaim_global_default_nominal" class="w-full pl-7 pr-2 py-0.5 text-xs border border-gray-300 rounded font-semibold text-gray-800" placeholder="50.000" value="50.000">
                    </div>
                    <button type="button" id="klaim_apply_global_nominal_btn" class="text-xs px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded font-medium transition" title="Terapkan tarif ini ke semua kontainer terpilih di semua kapal">
                        Terapkan
                    </button>
                </div>

                <button type="button" id="add_klaim_section_btn" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs rounded-lg transition flex items-center gap-1.5 shadow-sm font-semibold">
                    <i class="fas fa-plus"></i>
                    <span>+ Tambah Kapal</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Container untuk daftar kapal-kapal -->
    <div id="klaim_sections_container" class="space-y-5"></div>
    
    <!-- Tombol tambah kapal di bawah -->
    <button type="button" id="add_klaim_section_bottom_btn" class="mt-4 w-full py-3 border-2 border-dashed border-rose-300 rounded-xl text-rose-600 hover:bg-rose-50/70 hover:border-rose-400 transition flex items-center justify-center gap-2 font-semibold text-sm shadow-sm">
        <i class="fas fa-plus-circle text-base"></i>
        <span>+ Tambah Kapal &amp; Voyage Lainnya</span>
    </button>

    <!-- Ringkasan Keseluruhan Transfer Klaim -->
    <div class="mt-5 p-4 bg-rose-50 border-2 border-rose-200 rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-sm">
        <div>
            <span class="text-xs font-bold uppercase text-rose-700 tracking-wider block">Ringkasan Total Klaim</span>
            <p class="text-sm text-gray-700 mt-0.5">
                Jumlah yang ditransfer: <strong id="klaim_summary_cont_count" class="text-rose-700">0</strong> cont
                <span id="klaim_summary_calc_text" class="text-gray-500 font-medium ml-1"></span>
            </p>
        </div>
        <div class="text-right">
            <span class="text-xs font-semibold text-gray-500 block">Total Ditransfer</span>
            <p id="klaim_summary_total_display" class="text-xl font-black text-rose-700">Rp 0</p>
        </div>
    </div>
</div>
