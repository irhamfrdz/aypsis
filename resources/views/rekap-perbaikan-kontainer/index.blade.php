@extends('layouts.app')

@section('title', 'Rekap Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl shadow-xl border-none p-8 mb-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="bg-blue-500/30 text-blue-200 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider">Laporan</span>
                <h1 class="text-3xl font-extrabold mt-2 tracking-tight">Rekap Perbaikan Kontainer</h1>
                <p class="text-blue-100 mt-2 text-sm max-w-xl">
                    Cari dan pilih nomor kontainer untuk melihat rekapitulasi riwayat perbaikannya.
                </p>
            </div>
            <div class="bg-white/10 p-3 rounded-2xl backdrop-blur-md hidden md:block">
                <i class="fas fa-tools text-4xl text-blue-200"></i>
            </div>
        </div>
    </div>

    <!-- Selection Card -->
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transform hover:shadow-xl transition-all duration-300">
        <div class="bg-gray-50/50 border-b border-gray-100 px-8 py-5">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-search text-blue-600 mr-2"></i> Pencarian Kontainer
            </h2>
        </div>
        
        <div class="p-8">
            <form action="{{ route('rekap-perbaikan-kontainer.show') }}" method="GET" id="rekapForm">
                <div class="space-y-6">
                    <!-- Container Selection -->
                    <div>
                        <label for="kontainer_select" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-box text-gray-400 mr-1"></i> Pilih Nomor Kontainer
                        </label>
                        <select name="nomor_kontainer" id="kontainer_select" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent select2" required>
                            <option value="">-- Pilih Nomor Kontainer --</option>
                            @foreach($allKontainers as $kontainer)
                                <option value="{{ $kontainer }}">{{ $kontainer }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button type="button" id="btnReset" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition duration-200 text-sm flex items-center">
                            <i class="fas fa-redo-alt mr-2"></i> Reset
                        </button>
                        <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition duration-200 text-sm flex items-center">
                            <i class="fas fa-clipboard-list mr-2"></i> Tampilkan Rekap
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
        $('#kontainer_select').select2({
            placeholder: "-- Pilih Nomor Kontainer --",
            allowClear: true,
            width: '100%'
        });

        $('#btnReset').on('click', function() {
            $('#kontainer_select').val(null).trigger('change');
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
