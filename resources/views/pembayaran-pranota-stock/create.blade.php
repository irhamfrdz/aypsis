@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Stock')
@section('page_title', 'Form Pembayaran Pranota Stock')

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

        <!-- Filter Tanggal -->
        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200">
            <form action="{{ route('pembayaran-pranota-stock.create') }}" method="GET" class="flex flex-col sm:flex-row gap-2 items-end">
                <div class="flex-1 grid grid-cols-2 gap-2">
                    <div>
                        <label for="start_date" class="{{ $labelClasses }}">Dari Tanggal Pranota</label>
                        <input type="date" name="start_date" id="start_date" class="{{ $inputClasses }} bg-white" value="{{ request('start_date') }}">
                    </div>
                    <div>
                        <label for="end_date" class="{{ $labelClasses }}">Sampai Tanggal Pranota</label>
                        <input type="date" name="end_date" id="end_date" class="{{ $inputClasses }} bg-white" value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="flex gap-1">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Cari</button>
                    <a href="{{ route('pembayaran-pranota-stock.create') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">Reset</a>
                </div>
            </form>
        </div>

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-stock.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Data Pembayaran -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold mb-3 border-b pb-2">Data Pembayaran</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="{{ $labelClasses }}">Nomor Pembayaran <span class="text-red-500">*</span></label>
                            <div class="flex gap-1">
                                <input type="text" name="nomor_pembayaran" id="nomor_pembayaran" value="{{ $nomorPembayaran }}" class="{{ $readonlyInputClasses }}" readonly required>
                                <button type="button" id="generateNomorBtn" class="px-3 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Nomor Accurate</label>
                            <input type="text" name="nomor_accurate" class="{{ $inputClasses }}" placeholder="Input Accurate No">
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Tanggal Kas <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_pembayaran" value="{{ date('Y-m-d') }}" class="{{ $inputClasses }}" required>
                        </div>
                    </div>
                </div>

                <!-- Bank & Accounting -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold mb-3 border-b pb-2">Bank & Transaksi (Double Book)</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="{{ $labelClasses }}">Pilih Bank/Kas <span class="text-red-500">*</span></label>
                            <select name="bank" id="bank" class="{{ $inputClasses }}" required>
                                <option value="">-- Pilih Bank --</option>
                                @foreach($akunCoa as $akun)
                                    <option value="{{ $akun->nama_akun }}">{{ $akun->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Jenis Transaksi <span class="text-red-500">*</span></label>
                            <select name="jenis_transaksi" class="{{ $inputClasses }}" required>
                                <option value="Kredit" selected>Kredit (Biaya bertambah, Bank berkurang)</option>
                                <option value="Debit">Debit (Bank bertambah, Biaya berkurang)</option>
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
                <div class="bg-gray-100 px-4 py-2 border-b flex justify-between items-center">
                    <h4 class="text-sm font-semibold">Pilih Pranota Stock</h4>
                    <span id="checkedCount" class="text-xs font-medium text-blue-600">0 Item Terpilih</span>
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
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pranotaStocks as $pranota)
                                @php
                                    $totalBill = 0;
                                    if(is_array($pranota->items)) {
                                        foreach($pranota->items as $it) {
                                            $totalBill += ($it['harga'] ?? 0) * ($it['jumlah'] ?? 0);
                                        }
                                    }
                                    $totalBill += $pranota->adjustment ?? 0;
                                @endphp
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="toggleRow(this)">
                                    <td class="px-4 py-2" onclick="event.stopPropagation()">
                                        <input type="checkbox" name="pranota_stock_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox rounded border-gray-300" data-amount="{{ $totalBill }}">
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
                            <input type="number" id="subtotal_display" class="{{ $readonlyInputClasses }} text-right w-48 font-bold" value="0" readonly>
                            <input type="hidden" name="total_pembayaran" id="total_pembayaran" value="0">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Penyesuaian Akhir:</span>
                            <input type="number" name="total_tagihan_penyesuaian" id="total_tagihan_penyesuaian" class="{{ $inputClasses }} text-right w-48 bg-white" value="0">
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t">
                            <span class="font-bold text-gray-800">TOTAL AKHIR:</span>
                            <input type="number" name="total_tagihan_setelah_penyesuaian" id="total_akhir" class="{{ $readonlyInputClasses }} text-right w-48 font-black text-blue-700 bg-blue-50" value="0" readonly>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold mb-3">Informasi Tambahan</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                            <textarea name="alasan_penyesuaian" rows="1" class="{{ $inputClasses }} bg-white"></textarea>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Keterangan</label>
                            <textarea name="keterangan" rows="1" class="{{ $inputClasses }} bg-white"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('pembayaran-pranota-stock.index') }}" class="px-6 py-2 border rounded text-gray-600 hover:bg-gray-100 transition-colors">Batal</a>
                <button type="submit" class="px-8 py-2 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700 shadow-md">Simpan Pembayaran</button>
            </div>
        </form>
    </div>

    <script>
        function toggleRow(row) {
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

        function calculateTotal() {
            let subtotal = 0;
            let count = 0;
            checkboxes.forEach(cb => {
                if(cb.checked) {
                    subtotal += parseFloat(cb.dataset.amount);
                    count++;
                }
            });

            subtotalInput.value = subtotal;
            subtotalDisplay.value = subtotal;
            checkedCount.innerText = count + " Item Terpilih";
            
            const penyesuaian = parseFloat(penyesuaianInput.value) || 0;
            totalAkhirInput.value = subtotal + penyesuaian;
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

        penyesuaianInput.addEventListener('input', calculateTotal);

        document.getElementById('generateNomorBtn').addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            fetch('{{ route("pembayaran-pranota-stock.generate-nomor") }}')
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('nomor_pembayaran').value = data.nomor_pembayaran;
                    }
                })
                .finally(() => btn.disabled = false);
        });

        document.getElementById('pembayaranForm').addEventListener('submit', function(e) {
            const count = document.querySelectorAll('.pranota-checkbox:checked').length;
            if(count === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu pranota stock yang akan dibayar.');
                return;
            }
            if(!confirm('Konfirmasi simpan pembayaran ini?')) {
                e.preventDefault();
            }
        });
    </script>
@endsection
