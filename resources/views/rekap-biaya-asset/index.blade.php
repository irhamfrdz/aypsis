@extends('layouts.app')

@section('title', 'Rekap Biaya Asset')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-800 rounded-2xl shadow-xl border-none p-8 mb-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="bg-emerald-500/30 text-emerald-100 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider">Laporan</span>
                <h1 class="text-3xl font-extrabold mt-2 tracking-tight">Rekap Biaya Asset</h1>
                <p class="text-emerald-50 mt-2 text-sm max-w-xl">
                    Pilih kategori asset, asset terkait, serta bulan/tahun untuk melihat akumulasi biaya pemakaian (Amprahan) dari asset tersebut.
                </p>
            </div>
            <div class="bg-white/10 p-3 rounded-2xl backdrop-blur-md hidden md:block">
                <i class="fas fa-truck-moving text-4xl text-emerald-100"></i>
            </div>
        </div>
    </div>

    <!-- Selection Card -->
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transform hover:shadow-xl transition-all duration-300">
        <div class="bg-gray-50/50 border-b border-gray-100 px-8 py-5">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-filter text-emerald-600 mr-2"></i> Parameter Rekapitulasi
            </h2>
        </div>
        
        <div class="p-8">
            <form action="{{ route('rekap-biaya-asset.show') }}" method="GET" id="rekapForm">
                <div class="space-y-6">
                    
                    <!-- Kategori Asset -->
                    <div>
                        <label for="asset_type_select" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-layer-group text-gray-400 mr-1"></i> Kategori Asset
                        </label>
                        <select name="asset_type" id="asset_type_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent select2" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="mobil">Mobil (Truk/Kendaraan)</option>
                            <option value="alat_berat">Alat Berat</option>
                        </select>
                    </div>

                    <!-- Asset Selection (Mobil) -->
                    <div id="wrapper_mobil" style="display:none;">
                        <label for="mobil_select" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-truck text-gray-400 mr-1"></i> Pilih Mobil/Truk
                        </label>
                        <select name="asset_id_mobil" id="mobil_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent select2">
                            <option value="">-- Pilih Mobil --</option>
                            @foreach($mobils as $mobil)
                                <option value="{{ $mobil->id }}">{{ $mobil->nomor_polisi ?? $mobil->no_kir ?? 'Truk '.$mobil->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Asset Selection (Alat Berat) -->
                    <div id="wrapper_alat_berat" style="display:none;">
                        <label for="alat_berat_select" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tractor text-gray-400 mr-1"></i> Pilih Alat Berat
                        </label>
                        <select name="asset_id_alat_berat" id="alat_berat_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent select2">
                            <option value="">-- Pilih Alat Berat --</option>
                            @foreach($alatBerats as $ab)
                                <option value="{{ $ab->id }}">{{ $ab->nama ?? 'Alat Berat '.$ab->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="asset_id" id="asset_id_final" value="">

                    <!-- Waktu -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="bulan_select" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-gray-400 mr-1"></i> Bulan (Opsional)
                            </label>
                            <select name="bulan" id="bulan_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent select2">
                                <option value="">-- Semua Bulan --</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ date("F", mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="tahun_select" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar text-gray-400 mr-1"></i> Tahun (Opsional)
                            </label>
                            <select name="tahun" id="tahun_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent select2">
                                <option value="">-- Semua Tahun --</option>
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button type="button" id="btnReset" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition duration-200 text-sm flex items-center">
                            <i class="fas fa-redo-alt mr-2"></i> Reset
                        </button>
                        <button type="submit" id="btnSubmit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition duration-200 text-sm flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> Tampilkan Rekap
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });

        $('#asset_type_select').on('change', function() {
            var val = $(this).val();
            $('#wrapper_mobil').hide();
            $('#wrapper_alat_berat').hide();
            $('#mobil_select').prop('required', false);
            $('#alat_berat_select').prop('required', false);

            if(val === 'mobil') {
                $('#wrapper_mobil').show();
                $('#mobil_select').prop('required', true);
            } else if(val === 'alat_berat') {
                $('#wrapper_alat_berat').show();
                $('#alat_berat_select').prop('required', true);
            }
        });

        $('#rekapForm').on('submit', function(e) {
            var type = $('#asset_type_select').val();
            if(type === 'mobil') {
                $('#asset_id_final').val($('#mobil_select').val());
            } else if(type === 'alat_berat') {
                $('#asset_id_final').val($('#alat_berat_select').val());
            } else {
                e.preventDefault();
                alert('Pilih Kategori Asset!');
            }
        });

        $('#btnReset').on('click', function() {
            $('#asset_type_select').val(null).trigger('change');
            $('#mobil_select').val(null).trigger('change');
            $('#alat_berat_select').val(null).trigger('change');
            $('#bulan_select').val(null).trigger('change');
            $('#tahun_select').val(null).trigger('change');
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Premium Select2 overrides matching layouts styling */
    .select2-container .select2-selection--single {
        height: 48px !important;
        padding-top: 10px !important;
        border-color: #e5e7eb !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease-in-out;
    }
    .select2-container--default .select2-selection--single:hover {
        border-color: #cbd5e1 !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #059669 !important; /* emerald-600 */
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #1f2937 !important;
        font-weight: 500 !important;
        padding-left: 16px !important;
    }
    .select2-dropdown {
        border-color: #f3f4f6 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden;
    }
    .select2-results__option {
        padding: 10px 16px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #059669 !important;
    }
</style>
@endpush
@endsection
