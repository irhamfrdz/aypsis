@extends('layouts.app')

@section('title', 'Edit Stock Amprahan')
@section('page_title', 'Edit Stock Amprahan')

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
    /* Make form controls match the Create form appearance */
    .max-w-2xl .p-8 input,
    .max-w-2xl .p-8 select,
    .max-w-2xl .p-8 textarea {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        background: #f8fafc; /* bg-gray-50 */
        border: 1px solid #e5e7eb; /* border-gray-200 */
        border-radius: 0.75rem; /* rounded-xl */
        color: #374151; /* text-gray-700 */
        box-shadow: none;
    }
    .max-w-2xl .p-8 input[readonly],
    .max-w-2xl .p-8 input[disabled],
    .max-w-2xl .p-8 textarea[readonly],
    .max-w-2xl .p-8 select[disabled] {
        background: #f3f4f6; /* lighter when readonly/disabled */
        cursor: not-allowed;
    }
    .hidden { display: none !important; }
</style>
@endpush
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6 text-sm text-gray-500">
            <a href="{{ route('stock-amprahan.index') }}" class="hover:text-indigo-600 transition-colors">Stock Amprahan</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium">Edit Data</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Ubah Informasi Stock</h2>
                
                <form action="{{ route('stock-amprahan.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Nomor Bukti --}}
                            <div>
                                <label for="nomor_bukti" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Bukti</label>
                                <input type="text" name="nomor_bukti" id="nomor_bukti" value="{{ old('nomor_bukti', $item->nomor_bukti) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('nomor_bukti')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tanggal Beli --}}
                            <div>
                                <label for="tanggal_beli" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Beli</label>
                                <input type="date" name="tanggal_beli" id="tanggal_beli" value="{{ old('tanggal_beli', $item->tanggal_beli ? \Carbon\Carbon::parse($item->tanggal_beli)->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('tanggal_beli')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Type Amprahan --}}
                            <div>
                                <label for="type_amprahan" class="block text-sm font-semibold text-gray-700 mb-1">Type Amprahan <span class="text-red-500">*</span></label>
                                <select name="type_amprahan" id="type_amprahan" required 
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                    <option value="Stock" {{ old('type_amprahan', $item->type_amprahan) == 'Stock' ? 'selected' : '' }}>Stock</option>
                                    <option value="Pemakaian" {{ old('type_amprahan', $item->type_amprahan) == 'Pemakaian' ? 'selected' : '' }}>Pemakaian</option>
                                    <option value="Perbaikan" {{ old('type_amprahan', $item->type_amprahan) == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                    <option value="Perlengkapan" {{ old('type_amprahan', $item->type_amprahan) == 'Perlengkapan' ? 'selected' : '' }}>Perlengkapan</option>
                                    <option value="Transportasi" {{ old('type_amprahan', $item->type_amprahan) == 'Transportasi' ? 'selected' : '' }}>Transportasi</option>
                                </select>
                                @error('type_amprahan')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Nama Barang --}}
                        <div>
                            <label for="nama_barang" class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '')) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" required>
                            @error('nama_barang')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Type Barang --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="master_nama_barang_amprahan_id" class="text-sm font-semibold text-gray-700">Type Barang <span class="text-red-500">*</span></label>
                                <a href="{{ route('master.nama-barang-amprahan.create') }}" id="add_type_barang_link"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-type-barang">
                                    <input type="text" id="search_type_barang" placeholder="Search..." autocomplete="off"
                                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                    <select name="master_nama_barang_amprahan_id" id="master_nama_barang_amprahan_id" required
                                            class="hidden w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                        <option value="">Select an option</option>
                                        @foreach($masterItems as $master)
                                            <option value="{{ $master->id }}" {{ old('master_nama_barang_amprahan_id', $item->master_nama_barang_amprahan_id) == $master->id ? 'selected' : '' }}>
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
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                            {{-- Harga Satuan --}}
                            <div>
                                <label for="harga_satuan" class="block text-sm font-semibold text-gray-700 mb-1">Harga Satuan</label>
                                <input type="number" name="harga_satuan" id="harga_satuan" value="{{ old('harga_satuan', $item->harga_satuan ?? 0) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('harga_satuan')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Adjustment --}}
                            <div>
                                <label for="adjustment" class="block text-sm font-semibold text-gray-700 mb-1">Adjustment</label>
                                <input type="number" name="adjustment" id="adjustment" value="{{ old('adjustment', $item->adjustment ?? 0) }}" placeholder="Contoh: 1000 atau -1000" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('adjustment')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Jumlah --}}
                            <div>
                                <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="jumlah" id="jumlah" value="{{ old('jumlah', $item->jumlah) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" required>
                                <p class="mt-1 text-[10px] text-gray-500 italic">Untuk pemakaian langsung, masukkan jumlah TOTAL pembelian.</p>
                                @error('jumlah')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Satuan --}}
                            <div>
                                <label for="satuan" class="block text-sm font-semibold text-gray-700 mb-1">Satuan</label>
                                <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $item->satuan) }}" placeholder="Contoh: rim, pack, pcs" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('satuan')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Harga Total --}}
                        <div>
                            <label for="harga_total" class="block text-sm font-semibold text-gray-700 mb-1">Harga Total</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                <input type="text" id="harga_total" readonly class="w-full pl-10 rounded-lg bg-gray-100 border-gray-300 text-gray-700 cursor-not-allowed font-semibold" value="0">
                            </div>
                            <p class="mt-1 text-[10px] text-gray-500 italic">Otomatis dihitung dari (Harga Satuan × Jumlah) + Adjustment</p>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label for="lokasi" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Penyimpanan</label>
                            <div class="relative">
                                <div class="dropdown-container-lokasi">
                                    <input type="text" id="search_lokasi" placeholder="Search Location..." autocomplete="off"
                                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                    <select name="lokasi" id="lokasi" 
                                            class="hidden w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                        <option value="">Select a location</option>
                                        @foreach($gudangItems as $gudang)
                                            <option value="{{ $gudang->nama_gudang }}" {{ old('lokasi', $item->lokasi) == $gudang->nama_gudang ? 'selected' : '' }}>
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
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Catatan tambahan jika ada..." class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">{{ old('keterangan', $item->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Langsung Pakai Checklist --}}
                        <div class="pt-4">
                            <label class="flex items-center space-x-3 cursor-pointer group w-fit">
                                <div class="relative">
                                    <input type="checkbox" name="is_langsung_pakai" id="is_langsung_pakai" value="1" {{ (old('is_langsung_pakai') || $directUsage) ? 'checked' : '' }} class="peer hidden">
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-lg group-hover:border-indigo-500 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all duration-200 flex items-center justify-center">
                                        <i class="fas fa-check text-white text-xs scale-0 peer-checked:scale-100 transition-transform duration-200"></i>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-gray-700 group-hover:text-indigo-600 transition-colors">Langsung Pakai (Gunakan Barang Ini)</span>
                            </label>
                        </div>

                        {{-- Usage Fields Container --}}
                        <div id="usage_fields_container" class="{{ (old('is_langsung_pakai') || $directUsage) ? '' : 'hidden' }} mt-6 p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100 space-y-6">
                            <h3 class="text-sm font-bold text-indigo-800 flex items-center uppercase tracking-wider">
                                <i class="fas fa-wrench mr-2"></i>Informasi Pemakaian Langsung
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Jumlah Pakai --}}
                                <div class="group">
                                    <label for="jumlah_pakai" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                        Jumlah Pakai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="jumlah_pakai" id="jumlah_pakai" value="{{ old('jumlah_pakai', $directUsage->jumlah ?? '') }}" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                    @error('jumlah_pakai')
                                        <p class="mt-2 text-xs font-medium text-red-500 items-center flex"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Tanggal Pakai --}}
                                <div class="group">
                                    <label for="tanggal_pengambilan" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                        Tanggal Pakai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_pengambilan" id="tanggal_pengambilan" value="{{ old('tanggal_pengambilan', $directUsage && $directUsage->tanggal_pengambilan ? \Carbon\Carbon::parse($directUsage->tanggal_pengambilan)->format('Y-m-d') : date('Y-m-d')) }}" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
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
                                        <select name="penerima_id" id="penerima_id" style="display: none !important;">
                                            <option value="">-- Pilih Penerima --</option>
                                            @foreach($karyawans as $k)
                                                <option value="{{ $k->id }}" {{ old('penerima_id', $directUsage->penerima_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_penerima" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                </div>
                                @error('penerima_id')
                                    <p class="mt-2 text-xs font-medium text-red-500 items-center flex"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {{-- Kendaraan --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kendaraan</label>
                                    <div class="dropdown-container-kendaraan relative">
                                        <input type="text" id="search_kendaraan" placeholder="Cari kendaraan..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <select name="kendaraan_id" id="kendaraan_id" style="display: none !important;">
                                            <option value="">-- Pilih Kendaraan --</option>
                                            @foreach($kendaraans as $m)
                                                <option value="{{ $m->id }}" {{ (old('kendaraan_id') ?? ($directUsage->kendaraan_id ?? '')) == $m->id ? 'selected' : '' }}>{{ $m->nomor_polisi }} ({{ $m->merek }})</option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_kendaraan" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                    @error('kendaraan_id')
                                        <p class="mt-2 text-xs font-medium text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Truck --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Truck</label>
                                    <div class="dropdown-container-truck relative">
                                        <input type="text" id="search_truck" placeholder="Cari truck..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <select name="truck_id" id="truck_id" style="display: none !important;">
                                            <option value="">-- Pilih Truck --</option>
                                            @foreach($kendaraans as $m)
                                                <option value="{{ $m->id }}" {{ (old('truck_id') ?? ($directUsage->truck_id ?? '')) == $m->id ? 'selected' : '' }}>{{ $m->nomor_polisi }} ({{ $m->merek }})</option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_truck" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                    @error('truck_id')
                                        <p class="mt-2 text-xs font-medium text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Buntut --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Buntut</label>
                                    <div class="dropdown-container-buntut relative">
                                        <input type="text" id="search_buntut" placeholder="Cari buntut..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <select name="buntut_id" id="buntut_id" style="display: none !important;">
                                            <option value="">-- Pilih Buntut --</option>
                                            @foreach($kendaraans as $m)
                                                <option value="{{ $m->id }}" {{ old('buntut_id', $directUsage->buntut_id ?? '') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->no_kir ?: ($m->nomor_polisi ?: 'No KIR: -') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_buntut" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                    @error('buntut_id')
                                        <p class="mt-2 text-xs font-medium text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Kapal --}}
                                <div class="group">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kapal</label>
                                    <div class="dropdown-container-kapal relative">
                                        <input type="text" id="search_kapal" placeholder="Cari kapal..." autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <select name="kapal_id" id="kapal_id" style="display: none !important;">
                                            <option value="">-- Pilih Kapal --</option>
                                            @foreach($kapals as $k)
                                                <option value="{{ $k->id }}" {{ old('kapal_id', $directUsage->kapal_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_kapal }}</option>
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
                                        <select name="alat_berat_id" id="alat_berat_id" style="display: none !important;">
                                            <option value="">-- Pilih Alat Berat --</option>
                                            @foreach($alatBerats as $ab)
                                                <option value="{{ $ab->id }}" {{ old('alat_berat_id', $directUsage->alat_berat_id ?? '') == $ab->id ? 'selected' : '' }}>{{ $ab->kode_alat }} - {{ $ab->nama }}{{ $ab->merk ? ' - ' . $ab->merk : '' }}</option>
                                            @endforeach
                                        </select>
                                        <div id="dropdown_options_alat_berat" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden shadow-xl mt-1"></div>
                                    </div>
                                    @error('alat_berat_id')
                                        <p class="mt-2 text-xs font-medium text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                                {{-- Kantor --}}
                                <div class="group">
                                    <label for="kantor" class="block text-sm font-bold text-gray-700 mb-2">Kantor</label>
                                    <div class="relative dropdown-container-kantor">
                                        <input type="text" name="kantor" id="kantor" 
                                               value="{{ old('kantor', $directUsage->kantor ?? '') }}" 
                                               placeholder="Ketik atau pilih..."
                                               autocomplete="off"
                                               class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                        <div id="dropdown_options_kantor" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b-xl max-h-60 overflow-y-auto hidden shadow-xl mt-1 border-t-0">
                                            <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 option-item text-sm" data-value="MONTIR GARASI PLUIT">MONTIR GARASI PLUIT</div>
                                            <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 option-item text-sm" data-value="MONTIR PELABUHAN">MONTIR PELABUHAN</div>
                                            <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 option-item text-sm" data-value="TUKANG LAS GARASI">TUKANG LAS GARASI</div>
                                            <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 option-item text-sm" data-value="TUKANG TAMBAL BAN GARASI">TUKANG TAMBAL BAN GARASI</div>
                                            <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 option-item text-sm" data-value="KENEK MONTIR GARASI">KENEK MONTIR GARASI</div>
                                            <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 option-item text-sm" data-value="KANTOR GARASI PLUIT">KANTOR GARASI PLUIT</div>
                                            <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 option-item text-sm" data-value="KANTOR PELABUHAN">KANTOR PELABUHAN</div>
                                            <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 option-item text-sm" data-value="KANTOR GARASI SEMUT">KANTOR GARASI SEMUT</div>
                                        </div>
                                    </div>
                                    @error('kantor')
                                        <p class="mt-2 text-xs font-medium text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Kilometer --}}
                                <div class="group">
                                    <label for="kilometer" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                                        Kilometer (Opsional)
                                    </label>
                                    <input type="number" step="0.01" name="kilometer" id="kilometer" value="{{ old('kilometer', $directUsage->kilometer ?? '') }}" class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                                    @error('kilometer')
                                        <p class="mt-2 text-xs font-medium text-red-500 items-center flex"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Keterangan Pakai --}}
                            <div class="group">
                                <label for="keterangan_pakai" class="block text-sm font-bold text-gray-700 mb-2">Keterangan Pakai <span class="text-red-500">*</span></label>
                                <textarea name="keterangan_pakai" id="keterangan_pakai" rows="2" placeholder="Tujuan pemakaian..." class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm resize-none">{{ old('keterangan_pakai', $directUsage->keterangan ?? '') }}</textarea>
                                @error('keterangan_pakai')
                                    <p class="mt-2 text-xs font-medium text-red-500 items-center flex"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                        <a href="{{ route('stock-amprahan.index') }}" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors">Batal</a>
                        <button type="submit" class="btn-submit-premium px-10 py-3 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95">
                            <i class="fas fa-save mr-2"></i>Perbarui Stock
                        </button>
                    </div>
                </form>
            </div>
            
            {{-- Audit Info --}}
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-4 text-xs text-gray-400">
                <div>
                    <span class="font-semibold block uppercase">Dibuat Oleh:</span>
                    {{ $item->createdBy->name ?? '-' }} ({{ $item->created_at->format('d/m/Y H:i') }})
                </div>
                <div>
                    <span class="font-semibold block uppercase">Diperbarui Oleh:</span>
                    {{ $item->updatedBy->name ?? '-' }} ({{ $item->updated_at->format('d/m/Y H:i') }})
                </div>
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
        
        if (!selectElement || !searchInput || !dropdownOptions) return;

        let originalOptions = Array.from(selectElement.options);

        // Get selected value and set it in search input
        const selectedOption = Array.from(selectElement.options).find(opt => opt.selected);
        if (selectedOption && selectedOption.value !== '') {
            searchInput.value = selectedOption.text;
        }

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
        selectId: 'kendaraan_id',
        searchId: 'search_kendaraan',
        dropdownId: 'dropdown_options_kendaraan',
        containerClass: 'dropdown-container-kendaraan'
    });

    createSearchableDropdown({
        selectId: 'truck_id',
        searchId: 'search_truck',
        dropdownId: 'dropdown_options_truck',
        containerClass: 'dropdown-container-truck'
    });
 
    createSearchableDropdown({
        selectId: 'buntut_id',
        searchId: 'search_buntut',
        dropdownId: 'dropdown_options_buntut',
        containerClass: 'dropdown-container-buntut'
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

    // Logic for Kantor Manual Input with Suggestions
    (function initKantorDropdown() {
        const input = document.getElementById('kantor');
        const optionsDiv = document.getElementById('dropdown_options_kantor');
        const container = optionsDiv.closest('.dropdown-container-kantor');
        const options = Array.from(optionsDiv.querySelectorAll('.option-item'));

        input.addEventListener('focus', () => {
            optionsDiv.classList.remove('hidden');
        });
        
        input.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            let visibleCount = 0;
            options.forEach(opt => {
                const text = opt.textContent.toLowerCase();
                if (text.includes(term)) {
                    opt.style.display = 'block';
                    visibleCount++;
                } else {
                    opt.style.display = 'none';
                }
            });
            
            if (visibleCount > 0) {
                optionsDiv.classList.remove('hidden');
            } else {
                optionsDiv.classList.add('hidden');
            }
        });

        options.forEach(opt => {
            opt.addEventListener('click', function() {
                input.value = this.getAttribute('data-value');
                optionsDiv.classList.add('hidden');
            });
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                optionsDiv.classList.add('hidden');
            }
        });
    })();

    // Handle Type Barang "Tambah" link to pass search parameter
    const addTypeBarangLink = document.getElementById('add_type_barang_link');
    const searchTypeBarangInput = document.getElementById('search_type_barang');
    if (addTypeBarangLink && searchTypeBarangInput) {
        addTypeBarangLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTypeBarangInput.value.trim();
            let url = "{{ route('master.nama-barang-amprahan.create', [], false) }}";

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

    // Function to update total price
    function updateHargaTotal() {
        const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
        const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
        const adjustment = parseFloat(document.getElementById('adjustment').value) || 0;
        const total = (harga * jumlah) + adjustment;
        
        document.getElementById('harga_total').value = total.toLocaleString('id-ID');
    }

    // Attach listeners for total price calculation
    if (document.getElementById('harga_satuan')) document.getElementById('harga_satuan').addEventListener('input', updateHargaTotal);
    if (document.getElementById('jumlah')) document.getElementById('jumlah').addEventListener('input', updateHargaTotal);
    if (document.getElementById('adjustment')) document.getElementById('adjustment').addEventListener('input', updateHargaTotal);
    
    // Initial calculation
    updateHargaTotal();
});
</script>
@endpush
@endsection
