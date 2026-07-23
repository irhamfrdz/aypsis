@extends('layouts.app')

@section('title', 'Rekap Pemakaian Barang')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-boxes mr-2 text-indigo-600"></i>
            Rekap Pemakaian Barang
        </h2>
        <p class="text-gray-500 mt-1 text-sm">Laporan riwayat pemakaian barang dari Stock Amprahan maupun Stock Ban.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-8">
        <form method="GET" action="{{ route('rekap-pemakaian-barang.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Select Barang -->
            <div class="col-span-1 md:col-span-2">
                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Pilih Barang <span class="text-red-500">*</span></label>
                <select name="nama_barang" id="nama_barang" class="select2 form-input-premium w-full" required>
                    <option value="" disabled {{ empty($namaBarang) ? 'selected' : '' }}>-- Ketik untuk mencari barang --</option>
                    @foreach($allBarang as $barang)
                        <option value="{{ $barang }}" {{ $namaBarang === $barang ? 'selected' : '' }}>{{ $barang }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-input-premium w-full" required>
            </div>

            <!-- End Date -->
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-input-premium w-full" required>
            </div>

            <div class="col-span-1 md:col-span-4 flex justify-end mt-2 gap-2">
                <a href="{{ route('rekap-pemakaian-barang.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-search mr-1"></i> Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    @if(isset($namaBarang))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Hasil Pencarian: <span class="text-indigo-600">{{ $namaBarang }}</span></h3>
                    <p class="text-xs text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                </div>
                <div class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-200">
                    Total: {{ $results->count() }} Data
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Detail Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penerima</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit / Tujuan</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($results as $idx => $row)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <div class="font-medium text-gray-900">{{ $row->nama_barang }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">Sumber: {{ $row->sumber }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $row->penerima }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded">
                                        {{ $row->unit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                    {{ $row->qty }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 italic max-w-xs truncate" title="{{ $row->keterangan }}">
                                    {{ $row->keterangan ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 font-medium">Tidak ada data pemakaian untuk barang dan periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Ketik untuk mencari barang...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        background-color: #f9fafb;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #a5b4fc;
        outline: 0;
        box-shadow: 0 0 0 0.1rem rgba(99, 102, 241, 0.25);
        background-color: #fff;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
        color: #374151;
        padding-left: 0;
    }
</style>
@endpush
