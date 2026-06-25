@extends('layouts.app')

@section('title', 'Tambah Gaji Supir Batam')
@section('page_title', 'Tambah Gaji Supir Batam')

@section('content')
<div class="mb-6">
    <a href="{{ route('gaji-supir-batam.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 flex items-center transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden max-w-4xl">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900">Form Input Gaji Supir Batam</h3>
        <p class="text-xs text-gray-500 mt-1">Masukkan data gaji pokok, tunjangan, dan potongan untuk periode tertentu</p>
    </div>

    @if (session('error'))
        <div class="m-6 rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('gaji-supir-batam.store') }}" class="p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Supir -->
            <div>
                <label for="karyawan_id" class="block text-sm font-semibold text-gray-700 mb-2">Nama Supir <span class="text-red-500">*</span></label>
                <select name="karyawan_id" id="karyawan_id" class="block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('karyawan_id') border-red-500 @enderror" required>
                    <option value="">-- Pilih Supir --</option>
                    @foreach($supirList as $s)
                        <option value="{{ $s->id }}" {{ old('karyawan_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_lengkap }} (NIK: {{ $s->nik ?? '-' }} | {{ $s->plat ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('karyawan_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bulan -->
            <div>
                <label for="periode_bulan" class="block text-sm font-semibold text-gray-700 mb-2">Bulan <span class="text-red-500">*</span></label>
                <select name="periode_bulan" id="periode_bulan" class="block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('periode_bulan') border-red-500 @enderror" required>
                    <option value="">-- Pilih Bulan --</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('periode_bulan', date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                @error('periode_bulan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tahun -->
            <div>
                <label for="periode_tahun" class="block text-sm font-semibold text-gray-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                <select name="periode_tahun" id="periode_tahun" class="block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('periode_tahun') border-red-500 @enderror" required>
                    <option value="">-- Pilih Tahun --</option>
                    @for($y = date('Y') + 1; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ old('periode_tahun', date('Y')) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
                @error('periode_tahun')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="border-t border-gray-200 my-6"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
            <!-- Pendapatan / Allowances -->
            <div class="bg-green-50/50 rounded-lg p-5 border border-green-100">
                <h4 class="text-sm font-bold text-green-800 uppercase tracking-wider mb-4 flex items-center">
                    <i class="fas fa-wallet mr-2"></i> Pendapatan & Tunjangan
                </h4>

                <div class="space-y-4">
                    <div>
                        <label for="gaji_pokok" class="block text-xs font-semibold text-gray-700 mb-1">Gaji Pokok <span class="text-red-500">*</span></label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="gaji_pokok" id="gaji_pokok" value="{{ old('gaji_pokok', 0) }}" min="0" class="calc-input block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 text-sm" required>
                        </div>
                    </div>

                    <div>
                        <label for="tunjangan_kehadiran" class="block text-xs font-semibold text-gray-700 mb-1">Tunjangan Kehadiran</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="tunjangan_kehadiran" id="tunjangan_kehadiran" value="{{ old('tunjangan_kehadiran', 0) }}" min="0" class="calc-input block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="tunjangan_makan" class="block text-xs font-semibold text-gray-700 mb-1">Tunjangan Makan</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="tunjangan_makan" id="tunjangan_makan" value="{{ old('tunjangan_makan', 0) }}" min="0" class="calc-input block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="tunjangan_lainnya" class="block text-xs font-semibold text-gray-700 mb-1">Tunjangan Lainnya</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="tunjangan_lainnya" id="tunjangan_lainnya" value="{{ old('tunjangan_lainnya', 0) }}" min="0" class="calc-input block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Potongan / Deductions -->
            <div class="bg-red-50/50 rounded-lg p-5 border border-red-100">
                <h4 class="text-sm font-bold text-red-800 uppercase tracking-wider mb-4 flex items-center">
                    <i class="fas fa-minus-circle mr-2"></i> Potongan & Pinjaman
                </h4>

                <div class="space-y-4">
                    <div>
                        <label for="potongan_bpjs" class="block text-xs font-semibold text-gray-700 mb-1">Potongan BPJS</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="potongan_bpjs" id="potongan_bpjs" value="{{ old('potongan_bpjs', 0) }}" min="0" class="calc-input block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="potongan_pinjaman" class="block text-xs font-semibold text-gray-700 mb-1">Potongan Pinjaman</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="potongan_pinjaman" id="potongan_pinjaman" value="{{ old('potongan_pinjaman', 0) }}" min="0" class="calc-input block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="potongan_lainnya" class="block text-xs font-semibold text-gray-700 mb-1">Potongan Lainnya</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="potongan_lainnya" id="potongan_lainnya" value="{{ old('potongan_lainnya', 0) }}" min="0" class="calc-input block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Salary Calculation Box -->
        <div class="bg-gray-800 text-white rounded-lg p-6 mb-6 flex flex-col md:flex-row justify-between items-center border border-gray-700 shadow-inner">
            <div class="mb-4 md:mb-0">
                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Estimasi Gaji Bersih</h4>
                <p class="text-xs text-gray-500">Hasil: (Total Pendapatan) - (Total Potongan)</p>
            </div>
            <div class="text-right">
                <span class="text-2xl md:text-3xl font-extrabold text-green-400" id="total_salary_display">Rp 0</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Status Pembayaran -->
            <div>
                <label for="status_pembayaran" class="block text-sm font-semibold text-gray-700 mb-2">Status Pembayaran <span class="text-red-500">*</span></label>
                <select name="status_pembayaran" id="status_pembayaran" class="block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                    <option value="PENDING" {{ old('status_pembayaran') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                    <option value="PAID" {{ old('status_pembayaran') == 'PAID' ? 'selected' : '' }}>PAID (SUDAH DIBAYAR)</option>
                    <option value="CANCELLED" {{ old('status_pembayaran') == 'CANCELLED' ? 'selected' : '' }}>CANCELLED (BATAL)</option>
                </select>
            </div>

            <!-- Tanggal Dibayar -->
            <div id="payment_date_wrapper" class="hidden">
                <label for="tanggal_dibayar" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Dibayar</label>
                <input type="date" name="tanggal_dibayar" id="tanggal_dibayar" value="{{ old('tanggal_dibayar', date('Y-m-d')) }}" class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>

        <!-- Keterangan -->
        <div class="mb-6">
            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / Catatan</label>
            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Tambahkan catatan jika ada..." class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('keterangan') }}</textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('gaji-supir-batam.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                Simpan Gaji
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputFields = document.querySelectorAll('.calc-input');
        const totalDisplay = document.getElementById('total_salary_display');
        const statusSelect = document.getElementById('status_pembayaran');
        const dateWrapper = document.getElementById('payment_date_wrapper');

        // Salary calculator function
        function calculateSalary() {
            const gajiPokok = parseFloat(document.getElementById('gaji_pokok').value) || 0;
            const tunjKehadiran = parseFloat(document.getElementById('tunjangan_kehadiran').value) || 0;
            const tunjMakan = parseFloat(document.getElementById('tunjangan_makan').value) || 0;
            const tunjLainnya = parseFloat(document.getElementById('tunjangan_lainnya').value) || 0;

            const potBpjs = parseFloat(document.getElementById('potongan_bpjs').value) || 0;
            const potPinjaman = parseFloat(document.getElementById('potongan_pinjaman').value) || 0;
            const potLainnya = parseFloat(document.getElementById('potongan_lainnya').value) || 0;

            const totalAllowances = gajiPokok + tunjKehadiran + tunjMakan + tunjLainnya;
            const totalDeductions = potBpjs + potPinjaman + potLainnya;
            const totalSalary = totalAllowances - totalDeductions;

            totalDisplay.textContent = 'Rp ' + totalSalary.toLocaleString('id-ID');
        }

        // Add event listeners to input fields
        inputFields.forEach(field => {
            field.addEventListener('input', calculateSalary);
        });

        // Toggle payment date field wrapper based on status
        function togglePaymentDate() {
            if (statusSelect.value === 'PAID') {
                dateWrapper.classList.remove('hidden');
            } else {
                dateWrapper.classList.add('hidden');
            }
        }

        statusSelect.addEventListener('change', togglePaymentDate);

        // Initial calculations
        calculateSalary();
        togglePaymentDate();
    });
</script>
@endpush
@endsection
