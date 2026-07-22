
@extends('layouts.app')

@section('title', 'Payroll Uang Makan')
@section('page_title', 'Payroll Uang Makan Mingguan')

@section('content')
<div class="space-y-6">
    <!-- Filter Card -->
    <div class="bg-white shadow-md rounded-lg p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan & Filter Pencairan</h3>
        
        <form action="{{ route('payroll.uang-makan') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Awal (Start Date)</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Akhir (End Date)</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Penempatan (Opsional)</label>
                    <select name="penempatan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">-- Semua Penempatan --</option>
                        <option value="JAKARTA PELABHUHAN" {{ request('penempatan') == 'JAKARTA PELABHUHAN' ? 'selected' : '' }}>JAKARTA PELABHUHAN</option>
                        <option value="JAKARTA PELABUHAN 1" {{ request('penempatan') == "JAKARTA PELABUHAN 1" ? 'selected' : '' }}>JAKARTA PELABUHAN 1</option>
                        <option value="JAKARTA KRANI" {{ request('penempatan') == 'JAKARTA KRANI' ? 'selected' : '' }}>JAKARTA KRANI</option>
                        <option value="KANTOR JAKARTA" {{ request('penempatan') == 'KANTOR JAKARTA' ? 'selected' : '' }}>KANTOR JAKARTA</option>
                        <option value="GARASAI JAKARTA" {{ request('penempatan') == 'GARASAI JAKARTA' ? 'selected' : '' }}>GARASAI JAKARTA</option>
                        <option value="KANTOR BATAM" {{ request('penempatan') == 'KANTOR BATAM' ? 'selected' : '' }}>KANTOR BATAM</option>
                        <option value="GARASI BATAM" {{ request('penempatan') == 'GARASI BATAM' ? 'selected' : '' }}>GARASI BATAM</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end pt-2 border-t border-gray-100 mt-4">
                <button type="submit" name="generate" value="1" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Kalkulasi Data Absensi
                </button>
            </div>
        </form>
    </div>

    <!-- Results Card -->
    @if($isGenerated)
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Hasil Kalkulasi: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</h3>
                <p class="text-sm text-gray-500 mt-1">Ditemukan {{ count($payrolls) }} karyawan dengan data absensi masuk.</p>
            </div>
            
            @if(count($payrolls) > 0)
            <div class="flex items-center gap-2">
                <form action="{{ route('payroll.uang-makan.store') }}" method="POST" id="form-payout" class="m-0">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    @if(request('penempatan'))
                    <input type="hidden" name="penempatan" value="{{ request('penempatan') }}">
                    @endif
                    
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Data Payout
                    </button>
            </div>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-sm">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penempatan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kehadiran</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Multiplier</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal Uang Makan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payout</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payrolls as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <div class="font-medium text-gray-800">{{ $row['karyawan']->nama_lengkap }}</div>
                            <div class="text-xs text-gray-500">{{ $row['karyawan']->nik }}</div>
                        </td>
                        <td class="p-4 text-sm text-gray-700">
                            {{ $row['karyawan']->penempatan ?? '-' }}
                        </td>
                        <td class="p-4 text-center font-bold text-blue-600">
                            {{ $row['total_kehadiran'] }} Hari
                        </td>
                        <td class="p-4 text-center">
                            @if($row['multiplier'] == 2)
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">2x</span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">1x</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                            <div class="flex items-center justify-end gap-2">
                                <span class="text-gray-500">Rp</span>
                                <input type="number" name="payrolls[{{ $row['karyawan']->id }}][nominal_per_hari]" value="{{ $row['nominal_per_hari'] }}" 
                                       data-kehadiran="{{ $row['total_kehadiran'] }}" 
                                       data-multiplier="{{ $row['multiplier'] }}"
                                       class="nominal-input w-28 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-right text-sm py-1">
                            </div>
                        </td>
                        <td class="p-4 text-right font-bold text-green-600 total-payout-text">
                            Rp {{ number_format($row['total_payout'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            Tidak ada data absensi untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if(count($payrolls) > 0)
            </form>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.nominal-input');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const kehadiran = parseFloat(this.getAttribute('data-kehadiran')) || 0;
                const multiplier = parseFloat(this.getAttribute('data-multiplier')) || 1;
                const nominal = parseFloat(this.value) || 0;
                
                const total = kehadiran * multiplier * nominal;
                
                // Cari td target di row (tr) yang sama
                const targetTd = this.closest('tr').querySelector('.total-payout-text');
                if (targetTd) {
                    // Format ke Rupiah
                    targetTd.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                }
            });
        });
    });
</script>
@endpush
@endsection
