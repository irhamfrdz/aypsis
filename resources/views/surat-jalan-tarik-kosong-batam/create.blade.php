@extends('layouts.app')

@section('title', 'Tambah Surat Jalan Tarik Kosong Batam')
@section('page_title', 'Tambah Surat Jalan Tarik Kosong Batam')

@section('content')
<div class="bg-white shadow-lg rounded-xl overflow-hidden">
    <div class="bg-indigo-700 px-6 py-4">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i class="fas fa-plus-circle mr-3"></i> Form Surat Jalan Tarik Kosong
        </h2>
    </div>

    <form action="{{ route('surat-jalan-tarik-kosong-batam.store') }}" method="POST" class="p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Informasi Utama -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-indigo-600"></i> Informasi Utama
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_surat_jalan" class="block text-sm font-medium text-gray-700">Tanggal SJ <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_surat_jalan" id="tanggal_surat_jalan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label for="no_surat_jalan" class="block text-sm font-medium text-gray-700">No. Surat Jalan <span class="text-red-500">*</span></label>
                        <div class="flex mt-1">
                            <input type="text" name="no_surat_jalan" id="no_surat_jalan" class="block w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required readonly>
                            <button type="button" id="btn-generate-number" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="no_tiket_do" class="block text-sm font-medium text-gray-700">No. Tiket / DO</label>
                    <input type="text" name="no_tiket_do" id="no_tiket_do" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pengirim" class="block text-sm font-medium text-gray-700">Pengirim</label>
                        <input type="text" name="pengirim" id="pengirim" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="penerima" class="block text-sm font-medium text-gray-700">Penerima</label>
                        <input type="text" name="penerima" id="penerima" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tujuan_pengambilan" class="block text-sm font-medium text-gray-700">Tujuan Pengambilan</label>
                        <input type="text" name="tujuan_pengambilan" id="tujuan_pengambilan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="tujuan_pengiriman" class="block text-sm font-medium text-gray-700">Tujuan Pengiriman</label>
                        <input type="text" name="tujuan_pengiriman" id="tujuan_pengiriman" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Transportasi & Kontainer -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 flex items-center">
                    <i class="fas fa-truck mr-2 text-indigo-600"></i> Transportasi & Kontainer
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="no_plat" class="block text-sm font-medium text-gray-700">No. Plat / Armada</label>
                        <select name="no_plat" id="no_plat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm select2">
                            <option value="">-- Pilih Armada --</option>
                            @foreach($mobils as $mobil)
                                <option value="{{ $mobil->nomor_polisi }}">{{ $mobil->nomor_polisi }} ({{ $mobil->merek }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="supir" class="block text-sm font-medium text-gray-700">Supir Utama</label>
                        <select name="supir" id="supir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm select2">
                            <option value="">-- Pilih Supir --</option>
                            @foreach($supirs as $supir)
                                <option value="{{ $supir->nama_lengkap }}">{{ $supir->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="supir2" class="block text-sm font-medium text-gray-700">Supir Cadangan</label>
                        <select name="supir2" id="supir2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm select2">
                            <option value="">-- Pilih Supir --</option>
                            @foreach($supirs as $supir)
                                <option value="{{ $supir->nama_lengkap }}">{{ $supir->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="kenek" class="block text-sm font-medium text-gray-700">Kenek</label>
                        <select name="kenek" id="kenek" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm select2">
                            <option value="">-- Pilih Kenek --</option>
                            @foreach($keneks as $kenek)
                                <option value="{{ $kenek->nama_lengkap }}">{{ $kenek->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="no_kontainer" class="block text-sm font-medium text-gray-700">No. Kontainer</label>
                        <input type="text" name="no_kontainer" id="no_kontainer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700">Ukuran</label>
                        <select name="size" id="size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Pilih Ukuran --</option>
                            <option value="20">20 FT</option>
                            <option value="40">40 FT</option>
                            <option value="45">45 FT</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700">Tipe Kontainer</label>
                        <select name="tipe_kontainer" id="tipe_kontainer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="GP">GP (General Purpose)</option>
                            <option value="HC">HC (High Cube)</option>
                            <option value="FR">FR (Flat Rack)</option>
                            <option value="OT">OT (Open Top)</option>
                            <option value="RF">RF (Reefer)</option>
                        </select>
                    </div>
                    <div>
                        <label for="f_e" class="block text-sm font-medium text-gray-700">F / E</label>
                        <select name="f_e" id="f_e" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="E" selected>Empty (E)</option>
                            <option value="F">Full (F)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="uang_jalan" class="block text-sm font-medium text-gray-700">Uang Jalan</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="text" name="uang_jalan" id="uang_jalan" class="mt-1 block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm currency" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="draft">Draft</option>
                            <option value="active" selected>Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
            </div>
        </div>

        <div class="mt-10 border-t pt-6 flex justify-end space-x-3">
            <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-save mr-2"></i> Simpan Surat Jalan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Currency formatting
        $('.currency').on('input', function() {
            var value = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(new Intl.NumberFormat('id-ID').format(value));
        });

        // Auto generate number
        function generateNumber() {
            var date = $('#tanggal_surat_jalan').val();
            $.ajax({
                url: "{{ route('surat-jalan-tarik-kosong-batam.generate-number') }}",
                data: { date: date },
                success: function(response) {
                    $('#no_surat_jalan').val(response.number);
                }
            });
        }

        $('#btn-generate-number').click(generateNumber);
        $('#tanggal_surat_jalan').change(generateNumber);
        
        // Initial generate
        generateNumber();
        
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
@endpush
@endsection
