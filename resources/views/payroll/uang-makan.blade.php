
@extends('layouts.app')

@section('title', 'Payroll Uang Makan')
@section('page_title', 'Payroll Uang Makan Mingguan')

@section('content')
<div class="space-y-6">
    <!-- Filter Card -->
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Pengaturan & Filter Pencairan</h3>
        
        <form action="{{ route('payroll.uang-makan') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Periode Awal (Start Date)</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Periode Akhir (End Date)</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Filter Penempatan (Opsional)</label>
                    <select name="penempatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                        <option value="">Semua Penempatan</option>
                        <option value="JAKARTA PELABHUHAN" {{ request('penempatan') == 'JAKARTA PELABHUHAN' ? 'selected' : '' }}>JAKARTA PELABHUHAN</option>
                        <option value="JAKARTA PELABUHAN 1" {{ request('penempatan') == "JAKARTA PELABUHAN 1" ? 'selected' : '' }}>JAKARTA PELABUHAN 1</option>
                        <option value="JAKARTA KRANI" {{ request('penempatan') == 'JAKARTA KRANI' ? 'selected' : '' }}>JAKARTA KRANI</option>
                        <option value="KANTOR JAKARTA" {{ request('penempatan') == 'KANTOR JAKARTA' ? 'selected' : '' }}>KANTOR JAKARTA</option>
                        <option value="GARASAI JAKARTA" {{ request('penempatan') == 'GARASAI JAKARTA' ? 'selected' : '' }}>GARASAI JAKARTA</option>
                        <option value="KANTOR BATAM" {{ request('penempatan') == 'KANTOR BATAM' ? 'selected' : '' }}>KANTOR BATAM</option>
                        <option value="GARASI BATAM" {{ request('penempatan') == 'GARASI BATAM' ? 'selected' : '' }}>GARASI BATAM</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Filter Group (Opsional)</label>
                    <select name="group" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                        <option value="">Semua Group</option>
                        @foreach($allGroups as $g)
                            <option value="{{ $g }}" {{ request('group') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Filter Sub Group (Opsional)</label>
                    <select name="sub_group" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                        <option value="">Semua Sub Group</option>
                        @foreach($allSubGroups as $sg)
                            <option value="{{ $sg }}" {{ request('sub_group') == $sg ? 'selected' : '' }}>{{ $sg }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="md:col-span-5 flex items-end gap-2 justify-end mt-2">
                    @if(request()->has('generate'))
                        <a href="{{ route('payroll.uang-makan') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" name="generate" value="1" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                        Kalkulasi Data Absensi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Card -->
    @if($isGenerated)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">
                    Hasil Kalkulasi: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                </h3>
                <p class="text-xs text-gray-500 mt-1">Ditemukan {{ count($payrolls) }} karyawan dengan data absensi masuk.</p>
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
                    @if(request('group'))
                    <input type="hidden" name="group" value="{{ request('group') }}">
                    @endif
                    @if(request('sub_group'))
                    <input type="hidden" name="sub_group" value="{{ request('sub_group') }}">
                    @endif
                    
                    <button type="button" id="btn-masukkan-pranota" class="hidden inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 focus:outline-none transition-colors duration-200 shadow-sm cursor-pointer mr-2">
                        <i class="fas fa-file-invoice mr-1.5"></i>
                        Masukkan Pranota
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 focus:outline-none transition-colors duration-200 shadow-sm cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Data Payout
                    </button>
            </div>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 font-semibold text-gray-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">
                            <input type="checkbox" id="check-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </th>
                        <th class="px-6 py-3 text-center w-12">No.</th>
                        <th class="px-6 py-3 text-left">Karyawan</th>
                        <th class="px-6 py-3 text-left">Penempatan</th>
                        <th class="px-6 py-3 text-center">Total Kehadiran</th>
                        <th class="px-6 py-3 text-center">Multiplier</th>
                        <th class="px-6 py-3 text-right">Nominal Uang Makan</th>
                        <th class="px-6 py-3 text-right">Total Payout</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-xs text-gray-900">
                    @forelse($payrolls as $row)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <input type="checkbox" name="selected_pranota[]" value="{{ $row['karyawan']->id }}" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500 font-medium">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium">{{ $row['karyawan']->nama_lengkap }}</div>
                            <div class="text-xs text-indigo-600 font-mono font-semibold">{{ $row['karyawan']->nik }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $row['karyawan']->penempatan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                {{ $row['total_kehadiran'] }} Hari
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($row['multiplier'] == 2)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">2x</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">1x</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <span class="text-gray-500">Rp</span>
                                <input type="number" name="payrolls[{{ $row['karyawan']->id }}][nominal_per_hari]" value="{{ $row['nominal_per_hari'] }}" 
                                       data-kehadiran="{{ $row['total_kehadiran'] }}" 
                                       data-multiplier="{{ $row['multiplier'] }}"
                                       class="nominal-input w-28 px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-right text-xs transition-colors duration-200" readonly>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-green-600 total-payout-text">
                            Rp {{ number_format($row['total_payout'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data absensi</h3>
                                <p class="text-xs text-gray-500">Tidak ada data absensi untuk periode ini.</p>
                            </div>
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

{{-- Modal Masukkan Pranota --}}
<div id="pranota-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closePranotaModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Masukkan Pranota</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">Silakan masukkan detail pranota untuk karyawan yang dipilih.</p>
                            
                            <!-- Contoh Form Input -->
                            <div class="space-y-4">
                                <div>
                                    <label for="no_pranota" class="block text-sm font-medium text-gray-700">No Pranota</label>
                                    <input type="text" name="no_pranota" id="no_pranota" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: PRN-2026-07-001">
                                </div>
                                <div>
                                    <label for="keterangan_pranota" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea name="keterangan_pranota" id="keterangan_pranota" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Opsional..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Simpan Pranota
                </button>
                <button type="button" onclick="closePranotaModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
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

        // Checkbox logic
        const checkAll = document.getElementById('check-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const btnPranota = document.getElementById('btn-masukkan-pranota');

        function togglePranotaButton() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            if (anyChecked) {
                btnPranota.classList.remove('hidden');
            } else {
                btnPranota.classList.add('hidden');
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => cb.checked = this.checked);
                togglePranotaButton();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked) checkAll.checked = false;
                
                // If all are checked, check the check-all box
                if (Array.from(rowCheckboxes).every(c => c.checked)) {
                    checkAll.checked = true;
                }
                
                togglePranotaButton();
            });
        });

        // Modal Logic
        if (btnPranota) {
            btnPranota.addEventListener('click', function() {
                openPranotaModal();
            });
        }
    });

    function openPranotaModal() {
        document.getElementById('pranota-modal').classList.remove('hidden');
    }

    function closePranotaModal() {
        document.getElementById('pranota-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection
