@extends('layouts.app')

@section('title', 'Buat Broadcast WA')
@section('page_title', 'Buat Broadcast WhatsApp')

@section('content')
<div class="bg-white shadow rounded-lg p-6 font-sans max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Buat Broadcast Baru</h2>
        <a href="{{ route('master.wa-broadcast.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('master.wa-broadcast.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Pilihan Kapal -->
            <div>
                <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Nama Kapal <span class="text-red-500">*</span></label>
                <select name="nama_kapal" id="nama_kapal" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <option value="">-- Pilih Kapal --</option>
                    @foreach($kapals as $kapal)
                        <option value="{{ $kapal }}" {{ old('nama_kapal') == $kapal ? 'selected' : '' }}>{{ $kapal }}</option>
                    @endforeach
                </select>
                @error('nama_kapal') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Pilihan Voyage -->
            <div>
                <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">No Voyage <span class="text-red-500">*</span></label>
                <select name="no_voyage" id="no_voyage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <option value="">-- Pilih Voyage --</option>
                    @foreach($voyages as $voyage)
                        <option value="{{ $voyage }}" {{ old('no_voyage') == $voyage ? 'selected' : '' }}>{{ $voyage }}</option>
                    @endforeach
                </select>
                @error('no_voyage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="kategori_masalah" class="block text-sm font-medium text-gray-700 mb-2">Kategori Kendala <span class="text-red-500">*</span></label>
            <select name="kategori_masalah" id="kategori_masalah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                <option value="">-- Pilih Kategori Kendala --</option>
                <option value="Cuaca Buruk" {{ old('kategori_masalah') == 'Cuaca Buruk' ? 'selected' : '' }}>Cuaca Buruk</option>
                <option value="Kerusakan Mesin Kapal" {{ old('kategori_masalah') == 'Kerusakan Mesin Kapal' ? 'selected' : '' }}>Kerusakan Mesin Kapal</option>
                <option value="Antrean Pelabuhan" {{ old('kategori_masalah') == 'Antrean Pelabuhan' ? 'selected' : '' }}>Antrean Pelabuhan</option>
                <option value="Dokumen Tertunda" {{ old('kategori_masalah') == 'Dokumen Tertunda' ? 'selected' : '' }}>Dokumen Tertunda</option>
                <option value="Lainnya" {{ old('kategori_masalah') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('kategori_masalah') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label for="deskripsi_masalah" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi / Keterangan Tambahan</label>
            <textarea name="deskripsi_masalah" id="deskripsi_masalah" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: Keterlambatan diperkirakan sekitar 2 hari...">{{ old('deskripsi_masalah') }}</textarea>
            @error('deskripsi_masalah') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <label for="template_id" class="block text-sm font-bold text-gray-800 mb-3">Pilih Template WA <span class="text-red-500">*</span></label>
            <select name="template_id" id="template_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                <option value="">-- Pilih Template Pesan --</option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>{{ $template->nama_template }}</option>
                @endforeach
            </select>
            @error('template_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            
            <div class="mt-3 text-xs text-gray-500 italic">
                * Pesan akan dibuat otomatis berdasarkan variabel seperti nama kapal, no voyage, dan kategori masalah sesuai dengan isi template yang dipilih. Data nomor shipper otomatis ditarik dari tabel manifest berdasarkan Kapal & Voyage yang dipilih di atas.
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('master.wa-broadcast.index') }}" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Preview & Kirim Pesan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<!-- Add Select2 if available in the project -->
<script>
    $(document).ready(function() {
        if($.fn.select2) {
            $('#nama_kapal, #no_voyage, #kategori_masalah, #template_id').select2({
                width: '100%'
            });
        }
    });
</script>
@endpush
@endsection
