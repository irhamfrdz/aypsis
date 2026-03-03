@extends('layouts.app')

@section('content')
<div class="space-y-4 max-w-4xl mx-auto">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Buat Pranota Invoice Vendor Supir</h2>
            <p class="text-sm text-gray-500">Kelompokkan beberapa invoice tagihan vendor menjadi satu pranota</p>
        </div>
        <a href="{{ route('pranota-invoice-vendor-supir.index') }}" class="flex items-center text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg px-4 py-2 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat error pada input Anda:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row gap-4 justify-between items-start md:items-end">
            <!-- Filter Vendor Setup -->
            <form action="{{ route('pranota-invoice-vendor-supir.create') }}" method="GET" class="w-full md:w-auto">
                <div class="form-group">
                    <label for="vendor_id_filter" class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Vendor untuk Mengambil Invoice</label>
                    <div class="flex gap-2 relative">
                        <select name="vendor_id" id="vendor_id_filter" required class="min-w-[250px] px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-sm transition-shadow">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ $selectedVendor == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama_vendor }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-700 transition">Tampilkan Invoice</button>
                    </div>
                </div>
            </form>
        </div>

        <form action="{{ route('pranota-invoice-vendor-supir.store') }}" method="POST" id="pranotaForm">
            @csrf
            
            <input type="hidden" name="vendor_id" value="{{ $selectedVendor }}">
            
            <div class="p-6 space-y-6">
                
                @if($selectedVendor)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="no_pranota" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Pranota<span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="no_pranota" id="no_pranota" required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-sm transition-shadow @error('no_pranota') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                value="{{ old('no_pranota', $defaultPranotaNo) }}">
                            @error('no_pranota')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tanggal_pranota" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Pranota<span class="text-red-500 ml-1">*</span></label>
                            <input type="date" name="tanggal_pranota" id="tanggal_pranota" required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-sm transition-shadow @error('tanggal_pranota') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                value="{{ old('tanggal_pranota', now()->format('Y-m-d')) }}">
                            @error('tanggal_pranota')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="form-group md:col-span-2">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan (Opsional)</label>
                            <textarea name="keterangan" id="keterangan" rows="2" 
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-sm transition-shadow @error('keterangan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                placeholder="Tuliskan keterangan tambahan bila ada..."></textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group md:col-span-2 mt-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="potong_pph" id="potong_pph" value="1" class="h-4 w-4 text-rose-600 focus:ring-rose-500 border-gray-300 rounded cursor-pointer">
                                <label for="potong_pph" class="ml-2 block text-sm font-medium text-gray-700 cursor-pointer">
                                    Potong PPh 2% dari Total Invoice
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="flex justify-between items-end mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Daftar Invoice Tersedia</h3>
                            <div class="flex flex-col gap-2 items-end">
                                <div class="bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium shadow-sm border border-gray-200">
                                    Subtotal: <span id="subtotal_display" class="font-bold">Rp 0</span>
                                </div>
                                <div id="pph_container" class="hidden bg-orange-50 text-orange-800 px-4 py-2 rounded-lg font-medium shadow-sm border border-orange-200">
                                    PPh 2%: <span id="total_pph_display" class="font-bold">- Rp 0</span>
                                </div>
                                <div class="bg-rose-50 text-rose-800 px-4 py-2 rounded-lg font-medium shadow-sm border border-rose-100">
                                    Grand Total: <span id="total_nominal_display" class="font-bold text-lg">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        @error('invoice_id')
                            <p class="mb-3 text-sm font-medium text-red-600 bg-red-50 py-2 px-3 rounded border border-red-200">{{ $message }}</p>
                        @enderror

                        @if($invoices->isEmpty())
                            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <p class="text-gray-500">Tidak ada invoice yang belum masuk pranota untuk vendor ini.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto shadow-sm shadow-rose-50/50 rounded-lg border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-100 text-gray-700 font-semibold border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 w-12 text-center">
                                                <input type="checkbox" id="selectAll" class="rounded text-rose-600 focus:ring-rose-500 border-gray-300">
                                            </th>
                                            <th class="px-4 py-3">No. Invoice</th>
                                            <th class="px-4 py-3">Tanggal Invoice</th>
                                            <th class="px-4 py-3 text-right whitespace-nowrap">Total Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($invoices as $invoice)
                                            <tr class="hover:bg-rose-50/50 transition-colors">
                                                <td class="px-4 py-3 text-center">
                                                    <input type="checkbox" name="invoice_id[]" value="{{ $invoice->id }}" data-total="{{ $invoice->total_nominal }}" class="invoice-checkbox rounded text-rose-600 focus:ring-rose-500 border-gray-300">
                                                </td>
                                                <td class="px-4 py-3 font-medium text-gray-900">
                                                    {{ $invoice->no_invoice }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-600">
                                                    {{ $invoice->tanggal_invoice->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-right font-medium text-gray-900">
                                                    Rp {{ number_format($invoice->total_nominal, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-10 bg-gray-50 rounded-lg border border-gray-200">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <h4 class="text-gray-500 font-medium">Pilih vendor terlebih dahulu untuk menampilkan daftar invoice vendor supir</h4>
                    </div>
                @endif
                
            </div>

            @if($selectedVendor && !$invoices->isEmpty())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end space-x-3">
                <a href="{{ route('pranota-invoice-vendor-supir.index') }}" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">Batal</a>
                <button type="button" id="submitBtn" class="px-5 py-2 text-sm font-medium text-white bg-rose-600 border border-transparent rounded-lg hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors shadow-sm focus:bg-rose-700 flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Pranota (<span id="count_selected">0</span>)
                </button>
            </div>
            @endif
            
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.invoice-checkbox');
        const totalDisplay = document.getElementById('total_nominal_display');
        const countDisplay = document.getElementById('count_selected');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('pranotaForm');

        const potongPphCheckbox = document.getElementById('potong_pph');
        const pphContainer = document.getElementById('pph_container');
        const subtotalDisplay = document.getElementById('subtotal_display');
        const totalPphDisplay = document.getElementById('total_pph_display');

        function calculateTotal() {
            let subtotal = 0;
            let count = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    subtotal += parseFloat(cb.dataset.total) || 0;
                    count++;
                }
            });
            
            let pph = 0;
            if (potongPphCheckbox && potongPphCheckbox.checked) {
                pph = subtotal * 0.02;
                if(pphContainer) pphContainer.classList.remove('hidden');
            } else {
                if(pphContainer) pphContainer.classList.add('hidden');
            }

            let grandTotal = subtotal - pph;

            if (subtotalDisplay) subtotalDisplay.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            if (totalPphDisplay) totalPphDisplay.textContent = '- Rp ' + pph.toLocaleString('id-ID');
            
            totalDisplay.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
            countDisplay.textContent = count;

            if (count > 0) {
                submitBtn.removeAttribute('disabled');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
            }

            // Sync selectAll status
            if(selectAll) {
                selectAll.checked = checkboxes.length > 0 && count === checkboxes.length;
            }
        }

        if(potongPphCheckbox) {
            potongPphCheckbox.addEventListener('change', calculateTotal);
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                calculateTotal();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', calculateTotal);
        });

        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                let count = 0;
                checkboxes.forEach(cb => {
                    if (cb.checked) count++;
                });

                if (count === 0) {
                    alert('Silakan pilih minimal satu invoice untuk digabungkan menjadi pranota.');
                    return;
                }
                
                form.submit();
            });
            
            // Initial call to set state
            calculateTotal();
        }
    });
</script>
@endsection
