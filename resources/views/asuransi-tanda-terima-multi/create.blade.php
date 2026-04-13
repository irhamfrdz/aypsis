@extends('layouts.app')

@section('title', 'Input Batch Asuransi Baru')
@section('page_title', 'Form Input Batch Asuransi')

@section('content')
<div class="max-w-6xl mx-auto">
    <form id="insuranceForm" action="{{ route('asuransi-tanda-terima-multi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Polis Metadata -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100 sticky top-6">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-6 border-b pb-4">Informasi Polis</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-tighter mb-1">Nomor Polis</label>
                            <input type="text" name="nomor_polis" value="{{ old('nomor_polis') }}" 
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-tighter mb-1">Tanggal Polis <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_polis" value="{{ old('tanggal_polis', date('Y-m-d')) }}" required
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-tighter mb-1">Vendor Asuransi <span class="text-red-500">*</span></label>
                            <select name="vendor_asuransi_id" id="vendor_asuransi_id" required
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 transition-all">
                                <option value="">-- Pilih Vendor --</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" data-rate="{{ $vendor->tarif ?? 0 }}" {{ old('vendor_asuransi_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->nama_asuransi }} ({{ $vendor->tarif ?? 0 }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-tighter mb-1">Rate (%)</label>
                                <input type="number" step="0.00001" name="asuransi_rate" id="asuransi_rate" value="{{ old('asuransi_rate', 0) }}"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 transition-all bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-tighter mb-1">Biaya Admin</label>
                                <input type="number" name="biaya_admin" id="biaya_admin" value="{{ old('biaya_admin', 0) }}"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-tighter mb-1">Upload File Polis</label>
                            <div class="mt-1 flex justify-center px-4 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-400 transition-colors cursor-pointer group bg-gray-50 relative">
                                <input type="file" name="asuransi_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 group-hover:text-blue-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-xs text-gray-600">
                                        <span class="font-bold text-blue-600">Klik untuk upload</span>
                                    </div>
                                    <p class="text-[10px] text-gray-500">PDF, PNG, JPG up to 5MB</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-tighter mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 transition-all">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>

                    <!-- Summary Stats Card -->
                    <div class="mt-8 bg-gradient-to-br from-gray-900 to-indigo-900 rounded-2xl p-5 text-white shadow-xl">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center opacity-70">
                                <span class="text-xs uppercase tracking-widest font-bold">Total Nilai Barang</span>
                                <span class="text-xs font-mono" id="summary_total_np">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center opacity-70">
                                <span class="text-xs uppercase tracking-widest font-bold">Estimasi Premi</span>
                                <span class="text-xs font-mono" id="summary_premi">Rp 0</span>
                            </div>
                            <div class="border-t border-white/20 pt-3 flex justify-between items-end">
                                <span class="text-xs uppercase tracking-widest font-bold">Grand Total</span>
                                <span class="text-xl font-black font-mono text-blue-300" id="summary_grand_total">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full mt-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold uppercase tracking-widest shadow-lg hover:shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                        Simpan Batch Asuransi
                    </button>
                    <a href="{{ route('asuransi-tanda-terima-multi.index') }}" class="block text-center mt-3 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">
                        Kembali ke Daftar
                    </a>
                </div>
            </div>

            <!-- Right Column: Receipt Selection -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 min-h-[500px]">
                    <div class="p-6 border-b flex justify-between items-center bg-gray-50/50">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Pilih Tanda Terima</h3>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tighter">Hanya menampilkan data yang belum diasuransikan</p>
                        </div>
                        <div class="relative group">
                            <input type="text" id="receiptSearch" placeholder="Cari..." 
                                class="rounded-xl border-gray-300 py-1.5 px-4 text-xs w-48 transition-all focus:w-64 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="receiptsTable">
                            <thead class="bg-gray-100/30">
                                <tr>
                                    <th class="px-4 py-3 text-left w-10">
                                        <input type="checkbox" id="selectAll" class="rounded text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Details</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Barang</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">No. Kontainer</th>
                                    <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Qty</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Satuan</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest w-48">Nilai Pertanggungan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($receipts as $receipt)
                                    @php $key = $receipt->type . '_' . $receipt->id; @endphp
                                    <tr class="receipt-row hover:bg-blue-50/20 transition-colors duration-150">
                                        <td class="px-4 py-4 align-top">
                                            <input type="checkbox" name="selected_receipts[]" value="{{ $key }}" 
                                                class="receipt-checkbox rounded text-blue-600 focus:ring-blue-500"
                                                data-key="{{ $key }}">
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-900">{{ $receipt->number }}</span>
                                                <div class="flex gap-2 mt-1">
                                                    <span class="text-[9px] px-1.5 py-0.5 rounded-md bg-gray-100 text-gray-500 font-bold uppercase">{{ $receipt->type }}</span>
                                                    <span class="text-[9px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($receipt->date)->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="mt-1 text-[10px] text-gray-500 font-medium">
                                                    {{ Str::limit($receipt->pengirim, 20) }} → {{ Str::limit($receipt->penerima, 20) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 min-w-[150px]">
                                            <div class="text-[11px] text-gray-700 font-medium leading-relaxed">
                                                {{ $receipt->name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md inline-block">
                                                {{ $receipt->no_kontainer ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm font-bold text-gray-700">
                                            {{ number_format($receipt->qty, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4 text-left text-[10px] font-bold text-gray-500 uppercase">
                                            {{ $receipt->satuan ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-400 text-[10px] font-bold">Rp</span>
                                                <input type="number" name="nilai_pertanggungan[{{ $key }}]" 
                                                    class="np-input w-full rounded-lg border-gray-200 pl-7 py-1 text-xs focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:opacity-50"
                                                    placeholder="0" disabled>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-sm text-gray-400 italic">
                                            Tidak ada tanda terima baru yang tersedia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const vendorSelect = document.getElementById('vendor_asuransi_id');
        const rateInput = document.getElementById('asuransi_rate');
        const adminInput = document.getElementById('biaya_admin');
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.receipt-checkbox');
        const npInputs = document.querySelectorAll('.np-input');
        const searchInput = document.getElementById('receiptSearch');
        
        // Formatter
        const moneyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        });

        // Update rate when vendor changes
        vendorSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const rate = selectedOption.dataset.rate || 0;
            rateInput.value = rate;
            updateSummary();
        });

        rateInput.addEventListener('input', updateSummary);
        adminInput.addEventListener('input', updateSummary);

        // Checkbox logic
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                toggleInput(cb);
            });
            updateSummary();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                toggleInput(this);
                updateSummary();
            });
        });

        function toggleInput(checkbox) {
            const tr = checkbox.closest('tr');
            const input = tr.querySelector('.np-input');
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                input.value = '';
            }
        }

        // Real-time calculation
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('np-input')) {
                updateSummary();
            }
        });

        function updateSummary() {
            let totalNP = 0;
            document.querySelectorAll('.receipt-checkbox:checked').forEach(cb => {
                const tr = cb.closest('tr');
                const val = parseFloat(tr.querySelector('.np-input').value) || 0;
                totalNP += val;
            });

            const rate = parseFloat(rateInput.value) || 0;
            const admin = parseFloat(adminInput.value) || 0;
            const premi = totalNP * (rate / 100);
            const grandTotal = premi + admin;

            document.getElementById('summary_total_np').innerText = moneyFormatter.format(totalNP);
            document.getElementById('summary_premi').innerText = moneyFormatter.format(premi);
            document.getElementById('summary_grand_total').innerText = moneyFormatter.format(grandTotal);
        }

        // Search logic
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.receipt-row').forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    });
</script>
@endpush
@endsection
