
@extends('layouts.app')

@section('title', 'Payroll Uang Makan')
@section('page_title', 'Payroll Uang Makan Mingguan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Payroll Uang Makan Mingguan</h1>
                <p class="mt-1 text-sm text-gray-600">Perhitungan akumulasi kehadiran dan pencairan uang makan per karyawan</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="openRiwayatPranotaModal()" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 hover:text-blue-600 hover:border-blue-300 focus:outline-none transition-all duration-200 shadow-sm cursor-pointer">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Riwayat Pranota Uang Makan
                    @if(isset($riwayatPranota) && $riwayatPranota->count() > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            {{ $riwayatPranota->count() }}
                        </span>
                    @endif
                </button>
                <a href="{{ route('pranota-uang-makan.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm" title="Lihat Halaman Lengkap Riwayat Pranota Uang Makan">
                    <i class="fas fa-list mr-2 text-gray-500"></i>
                    Semua Pranota
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

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
                
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Tipe Karyawan</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipe_karyawan" value="all" {{ request('tipe_karyawan', 'all') == 'all' ? 'checked' : '' }} class="form-radio text-indigo-600 focus:ring-indigo-500 h-4 w-4 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Semua</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipe_karyawan" value="Karyawan" {{ request('tipe_karyawan') == 'Karyawan' ? 'checked' : '' }} class="form-radio text-indigo-600 focus:ring-indigo-500 h-4 w-4 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Karyawan</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipe_karyawan" value="KaryawanTidakTetap" {{ request('tipe_karyawan') == 'KaryawanTidakTetap' ? 'checked' : '' }} class="form-radio text-indigo-600 focus:ring-indigo-500 h-4 w-4 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Non Karyawan</span>
                        </label>
                    </div>
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
    <div id="results-card" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-gray-900">
                    Hasil Kalkulasi: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                </h3>
                <p class="text-xs text-gray-500 mt-1">Ditemukan {{ count($payrolls) }} karyawan dengan data absensi masuk.</p>
            </div>
            
            @if(count($payrolls) > 0)
            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Search Table Input -->
                <div class="relative flex-1 md:w-56">
                    <input type="text" id="table-search-input" class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 w-full" placeholder="Cari Nama atau NIK...">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <form action="{{ route('payroll.uang-makan.store') }}" method="POST" id="form-payout" class="m-0 flex items-center gap-2">
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
                    @if(request('tipe_karyawan'))
                    <input type="hidden" name="tipe_karyawan" value="{{ request('tipe_karyawan') }}">
                    @endif
                    
                    <button type="button" id="btn-refresh-data" class="inline-flex items-center justify-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold rounded-lg focus:outline-none transition-colors duration-200 shadow-sm cursor-pointer border border-indigo-200">
                        <i class="fas fa-sync-alt mr-1.5"></i>
                        Refresh
                    </button>
                    <button type="button" id="btn-masukkan-pranota" class="hidden inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 focus:outline-none transition-colors duration-200 shadow-sm cursor-pointer">
                        <i class="fas fa-file-invoice mr-1.5"></i>
                        Masukkan Pranota
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 focus:outline-none transition-colors duration-200 shadow-sm cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Data
                    </button>
                </form>
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
                            <input type="checkbox" name="selected_pranota[]" value="{{ class_basename($row['karyawan']) . '_' . $row['karyawan']->id }}" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
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
                            @php
                                \Carbon\Carbon::setLocale('id');
                                $datesStr = '';
                                if(isset($row['dates_kehadiran']) && is_array($row['dates_kehadiran'])) {
                                    $datesList = array_map(function($date) {
                                        return \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y');
                                    }, $row['dates_kehadiran']);
                                    $datesStr = implode("\n", $datesList);
                                }
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 cursor-help" title="{{ $datesStr }}">
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
                                <input type="number" name="payrolls[{{ class_basename($row['karyawan']) . '_' . $row['karyawan']->id }}][nominal_per_hari]" value="{{ $row['nominal_per_hari'] }}" 
                                       data-kehadiran="{{ $row['total_kehadiran'] }}" 
                                       data-multiplier="{{ $row['multiplier'] }}"
                                       data-is-satpam-pelabuhan="{{ isset($row['is_satpam_pelabuhan']) && $row['is_satpam_pelabuhan'] ? '1' : '0' }}"
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
                            <input type="text" id="nomor_pranota" name="nomor_pranota" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono text-gray-700 bg-gray-50" readonly value="{{ $suggestedNomorPranota ?? ('PUM-' . date('y') . '-' . date('m') . '-001') }}">
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
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-800">Item Terpilih</h4>
                        <div class="relative">
                            <input type="text" id="modal-search-input" class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 w-64 md:w-80" placeholder="Cari Nama atau NIK...">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg overflow-hidden flex flex-col">
                        <div class="custom-scrollbar">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Karyawan</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penempatan</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Kehadiran</th>
                                        <th scope="col" class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Nominal/Hari</th>
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

{{-- Modal Riwayat Pranota Uang Makan --}}
<div id="riwayat-pranota-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-riwayat-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRiwayatPranotaModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-flex flex-col align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl xl:max-w-7xl sm:w-full border border-gray-100 max-h-[90vh]">
            <!-- Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2.5 rounded-xl text-blue-600">
                        <i class="fas fa-history text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900" id="modal-riwayat-title">Riwayat Pranota Uang Makan</h3>
                        <p class="text-xs text-gray-500">Daftar pranota uang makan yang telah dibuat dari sistem payroll</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('pranota-uang-makan.index') }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                        <i class="fas fa-external-link-alt mr-1.5"></i>
                        Halaman Lengkap
                    </a>
                    <button type="button" onclick="closeRiwayatPranotaModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="bg-white px-6 py-5 flex-1 overflow-y-auto space-y-4">
                <!-- Summary Stats Bar -->
                @php
                    $totalPranotaCount = isset($riwayatPranota) ? $riwayatPranota->count() : 0;
                    $totalNominalAccum = isset($riwayatPranota) ? $riwayatPranota->sum('total_nominal') : 0;
                    $totalKaryawanCount = isset($riwayatPranota) ? $riwayatPranota->sum('details_count') : 0;
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium">Total Pranota Dibuat</div>
                            <div class="text-lg font-bold text-gray-900">{{ $totalPranotaCount }} Pranota</div>
                        </div>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium">Total Akumulasi Nominal</div>
                            <div class="text-lg font-bold text-emerald-600">Rp {{ number_format($totalNominalAccum, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium">Total Karyawan Terekam</div>
                            <div class="text-lg font-bold text-gray-900">{{ $totalKaryawanCount }} Data Karyawan</div>
                        </div>
                    </div>
                </div>

                <!-- Search Filter & Actions inside modal -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                    <div class="relative flex-1 max-w-sm">
                        <input type="text" id="riwayat-search-input" onkeyup="filterRiwayatPranota()" placeholder="Cari nomor pranota / tanggal..." class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500" id="riwayat-count-info">
                        Menampilkan {{ $totalPranotaCount }} data
                    </span>
                </div>

                <!-- Table -->
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto max-h-[50vh]">
                        <table class="min-w-full divide-y divide-gray-200" id="table-riwayat-pranota">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor Pranota</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Karyawan</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Nominal</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-xs">
                                @forelse($riwayatPranota ?? [] as $index => $item)
                                    <tr class="hover:bg-blue-50/50 transition-colors riwayat-row" data-nomor="{{ strtolower($item->nomor_pranota) }}" data-tanggal="{{ $item->tanggal_pranota ? $item->tanggal_pranota->format('d/m/Y') : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="{{ route('pranota-uang-makan.show', $item->id) }}" target="_blank" class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1.5" title="Buka Detail">
                                                <span>{{ $item->nomor_pranota }}</span>
                                                <i class="fas fa-external-link-alt text-[10px] text-gray-400"></i>
                                            </a>
                                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $item->tanggal_pranota ? $item->tanggal_pranota->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <button type="button" onclick="toggleKaryawanRow({{ $item->id }})" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors cursor-pointer" title="Klik untuk lihat daftar karyawan">
                                                <i class="fas fa-users text-[10px]"></i>
                                                <span>{{ $item->details_count ?? $item->details->count() }} Orang</span>
                                                <i class="fas fa-chevron-down text-[9px] transition-transform duration-200" id="icon-chevron-{{ $item->id }}"></i>
                                            </button>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-emerald-600">
                                            Rp {{ number_format($item->total_nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            @if($item->pranota_puml_id)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 text-purple-800">
                                                    Masuk PUML
                                                </span>
                                            @elseif($item->status === 'draft')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-800">
                                                    Draft
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <a href="{{ route('pranota-uang-makan.show', $item->id) }}" target="_blank" class="p-1.5 rounded-md text-blue-600 hover:bg-blue-100 transition-colors" title="Lihat Rincian">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('pranota-uang-makan.edit', $item->id) }}" class="p-1.5 rounded-md text-amber-600 hover:bg-amber-100 transition-colors" title="Edit Pranota">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="{{ route('pranota-uang-makan.show', $item->id) }}?print=true" target="_blank" class="p-1.5 rounded-md text-indigo-600 hover:bg-indigo-100 transition-colors" title="Cetak Pranota">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="{{ route('pranota-uang-makan.export-auto-transfer', $item->id) }}" class="p-1.5 rounded-md text-emerald-600 hover:bg-emerald-100 transition-colors" title="Export Excel Auto Transfer">
                                                    <i class="fas fa-file-excel"></i>
                                                </a>
                                                @if(!$item->pranota_puml_id)
                                                    <form action="{{ route('pranota-uang-makan.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota uang makan {{ $item->nomor_pranota }}? Data detail di dalamnya akan ikut terhapus.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 rounded-md text-red-600 hover:bg-red-100 transition-colors cursor-pointer" title="Hapus Pranota">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Collapsible Sub-Row: Employee List -->
                                    <tr id="karyawan-row-{{ $item->id }}" class="hidden bg-slate-50/90 border-b border-gray-200">
                                        <td colspan="7" class="px-6 py-3">
                                            <div class="text-xs">
                                                <div class="font-bold text-gray-700 mb-2 flex items-center justify-between">
                                                    <div class="flex items-center gap-1.5">
                                                        <i class="fas fa-id-card text-blue-500"></i>
                                                        <span>Daftar Karyawan di Pranota {{ $item->nomor_pranota }}:</span>
                                                    </div>
                                                    <a href="{{ route('pranota-uang-makan.export-auto-transfer', $item->id) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded transition-colors">
                                                        <i class="fas fa-file-excel text-emerald-600"></i>
                                                        <span>Export Excel Auto Transfer</span>
                                                    </a>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                                    @foreach($item->details as $d)
                                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200 flex items-center justify-between gap-2 shadow-xs">
                                                            <div class="min-w-0">
                                                                <p class="font-semibold text-gray-900 truncate">{{ $d->karyawan->nama_lengkap ?? 'Karyawan' }}</p>
                                                                <p class="text-[10px] text-gray-500 font-mono">{{ $d->karyawan->nik ?? '-' }} &bull; {{ $d->kehadiran ?? '-' }}</p>
                                                                @if($d->catatan)
                                                                    <p class="text-[10px] text-gray-400 italic truncate" title="{{ $d->catatan }}">{{ $d->catatan }}</p>
                                                                @endif
                                                            </div>
                                                            <div class="text-right whitespace-nowrap">
                                                                <span class="text-xs font-bold text-emerald-600 block">
                                                                    Rp {{ number_format($d->total_akhir, 0, ',', '.') }}
                                                                </span>
                                                                @if($d->adjustment != 0)
                                                                    <span class="text-[10px] {{ $d->adjustment > 0 ? 'text-blue-500' : 'text-red-500' }}">
                                                                        {{ $d->adjustment > 0 ? '+' : '' }}{{ number_format($d->adjustment, 0, ',', '.') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-riwayat-row">
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-400">
                                                    <i class="fas fa-file-invoice text-xl"></i>
                                                </div>
                                                <p class="font-semibold text-gray-700 text-sm">Belum Ada Riwayat Pranota Uang Makan</p>
                                                <p class="text-xs text-gray-500 mt-1 max-w-sm">Pilih karyawan dari hasil kalkulasi absensi di bawah, lalu klik tombol "Masukkan Pranota" untuk membuat pranota uang makan pertama.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 shrink-0">
                <a href="{{ route('pranota-uang-makan.index') }}" class="text-xs text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1.5 font-medium">
                    <i class="fas fa-list-ul"></i>
                    <span>Buka Semua Data Pranota Uang Makan</span>
                </a>
                <button type="button" onclick="closeRiwayatPranotaModal()" class="w-full sm:w-auto px-5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initTableEvents();
        
        // Modal Search Logic
        const modalSearchInput = document.getElementById('modal-search-input');
        if (modalSearchInput) {
            modalSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#modal-item-list tr');
                
                rows.forEach(row => {
                    const nameNode = row.querySelector('td:nth-child(1) .font-bold');
                    const nikNode = row.querySelector('td:nth-child(1) .text-\\[10px\\]');
                    
                    if (nameNode && nikNode) {
                        const name = nameNode.innerText.toLowerCase();
                        const nik = nikNode.innerText.toLowerCase();
                        
                        if (name.includes(searchTerm) || nik.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        }
    });

    async function refreshTableData() {
        const btn = document.getElementById('btn-refresh-data');
        if (!btn) return;
        
        const icon = btn.querySelector('i');
        if (icon) icon.classList.add('fa-spin');
        
        try {
            // Re-fetch current URL
            const url = window.location.href;
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const htmlText = await response.text();
            
            // Parse and extract the new results-card
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');
            
            const newResultsCard = doc.getElementById('results-card');
            const currentResultsCard = document.getElementById('results-card');
            
            if (newResultsCard && currentResultsCard) {
                // Simpan state checkbox yang sudah dicentang
                const checkedCheckboxes = Array.from(currentResultsCard.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
                
                // Simpan text pencarian jika ada
                const searchInput = currentResultsCard.querySelector('#table-search-input');
                const searchValue = searchInput ? searchInput.value : '';

                currentResultsCard.innerHTML = newResultsCard.innerHTML;
                
                // Re-initialize all table events on the new HTML
                initTableEvents();
                
                // Kembalikan state checkbox yang sudah dicentang
                if (checkedCheckboxes.length > 0) {
                    const newCheckboxes = currentResultsCard.querySelectorAll('.row-checkbox');
                    newCheckboxes.forEach(cb => {
                        if (checkedCheckboxes.includes(cb.value)) {
                            cb.checked = true;
                            // Trigger change event to update button state & select all
                            cb.dispatchEvent(new Event('change'));
                        }
                    });
                }

                // Kembalikan text pencarian jika ada
                if (searchValue) {
                    const newSearchInput = currentResultsCard.querySelector('#table-search-input');
                    if (newSearchInput) {
                        newSearchInput.value = searchValue;
                        newSearchInput.dispatchEvent(new Event('input'));
                    }
                }
            }
        } catch (error) {
            console.error('Error refreshing data:', error);
            alert('Gagal merefresh data. Silakan coba muat ulang halaman.');
        } finally {
            if (icon) icon.classList.remove('fa-spin');
        }
    }

    function initTableEvents() {
        const resultsCard = document.getElementById('results-card');
        if (!resultsCard) return;

        const inputs = resultsCard.querySelectorAll('.nominal-input');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const kehadiran = parseFloat(this.getAttribute('data-kehadiran')) || 0;
                const multiplier = parseFloat(this.getAttribute('data-multiplier')) || 1;
                const isSatpamPelabuhan = this.getAttribute('data-is-satpam-pelabuhan') === '1';
                const nominal = parseFloat(this.value) || 0;
                
                const total = isSatpamPelabuhan ? (multiplier * nominal) : (kehadiran * multiplier * nominal);
                
                // Cari td target di row (tr) yang sama
                const targetTd = this.closest('tr').querySelector('.total-payout-text');
                if (targetTd) {
                    // Format ke Rupiah
                    targetTd.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                }
            });
        });

        // Checkbox logic
        const checkAll = resultsCard.querySelector('#check-all');
        const rowCheckboxes = resultsCard.querySelectorAll('.row-checkbox');
        const btnPranota = resultsCard.querySelector('#btn-masukkan-pranota');

        function togglePranotaButton() {
            if (!btnPranota) return;
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
                if (!this.checked && checkAll) checkAll.checked = false;
                
                // If all are checked, check the check-all box
                if (Array.from(rowCheckboxes).every(c => c.checked) && checkAll) {
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

        // Table Search Logic
        const tableSearchInput = resultsCard.querySelector('#table-search-input');
        if (tableSearchInput) {
            tableSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = resultsCard.querySelectorAll('table.min-w-full > tbody > tr');
                
                rows.forEach(row => {
                    // Cek jika ini baris kosong
                    if (row.querySelector('td[colspan]')) return;
                    
                    const nameNode = row.querySelector('td:nth-child(3) .font-medium');
                    const nikNode = row.querySelector('td:nth-child(3) .text-xs');
                    
                    if (nameNode && nikNode) {
                        const name = nameNode.innerText.toLowerCase();
                        const nik = nikNode.innerText.toLowerCase();
                        
                        if (name.includes(searchTerm) || nik.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        }

        // Refresh Button Binding
        const btnRefresh = resultsCard.querySelector('#btn-refresh-data');
        if (btnRefresh) {
            btnRefresh.addEventListener('click', refreshTableData);
        }
    }

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
            
            const nominalInput = tr.querySelector('.nominal-input');
            const nominalPerHari = nominalInput ? parseInt(nominalInput.value) || 0 : 0;
            
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
                <td class="px-3 py-2 whitespace-nowrap text-right">
                    <input type="text" value="Rp ${new Intl.NumberFormat('id-ID').format(nominalPerHari)}" class="w-24 px-2 py-1 text-xs border border-gray-200 rounded bg-gray-50 text-gray-500 text-right font-medium" readonly title="Nominal Per Hari">
                    <input type="hidden" name="karyawans[${karyawanId}][nominal_per_hari]" value="${nominalPerHari}">
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

        // Reset search input
        const searchInput = document.getElementById('modal-search-input');
        if (searchInput) searchInput.value = '';

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

    // --- LOGIKA RIWAYAT PRANOTA MODAL ---
    function openRiwayatPranotaModal() {
        document.getElementById('riwayat-pranota-modal').classList.remove('hidden');
    }

    function closeRiwayatPranotaModal() {
        document.getElementById('riwayat-pranota-modal').classList.add('hidden');
    }

    function filterRiwayatPranota() {
        const input = (document.getElementById('riwayat-search-input').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.riwayat-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const nomor = (row.getAttribute('data-nomor') || '').toLowerCase();
            const tanggal = (row.getAttribute('data-tanggal') || '').toLowerCase();
            const isMatch = nomor.includes(input) || tanggal.includes(input);
            
            row.style.display = isMatch ? '' : 'none';
            if (isMatch) visibleCount++;

            // Jika row disembunyikan, sembunyikan juga sub-row karyawannya jika sedang terbuka
            if (!isMatch) {
                const subRow = row.nextElementSibling;
                if (subRow && subRow.id && subRow.id.startsWith('karyawan-row-')) {
                    subRow.classList.add('hidden');
                }
            }
        });

        const countInfo = document.getElementById('riwayat-count-info');
        if (countInfo) {
            countInfo.innerText = `Menampilkan ${visibleCount} data`;
        }
    }

    function toggleKaryawanRow(id) {
        const row = document.getElementById('karyawan-row-' + id);
        const icon = document.getElementById('icon-chevron-' + id);
        if (row) {
            row.classList.toggle('hidden');
            if (icon) {
                if (row.classList.contains('hidden')) {
                    icon.classList.remove('rotate-180');
                } else {
                    icon.classList.add('rotate-180');
                }
            }
        }
    }

    // Escape key listener to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const riwayatModal = document.getElementById('riwayat-pranota-modal');
            if (riwayatModal && !riwayatModal.classList.contains('hidden')) {
                closeRiwayatPranotaModal();
            }
            const pranotaModal = document.getElementById('pranota-modal');
            if (pranotaModal && !pranotaModal.classList.contains('hidden')) {
                closePranotaModal();
            }
        }
    });
</script>
@endpush
@endsection

