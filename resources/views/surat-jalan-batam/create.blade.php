@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Tambah Surat Jalan Batam {{ $selectedOrder ? '' : '(Tanpa Order)' }}</h1>
                <p class="text-xs text-gray-600 mt-1">Buat surat jalan baru untuk pengiriman Batam {{ $selectedOrder ? 'berdasarkan Order ' . $selectedOrder->nomor_order : 'manual' }}</p>
            </div>
            <a href="{{ route('surat-jalan-batam.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        @if($selectedOrder)
        <!-- Selected Order Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg mx-4 mt-4 p-4 text-sm">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-medium text-blue-800">Order Terpilih: {{ $selectedOrder->nomor_order }}</h4>
                    <p class="text-blue-700 text-xs mt-1">
                        Pengirim: {{ $selectedOrder->pengirim->nama_pengirim ?? '-' }} | 
                        Barang: {{ $selectedOrder->jenisBarang->nama_barang ?? '-' }}
                    </p>
                </div>
                <a href="{{ route('surat-jalan-batam.select-order') }}" class="text-blue-600 hover:underline">Ganti Order</a>
            </div>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('surat-jalan-batam.store') }}" method="POST" class="p-4">
            @csrf

            @if($selectedOrder)
                <input type="hidden" name="order_batam_id" value="{{ $selectedOrder->id }}">
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Row 1 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <div class="flex">
                        <input type="text" name="no_surat_jalan" id="no_surat_jalan" value="{{ old('no_surat_jalan') }}" required
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="button" onclick="generateSjNumber()" class="bg-indigo-600 text-white px-3 py-2 rounded-r-lg text-xs">Auto</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_surat_jalan" value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Section: Order Details -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Detail Pengiriman</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term Pembayaran</label>
                    <select name="term" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih Term</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->kode }}" {{ old('term', $selectedOrder->term->kode ?? '') == $term->kode ? 'selected' : '' }}>
                                {{ $term->kode }} - {{ $term->nama_status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aktifitas</label>
                    <select name="aktifitas" id="aktifitas_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih Aktifitas</option>
                        @foreach($masterKegiatans as $kegiatan)
                            <option value="{{ $kegiatan->nama_kegiatan }}" {{ old('aktifitas') == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                {{ $kegiatan->nama_kegiatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="relative pengirim-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                    <div class="relative">
                        <input type="text" id="pengirim_search" placeholder="Cari pengirim..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="pengirim" id="pengirim_value" value="{{ old('pengirim', $selectedOrder->pengirim->nama_pengirim ?? '') }}">
                        <div id="pengirim_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic pengirim-item" data-value="">Pilih Pengirim</div>
                            @foreach($allPenerimas as $p)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 pengirim-item" 
                                     data-value="{{ $p->nama_penerima }}">
                                    <div class="font-medium">{{ $p->nama_penerima }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative penerima-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                    <div class="relative">
                        <input type="text" id="penerima_search" placeholder="Cari penerima..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="penerima" id="penerima_value" value="{{ old('penerima') }}">
                        <div id="penerima_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic penerima-item" data-value="">Pilih Penerima</div>
                            @foreach($allPenerimas as $p)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 penerima-item" 
                                     data-value="{{ $p->nama_penerima }}"
                                     data-alamat="{{ $p->alamat }}">
                                    <div class="font-medium">{{ $p->nama_penerima }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat / Tujuan Alamat</label>
                    <input type="text" name="alamat" id="alamat_input" value="{{ old('alamat', $selectedOrder->alamat_penerima ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="relative jenis-barang-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <div class="relative">
                        <input type="text" id="jenis_barang_search" placeholder="Cari jenis barang..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="jenis_barang" id="jenis_barang_value" value="{{ old('jenis_barang', $selectedOrder->jenisBarang->nama_barang ?? '') }}">
                        <div id="jenis_barang_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic jenis-barang-item" data-value="">Pilih Jenis Barang</div>
                            @foreach($jenisBarangs as $jb)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 jenis-barang-item" 
                                     data-value="{{ $jb->nama_barang }}">
                                    <div class="font-medium">{{ $jb->nama_barang }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <select name="tujuan_pengambilan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih Tujuan Pengambilan</option>
                        @foreach($pricelistRings as $ring)
                            <option value="{{ $ring['value'] }}" {{ old('tujuan_pengambilan', $selectedOrder->tujuan_ambil ?? '') == $ring['value'] ? 'selected' : '' }}>
                                {{ $ring['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <select name="tujuan_pengiriman" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih Tujuan Pengiriman</option>
                        @foreach($masterTujuanKirims as $tk)
                            <option value="{{ $tk->nama_tujuan }}" {{ old('tujuan_pengiriman', $selectedOrder->tujuan_kirim ?? '') == $tk->nama_tujuan ? 'selected' : '' }}>
                                {{ $tk->nama_tujuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Section: Transport -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Informasi Transportasi</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat Kendaraan</label>
                    <input type="text" name="no_plat" id="no_plat" value="{{ old('no_plat') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="relative supir-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="supir_search" placeholder="Cari supir..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="supir" id="supir_value" value="{{ old('supir') }}">
                        <div id="supir_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic supir-item" data-value="" data-plat="">Pilih Supir</div>
                            @foreach($supirs as $s)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 supir-item" 
                                     data-value="{{ $s->nama_panggilan ?: $s->nama_lengkap }}" 
                                     data-plat="{{ $s->plat }}">
                                    <div class="font-medium">{{ $s->nama_panggilan ?: $s->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-400">{{ $s->plat ?: 'Tanpa Plat' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir 2 (Opsional)</label>
                    <input type="text" name="supir2" value="{{ old('supir2') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="relative kenek-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <div class="relative">
                        <input type="text" id="kenek_search" placeholder="Cari kenek..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="kenek" id="kenek_value" value="{{ old('kenek') }}">
                        <div id="kenek_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic kenek-item" data-value="">Pilih Kenek</div>
                            @foreach($keneks as $k)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 kenek-item" 
                                     data-value="{{ $k->nama_panggilan ?: $k->nama_lengkap }}">
                                    <div class="font-medium">{{ $k->nama_panggilan ?: $k->nama_lengkap }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative krani-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Krani</label>
                    <div class="relative">
                        <input type="text" id="krani_search" placeholder="Cari krani..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="krani" id="krani_value" value="{{ old('krani') }}">
                        <div id="krani_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic krani-item" data-value="">Pilih Krani</div>
                            @foreach($kranis as $kr)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 krani-item" 
                                     data-value="{{ $kr->nama_panggilan ?: $kr->nama_lengkap }}">
                                    <div class="font-medium">{{ $kr->nama_panggilan ?: $kr->nama_lengkap }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <input type="text" name="uang_jalan" id="uang_jalan" 
                           value="{{ old('uang_jalan', number_format($defaultUangJalan, 0, ',', '.')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 money-format">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Insentif Driver</label>
                    <div class="flex space-x-4 mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="lembur" value="1" {{ old('lembur') ? 'checked' : '' }} class="form-checkbox text-indigo-600 rounded">
                            <span class="ml-2 text-sm text-gray-700">Lembur</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="nginap" value="1" {{ old('nginap') ? 'checked' : '' }} class="form-checkbox text-indigo-600 rounded">
                            <span class="ml-2 text-sm text-gray-700">Nginap</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center">
                    <label class="inline-flex items-center mt-6">
                        <input type="checkbox" name="is_supir_customer" value="1" {{ old('is_supir_customer') ? 'checked' : '' }} class="form-checkbox text-indigo-600 rounded">
                        <span class="ml-2 text-sm font-medium text-gray-700">Supir Customer?</span>
                    </label>
                </div>

                <!-- Section: Kontainer -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Informasi Kontainer</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size Kontainer</label>
                    <select name="size" id="size_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih Size</option>
                        @foreach($ukuranKontainers as $uk)
                            <option value="{{ $uk }}" {{ old('size', $selectedOrder->size_kontainer ?? '') == $uk ? 'selected' : '' }}>
                                {{ $uk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select name="tipe_kontainer" id="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih Tipe</option>
                        @php
                            $selectedTipe = old('tipe_kontainer', $selectedOrder->tipe_kontainer ?? '');
                        @endphp
                        <option value="FCL" {{ $selectedTipe == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ $selectedTipe == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="CARGO" {{ $selectedTipe == 'CARGO' ? 'selected' : '' }}>CARGO</option>
                    </select>
                </div>

                <div class="relative no-kontainer-dropdown-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    <div class="relative">
                        <input type="text" id="no_kontainer_search" placeholder="Cari nomor kontainer..." autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <input type="hidden" name="no_kontainer" id="no_kontainer_value" value="{{ old('no_kontainer') }}">
                        <div id="no_kontainer_list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 italic no-kontainer-item" data-value="">Pilih No. Kontainer</div>
                            @foreach($daftarKontainers as $kontainer)
                                <div class="px-4 py-2 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer text-sm transition-colors border-b border-gray-50 last:border-0 no-kontainer-item" 
                                     data-value="{{ $kontainer['no'] }}"
                                     data-size="{{ $kontainer['size'] }}"
                                     data-tipe="{{ $kontainer['tipe'] }}">
                                    <div class="font-medium">{{ $kontainer['no'] }}</div>
                                    @if($kontainer['size'] || $kontainer['tipe'])
                                        <div class="text-xs text-gray-400">
                                            {{ $kontainer['size'] ? 'Size: ' . $kontainer['size'] : '' }}
                                            {{ $kontainer['size'] && $kontainer['tipe'] ? ' | ' : '' }}
                                            {{ $kontainer['tipe'] ? 'Tipe: ' . $kontainer['tipe'] : '' }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Seal</label>
                    <input type="text" name="no_seal" value="{{ old('no_seal') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">F/E</label>
                    <select name="f_e" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Full" {{ old('f_e') == 'Full' ? 'selected' : '' }}>Full</option>
                        <option value="Empty" {{ old('f_e') == 'Empty' ? 'selected' : '' }}>Empty</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RIT</label>
                    <div class="flex space-x-4 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="rit" value="menggunakan_rit" class="form-radio text-indigo-600" checked>
                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="rit" value="tidak_menggunakan_rit" class="form-radio text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                        </label>
                    </div>
                </div>

                <!-- Section: Packaging -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Packaging</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Karton</label>
                    <div class="flex space-x-4 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="karton" value="ya" class="form-radio text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="karton" value="tidak" class="form-radio text-indigo-600" checked>
                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plastik</label>
                    <div class="flex space-x-4 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="plastik" value="ya" class="form-radio text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="plastik" value="tidak" class="form-radio text-indigo-600" checked>
                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Terpal</label>
                    <div class="flex space-x-4 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="terpal" value="ya" class="form-radio text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="terpal" value="tidak" class="form-radio text-indigo-600" checked>
                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                        </label>
                    </div>
                </div>

                <div></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <a href="{{ route('surat-jalan-batam.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-600">Batal</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium shadow-sm">Simpan Surat Jalan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    async function generateSjNumber() {
        const dateInput = document.querySelector('input[name="tanggal_surat_jalan"]');
        const sjInput = document.getElementById('no_surat_jalan');
        
        try {
            const response = await fetch('{{ route("surat-jalan-batam.generate-number") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ date: dateInput.value })
            });
            const data = await response.json();
            if (data.number) {
                sjInput.value = data.number;
            }
        } catch (error) {
            console.error('Error generating SJ number:', error);
        }
    }
    // Money format script
    document.querySelectorAll('.money-format').forEach(function(input) {
        input.addEventListener('keyup', function(e) {
            let value = this.value.replace(/\D/g, '');
            this.value = new Intl.NumberFormat('id-ID').format(value);
        });
    });
    // Custom Searchable Dropdown Logic
    function setupSearchableDropdown(containerClass, inputId, listId, valueId, itemClass, onSelect = null) {
        const searchInput = document.getElementById(inputId);
        const listContainer = document.getElementById(listId);
        const hiddenValue = document.getElementById(valueId);
        const items = document.querySelectorAll('.' + itemClass);

        // Toggle list visibility
        searchInput.addEventListener('focus', () => {
            listContainer.classList.remove('hidden');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.' + containerClass)) {
                listContainer.classList.add('hidden');
            }
        });

        // Filter items
        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? 'block' : 'none';
            });
            listContainer.classList.remove('hidden');
        });

        // Select item
        items.forEach(item => {
            item.addEventListener('click', () => {
                const val = item.getAttribute('data-value');
                searchInput.value = val;
                hiddenValue.value = val;
                listContainer.classList.add('hidden');
                if (onSelect) onSelect(item);
            });
        });

        // Handle initial value (for old/edit)
        if (hiddenValue.value) {
            searchInput.value = hiddenValue.value;
        }
    }

    // Initialize Supir Dropdown
    setupSearchableDropdown(
        'supir-dropdown-container',
        'supir_search',
        'supir_list',
        'supir_value',
        'supir-item',
        (item) => {
            const plat = item.getAttribute('data-plat');
            if (plat) {
                document.getElementById('no_plat').value = plat;
            }
        }
    );

    // Initialize Kenek Dropdown
    setupSearchableDropdown(
        'kenek-dropdown-container',
        'kenek_search',
        'kenek_list',
        'kenek_value',
        'kenek-item'
    );

    // Initialize Jenis Barang Dropdown
    setupSearchableDropdown(
        'jenis-barang-dropdown-container',
        'jenis_barang_search',
        'jenis_barang_list',
        'jenis_barang_value',
        'jenis-barang-item'
    );

    // Initialize Pengirim Dropdown
    setupSearchableDropdown(
        'pengirim-dropdown-container',
        'pengirim_search',
        'pengirim_list',
        'pengirim_value',
        'pengirim-item'
    );

    // Initialize Penerima Dropdown
    setupSearchableDropdown(
        'penerima-dropdown-container',
        'penerima_search',
        'penerima_list',
        'penerima_value',
        'penerima-item',
        (item) => {
            const alamat = item.getAttribute('data-alamat');
            if (alamat) {
                const alamatInput = document.getElementById('alamat_input');
                if (alamatInput) alamatInput.value = alamat;
            }
        }
    );

    // Initialize Krani Dropdown
    setupSearchableDropdown(
        'krani-dropdown-container',
        'krani_search',
        'krani_list',
        'krani_value',
        'krani-item'
    );

    // Initialize No. Kontainer Dropdown
    setupSearchableDropdown(
        'no-kontainer-dropdown-container',
        'no_kontainer_search',
        'no_kontainer_list',
        'no_kontainer_value',
        'no-kontainer-item',
        (item) => {
            const size = item.getAttribute('data-size');
            
            if (size) {
                const sizeSelect = document.getElementById('size_select');
                if (sizeSelect) sizeSelect.value = size;
            }
        }
    );

    // Auto-fill Jenis Barang for TARIK KOSONG
    const aktifitasSelect = document.getElementById('aktifitas_select');
    const jenisBarangSearch = document.getElementById('jenis_barang_search');
    const jenisBarangValue = document.getElementById('jenis_barang_value');

    if (aktifitasSelect) {
        aktifitasSelect.addEventListener('change', function() {
            if (this.value === 'TARIK KOSONG') {
                jenisBarangSearch.value = 'empty container';
                jenisBarangValue.value = 'empty container';
            }
        });
    }
</script>
@endpush
