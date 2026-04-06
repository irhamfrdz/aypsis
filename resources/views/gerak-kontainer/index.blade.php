@extends('layouts.app')

@section('title', 'Gerak Kontainer')
@section('page_title', 'Gerak Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 px-8 py-4 text-white">
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-inner">
                        <svg class="w-6 h-6 text-white" style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">Gerak Kontainer</h1>
                        <p class="text-blue-100 text-xs">Pindahkan kontainer antar gudang secara manual.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('gerak-kontainer.store') }}" method="POST" class="p-8">
                @csrf
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center shadow-sm animate-fadeIn">
                        <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="space-y-6">
                    <!-- Container Search -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Nomor Kontainer <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="kontainer_id" name="kontainer_id" required 
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 vanilla-searchable invisible h-0 overflow-hidden">
                                <option value="">-- Ketik Nomor Kontainer --</option>
                            </select>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 font-medium italic flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Data diambil dari Master Kontainer & Stock Kontainer Aktif.
                        </p>
                        @error('kontainer_id') <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Origin Warehouse (Auto-filled) -->
                    <div id="asal_gudang_wrapper" class="hidden animate-fadeIn">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 italic text-blue-600">Asal Gudang Saat Ini</label>
                        <div class="relative">
                            <input type="text" id="asal_gudang_display" readonly 
                                   class="w-full border-2 border-blue-100 bg-blue-50/50 rounded-xl px-4 py-3 text-gray-600 font-semibold cursor-not-allowed shadow-inner" 
                                   placeholder="Pilih kontainer dahulu...">
                            <input type="hidden" name="asal_name" id="asal_name_hidden">
                        </div>
                    </div>

                    <!-- Target Warehouse -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Gudang Tujuan <span class="text-red-500">*</span></label>
                        <select name="gudang_id" required 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-gray-700 shadow-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]"
                                style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E');">
                            <option value="">-- Pilih Gudang --</option>
                            @foreach($gudangs as $g)
                                <option value="{{ $g->id }}" {{ old('gudang_id') == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }} ({{ $g->lokasi }})</option>
                            @endforeach
                        </select>
                        @error('gudang_id') <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pergerakan <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-gray-700 shadow-sm">
                            @error('tanggal') <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Keterangan (Optional) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                            <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Opsional..."
                                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-gray-700 shadow-sm">
                            @error('keterangan') <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex justify-center items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            SIMPAN PERGERAKAN
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="mt-6 text-center">
            <a href="{{ route('history-kontainer.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold underline underline-offset-4 flex items-center justify-center group">
                Lihat History Pergerakan 
                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script>
    class VanillaRemoteSearchableSelect {
        constructor(id, searchUrl) {
            this.id = id;
            this.searchUrl = searchUrl;
            this.select = document.getElementById(id);
            this.init();
        }
        
        init() {
            this.container = document.createElement('div');
            this.container.className = 'relative vanilla-select-host w-full';
            
            this.trigger = document.createElement('div');
            this.trigger.className = 'w-full border border-gray-300 rounded-xl px-4 py-3 bg-white flex justify-between items-center text-sm transition-all duration-200 cursor-pointer focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm';
            this.trigger.tabIndex = 0;
            this.trigger.innerHTML = `<span class="current-value truncate mr-1">-- Ketik Nomor Kontainer --</span><svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200 dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
            
            this.dropdown = document.createElement('div');
            this.dropdown.className = 'absolute z-[1050] w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl hidden overflow-hidden scale-95 opacity-0 transition-all duration-200 transform origin-top';
            
            const searchWrapper = document.createElement('div');
            searchWrapper.className = 'p-3 border-b border-gray-100 bg-gray-50 sticky top-0';
            this.search = document.createElement('input');
            this.search.type = 'text';
            this.search.placeholder = 'Ketik minimal 3 karakter...';
            this.search.className = 'w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all';
            searchWrapper.appendChild(this.search);
            
            this.list = document.createElement('div');
            this.list.className = 'max-h-64 overflow-y-auto custom-scrollbar';
            
            this.dropdown.appendChild(searchWrapper);
            this.dropdown.appendChild(this.list);
            this.container.appendChild(this.trigger);
            this.container.appendChild(this.dropdown);
            
            this.select.parentNode.insertBefore(this.container, this.select);
            
            this.trigger.onclick = (e) => { e.stopPropagation(); this.toggle(); };
            this.search.onclick = (e) => e.stopPropagation();
            
            let debounceTimer;
            this.search.oninput = (e) => {
                const val = e.target.value.toLowerCase();
                clearTimeout(debounceTimer);
                if (val.length >= 2) {
                    debounceTimer = setTimeout(() => this.performSearch(val), 300);
                } else {
                    this.renderList([]);
                }
            };
            
            document.addEventListener('click', (e) => {
                if (!this.container.contains(e.target)) this.close();
            });
        }
        
        async performSearch(term) {
            this.list.innerHTML = `<div class="px-4 py-4 text-sm text-gray-500 flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mencari...</div>`;
            try {
                const response = await fetch(`${this.searchUrl}?term=${term}`);
                const results = await response.json();
                this.renderList(results);
            } catch (error) {
                console.error("Search failed", error);
                this.list.innerHTML = '<div class="px-4 py-3 text-sm text-red-500">Gagal mengambil data</div>';
            }
        }
        
        renderList(results) {
            this.list.innerHTML = '';
            if (results.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-4 text-sm text-gray-400 italic text-center';
                empty.textContent = this.search.value.length < 2 ? 'Ketik minimal 2 karakter...' : 'Tidak ditemukan';
                this.list.appendChild(empty);
                return;
            }
            
            results.forEach(res => {
                const item = document.createElement('div');
                item.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer text-sm text-gray-700 transition-colors border-b border-gray-50 last:border-0';
                item.textContent = res.text;
                item.onclick = (e) => {
                    e.stopPropagation();
                    this.selectValue(res);
                };
                this.list.appendChild(item);
            });
        }
        
        selectValue(res) {
            let opt = this.select.querySelector(`option[value="${res.id}"]`);
            if (!opt) {
                opt = document.createElement('option');
                opt.value = res.id;
                opt.text = res.text;
                this.select.appendChild(opt);
            }
            this.select.value = res.id;
            this.trigger.querySelector('.current-value').textContent = res.text;
            
            // Populating Origin Warehouse Display
            const asalWrapper = document.getElementById('asal_gudang_wrapper');
            const asalDisplay = document.getElementById('asal_gudang_display');
            const asalHidden = document.getElementById('asal_name_hidden');
            
            if (res.asal_name) {
                asalDisplay.value = res.asal_name;
                asalHidden.value = res.asal_name;
                asalWrapper.classList.remove('hidden');
            } else {
                asalWrapper.classList.add('hidden');
            }

            this.close();
        }
        
        toggle() { this.dropdown.classList.contains('hidden') ? this.open() : this.close(); }
        
        open() {
            this.dropdown.classList.remove('hidden');
            this.container.classList.add('z-50');
            this.trigger.classList.add('ring-2', 'ring-blue-500', 'border-blue-500');
            this.trigger.querySelector('.dropdown-arrow').classList.add('rotate-180');
            setTimeout(() => {
                this.dropdown.classList.remove('scale-95', 'opacity-0');
                this.dropdown.classList.add('scale-100', 'opacity-100');
                this.search.focus();
            }, 10);
        }
        
        close() {
            this.dropdown.classList.add('scale-95', 'opacity-0');
            this.dropdown.classList.remove('scale-100', 'opacity-100');
            this.container.classList.remove('z-50');
            this.trigger.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500');
            this.trigger.querySelector('.dropdown-arrow').classList.remove('rotate-180');
            setTimeout(() => this.dropdown.classList.add('hidden'), 200);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        new VanillaRemoteSearchableSelect('kontainer_id', "{{ route('gerak-kontainer.search') }}");
    });
</script>
@endsection
