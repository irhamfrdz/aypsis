@extends('layouts.app')

@section('title', 'Dashboard Cek Kendaraan Mingguan')
@section('page_title', 'Dashboard Cek Kendaraan Mingguan')

@section('content')
<div class="p-4 sm:p-6 max-w-[1600px] mx-auto">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Status Kendaraan <span class="text-indigo-600">Mingguan</span></h1>
            <p class="text-gray-500 mt-2 flex items-center">
                <i class="fas fa-info-circle mr-2 text-indigo-400"></i>
                Memantau kelengkapan pengecekan mingguan oleh seluruh supir armada
            </p>
            <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">
                {{ $weekStart->format('d M Y') }} — {{ $weekEnd->format('d M Y') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <a href="{{ route('admin.cek-kendaraan.weekly', ['week_start' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" 
                   class="p-2.5 hover:bg-gray-50 text-gray-600 border-r border-gray-100 transition-all">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <form action="{{ route('admin.cek-kendaraan.weekly') }}" method="GET" class="relative group">
                    <input type="date" name="week_start" value="{{ $weekStart->format('Y-m-d') }}" 
                        class="pl-4 pr-4 py-2.5 bg-transparent border-none focus:ring-0 transition-all text-sm font-bold text-gray-700 outline-none" 
                        onchange="this.form.submit()">
                </form>
                <a href="{{ route('admin.cek-kendaraan.weekly', ['week_start' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" 
                   class="p-2.5 hover:bg-gray-50 text-gray-600 border-l border-gray-100 transition-all">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <a href="{{ route('admin.cek-kendaraan.weekly.export', ['week_start' => $weekStart->format('Y-m-d')]) }}" class="px-4 py-2.5 bg-emerald-600 text-white rounded-2xl shadow-sm hover:bg-emerald-700 transition-all text-sm font-bold flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <button onclick="window.location.reload()" class="p-2.5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:bg-gray-50 text-gray-600 transition-all tooltip" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    @php
        $totalDrivers = $drivers->count();
        $fullCompliance = 0;
        $partialCompliance = 0;
        $noCompliance = 0;

        foreach($drivers as $driver) {
            $driverChecks = $checksForWeek->get($driver->id);
            $checkCount = $driverChecks ? $driverChecks->count() : 0;
            
            if ($checkCount >= 7) $fullCompliance++;
            elseif ($checkCount > 0) $partialCompliance++;
            else $noCompliance++;
        }
        
        $overallRate = $totalDrivers > 0 ? round((($fullCompliance * 7 + $partialCompliance * 3) / ($totalDrivers * 7)) * 100) : 0;
    @endphp

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Supir --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Supir</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $totalDrivers }}</h3>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-500">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-gray-500">
                <span class="px-2 py-0.5 bg-gray-100 rounded-full mr-2">Driver Aktif</span>
            </div>
        </div>

        {{-- Full Compliance --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-1">Full (7/7)</p>
                    <h3 class="text-3xl font-black text-emerald-600">{{ $fullCompliance }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-emerald-600">
                Cek setiap hari dalam seminggu
            </div>
        </div>

        {{-- Partial --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1">Parsial (1-6)</p>
                    <h3 class="text-3xl font-black text-amber-500">{{ $partialCompliance }}</h3>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-500">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-amber-500">
                Melewatkan beberapa hari
            </div>
        </div>

        {{-- Belum Cek --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-rose-400 uppercase tracking-widest mb-1">Belum (0/7)</p>
                    <h3 class="text-3xl font-black text-rose-500">{{ $noCompliance }}</h3>
                </div>
                <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-500">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-rose-500">
                Sama sekali belum cek minggu ini
            </div>
        </div>
    </div>

    {{-- Data Table Section --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input type="text" id="driverSearch" placeholder="Cari nama supir atau NIK..." 
                    class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-100 transition-all text-sm font-medium outline-none">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-400 mr-2 uppercase">Filter:</span>
                <button onclick="filterStatus('all')" class="status-btn active px-4 py-2 rounded-xl text-xs font-bold transition-all bg-indigo-50 text-indigo-600">Semua</button>
                <button onclick="filterStatus('full')" class="status-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:bg-gray-50">Full</button>
                <button onclick="filterStatus('partial')" class="status-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:bg-gray-50">Parsial</button>
                <button onclick="filterStatus('none')" class="status-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:bg-gray-50">Belum</button>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table id="driverTable" class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-400 text-[10px] uppercase font-black tracking-[0.2em]">
                        <th class="px-8 py-5 min-w-[250px]">Informasi Supir</th>
                        @foreach($weekDays as $day)
                            <th class="px-2 py-5 text-center">
                                <div>{{ $day->isoFormat('ddd') }}</div>
                                <div class="text-[8px] opacity-60">{{ $day->format('d/m') }}</div>
                            </th>
                        @endforeach
                        <th class="px-8 py-5 text-center">Total</th>
                        <th class="px-8 py-5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($drivers as $driver)
                        @php
                            $driverChecks = $checksForWeek->get($driver->id);
                            $checkCount = $driverChecks ? $driverChecks->count() : 0;
                            $status = 'none';
                            if ($checkCount >= 7) $status = 'full';
                            elseif ($checkCount > 0) $status = 'partial';
                        @endphp
                        <tr class="driver-row hover:bg-indigo-50/30 transition-colors group" data-status="{{ $status }}" data-search="{{ strtolower($driver->nama_lengkap . ' ' . $driver->nik) }}">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $checkCount > 0 ? 'from-indigo-500 to-purple-600 text-white' : 'from-gray-100 to-gray-200 text-gray-500' }} flex items-center justify-center font-black text-sm shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($driver->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-gray-800 leading-tight">{{ $driver->nama_lengkap }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">NIK: {{ $driver->nik ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            @foreach($weekDays as $day)
                                @php
                                    $dateKey = $day->format('Y-m-d');
                                    $check = $driverChecks ? $driverChecks->get($dateKey) : null;
                                    $dayCheck = $check ? $check->first() : null;
                                @endphp
                                <td class="px-2 py-5 text-center">
                                    @if($dayCheck)
                                        <a href="{{ route('admin.cek-kendaraan.show', $dayCheck->id) }}" target="_blank" 
                                           class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all tooltip" 
                                           title="Cek pukul {{ Carbon\Carbon::parse($dayCheck->jam)->format('H:i') }}">
                                            <i class="fas fa-check text-[10px]"></i>
                                        </a>
                                    @else
                                        <div class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-300">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </div>
                                    @endif
                                </td>
                            @endforeach

                            <td class="px-8 py-5 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-sm font-black {{ $checkCount >= 7 ? 'text-emerald-600' : ($checkCount > 0 ? 'text-amber-500' : 'text-rose-500') }}">
                                        {{ $checkCount }}/7
                                    </span>
                                    <div class="w-16 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full {{ $checkCount >= 7 ? 'bg-emerald-500' : ($checkCount > 0 ? 'bg-amber-400' : 'bg-rose-400') }}" 
                                             style="width: {{ ($checkCount / 7) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    @if($checkCount > 0)
                                        <div class="relative group/actions">
                                            <button class="p-2 bg-gray-50 text-gray-400 hover:text-indigo-600 rounded-xl transition-all">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 hidden group-hover/actions:block z-50">
                                                <p class="px-4 py-1 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1">Detail Cek</p>
                                                @foreach($weekDays as $day)
                                                    @php
                                                        $dateKey = $day->format('Y-m-d');
                                                        $dayChecks = $driverChecks ? $driverChecks->get($dateKey) : null;
                                                        $dc = $dayChecks ? $dayChecks->first() : null;
                                                    @endphp
                                                    @if($dc)
                                                        <a href="{{ route('admin.cek-kendaraan.show', $dc->id) }}" target="_blank" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                                            {{ $day->isoFormat('dddd') }}: {{ Carbon\Carbon::parse($dc->jam)->format('H:i') }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <button disabled class="p-2 bg-gray-50 text-gray-200 rounded-xl cursor-not-allowed">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center border-4 border-white shadow-inner mb-4">
                                        <i class="fas fa-users-slash text-3xl text-gray-200"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-400">Tidak ada data driver ditemukan</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="lg:hidden p-4 grid grid-cols-1 gap-4 bg-gray-50/50" id="mobileCards">
            @forelse($drivers as $driver)
                @php
                    $driverChecks = $checksForWeek->get($driver->id);
                    $checkCount = $driverChecks ? $driverChecks->count() : 0;
                    $status = 'none';
                    if ($checkCount >= 7) $status = 'full';
                    elseif ($checkCount > 0) $status = 'partial';
                @endphp
                <div class="driver-row bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all active:scale-[0.98]" data-status="{{ $status }}" data-search="{{ strtolower($driver->nama_lengkap . ' ' . $driver->nik) }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $checkCount > 0 ? 'from-indigo-500 to-purple-600' : 'from-gray-100 to-gray-200 text-gray-400' }} text-white flex items-center justify-center font-black text-sm shadow-sm">
                                {{ substr($driver->nama_lengkap, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-bold text-gray-800 leading-tight">{{ $driver->nama_lengkap }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">NIK: {{ $driver->nik ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs font-black {{ $checkCount >= 7 ? 'text-emerald-600' : ($checkCount > 0 ? 'text-amber-500' : 'text-rose-500') }}">
                                {{ $checkCount }}/7 Hari
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100">
                        @foreach($weekDays as $day)
                            @php
                                $dateKey = $day->format('Y-m-d');
                                $hasCheck = $driverChecks && $driverChecks->has($dateKey);
                            @endphp
                            <div class="flex flex-col items-center">
                                <span class="text-[8px] font-black text-gray-400 uppercase mb-1">{{ substr($day->isoFormat('ddd'), 0, 1) }}</span>
                                <div class="w-3 h-3 rounded-full {{ $hasCheck ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="py-10 text-center col-span-full">
                    <p class="text-sm font-bold text-gray-400">Tidak ada data driver ditemukan</p>
                </div>
            @endforelse
        </div>

        @if($drivers->count() > 0)
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 text-[10px] font-black text-gray-400 flex flex-col sm:flex-row justify-between items-center uppercase tracking-[0.2em] gap-2">
            <span>{{ $drivers->count() }} Driver Armada Terdaftar</span>
            <span class="flex items-center"><i class="fas fa-sync-alt mr-2 text-xs text-indigo-400"></i> Terakhir Diperbarui: {{ now()->format('H:i') }} WIB</span>
        </div>
        @endif
    </div>
</div>

<style>
    .status-btn.active {
        background-color: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
    }

    .tooltip {
        position: relative;
    }
    
    /* Minimalist Tooltip logic if needed, or use browser native title */
</style>

@push('scripts')
<script>
    function filterStatus(status) {
        // Update button styles
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-indigo-50', 'text-indigo-600');
            btn.classList.add('text-gray-500', 'hover:bg-gray-50');
        });
        
        const activeBtn = event.currentTarget;
        activeBtn.classList.add('active', 'bg-indigo-50', 'text-indigo-600');
        activeBtn.classList.remove('text-gray-500', 'hover:bg-gray-50');

        // Filter table rows and mobile cards
        const rows = document.querySelectorAll('.driver-row');
        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.getElementById('driverSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.driver-row');
        
        rows.forEach(row => {
            const searchText = row.dataset.search;
            if (searchText.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection
