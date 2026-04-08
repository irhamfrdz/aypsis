@extends('layouts.app')

@section('title', 'Edit Pranota Stock Amprahan')
@section('page_title', 'Edit Pranota Stock Amprahan')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden max-w-4xl mx-auto">
    <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
        <h3 class="text-white font-bold text-lg">
            <i class="fas fa-edit mr-2"></i> Edit Pranota: {{ $pranota->nomor_pranota }}
        </h3>
        <a href="{{ route('pranota-stock.index') }}" class="text-white hover:text-indigo-100">
            <i class="fas fa-times"></i>
        </a>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Header Info -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Pranota</label>
                    <input type="text" id="nomor_pranota" value="{{ $pranota->nomor_pranota }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 font-bold" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pranota</label>
                    <input type="date" id="tanggal_pranota" value="{{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('Y-m-d') : date('Y-m-d') }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Accurate (Opsional)</label>
                    <input type="text" id="nomor_accurate" value="{{ $pranota->nomor_accurate }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: ACC/2024/001">
                </div>
            </div>

            <!-- Recipient Info -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Vendor / Supplier</label>
                    <input type="text" id="vendor_pranota" value="{{ $pranota->vendor }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama Vendor">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" id="rekening_pranota" value="{{ $pranota->rekening }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Bank - Nomor Rekening">
                </div>
                <div class="relative" id="pranota_penerima_dropdown_container">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Penerima Dana (Opsional)</label>
                    <div class="relative">
                        <input type="text" id="penerima_pranota" value="{{ $pranota->penerima }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pr-10" 
                               placeholder="Cari atau ketik nama penerima..." autocomplete="off">
                        <button type="button" onclick="togglePranotaPenerimaDropdown()" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fas fa-chevron-down transition-transform duration-200" id="pranota_penerima_arrow"></i>
                        </button>
                    </div>
                    
                    <!-- Searchable Dropdown -->
                    <div id="pranota_penerima_list" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-xl max-h-60 overflow-y-auto">
                        @foreach($karyawans as $k)
                            <div class="pranota-penerima-option px-4 py-2 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700 border-b border-gray-50 last:border-0" 
                                 onclick="selectPranotaPenerima('{{ $k->nama_lengkap }}')" 
                                 data-name="{{ $k->nama_lengkap }}">
                                <div class="font-medium">{{ $k->nama_lengkap }}</div>
                                <div class="text-[10px] text-gray-400 capitalize">{{ $k->jabatan ?? '-' }} - {{ $k->lokasi ?? '-' }}</div>
                            </div>
                        @endforeach
                        <div id="pranota_penerima_no_results" class="hidden px-4 py-8 text-center">
                            <div class="text-gray-400 text-sm mb-1">Tidak ada hasil</div>
                            <div class="text-[10px] text-gray-300">Tetap gunakan nama yang diketik</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Item dalam Pranota</h4>
                <span class="text-xs text-gray-500"><span id="total-count-display">{{ count($pranota->items) }}</span> Item Terpilih</span>
            </div>
            <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Barang</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="pranota-items" class="bg-white divide-y divide-gray-50">
                        @php $totalBiaya = 0; @endphp
                        @foreach($pranota->items as $index => $item)
                        @php 
                            $biaya = ($item['harga'] * $item['jumlah']) + ($item['adjustment'] ?? 0);
                            $totalBiaya += $biaya;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <div class="font-bold text-gray-900">{{ $item['nama_barang'] ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400">ID: {{ $item['id'] }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-bold text-gray-800">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold text-indigo-600">Rp {{ number_format($biaya, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-indigo-50">
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-right">
                                <div class="text-xs font-bold text-gray-600 uppercase">Penyesuaian (Adjustment)</div>
                                <div class="mt-1">
                                    <input type="number" id="adjustment" value="{{ $pranota->adjustment }}" 
                                           class="w-32 text-right border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold" placeholder="0">
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="text-xs font-bold text-gray-600 uppercase mb-1">Total Biaya</div>
                                <div id="total-biaya-display" class="text-xl font-black text-indigo-700" data-original="{{ $totalBiaya }}">
                                    Rp {{ number_format($totalBiaya + $pranota->adjustment, 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan Opsional</label>
            <textarea id="keterangan_pranota" rows="3" 
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                      placeholder="Tambahkan catatan jika diperlukan...">{{ $pranota->keterangan }}</textarea>
        </div>
    </div>

    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-200">
        <a href="{{ route('pranota-stock.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
            <i class="fas fa-arrow-left mr-2"></i> Batal
        </a>
        <button type="button" id="btnUpdatePranota" 
                class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
            <i class="fas fa-save mr-2"></i> Simpan Perubahan
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pranota Penerima Dropdown Logic
    const pranotaPenerimaContainer = document.getElementById('pranota_penerima_dropdown_container');
    const pranotaPenerimaInput = document.getElementById('penerima_pranota');
    const pranotaPenerimaList = document.getElementById('pranota_penerima_list');
    const pranotaPenerimaArrow = document.getElementById('pranota_penerima_arrow');
    const pranotaPenerimaOptions = document.querySelectorAll('.pranota-penerima-option');
    const pranotaPenerimaNoResults = document.getElementById('pranota_penerima_no_results');

    function togglePranotaPenerimaDropdown() {
        pranotaPenerimaList.classList.toggle('hidden');
        pranotaPenerimaArrow.classList.toggle('rotate-180');
    }

    if (pranotaPenerimaInput) {
        pranotaPenerimaInput.addEventListener('focus', function() {
            pranotaPenerimaList.classList.remove('hidden');
            pranotaPenerimaArrow.classList.add('rotate-180');
        });

        pranotaPenerimaInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            let hasVisible = false;
            
            pranotaPenerimaOptions.forEach(option => {
                const name = option.getAttribute('data-name').toLowerCase();
                if (name.includes(value)) {
                    option.classList.remove('hidden');
                    hasVisible = true;
                } else {
                    option.classList.add('hidden');
                }
            });

            if (!hasVisible) {
                pranotaPenerimaNoResults.classList.remove('hidden');
            } else {
                pranotaPenerimaNoResults.classList.add('hidden');
            }
        });
    }

    window.selectPranotaPenerima = function(name) {
        pranotaPenerimaInput.value = name;
        pranotaPenerimaList.classList.add('hidden');
        pranotaPenerimaArrow.classList.remove('rotate-180');
    };

    window.togglePranotaPenerimaDropdown = togglePranotaPenerimaDropdown;

    function updateTotalBiayaDisplay() {
        const display = document.getElementById('total-biaya-display');
        const original = parseFloat(display.dataset.original || 0);
        const adj = parseFloat(document.getElementById('adjustment').value || 0);
        const total = original + adj;
        display.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    }

    const adjInput = document.getElementById('adjustment');
    if (adjInput) {
        adjInput.addEventListener('input', updateTotalBiayaDisplay);
    }

    const btnUpdate = document.getElementById('btnUpdatePranota');
    if (btnUpdate) {
        btnUpdate.addEventListener('click', function() {
            const nomor = document.getElementById('nomor_pranota').value;
            const tanggal = document.getElementById('tanggal_pranota').value;
            const accurate = document.getElementById('nomor_accurate').value;
            const adj = document.getElementById('adjustment').value;
            const ket = document.getElementById('keterangan_pranota').value;
            const vendor = document.getElementById('vendor_pranota').value;
            const rekening = document.getElementById('rekening_pranota').value;
            const penerima = document.getElementById('penerima_pranota').value;

            const btn = this;
            btn.disabled = true;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

            fetch("{{ route('pranota-stock.update', $pranota->id) }}", {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nomor_pranota: nomor,
                    tanggal_pranota: tanggal,
                    nomor_accurate: accurate,
                    vendor: vendor,
                    rekening: rekening,
                    penerima: penerima,
                    adjustment: adj,
                    keterangan: ket,
                    items: @json($pranota->items)
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat menghubungi server');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    }

    window.onclick = function(event) {
        if (pranotaPenerimaContainer && !pranotaPenerimaContainer.contains(event.target)) {
            pranotaPenerimaList.classList.add('hidden');
            pranotaPenerimaArrow.classList.remove('rotate-180');
        }
    }
});
</script>
@endsection
