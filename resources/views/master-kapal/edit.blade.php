@extends('layouts.app')

@section('title', 'Edit Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('master-kapal.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-ship mr-2"></i>
                    Master Kapal
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Kapal</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Kapal</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi kapal <span class="font-semibold">{{ $masterKapal->nama_kapal }}</span></p>
            </div>
            <div class="flex items-center space-x-2">
                @if($masterKapal->status == 'aktif')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-circle text-green-500 text-xs mr-1"></i> Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-circle text-gray-500 text-xs mr-1"></i> Nonaktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada form:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Form Edit Kapal</h2>
        </div>

        <form action="{{ route('master-kapal.update', $masterKapal->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Row 1: Kode & Kode Kapal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="kode"
                           name="kode"
                           value="{{ old('kode', $masterKapal->kode) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kode') border-red-500 @enderror"
                           placeholder="Masukkan kode kapal"
                           required>
                    @error('kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kode unik untuk identifikasi kapal (maks. 50 karakter)</p>
                </div>

                <div>
                    <label for="kode_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Kapal
                    </label>
                    <input type="text"
                           id="kode_kapal"
                           name="kode_kapal"
                           value="{{ old('kode_kapal', $masterKapal->kode_kapal) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kode_kapal') border-red-500 @enderror"
                           placeholder="Masukkan kode alternatif kapal">
                    @error('kode_kapal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kode alternatif/tambahan (opsional, maks. 100 karakter)</p>
                </div>
            </div>

            <!-- Nama Kapal -->
            <div class="mb-6">
                <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Kapal <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="nama_kapal"
                       name="nama_kapal"
                       value="{{ old('nama_kapal', $masterKapal->nama_kapal) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_kapal') border-red-500 @enderror"
                       placeholder="Masukkan nama kapal"
                       required>
                @error('nama_kapal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Row 2: Nickname & Pelayaran -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">
                        Nickname
                    </label>
                    <input type="text"
                           id="nickname"
                           name="nickname"
                           value="{{ old('nickname', $masterKapal->nickname) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nickname') border-red-500 @enderror"
                           placeholder="Masukkan nickname kapal">
                    @error('nickname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Nama panggilan/singkatan kapal (opsional)</p>
                </div>

                <div>
                    <label for="pelayaran" class="block text-sm font-medium text-gray-700 mb-2">
                        Pelayaran (Pemilik Kapal)
                    </label>
                    <input type="text"
                           id="pelayaran"
                           name="pelayaran"
                           value="{{ old('pelayaran', $masterKapal->pelayaran) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pelayaran') border-red-500 @enderror"
                           placeholder="Masukkan nama pelayaran/pemilik kapal">
                    @error('pelayaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Nama perusahaan pelayaran pemilik kapal (opsional)</p>
                </div>
            </div>

            <!-- Row 3: Kapasitas Kontainer & Gross Tonnage -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="kapasitas_kontainer_palka" class="block text-sm font-medium text-gray-700 mb-2">
                        Kapasitas Kontainer Palka
                    </label>
                    <div class="relative">
                        <input type="number"
                               id="kapasitas_kontainer_palka"
                               name="kapasitas_kontainer_palka"
                               value="{{ old('kapasitas_kontainer_palka', $masterKapal->kapasitas_kontainer_palka) }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kapasitas_kontainer_palka') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('kapasitas_kontainer_palka')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kapasitas kontainer di bagian palka kapal</p>
                </div>

                <div>
                    <label for="kapasitas_kontainer_deck" class="block text-sm font-medium text-gray-700 mb-2">
                        Kapasitas Kontainer Deck
                    </label>
                    <div class="relative">
                        <input type="number"
                               id="kapasitas_kontainer_deck"
                               name="kapasitas_kontainer_deck"
                               value="{{ old('kapasitas_kontainer_deck', $masterKapal->kapasitas_kontainer_deck) }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kapasitas_kontainer_deck') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('kapasitas_kontainer_deck')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kapasitas kontainer di bagian deck kapal</p>
                </div>

                <div>
                    <label for="gross_tonnage" class="block text-sm font-medium text-gray-700 mb-2">
                        Gross Tonnage
                    </label>
                    <div class="relative">
                        <input type="number"
                               id="gross_tonnage"
                               name="gross_tonnage"
                               value="{{ old('gross_tonnage', $masterKapal->gross_tonnage) }}"
                               min="0"
                               step="any"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gross_tonnage') border-red-500 @enderror"
                               placeholder="0.00">
                    </div>
                    @error('gross_tonnage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Gross tonnage kapal dalam ton</p>
                </div>
            </div>

            <!-- Row 4: Deadweight Tonnage & Length Overall -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="deadweight_tonnage" class="block text-sm font-medium text-gray-700 mb-2">
                        Deadweight Tonnage (DWT)
                    </label>
                    <div class="relative">
                        <input type="number"
                               id="deadweight_tonnage"
                               name="deadweight_tonnage"
                               value="{{ old('deadweight_tonnage', $masterKapal->deadweight_tonnage) }}"
                               min="0"
                               step="any"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deadweight_tonnage') border-red-500 @enderror"
                               placeholder="0.00">
                    </div>
                    @error('deadweight_tonnage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Deadweight tonnage kapal dalam ton</p>
                </div>

                <div>
                    <label for="length_overall" class="block text-sm font-medium text-gray-700 mb-2">
                        Length Overall (LOA)
                    </label>
                    <div class="relative">
                        <input type="number"
                               id="length_overall"
                               name="length_overall"
                               value="{{ old('length_overall', $masterKapal->length_overall) }}"
                               min="0"
                               step="any"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('length_overall') border-red-500 @enderror"
                               placeholder="0.00">
                    </div>
                    @error('length_overall')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Length overall (LOA) kapal dalam meter</p>
                </div>
            </div>

            <!-- Stowage Config -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Konfigurasi Peta Kapal (Stowage)
                </label>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-4">Tambahkan nomor Bay, Row, dan Tier kapal. Urutan akan otomatis disesuaikan di halaman Stowage Plan.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Bays -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Stowage Bays</label>
                            <div class="flex gap-2 mb-2">
                                <input type="text" id="bay_input_field" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="Contoh: 01, 03..." maxlength="3">
                                <button type="button" onclick="addPill('bay')" class="bg-purple-100 text-purple-700 hover:bg-purple-600 hover:text-white px-3 py-1.5 rounded-lg transition-colors text-sm font-medium shadow-sm">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div id="bay_pills_container" class="flex flex-wrap gap-1"></div>
                            <input type="hidden" name="stowage_bays" id="stowage_bays" value="{{ old('stowage_bays', $masterKapal->stowage_bays) }}">
                        </div>

                        <!-- Rows -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Stowage Rows</label>
                            <div class="flex gap-2 mb-2">
                                <input type="text" id="row_input_field" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Contoh: 02, 00, 01..." maxlength="3">
                                <button type="button" onclick="addPill('row')" class="bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg transition-colors text-sm font-medium shadow-sm">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div id="row_pills_container" class="flex flex-wrap gap-1"></div>
                            <input type="hidden" name="stowage_rows" id="stowage_rows" value="{{ old('stowage_rows', $masterKapal->stowage_rows) }}">
                        </div>

                        <!-- Tiers -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Stowage Tiers</label>
                            <div class="flex gap-2 mb-2">
                                <input type="text" id="tier_input_field" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" placeholder="Contoh: 82, 84, 86..." maxlength="3">
                                <button type="button" onclick="addPill('tier')" class="bg-green-100 text-green-700 hover:bg-green-600 hover:text-white px-3 py-1.5 rounded-lg transition-colors text-sm font-medium shadow-sm">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div id="tier_pills_container" class="flex flex-wrap gap-1"></div>
                            <input type="hidden" name="stowage_tiers" id="stowage_tiers" value="{{ old('stowage_tiers', $masterKapal->stowage_tiers) }}">
                        </div>
                    </div>
                </div>
                @error('stowage_bays')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('stowage_rows')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('stowage_tiers')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Catatan -->
            <div class="mb-6">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea id="catatan"
                          name="catatan"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('catatan') border-red-500 @enderror"
                          placeholder="Masukkan catatan tambahan tentang kapal">{{ old('catatan', $masterKapal->catatan) }}</textarea>
                @error('catatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status"
                        name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Status</option>
                    <option value="aktif" {{ old('status', $masterKapal->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $masterKapal->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-6"></div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('master-kapal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    const configs = {
        bay: {
            data: document.getElementById('stowage_bays').value ? document.getElementById('stowage_bays').value.split(',').map(s => s.trim()).filter(s => s) : [],
            input: 'bay_input_field',
            container: 'bay_pills_container',
            hidden: 'stowage_bays',
            color: 'purple',
            label: 'Bay'
        },
        row: {
            data: document.getElementById('stowage_rows').value ? document.getElementById('stowage_rows').value.split(',').map(s => s.trim()).filter(s => s) : [],
            input: 'row_input_field',
            container: 'row_pills_container',
            hidden: 'stowage_rows',
            color: 'blue',
            label: 'Row'
        },
        tier: {
            data: document.getElementById('stowage_tiers').value ? document.getElementById('stowage_tiers').value.split(',').map(s => s.trim()).filter(s => s) : [],
            input: 'tier_input_field',
            container: 'tier_pills_container',
            hidden: 'stowage_tiers',
            color: 'green',
            label: 'Tier'
        }
    };

    function renderPills(type) {
        const conf = configs[type];
        const container = document.getElementById(conf.container);
        if(!container) return;
        
        container.innerHTML = '';
        
        // Sort numerically
        conf.data.sort((a, b) => parseInt(a) - parseInt(b));
        
        if (conf.data.length === 0) {
            container.innerHTML = '<span class="text-[10px] text-gray-400 italic">Belum ada data.</span>';
        }
        
        conf.data.forEach(item => {
            const pill = document.createElement('div');
            pill.className = `inline-flex items-center bg-white border border-${conf.color}-200 text-${conf.color}-700 rounded-full px-2 py-0.5 shadow-sm text-xs group transition-colors hover:border-red-300 hover:bg-red-50`;
            pill.innerHTML = `
                <span class="font-bold mr-1">${item}</span>
                <button type="button" onclick="removePill('${type}', '${item}')" class="text-${conf.color}-400 hover:text-red-500 focus:outline-none transition-colors">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            `;
            container.appendChild(pill);
        });

        document.getElementById(conf.hidden).value = conf.data.join(',');
    }

    function addPill(type) {
        const conf = configs[type];
        const input = document.getElementById(conf.input);
        if(!input) return;
        
        let val = input.value.trim();
        // pad with 0 if single digit
        if (val.length === 1 && !isNaN(val)) val = '0' + val;
        
        if (val && !conf.data.includes(val) && !isNaN(val)) {
            conf.data.push(val);
            renderPills(type);
            input.value = '';
        } else if(isNaN(val)) {
            alert('Nilai harus berupa angka!');
        } else if(conf.data.includes(val)) {
            alert(conf.label + ' sudah ada!');
        }
        input.focus();
    }

    function removePill(type, item) {
        configs[type].data = configs[type].data.filter(b => b !== item);
        renderPills(type);
    }

    // Bind Enter key
    ['bay', 'row', 'tier'].forEach(type => {
        document.getElementById(configs[type].input)?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addPill(type);
            }
        });
        renderPills(type); // Initial render
    });
</script>
@endpush

@endsection
