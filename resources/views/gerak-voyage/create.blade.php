@extends('layouts.app')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 styling to match Tailwind */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border-color: #d1d5db;
        border-radius: 0.375rem;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: #374151;
        font-size: 0.875rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-plus-circle mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Tanggal Gerak Voyage</h1>
                    <p class="text-gray-600">Input data pergerakan kapal</p>
                </div>
            </div>
            <div>
                <a href="{{ route('gerak-voyage.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong>Ada kesalahan input:</strong>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('gerak-voyage.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label for="kapal_voyage" class="block text-sm font-medium text-gray-700 mb-2">Kapal - Voyage <span class="text-red-500">*</span></label>
                    <select id="kapal_voyage" name="kapal_voyage" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">-- Pilih Kapal & Voyage --</option>
                        @foreach($manifests as $manifest)
                            <option value="{{ $manifest->nama_kapal }}|{{ $manifest->no_voyage }}" {{ old('kapal_voyage') == $manifest->nama_kapal.'|'.$manifest->no_voyage ? 'selected' : '' }}>
                                {{ $manifest->nama_kapal }} - Voyage: {{ $manifest->no_voyage }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tanggal_mulai_berlayar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Berlayar</label>
                    <input type="date" id="tanggal_mulai_berlayar" name="tanggal_mulai_berlayar" value="{{ old('tanggal_mulai_berlayar') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="tanggal_berlabuh" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berlabuh</label>
                    <input type="date" id="tanggal_berlabuh" name="tanggal_berlabuh" value="{{ old('tanggal_berlabuh') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="tanggal_sandar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sandar</label>
                    <input type="date" id="tanggal_sandar" name="tanggal_sandar" value="{{ old('tanggal_sandar') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="tanggal_mulai_bongkar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Bongkar</label>
                    <input type="date" id="tanggal_mulai_bongkar" name="tanggal_mulai_bongkar" value="{{ old('tanggal_mulai_bongkar') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="tanggal_selesai_bongkar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai Bongkar</label>
                    <input type="date" id="tanggal_selesai_bongkar" name="tanggal_selesai_bongkar" value="{{ old('tanggal_selesai_bongkar') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-sm">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Select2 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#kapal_voyage').select2({
            placeholder: '-- Pilih Kapal & Voyage --',
            width: '100%'
        });
    });
</script>
@endsection
