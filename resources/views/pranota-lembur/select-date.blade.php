@extends('layouts.app')

@section('title', 'Pilih Periode Pranota Lembur')
@section('page_title', 'Pranota Lembur/Nginap')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center mb-6">
            <i class="fas fa-bed mr-3 text-blue-600 text-2xl"></i>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Pranota Lembur/Nginap</h1>
                <p class="text-gray-600">Pilih periode untuk membuat pranota lembur/nginap</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form method="GET" action="{{ route('pranota-lembur.create') }}" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tanggal Mulai -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ old('start_date', request('start_date')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Akhir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ old('end_date', request('end_date')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <!-- Nomor Cetakan -->
                <div>
                    <label for="nomor_cetakan" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Cetakan
                    </label>
                    <input type="number" 
                           id="nomor_cetakan" 
                           name="nomor_cetakan" 
                           value="1"
                           min="1"
                           max="5"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Default: 1 (untuk cetakan ulang gunakan 2, 3, dst)</p>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('pranota-lembur.list') }}" class="text-gray-600 hover:text-gray-800 inline-flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    Lihat Daftar Pranota
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Cari Data
                </button>
            </div>
        </form>
    </div>

    <!-- Info Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Pilih periode berdasarkan tanggal tanda terima</li>
                        <li>Sistem akan menampilkan surat jalan yang memiliki status lembur/nginap</li>
                        <li>Hanya surat jalan yang belum masuk pranota yang akan ditampilkan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Set max date to today
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').setAttribute('max', today);
        document.getElementById('end_date').setAttribute('max', today);
        
        // Validate end date is after start date
        document.getElementById('end_date').addEventListener('change', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = this.value;
            
            if (startDate && endDate && endDate < startDate) {
                alert('Tanggal akhir harus sama atau setelah tanggal mulai');
                this.value = '';
            }
        });
    });
</script>
@endpush
