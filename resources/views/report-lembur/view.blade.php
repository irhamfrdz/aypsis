@extends('layouts.app')

@section('title', 'Report Lembur/Nginap')
@section('page_title', 'Report Lembur/Nginap')

@section('content')
<div class="container mx-auto px-4 py-6">
    <form action="{{ route('pranota-lembur.create') }}" method="GET" id="bulkForm">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center">
                    <i class="fas fa-bed mr-3 text-blue-600 text-2xl"></i>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Report Lembur/Nginap</h1>
                        <p class="text-xs text-gray-500 font-medium">Laporan driver lembur/nginap berdasarkan periode</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" id="btnProcess" class="hidden bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200 flex items-center shadow-md shadow-blue-100">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Masukan ke Pranota
                    </button>
                    <a href="{{ route('report.lembur.index') }}" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-50 transition-all duration-200 inline-flex items-center shadow-sm">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                        Ganti Periode
                    </a>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <form action="{{ route('report.lembur.view') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    
                    <div class="relative flex-1 min-w-[280px]">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Cari No SJ / Supir / Plat..." 
                            class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>

                    <div class="min-w-[180px]">
                        <select name="status_pranota" onchange="this.form.submit()" 
                            class="block w-full px-3 py-2 border border-gray-200 rounded-lg text-xs bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-gray-600 cursor-pointer">
                            <option value="">Semua Status Pranota</option>
                            <option value="belum" {{ request('status_pranota') == 'belum' ? 'selected' : '' }}>Belum Masuk Pranota</option>
                            <option value="sudah" {{ request('status_pranota') == 'sudah' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-gray-800 text-white px-5 py-2 rounded-lg text-xs font-bold hover:bg-gray-900 transition-all shadow-md shadow-gray-200">
                        Terapkan Filter
                    </button>
                    
                    @if(request('search') || request('status_pranota'))
                        <a href="{{ route('report.lembur.view', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
                           class="text-xs text-red-500 font-bold hover:text-red-700 underline px-2">
                           Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">
                                <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal Tanda Terima</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SJ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pranota</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratJalans as $sj)
                    <tr>
                        <td class="px-4 py-3">
                            @if(!$sj->sudah_pranota)
                                <input type="checkbox" name="selected_items[]" 
                                    value="{{ $sj->type_surat }}|{{ $sj->id }}" 
                                    data-type="{{ strtolower($sj->type_surat) }}"
                                    data-id="{{ $sj->id }}"
                                    data-no-sj="{{ $sj->no_surat_jalan }}"
                                    data-supir="{{ $sj->supir }}"
                                    data-plat="{{ $sj->no_plat }}"
                                    data-lembur="{{ $sj->lembur ? 1 : 0 }}"
                                    data-nginap="{{ $sj->nginap ? 1 : 0 }}"
                                    class="row-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $sj->report_date ? \Carbon\Carbon::parse($sj->report_date)->format('d/M/Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sj->type_surat == 'Muat' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $sj->type_surat }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $sj->no_surat_jalan }}</td>
                        <td class="px-4 py-3">{{ $sj->pengirim ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $sj->supir }}</td>
                        <td class="px-4 py-3">{{ $sj->no_plat }}</td>
                        <td class="px-4 py-3">
                            @if($sj->lembur) <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold mr-1">Lembur</span> @endif
                            @if($sj->nginap) <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">Nginap</span> @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($sj->sudah_pranota)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Sudah Pranota</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Belum Pranota</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Pagination Placehoder if needed later --}}
        </div>
    </div>
    </form>

    <!-- Create Pranota Modal -->
    <div id="confirmModal" class="fixed inset-0 z-[100] hidden overflow-hidden">
        {{-- Background Overlay --}}
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" id="modalOverlay"></div>

        {{-- Modal Content Container --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl flex flex-col max-h-[90vh]">
                <form action="{{ route('pranota-lembur.store') }}" method="POST" id="createPranotaForm" class="flex flex-col h-full overflow-hidden">
                    @csrf
                    {{-- Modal Header --}}
                    <div class="px-6 py-5 flex items-center gap-4 bg-white shrink-0">
                        <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-file-invoice text-orange-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Buat Pranota Lembur/Nginap</h3>
                        <button type="button" class="ml-auto text-gray-400 hover:text-gray-600 pr-2" id="closeModalIcon">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Modal Body - Scrollable --}}
                    <div class="px-8 pb-6 overflow-y-auto grow space-y-4">
                        <!-- Fields Section -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Nomor Pranota</label>
                                <input type="text" value="{{ $nomorPranotaDisplay }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 focus:outline-none" readonly>
                                <input type="hidden" name="nomor_cetakan" value="1">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Pranota <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_pranota" value="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Penyesuaian (Adjustment)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-xs font-medium">Rp</span>
                                        </div>
                                        <input type="number" name="adjustment" value="0" id="modalAdjustment" class="w-full pl-8 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Penyesuaian</label>
                                    <input type="text" name="alasan_adjustment" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Opsional...">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Catatan</label>
                                <textarea name="catatan" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan tambahan..."></textarea>
                            </div>
                        </div>

                        <!-- Table Section -->
                        <div class="mt-6">
                            <label class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-2">Driver Terpilih:</label>
                            <div class="border border-gray-100 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-100 text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-400 uppercase">NO</th>
                                            <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-400 uppercase">DRIVER / UNIT</th>
                                            <th class="px-3 py-2 text-center text-[10px] font-bold text-gray-400 uppercase">TYPE</th>
                                            <th class="px-3 py-2 text-right text-[10px] font-bold text-gray-400 uppercase">HARGA</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalItemsTable" class="divide-y divide-gray-50 bg-white">
                                        <!-- Populated by JS -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Summary below table -->
                            <div class="bg-gray-50/50 p-3 mt-0 border-x border-b border-gray-100 rounded-b-lg flex flex-col gap-1">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="font-bold text-gray-400 uppercase">Total Terpilih:</span>
                                    <span class="font-black text-gray-800" id="modalSelectedCount">0</span>
                                </div>
                                <div class="flex justify-between items-center text-[11px] border-t border-gray-100 pt-1 mt-1">
                                    <span class="font-bold text-gray-400 uppercase">Grand Total:</span>
                                    <span class="font-black text-orange-600 text-sm" id="modalGrandTotal">Rp 0</span>
                                </div>
                                <input type="hidden" id="modalSubtotalBeforeAdj">
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-8 py-5 flex items-center justify-center gap-3 shrink-0 bg-gray-50/30">
                        <button type="button" id="closeModal" class="min-w-[100px] px-6 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 font-bold text-xs hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="min-w-[120px] px-8 py-2 rounded-lg bg-orange-600 text-white font-bold text-xs hover:bg-orange-700 shadow-md transition-all">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const btnProcess = document.getElementById('btnProcess');
        const confirmModal = document.getElementById('confirmModal');
        const closeModal = document.getElementById('closeModal');
        const closeModalIcon = document.getElementById('closeModalIcon');
        const modalOverlay = document.getElementById('modalOverlay');
        const modalItemsTable = document.getElementById('modalItemsTable');
        const modalSelectedCount = document.getElementById('modalSelectedCount');
        const modalGrandTotal = document.getElementById('modalGrandTotal');
        const modalAdjustment = document.getElementById('modalAdjustment');
        
        // Pricelist from controller
        const pricelist = @json($pricelistLemburs);

        // Function to update button visibility
        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            if (checkedCount > 0) {
                btnProcess.classList.remove('hidden');
            } else {
                btnProcess.classList.add('hidden');
            }
            
            const allChecked = rowCheckboxes.length > 0 && Array.from(rowCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            
            checkAll.checked = allChecked;
            checkAll.indeterminate = someChecked && !allChecked;
        }

        // Open Modal handler
        btnProcess.addEventListener('click', function() {
            try {
                const selected = document.querySelectorAll('.row-checkbox:checked');
                modalItemsTable.innerHTML = '';
                modalSelectedCount.textContent = selected.length;
                
                // Defensive check for pricelist and names
                let lemburPrice = 75000;
                let nginapPrice = 150000;

                if (Array.isArray(pricelist)) {
                    const lPrice = pricelist.find(p => p.nama && typeof p.nama === 'string' && p.nama.toLowerCase() === 'lembur');
                    if (lPrice) lemburPrice = lPrice.nominal;
                    
                    const nPrice = pricelist.find(p => p.nama && typeof p.nama === 'string' && p.nama.toLowerCase() === 'nginap');
                    if (nPrice) nginapPrice = nPrice.nominal;
                }

                selected.forEach((cb, index) => {
                    const data = cb.dataset;
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 modal-item-row';
                    
                    const defaultLembur = data.lembur == 1 ? lemburPrice : 0;
                    const defaultNginap = data.nginap == 1 ? nginapPrice : 0;

                    row.innerHTML = `
                        <td class="px-3 py-2 text-gray-400 font-medium">${index + 1}</td>
                        <td class="px-3 py-2">
                            <div class="font-bold text-gray-700 leading-tight">${data.supir || '-'}</div>
                            <div class="text-[9px] text-gray-400 font-medium">${data.plat || '-'} / ${data.noSj || '-'}</div>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex flex-col gap-0.5 items-center">
                                ${data.lembur == 1 ? '<span class="bg-blue-50 text-blue-600 rounded px-1.5 py-0.5 text-[8px] font-black italic border border-blue-100">LEMBUR</span>' : ''}
                                ${data.nginap == 1 ? '<span class="bg-indigo-50 text-indigo-600 rounded px-1.5 py-0.5 text-[8px] font-black italic border border-indigo-100">NGINAP</span>' : ''}
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex flex-col gap-1 items-end">
                                ${data.lembur == 1 ? `
                                    <div class="flex items-center gap-1">
                                        <span class="text-[8px] text-gray-400 uppercase font-bold">L:</span>
                                        <input type="number" name="items[${index}][biaya_lembur]" value="${defaultLembur}" step="1000" 
                                            class="biaya-input modal-biaya-lembur text-right border-none p-0 focus:ring-0 w-20 text-xs font-bold text-gray-700 bg-transparent">
                                    </div>
                                ` : '<input type="hidden" name="items['+index+'][biaya_lembur]" value="0" class="modal-biaya-lembur">'}
                                
                                ${data.nginap == 1 ? `
                                    <div class="flex items-center gap-1">
                                        <span class="text-[8px] text-gray-400 uppercase font-bold">N:</span>
                                        <input type="number" name="items[${index}][biaya_nginap]" value="${defaultNginap}" step="1000" 
                                            class="biaya-input modal-biaya-nginap text-right border-none p-0 focus:ring-0 w-20 text-xs font-bold text-gray-700 bg-transparent">
                                    </div>
                                ` : '<input type="hidden" name="items['+index+'][biaya_nginap]" value="0" class="modal-biaya-nginap">'}
                                
                                <div class="text-[10px] font-black text-gray-900 border-t border-gray-100 pt-0.5 modal-row-total">Rp 0</div>
                            </div>
                        </td>
                        
                        <input type="hidden" name="items[${index}][type]" value="${data.type}">
                        <input type="hidden" name="items[${index}][id]" value="${data.id}">
                        <input type="hidden" name="items[${index}][supir]" value="${data.supir}">
                        <input type="hidden" name="items[${index}][no_plat]" value="${data.plat}">
                        <input type="hidden" name="items[${index}][is_lembur]" value="${data.lembur}">
                        <input type="hidden" name="items[${index}][is_nginap]" value="${data.nginap}">
                    `;
                    
                    modalItemsTable.appendChild(row);
                });

                confirmModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                calculateModalTotals();
            } catch (err) {
                console.error('Error:', err);
                alert('Terjadi kesalahan saat memuat data modal.');
            }
        });

        // Close functions
        const close = () => {
            confirmModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        };
        closeModal.addEventListener('click', close);
        closeModalIcon.addEventListener('click', close);
        modalOverlay.addEventListener('click', close);

        // Recalculate on input
        modalItemsTable.addEventListener('input', function(e) {
            if (e.target.classList.contains('biaya-input')) {
                calculateModalTotals();
            }
        });

        modalAdjustment.addEventListener('input', calculateModalTotals);

        function calculateModalTotals() {
            let subtotal = 0;
            const rows = document.querySelectorAll('.modal-item-row');
            
            rows.forEach(row => {
                const lembur = parseFloat(row.querySelector('.modal-biaya-lembur')?.value) || 0;
                const nginap = parseFloat(row.querySelector('.modal-biaya-nginap')?.value) || 0;
                const total = lembur + nginap;
                subtotal += total;
                row.querySelector('.modal-row-total').textContent = formatRupiah(total);
            });

            const adjustment = parseFloat(modalAdjustment.value) || 0;
            const grandTotal = subtotal + adjustment;

            modalGrandTotal.textContent = formatRupiah(grandTotal);
        }

        function formatRupiah(val) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
        }

        // Initial handlers
        checkAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
            updateButtonState();
        });

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonState);
        });
    });
</script>
@endpush
@endsection
