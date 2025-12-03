@extends('layouts.app')

@section('title', 'Edit Tanda Terima - ' . $tandaTerimaTanpaSuratJalan->no_tanda_terima)
@section('page_title', 'Edit Tanda Terima - ' . $tandaTerimaTanpaSuratJalan->no_tanda_terima)

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Edit Tanda Terima</h1>
                <p class="text-xs text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->no_tanda_terima }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tanda-terima-tanpa-surat-jalan.show', $tandaTerimaTanpaSuratJalan) }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-4">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="font-medium text-sm mb-2">Terdapat kesalahan pada input:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tanda-terima-tanpa-surat-jalan.update', $tandaTerimaTanpaSuratJalan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Informasi Dasar -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="nomor_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_tanda_terima" id="nomor_tanda_terima"
                                   value="{{ old('nomor_tanda_terima', $tandaTerimaTanpaSuratJalan->nomor_tanda_terima) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_tanda_terima') border-red-500 @enderror"
                                   placeholder="TTR-001">
                            @error('nomor_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nomor_surat_jalan_customer" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Surat Jalan Customer
                            </label>
                            <input type="text" name="nomor_surat_jalan_customer" id="nomor_surat_jalan_customer"
                                   value="{{ old('nomor_surat_jalan_customer', $tandaTerimaTanpaSuratJalan->nomor_surat_jalan_customer) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_surat_jalan_customer') border-red-500 @enderror"
                                   placeholder="SJ-CUSTOMER-001">
                            @error('nomor_surat_jalan_customer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_tanda_terima" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Tanda Terima <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_tanda_terima" id="tanggal_tanda_terima"
                                   value="{{ old('tanggal_tanda_terima', $tandaTerimaTanpaSuratJalan->tanggal_tanda_terima->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_tanda_terima') border-red-500 @enderror">
                            @error('tanggal_tanda_terima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Term
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="term_id" id="term_id" class="hidden @error('term_id') border-red-500 @enderror">
                                    <option value="">Pilih Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id', $tandaTerimaTanpaSuratJalan->term_id) == $term->id ? 'selected' : '' }}>
                                            {{ $term->nama_status }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Search input -->
                                <input type="text" id="termSearch"
                                       placeholder="Cari atau pilih term..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('term_id') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="termDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('term.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Term Baru
                                        </a>
                                    </div>
                                    @foreach($terms as $term)
                                        <div class="term-option px-3 py-2 hover:bg-gray-50 cursor-pointer"
                                             data-value="{{ $term->id }}"
                                             data-text="{{ $term->nama_status }}">
                                            {{ $term->nama_status }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('term_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Penerima dan Pengirim -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Penerima dan Pengirim</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                Penerima <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="penerima" id="penerima" value="{{ old('penerima', $tandaTerimaTanpaSuratJalan->penerima) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('penerima') border-red-500 @enderror"
                                   placeholder="Nama penerima">
                            @error('penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                Pengirim <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim', $tandaTerimaTanpaSuratJalan->pengirim) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('pengirim') border-red-500 @enderror"
                                   placeholder="Nama pengirim">
                            @error('pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="pic" class="block text-sm font-medium text-gray-700 mb-1">
                                PIC (Person In Charge)
                            </label>
                            <input type="text" name="pic" id="pic" value="{{ old('pic', $tandaTerimaTanpaSuratJalan->pic) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('pic') border-red-500 @enderror"
                                   placeholder="Nama PIC">
                            @error('pic')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">
                                Telepon
                            </label>
                            <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $tandaTerimaTanpaSuratJalan->telepon) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('telepon') border-red-500 @enderror"
                                   placeholder="Nomor telepon">
                            @error('telepon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Penerima
                            </label>
                            <textarea name="alamat_penerima" id="alamat_penerima" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat_penerima') border-red-500 @enderror"
                                      placeholder="Alamat lengkap penerima">{{ old('alamat_penerima', $tandaTerimaTanpaSuratJalan->alamat_penerima) }}</textarea>
                            @error('alamat_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Pengirim
                            </label>
                            <textarea name="alamat_pengirim" id="alamat_pengirim" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat_pengirim') border-red-500 @enderror"
                                      placeholder="Alamat lengkap pengirim">{{ old('alamat_pengirim', $tandaTerimaTanpaSuratJalan->alamat_pengirim) }}</textarea>
                            @error('alamat_pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Barang (LCL-friendly: array-based, same as create.blade.php) -->
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Dimensi dan Volume
                        </h3>
                        <button type="button"
                                id="add-dimensi-btn-edit"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Dimensi
                        </button>
                    </div>

                    <div id="dimensi-container-edit">
                        {{-- Existing LCL rows (populate from model dimensiItems) --}}
                        @php
                            $dimensiItems = $tandaTerimaTanpaSuratJalan->dimensiItems ?? [];
                            if ($dimensiItems instanceof \Illuminate\Database\Eloquent\Collection) {
                                $dimensiItems = $dimensiItems->toArray();
                            }
                            $initialDimensiRows = old('nama_barang') ? array_map(null, old('nama_barang'), old('jumlah'), old('satuan'), old('panjang'), old('lebar'), old('tinggi'), old('meter_kubik'), old('tonase')) : null;
                        @endphp

                        @if(!empty($initialDimensiRows))
                            @foreach(old('nama_barang') as $idx => $nm)
                                <div class="dimensi-row-edit mb-4 pb-4 border-b border-purple-200 relative">
                                    <button type="button" class="remove-dimensi-btn-edit absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                                            <input type="text" name="nama_barang[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required value="{{ old('nama_barang.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label>
                                            <input type="number" name="jumlah[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="{{ old('jumlah.'.$idx, 1) }}" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label>
                                            <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="{{ old('satuan.'.$idx, 'unit') }}" required>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                                            <input type="number" name="panjang[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('panjang.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                                            <input type="number" name="lebar[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('lebar.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                                            <input type="number" name="tinggi[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('tinggi.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                                            <input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="0.000" min="0" step="0.001" readonly value="{{ old('meter_kubik.'.$idx) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                                            <input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" value="{{ old('tonase.'.$idx) }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @elseif(!empty($dimensiItems))
                            @foreach($dimensiItems as $item)
                                <div class="dimensi-row-edit mb-4 pb-4 border-b border-purple-200 relative">
                                    <button type="button" class="remove-dimensi-btn-edit absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                                            <input type="text" name="nama_barang[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required value="{{ old('nama_barang', $item['nama_barang'] ?? $item->nama_barang ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label>
                                            <input type="number" name="jumlah[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="{{ old('jumlah', $item['jumlah'] ?? $item->jumlah ?? 1) }}" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label>
                                            <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="{{ old('satuan', $item['satuan'] ?? $item->satuan ?? 'unit') }}" required>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                                            <input type="number" name="panjang[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('panjang', $item['panjang'] ?? $item->panjang ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                                            <input type="number" name="lebar[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('lebar', $item['lebar'] ?? $item->lebar ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                                            <input type="number" name="tinggi[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))" value="{{ old('tinggi', $item['tinggi'] ?? $item->tinggi ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                                            <input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="0.000" min="0" step="0.001" readonly value="{{ old('meter_kubik', $item['meter_kubik'] ?? $item->meter_kubik ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                                            <input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" value="{{ old('tonase', $item['tonase'] ?? $item->tonase ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Keep an empty single initial row to match create behavior -->
                            <div class="dimensi-row-edit mb-4 pb-4 border-b border-purple-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama_barang[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label>
                                        <input type="number" name="jumlah[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="1" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label>
                                        <input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="unit" required>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label>
                                        <input type="number" name="panjang[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label>
                                        <input type="number" name="lebar[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label>
                                        <input type="number" name="tinggi[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label>
                                        <input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="0.000" min="0" step="0.001" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label>
                                        <input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Hidden fields for backward compatibility (scalar) -->
                    <input type="hidden" name="jenis_barang" id="jenis_barang" value="{{ old('jenis_barang', $tandaTerimaTanpaSuratJalan->jenis_barang) }}">
                    <input type="hidden" name="jumlah_barang" id="jumlah_barang" value="{{ old('jumlah_barang', $tandaTerimaTanpaSuratJalan->jumlah_barang) }}">
                    <input type="hidden" name="satuan_barang" id="satuan_barang" value="{{ old('satuan_barang', $tandaTerimaTanpaSuratJalan->satuan_barang) }}">
                    <input type="hidden" name="berat" id="berat" value="{{ old('berat', $tandaTerimaTanpaSuratJalan->berat) }}">
                    <input type="hidden" name="satuan_berat" id="satuan_berat" value="{{ old('satuan_berat', $tandaTerimaTanpaSuratJalan->satuan_berat) }}">
                    <input type="hidden" name="keterangan_barang" id="keterangan_barang" value="{{ old('keterangan_barang', $tandaTerimaTanpaSuratJalan->keterangan_barang) }}">
                    <!-- End LCL-style Informasi Barang -->
                </div>

                <!-- Informasi Tujuan -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tujuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="tujuan_pengambilan" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Pengambilan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tujuan_pengambilan" id="tujuan_pengambilan" value="{{ old('tujuan_pengambilan', $tandaTerimaTanpaSuratJalan->tujuan_pengambilan) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengambilan') border-red-500 @enderror"
                                   placeholder="Lokasi pengambilan barang">
                            @error('tujuan_pengambilan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Pengiriman <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tujuan_pengiriman" id="tujuan_pengiriman" value="{{ old('tujuan_pengiriman', $tandaTerimaTanpaSuratJalan->tujuan_pengiriman) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_pengiriman') border-red-500 @enderror"
                                   placeholder="Lokasi tujuan pengiriman">
                            @error('tujuan_pengiriman')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Transportasi -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Transportasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="supir" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Supir <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="supir" id="supir" class="hidden @error('supir') border-red-500 @enderror" required>
                                    <option value="">Pilih Supir</option>
                                    @foreach($supirs as $supir)
                                        <option value="{{ $supir->nama_lengkap }}" {{ old('supir', $tandaTerimaTanpaSuratJalan->supir) == $supir->nama_lengkap ? 'selected' : '' }}>
                                            {{ $supir->nama_panggilan ?? $supir->nama_lengkap }}
                                        </option>
                                    @endforeach
                                    @if(old('supir', $tandaTerimaTanpaSuratJalan->supir) && !in_array(old('supir', $tandaTerimaTanpaSuratJalan->supir), $supirs->pluck('nama_lengkap')->toArray()))
                                        <option value="{{ old('supir', $tandaTerimaTanpaSuratJalan->supir) }}" selected>{{ old('supir', $tandaTerimaTanpaSuratJalan->supir) }}</option>
                                    @endif
                                </select>

                                <!-- Search input -->
                                <input type="text" id="supirSearch"
                                       placeholder="Cari atau pilih supir..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('supir') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="supirDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('karyawan.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Supir Baru
                                        </a>
                                    </div>
                                    @foreach($supirs as $supir)
                                        <div class="supir-option px-3 py-2 hover:bg-gray-50 cursor-pointer"
                                             data-value="{{ $supir->nama_lengkap }}"
                                             data-text="{{ $supir->nama_lengkap }}">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $supir->nama_lengkap }}</span>
                                                @if($supir->nik)
                                                    <span class="text-xs text-gray-500">NIK: {{ $supir->nik }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('supir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kenek" class="block text-sm font-medium text-gray-700 mb-1">
                                Kenek
                            </label>
                            <div class="relative">
                                <!-- Hidden select for form submission -->
                                <select name="kenek" id="kenek" class="hidden @error('kenek') border-red-500 @enderror">
                                    <option value="">Pilih Kenek</option>
                                    @foreach($kranis as $krani)
                                        <option value="{{ $krani->nama_lengkap }}" {{ old('kenek', $tandaTerimaTanpaSuratJalan->kenek) == $krani->nama_lengkap ? 'selected' : '' }}>
                                            {{ $krani->nama_lengkap }}
                                        </option>
                                    @endforeach
                                    @if(old('kenek', $tandaTerimaTanpaSuratJalan->kenek) && !in_array(old('kenek', $tandaTerimaTanpaSuratJalan->kenek), $kranis->pluck('nama_lengkap')->toArray()))
                                        <option value="{{ old('kenek', $tandaTerimaTanpaSuratJalan->kenek) }}" selected>{{ old('kenek', $tandaTerimaTanpaSuratJalan->kenek) }}</option>
                                    @endif
                                </select>

                                <!-- Search input -->
                                <input type="text" id="kenekSearch"
                                       placeholder="Cari atau pilih kenek..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('kenek') border-red-500 @enderror">

                                <!-- Dropdown options -->
                                <div id="kenekDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <div class="p-2 border-b border-gray-200">
                                        <a href="{{ route('karyawan.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fas fa-plus mr-1"></i> Tambah Kenek Baru
                                        </a>
                                    </div>
                                    @foreach($kranis as $krani)
                                        <div class="kenek-option px-3 py-2 hover:bg-gray-50 cursor-pointer"
                                             data-value="{{ $krani->nama_lengkap }}"
                                             data-text="{{ $krani->nama_lengkap }}">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $krani->nama_lengkap }}</span>
                                                @if($krani->nik)
                                                    <span class="text-xs text-gray-500">NIK: {{ $krani->nik }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('kenek')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Plat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_plat" id="no_plat" value="{{ old('no_plat', $tandaTerimaTanpaSuratJalan->no_plat) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_plat') border-red-500 @enderror"
                                   placeholder="Nomor plat kendaraan">
                            @error('no_plat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe Kontainer
                            </label>
                                <select name="_tipe_kontainer_disabled" id="tipe_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 cursor-not-allowed @error('tipe_kontainer') border-red-500 @enderror"
                                    disabled onchange="handleTipeKontainerChange()">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="fcl" {{ old('tipe_kontainer', $tandaTerimaTanpaSuratJalan->tipe_kontainer) == 'fcl' ? 'selected' : '' }}>FCL</option>
                                <option value="lcl" {{ old('tipe_kontainer', $tandaTerimaTanpaSuratJalan->tipe_kontainer) == 'lcl' ? 'selected' : '' }}>LCL</option>
                                <option value="cargo" {{ old('tipe_kontainer', $tandaTerimaTanpaSuratJalan->tipe_kontainer) == 'cargo' ? 'selected' : '' }}>Cargo</option>
                            </select>
                            {{-- Keep a hidden field to submit tipe_kontainer value since disabled selects are not submitted --}}
                            <input type="hidden" name="tipe_kontainer" value="{{ old('tipe_kontainer', $tandaTerimaTanpaSuratJalan->tipe_kontainer) }}">
                            @error('tipe_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Kontainer Details -->
                    <div id="kontainer_fields" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label for="no_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Kontainer
                            </label>
                            <div class="relative">
                                <input type="text" id="noKontainerSearch" placeholder="Cari nomor kontainer..." autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_kontainer') border-red-500 @enderror">
                                <div id="noKontainerDropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto hidden">
                                    @if(isset($containerOptions) && count($containerOptions))
                                        @foreach($containerOptions as $opt)
                                            <div class="no-kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer" data-value="{{ $opt['value'] }}" data-text="{{ $opt['label'] }}@if(!empty($opt['size'])) - {{ $opt['size'] }}@endif" data-size="{{ $opt['size'] }}" data-source="{{ $opt['source'] }}">
                                                {{ $opt['label'] }}@if(!empty($opt['size'])) - {{ $opt['size'] }}@endif
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="no-kontainer-option px-3 py-2 hover:bg-gray-100 cursor-pointer text-blue-600" data-value="__manual__" data-text="&raquo; Ketik manual / Lainnya">
                                        &raquo; Ketik manual / Lainnya
                                    </div>
                                </div>
                                <input type="hidden" name="no_kontainer" id="no_kontainer" value="{{ old('no_kontainer', $tandaTerimaTanpaSuratJalan->no_kontainer) }}">
                            </div>
                            <input type="text" name="no_kontainer_manual" id="no_kontainer_manual" value="{{ old('no_kontainer_manual') }}" placeholder="Masukkan nomor kontainer jika memilih Lainnya" class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md text-sm hidden" />
                            @error('no_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-1">
                                Size Kontainer
                            </label>
                            <select name="size_kontainer" id="size_kontainer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('size_kontainer') border-red-500 @enderror">
                                <option value="">-- Pilih Size --</option>
                                <option value="20 ft" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '20 ft' ? 'selected' : '' }}>20 ft</option>
                                <option value="40 ft" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '40 ft' ? 'selected' : '' }}>40 ft</option>
                                <option value="40 HC" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '40 HC' ? 'selected' : '' }}>40 HC (High Cube)</option>
                                <option value="45 ft" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '45 ft' ? 'selected' : '' }}>45 ft</option>
                                <option value="53 ft" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == '53 ft' ? 'selected' : '' }}>53 ft</option>
                                <option value="other" {{ old('size_kontainer', $tandaTerimaTanpaSuratJalan->size_kontainer) == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div id="seal_field">
                            <label for="no_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                No. Seal
                            </label>
                            <input type="text" name="no_seal" id="no_seal" value="{{ old('no_seal', $tandaTerimaTanpaSuratJalan->no_seal) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('no_seal') border-red-500 @enderror"
                                   placeholder="Nomor seal">
                            @error('no_seal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 3: Tanggal Seal -->
                    <div id="tanggal_seal_field" class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label for="tanggal_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Seal
                            </label>
                            <input type="date" name="tanggal_seal" id="tanggal_seal" value="{{ old('tanggal_seal', $tandaTerimaTanpaSuratJalan->tanggal_seal ? $tandaTerimaTanpaSuratJalan->tanggal_seal->format('Y-m-d') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_seal') border-red-500 @enderror">
                            @error('tanggal_seal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan</h3>
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan Tambahan
                        </label>
                        <textarea name="catatan" id="catatan" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Catatan atau informasi tambahan (opsional)">{{ old('catatan', $tandaTerimaTanpaSuratJalan->catatan) }}</textarea>
                    </div>
                </div>

                <!-- Upload Gambar -->
                @php
                    $__gambarArray = $tandaTerimaTanpaSuratJalan->gambar_tanda_terima;
                    if (is_string($__gambarArray)) {
                        $__decoded = json_decode($__gambarArray, true);
                        $__gambarArray = is_array($__decoded) ? $__decoded : [];
                    }
                    if (!is_array($__gambarArray)) {
                        $__gambarArray = [];
                    }
                @endphp
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gambar Tanda Terima</h3>

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors upload-dropzone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="gambar_tanda_terima" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload gambar</span>
                                    <input id="gambar_tanda_terima" 
                                           name="gambar_tanda_terima[]" 
                                           type="file" 
                                           class="sr-only" 
                                           multiple
                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                           onchange="previewImages(this)">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, JPEG, GIF, WEBP sampai 10MB per file (max 5 file)
                            </p>
                        </div>
                    </div>
                    @error('gambar_tanda_terima.*')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Preview Area for Images -->
                    <div id="image-preview-container" class="mt-4 @if(empty($__gambarArray)) hidden @endif">
                        <label class="block text-xs font-medium text-gray-500 mb-2">
                            <i class="fas fa-eye mr-1 text-green-600"></i>
                            Preview Gambar
                        </label>
                        <div id="image-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            {{-- Existing images previewed here --}}
                            @foreach($__gambarArray as $index => $imagePath)
                                @php $imgUrl = asset('storage/' . ltrim($imagePath, '/')); @endphp
                                <div class="relative bg-gray-50 rounded-lg border border-gray-200 p-2 image-preview-item" data-is-existing="1" data-path="{{ $imagePath }}">
                                    <img src="{{ $imgUrl }}" alt="Gambar {{ $index + 1 }}" class="object-cover w-full h-28 rounded"/>
                                    <div class="flex justify-between items-center mt-2">
                                        <div class="text-xs text-gray-600 truncate">Gambar {{ $index + 1 }}</div>
                                        <div class="flex gap-2 items-center">
                                            <a href="{{ $imgUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-2 py-1 text-xs bg-white border rounded text-gray-700 hover:bg-gray-50" download>
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M21 12v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8"></path></svg>
                                                Unduh
                                            </a>
                                            <button type="button" onclick="removeExistingImage(this, '{{ $imagePath }}')" class="inline-flex items-center px-2 py-1 text-xs bg-red-50 border rounded text-red-700 hover:bg-red-100">Hapus</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="existing_images[]" value="{{ $imagePath }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.show', $tandaTerimaTanpaSuratJalan) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Tanda Terima
                    </button>
                </div>
            </form>
        </div>
            </div>
        </div>
        @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate meter kubik on page load if values exist
        calculateMeterKubik();

        // Initialize term dropdown
        initializeTermDropdown();

        // Initialize supir dropdown
        initializeSupirDropdown();

        // Initialize kenek dropdown
        initializeKenekDropdown();

        // Handle tipe kontainer on page load
        handleTipeKontainerChange();
        // Initialize per-row volume calculation for existing dimensi rows
        const initialDimensiRows = document.querySelectorAll('#dimensi-container-edit .dimensi-row-edit');
        initialDimensiRows.forEach(row => calculateVolumeEdit(row));
    });

    function handleTipeKontainerChange() {
        const tipeKontainer = document.getElementById('tipe_kontainer').value;
        const kontainerFields = document.getElementById('kontainer_fields');
        const sealField = document.getElementById('seal_field');
        const tanggalSealField = document.getElementById('tanggal_seal_field');
        
        if (tipeKontainer === 'cargo') {
            kontainerFields.style.display = 'none';
            if (sealField) sealField.style.display = 'none';
            if (tanggalSealField) tanggalSealField.style.display = 'none';
            // Clear kontainer fields when cargo is selected
            document.getElementById('no_kontainer').value = '';
            document.getElementById('size_kontainer').value = '';
            document.getElementById('no_seal').value = '';
            document.getElementById('tanggal_seal').value = '';
        } else {
            kontainerFields.style.display = 'grid';
            if (sealField) sealField.style.display = 'block';
            if (tanggalSealField) tanggalSealField.style.display = 'grid';
        }
    }

    // Initialize no kontainer dropdown
    initializeNoKontainerDropdown();

    function setSizeKontainerValue(size) {
        const sizeSelect = document.getElementById('size_kontainer');
        if (!sizeSelect) return;
        let matched = false;
        for (let i = 0; i < sizeSelect.options.length; i++) {
            const opt = sizeSelect.options[i];
            if (!size) { opt.selected = false; continue; }
            if (opt.value === size || (opt.text && opt.text.toLowerCase().includes(String(size).toLowerCase())) || opt.value.replace(/\s|-/g, '').toLowerCase() === String(size).replace(/\s|-/g, '').toLowerCase()) {
                opt.selected = true;
                matched = true;
                break;
            }
        }
        if (!matched) {
            sizeSelect.value = size;
        }
    }
    // Ensure manual value is submitted if manual option chosen (edit)
    const editForm = document.querySelector('form');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            const hiddenInput = document.getElementById('no_kontainer');
            const manualField = document.getElementById('no_kontainer_manual');
            if (hiddenInput && hiddenInput.value === '__manual__') {
                if (!manualField || !manualField.value.trim()) {
                    e.preventDefault();
                    alert('Silakan isi nomor kontainer pada input manual.');
                    (manualField || document.getElementById('noKontainerSearch')).focus();
                    return false;
                }
                // Set hidden input to manual value
                hiddenInput.value = manualField.value.trim();
            }
            // Update hidden legacy fields from LCL rows
            try { updateHiddenBarangFields(); } catch (err) { /* ignore */ }
        });
    }

    function calculateMeterKubik() {
        // This function is not needed for edit page since we use array-based inputs
        // Volume calculation is handled by calculateVolumeEdit() for each row
        return;
    }

    function initializeTermDropdown() {
        const searchInput = document.getElementById('termSearch');
        const dropdown = document.getElementById('termDropdown');
        const hiddenSelect = document.getElementById('term_id');
        const options = document.querySelectorAll('.term-option');

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                // Set the hidden select value
                hiddenSelect.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#termSearch') && !e.target.closest('#termDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });

        // Set initial value if exists
        const selectedOption = hiddenSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent;
        }
    }

    function initializeSupirDropdown() {
        const searchInput = document.getElementById('supirSearch');
        const dropdown = document.getElementById('supirDropdown');
        const hiddenSelect = document.getElementById('supir');
        const options = document.querySelectorAll('.supir-option');

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            // Update hidden select with current input value for custom entries
            hiddenSelect.value = this.value;

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                // Set the hidden select value
                hiddenSelect.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#supirSearch') && !e.target.closest('#supirDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });

        // Set initial value if exists
        const selectedOption = hiddenSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent;
        } else if (hiddenSelect.value) {
            // Handle custom supir value that might not be in the dropdown options
            const customSupir = hiddenSelect.value;
            // Check if this value exists in any option
            const existingOption = Array.from(hiddenSelect.options).find(opt => opt.value === customSupir);
            if (!existingOption) {
                // This is a custom value, display it in the search input
                searchInput.value = customSupir;
            }
        }
    }

    function initializeKenekDropdown() {
        const searchInput = document.getElementById('kenekSearch');
        const dropdown = document.getElementById('kenekDropdown');
        const hiddenSelect = document.getElementById('kenek');
        const options = document.querySelectorAll('.kenek-option');

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            // Update hidden select with current input value for custom entries
            hiddenSelect.value = this.value;

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');

                // Set the hidden select value
                hiddenSelect.value = value;

                // Update search input
                searchInput.value = text;

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#kenekSearch') && !e.target.closest('#kenekDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });

        // Set initial value if exists
        const selectedOption = hiddenSelect.querySelector('option:checked');
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent;
        } else if (hiddenSelect.value) {
            // Handle custom kenek value that might not be in the dropdown options
            const customKenek = hiddenSelect.value;
            // Check if this value exists in any option
            const existingOption = Array.from(hiddenSelect.options).find(opt => opt.value === customKenek);
            if (!existingOption) {
                // This is a custom value, display it in the search input
                searchInput.value = customKenek;
            }
        }
    }

    function initializeNoKontainerDropdown() {
        const searchInput = document.getElementById('noKontainerSearch');
        const dropdown = document.getElementById('noKontainerDropdown');
        const hiddenInput = document.getElementById('no_kontainer');
        const manualField = document.getElementById('no_kontainer_manual');
        const options = document.querySelectorAll('.no-kontainer-option');

        if (!searchInput || !dropdown || !hiddenInput) {
            console.error('Required elements not found for no kontainer dropdown');
            return;
        }

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', function() {
            dropdown.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            dropdown.classList.remove('hidden');
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.getAttribute('data-text');
                const size = this.getAttribute('data-size');

                // Set the hidden input value
                hiddenInput.value = value;

                // Update search input
                searchInput.value = text;

                // Auto-fill size_kontainer
                setSizeKontainerValue(size);

                // Handle manual field
                if (value === '__manual__') {
                    manualField.classList.remove('hidden');
                    manualField.focus();
                } else {
                    manualField.classList.add('hidden');
                }

                // Hide dropdown
                dropdown.classList.add('hidden');
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#noKontainerSearch') && !e.target.closest('#noKontainerDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });

        // Set initial value if exists
        if (hiddenInput.value) {
            const selectedOption = document.querySelector(`.no-kontainer-option[data-value="${hiddenInput.value}"]`);
            if (selectedOption) {
                searchInput.value = selectedOption.getAttribute('data-text');
                const size = selectedOption.getAttribute('data-size');
                setSizeKontainerValue(size);
            } else if (hiddenInput.value === '__manual__' && manualField.value) {
                searchInput.value = manualField.value;
                manualField.classList.remove('hidden');
            }
        }
    }

        // Image Upload Functions (for Edit)
        function previewImages(input) {
            const previewContainer = document.getElementById('image-preview-container');
            const previewGrid = document.getElementById('image-preview-grid');
        
            if (input.files && input.files.length > 0) {
                previewContainer.classList.remove('hidden');
                const existingCount = previewGrid.querySelectorAll('[data-is-existing="1"]').length;
                const maxAllowed = 5 - existingCount;
                if (maxAllowed <= 0) {
                    alert('Anda sudah memiliki gambar yang tersimpan. Maksimal 5 gambar. Hapus beberapa gambar terlebih dahulu jika ingin menambahkan.');
                    input.value = '';
                    return;
                }
                const prevNew = previewGrid.querySelectorAll('[data-is-existing!="1"]');
                prevNew.forEach(el => el.remove());

                const filesToProcess = Math.min(input.files.length, maxAllowed);
                for (let i = 0; i < filesToProcess; i++) {
                    const file = input.files[i];
                    if (!file.type.startsWith('image/')) { continue; }
                    if (file.size > 10 * 1024 * 1024) { alert('Salah satu file terlalu besar. Maksimal 10MB per file.'); continue; }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'relative bg-gray-50 rounded-lg border border-gray-200 p-2 image-preview-item';
                        previewDiv.dataset.isExisting = 0;
                        previewDiv.dataset.fileIndex = i;
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${i+1}" class="object-cover w-full h-28 rounded"/>
                            <div class="flex justify-between items-center mt-2">
                                <div class="text-xs text-gray-600">Upload Baru ${i+1}</div>
                                <div class="flex gap-2 items-center">
                                    <button type="button" onclick="removePreview(this, ${i})" class="inline-flex items-center px-2 py-1 text-xs bg-red-50 border rounded text-red-700 hover:bg-red-100">Hapus</button>
                                </div>
                            </div>
                        `;
                        previewGrid.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                const previewGrid = document.getElementById('image-preview-grid');
                if (!previewGrid || previewGrid.children.length === 0) {
                    document.getElementById('image-preview-container').classList.add('hidden');
                }
            }
        }

        function removePreview(button, index) {
            const input = document.getElementById('gambar_tanda_terima');
            const previewContainer = document.getElementById('image-preview-container');
            const previewGrid = document.getElementById('image-preview-grid');
            const node = button.closest('.image-preview-item');
            if (node) node.remove();
            if (previewGrid.children.length === 0) { previewContainer.classList.add('hidden'); }
            try {
                const files = Array.from(input.files || []);
                const newFiles = files.filter((_, idx) => idx !== index);
                const dataTransfer = new DataTransfer();
                newFiles.forEach(f => dataTransfer.items.add(f));
                input.files = dataTransfer.files;
            } catch (err) {
                // ignore
            }
        }

        function removeExistingImage(button, path) {
            const previewDiv = button.closest('.image-preview-item');
            if (previewDiv) previewDiv.remove();
            const existingInputs = document.querySelectorAll('input[name="existing_images[]"]');
            existingInputs.forEach(inp => { if (inp.value === path) inp.remove(); });
            const form = document.querySelector('form');
            if (form) {
                const rem = document.createElement('input');
                rem.type = 'hidden'; rem.name = 'hapus_gambar[]'; rem.value = path; form.appendChild(rem);
            }
            const previewGrid = document.getElementById('image-preview-grid');
            if (previewGrid.children.length === 0) document.getElementById('image-preview-container').classList.add('hidden');
        }

        // Dimensi (LCL-like) functions for edit
        function calculateVolumeEdit(rowElement) {
            if (!rowElement) return;
            const panjangInput = rowElement.querySelector('input[name="panjang[]"]');
            const lebarInput = rowElement.querySelector('input[name="lebar[]"]');
            const tinggiInput = rowElement.querySelector('input[name="tinggi[]"]');
            const volumeInput = rowElement.querySelector('input[name="meter_kubik[]"]');

            const panjang = parseFloat(panjangInput?.value) || 0;
            const lebar = parseFloat(lebarInput?.value) || 0;
            const tinggi = parseFloat(tinggiInput?.value) || 0;

            if (panjang > 0 && lebar > 0 && tinggi > 0) {
                const volume = panjang * lebar * tinggi;
                volumeInput.value = volume.toFixed(3);
            } else {
                volumeInput.value = '';
            }
            updateHiddenBarangFields();
        }

        // Add and remove dimensi row handlers
        document.addEventListener('click', function(e) {
            if (e.target && e.target.closest('#add-dimensi-btn-edit')) {
                const container = document.getElementById('dimensi-container-edit');
                if (!container) return;
                const newRow = document.createElement('div');
                newRow.className = 'dimensi-row-edit mb-4 pb-4 border-b border-purple-200 relative';
                newRow.innerHTML = `
                    <button type="button" class="remove-dimensi-btn-edit absolute top-0 right-0 text-red-500 hover:text-red-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Nama Barang <span class="text-red-500">*</span></label><input type="text" name="nama_barang[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Nama barang" required></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Jumlah <span class="text-red-500">*</span></label><input type="number" name="jumlah[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0" min="1" step="1" value="1" required></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Satuan <span class="text-red-500">*</span></label><input type="text" name="satuan[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Pcs, Kg, Box" value="unit" required></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Panjang (m)</label><input type="number" name="panjang[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))"></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Lebar (m)</label><input type="number" name="lebar[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))"></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Tinggi (m)</label><input type="number" name="tinggi[]" class="dimensi-input-edit w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001" oninput="calculateVolumeEdit(this.closest('.dimensi-row-edit'))"></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Volume (m³)</label><input type="number" name="meter_kubik[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm" placeholder="0.000" min="0" step="0.001" readonly></div>
                        <div><label class="block text-xs font-medium text-gray-500 mb-2">Tonase (Ton)</label><input type="number" name="tonase[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="0.000" min="0" step="0.001"></div>
                    </div>
                `;
                container.appendChild(newRow);
            }
            if (e.target && e.target.closest('.remove-dimensi-btn-edit')) {
                const row = e.target.closest('.dimensi-row-edit');
                if (row) row.remove();
                updateHiddenBarangFields();
            }
        });

        // Update hidden legacy fields (jenis_barang, jumlah_barang, satuan_barang, keterangan_barang)
        function updateHiddenBarangFields() {
            try {
                const jenisEl = document.getElementById('jenis_barang');
                const jumlahEl = document.getElementById('jumlah_barang');
                const satuanEl = document.getElementById('satuan_barang');
                const keteranganEl = document.getElementById('keterangan_barang');
                const beratEl = document.getElementById('berat');
                const satuanBeratEl = document.getElementById('satuan_berat');

                const namaInputs = Array.from(document.querySelectorAll('input[name="nama_barang[]"]'));
                const jumlahInputs = Array.from(document.querySelectorAll('input[name="jumlah[]"]'));
                const satuanInputs = Array.from(document.querySelectorAll('input[name="satuan[]"]'));

                const namaVals = namaInputs.map(i => i.value.trim()).filter(v => v !== '');
                const jumlahVals = jumlahInputs.map(i => parseInt(i.value, 10) || 0).filter(v => v >= 0);
                const satuanVals = satuanInputs.map(i => i.value.trim()).filter(v => v !== '');

                if (jenisEl) jenisEl.value = namaVals.length ? namaVals.join(', ') : (jenisEl.value || '');
                if (jumlahEl) {
                    const totalJumlah = jumlahVals.length ? jumlahVals.reduce((a, b) => a + b, 0) : parseInt(jumlahEl.value, 10) || 1;
                    jumlahEl.value = totalJumlah;
                }
                if (satuanEl) satuanEl.value = satuanVals.length ? satuanVals.join(',') : (satuanEl.value || 'unit');
                if (keteranganEl && !keteranganEl.value && namaVals.length) {
                    keteranganEl.value = keteranganEl.value || '';
                }
                if (beratEl && !beratEl.value) {
                    beratEl.value = beratEl.value || '';
                }
                if (satuanBeratEl && !satuanBeratEl.value) {
                    satuanBeratEl.value = satuanBeratEl.value || 'kg';
                }
            } catch (err) {
                // ignore errors
            }
        }

        // Attach event to initial dimensi inputs
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('dimensi-container-edit');
            if (container) {
                container.querySelectorAll('.dimensi-input-edit').forEach(inp => {
                    inp.addEventListener('input', function() {
                        const row = this.closest('.dimensi-row-edit');
                        if (row) calculateVolumeEdit(row);
                    });
                });
                // When tonase/jumlah/satuan/nama input change update hidden fields
                container.querySelectorAll('input[name="tonase[]"], input[name="jumlah[]"], input[name="satuan[]"], input[name="nama_barang[]"]').forEach(inp => {
                    inp.addEventListener('input', updateHiddenBarangFields);
                });
            }
        });

        // Handle updates from dynamically added fields (delegated)
        document.addEventListener('input', function(e) {
            if (e.target && (e.target.matches('input[name="nama_barang[]"]') || e.target.matches('input[name="jumlah[]"]') || e.target.matches('input[name="satuan[]"]') || e.target.matches('input[name="tonase[]"]'))) {
                updateHiddenBarangFields();
            }
        });

    </script>
@endpush

@endsection
