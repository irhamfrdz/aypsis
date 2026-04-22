@extends('layouts.app')

@section('title', 'Edit Pembayaran Pranota Stock')
@section('page_title', 'Edit Pembayaran Pranota Stock')

@push('styles')
    <style>
        .searchable-dropdown-items {
            max-height: 200px;
            overflow-y: auto;
            z-index: 50;
        }
        .searchable-dropdown-item {
            transition: background-color 0.2s;
        }
        .searchable-dropdown-item:hover {
            background-color: #f3f4f6;
        }
    </style>
@endpush

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        @if(session('success'))
            <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        @endif

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-stock.update', $item->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Data Pembayaran -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold mb-3 border-b pb-2">Data Pembayaran</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="{{ $labelClasses }}">Nomor Pembayaran <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_pembayaran" id="nomor_pembayaran" value="{{ $item->nomor_pembayaran }}" class="{{ $readonlyInputClasses }}" readonly required>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Nomor Accurate</label>
                            <input type="text" name="nomor_accurate" value="{{ $item->nomor_accurate }}" class="{{ $inputClasses }}" placeholder="Input Accurate No">
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Tanggal Kas <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_pembayaran" value="{{ $item->tanggal_pembayaran->format('Y-m-d') }}" class="{{ $inputClasses }}" required>
                        </div>
                    </div>
                </div>

                <!-- Bank & Accounting -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold mb-3 border-b pb-2">Bank & Transaksi (Double Book)</h4>
                    <div class="space-y-3">
                        <div class="relative" id="bankDropdownContainer">
                            <label class="{{ $labelClasses }}">Pilih Bank/Kas <span class="text-red-500">*</span></label>
                            <input type="text" id="bankSearch" value="{{ $item->bank }}" class="{{ $inputClasses }} bg-white" placeholder="Cari Bank/Kas..." autocomplete="off">
                            <input type="hidden" name="bank" id="bankValue" value="{{ $item->bank }}" required>
                            
                            <div id="bankList" class="hidden absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg searchable-dropdown-items z-50">
                                @foreach($akunCoas as $akun)
                                    <div class="searchable-dropdown-item px-3 py-2 text-sm cursor-pointer hover:bg-gray-100" data-value="{{ $akun->nama_akun }}">
                                        {{ $akun->nama_akun }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Jenis Transaksi <span class="text-red-500">*</span></label>
                            <select name="jenis_transaksi" class="{{ $inputClasses }}" required>
                                <option value="Kredit" {{ $item->jenis_transaksi == 'Kredit' ? 'selected' : '' }}>Kredit (Biaya bertambah, Bank berkurang)</option>
                                <option value="Debit" {{ $item->jenis_transaksi == 'Debit' ? 'selected' : '' }}>Debit (Bank bertambah, Biaya berkurang)</option>
                            </select>
                        </div>
                        <div class="text-[10px] text-blue-600 bg-blue-50 p-2 rounded">
                            <strong>Info:</strong> Otomatis jurnal ke akun <strong>"Biaya Amprahan"</strong>.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Pranota Selection -->
            <div class="border rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-4 py-2 border-b flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-1">
                        <h4 class="text-sm font-semibold">Pilih Pranota Stock</h4>
                        <div class="relative w-full sm:w-80">
                            <input type="text" id="pranotaSearch" placeholder="Cari No. Pranota atau Vendor..." class="w-full text-xs border border-gray-300 rounded-md pl-8 pr-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" autocomplete="off">
                            <div class="absolute left-2.5 top-2 text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <span id="checkedCount" class="text-xs font-medium text-blue-600 whitespace-nowrap">0 Item Terpilih</span>
                </div>
                <div class="overflow-x-auto max-h-72">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left w-10">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pranota</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Bill</th>
                            </tr>
                        </thead>
                        <tbody id="pranotaTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($pranotaStocks as $pranota)
                                @php
                                    $totalBill = 0;
                                    if(is_array($pranota->items)) {
                                        foreach($pranota->items as $it) {
                                            $totalBill += ($it['harga'] ?? 0) * ($it['jumlah'] ?? 0);
                                        }
                                    }
                                    $totalBill += $pranota->adjustment ?? 0;
                                    $isChecked = in_array($pranota->id, $selectedPranotaIds);
                                @endphp
                                <tr class="pranota-row hover:bg-gray-50 cursor-pointer" onclick="toggleRow(this)">
                                    <td class="px-4 py-2" onclick="event.stopPropagation()">
                                        <input type="checkbox" name="pranota_stock_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox rounded border-gray-300" data-amount="{{ $totalBill }}" {{ $isChecked ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-4 py-2 text-xs font-medium">{{ $pranota->nomor_pranota }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-2 text-xs text-gray-600">{{ $pranota->vendor ?? '-' }}</td>
                                    <td class="px-4 py-2 text-xs text-right font-bold">Rp {{ number_format($totalBill, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">Tidak ada pranota yang dapat dibayar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold mb-3">Total Pembayaran</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Subtotal Tagihan:</span>
                            <input type="number" id="subtotal_display" class="{{ $readonlyInputClasses }} text-right w-48 font-bold" value="{{ $item->total_pembayaran }}" readonly>
                            <input type="hidden" name="total_pembayaran" id="total_pembayaran" value="{{ $item->total_pembayaran }}">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Penyesuaian Akhir:</span>
                            <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian" class="{{ $inputClasses }} text-right w-48 bg-white" value="{{ $item->total_tagihan_penyesuaian }}">
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t">
                            <span class="font-bold text-gray-800">TOTAL AKHIR:</span>
                            <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_akhir" class="{{ $readonlyInputClasses }} text-right w-48 font-black text-blue-700 bg-blue-50" value="{{ $item->total_tagihan_setelah_penyesuaian }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold mb-3">Informasi Tambahan</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                            <textarea name="alasan_penyesuaian" rows="1" class="{{ $inputClasses }} bg-white">{{ $item->alasan_penyesuaian }}</textarea>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Keterangan</label>
                            <textarea name="keterangan" rows="1" class="{{ $inputClasses }} bg-white">{{ $item->keterangan }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('pembayaran-pranota-stock.index') }}" class="px-6 py-2 border rounded text-gray-600 hover:bg-gray-100 transition-colors">Batal</a>
                <button type="submit" class="px-8 py-2 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700 shadow-md">Update Pembayaran</button>
            </div>
        </form>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Searchable Dropdown Logic (Vanilla JS)
            const bankSearch = document.getElementById('bankSearch');
            const bankList = document.getElementById('bankList');
            const bankValue = document.getElementById('bankValue');
            const bankItems = document.querySelectorAll('.searchable-dropdown-item');

            bankSearch.addEventListener('focus', () => bankList.classList.remove('hidden'));
            
            document.addEventListener('click', (e) => {
                if (!document.getElementById('bankDropdownContainer').contains(e.target)) {
                    bankList.classList.add('hidden');
                }
            });

            bankSearch.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                bankItems.forEach(item => {
                    const text = item.innerText.toLowerCase();
                    item.classList.toggle('hidden', !text.includes(term));
                });
                bankList.classList.remove('hidden');
            });

            bankItems.forEach(item => {
                item.addEventListener('click', () => {
                    bankSearch.value = item.innerText.trim();
                    bankValue.value = item.dataset.value;
                    bankList.classList.add('hidden');
                });
            });

            const pranotaSearch = document.getElementById('pranotaSearch');
            const pranotaRows = document.querySelectorAll('.pranota-row');

            if (pranotaSearch) {
                pranotaSearch.addEventListener('input', function() {
                    const term = this.value.toLowerCase();
                    pranotaRows.forEach(row => {
                        const noPranota = row.cells[1].innerText.toLowerCase();
                        const vendor = row.cells[3].innerText.toLowerCase();
                        row.classList.toggle('hidden', !(noPranota.includes(term) || vendor.includes(term)));
                    });
                });
            }

            window.toggleRow = function(row) {
                const cb = row.querySelector('.pranota-checkbox');
                if(cb) {
                    cb.checked = !cb.checked;
                    calculateTotal();
                }
            }

            const checkboxes = document.querySelectorAll('.pranota-checkbox');
            const selectAll = document.getElementById('selectAll');
            const subtotalInput = document.getElementById('total_pembayaran');
            const subtotalDisplay = document.getElementById('subtotal_display');
            const penyesuaianInput = document.getElementById('total_tagihan_penyesuaian');
            const totalAkhirInput = document.getElementById('total_akhir');
            const checkedCount = document.getElementById('checkedCount');

            window.calculateTotal = function() {
                let subtotal = 0;
                let count = 0;
                checkboxes.forEach(cb => {
                    if(cb.checked) {
                        subtotal += parseFloat(cb.dataset.amount);
                        count++;
                    }
                });

                if(subtotalInput) subtotalInput.value = subtotal;
                if(subtotalDisplay) subtotalDisplay.value = subtotal;
                if(checkedCount) checkedCount.innerText = count + " Item Terpilih";
                
                const penyesuaian = parseFloat(penyesuaianInput.value) || 0;
                if(totalAkhirInput) totalAkhirInput.value = subtotal + penyesuaian;
            }

            if(selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    calculateTotal();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', calculateTotal);
            });

            if(penyesuaianInput) {
                penyesuaianInput.addEventListener('input', calculateTotal);
            }

            // Initial calculation
            calculateTotal();

            document.getElementById('pembayaranForm').addEventListener('submit', function(e) {
                const count = document.querySelectorAll('.pranota-checkbox:checked').length;
                if(count === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu pranota stock yang akan dibayar.');
                    return;
                }
                if(!bankValue.value) {
                    e.preventDefault();
                    alert('Silakan pilih bank terlebih dahulu.');
                    bankSearch.focus();
                    return;
                }
                if(!confirm('Konfirmasi update pembayaran ini?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
@endsection
