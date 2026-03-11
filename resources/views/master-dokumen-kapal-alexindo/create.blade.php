@extends('layouts.app')

@section('title', 'Tambah Dokumen Kapal')

@push('styles')
<style>
    .custom-select-container { position: relative; z-index: 50; }
    .custom-select-button { display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 0.5rem 0.75rem; background-color: white; border: 1px solid #d1d5db; border-radius: 0.375rem; cursor: pointer; text-align: left; font-size: 0.875rem; line-height: 1.25rem; }
    .custom-select-button:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 1px #3b82f6; }
    .custom-select-dropdown { position: absolute; z-index: 9999; width: 100%; margin-top: 0.25rem; background-color: white; border: 1px solid #d1d5db; border-radius: 0.375rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); max-height: 15rem; overflow-y: auto; display: none; }
    .custom-select-search { position: sticky; top: 0; padding: 0.5rem; background-color: #f9fafb; border-bottom: 1px solid #d1d5db; }
    .custom-select-option { padding: 0.5rem 0.75rem; cursor: pointer; font-size: 0.875rem; }
    .custom-select-option:hover { background-color: #eff6ff; }
    .custom-select-option.selected { background-color: #dbeafe; font-weight: 500; }
    .hidden { display: none !important; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center rounded-t-lg">
                <h2 class="text-lg font-bold text-gray-800">Tambah Dokumen Kapal</h2>
                <a href="{{ $selected_kapal_id ? route('master-dokumen-kapal-alexindo.show', $selected_kapal_id) : route('master-dokumen-kapal-alexindo.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium pb-1.5 border-b-2 border-transparent hover:border-gray-500 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            <div class="px-6 py-4">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <ul class="list-disc pl-5 text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('master-dokumen-kapal-alexindo.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kapal <span class="text-red-500">*</span></label>
                            <select name="kapal_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">-- Pilih Kapal --</option>
                                @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->id }}" {{ (old('kapal_id') ?? $selected_kapal_id ?? '') == $kapal->id ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }} {{ $kapal->nickname ? '('.$kapal->nickname.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dokumen / Sertifikat <span class="text-red-500">*</span></label>
                            <div class="custom-select-container" id="sertifikat-select-container">
                                <input type="hidden" name="sertifikat_kapal_id" id="sertifikat_kapal_id" value="{{ old('sertifikat_kapal_id') }}" required>
                                
                                <button type="button" id="sertifikat-select-button" class="custom-select-button">
                                    <span id="sertifikat-selected-text">
                                        @if(old('sertifikat_kapal_id'))
                                            @php $selected = $sertifikat_kapals->firstWhere('id', old('sertifikat_kapal_id')); @endphp
                                            @if($selected)
                                                {{ $selected->nama_sertifikat }}
                                                @if($selected->nickname)
                                                    <span class="text-xs text-blue-600 font-normal ml-1">({{ $selected->nickname }})</span>
                                                @endif
                                            @else
                                                -- Pilih Dokumen/Sertifikat --
                                            @endif
                                        @else
                                            -- Pilih Dokumen/Sertifikat --
                                        @endif
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div id="sertifikat-select-dropdown" class="custom-select-dropdown">
                                    <div class="custom-select-search">
                                        <input type="text" id="sertifikat-search-input" placeholder="Cari dokumen..." class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div class="custom-select-options" id="sertifikat-options-list">
                                        <div class="custom-select-option" data-value="" data-text="-- Pilih Dokumen/Sertifikat --" data-nickname="">-- Pilih Dokumen/Sertifikat --</div>
                                        @foreach($sertifikat_kapals as $sertifikat)
                                            <div class="custom-select-option" 
                                                 data-value="{{ $sertifikat->id }}" 
                                                 data-search="{{ strtolower($sertifikat->nama_sertifikat) }} {{ strtolower($sertifikat->nickname ?? '') }}"
                                                 data-text="{{ $sertifikat->nama_sertifikat }}"
                                                 data-nickname="{{ $sertifikat->nickname ?? '' }}">
                                                <div class="flex flex-col py-0.5">
                                                    <span class="font-medium text-gray-800">{{ $sertifikat->nama_sertifikat }}</span>
                                                    @if($sertifikat->nickname)
                                                        <span class="text-xs text-blue-600 font-normal mt-0.5">{{ $sertifikat->nickname }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="no-sertifikat-results" class="hidden p-4 text-center text-sm text-gray-500">
                                        Dokumen tidak ditemukan
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Dokumen</label>
                            <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nomor Dokumen/Sertifikat">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                            <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kadaluarsa</label>
                            <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">File Dokumen</label>
                            <input type="file" name="file_dokumen" accept=".pdf,.png,.jpg,.jpeg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p class="text-xs text-gray-500 mt-1">Maksimal 5MB. Format: PDF, JPG, PNG.</p>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm placeholder-gray-400" placeholder="Keterangan tambahan jika ada">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-gray-200 mt-6 pt-4 gap-2">
                        <a href="{{ $selected_kapal_id ? route('master-dokumen-kapal-alexindo.show', $selected_kapal_id) : route('master-dokumen-kapal-alexindo.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium transition duration-150">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium transition duration-150">Simpan Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        function initSertifikatSelect() {
            const selectContainer = document.getElementById('sertifikat-select-container');
            const selectButton = document.getElementById('sertifikat-select-button');
            const selectDropdown = document.getElementById('sertifikat-select-dropdown');
            const searchInput = document.getElementById('sertifikat-search-input');
            const optionsList = document.getElementById('sertifikat-options-list');
            const noResults = document.getElementById('no-sertifikat-results');
            const hiddenInput = document.getElementById('sertifikat_kapal_id');
            const selectedText = document.getElementById('sertifikat-selected-text');

            if (!selectContainer || !selectButton || !selectDropdown || !searchInput || !optionsList || !hiddenInput || !selectedText) {
                return;
            }

            let isOpen = false;
            let dropdownAppended = false;
            const originalParent = selectDropdown.parentNode;
            const placeholder = document.createComment('sertifikat-select-dropdown-placeholder');

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

            function selectOption(id, text, nickname) {
                hiddenInput.value = id;
                if (nickname) {
                    selectedText.innerHTML = text + ' <span style="font-size:0.75rem;color:#2563eb;font-weight:400;margin-left:4px">('+nickname+')</span>';
                } else {
                    selectedText.textContent = text;
                }
                closeDropdown();
                updateSelectedState(id);
            }

            updateSelectedState(hiddenInput.value);

            function openDropdown() {
                if (isOpen) return;
                searchInput.value = '';
                const options = optionsList.querySelectorAll('.custom-select-option');
                options.forEach(opt => opt.classList.remove('hidden'));
                noResults.classList.add('hidden');

                const rect = selectButton.getBoundingClientRect();
                selectDropdown.style.position = 'absolute';
                selectDropdown.style.left = (rect.left + window.scrollX) + 'px';
                selectDropdown.style.top = (rect.bottom + window.scrollY) + 'px';
                selectDropdown.style.width = rect.width + 'px';
                selectDropdown.style.display = 'block';
                selectDropdown.style.zIndex = '9999';

                if (!dropdownAppended) {
                    originalParent.replaceChild(placeholder, selectDropdown);
                    document.body.appendChild(selectDropdown);
                    dropdownAppended = true;
                }

                isOpen = true;
                setTimeout(() => searchInput.focus(), 10);

                window.addEventListener('scroll', repositionDropdown, true);
                window.addEventListener('resize', repositionDropdown);
            }

            function closeDropdown() {
                if (!isOpen) return;
                selectDropdown.style.display = 'none';
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

            selectButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isOpen) closeDropdown();
                else {
                    document.querySelectorAll('.custom-select-dropdown').forEach(dd => {
                        if (dd !== selectDropdown) dd.style.display = 'none';
                    });
                    openDropdown();
                }
            });

            document.addEventListener('click', function(e) {
                if (!isOpen) return;
                if (!selectDropdown.contains(e.target) && !selectContainer.contains(e.target)) {
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
                const option = e.target.closest('.custom-select-option');
                if (option) {
                    e.stopPropagation();
                    selectOption(
                        option.getAttribute('data-value'),
                        option.getAttribute('data-text'),
                        option.getAttribute('data-nickname')
                    );
                }
            });

            searchInput.addEventListener('click', e => e.stopPropagation());
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSertifikatSelect);
        } else {
            initSertifikatSelect();
        }
    })();
</script>
@endpush
