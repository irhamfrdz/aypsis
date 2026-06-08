@extends('layouts.app')

@section('title', 'Tambah Perbaikan Kontainer')
@section('page_title', 'Perbaikan Kontainer')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 styling to match Tailwind */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0 1rem;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        color: #111827;
        padding-left: 0;
        padding-right: 20px;
        width: 100%;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }

    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
        outline: none;
    }

    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-4 overflow-y-auto h-full pb-24">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-150">
            <div>
                <h1 class="text-lg font-bold text-gray-900">Tambah Perbaikan Kontainer</h1>
                <p class="text-xs text-gray-500 mt-1">Buat data baru perbaikan kontainer yang mengalami kerusakan.</p>
            </div>
            <div>
                <a href="{{ route('perbaikan-kontainer.index') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-chevron-left mr-1.5"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg" role="alert">
                    <div class="font-semibold text-sm mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan pada input:</div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('perbaikan-kontainer.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Container Info Section -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4"><i class="fas fa-box mr-2 text-blue-500"></i>Informasi Kontainer</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- No. Kontainer (Select2 Search) -->
                        <div class="md:col-span-2">
                            <label for="no_kontainer_select" class="block text-sm font-semibold text-gray-700 mb-1">
                                No. Kontainer / Serial Number <span class="text-red-500">*</span>
                            </label>
                            <select id="no_kontainer_select" class="w-full" required></select>
                            <input type="hidden" name="no_kontainer" id="no_kontainer" value="{{ old('no_kontainer') }}">
                            <p class="mt-1 text-xs text-gray-400">Ketik minimal 1 karakter nomor seri kontainer untuk mencari</p>
                        </div>

                        <!-- Status Kontainer Saat Ini (Readonly) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-500 mb-1">Status Kontainer</label>
                            <input type="text" id="status_kontainer" readonly 
                                   class="w-full px-3 py-2 border border-gray-200 bg-gray-100 rounded-lg text-sm text-gray-500 cursor-not-allowed outline-none" 
                                   placeholder="-">
                        </div>

                        <!-- Ukuran (Auto fill / Manual override) -->
                        <div>
                            <label for="ukuran" class="block text-sm font-semibold text-gray-700 mb-1">Ukuran (Feet)</label>
                            <input type="text" name="ukuran" id="ukuran" value="{{ old('ukuran') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                                   placeholder="Contoh: 20, 40">
                        </div>

                        <!-- Tipe Kontainer (Auto fill / Manual override) -->
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-semibold text-gray-700 mb-1">Tipe Kontainer</label>
                            <input type="text" name="tipe_kontainer" id="tipe_kontainer" value="{{ old('tipe_kontainer') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                                   placeholder="Contoh: DRY, REEFER">
                        </div>
                    </div>
                </div>

                <!-- Repair Details Section -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b border-gray-100 pb-2"><i class="fas fa-tools mr-2 text-indigo-500"></i>Detail Perbaikan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Vendor Bengkel -->
                        <div>
                            <label for="vendor_bengkel_id" class="block text-sm font-semibold text-gray-700 mb-1">
                                Bengkel / Vendor <span class="text-red-500">*</span>
                            </label>
                            <select name="vendor_bengkel_id" id="vendor_bengkel_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                <option value="">-- Pilih Bengkel --</option>
                                @foreach($bengkels as $bengkel)
                                    <option value="{{ $bengkel->id }}" {{ old('vendor_bengkel_id') == $bengkel->id ? 'selected' : '' }}>
                                        {{ $bengkel->nama_bengkel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Perbaikan -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">
                                Status Perbaikan <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending (Draft)</option>
                                <option value="proses" {{ old('status', 'proses') === 'proses' ? 'selected' : '' }}>Proses Perbaikan</option>
                                <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="batal" {{ old('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>

                        <!-- Tanggal Masuk -->
                        <div>
                            <label for="tanggal_masuk" class="block text-sm font-semibold text-gray-700 mb-1">
                                Tanggal Masuk Perbaikan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" 
                                   value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        </div>

                        <!-- Tanggal Selesai -->
                        <div>
                            <label for="tanggal_keluar" class="block text-sm font-semibold text-gray-700 mb-1">
                                Tanggal Selesai Perbaikan <span id="tanggal_keluar_required_star" class="text-red-500 hidden">*</span>
                            </label>
                            <input type="date" name="tanggal_keluar" id="tanggal_keluar" 
                                   value="{{ old('tanggal_keluar') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        </div>

                        <!-- Estimasi Biaya -->
                        <div class="md:col-span-2">
                            <label for="estimasi_biaya" class="block text-sm font-semibold text-gray-700 mb-1">
                                Estimasi Biaya (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">Rp</span>
                                </div>
                                <input type="number" name="estimasi_biaya" id="estimasi_biaya" 
                                       value="{{ old('estimasi_biaya', 0) }}" min="0" required
                                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Pengecatan Kontainer (Paint Fields) -->
                    <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100/80 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-blue-900 uppercase tracking-wider"><i class="fas fa-paint-roller mr-2"></i>Pengecatan Kontainer</h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_cat" id="is_cat" value="1" class="sr-only peer" {{ old('is_cat') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-2 text-sm font-medium text-gray-700">Menggunakan Cat</span>
                            </label>
                        </div>

                        <div id="paint_fields_container" class="grid grid-cols-1 md:grid-cols-3 gap-4 hidden">
                            <!-- Vendor Cat -->
                            <div>
                                <label for="vendor_cat" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Vendor Cat <span class="text-red-500">*</span>
                                </label>
                                <select name="vendor_cat" id="vendor_cat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                    <option value="">-- Pilih Vendor Cat --</option>
                                    @foreach($paintVendors as $vendor)
                                        <option value="{{ $vendor->vendor }}" {{ old('vendor_cat') == $vendor->vendor ? 'selected' : '' }}>
                                            {{ $vendor->vendor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jenis/Status Cat -->
                            <div>
                                <label for="jenis_cat" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Status Cat <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_cat" id="jenis_cat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                    <option value="">-- Pilih Status Cat --</option>
                                    <option value="cat_sebagian" {{ old('jenis_cat') === 'cat_sebagian' ? 'selected' : '' }}>Sebagian</option>
                                    <option value="cat_full" {{ old('jenis_cat') === 'cat_full' ? 'selected' : '' }}>Full</option>
                                </select>
                            </div>

                            <!-- Biaya Cat -->
                            <div>
                                <label for="biaya_cat" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Biaya Cat (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="biaya_cat" id="biaya_cat" 
                                           value="{{ old('biaya_cat', 0) }}" min="0"
                                           class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan Kerusakan -->
                    <div>
                        <label for="keterangan_kerusakan" class="block text-sm font-semibold text-gray-700 mb-1">
                            Keterangan Kerusakan / Pekerjaan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="keterangan_kerusakan" id="keterangan_kerusakan" rows="4" required
                                  placeholder="Tuliskan detail kerusakan kontainer yang perlu diperbaiki..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">{{ old('keterangan_kerusakan') }}</textarea>
                    </div>

                    <!-- Selesai Fields Container (Toggled via JS) -->
                    <div id="selesai_fields_container" class="bg-green-50 p-4 rounded-xl border border-green-150 space-y-4 hidden">
                        <h4 class="text-sm font-bold text-green-800 uppercase tracking-wider"><i class="fas fa-check mr-2"></i>Data Penyelesaian</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Biaya Riil -->
                            <div class="md:col-span-2">
                                <label for="biaya_riil" class="block text-sm font-semibold text-green-800 mb-1">
                                    Biaya Riil (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-green-600 text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="biaya_riil" id="biaya_riil" 
                                           value="{{ old('biaya_riil') }}" min="0"
                                           class="w-full pl-9 pr-3 py-2 border border-green-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 focus:outline-none bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan Perbaikan -->
                        <div>
                            <label for="keterangan_perbaikan" class="block text-sm font-semibold text-green-800 mb-1">
                                Keterangan Perbaikan / Hasil Pekerjaan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="keterangan_perbaikan" id="keterangan_perbaikan" rows="3"
                                      placeholder="Tuliskan tindakan perbaikan yang telah dilakukan dan status akhir kontainer..."
                                      class="w-full px-3 py-2 border border-green-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 focus:outline-none bg-white">{{ old('keterangan_perbaikan') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 border-t border-gray-150 pt-5">
                    <a href="{{ route('perbaikan-kontainer.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-5 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle Selesai fields based on Status input
        function toggleSelesaiFields() {
            var statusVal = $('#status').val();
            if (statusVal === 'selesai') {
                $('#selesai_fields_container').slideDown(200);
                $('#tanggal_keluar, #biaya_riil, #keterangan_perbaikan').prop('required', true);
                $('#tanggal_keluar_required_star').removeClass('hidden');
                
                // Autofill completion date if empty
                if (!$('#tanggal_keluar').val()) {
                    var today = new Date().toISOString().split('T')[0];
                    $('#tanggal_keluar').val(today);
                }
            } else {
                $('#selesai_fields_container').slideUp(200);
                $('#tanggal_keluar, #biaya_riil, #keterangan_perbaikan').prop('required', false);
                $('#tanggal_keluar_required_star').addClass('hidden');
            }
        }

        // Trigger on page load and status input change
        toggleSelesaiFields();
        $('#status').on('change', function() {
            toggleSelesaiFields();
        });

        // Initialize Select2 Autocomplete Container Search
        $('#no_kontainer_select').select2({
            placeholder: 'Cari nomor kontainer...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("supir.api.kontainer.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        // Trigger on selection change
        $('#no_kontainer_select').on('select2:select', function(e) {
            var data = e.params.data;
            $('#no_kontainer').val(data.id);
            $('#status_kontainer').val(data.status ? data.status.toUpperCase() : '-');
            
            if (data.ukuran) {
                $('#ukuran').val(data.ukuran);
            }
            if (data.text) {
                // Infer type if contained in label or default to Dry
                var lowerText = data.text.toLowerCase();
                if (lowerText.includes('reefer')) {
                    $('#tipe_kontainer').val('REEFER');
                } else if (lowerText.includes('flat rack')) {
                    $('#tipe_kontainer').val('FLAT RACK');
                } else if (lowerText.includes('open top')) {
                    $('#tipe_kontainer').val('OPEN TOP');
                } else {
                    $('#tipe_kontainer').val('DRY');
                }
            }
        });

        // Clear input when selection is removed
        $('#no_kontainer_select').on('select2:clear', function() {
            $('#no_kontainer').val('');
            $('#status_kontainer').val('-');
            $('#ukuran').val('');
            $('#tipe_kontainer').val('');
        });

        function togglePaintFields() {
            if ($('#is_cat').is(':checked')) {
                $('#paint_fields_container').removeClass('hidden');
                $('#vendor_cat').prop('required', true);
                $('#jenis_cat').prop('required', true);
                $('#biaya_cat').prop('required', true);
            } else {
                $('#paint_fields_container').addClass('hidden');
                $('#vendor_cat').prop('required', false);
                $('#jenis_cat').prop('required', false);
                $('#biaya_cat').prop('required', false);
            }
        }

        $('#is_cat').on('change', function() {
            togglePaintFields();
        });

        // Trigger on load
        togglePaintFields();

        // Retain values if validation fails on form submit
        @if(old('no_kontainer'))
            var oldNo = '{{ old("no_kontainer") }}';
            var oldUkuran = '{{ old("ukuran") }}';
            var oldTipe = '{{ old("tipe_kontainer") }}';
            
            $('#no_kontainer').val(oldNo);
            
            // Set values inside Select2 selection
            var option = new Option(oldNo + (oldUkuran ? ' - ' + oldUkuran + 'ft' : ''), oldNo, true, true);
            $('#no_kontainer_select').append(option).trigger('change');
        @endif
    });
</script>
@endpush
