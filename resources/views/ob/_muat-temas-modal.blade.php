<div id="muatTemasModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50" role="dialog" aria-modal="true" aria-labelledby="muatTemasTitle">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-lg bg-white rounded-lg shadow-xl p-6">
            <h2 id="muatTemasTitle" class="text-lg font-semibold text-gray-900 mb-4">Form OB Muat Temas</h2>
            <p id="muatTemasKontainer" class="text-sm text-gray-600 mb-4"></p>
            <form id="muatTemasForm" class="space-y-4">
                <input type="hidden" id="muatTemasNaikKapalId">
                <div>
                    <label for="muatTemasTanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal OB <span class="text-red-500">*</span></label>
                    <input type="date" id="muatTemasTanggal" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="muatTemasSuratJalan" class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat Jalan <span class="text-red-500">*</span></label>
                    <input type="text" id="muatTemasSuratJalan" required class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Masukkan nomor surat jalan yang sudah terdaftar">
                </div>
                <div>
                    <label for="muatTemasStatus" class="block text-sm font-medium text-gray-700 mb-1">Status Kontainer <span class="text-red-500">*</span></label>
                    <select id="muatTemasStatus" required class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="filterMuatTemasPricelists()">
                        <option value="">--Pilih Status--</option>
                        <option value="full">Full</option>
                        <option value="empty">Empty</option>
                    </select>
                </div>
                <div>
                    <label for="muatTemasPricelist" class="block text-sm font-medium text-gray-700 mb-1">Pricelist OB Tujuan Temas <span class="text-red-500">*</span></label>
                    <select id="muatTemasPricelist" required class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="updateMuatTemasBiaya()">
                        <option value="">--Pilih Pricelist OB--</option>
                        @foreach($temasPricelists as $pricelist)
                            <option value="{{ $pricelist->id }}" data-size="{{ str_replace('ft', '', $pricelist->size_kontainer) }}" data-status="{{ $pricelist->status_kontainer ?? '' }}" data-biaya="{{ $pricelist->biaya }}">
                                {{ $pricelist->gudangTujuan->nama_gudang }} - {{ $pricelist->size_kontainer_label }} - {{ $pricelist->status_service_label }}{{ $pricelist->status_kontainer_label ? ' - '.$pricelist->status_kontainer_label : '' }} - {{ $pricelist->formatted_biaya }}
                            </option>
                        @endforeach
                    </select>
                    <p id="muatTemasPricelistStatus" class="text-xs text-gray-500 mt-1" role="status"></p>
                </div>
                <div>
                    <label for="muatTemasBiaya" class="block text-sm font-medium text-gray-700 mb-1">Biaya OB</label>
                    <input type="text" id="muatTemasBiaya" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" placeholder="Pilih pricelist untuk menampilkan biaya OB">
                    <p class="text-xs text-gray-500 mt-1">Biaya OB mengikuti pricelist yang dipilih.</p>
                </div>
                <p id="muatTemasError" class="hidden text-sm text-red-600" role="alert"></p>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" id="muatTemasCancel" onclick="closeMuatTemasModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-sm">Batal</button>
                    <button type="submit" id="muatTemasSubmit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-md text-sm">Simpan OB Muat Temas</button>
                </div>
            </form>
        </div>
    </div>
</div>
