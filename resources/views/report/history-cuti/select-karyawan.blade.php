@extends('layouts.app')

@section('title', 'Pilih Karyawan - Report History Cuti')
@section('page_title', 'Report History Cuti')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        height: 42px;
        padding: 5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>
@endpush

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Pilih Karyawan</h3>
        <p class="text-sm text-gray-600">Silakan pilih karyawan untuk melihat history cutinya</p>
    </div>

    <form action="{{ route('report-history-cuti.index') }}" method="GET" class="space-y-6">
        <div>
            <label for="karyawan_id" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Karyawan <span class="text-red-500">*</span>
            </label>
            <select name="karyawan_id" id="karyawan_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach($karyawanList as $karyawan)
                    <option value="{{ $karyawan->id }}">{{ $karyawan->nama }} {{ $karyawan->nik ? '('.$karyawan->nik.')' : '' }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai (Opsional)</label>
                <input type="date" name="start_date" id="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir (Opsional)</label>
                <input type="date" name="end_date" id="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-200">
            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Lihat Report
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#karyawan_id').select2({
            placeholder: "-- Pilih Karyawan --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
@endsection
