@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Gabungkan Pranota Uang Makan & Lembur</h1>
        <p class="text-gray-600 mt-2">Pilih data draf dari kalkulasi Uang Makan dan Lembur untuk digabungkan menjadi 1 PUML.</p>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form action="{{ route('pranota-puml.store') }}" method="POST">
        @csrf
        
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal PUML <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pranota" required value="{{ date('Y-m-d') }}" class="w-full form-input rounded-md border border-gray-300">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Pilihan Uang Makan -->
            <div class="bg-white shadow-lg rounded-sm border border-gray-200">
                <header class="px-5 py-4 border-b border-gray-100 bg-gray-50 text-indigo-700 font-semibold">
                    <i class="fas fa-utensils mr-2"></i> Draft Uang Makan
                </header>
                <div class="p-3">
                    @forelse($draftUangMakan as $um)
                        <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-md mb-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="uang_makan_ids[]" value="{{ $um->id }}" class="form-checkbox text-indigo-500 h-5 w-5 rounded">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $um->nomor_pranota }}</p>
                                <p class="text-xs text-gray-500">Tgl: {{ $um->tanggal_pranota->format('d/m/Y') }} | Total: Rp {{ number_format($um->total_nominal, 0, ',', '.') }}</p>
                            </div>
                        </label>
                    @empty
                        <div class="text-center p-4 text-sm text-gray-500">
                            Tidak ada draf Uang Makan tersedia. <br>
                            <a href="{{ route('payroll.uang-makan') }}" class="text-indigo-500 underline">Kalkulasi Uang Makan</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pilihan Lembur -->
            <div class="bg-white shadow-lg rounded-sm border border-gray-200">
                <header class="px-5 py-4 border-b border-gray-100 bg-gray-50 text-orange-700 font-semibold">
                    <i class="fas fa-clock mr-2"></i> Draft Lembur
                </header>
                <div class="p-3">
                    @forelse($draftLembur as $lm)
                        <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-md mb-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="lembur_ids[]" value="{{ $lm->id }}" class="form-checkbox text-orange-500 h-5 w-5 rounded">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $lm->nomor_pranota }}</p>
                                <p class="text-xs text-gray-500">Tgl: {{ $lm->tanggal_pranota->format('d/m/Y') }} | Total: Rp {{ number_format($lm->total_setelah_adjustment, 0, ',', '.') }}</p>
                            </div>
                        </label>
                    @empty
                        <div class="text-center p-4 text-sm text-gray-500">
                            Tidak ada draf Lembur tersedia. <br>
                            <a href="{{ route('payroll.perhitungan-lembur') }}" class="text-indigo-500 underline">Kalkulasi Lembur</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="flex justify-end space-x-2">
            <a href="{{ route('pranota-puml.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Batal</a>
            <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                <i class="fas fa-check-circle mr-2"></i> Generate PUML
            </button>
        </div>
    </form>
</div>
@endsection
