@extends('layouts.app')

@section('title', 'Stock Ban')
@section('page_title', 'Stock Ban')

@push('styles')
<style>
    .tab-btn {
        padding: 0.75rem 1rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        color: #6b7280;
    }
    .tab-btn:hover {
        color: #1f2937;
        border-color: #d1d5db;
    }
    .tab-btn.active {
        color: #2563eb;
        border-color: #2563eb;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    /* Refined Custom Dropdown & Form Styles */
    .dropdown-menu-custom {
        display: none;
        position: fixed;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        z-index: 999999;
        max-height: 300px;
        overflow-y: auto;
        min-width: 250px;
        border: 1px solid #3b82f633;
    }
    .dropdown-search-container {
        position: sticky;
        top: 0;
        background-color: #f9fafb;
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        z-index: 10;
    }
    .dropdown-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        color: #4b5563;
        font-size: 0.875rem;
        transition: all 0.2s;
        border-bottom: 1px solid #f3f4f6;
    }
    .dropdown-item:last-child {
        border-bottom: none;
    }
    .dropdown-item:hover {
        background-color: #eff6ff;
        color: #2563eb;
        padding-left: 1.25rem;
    }
    .dropdown-item.selected {
        background-color: #dbeafe;
        color: #1d4ed8;
        font-weight: 600;
    }

    /* Premium Input Styling */
    .form-input-premium {
        width: 100%;
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
        color: #111827;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .form-input-premium:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    .form-label-premium {
        display: block;
        margin-bottom: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
    }
    .btn-dropdown-premium {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
        color: #111827;
        font-size: 0.875rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-dropdown-premium:hover {
        border-color: #9ca3af;
    }
    .btn-dropdown-premium:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .hidden {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
           <div class="flex space-x-1 border-b border-gray-200" id="tabs-container">
               <button class="tab-btn active" data-target="tab-ban-luar">Ban Luar</button>
               <button class="tab-btn" data-target="tab-ban-dalam">Ban Dalam</button>
               <button class="tab-btn" data-target="tab-ban-perut">Ban Perut</button>
               <button class="tab-btn" data-target="tab-lock-kontainer">Lock Kontainer</button>
               <button class="tab-btn" data-target="tab-ring-velg">Ring Velg</button>
               <button class="tab-btn" data-target="tab-velg">Velg</button>
           </div>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('stock-ban.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Stock
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Tab: Ban Luar -->
        <div id="tab-ban-luar" class="tab-content active p-4">
            <form action="{{ route('stock-ban.bulk-masak') }}" method="POST" id="bulk-masak-form">
                @csrf
                <div class="mb-4 flex justify-end hidden" id="bulk-action-container">
                     <button type="button" class="px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 shadow-md transition flex items-center" onclick="openKanisirModal(event)">
                        <i class="fas fa-fire mr-2"></i> Masak Kanisir (Bulk)
                     </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="check-all-ban-luar" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Seri / Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk & Ukuran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi / Posisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Masuk</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stockBans as $ban)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ban->status == 'Stok' && $ban->kondisi != 'afkir')
                                    <input type="checkbox" name="ids[]" value="{{ $ban->id }}" class="check-item rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $ban->nomor_seri ?? '-' }}
                                    @if($ban->namaStockBan)
                                    <div class="text-xs text-gray-500">{{ $ban->namaStockBan->nama }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="font-medium text-gray-800">{{ $ban->merk ?? $ban->merkBan->nama ?? '-' }}</div>
                                    <div class="text-xs">{{ $ban->ukuran ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $ban->kondisi == 'asli' ? 'bg-green-100 text-green-800' : 
                                           ($ban->kondisi == 'kanisir' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($ban->kondisi == 'afkir' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($ban->kondisi) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $ban->status == 'Stok' ? 'bg-blue-100 text-blue-800' : 
                                           ($ban->status == 'Terpakai' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $ban->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ban->lokasi ?? '-' }}
                                    @if($ban->mobil)
                                    <div class="text-xs text-blue-600 font-medium mt-1">
                                        <i class="fas fa-truck mr-1"></i> {{ $ban->mobil->nomor_polisi }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ date('d-m-Y', strtotime($ban->tanggal_masuk)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        @if($ban->status == 'Stok')
                                            <button type="button" 
                                                onclick="openUsageModal('{{ $ban->id }}', '{{ $ban->nomor_seri }}')"
                                                class="text-green-600 hover:text-green-900" title="Gunakan / Pasang">
                                                <i class="fas fa-wrench"></i>
                                            </button>
                                            
                                            @if($ban->kondisi != 'kanisir' && $ban->kondisi != 'afkir')
                                            <form action="{{ route('stock-ban.masak', $ban->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin masak ban ini jadi kanisir?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-orange-600 hover:text-orange-900" title="Masak Kanisir">
                                                    <i class="fas fa-fire"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif

                                        <a href="{{ route('stock-ban.edit', $ban->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('stock-ban.destroy', $ban->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data stock ban luar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        <!-- Tab: Ban Dalam -->
        <div id="tab-ban-dalam" class="tab-content p-4">
            @include('stock-ban.partials.table-bulk', ['items' => $stockBanDalams, 'type' => 'Ban Dalam'])
        </div>

        <!-- Tab: Ban Perut -->
        <div id="tab-ban-perut" class="tab-content p-4">
            @include('stock-ban.partials.table-bulk', ['items' => $stockBanPeruts, 'type' => 'Ban Perut'])
        </div>

        <!-- Tab: Lock Kontainer -->
        <div id="tab-lock-kontainer" class="tab-content p-4">
            @include('stock-ban.partials.table-bulk', ['items' => $stockLockKontainers, 'type' => 'Lock Kontainer'])
        </div>

        <!-- Tab: Ring Velg -->
        <div id="tab-ring-velg" class="tab-content p-4">
            @include('stock-ban.partials.table-ring-velg', ['items' => $stockRingVelgs, 'type' => 'Ring Velg'])
        </div>

        <!-- Tab: Velg -->
        <div id="tab-velg" class="tab-content p-4">
            @include('stock-ban.partials.table-ring-velg', ['items' => $stockVelgs, 'type' => 'Velg'])
        </div>
    </div>
</div>

<!-- Modal Masak Kanisir (Bulk) -->
<div id="kanisirModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeKanisirModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-fire text-orange-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Proses Masak Kanisir
                        </h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="kanisir_invoice" class="form-label-premium">Nomor Invoice</label>
                                <input type="text" id="kanisir_invoice" class="form-input-premium bg-gray-100" value="{{ $nextInvoice }}" readonly>
                            </div>
                            
                            <div>
                                <label for="kanisir_tanggal" class="form-label-premium">Tanggal Masuk Kanisir <span class="text-red-500">*</span></label>
                                <input type="date" id="kanisir_tanggal" class="form-input-premium" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div>
                                <label for="kanisir_vendor" class="form-label-premium">Vendor <span class="text-red-500">*</span></label>
                                <input type="text" id="kanisir_vendor" class="form-input-premium" placeholder="Nama Vendor" required>
                            </div>

                            <div>
                                <label for="kanisir_harga" class="form-label-premium">Harga (Total/Satuan) <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" id="kanisir_harga" class="form-input-premium pl-10" placeholder="0" required>
                                </div>
                                <p class="mt-2 text-xs text-gray-400 italic">Harga yang dimasukkan akan diupdate ke data ban.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" onclick="submitKanisirForm(event)" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-6 py-2 bg-orange-600 text-base font-semibold text-white hover:bg-orange-700 focus:outline-none transition-all transform hover:scale-105 sm:ml-0 sm:w-auto sm:text-sm">
                    Simpan
                </button>
                <button type="button" onclick="closeKanisirModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-all sm:mt-0 sm:ml-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gunakan Ban Luar -->
<div id="usageModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeUsageModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="usageForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Gunakan Ban: <span id="modal-ban-seri"></span>
                    </h3>
                    
                    <div class="mb-4">
                        <label class="form-label-premium">Mobil</label>
                        <input type="hidden" name="mobil_id" id="mobil_id" required>
                        <button type="button" id="btn-mobil" class="btn-dropdown-premium" onclick="DropdownManager.toggle('mobil', this)">
                            <span class="block truncate" id="text-mobil">-- Pilih Mobil --</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        
                        <!-- Dropdown Content (Hidden Loop) -->
                        <div id="dropdown-content-mobil" class="hidden">
                            <div class="dropdown-search-container">
                                <input type="text" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2" placeholder="Cari mobil..." onkeyup="DropdownManager.filter(this)">
                            </div>
                            <div class="dropdown-list">
                                <div class="dropdown-item" onclick="DropdownManager.select('mobil', '', '-- Pilih Mobil --')">-- Pilih Mobil --</div>
                                @foreach($mobils as $mobil)
                                    <div class="dropdown-item" 
                                         onclick="DropdownManager.select('mobil', '{{ $mobil->id }}', '{{ $mobil->nomor_polisi }}')"
                                         data-search="{{ strtolower($mobil->nomor_polisi . ' ' . $mobil->merek . ' ' . $mobil->jenis) }}">
                                        {{ $mobil->nomor_polisi }} ({{ $mobil->merek }} - {{ $mobil->jenis }})
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Penerima (Supir/Kenek)</label>
                        <input type="hidden" name="penerima_id" id="penerima_id" required>
                        <button type="button" id="btn-penerima" class="btn-dropdown-premium" onclick="DropdownManager.toggle('penerima', this)">
                            <span class="block truncate" id="text-penerima">-- Pilih Penerima --</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        
                        <!-- Dropdown Content (Hidden Loop) -->
                        <div id="dropdown-content-penerima" class="hidden">
                            <div class="dropdown-search-container">
                                <input type="text" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2" placeholder="Cari penerima..." onkeyup="DropdownManager.filter(this)">
                            </div>
                            <div class="dropdown-list">
                                <div class="dropdown-item" onclick="DropdownManager.select('penerima', '', '-- Pilih Penerima --')">-- Pilih Penerima --</div>
                                @foreach($karyawans as $karyawan)
                                    <div class="dropdown-item" 
                                         onclick="DropdownManager.select('penerima', '{{ $karyawan->id }}', '{{ $karyawan->nama_lengkap }}')"
                                         data-search="{{ strtolower($karyawan->nama_lengkap) }}">
                                        {{ $karyawan->nama_lengkap }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Tanggal Pasang / Keluar</label>
                        <input type="date" name="tanggal_keluar" required value="{{ date('Y-m-d') }}" class="form-input-premium">
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="form-input-premium" placeholder="Tambahkan catatan pemakaian..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-6 py-2 bg-blue-600 text-base font-semibold text-white hover:bg-blue-700 focus:outline-none transition-all transform hover:scale-105 sm:ml-0 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="closeUsageModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-all sm:mt-0 sm:ml-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Logic
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(tab.dataset.target).classList.add('active');
            });
        });

        // Bulk Action & Check All Logic (specific for Ban Luar)
        const bulkActionContainer = document.getElementById('bulk-action-container');
        
        function updateBulkButton() {
            if (!bulkActionContainer) return;
            const checkedCount = document.querySelectorAll('#tab-ban-luar .check-item:checked').length;
            
            if (checkedCount > 0) {
                bulkActionContainer.classList.remove('hidden');
            } else {
                bulkActionContainer.classList.add('hidden');
            }
        }

        // Listen for changes on the unique check-all box
        const checkAllBanLuar = document.getElementById('check-all-ban-luar');
        if (checkAllBanLuar) {
            checkAllBanLuar.addEventListener('change', function(e) {
                const isChecked = e.target.checked;
                // Only target check-items within the ban-luar tab to avoid cross-tab pollution
                const container = document.getElementById('tab-ban-luar');
                if(container) {
                    container.querySelectorAll('.check-item').forEach(item => {
                        item.checked = isChecked;
                    });
                }
                updateBulkButton();
            });
        }

        // Delegated listener for individual checkboxes
        document.getElementById('tab-ban-luar').addEventListener('change', function(e) {
            if (e.target.classList.contains('check-item')) {
                updateBulkButton();
            }
        });

        // Initialize state
        updateBulkButton();
    });

    const DropdownManager = {
        activeDropdownId: null,
        activeMenu: null,

        toggle: function(id, button) {
            if (this.activeDropdownId === id) {
                this.close();
                return;
            }
            this.open(id, button);
        },

        open: function(id, button) {
            this.close(); // Close existing

            // Create/Get Menu
            let menu = document.getElementById('dropdown-menu-overlay-' + id);
            if (!menu) {
                // Clone the content template
                const template = document.getElementById('dropdown-content-' + id);
                if (!template) return;

                menu = document.createElement('div');
                menu.id = 'dropdown-menu-overlay-' + id;
                menu.className = 'dropdown-menu-custom';
                menu.innerHTML = template.innerHTML;
                document.body.appendChild(menu);

                // Prevent click bubbling from menu
                menu.addEventListener('click', (e) => e.stopPropagation());
            }

            // Position it
            const rect = button.getBoundingClientRect();
            menu.style.width = rect.width + 'px';
            menu.style.left = rect.left + 'px';
            menu.style.top = (rect.bottom + window.scrollY) + 'px';
            menu.style.display = 'block';

            // Reset search
            const searchInput = menu.querySelector('input');
            if(searchInput) {
                searchInput.value = '';
                searchInput.focus();
                this.filter(searchInput); // Reset filter
            }

            this.activeDropdownId = id;
            this.activeMenu = menu;

            // Add scroll listener to update position
            window.addEventListener('scroll', this.reposition, true);
            window.addEventListener('resize', this.reposition);
        },

        close: function() {
            if (this.activeMenu) {
                this.activeMenu.style.display = 'none';
                this.activeDropdownId = null;
                this.activeMenu = null;
                window.removeEventListener('scroll', this.reposition, true);
                window.removeEventListener('resize', this.reposition);
            }
        },

        reposition: function() {
            if (!DropdownManager.activeDropdownId || !DropdownManager.activeMenu) return;
            // Find the button (re-query in case context changed?) - we assume button ID convention
            const button = document.getElementById('btn-' + DropdownManager.activeDropdownId);
            if(button) {
                const rect = button.getBoundingClientRect();
                DropdownManager.activeMenu.style.left = rect.left + 'px';
                DropdownManager.activeMenu.style.top = (rect.bottom + window.scrollY) + 'px';
                DropdownManager.activeMenu.style.width = rect.width + 'px';
            }
        },

        select: function(id, value, text) {
            document.getElementById(id).value = value;
            const textEl = document.getElementById('text-' + id);
            if (textEl) textEl.textContent = text;
            this.close();
        },

        filter: function(input) {
            const term = input.value.toLowerCase();
            const items = input.closest('.dropdown-menu-custom').querySelectorAll('.dropdown-item');
            items.forEach(item => {
                const search = item.getAttribute('data-search') || '';
                if (!term || search.includes(term) || item.textContent.toLowerCase().includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    };

    // Close on click outside
    document.addEventListener('click', function(e) {
        // If click is not on a dropdown button and not inside a menu
        if (!e.target.closest('button[onclick^="DropdownManager.toggle"]') && 
            !e.target.closest('.dropdown-menu-custom')) {
            DropdownManager.close();
        }
    });

    function openUsageModal(id, seri) {
        document.getElementById('modal-ban-seri').textContent = seri;
        document.getElementById('usageForm').action = "{{ url('stock-ban') }}/" + id + "/use";
        
        // Reset selections
        document.getElementById('mobil_id').value = '';
        document.getElementById('text-mobil').textContent = '-- Pilih Mobil --';
        document.getElementById('penerima_id').value = '';
        document.getElementById('text-penerima').textContent = '-- Pilih Penerima --';
        
        document.getElementById('usageModal').classList.remove('hidden');
    }

    function closeUsageModal() {
        document.getElementById('usageModal').classList.add('hidden');
        DropdownManager.close();
    }

    // Kanisir Modal Logic
    function openKanisirModal(e) {
        e.preventDefault();
        document.getElementById('kanisirModal').classList.remove('hidden');
    }

    function closeKanisirModal() {
        document.getElementById('kanisirModal').classList.add('hidden');
    }

    function submitKanisirForm(e) {
        e.preventDefault();
        
        const form = document.getElementById('bulk-masak-form');
        const modal = document.getElementById('kanisirModal');
        
        // Get values
        const invoice = document.getElementById('kanisir_invoice').value;
        const tanggal = document.getElementById('kanisir_tanggal').value;
        const vendor = document.getElementById('kanisir_vendor').value;
        const harga = document.getElementById('kanisir_harga').value;
        
        if (!tanggal || !vendor || !harga) {
            alert('Mohon lengkapi data Tanggal, Vendor, dan Harga.');
            return;
        }

        // Helper to append hidden input
        const appendHidden = (name, value) => {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        };

        appendHidden('nomor_invoice', invoice);
        appendHidden('tanggal_masuk_kanisir', tanggal);
        appendHidden('vendor', vendor);
        appendHidden('harga', harga);

        form.submit();
    }
</script>
@endpush
