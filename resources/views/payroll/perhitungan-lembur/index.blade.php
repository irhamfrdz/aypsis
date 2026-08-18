@extends('layouts.app')

@section('title', 'Perhitungan Lembur Karyawan')
@section('page_title', 'Perhitungan Lembur Karyawan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Perhitungan Lembur Karyawan</h1>
                    <p class="mt-1 text-sm text-gray-600">Perhitungan akumulasi jam dan uang lembur per karyawan</p>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle mt-0.5 mr-2"></i>
                    <div>
                        <p class="font-bold mb-1">Terjadi Kesalahan:</p>
                        <ul class="list-disc ml-5 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('payroll.perhitungan-lembur') }}" method="GET" class="space-y-4" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search Karyawan -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">Cari Karyawan / NIK</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Nama atau NIK..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Group -->
                    <div class="md:col-span-1">
                        <label for="grup" class="block text-xs font-semibold text-gray-700 mb-1">Group</label>
                        <select name="grup" id="grup"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs"
                                onchange="handleGrupFilterChange()">
                            <option value="">Semua Group</option>
                            @foreach($grupsList as $g)
                                <option value="{{ $g }}" {{ request('grup') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Group -->
                    <div class="md:col-span-1">
                        <label for="sub_grup" class="block text-xs font-semibold text-gray-700 mb-1">Sub Group</label>
                        <select name="sub_grup" id="sub_grup"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Sub Group</option>
                        </select>
                    </div>

                    <!-- Group BPJS -->
                    <div class="md:col-span-1">
                        <label for="grup_bpjs" class="block text-xs font-semibold text-gray-700 mb-1">Group BPJS</label>
                        <select name="grup_bpjs" id="grup_bpjs"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs"
                                onchange="handleGrupBpjsFilterChange()">
                            <option value="">Semua Group BPJS</option>
                            @foreach($grupsBpjsList as $g)
                                <option value="{{ $g }}" {{ request('grup_bpjs') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Group BPJS -->
                    <div class="md:col-span-1">
                        <label for="sub_grup_bpjs" class="block text-xs font-semibold text-gray-700 mb-1">Sub Group BPJS</label>
                        <select name="sub_grup_bpjs" id="sub_grup_bpjs"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Sub Group BPJS</option>
                        </select>
                    </div>

                    <!-- Tingkat Kehadiran -->
                    <div class="md:col-span-1">
                        <label for="kehadiran" class="block text-xs font-semibold text-gray-700 mb-1">Tingkat Kehadiran</label>
                        <select name="kehadiran" id="kehadiran"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Tingkat</option>
                            <option value="0_hari" {{ request('kehadiran') == '0_hari' ? 'selected' : '' }}>0 Hari (Tidak Absen)</option>
                            <option value="ada_absen" {{ request('kehadiran') == 'ada_absen' ? 'selected' : '' }}>Ada Absen (> 0 Hari)</option>
                            <option value="tidak_lengkap" {{ request('kehadiran') == 'tidak_lengkap' ? 'selected' : '' }}>Hanya Masuk/Pulang Saja</option>
                        </select>
                    </div>

                    <!-- Dari Tanggal -->
                    <div class="md:col-span-1">
                        <label for="start_date" class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" required
                               value="{{ $startDateStr }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="md:col-span-1">
                        <label for="end_date" class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" required
                               value="{{ $endDateStr }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Action Buttons -->
                    <div class="md:col-span-5 flex items-end gap-2 justify-end mt-2">
                        @if(request()->anyFilled(['search', 'penempatan', 'grup', 'sub_grup', 'grup_bpjs', 'sub_grup_bpjs', 'kehadiran']))
                            <a href="{{ route('payroll.perhitungan-lembur') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                                Reset
                            </a>
                        @endif
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                            Filter Rekap
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if(count($rekapData) > 0)
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Hasil Kalkulasi</h3>
                    <p class="text-xs text-gray-500 mt-1">Ditemukan {{ count($rekapData) }} karyawan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-masukkan-pranota" class="hidden inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 focus:outline-none transition-colors duration-200 shadow-sm cursor-pointer mr-2">
                        <i class="fas fa-file-invoice mr-1.5"></i>
                        Masukkan Pranota
                    </button>
                </div>
            </div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">
                                <input type="checkbox" id="check-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16">
                                No
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                NIK
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Nama Karyawan
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Lembur (H. Biasa)
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Lembur (H. Libur)
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Total Bayar
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rekapData as $id => $data)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <input type="checkbox" value="{{ $data['karyawan']->id }}" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-indigo-600 text-sm font-semibold">
                                    {{ $data['karyawan']->nik }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $data['karyawan']->nama_lengkap }}
                                    <div class="text-xs text-gray-500 mt-1">{{ $data['karyawan']->penempatan ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    @if($data['total_jam_biasa'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $data['total_jam_biasa'] }} Jam
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    @if($data['total_jam_libur'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $data['total_jam_libur'] }} Jam
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-emerald-600 total-payout-text" data-jam-lembur="{{ $data['total_jam_biasa'] + $data['total_jam_libur'] }}">
                                    Rp {{ number_format($data['total_nominal'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <button type="button" data-nama="{{ $data['karyawan']->nama_lengkap }}" data-detail="{{ json_encode($data['detail']) }}" class="btn-detail text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors font-medium">
                                        Lihat Rincian
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-file-invoice-dollar text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">Tidak ada data lembur</h3>
                                        <p class="mt-1 text-sm text-gray-500">Silakan sesuaikan filter tanggal atau karyawan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Masukkan Pranota --}}
<div id="pranota-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closePranotaModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal panel -->
        <form action="{{ route('pranota-lembur-karyawan.store') }}" method="POST" id="form-payout" class="inline-flex flex-col align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl xl:max-w-7xl sm:w-full border border-gray-100 max-h-[90vh]">
            @csrf
            <!-- Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-lg">
                        <i class="fas fa-file-invoice text-blue-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800" id="modal-title">Konfirmasi Masuk Pranota Lembur Karyawan</h3>
                </div>
                <button type="button" onclick="closePranotaModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="bg-white px-6 py-5 flex-1 overflow-y-auto">
                <!-- Form Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Pranota</label>
                        <div class="flex rounded-md shadow-sm">
                            <input type="text" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 sm:text-sm font-mono text-gray-700 bg-gray-50" readonly value="PML-1-{{ date('m') }}-{{ date('y') }}-XXX (Auto Generated)">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pranota <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_pranota" id="modal_tanggal_pranota" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ date('Y-m-d') }}">
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
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Jam</th>
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

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-list text-indigo-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2 sm:mb-0" id="detailTitle">
                                Rincian Lembur
                            </h3>
                            <div class="flex items-center space-x-2">
                                <label for="filterDetailTanggal" class="text-xs font-medium text-gray-700">Filter:</label>
                                <input type="text" id="filterDetailTanggal" placeholder="Cari tanggal..." class="px-2 py-1 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 w-32 sm:w-48">
                                <button type="button" id="btnRefreshDetail" class="text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-2 py-1.5 rounded-md transition-colors flex items-center font-medium" title="Refresh & Reset Centang">
                                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="mt-4 max-h-[60vh] overflow-y-auto pr-2" id="detailContent">
                            <!-- Content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.btn-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            const tr = this.closest('tr');
            const nama = this.getAttribute('data-nama');
            let details = [];
            try {
                details = JSON.parse(this.getAttribute('data-detail'));
                // Cek apakah data sudah pernah diinisialisasi (sudah ada properti selected)
                let hasInitialized = details.some(r => r.hasOwnProperty('selected'));
                if (!hasInitialized) {
                    details.forEach(r => r.selected = false); // Default tidak terceklis
                    this.setAttribute('data-detail', JSON.stringify(details));
                }
            } catch (e) {
                console.error("Gagal parse details", e);
            }
            
            const btnEl = this;
            const filterInput = document.getElementById('filterDetailTanggal');
            filterInput.value = ''; // Reset filter when opening new modal
            
            // Recreate input to clear old event listeners
            const newFilterInput = filterInput.cloneNode(true);
            filterInput.parentNode.replaceChild(newFilterInput, filterInput);
            
            newFilterInput.addEventListener('input', function() {
                renderDetail();
            });

            // Set up Refresh Button
            const btnRefresh = document.getElementById('btnRefreshDetail');
            const newBtnRefresh = btnRefresh.cloneNode(true);
            btnRefresh.parentNode.replaceChild(newBtnRefresh, btnRefresh);

            newBtnRefresh.addEventListener('click', async function() {
                const icon = this.querySelector('i');
                icon.classList.add('fa-spin');
                
                try {
                    // Fetch latest data from server
                    const currentUrl = new URL(window.location.href);
                    
                    // Add search param for this employee's NIK
                    const tr = btnEl.closest('tr');
                    const nik = tr.querySelector('td:nth-child(3)').innerText.trim();
                    const karyawanId = tr.querySelector('.row-checkbox').value;
                    
                    currentUrl.searchParams.set('search', nik);
                    
                    const response = await fetch(currentUrl.toString());
                    const htmlText = await response.text();
                    
                    // Parse HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(htmlText, 'text/html');
                    
                    // Find the button for this employee using checkbox value
                    const newCheckbox = doc.querySelector(`input.row-checkbox[value="${karyawanId}"]`);
                    if (newCheckbox) {
                        const newBtn = newCheckbox.closest('tr').querySelector('.btn-detail');
                        if (newBtn) {
                            let newDetails = JSON.parse(newBtn.getAttribute('data-detail'));
                            
                            // Kembalikan ke default (tidak terceklis)
                            newDetails.forEach(r => r.selected = false);
                            
                            // Update current button's attribute and details array
                            btnEl.setAttribute('data-detail', JSON.stringify(newDetails));
                            details = newDetails;
                        }
                    }
                } catch (e) {
                    console.error('Gagal merefresh data:', e);
                    alert('Gagal mengambil data terbaru dari server.');
                }
                
                newFilterInput.value = '';
                icon.classList.remove('fa-spin');
                renderDetail();
            });
            
            function renderDetail() {
                const filterVal = newFilterInput.value.toLowerCase().trim();
                
                let html = '<table class="min-w-full divide-y divide-gray-200 mt-2"><thead class="bg-gray-50"><tr>';
                html += '<th class="px-4 py-2 text-center w-10"><input type="checkbox" id="detail-check-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></th>';
                html += '<th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th><th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tipe Hari</th><th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Jam Pulang</th><th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Durasi</th><th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tarif/Rule</th><th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Nominal</th></tr></thead><tbody class="divide-y divide-gray-200">';
                
                let total = 0;
                let totalJam = 0;
                let totalBiasa = 0;
                let totalLibur = 0;
                let allChecked = true;
                let rowCount = 0;
                
                // Hitung total dari SEMUA data (tidak terpengaruh filter pencarian)
                if (details.length > 0) {
                    details.forEach((row) => {
                        let isChecked = (row.selected !== false);
                        if (!isChecked) allChecked = false;
                        
                        if (isChecked) {
                            total += Number(row.nominal);
                            totalJam += Number(row.durasi_jam);
                            if (row.tipe_hari === 'Hari Biasa') {
                                totalBiasa += Number(row.durasi_jam);
                            } else {
                                totalLibur += Number(row.durasi_jam);
                            }
                        }
                    });
                }
                
                if (details.length > 0) {
                    details.forEach((row, i) => {
                        // Apply filter untuk tampilan saja
                        if (filterVal && !String(row.tanggal).toLowerCase().includes(filterVal)) {
                            return; // Skip if filter doesn't match
                        }
                        
                        rowCount++;
                        let isChecked = (row.selected !== false);
                        
                        html += `<tr>
                            <td class="px-4 py-2 text-center">
                                <input type="checkbox" class="detail-row-cb rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" data-idx="${i}" ${isChecked ? 'checked' : ''}>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">${row.tanggal}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">${row.tipe_hari}</td>
                            <td class="px-4 py-2 text-sm text-center text-gray-900 font-mono">${row.jam_pulang}</td>
                            <td class="px-4 py-2 text-sm text-center font-bold text-indigo-600">${row.durasi_jam} Jam</td>
                            <td class="px-4 py-2 text-sm text-gray-500">${row.rule}</td>
                            <td class="px-4 py-2 text-sm text-right font-bold text-emerald-600">Rp ${Number(row.nominal).toLocaleString('id-ID')}</td>
                        </tr>`;
                    });
                    
                    if (rowCount > 0) {
                        html += `<tr class="bg-gray-50">
                            <td colspan="6" class="px-4 py-3 text-right text-sm font-bold text-gray-900">TOTAL TERPILIH KESELURUHAN</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-emerald-700">Rp ${total.toLocaleString('id-ID')}</td>
                        </tr>`;
                    } else {
                        html += '<tr><td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada rincian yang cocok dengan pencarian</td></tr>';
                    }
                } else {
                    html += '<tr><td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada rincian</td></tr>';
                }
                html += '</tbody></table>';
                
                document.getElementById('detailContent').innerHTML = html;
                
                const checkAllCb = document.getElementById('detail-check-all');
                if (checkAllCb) {
                    checkAllCb.checked = allChecked && details.length > 0;
                    checkAllCb.addEventListener('change', function() {
                        const checked = this.checked;
                        details.forEach(r => r.selected = checked);
                        btnEl.setAttribute('data-detail', JSON.stringify(details));
                        renderDetail(); 
                    });
                }
                
                document.querySelectorAll('.detail-row-cb').forEach(cb => {
                    cb.addEventListener('change', function() {
                        const idx = this.getAttribute('data-idx');
                        details[idx].selected = this.checked;
                        btnEl.setAttribute('data-detail', JSON.stringify(details));
                        renderDetail();
                    });
                });
                
                updateMainTable(totalJam, totalBiasa, totalLibur, total);
            }
            
            function updateMainTable(totalJam, totalBiasa, totalLibur, totalNominal) {
                const payoutTd = tr.querySelector('.total-payout-text');
                if (payoutTd) {
                    payoutTd.setAttribute('data-jam-lembur', totalJam);
                    payoutTd.innerText = 'Rp ' + totalNominal.toLocaleString('id-ID');
                }
                
                const biasaTd = tr.querySelector('td:nth-child(5)');
                if (biasaTd) {
                    biasaTd.innerHTML = totalBiasa > 0 
                        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${totalBiasa} Jam</span>`
                        : `<span class="text-gray-400">-</span>`;
                }
                
                const liburTd = tr.querySelector('td:nth-child(6)');
                if (liburTd) {
                    liburTd.innerHTML = totalLibur > 0 
                        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">${totalLibur} Jam</span>`
                        : `<span class="text-gray-400">-</span>`;
                }
            }
            
            document.getElementById('detailTitle').innerText = 'Rincian Lembur: ' + nama;
            renderDetail();
            document.getElementById('detailModal').classList.remove('hidden');
        });
    });

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // --- LOGIKA PRANOTA MODAL ---
    document.addEventListener('DOMContentLoaded', function() {
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
                if (Array.from(rowCheckboxes).every(c => c.checked)) checkAll.checked = true;
                togglePranotaButton();
            });
        });

        if (btnPranota) {
            btnPranota.addEventListener('click', function() {
                openPranotaModal();
            });
        }

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

    function openPranotaModal() {
        const modalList = document.getElementById('modal-item-list');
        const countSpan = document.getElementById('modal-item-count');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        
        modalList.innerHTML = '';
        
        rowCheckboxes.forEach(cb => {
            const tr = cb.closest('tr');
            const karyawanId = cb.value;
            
            const karyawanNik = tr.querySelector('td:nth-child(3)').innerText.trim();
            const nameTd = tr.querySelector('td:nth-child(4)');
            
            // Extract Name (it's the first text node before the penempatan div)
            const karyawanName = nameTd.childNodes[0].textContent.trim() || nameTd.innerText.split('\n')[0].trim();
            
            // Extract Penempatan
            const penempatanNode = nameTd.querySelector('div.text-xs');
            const penempatan = penempatanNode ? penempatanNode.innerText.trim() : '-';
            
            const payoutTd = tr.querySelector('.total-payout-text');
            const totalJam = payoutTd.getAttribute('data-jam-lembur') + ' Jam';
            const payoutText = payoutTd.innerText;
            
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
                    ${totalJam}
                    <input type="hidden" name="karyawans[${karyawanId}][kehadiran]" value="${totalJam}">
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

    // --- END LOGIKA PRANOTA MODAL ---

    const grupMap = @json($grupMap ?? []);
    const oldSubGrup = '{{ request('sub_grup') }}';
    const oldGrup = '{{ request('grup') }}';

    const grupBpjsMap = @json($grupBpjsMap ?? []);
    const oldSubGrupBpjs = '{{ request('sub_grup_bpjs') }}';
    const oldGrupBpjs = '{{ request('grup_bpjs') }}';

    function handleGrupFilterChange() {
        const grupSelect = document.getElementById('grup');
        const subGrupSelect = document.getElementById('sub_grup');
        const selectedGrup = grupSelect.value;
        
        // Reset Sub Group options
        subGrupSelect.innerHTML = '<option value="">Semua Sub Group</option>';
        
        if (selectedGrup && grupMap[selectedGrup]) {
            const subs = grupMap[selectedGrup];
            subs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;
                subGrupSelect.appendChild(option);
            });
        }
    }

    function handleGrupBpjsFilterChange() {
        const grupBpjsSelect = document.getElementById('grup_bpjs');
        const subGrupBpjsSelect = document.getElementById('sub_grup_bpjs');
        const selectedGrupBpjs = grupBpjsSelect.value;
        
        // Reset Sub Group options
        subGrupBpjsSelect.innerHTML = '<option value="">Semua Sub Group BPJS</option>';
        
        if (selectedGrupBpjs && grupBpjsMap[selectedGrupBpjs]) {
            const subs = grupBpjsMap[selectedGrupBpjs];
            subs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;
                subGrupBpjsSelect.appendChild(option);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleGrupFilterChange();
        if (oldSubGrup) {
            document.getElementById('sub_grup').value = oldSubGrup;
        }

        handleGrupBpjsFilterChange();
        if (oldSubGrupBpjs) {
            document.getElementById('sub_grup_bpjs').value = oldSubGrupBpjs;
        }
    });
</script>
@endpush
@endsection
