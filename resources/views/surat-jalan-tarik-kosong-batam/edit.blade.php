@extends('layouts.app')

@section('title', 'Edit Surat Jalan Tarik Kosong Batam')

@push('styles')
<style>
    .kontainer-option:hover {
        background-color: #f3f4f6;
    }
    .kontainer-option.selected {
        background-color: #eef2ff;
        border-left: 4px solid #4f46e5;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-gray-900">Edit Surat Jalan</h1>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Tarik Kosong Batam</span>
                </div>
                <p class="text-xs text-gray-600 mt-1">Ubah data surat jalan: {{ $item->no_surat_jalan }}</p>
            </div>
            <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('surat-jalan-tarik-kosong-batam.update', $item->id) }}" method="POST" class="p-4" id="sjtk-form">
            @csrf
            @method('PUT')

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <div class="font-medium">Terdapat kesalahan pada form:</div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Dasar</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date"
                           name="tanggal_surat_jalan"
                           id="tanggal_surat_jalan"
                           value="{{ old('tanggal_surat_jalan', $item->tanggal_surat_jalan->format('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_surat_jalan') border-red-500 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="text"
                           name="no_surat_jalan"
                           id="no_surat_jalan"
                           value="{{ old('no_surat_jalan', $item->no_surat_jalan) }}"
                           required
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none @error('no_surat_jalan') border-red-500 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Tiket / DO</label>
                    <input type="text"
                           name="no_tiket_do"
                           value="{{ old('no_tiket_do', $item->no_tiket_do) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <input type="text"
                           name="tujuan_pengambilan"
                           value="{{ old('tujuan_pengambilan', $item->tujuan_pengambilan) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text"
                           name="tujuan_pengiriman"
                           value="{{ old('tujuan_pengiriman', $item->tujuan_pengiriman) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Armada Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Armada</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat / Armada</label>
                    <select name="no_plat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Armada --</option>
                        @foreach($mobils as $mobil)
                            <option value="{{ $mobil->nomor_polisi }}" {{ old('no_plat', $item->no_plat) == $mobil->nomor_polisi ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi }} ({{ $mobil->merek }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir Utama</label>
                    <select name="supir"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" {{ old('supir', $item->supir) == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir Cadangan</label>
                    <select name="supir2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" {{ old('supir2', $item->supir2) == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <select name="kenek"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Kenek --</option>
                        @foreach($keneks as $kenek)
                            <option value="{{ $kenek->nama_lengkap }}" {{ old('kenek', $item->kenek) == $kenek->nama_lengkap ? 'selected' : '' }}>
                                {{ $kenek->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kontainer Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kontainer</h3>
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    @php
                        $currentNoKontainer = old('no_kontainer', $item->no_kontainer);
                    @endphp
                    <input type="text"
                           id="no_kontainer_search"
                           placeholder="Cari nomor kontainer..."
                           autocomplete="off"
                           value="{{ $currentNoKontainer }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="hidden" name="no_kontainer" id="no_kontainer_hidden" value="{{ $currentNoKontainer }}">
                    
                    <div id="no_kontainer_dropdown" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto mt-1">
                        @php $foundInList = false; @endphp
                        @foreach($kontainers as $kontainer)
                            @if($currentNoKontainer == $kontainer->nomor_seri_gabungan) @php $foundInList = true; @endphp @endif
                            <div class="kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-50 text-sm {{ $currentNoKontainer == $kontainer->nomor_seri_gabungan ? 'selected' : '' }}"
                                 data-value="{{ $kontainer->nomor_seri_gabungan }}"
                                 data-ukuran="{{ $kontainer->ukuran }}">
                                <div class="font-medium">{{ $kontainer->nomor_seri_gabungan }}</div>
                                <div class="text-xs text-gray-500">{{ $kontainer->ukuran }}'</div>
                            </div>
                        @endforeach
                        
                        @if(!$foundInList && $currentNoKontainer)
                            <div class="kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-50 text-sm selected"
                                 data-value="{{ $currentNoKontainer }}"
                                 data-ukuran="{{ $item->size }}">
                                <div class="font-medium">{{ $currentNoKontainer }}</div>
                                <div class="text-xs text-gray-500">{{ $item->size }}' (Current)</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                    <select name="size"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Ukuran --</option>
                        <option value="20" {{ old('size', $item->size) == '20' ? 'selected' : '' }}>20 FT</option>
                        <option value="40" {{ old('size', $item->size) == '40' ? 'selected' : '' }}>40 FT</option>
                        <option value="45" {{ old('size', $item->size) == '45' ? 'selected' : '' }}>45 FT</option>
                    </select>
                </div>



                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">F / E</label>
                    <select name="f_e"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="E" {{ old('f_e', $item->f_e) == 'E' ? 'selected' : '' }}>Empty (E)</option>
                        <option value="F" {{ old('f_e', $item->f_e) == 'F' ? 'selected' : '' }}>Full (F)</option>
                    </select>
                </div>

                <!-- Keuangan Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Lain-lain</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            Rp
                        </span>
                        <input type="text"
                               name="uang_jalan"
                               id="uang_jalan"
                               value="{{ old('uang_jalan', number_format($item->uang_jalan, 0, ',', '.')) }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 currency">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select name="status"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="draft" {{ old('status', $item->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $item->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $item->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $item->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan"
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">{{ old('catatan', $item->catatan) }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-150">
                    Batal
                </a>
                <button type="submit"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <i class="fas fa-save mr-2"></i> Perbarui Surat Jalan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Currency formatting
        const currencyInputs = document.querySelectorAll('.currency');
        currencyInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = new Intl.NumberFormat('id-ID').format(this.value.replace(/[^0-9]/g, ''));
            });
        });
        
        // --- Custom Searchable Dropdown for Kontainer ---
        const searchInput = document.getElementById('no_kontainer_search');
        const hiddenInput = document.getElementById('no_kontainer_hidden');
        const dropdown = document.getElementById('no_kontainer_dropdown');
        const options = dropdown.querySelectorAll('.kontainer-option');
        const sizeSelect = document.querySelector('select[name="size"]');

        // Show/Hide dropdown
        searchInput.addEventListener('focus', () => {
            dropdown.classList.remove('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Filter functionality
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            let hasVisible = false;
            
            options.forEach(opt => {
                const text = opt.querySelector('.font-medium').innerText.toLowerCase();
                if (text.includes(filter)) {
                    opt.classList.remove('hidden');
                    hasVisible = true;
                } else {
                    opt.classList.add('hidden');
                }
            });

            dropdown.classList.toggle('hidden', !hasVisible);
            
            // Clear hidden if search is empty
            if (!this.value) {
                hiddenInput.value = '';
            }
        });

        // Selection functionality
        options.forEach(opt => {
            opt.addEventListener('click', function() {
                const val = this.dataset.value;
                const ukuran = this.dataset.ukuran;

                searchInput.value = val;
                hiddenInput.value = val;
                
                if (ukuran) sizeSelect.value = ukuran;

                dropdown.classList.add('hidden');
                
                // Highlight selected
                options.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // Ensure search input value matches hidden on blur if not matches
        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (!this.value) {
                    hiddenInput.value = '';
                }
            }, 200);
        });
    });
</script>
@endpush
@endsection
