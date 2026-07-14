@extends('layouts.app')

@section('title', 'Rekap Biaya Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl shadow-xl border-none p-8 mb-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="bg-blue-500/30 text-blue-200 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider">Laporan</span>
                <h1 class="text-3xl font-extrabold mt-2 tracking-tight">Rekap Biaya Kapal & Voyage</h1>
                <p class="text-blue-100 mt-2 text-sm max-w-xl">
                    Pilih kapal dan nomor voyage untuk melihat akumulasi biaya kapal, ppn, pph, serta rincian biaya yang dikelompokkan berdasarkan klasifikasi.
                </p>
            </div>
            <div class="bg-white/10 p-3 rounded-2xl backdrop-blur-md hidden md:block">
                <i class="fas fa-ship text-4xl text-blue-200"></i>
            </div>
        </div>
    </div>

    <!-- Selection Card -->
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transform hover:shadow-xl transition-all duration-300">
        <div class="bg-gray-50/50 border-b border-gray-100 px-8 py-5">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-filter text-blue-600 mr-2"></i> Parameter Rekapitulasi
            </h2>
        </div>
        
        <div class="p-8">
            <form action="{{ route('rekap-biaya-kapal.show') }}" method="GET" id="rekapForm">
                <div class="space-y-6">
                    <!-- Pemilik Kapal Selection -->
                    <div>
                        <label for="pemilik_select" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-building text-gray-400 mr-1"></i> Pilih Pemilik Kapal
                        </label>
                        <select name="pemilik" id="pemilik_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent select2">
                            <option value="">-- Semua Pemilik --</option>
                            @foreach($pemilikList as $pemilik)
                                <option value="{{ $pemilik }}">{{ $pemilik }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ship Selection -->
                    <div>
                        <label for="kapal_select" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-anchor text-gray-400 mr-1"></i> Pilih Kapal
                        </label>
                        <select name="kapal" id="kapal_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent select2" required>
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal }}" data-pemilik="{{ $kapalPemilikMap[strtolower(trim($kapal))] ?? '' }}">{{ $kapal }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Voyage Selection -->
                    <div>
                        <label for="voyage_select" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-route text-gray-400 mr-1"></i> Pilih Nomor Voyage
                        </label>
                        <select name="voyage" id="voyage_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent select2" required disabled>
                            <option value="">-- Silakan Pilih Kapal Terlebih Dahulu --</option>
                        </select>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button type="button" id="btnReset" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition duration-200 text-sm flex items-center">
                            <i class="fas fa-redo-alt mr-2"></i> Reset
                        </button>
                        <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition duration-200 text-sm flex items-center">
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
        // Initialize Select2 with premium tailwind-aligned themes
        $('#pemilik_select').select2({
            placeholder: "-- Semua Pemilik --",
            allowClear: true,
            width: '100%'
        });

        $('#kapal_select').select2({
            placeholder: "-- Pilih Kapal --",
            allowClear: true,
            width: '100%'
        });

        $('#voyage_select').select2({
            placeholder: "-- Pilih Nomor Voyage --",
            allowClear: true,
            width: '100%'
        });

        // Store original options for filtering
        const originalKapalOptions = $('#kapal_select option').clone();

        // AJAX Voyage Loader when Ship changes
        $('#pemilik_select').on('change', function() {
            const pemilik = $(this).val();
            const $kapalSelect = $('#kapal_select');
            const currentVal = $kapalSelect.val();

            $kapalSelect.empty();

            if (!pemilik) {
                // Show all
                $kapalSelect.append(originalKapalOptions.clone());
            } else {
                // Filter
                $kapalSelect.append(originalKapalOptions.filter(function() {
                    return $(this).val() === "" || $(this).data('pemilik') === pemilik;
                }).clone());
            }
            
            // Try to keep previous selection if it's still available
            if ($kapalSelect.find('option[value="' + currentVal + '"]').length > 0) {
                $kapalSelect.val(currentVal);
            } else {
                $kapalSelect.val(null);
            }
            $kapalSelect.trigger('change');
        });

        $('#kapal_select').on('change', function() {
            const kapal = $(this).val();
            const $voyageSelect = $('#voyage_select');
            
            // Clear current voyage options
            $voyageSelect.empty().trigger('change');

            if (!kapal) {
                $voyageSelect.prop('disabled', true);
                $voyageSelect.append(new Option('-- Silakan Pilih Kapal Terlebih Dahulu --', '', true, true)).trigger('change');
                return;
            }

            // Set loading state
            $voyageSelect.prop('disabled', true);
            $voyageSelect.append(new Option('Memuat nomor voyage...', '', true, true)).trigger('change');

            // Fetch Voyages from Controller
            $.ajax({
                url: "{{ route('rekap-biaya-kapal.get-voyages') }}",
                type: "GET",
                data: { kapal: kapal },
                dataType: "json",
                success: function(data) {
                    $voyageSelect.empty();
                    $voyageSelect.prop('disabled', false);

                    if (data.length === 0) {
                        $voyageSelect.append(new Option('-- Tidak ada Voyage ditemukan untuk Kapal ini --', '', true, true)).trigger('change');
                        return;
                    }

                    $voyageSelect.append(new Option('-- Pilih Nomor Voyage --', '', true, true));
                    data.forEach(function(voyage) {
                        $voyageSelect.append(new Option(voyage, voyage, false, false));
                    });
                    $voyageSelect.trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                    $voyageSelect.empty();
                    $voyageSelect.append(new Option('-- Gagal memuat data --', '', true, true)).trigger('change');
                }
            });
        });

        // Reset functionality
        $('#btnReset').on('click', function() {
            $('#pemilik_select').val(null).trigger('change');
            $('#kapal_select').val(null).trigger('change');
            $('#voyage_select').val(null).trigger('change');
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
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
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
        background-color: #3b82f6 !important;
    }
</style>
@endpush
@endsection
