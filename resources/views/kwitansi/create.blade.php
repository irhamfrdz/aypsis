@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kwitansi / Akuntansi Baru</h2>
        <a href="{{ route('kwitansi.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kwitansi.store') }}" method="POST" id="kwitansi-form">
        @csrf

        {{-- Header Section --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Kolom 1 -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
                        <div class="flex gap-2">
                            <input type="text" name="pelanggan_kode" class="w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Kode">
                            <input type="text" name="pelanggan_nama" class="w-2/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Nama Pelanggan">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terima Dari</label>
                        <textarea name="terima_dari" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kirim Ke</label>
                        <textarea name="kirim_ke" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                </div>

                <!-- Kolom 2 -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kwt No.</label>
                        <input type="text" name="kwt_no" value="{{ $kwtNo }}" readonly class="w-full bg-gray-100 rounded-md border-gray-300 shadow-sm text-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tgl. Inv.</label>
                        <input type="date" name="tgl_inv" value="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. PO</label>
                        <input type="text" name="no_po" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tgl. Kirim</label>
                        <input type="date" name="tgl_kirim" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                <!-- Kolom 3 -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">FOB</label>
                        <input type="text" name="fob" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Syarat Pembayaran</label>
                        <input type="text" name="syarat_pembayaran" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                        <input type="text" name="pengirim" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penjual</label>
                        <input type="text" name="penjual" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
            </div>
        </div>

        {{-- Detail Item Section --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Item Detail</h3>
                <button type="button" id="btn-add-item" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-md hover:bg-indigo-100 text-sm font-medium transition-colors border border-indigo-200">
                    <i class="fas fa-plus mr-1"></i> Tambah Item
                </button>
            </div>
            
            <div class="overflow-x-auto border border-gray-200 rounded-md">
                <table class="min-w-full divide-y divide-gray-200" id="detail-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Item (Kode & Deskripsi)</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Qty</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Unit Price</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Amount</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. B/L</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. S/J</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dept.</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyek</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S/N</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="detail-body">
                        <!-- Baris pertama (default) -->
                        <tr class="detail-row">
                            <td class="px-2 py-2">
                                <div class="flex flex-col gap-1">
                                    <input type="text" name="details[0][item_kode]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" placeholder="Kode Item">
                                    <input type="text" name="details[0][item_description]" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" placeholder="Deskripsi Item">
                                </div>
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" name="details[0][qty]" min="0" step="1" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs text-right qty-input">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="details[0][unit_price]" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs text-right price-input number-format">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="details[0][amount]" value="0" readonly class="w-full bg-gray-50 rounded-md border-gray-300 shadow-sm text-xs text-right amount-input">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="details[0][no_bl]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="details[0][no_sj]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="details[0][dept]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="details[0][proyek]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="details[0][sn]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                            </td>
                            <td class="px-2 py-2 text-center">
                                <button type="button" class="text-red-500 hover:text-red-700 btn-remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer/Summary Section --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Akun Piutang</label>
                    <input type="text" name="akun_piutang" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700 w-1/3">Sub Total</label>
                        <input type="text" name="sub_total" id="sub_total" value="0" readonly class="w-2/3 bg-gray-50 rounded-md border-gray-300 shadow-sm text-sm text-right font-medium">
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700 w-1/3">Discount</label>
                        <div class="w-2/3 flex gap-2">
                            <div class="relative w-1/3">
                                <input type="number" name="discount_persen" id="discount_persen" value="0" min="0" max="100" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-right pr-6">
                                <span class="absolute right-2 top-2 text-gray-500 text-sm">%</span>
                            </div>
                            <input type="text" name="discount_nominal" id="discount_nominal" value="0" class="w-2/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-right number-format">
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700 w-1/3">Biaya Kirim</label>
                        <input type="text" name="biaya_kirim" id="biaya_kirim" value="0" class="w-2/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-right number-format">
                    </div>
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <label class="text-base font-bold text-gray-900 w-1/3">Total Invoice</label>
                        <input type="text" name="total_invoice" id="total_invoice" value="0" readonly class="w-2/3 bg-indigo-50 rounded-md border-indigo-300 text-indigo-700 shadow-sm text-base font-bold text-right">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="reset" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                Reset
            </button>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium shadow-sm flex items-center">
                <i class="fas fa-save mr-2"></i> Simpan Kwitansi
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = 1;
        
        // Format number to currency
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Remove formatting to get pure number
        function parseNumber(str) {
            return parseFloat(str.toString().replace(/,/g, '')) || 0;
        }

        // Auto format numbers on input
        function initNumberInputs() {
            document.querySelectorAll('.number-format').forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^0-9.]/g, '');
                    if (value !== '') {
                        let parts = value.split('.');
                        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        this.value = parts.join('.');
                    }
                    calculateAll();
                });
            });
            
            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('input', calculateAll);
            });
        }
        
        // Calculate amount per row and total
        function calculateAll() {
            let subTotal = 0;
            
            // Calculate per row
            document.querySelectorAll('.detail-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseNumber(row.querySelector('.price-input').value);
                
                const amount = qty * price;
                row.querySelector('.amount-input').value = formatNumber(amount);
                
                subTotal += amount;
            });
            
            // Update Sub Total
            document.getElementById('sub_total').value = formatNumber(subTotal);
            
            // Calculate Discount
            const discPersen = parseFloat(document.getElementById('discount_persen').value) || 0;
            let discNominal = parseNumber(document.getElementById('discount_nominal').value);
            
            // Priority to percent if both provided? Normally you'd sync them, but here we just subtract nominal.
            // If they input percent, update nominal
            if (document.activeElement.id === 'discount_persen') {
                discNominal = (discPersen / 100) * subTotal;
                document.getElementById('discount_nominal').value = formatNumber(discNominal);
            } else if (document.activeElement.id === 'discount_nominal' && subTotal > 0) {
                // If they input nominal, update percent
                const pct = (discNominal / subTotal) * 100;
                document.getElementById('discount_persen').value = pct.toFixed(2);
            }
            
            const biayaKirim = parseNumber(document.getElementById('biaya_kirim').value);
            
            // Total Invoice
            const totalInvoice = subTotal - discNominal + biayaKirim;
            document.getElementById('total_invoice').value = formatNumber(totalInvoice);
        }
        
        // Add row
        document.getElementById('btn-add-item').addEventListener('click', function() {
            const index = rowCount++;
            const newRow = `
                <tr class="detail-row">
                    <td class="px-2 py-2">
                        <div class="flex flex-col gap-1">
                            <input type="text" name="details[${index}][item_kode]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" placeholder="Kode Item">
                            <input type="text" name="details[${index}][item_description]" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" placeholder="Deskripsi Item">
                        </div>
                    </td>
                    <td class="px-2 py-2">
                        <input type="number" name="details[${index}][qty]" min="0" step="1" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs text-right qty-input">
                    </td>
                    <td class="px-2 py-2">
                        <input type="text" name="details[${index}][unit_price]" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs text-right price-input number-format">
                    </td>
                    <td class="px-2 py-2">
                        <input type="text" name="details[${index}][amount]" value="0" readonly class="w-full bg-gray-50 rounded-md border-gray-300 shadow-sm text-xs text-right amount-input">
                    </td>
                    <td class="px-2 py-2">
                        <input type="text" name="details[${index}][no_bl]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                    </td>
                    <td class="px-2 py-2">
                        <input type="text" name="details[${index}][no_sj]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                    </td>
                    <td class="px-2 py-2">
                        <input type="text" name="details[${index}][dept]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                    </td>
                    <td class="px-2 py-2">
                        <input type="text" name="details[${index}][proyek]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                    </td>
                    <td class="px-2 py-2">
                        <input type="text" name="details[${index}][sn]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                    </td>
                    <td class="px-2 py-2 text-center">
                        <button type="button" class="text-red-500 hover:text-red-700 btn-remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            document.getElementById('detail-body').insertAdjacentHTML('beforeend', newRow);
            initNumberInputs();
        });
        
        // Remove row
        document.getElementById('detail-body').addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-item')) {
                if (document.querySelectorAll('.detail-row').length > 1) {
                    e.target.closest('.detail-row').remove();
                    calculateAll();
                } else {
                    alert('Minimal harus ada 1 item.');
                }
            }
        });
        
        // Listeners for discount and shipping inputs
        document.getElementById('discount_persen').addEventListener('input', calculateAll);
        document.getElementById('discount_nominal').addEventListener('input', calculateAll);
        document.getElementById('biaya_kirim').addEventListener('input', calculateAll);
        
        // Initial setup
        initNumberInputs();
        
        // Remove formatting before form submit
        document.getElementById('kwitansi-form').addEventListener('submit', function() {
            document.querySelectorAll('.number-format, .amount-input, #sub_total, #total_invoice').forEach(input => {
                input.value = parseNumber(input.value);
            });
        });
    });
</script>
@endpush
@endsection
