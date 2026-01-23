@extends('layouts.app')

@section('title', 'Buat Belanja Amprahan')
@section('page_title', 'Buat Belanja Amprahan')

@push('styles')
<style>
    .custom-select-container { position: relative; z-index: 50; }
    .custom-select-button { display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 0.5rem 1rem; background-color: white; border: 1px solid #e5e7eb; border-radius: 0.25rem; cursor: pointer; text-align: left; }
    .custom-select-button:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 1px #2563eb; }
    .custom-select-dropdown { position: absolute; z-index: 9999; width: 100%; margin-top: 0.25rem; background-color: white; border: 1px solid #e5e7eb; border-radius: 0.25rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); max-height: 15rem; overflow-y: auto; display: none; }
    .custom-select-search { position: sticky; top: 0; padding: 0.5rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    .custom-select-option { padding: 0.5rem 1rem; cursor: pointer; }
    .custom-select-option:hover { background-color: #eff6ff; }
    .custom-select-option.selected { background-color: #dbeafe; font-weight: 500; }
    .hidden { display: none !important; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form action="{{ route('belanja-amprahan.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor</label>
                    <input type="text" name="nomor" value="{{ old('nomor') }}" class="w-full px-4 py-2 border rounded" placeholder="Nomor dokumen">
                    @error('nomor')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full px-4 py-2 border rounded">
                    @error('tanggal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <input type="text" name="supplier" value="{{ old('supplier') }}" class="w-full px-4 py-2 border rounded">
                    @error('supplier')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Barang</label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" class="w-full px-4 py-2 border rounded">
                    @error('nama_barang')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penerima</label>
                    
                    <div class="custom-select-container" id="penerima-select-container">
                        <input type="hidden" name="penerima_id" id="penerima_id" value="{{ old('penerima_id') }}">
                        
                        <button type="button" id="penerima-select-button" class="custom-select-button">
                            <span id="penerima-selected-text">
                                @if(old('penerima_id'))
                                    @php $selected = $karyawans->firstWhere('id', old('penerima_id')); @endphp
                                    {{ $selected ? $selected->nama_panggilan : 'Pilih Penerima' }}
                                @else
                                    Pilih Penerima
                                @endif
                            </span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div id="penerima-select-dropdown" class="custom-select-dropdown">
                            <div class="custom-select-search">
                                <input type="text" id="penerima-search-input" placeholder="Cari penerima..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div class="custom-select-options" id="penerima-options-list">
                                <div class="custom-select-option" data-value="" data-text="Pilih Penerima">Pilih Penerima</div>
                                @foreach($karyawans as $karyawan)
                                    <div class="custom-select-option" 
                                         data-value="{{ $karyawan->id }}" 
                                         data-search="{{ strtolower($karyawan->nama_panggilan) }}"
                                         data-text="{{ $karyawan->nama_panggilan }}">
                                        {{ $karyawan->nama_panggilan }}
                                    </div>
                                @endforeach
                            </div>
                            <div id="no-penerima-results" class="hidden p-4 text-center text-sm text-gray-500">
                                Penerima tidak ditemukan
                            </div>
                        </div>
                    </div>
                    @error('penerima_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                    <input type="number" step="0.01" name="total" value="{{ old('total', 0) }}" class="w-full px-4 py-2 border rounded">
                    @error('total')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" class="w-full px-4 py-2 border rounded" rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('belanja-amprahan.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        function initPenerimaSelect() {
            const selectContainer = document.getElementById('penerima-select-container');
            const selectButton = document.getElementById('penerima-select-button');
            const selectDropdown = document.getElementById('penerima-select-dropdown');
            const searchInput = document.getElementById('penerima-search-input');
            const optionsList = document.getElementById('penerima-options-list');
            const noResults = document.getElementById('no-penerima-results');
            const hiddenInput = document.getElementById('penerima_id');
            const selectedText = document.getElementById('penerima-selected-text');

            if (!selectContainer || !selectButton || !selectDropdown || !searchInput || !optionsList || !hiddenInput || !selectedText) {
                console.warn('Penerima select: Missing required elements');
                return;
            }

            let isOpen = false;
            let dropdownAppended = false;
            const originalParent = selectDropdown.parentNode;
            const placeholder = document.createComment('penerima-select-dropdown-placeholder');

            function updateSelectedState(value) {
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => {
                    if (opt.getAttribute('data-value') === (value || '').toString()) {
                        opt.classList.add('selected');
                    } else {
                        opt.classList.remove('selected');
                    }
                });
            }

            function selectOption(id, text) {
                hiddenInput.value = id;
                selectedText.textContent = text;
                closeDropdown();
                updateSelectedState(id);
            }

            // Init state
            updateSelectedState(hiddenInput.value);

            function openDropdown() {
                if (isOpen) return;
                
                // Reset search
                searchInput.value = '';
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => opt.classList.remove('hidden'));
                noResults.classList.add('hidden');

                // Calculate position
                const rect = selectButton.getBoundingClientRect();
                selectDropdown.style.position = 'absolute';
                selectDropdown.style.left = (rect.left + window.scrollX) + 'px';
                selectDropdown.style.top = (rect.bottom + window.scrollY) + 'px';
                selectDropdown.style.width = rect.width + 'px';
                selectDropdown.style.display = 'block';
                selectDropdown.style.zIndex = '99999';

                // Move to body to avoid overflow issues
                if (!dropdownAppended) {
                    originalParent.replaceChild(placeholder, selectDropdown);
                    document.body.appendChild(selectDropdown);
                    dropdownAppended = true;
                }

                isOpen = true;
                
                // Focus search
                requestAnimationFrame(() => {
                    searchInput.focus();
                });

                // Add scroll/resize listeners
                window.addEventListener('scroll', repositionDropdown, true);
                window.addEventListener('resize', repositionDropdown);
            }

            function closeDropdown() {
                if (!isOpen) return;

                selectDropdown.style.display = 'none';
                
                // Move back to original place
                if (dropdownAppended) {
                    document.body.removeChild(selectDropdown);
                    originalParent.replaceChild(selectDropdown, placeholder);
                    dropdownAppended = false;
                }

                isOpen = false;
                
                window.removeEventListener('scroll', repositionDropdown, true);
                window.removeEventListener('resize', repositionDropdown);
            }

            function repositionDropdown() {
                if (!isOpen || !dropdownAppended) return;
                const rect = selectButton.getBoundingClientRect();
                selectDropdown.style.left = (rect.left + window.scrollX) + 'px';
                selectDropdown.style.top = (rect.bottom + window.scrollY) + 'px';
                selectDropdown.style.width = rect.width + 'px';
            }

            // Toggle click handler
            selectButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isOpen) {
                    closeDropdown();
                } else {
                    // Close other dropdowns if any
                    document.querySelectorAll('.custom-select-dropdown').forEach(dd => {
                        if (dd !== selectDropdown) dd.style.display = 'none';
                    });
                    openDropdown();
                }
            });

            // Handle outside clicks
            document.addEventListener('click', function(e) {
                if (!isOpen) return;
                
                const clickedInsideContainer = selectContainer.contains(e.target);
                const clickedInsideDropdown = selectDropdown.contains(e.target);
                
                // If open (in body), we only care if clicked inside dropdown
                // If clicked button, handled by button listener (stopPropagation there protects us, but button is inside container)
                // Actually, since we stopPropagation on button, we just need to check if clicked inside dropdown
                
                if (!clickedInsideDropdown && !clickedInsideContainer) {
                    closeDropdown();
                }
            });

            // Search logic
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                const options = optionsList.querySelectorAll('.custom-select-option');
                let count = 0;
                
                options.forEach(opt => {
                    const searchData = opt.getAttribute('data-search') || '';
                    const textData = opt.textContent.toLowerCase();
                    if (searchData.includes(term) || textData.includes(term)) {
                        opt.classList.remove('hidden');
                        count++;
                    } else {
                        opt.classList.add('hidden');
                    }
                });

                noResults.classList.toggle('hidden', count === 0);
            });

            // Option Selection
            optionsList.addEventListener('click', function(e) {
                const option = e.target.closest('.custom-select-option');
                if (option) {
                    e.stopPropagation(); // prevent closure by document listener immediately
                    const val = option.getAttribute('data-value');
                    const txt = option.getAttribute('data-text');
                    selectOption(val, txt);
                }
            });

            // Prevent search click closing
            searchInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPenerimaSelect);
        } else {
            initPenerimaSelect();
        }
    })();
</script>
@endpush
