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

    /* Custom Searchable Select Styles */
    .custom-select-container {
        position: relative;
    }
    .custom-select-button {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 0.5rem 1rem;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        cursor: pointer;
        text-align: left;
    }
    .custom-select-button:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }
    .custom-select-dropdown {
        position: absolute;
        z-index: 9999;
        width: 100%;
        margin-top: 0.25rem;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 15rem;
        overflow-y: auto;
        display: none;
    }
    .custom-select-search {
        position: sticky;
        top: 0;
        padding: 0.5rem;
        background-color: #f9fafb;
        border-bottom: 1px solid #d1d5db;
    }
    .custom-select-option {
        padding: 0.5rem 1rem;
        cursor: pointer;
    }
    .custom-select-option:hover {
        background-color: #eff6ff;
    }
    .custom-select-option.selected {
        background-color: #dbeafe;
        font-weight: 500;
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
                <div class="mb-4 flex justify-end">
                     <button type="submit" class="px-3 py-1 bg-orange-500 text-white text-sm rounded hover:bg-orange-600 transition" onclick="return confirm('Apakah anda yakin ingin memasak ban yang dipilih menjadi kanisir?')">
                        <i class="fas fa-fire mr-1"></i> Masak Kanisir (Bulk)
                     </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="check-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobil</label>
                        <div class="custom-select-container" id="mobil-select-container">
                            <input type="hidden" name="mobil_id" id="mobil_id" required>
                            <button type="button" id="mobil-select-button" class="custom-select-button">
                                <span id="mobil-selected-text">-- Pilih Mobil --</span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            <div id="mobil-select-dropdown" class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" id="mobil-search-input" placeholder="Cari mobil..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="custom-select-options" id="mobil-options-list">
                                    <div class="custom-select-option" data-value="" data-text="-- Pilih Mobil --">-- Pilih Mobil --</div>
                                    @foreach($mobils as $mobil)
                                        <div class="custom-select-option" 
                                             data-value="{{ $mobil->id }}" 
                                             data-search="{{ strtolower($mobil->nomor_polisi . ' ' . $mobil->merek . ' ' . $mobil->jenis) }}"
                                             data-text="{{ $mobil->nomor_polisi }} ({{ $mobil->merek }} - {{ $mobil->jenis }})">
                                            {{ $mobil->nomor_polisi }} ({{ $mobil->merek }} - {{ $mobil->jenis }})
                                        </div>
                                    @endforeach
                                </div>
                                <div id="no-mobil-results" class="hidden p-4 text-center text-sm text-gray-500">Mobil tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penerima (Supir/Kenek)</label>
                        <div class="custom-select-container" id="penerima-select-container">
                            <input type="hidden" name="penerima_id" id="penerima_id" required>
                            <button type="button" id="penerima-select-button" class="custom-select-button">
                                <span id="penerima-selected-text">-- Pilih Penerima --</span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            <div id="penerima-select-dropdown" class="custom-select-dropdown">
                                <div class="custom-select-search">
                                    <input type="text" id="penerima-search-input" placeholder="Cari penerima..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="custom-select-options" id="penerima-options-list">
                                    <div class="custom-select-option" data-value="" data-text="-- Pilih Penerima --">-- Pilih Penerima --</div>
                                    @foreach($karyawans as $karyawan)
                                        <div class="custom-select-option" 
                                             data-value="{{ $karyawan->id }}" 
                                             data-search="{{ strtolower($karyawan->nama_lengkap) }}"
                                             data-text="{{ $karyawan->nama_lengkap }}">
                                            {{ $karyawan->nama_lengkap }}
                                        </div>
                                    @endforeach
                                </div>
                                <div id="no-penerima-results" class="hidden p-4 text-center text-sm text-gray-500">Penerima tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pasang / Keluar</label>
                        <input type="date" name="tanggal_keluar" required value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="closeUsageModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
    function initializeCustomSelects() {
        console.log('Initializing Custom Selects...');
        
        // Tab Logic
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        if (tabs.length > 0) {
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    tab.classList.add('active');
                    document.getElementById(tab.dataset.target).classList.add('active');
                });
            });
        }

        // Check All Logic
        const checkAll = document.getElementById('check-all');
        const checkItems = document.querySelectorAll('.check-item');
        
        if(checkAll) {
            checkAll.addEventListener('change', function() {
                checkItems.forEach(item => item.checked = this.checked);
            });
        }

        // Initialize custom selects
        initMobilSelectIndex();
        initPenerimaSelectIndex();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCustomSelects);
    } else {
        initializeCustomSelects();
    }

    function initMobilSelectIndex() {
        const selectContainer = document.getElementById('mobil-select-container');
        const selectButton = document.getElementById('mobil-select-button');
        const selectDropdown = document.getElementById('mobil-select-dropdown');
        const searchInput = document.getElementById('mobil-search-input');
        const optionsList = document.getElementById('mobil-options-list');
        const noResults = document.getElementById('no-mobil-results');
        const hiddenInput = document.getElementById('mobil_id');
        const selectedText = document.getElementById('mobil-selected-text');

        if (!selectContainer || !selectButton || !selectDropdown) {
            console.error('Mobil Select elements not found');
            return;
        }

        let dropdownAppended = false;
        const originalParent = selectDropdown.parentNode;
        const placeholder = document.createComment('mobil-select-dropdown-placeholder');

        function updateSelectedState(value) {
            const options = optionsList.querySelectorAll('.custom-select-option');
            options.forEach(opt => {
                if (opt.getAttribute('data-value') === (value || '').toString()) {
                    opt.classList.add('selected');
                    selectedText.textContent = opt.getAttribute('data-text');
                } else {
                    opt.classList.remove('selected');
                }
            });
        }

        function selectMobil(id, text) {
            hiddenInput.value = id;
            selectedText.textContent = text;
            closeDropdown();
            updateSelectedState(id);
        }

        function openDropdown() {
            searchInput.value = '';
            const options = optionsList.querySelectorAll('.custom-select-option');
            options.forEach(opt => opt.classList.remove('hidden'));
            noResults.classList.add('hidden');

            const rect = selectButton.getBoundingClientRect();
            // Use fixed position and append to body to ensure it's on top of everything
            selectDropdown.style.position = 'fixed';
            selectDropdown.style.left = rect.left + 'px';
            selectDropdown.style.top = rect.bottom + 'px';
            selectDropdown.style.width = rect.width + 'px';
            selectDropdown.style.display = 'block';
            selectDropdown.style.zIndex = '99999'; // Very high z-index

            if (!dropdownAppended) {
                originalParent.replaceChild(placeholder, selectDropdown);
                document.body.appendChild(selectDropdown);
                dropdownAppended = true;
            }

            setTimeout(() => searchInput.focus(), 10);
            window.addEventListener('scroll', repositionDropdown, true);
            window.addEventListener('resize', repositionDropdown);
        }

        function closeDropdown() {
            selectDropdown.style.display = 'none';
            if (dropdownAppended) {
                document.body.removeChild(selectDropdown);
                originalParent.replaceChild(selectDropdown, placeholder);
                dropdownAppended = false;
            }
            window.removeEventListener('scroll', repositionDropdown, true);
            window.removeEventListener('resize', repositionDropdown);
        }

        function repositionDropdown() {
            if (!dropdownAppended) return;
            // Recalculate position
            const rect = selectButton.getBoundingClientRect();
            selectDropdown.style.left = rect.left + 'px';
            selectDropdown.style.top = rect.bottom + 'px';
            selectDropdown.style.width = rect.width + 'px'; // Ensure width stays consistent
            // If button is hidden (e.g. modal closed), hide dropdown
            if (rect.width === 0 && rect.height === 0) {
                closeDropdown();
            }
        }

        selectButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (window.getComputedStyle(selectDropdown).display === 'none') {
                // Close other dropdowns first
                const otherDropdowns = document.querySelectorAll('.custom-select-dropdown');
                otherDropdowns.forEach(dd => {
                    if (dd !== selectDropdown && dd.style.display !== 'none') {
                        // We can't easily access the close function of others, but hiding them is a safe fallback
                        dd.style.display = 'none'; 
                    }
                });
                openDropdown();
            } else {
                closeDropdown();
            }
        });

        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            const options = optionsList.querySelectorAll('.custom-select-option');
            let count = 0;
            options.forEach(opt => {
                const searchData = opt.getAttribute('data-search') || '';
                if (searchData.includes(term) || opt.textContent.toLowerCase().includes(term)) {
                    opt.classList.remove('hidden');
                    count++;
                } else {
                    opt.classList.add('hidden');
                }
            });
            noResults.classList.toggle('hidden', count > 0);
        });

        optionsList.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent closing when clicking non-option areas in list
            const option = e.target.closest('.custom-select-option');
            if (option) selectMobil(option.getAttribute('data-value'), option.getAttribute('data-text'));
        });

        document.addEventListener('click', function(e) {
            // Close if click is outside container AND outside dropdown (which might be in body)
            if (!selectContainer.contains(e.target) && !selectDropdown.contains(e.target)) {
                closeDropdown();
            }
        });
        
        // Prevent click in search/dropdown from bubbling up
        selectDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    function initPenerimaSelectIndex() {
        const selectContainer = document.getElementById('penerima-select-container');
        const selectButton = document.getElementById('penerima-select-button');
        const selectDropdown = document.getElementById('penerima-select-dropdown');
        const searchInput = document.getElementById('penerima-search-input');
        const optionsList = document.getElementById('penerima-options-list');
        const noResults = document.getElementById('no-penerima-results');
        const hiddenInput = document.getElementById('penerima_id');
        const selectedText = document.getElementById('penerima-selected-text');

        if (!selectContainer || !selectButton || !selectDropdown) {
             console.error('Penerima Select elements not found');
             return;
        }

        let dropdownAppended = false;
        const originalParent = selectDropdown.parentNode;
        const placeholder = document.createComment('penerima-select-dropdown-placeholder');

        function updateSelectedState(value) {
            const options = optionsList.querySelectorAll('.custom-select-option');
            options.forEach(opt => {
                if (opt.getAttribute('data-value') === (value || '').toString()) {
                    opt.classList.add('selected');
                    selectedText.textContent = opt.getAttribute('data-text');
                } else {
                    opt.classList.remove('selected');
                }
            });
        }

        function selectPenerima(id, text) {
            hiddenInput.value = id;
            selectedText.textContent = text;
            closeDropdown();
            updateSelectedState(id);
        }

        function openDropdown() {
            searchInput.value = '';
            const options = optionsList.querySelectorAll('.custom-select-option');
            options.forEach(opt => opt.classList.remove('hidden'));
            noResults.classList.add('hidden');

            const rect = selectButton.getBoundingClientRect();
            selectDropdown.style.position = 'fixed';
            selectDropdown.style.left = rect.left + 'px';
            selectDropdown.style.top = rect.bottom + 'px';
            selectDropdown.style.width = rect.width + 'px';
            selectDropdown.style.display = 'block';
            selectDropdown.style.zIndex = '99999';

            if (!dropdownAppended) {
                originalParent.replaceChild(placeholder, selectDropdown);
                document.body.appendChild(selectDropdown);
                dropdownAppended = true;
            }

            setTimeout(() => searchInput.focus(), 10);
            window.addEventListener('scroll', repositionDropdown, true);
            window.addEventListener('resize', repositionDropdown);
        }

        function closeDropdown() {
            selectDropdown.style.display = 'none';
            if (dropdownAppended) {
                document.body.removeChild(selectDropdown);
                originalParent.replaceChild(selectDropdown, placeholder);
                dropdownAppended = false;
            }
            window.removeEventListener('scroll', repositionDropdown, true);
            window.removeEventListener('resize', repositionDropdown);
        }

        function repositionDropdown() {
            if (!dropdownAppended) return;
            const rect = selectButton.getBoundingClientRect();
            selectDropdown.style.left = rect.left + 'px';
            selectDropdown.style.top = rect.bottom + 'px';
            selectDropdown.style.width = rect.width + 'px';
             if (rect.width === 0 && rect.height === 0) {
                closeDropdown();
            }
        }

        selectButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (window.getComputedStyle(selectDropdown).display === 'none') {
                 const otherDropdowns = document.querySelectorAll('.custom-select-dropdown');
                otherDropdowns.forEach(dd => {
                    if (dd !== selectDropdown && dd.style.display !== 'none') {
                        dd.style.display = 'none';
                    }
                });
                openDropdown();
            } else {
                closeDropdown();
            }
        });

        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            const options = optionsList.querySelectorAll('.custom-select-option');
            let count = 0;
            options.forEach(opt => {
                const searchData = opt.getAttribute('data-search') || '';
                if (searchData.includes(term) || opt.textContent.toLowerCase().includes(term)) {
                    opt.classList.remove('hidden');
                    count++;
                } else {
                    opt.classList.add('hidden');
                }
            });
            noResults.classList.toggle('hidden', count > 0);
        });

        optionsList.addEventListener('click', function(e) {
            e.stopPropagation();
            const option = e.target.closest('.custom-select-option');
            if (option) selectPenerima(option.getAttribute('data-value'), option.getAttribute('data-text'));
        });

        document.addEventListener('click', function(e) {
            if (!selectContainer.contains(e.target) && !selectDropdown.contains(e.target)) {
                closeDropdown();
            }
        });
        
        selectDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    function openUsageModal(id, seri) {
        document.getElementById('modal-ban-seri').textContent = seri;
        document.getElementById('usageForm').action = "{{ url('stock-ban') }}/" + id + "/use";
        
        // Reset selections
        document.getElementById('mobil_id').value = '';
        document.getElementById('mobil-selected-text').textContent = '-- Pilih Mobil --';
        document.getElementById('penerima_id').value = '';
        document.getElementById('penerima-selected-text').textContent = '-- Pilih Penerima --';
        
        document.getElementById('usageModal').classList.remove('hidden');
    }

    function closeUsageModal() {
        document.getElementById('usageModal').classList.add('hidden');
    }
</script>
@endpush
