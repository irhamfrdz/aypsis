@extends('layouts.app')

@section('title', 'Master Karyawan (ABK)')
@section('page_title', 'Master Karyawan (ABK)')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Header Section -->
        <div class="px-5 py-4 border-b bg-white">
            <div class="flex flex-col gap-4">
                <!-- TOP BAR: Title, Stats & Primary Actions -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <h1 class="text-xl font-bold text-gray-900 leading-tight">Master Karyawan (ABK)</h1>
                        @if(isset($counts))
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold border border-green-100 uppercase tracking-tighter">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    Aktif: {{ $counts['aktif'] }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-red-50 text-red-700 text-[10px] font-bold border border-red-100 uppercase tracking-tighter">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                    Berhenti: {{ $counts['berhenti'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Toolbar -->
                        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg p-0.5 shadow-sm">
                            <div class="flex items-center">
                                <a href="{{ route('master.karyawan.template') }}" class="p-1.5 text-gray-400 hover:text-green-600 transition-colors" title="Download Template"><i class="fas fa-file-download border-r pr-2 border-gray-200"></i></a>
                                <a href="{{ route('master.karyawan.import') }}" class="p-1.5 text-gray-400 hover:text-orange-600 transition-colors" title="Import Data"><i class="fas fa-file-import border-r pr-2 border-gray-200 ml-1"></i></a>
                                <a href="{{ route('master.karyawan.export-excel') }}" class="p-1.5 text-gray-400 hover:text-purple-600 transition-colors" title="Export Excel"><i class="fas fa-file-export border-r pr-2 border-gray-200 ml-1"></i></a>
                                <div class="flex items-center ml-1 bg-gray-100/50 rounded-md px-1">
                                    <a href="{{ route('master.karyawan.print', array_merge(request()->query(), ['divisi' => 'ABK'])) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-gray-900 transition-colors border-r border-gray-200" title="Cetak Daftar (List)"><i class="fas fa-list-ul"></i></a>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('master.karyawan.create') }}"
                           class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-lg font-bold text-[11px] text-white uppercase tracking-wider hover:bg-blue-700 transition-all shadow-sm">
                            <i class="fas fa-plus mr-1.5"></i> Tambah
                        </a>
                    </div>
                </div>

                <!-- COMPACT FILTERS -->
                <div class="bg-gray-50/50 rounded-xl p-3 border border-gray-200 shadow-sm">
                    <form method="GET" action="{{ route('master.karyawan.abk-index') }}" class="space-y-3">
                        @foreach(request()->except(['search', 'cabang', 'tanggal_masuk_start', 'tanggal_masuk_end', 'tanggal_berhenti_start', 'tanggal_berhenti_end', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <!-- Row 1: Core Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                            <div class="md:col-span-6 relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-search text-[10px]"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="block w-full pl-8 pr-8 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Cari Data Karyawan ABK...">
                                @if(request('search'))
                                    <a href="{{ route('master.karyawan.abk-index', request()->except(['search', 'page'])) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-300 hover:text-red-500"><i class="fas fa-times-circle text-[10px]"></i></a>
                                @endif
                            </div>

                            <div class="md:col-span-3">
                                @if(isset($cabangOptions) && count($cabangOptions) > 0)
                                <select name="cabang" onchange="this.form.submit()" class="block w-full py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-blue-500">
                                    <option value="">Cabang: Semua</option>
                                    @foreach($cabangOptions as $opt)
                                        <option value="{{ $opt }}" {{ request('cabang') == $opt ? 'selected' : '' }}>{{ strtoupper($opt) }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>

                            <div class="md:col-span-3 flex items-center gap-2">
                                <div class="flex-1 inline-flex border border-gray-300 rounded-lg bg-white p-0.5 shadow-xs">
                                    <a href="{{ route('master.karyawan.abk-index', array_merge(request()->query(), ['show_berhenti' => request('show_berhenti') ? null : '1', 'show_all' => null])) }}"
                                       class="flex-1 inline-flex justify-center items-center py-1 rounded-md text-[9px] font-bold uppercase transition-all {{ request('show_berhenti') ? 'bg-red-500 text-white' : 'text-gray-400 hover:bg-gray-50' }}">
                                        Berhenti
                                    </a>
                                    <a href="{{ route('master.karyawan.abk-index', array_merge(request()->query(), ['show_all' => request('show_all') ? null : '1', 'show_berhenti' => null])) }}"
                                       class="flex-1 inline-flex justify-center items-center py-1 rounded-md text-[9px] font-bold uppercase transition-all {{ request('show_all') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-50' }}">
                                        Semua
                                    </a>
                                </div>
                                <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white rounded-lg text-[10px] font-bold uppercase hover:bg-gray-800 transition-colors"><i class="fas fa-filter mr-1"></i> Filter</button>
                                @if(request()->anyFilled(['search', 'cabang', 'tanggal_masuk_start', 'tanggal_masuk_end', 'tanggal_berhenti_start', 'tanggal_berhenti_end']))
                                    <a href="{{ route('master.karyawan.abk-index') }}" class="p-1.5 text-gray-400 hover:text-red-500" title="Reset"><i class="fas fa-undo"></i></a>
                                @endif
                            </div>
                        </div>

                        <!-- Row 2: Date Ranges -->
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Tgl Masuk:</span>
                                <div class="flex items-center gap-1">
                                    <input type="date" name="tanggal_masuk_start" value="{{ request('tanggal_masuk_start') }}" class="py-1 px-2 border border-gray-300 rounded-md text-[10px] focus:ring-1 focus:ring-blue-500 w-28 shadow-xs">
                                    <span class="text-gray-300">-</span>
                                    <input type="date" name="tanggal_masuk_end" value="{{ request('tanggal_masuk_end') }}" class="py-1 px-2 border border-gray-300 rounded-md text-[10px] focus:ring-1 focus:ring-blue-500 w-28 shadow-xs">
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-bold text-red-400 uppercase tracking-tighter">Tgl Berhenti:</span>
                                <div class="flex items-center gap-1">
                                    <input type="date" name="tanggal_berhenti_start" value="{{ request('tanggal_berhenti_start') }}" class="py-1 px-2 border border-gray-300 rounded-md text-[10px] focus:ring-1 focus:ring-red-500 w-28 shadow-xs">
                                    <span class="text-gray-300">-</span>
                                    <input type="date" name="tanggal_berhenti_end" value="{{ request('tanggal_berhenti_end') }}" class="py-1 px-2 border border-gray-300 rounded-md text-[10px] focus:ring-1 focus:ring-red-500 w-28 shadow-xs">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Rows Per Page Selection --}}
            @include('components.rows-per-page', [
                'routeName' => 'master.karyawan.abk-index',
                'paginator' => $karyawans,
                'entityName' => 'karyawan',
                'entityNamePlural' => 'karyawan'
            ])
        </div>

        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        {!! nl2br(e(session('success'))) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Table Section with Sticky Header -->
        <div class="table-container overflow-x-auto max-h-screen hidden md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <span>NO.</span>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>NIK Karyawan</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.abk-index', array_merge(request()->query(), ['sort' => 'nik', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'nik' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan A-Z">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.abk-index', array_merge(request()->query(), ['sort' => 'nik', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'nik' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Z-A">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>NAMA LENGKAP</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.abk-index', array_merge(request()->query(), ['sort' => 'nama_lengkap', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'nama_lengkap' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan A-Z">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.abk-index', array_merge(request()->query(), ['sort' => 'nama_lengkap', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'nama_lengkap' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Z-A">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>DIVISI</span>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>PEKERJAAN</span>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>NO HP</span>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center space-x-1">
                                <span>TANGGAL MASUK</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('master.karyawan.abk-index', array_merge(request()->query(), ['sort' => 'tanggal_masuk', 'direction' => 'asc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors {{ request('sort') == 'tanggal_masuk' && request('direction') == 'asc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Terlama">
                                        <i class="fas fa-sort-up text-xs"></i>
                                    </a>
                                    <a href="{{ route('master.karyawan.abk-index', array_merge(request()->query(), ['sort' => 'tanggal_masuk', 'direction' => 'desc'])) }}"
                                       class="text-gray-400 hover:text-gray-600 transition-colors -mt-1 {{ request('sort') == 'tanggal_masuk' && request('direction') == 'desc' ? 'text-blue-600' : '' }}"
                                       title="Urutkan Terbaru">
                                        <i class="fas fa-sort-down text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        @if(request('show_berhenti'))
                            <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center space-x-1">
                                    <span>TANGGAL BERHENTI</span>
                                </div>
                            </th>
                        @endif
                        <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($karyawans as $karyawan)
                        <tr class="hover:bg-gray-50 {{ $karyawan->tanggal_berhenti ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900 font-medium">
                                {{ ($karyawans->currentPage() - 1) * $karyawans->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ strtoupper($karyawan->nik) }}
                                @if($karyawan->tanggal_berhenti)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-medium bg-red-100 text-red-800">
                                        BERHENTI
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">{{ strtoupper($karyawan->nama_lengkap) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                                <span class="inline-flex px-2 py-1 text-[10px] font-medium rounded-md bg-blue-100 text-blue-800">
                                    {{ strtoupper($karyawan->divisi ?: 'ABK') }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ strtoupper($karyawan->pekerjaan ?: '-') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">{{ strtoupper($karyawan->no_hp ?: '-') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-[10px] text-gray-900">
                                {{ $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d/M/Y') : '-' }}
                            </td>
                            @if(request('show_berhenti'))
                                <td class="px-4 py-2 whitespace-nowrap text-center text-[10px]">
                                    @if($karyawan->tanggal_berhenti)
                                        <span class="text-red-600 font-medium">
                                            {{ \Carbon\Carbon::parse($karyawan->tanggal_berhenti)->format('d/M/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-3 text-[10px]">
                                    <a href="{{ route('master.karyawan.crew-checklist', $karyawan->id) }}"
                                       class="text-purple-600 hover:text-purple-800 hover:underline font-medium"
                                       title="Checklist Kelengkapan Crew">
                                        Checklist
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('master.karyawan.crew-checklist-new', $karyawan->id) }}"
                                       class="text-green-600 hover:text-green-800 hover:underline font-medium"
                                       title="Checklist Baru (Simplified)">
                                        New
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('master.karyawan.show', $karyawan->id) }}"
                                       class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                        Lihat
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('master.karyawan.edit', $karyawan->id) }}"
                                       class="text-amber-600 hover:text-amber-800 hover:underline font-medium">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ request('show_berhenti') ? '9' : '8' }}" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data karyawan ABK
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Simplified) -->
        <div class="md:hidden">
            @forelse($karyawans as $karyawan)
                <div class="p-4 border-b border-gray-200 {{ $karyawan->tanggal_berhenti ? 'bg-red-50' : 'bg-white' }}">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-semibold text-sm">{{ strtoupper($karyawan->nama_lengkap) }}</h3>
                            <p class="text-xs text-gray-500">{{ strtoupper($karyawan->nik) }}</p>
                        </div>
                        @if($karyawan->tanggal_berhenti)
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800">BERHENTI</span>
                        @endif
                    </div>
                    <div class="flex justify-between mt-3">
                         <a href="{{ route('master.karyawan.show', $karyawan->id) }}" class="text-blue-600 text-xs font-medium">Lihat</a>
                         <a href="{{ route('master.karyawan.edit', $karyawan->id) }}" class="text-amber-600 text-xs font-medium">Edit</a>
                         <a href="{{ route('master.karyawan.crew-checklist-new', $karyawan->id) }}" class="text-green-600 text-xs font-medium">Checklist</a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500 text-sm">Tidak ada data karyawan ABK</div>
            @endforelse
        </div>

        @include('components.modern-pagination', ['paginator' => $karyawans, 'routeName' => 'master.karyawan.abk-index'])
    </div>
</div>

<script>
    function openDeleteModal(id, nik, nama) {
        // We'll reuse the general karyawan delete logic if needed, 
        // but for now let's just use simple confirmation or route to main index for delete
        if(confirm('Yakin ingin menghapus karyawan ' + nama + '?')) {
            // Post delete form...
        }
    }
</script>
@endsection
