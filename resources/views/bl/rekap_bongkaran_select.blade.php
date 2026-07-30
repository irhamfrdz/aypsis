@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center mr-4">
                    <i class="fas fa-ship text-indigo-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Rekap Bongkar/Muat Barang</h1>
                    <p class="text-gray-500 text-sm">Pilih kapal dan voyage untuk melihat rekapan bongkar/muat barang</p>
                </div>
            </div>
            <div>
                <a href="{{ route('bl.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 transition duration-150 ease-in-out">
                    <i class="fas fa-list mr-2 text-gray-500"></i> Daftar BL
                </a>
            </div>
        </div>
    </div>

    <!-- Selection Form Card -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 max-w-3xl mx-auto">
        <form action="{{ route('bl.rekap-bongkaran') }}" method="GET" id="rekapForm">
            <div class="space-y-6">
                <!-- Ship Selection -->
                <div>
                    <label for="nama_kapal" class="block text-sm font-semibold text-gray-700 mb-2">Nama Kapal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-anchor"></i>
                        </div>
                        <select id="nama_kapal" name="nama_kapal" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 bg-gray-50/50 hover:bg-gray-50/100 transition duration-150" required>
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($masterKapals->unique('nama_kapal')->sortBy('nama_kapal') as $kapal)
                                <option value="{{ $kapal->nama_kapal }}">{{ $kapal->nama_kapal }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Voyage Selection -->
                <div>
                    <label for="no_voyage" class="block text-sm font-semibold text-gray-700 mb-2">Voyage <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-compass"></i>
                        </div>
                        <select id="no_voyage" name="no_voyage" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 bg-gray-50/50 hover:bg-gray-50/100 transition duration-150" required disabled>
                            <option value="">- Pilih Kapal Terlebih Dahulu -</option>
                        </select>
                    </div>
                </div>

                <!-- Jenis Tanggal & Input -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Estimasi Tiba -->
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center mb-3">
                            <input type="radio" id="jenis_estimasi" name="jenis_tanggal" value="estimasi_tiba" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                            <label for="jenis_estimasi" class="ml-2 block text-sm font-semibold text-gray-700">
                                Gunakan Estimasi Tiba
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Isi kolom ini jika ingin mengupdate tanggal Estimasi Tiba secara manual.</p>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <input type="date" id="estimasi_tiba" name="estimasi_tiba" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm text-gray-800 bg-white">
                        </div>
                    </div>

                    <!-- Tanggal Berangkat -->
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center mb-3">
                            <input type="radio" id="jenis_berangkat" name="jenis_tanggal" value="tanggal_berangkat" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <label for="jenis_berangkat" class="ml-2 block text-sm font-semibold text-gray-700">
                                Gunakan Tanggal Berangkat
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Isi kolom ini jika ingin mengupdate Tanggal Berangkat secara manual.</p>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <input type="date" id="tanggal_berangkat" name="tanggal_berangkat" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm text-gray-800 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-xl shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <i class="fas fa-file-invoice mr-2"></i> Lihat Rekap Bongkar/Muat
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('#nama_kapal').select2({
        width: '100%',
        placeholder: '-- Pilih Kapal --'
    });
    
    $('#no_voyage').select2({
        width: '100%',
        placeholder: '- Pilih Kapal Terlebih Dahulu -'
    });

    $('#nama_kapal').on('change', function() {
        const namaKapal = $(this).val();
        const voyageSelect = $('#no_voyage');
        
        voyageSelect.empty().append('<option value="">Loading...</option>').prop('disabled', true).trigger('change.select2');

        if (!namaKapal) {
            voyageSelect.empty().append('<option value="">- Pilih Kapal Terlebih Dahulu -</option>').trigger('change.select2');
            return;
        }

        $.ajax({
            url: `{{ route('bl.get-voyage-by-kapal', [], false) }}`,
            type: 'GET',
            data: { nama_kapal: namaKapal },
            dataType: 'json',
            success: function(data) {
                voyageSelect.empty();
                if (data.success && data.voyages && data.voyages.length) {
                    voyageSelect.append('<option value="">-- Pilih Voyage --</option>');
                    data.voyages.forEach(function(v) {
                        voyageSelect.append(new Option(v, v));
                    });
                    voyageSelect.prop('disabled', false).trigger('change.select2');
                } else {
                    voyageSelect.append('<option value="">Belum ada voyage untuk kapal ini</option>').prop('disabled', true).trigger('change.select2');
                }
            },
            error: function(err) {
                console.error('Fetch error:', err);
                voyageSelect.empty().append('<option value="">Error loading voyage</option>').prop('disabled', true).trigger('change.select2');
            }
        });
    });
});
</script>
@endpush
@endsection
