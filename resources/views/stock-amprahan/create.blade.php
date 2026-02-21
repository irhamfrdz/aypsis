@extends('layouts.app')

@section('title', 'Tambah Stock Amprahan')
@section('page_title', 'Tambah Stock Amprahan')

@section('content')
@push('styles')
<style>
    .rounded-xl { border-radius: 0.75rem; }
    .focus-ring-premium {
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }
    input:focus, select:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }
    .group:focus-within label i {
        transform: scale(1.1);
    }
    label i {
        transition: transform 0.2s ease;
    }
    .btn-submit-premium {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border: none;
    }
    .btn-submit-premium:hover {
        background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
    }
</style>
@endpush

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6 text-sm text-gray-500">
            <a href="{{ route('stock-amprahan.index') }}" class="hover:text-indigo-600 transition-colors">Stock Amprahan</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium">Tambah Baru</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Informasi Stock Baru</h2>
                
                <form action="{{ route('stock-amprahan.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nomor Bukti --}}
                            <div class="group">
                                <label for="nomor_bukti" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-file-invoice mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Nomor Bukti
                                </label>
                                <input type="text" name="nomor_bukti" id="nomor_bukti" value="{{ old('nomor_bukti') }}" placeholder="Contoh: BUKTI-001" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                @error('nomor_bukti')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tanggal Beli --}}
                            <div class="group">
                                <label for="tanggal_beli" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-calendar-alt mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Tanggal Beli
                                </label>
                                <input type="date" name="tanggal_beli" id="tanggal_beli" value="{{ old('tanggal_beli', date('Y-m-d')) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                @error('tanggal_beli')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Nama Barang --}}
                        <div class="group">
                            <label for="nama_barang" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                <i class="fas fa-box-open mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm" required>
                            @error('nama_barang')
                                <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Type Barang --}}
                            <div class="group">
                                <div class="flex items-center justify-between mb-2">
                                    <label for="master_nama_barang_amprahan_id" class="text-sm font-bold text-gray-700 group-focus-within:text-indigo-600 transition-colors">
                                        <i class="fas fa-tags mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Type Barang <span class="text-red-500">*</span>
                                    </label>
                                    <a href="{{ route('master.nama-barang-amprahan.create') }}" id="add_type_barang_link"
                                       class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                       title="Tambah">
                                        Tambah
                                    </a>
                                </div>
                                <div class="relative">
                                    <div class="dropdown-container-type-barang">
                                        <input type="text" id="search_type_barang" placeholder="Search..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                        <select name="master_nama_barang_amprahan_id" id="master_nama_barang_amprahan_id" required
                                                class="hidden w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                            <option value="">Select an option</option>
                                            @foreach($masterItems as $master)
                                                <option value="{{ $master->id }}" {{ old('master_nama_barang_amprahan_id') == $master->id ? 'selected' : '' }}>
                                                    {{ $master->nama_barang }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_type_barang" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                            {{-- Options will be populated by JavaScript --}}
                                        </div>
                                    </div>
                                </div>
                                @error('master_nama_barang_amprahan_id')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Harga Satuan --}}
                            <div class="group">
                                <label for="harga_satuan" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-money-bill-wave mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Harga Satuan
                                </label>
                                <input type="number" name="harga_satuan" id="harga_satuan" value="{{ old('harga_satuan', 0) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                @error('harga_satuan')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Jumlah --}}
                            <div class="group">
                                <label for="jumlah" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-calculator mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Jumlah <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="jumlah" id="jumlah" value="{{ old('jumlah', 0) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm" required>
                                @error('jumlah')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Satuan --}}
                            <div class="group">
                                <label for="satuan" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="fas fa-tag mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Satuan
                                </label>
                                <input type="text" name="satuan" id="satuan" value="{{ old('satuan') }}" placeholder="rim, pack, pcs" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                @error('satuan')
                                    <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Harga Total --}}
                        <div class="group">
                            <label for="harga_total" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                <i class="fas fa-calculator mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Harga Total
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="text" id="harga_total" readonly class="block w-full pl-12 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-700 cursor-not-allowed shadow-sm font-semibold" value="0">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>Otomatis dihitung dari Harga Satuan × Jumlah
                            </p>
                        </div>

                        {{-- Lokasi --}}
                        <div class="group">
                            <label for="lokasi" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Lokasi Penyimpanan
                            </label>
                            <div class="relative">
                                <div class="dropdown-container-lokasi">
                                    <input type="text" id="search_lokasi" placeholder="Search Location..." autocomplete="off" value="{{ old('lokasi') }}"
                                           class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                    <select name="lokasi" id="lokasi" 
                                            class="hidden w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                        <option value="">Select a location</option>
                                        @foreach($gudangItems as $gudang)
                                            <option value="{{ $gudang->nama_gudang }}" {{ old('lokasi') == $gudang->nama_gudang ? 'selected' : '' }}>
                                                {{ $gudang->nama_gudang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_lokasi" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        {{-- Options will be populated by JavaScript --}}
                                    </div>
                                </div>
                            </div>
                            @error('lokasi')
                                <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div class="group">
                            <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                <i class="fas fa-sticky-note mr-2 text-gray-400 group-focus-within:text-indigo-500"></i>Keterangan
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Catatan tambahan jika ada..." class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm resize-none">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-2 text-xs font-medium text-red-500 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Langsung Pakai Checklist --}}
                        <div class="pt-4">
                            <label class="flex items-center space-x-3 cursor-pointer group w-fit">
                                <div class="relative">
                                    <input type="checkbox" name="is_langsung_pakai" id="is_langsung_pakai" value="1" {{ old('is_langsung_pakai') ? 'checked' : '' }} class="peer hidden">
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-lg group-hover:border-indigo-500 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all duration-200 flex items-center justify-center">
                                        <i class="fas fa-check text-white text-xs scale-0 peer-checked:scale-100 transition-transform duration-200"></i>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-gray-700 group-hover:text-indigo-600 transition-colors">Langsung Pakai (Gunakan Barang Ini)</span>
                            </label>
                        </div>

                        {{-- Usage Fields Container --}}
                        <div id="usage_fields_container" class="{{ old('is_langsung_pakai') ? '' : 'hidden' }} mt-6 p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100 space-y-6">
                            <h3 class="text-sm font-bold text-indigo-800 flex items-center uppercase tracking-wider">
                                <i class="fas fa-wrench mr-2"></i>Informasi Pemakaian Langsung
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Jumlah Pakai --}}
                                <div class="group">
                                    <label for="jumlah_pakai" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                        Jumlah Pakai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="jumlah_pakai" id="jumlah_pakai" value="{{ old('jumlah_pakai') }}" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                    @error('jumlah_pakai')
                                        <p class="mt-2 text-xs font-medium text-red-500 items-center flex"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Tanggal Pakai --}}
                                <div class="group">
                                    <label for="tanggal_pengambilan" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                        Tanggal Pakai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_pengambilan" id="tanggal_pengambilan" value="{{ old('tanggal_pengambilan', date('Y-m-d')) }}" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                    @error('tanggal_pengambilan')
                                        <p class="mt-2 text-xs font-medium text-red-500 items-center flex"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Penerima --}}
                            <div class="group">
                                <label for="penerima_id" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                    Penerima <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="dropdown-container-penerima">
                                        <input type="text" id="search_penerima" placeholder="Cari penerima..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <select name="penerima_id" id="penerima_id" class="hidden">
                                            <option value="">-- Pilih Penerima --</option>
                                            @foreach($karyawans as $k)
                                                <option value="{{ $k->id }}" {{ old('penerima_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_penerima" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                </div>
                                @error('penerima_id')
                                    <p class="mt-2 text-xs font-medium text-red-500 items-center flex"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Mobil --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Mobil</label>
                                    <div class="dropdown-container-mobil relative">
                                        <input type="text" id="search_mobil" placeholder="Cari mobil..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <select name="mobil_id" id="mobil_id" class="hidden">
                                            <option value="">-- Pilih Mobil --</option>
                                            @foreach($mobils as $m)
                                                <option value="{{ $m->id }}" {{ old('mobil_id') == $m->id ? 'selected' : '' }}>{{ $m->nomor_polisi }} ({{ $m->merek }})</option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_mobil" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                    @error('mobil_id')
                                        <p class="mt-2 text-xs font-medium text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Kapal --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kapal</label>
                                    <div class="dropdown-container-kapal relative">
                                        <input type="text" id="search_kapal" placeholder="Cari kapal..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <select name="kapal_id" id="kapal_id" class="hidden">
                                            <option value="">-- Pilih Kapal --</option>
                                            @foreach($kapals as $k)
                                                <option value="{{ $k->id }}" {{ old('kapal_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kapal }}</option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_kapal" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                    @error('kapal_id')
                                        <p class="mt-2 text-xs font-medium text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Alat Berat --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Alat Berat</label>
                                    <div class="dropdown-container-alat-berat relative">
                                        <input type="text" id="search_alat_berat" placeholder="Cari alat berat..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <select name="alat_berat_id" id="alat_berat_id" class="hidden">
                                            <option value="">-- Pilih Alat Berat --</option>
                                            @foreach($alatBerats as $ab)
                                                <option value="{{ $ab->id }}" {{ old('alat_berat_id') == $ab->id ? 'selected' : '' }}>{{ $ab->kode_alat }} - {{ $ab->nama }}</option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_alat_berat" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                    @error('alat_berat_id')
                                        <p class="mt-2 text-xs font-medium text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Keterangan Pakai --}}
                            <div class="group">
                                <label for="keterangan_pakai" class="block text-sm font-bold text-gray-700 mb-2">Keterangan Pakai <span class="text-red-500">*</span></label>
                                <textarea name="keterangan_pakai" id="keterangan_pakai" rows="2" placeholder="Tujuan pemakaian..." class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm resize-none">{{ old('keterangan_pakai') }}</textarea>
                                @error('keterangan_pakai')
                                    <p class="mt-2 text-xs font-medium text-red-500 items-center flex"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                        <a href="{{ route('stock-amprahan.index') }}" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors">Batal</a>
                        <button type="submit" class="btn-submit-premium px-10 py-3 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95">
                            <i class="fas fa-save mr-2"></i>Simpan Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to create searchable dropdown
    function createSearchableDropdown(config) {
        const selectElement = document.getElementById(config.selectId);
        const searchInput = document.getElementById(config.searchId);
        const dropdownOptions = document.getElementById(config.dropdownId);
        let originalOptions = Array.from(selectElement.options);

        // Initially populate dropdown options
        populateDropdown(originalOptions);

        // Show dropdown when search input is focused or clicked
        searchInput.addEventListener('focus', function() {
            dropdownOptions.classList.remove('hidden');
        });

        searchInput.addEventListener('click', function() {
            dropdownOptions.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filteredOptions = originalOptions.filter(option => {
                if (option.value === '') return true;
                return option.text.toLowerCase().includes(searchTerm);
            });
            populateDropdown(filteredOptions);
            dropdownOptions.classList.remove('hidden');
        });

        // Populate dropdown with options
        function populateDropdown(options) {
            dropdownOptions.innerHTML = '';
            options.forEach(option => {
                const div = document.createElement('div');
                div.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                div.textContent = option.text;
                div.setAttribute('data-value', option.value);

                div.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;

                    // Set the select value
                    selectElement.value = value;

                    // Update search input
                    if (value === '') {
                        searchInput.value = '';
                        searchInput.placeholder = 'Search...';
                    } else {
                        searchInput.value = text;
                    }

                    // Hide dropdown
                    dropdownOptions.classList.add('hidden');

                    // Trigger change event
                    selectElement.dispatchEvent(new Event('change'));
                });

                dropdownOptions.appendChild(div);
            });
        }

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.' + config.containerClass)) {
                dropdownOptions.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownOptions.classList.add('hidden');
            }
        });
    }

    // Initialize Type Barang dropdown
    createSearchableDropdown({
        selectId: 'master_nama_barang_amprahan_id',
        searchId: 'search_type_barang',
        dropdownId: 'dropdown_options_type_barang',
        containerClass: 'dropdown-container-type-barang'
    });

    // Initialize Lokasi dropdown
    createSearchableDropdown({
        selectId: 'lokasi',
        searchId: 'search_lokasi',
        dropdownId: 'dropdown_options_lokasi',
        containerClass: 'dropdown-container-lokasi'
    });

    // Initialize Usage Fields Dropdowns
    createSearchableDropdown({
        selectId: 'penerima_id',
        searchId: 'search_penerima',
        dropdownId: 'dropdown_options_penerima',
        containerClass: 'dropdown-container-penerima'
    });

    createSearchableDropdown({
        selectId: 'mobil_id',
        searchId: 'search_mobil',
        dropdownId: 'dropdown_options_mobil',
        containerClass: 'dropdown-container-mobil'
    });

    createSearchableDropdown({
        selectId: 'kapal_id',
        searchId: 'search_kapal',
        dropdownId: 'dropdown_options_kapal',
        containerClass: 'dropdown-container-kapal'
    });

    createSearchableDropdown({
        selectId: 'alat_berat_id',
        searchId: 'search_alat_berat',
        dropdownId: 'dropdown_options_alat_berat',
        containerClass: 'dropdown-container-alat-berat'
    });

    // Langsung Pakai Toggle Logic
    const isLangsungPakaiCheckbox = document.getElementById('is_langsung_pakai');
    const usageFieldsContainer = document.getElementById('usage_fields_container');
    const jumlahPakaiInput = document.getElementById('jumlah_pakai');
    const jumlahStockInput = document.getElementById('jumlah');
    const keteranganPakaiTextarea = document.getElementById('keterangan_pakai');
    const keteranganStockTextarea = document.getElementById('keterangan');

    if (isLangsungPakaiCheckbox) {
        isLangsungPakaiCheckbox.addEventListener('change', function() {
            if (this.checked) {
                usageFieldsContainer.classList.remove('hidden');
                // Auto-fill jumlah pakai with jumlah stock if empty
                if (!jumlahPakaiInput.value && jumlahStockInput.value) {
                    jumlahPakaiInput.value = jumlahStockInput.value;
                }
                // Auto-fill keterangan pakai with keterangan stock if empty
                if (!keteranganPakaiTextarea.value && keteranganStockTextarea.value) {
                    keteranganPakaiTextarea.value = keteranganStockTextarea.value;
                }
            } else {
                usageFieldsContainer.classList.add('hidden');
            }
        });
    }

    // Update jumlah_pakai when jumlah stock changes (if checkbox is checked and values were synced)
    if (jumlahStockInput && jumlahPakaiInput) {
        jumlahStockInput.addEventListener('input', function() {
            if (isLangsungPakaiCheckbox && isLangsungPakaiCheckbox.checked) {
                // Only sync if they were already same or jumlah_pakai was empty
                if (!jumlahPakaiInput.value || jumlahPakaiInput.value == this.oldValue) {
                    jumlahPakaiInput.value = this.value;
                }
            }
            this.oldValue = this.value;
        });
    }

    // Handle Type Barang "Tambah" link to pass search parameter
    const addTypeBarangLink = document.getElementById('add_type_barang_link');
    const searchTypeBarangInput = document.getElementById('search_type_barang');
    if (addTypeBarangLink && searchTypeBarangInput) {
        addTypeBarangLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTypeBarangInput.value.trim();
            let url = "{{ route('master.nama-barang-amprahan.create') }}";

            // Add popup parameter and nama_barang if available
            const params = new URLSearchParams();
            params.append('popup', '1');

            if (searchValue) {
                params.append('search', searchValue);
            }

            url += '?' + params.toString();

            // Open as popup window with specific dimensions
            const popup = window.open(
                url,
                'addTypeBarang',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }

    // Function to calculate and update total price
    function updateHargaTotal() {
        const hargaSatuan = parseFloat(document.getElementById('harga_satuan').value) || 0;
        const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
        const hargaTotal = hargaSatuan * jumlah;
        
        // Format number with thousand separators
        const formattedTotal = hargaTotal.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
        
        const hargaTotalInput = document.getElementById('harga_total');
        if (hargaTotalInput) {
            hargaTotalInput.value = formattedTotal;
        }
    }

    // Add event listeners to harga_satuan and jumlah
    const hargaSatuanInput = document.getElementById('harga_satuan');
    const jumlahInput = document.getElementById('jumlah');
    
    if (hargaSatuanInput && jumlahInput) {
        hargaSatuanInput.addEventListener('input', updateHargaTotal);
        jumlahInput.addEventListener('input', updateHargaTotal);
        
        // Calculate on page load if there are old values
        updateHargaTotal();
    }
});
</script>
@endpush
@endsection
