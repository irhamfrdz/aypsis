@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-gray-900">Tambah Surat Jalan</h1>
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">Tarik Kosong Batam</span>
                </div>
                <p class="text-xs text-gray-600 mt-1">Buat surat jalan tarik kosong baru (Batam)</p>
            </div>
            <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('surat-jalan-tarik-kosong-batam.store') }}" method="POST" class="p-4">
            @csrf

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <div class="font-medium">Terdapat kesalahan pada form:</div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Dasar</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date"
                           name="tanggal_surat_jalan"
                           id="tanggal_surat_jalan"
                           value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_surat_jalan') border-red-500 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <div class="flex">
                        <input type="text"
                               name="no_surat_jalan"
                               id="no_surat_jalan"
                               value="{{ old('no_surat_jalan') }}"
                               required
                               readonly
                               placeholder="SJTK/YYYY/MM/XXXX"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @error('no_surat_jalan') border-red-500 @enderror">
                        <button type="button"
                                id="btn-generate-number"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-r-lg text-sm">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Tiket / DO</label>
                    <input type="text"
                           name="no_tiket_do"
                           value="{{ old('no_tiket_do') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <input type="text"
                           name="tujuan_pengambilan"
                           value="{{ old('tujuan_pengambilan') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text"
                           name="tujuan_pengiriman"
                           value="{{ old('tujuan_pengiriman') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Armada Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Armada</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat / Armada</label>
                    <select name="no_plat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 select2">
                        <option value="">-- Pilih Armada --</option>
                        @foreach($mobils as $mobil)
                            <option value="{{ $mobil->nomor_polisi }}" {{ old('no_plat') == $mobil->nomor_polisi ? 'selected' : '' }}>
                                {{ $mobil->nomor_polisi }} ({{ $mobil->merek }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir Utama</label>
                    <select name="supir"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 select2">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" {{ old('supir') == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir Cadangan</label>
                    <select name="supir2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 select2">
                        <option value="">-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                            <option value="{{ $supir->nama_lengkap }}" {{ old('supir2') == $supir->nama_lengkap ? 'selected' : '' }}>
                                {{ $supir->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <select name="kenek"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 select2">
                        <option value="">-- Pilih Kenek --</option>
                        @foreach($keneks as $kenek)
                            <option value="{{ $kenek->nama_lengkap }}" {{ old('kenek') == $kenek->nama_lengkap ? 'selected' : '' }}>
                                {{ $kenek->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kontainer Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Kontainer</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    <input type="text"
                           name="no_kontainer"
                           value="{{ old('no_kontainer') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                    <select name="size"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Ukuran --</option>
                        <option value="20" {{ old('size') == '20' ? 'selected' : '' }}>20 FT</option>
                        <option value="40" {{ old('size') == '40' ? 'selected' : '' }}>40 FT</option>
                        <option value="45" {{ old('size') == '45' ? 'selected' : '' }}>45 FT</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kontainer</label>
                    <select name="tipe_kontainer"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="GP" {{ old('tipe_kontainer') == 'GP' ? 'selected' : '' }}>GP (General Purpose)</option>
                        <option value="HC" {{ old('tipe_kontainer') == 'HC' ? 'selected' : '' }}>HC (High Cube)</option>
                        <option value="FR" {{ old('tipe_kontainer') == 'FR' ? 'selected' : '' }}>FR (Flat Rack)</option>
                        <option value="OT" {{ old('tipe_kontainer') == 'OT' ? 'selected' : '' }}>OT (Open Top)</option>
                        <option value="RF" {{ old('tipe_kontainer') == 'RF' ? 'selected' : '' }}>RF (Reefer)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">F / E</label>
                    <select name="f_e"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="E" {{ old('f_e', 'E') == 'E' ? 'selected' : '' }}>Empty (E)</option>
                        <option value="F" {{ old('f_e') == 'F' ? 'selected' : '' }}>Full (F)</option>
                    </select>
                </div>

                <!-- Keuangan Information -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Lain-lain</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Jalan</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            Rp
                        </span>
                        <input type="text"
                               name="uang_jalan"
                               id="uang_jalan"
                               value="{{ old('uang_jalan', '0') }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 currency">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select name="status"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan"
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-150">
                    Batal
                </a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors duration-150 flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Surat Jalan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Currency formatting
        function formatCurrency(value) {
            return new Intl.NumberFormat('id-ID').format(value.replace(/[^0-9]/g, ''));
        }

        $('.currency').on('input', function() {
            $(this).val(formatCurrency($(this).val()));
        });

        // Auto generate number
        function generateNumber() {
            var date = $('#tanggal_surat_jalan').val();
            if(!date) return;
            
            $.ajax({
                url: "{{ route('surat-jalan-tarik-kosong-batam.generate-number') }}",
                data: { date: date },
                success: function(response) {
                    $('#no_surat_jalan').val(response.number);
                },
                error: function() {
                    console.error('Gagal generate nomor surat jalan');
                }
            });
        }

        $('#btn-generate-number').click(generateNumber);
        $('#tanggal_surat_jalan').change(generateNumber);
        
        // Initial generate if empty
        if (!$('#no_surat_jalan').val()) {
            generateNumber();
        }
        
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
@endpush
@endsection
