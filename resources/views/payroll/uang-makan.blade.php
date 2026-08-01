
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

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm text-red-800 font-medium">Terdapat kesalahan:</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Pengaturan & Filter Pencairan</h3>
        
        <form action="{{ route('payroll.uang-makan') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
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
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Filter Cabang (Opsional)</label>
                    <select name="cabang" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                        <option value="">Semua Cabang</option>
                        @if(isset($allCabang))
                            @foreach($allCabang as $cb)
                                <option value="{{ $cb }}" {{ request('cabang') == $cb ? 'selected' : '' }}>{{ $cb }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="md:col-span-6 flex items-end gap-2 justify-end mt-2">
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
            <form action="{{ route('payroll.uang-makan.store') }}" method="POST" id="form-payout" class="m-0">
                <div class="flex items-center gap-2">
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
        <form action="{{ route('pranota-uang-makan.store') }}" method="POST" class="inline-flex flex-col align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl xl:max-w-7xl sm:w-full border border-gray-100 max-h-[90vh]">
            @csrf
            <!-- Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-lg">
                        <i class="fas fa-file-invoice text-blue-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800" id="modal-title">Konfirmasi Masuk Pranota Uang Makan</h3>
                </div>
                <button type="button" onclick="closePranotaModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="bg-white px-6 py-5 flex-1 overflow-y-auto">
                <!-- Form Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Pranota <span class="text-red-500">*</span></label>
                        <div class="flex rounded-md shadow-sm">
                            <input type="text" id="nomor_pranota" name="nomor_pranota" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono text-gray-700 bg-gray-50" readonly value="PUM-{{ date('y') }}-{{ date('m') }}-001">
                            <button type="button" onclick="generateNewPranotaNumber()" class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="Generate Ulang">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pranota <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_pranota" name="tanggal_pranota" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Item Terpilih -->
                <div class="mb-2">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Item Terpilih</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden flex flex-col">
                        <div class="custom-scrollbar">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Karyawan</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penempatan</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Kehadiran</th>
                                        <th scope="col" class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Nominal Awal</th>
                                        <th scope="col" class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Adjustment</th>
                                        <th scope="col" class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Akhir</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-item-list" class="bg-white divide-y divide-gray-100 text-sm">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="bg-gray-50 px-6 py-4 sm:flex sm:items-center sm:justify-between rounded-b-lg shrink-0 border-t border-gray-200">
                <!-- Footer Stats -->
                <div class="flex items-center gap-6 mb-4 sm:mb-0">
                    <div class="text-sm text-gray-500">
                        <span id="modal-item-count" class="font-medium text-gray-700">0</span> item
                    </div>
                    <div class="text-right sm:text-left">
                        <div class="text-xs text-gray-500">Total Nominal</div>
                        <div class="text-xl font-bold text-blue-600 leading-tight" id="modal-total-nominal">Rp 0</div>
                    </div>
                </div>
                
                <div class="sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto transition-colors">
                        Simpan Pranota
                    </button>
                    <button type="button" onclick="closePranotaModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </form>
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
        const modalList = document.getElementById('modal-item-list');
        const countSpan = document.getElementById('modal-item-count');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        
        modalList.innerHTML = '';
        
        rowCheckboxes.forEach(cb => {
            const tr = cb.closest('tr');
            const karyawanId = cb.value;
            
            const karyawanName = tr.querySelector('td:nth-child(3) .font-medium').innerText.trim();
            const karyawanNik = tr.querySelector('td:nth-child(3) .text-xs').innerText.trim();
            const penempatan = tr.querySelector('td:nth-child(4)').innerText.trim();
            const kehadiran = tr.querySelector('td:nth-child(5)').innerText.trim();
            const payoutText = tr.querySelector('.total-payout-text').innerText;
            
            // Parse Rp 325.000 to integer 325000
            const basePayoutVal = parseInt(payoutText.replace(/[^\d]/g, '')) || 0;
            
            const trModal = document.createElement('tr');
            trModal.innerHTML = `
                <td class="px-3 py-2 whitespace-nowrap">
                    <div class="font-bold text-gray-900">${karyawanName}</div>
                    <div class="text-[10px] text-gray-500 font-mono">${karyawanNik}</div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-gray-600">${penempatan}</td>
                <td class="px-3 py-2 whitespace-nowrap text-center text-gray-600 font-medium">
                    ${kehadiran}
                    <input type="hidden" name="karyawans[${karyawanId}][kehadiran]" value="${kehadiran}">
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-right font-medium text-gray-700">
                    Rp ${new Intl.NumberFormat('id-ID').format(basePayoutVal)}
                    <input type="hidden" name="karyawans[${karyawanId}][nominal_awal]" value="${basePayoutVal}">
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-right">
                    <input type="number" name="karyawans[${karyawanId}][adjustment]" class="modal-adjustment-input w-24 px-2 py-1 text-sm border border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-right" value="0" data-base-payout="${basePayoutVal}">
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-right font-bold text-blue-700 modal-row-payout" data-current-payout="${basePayoutVal}">Rp ${new Intl.NumberFormat('id-ID').format(basePayoutVal)}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <input type="text" name="karyawans[${karyawanId}][catatan]" class="w-full min-w-[120px] px-2 py-1 text-sm border border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Catatan...">
                </td>
            `;
            modalList.appendChild(trModal);
        });
        
        countSpan.innerText = rowCheckboxes.length;
        updateModalTotal();
        
        // Add event listeners to adjustment inputs
        const adjInputs = document.querySelectorAll('.modal-adjustment-input');
        adjInputs.forEach(input => {
            input.addEventListener('input', function() {
                const base = parseInt(this.getAttribute('data-base-payout')) || 0;
                const adj = parseInt(this.value) || 0;
                const newPayout = base + adj;
                
                const tr = this.closest('tr');
                const payoutTd = tr.querySelector('.modal-row-payout');
                payoutTd.setAttribute('data-current-payout', newPayout);
                payoutTd.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(newPayout);
                
                updateModalTotal();
            });
        });

        document.getElementById('pranota-modal').classList.remove('hidden');
    }

    function updateModalTotal() {
        const payouts = document.querySelectorAll('.modal-row-payout');
        let total = 0;
        payouts.forEach(td => {
            total += parseInt(td.getAttribute('data-current-payout')) || 0;
        });
        document.getElementById('modal-total-nominal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    function closePranotaModal() {
        document.getElementById('pranota-modal').classList.add('hidden');
    }

    function generateNewPranotaNumber() {
        const input = document.getElementById('nomor_pranota');
        const currentVal = input.value;
        const parts = currentVal.split('-');
        if (parts.length === 4) {
            let runningNumber = parseInt(parts[3], 10);
            runningNumber++;
            parts[3] = String(runningNumber).padStart(3, '0');
            input.value = parts.join('-');
        }
    }
</script>
@endpush
@endsection
