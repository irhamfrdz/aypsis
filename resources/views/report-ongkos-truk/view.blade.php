@extends('layouts.app')

@section('title', 'Hasil Laporan Ongkos Truk')
@section('page_title', 'Hasil Laporan Ongkos Truk')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <a href="{{ route('report.ongkos-truk.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Laporan Ongkos Truk</h1>
                    <p class="text-gray-600">
                        Periode: <span class="font-semibold text-blue-600">{{ $startDate->format('d/M/Y') }}</span> s/d <span class="font-semibold text-blue-600">{{ $endDate->format('d/M/Y') }}</span>
                        @if($noPlat)
                            | Unit: <span class="font-semibold text-blue-600">{{ is_array($noPlat) ? implode(', ', $noPlat) : $noPlat }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="window.print()" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 transition duration-200 flex items-center shadow-sm font-medium">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <button id="btnAddToPranota" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 flex items-center shadow-sm font-medium">
                    <i class="fas fa-plus-circle mr-2"></i> Tambahkan ke Pranota
                </button>
                <a href="{{ route('report.ongkos-truk.export', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200 flex items-center shadow-sm font-medium">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Filtered Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 uppercase text-xs font-bold tracking-wider">
                        <th class="px-6 py-4 border-b text-center w-10">
                            <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer">
                        </th>
                        <th class="px-6 py-4 border-b">No</th>
                        <th class="px-6 py-4 border-b text-center">Tanggal</th>
                        <th class="px-6 py-4 border-b">No. Surat Jalan</th>
                        <th class="px-6 py-4 border-b">Plat Mobil</th>
                        <th class="px-6 py-4 border-b">Supir</th>
                        <th class="px-6 py-4 border-b">Keterangan</th>
                        <th class="px-6 py-4 border-b">Tujuan</th>
                        <th class="px-6 py-4 border-b text-right">Ongkos Truk</th>
                        <th class="px-6 py-4 border-b text-right">Uang Jalan</th>
                        <th class="px-6 py-4 border-b">Bukti</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse($data as $item)
                        @php
                            $isAdj = str_ends_with($item['type'] ?? '', '_adj');
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ !($item['has_tanda_terima'] ?? true) && !$isAdj ? 'bg-yellow-50' : '' }} {{ $isAdj ? 'bg-blue-50/30' : '' }}">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="data-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer" 
                                    value="{{ $item['no_surat_jalan'] }}" 
                                    data-id="{{ $item['id'] ?? '' }}"
                                    data-type="{{ $item['model_type'] ?? '' }}">
                            </td>
                            <td class="px-6 py-4 text-xs">{{ $isAdj ? '' : $loop->iteration }}</td>
                            <td class="px-6 py-4 text-center {{ $isAdj ? 'text-blue-500/70 text-[11px]' : '' }}">
                                {{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}
                                @if(!($item['has_tanda_terima'] ?? true) && !$isAdj)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-orange-100 text-orange-700 ml-1" title="Belum ada Tanda Terima">Belum TT</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium {{ $isAdj ? 'opacity-30' : 'text-gray-800' }}">{{ $item['no_surat_jalan'] }}</td>
                            <td class="px-6 py-4 {{ $isAdj ? 'opacity-30' : '' }}">{{ $item['no_plat'] }}</td>
                            <td class="px-6 py-4 {{ $isAdj ? 'opacity-30' : '' }}">{{ $item['supir'] }}</td>
                            <td class="px-6 py-4 {{ $isAdj ? 'pl-12 italic text-blue-600' : '' }}">
                                @if($isAdj) <i class="fas fa-level-up-alt fa-rotate-90 mr-2 opacity-50"></i> @endif
                                {{ $item['keterangan'] }}
                            </td>
                            <td class="px-6 py-4 {{ $isAdj ? 'opacity-30' : '' }}">{{ $item['tujuan'] }}</td>
                            <td class="px-6 py-4 text-right font-semibold {{ $isAdj ? 'opacity-30' : 'text-gray-800' }}">
                                Rp {{ number_format($item['ongkos_truck'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold {{ $isAdj ? 'text-blue-700' : 'text-gray-800' }}">
                                Rp {{ number_format($item['uang_jalan'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-xs {{ $isAdj ? 'text-blue-600 font-medium' : 'opacity-30' }}">
                                {{ $item['nomor_bukti'] ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-gray-400 italic">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-4xl mb-2"></i>
                                    <span>Tidak ada data untuk periode dan filter yang dipilih.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot class="bg-gray-50 font-bold text-gray-800 uppercase text-xs">
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-right border-t">Grand Total</td>
                        <td class="px-6 py-4 text-right border-t text-sm">
                            Rp {{ number_format($data->sum('ongkos_truck'), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right border-t text-sm">
                            Rp {{ number_format($data->sum('uang_jalan'), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 border-t"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        header, .lg\:top-16, #sidebar, .bg-gray-100 {
            display: none !important;
        }
        .container {
            width: 100% !important;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .bg-white {
            box-shadow: none !important;
            border: none !important;
        }
        .bg-gray-50 {
            background-color: transparent !important;
        }
        .px-4, .px-6, .py-6, .p-6 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .mb-6 {
            margin-bottom: 20px !important;
        }
        table {
            border: 1px solid #000 !important;
        }
        th, td {
            border: 1px solid #000 !important;
            color: #000 !important;
        }
        .text-blue-600 {
            color: #000 !important;
        }
        .bg-blue-100 {
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
    }
</style>

<!-- Modal Masuk Pranota -->
<div id="pranotaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[100]">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-2xl rounded-2xl bg-white transition-all duration-300 transform">
        <div class="mt-1">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Konfirmasi Masuk Pranota Ongkos Truk</h3>
                </div>
                <button type="button" onclick="closePranotaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="modal_nomor_pranota" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nomor Pranota <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="modal_nomor_pranota" name="nomor_pranota" required readonly
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-xl bg-gray-50 cursor-not-allowed text-gray-600"
                                   placeholder="Generating...">
                            <button type="button" onclick="generateNomorPranota()" 
                                    class="px-4 py-2 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded-xl transition-colors"
                                    title="Generate nomor baru">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="modal_tanggal_pranota" class="block text-sm font-semibold text-gray-700 mb-1">
                            Tanggal Pranota <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="modal_tanggal_pranota" name="tanggal_pranota" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all"
                               value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Item Terpilih</label>
                    <div class="overflow-x-auto border border-gray-100 rounded-2xl max-h-72 shadow-inner">
                        <table id="pranota-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/50 sticky top-0 backdrop-blur-sm">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">No. SJ</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Unit</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Ongkos</th>
                                </tr>
                            </thead>
                            <tbody id="pranota-items-container" class="bg-white divide-y divide-gray-100">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex items-center justify-between px-2">
                        <span class="text-sm text-gray-500" id="modal-item-count">0 item terpilih</span>
                        <div class="text-right">
                            <span class="text-xs text-gray-400 block">Total Nominal</span>
                            <span class="text-2xl font-black text-blue-600" id="modal-grand-total">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="modal_adjustment" class="block text-sm font-semibold text-gray-700 mb-1">
                            Adjustment (Opsional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-gray-400 text-sm">Rp</span>
                            <input type="number" id="modal_adjustment" name="adjustment"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all font-semibold"
                                   placeholder="0">
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1">Gunakan minus (-) untuk pengurangan</p>
                    </div>
                    <div>
                        <label for="modal_keterangan" class="block text-sm font-semibold text-gray-700 mb-1">
                            Keterangan (Opsional)
                        </label>
                        <textarea id="modal_keterangan" name="keterangan" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all"
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closePranotaModal()"
                        class="px-6 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl transition duration-200">
                    Batal
                </button>
                <button type="button" id="btnConfirmPranota"
                        class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-200 transition duration-200 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Simpan Pranota
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.data-checkbox');
        const btnAddToPranota = document.getElementById('btnAddToPranota');
        const currentPranotaItems = [];

        // Check All functionality
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
        });

        // Individual checkbox change
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const checked = Array.from(checkboxes).filter(c => c.checked);
                checkAll.checked = checked.length === checkboxes.length;
                checkAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
            });
        });

        // Add to Pranota action using Modal (OB Style)
        btnAddToPranota.addEventListener('click', function() {
            const selectedCheckboxRows = Array.from(checkboxes).filter(cb => cb.checked);
            
            if (selectedCheckboxRows.length === 0) {
                alert('Silakan pilih minimal satu data laporan!');
                return;
            }

            const ids = selectedCheckboxRows.map(cb => cb.dataset.id).join(',');
            const types = selectedCheckboxRows.map(cb => cb.dataset.type).join(',');

            // Show loading state/opening modal
            openPranotaModal();
            fetchPreviewData(ids, types);
        });

        window.openPranotaModal = function() {
            document.getElementById('pranotaModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // Prevent scroll
            generateNomorPranota();
        };

        window.closePranotaModal = function() {
            document.getElementById('pranotaModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            // Reset form inside modal
            document.getElementById('modal_vendor_id').value = '';
            document.getElementById('modal_supir_id').value = '';
            document.getElementById('modal_keterangan').value = '';
        };

        window.generateNomorPranota = function() {
            const input = document.getElementById('modal_nomor_pranota');
            input.value = 'Generating...';
            
            fetch('{{ route('pranota-ongkos-truk.generate-nomor', [], false) }}')
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        input.value = data.nomor_pranota;
                    } else {
                        input.value = 'Error generating';
                    }
                })
                .catch(() => {
                    input.value = 'Error generating';
                });
        };

        function fetchPreviewData(ids, types) {
            const container = document.getElementById('pranota-items-container');
            container.innerHTML = '<tr><td colspan="4" class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i></td></tr>';

            const url = `{{ route('pranota-ongkos-truk.get-preview-data', [], false) }}?selected_ids=${ids}&types=${types}`;
            
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        populateModalTable(data.items);
                    } else {
                        alert('Gagal mengambil data preview.');
                        closePranotaModal();
                    }
                })
                .catch(e => {
                    console.error(e);
                    alert('Terjadi kesalahan saat memproses data.');
                    closePranotaModal();
                });
        }

        function populateModalTable(items) {
            const container = document.getElementById('pranota-items-container');
            container.innerHTML = '';
            
            currentPranotaItems.length = 0; // Clear the array

            if (items.length === 0) {
                container.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-500">Tidak ada data valid terpilih.</td></tr>';
                return;
            }

            items.forEach((item, index) => {
                currentPranotaItems.push(item);

                const row = `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${item.no_surat_jalan}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">${item.tanggal}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">${item.no_plat} <span class="text-[10px] ml-1 opacity-50">(${item.supir})</span></td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-gray-900 text-right">Rp ${new Intl.NumberFormat('id-ID').format(item.nominal)}</td>
                    </tr>
                `;
                container.innerHTML += row;
            });

            document.getElementById('modal-item-count').textContent = `${items.length} item terpilih`;
            updateModalGrandTotal();
        }

        function updateModalGrandTotal() {
            const itemsTotal = currentPranotaItems.reduce((sum, item) => sum + (parseFloat(item.nominal) || 0), 0);
            const adjustment = parseFloat(document.getElementById('modal_adjustment').value) || 0;
            const grandTotal = itemsTotal + adjustment;
            
            document.getElementById('modal-grand-total').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(grandTotal)}`;
        }

        document.getElementById('modal_adjustment').addEventListener('input', updateModalGrandTotal);

        // Submitting via AJAX
        document.getElementById('btnConfirmPranota').addEventListener('click', function() {
            if (currentPranotaItems.length === 0) return;

            const btn = this;
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...';

            const payload = {
                tanggal_pranota: document.getElementById('modal_tanggal_pranota').value,
                adjustment: document.getElementById('modal_adjustment').value,
                keterangan: document.getElementById('modal_keterangan').value,
                items: currentPranotaItems.map(item => ({
                    id: item.id,
                    type: item.type,
                    no_surat_jalan: item.no_surat_jalan,
                    tanggal: item.tanggal,
                    nominal: item.nominal
                }))
            };

            fetch('{{ route('pranota-ongkos-truk.store', [], false) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.href = data.redirect_url;
                } else {
                    alert('Gagal menyimpan pranota: ' + (data.message || 'Error unknown'));
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(e => {
                console.error(e);
                alert('Terjadi kesalahan sistem.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    });
</script>
@endsection
