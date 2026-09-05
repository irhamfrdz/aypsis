@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    {{-- Page Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Pranota BPJS</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola daftar pranota pembayaran BPJS karyawan</p>
        </div>
        <div class="flex items-center gap-2">
            @can('pranota-bpjs-create')
            <a href="{{ route('pranota-bpjs.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
                <i class="fas fa-plus text-xs"></i>
                Buat Pranota BPJS
            </a>
            @endcan
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {{-- Total Pranota --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-file-invoice text-teal-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Pranota</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pranotas->total() }}</p>
            </div>
        </div>

        {{-- Total Nominal --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-indigo-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Nominal (Halaman Ini)</p>
                <p class="text-xl font-bold text-gray-800">
                    Rp {{ number_format($pranotas->sum('grand_total'), 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Draft --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-pencil-alt text-amber-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Status Draft</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pranotas->where('status', 'draft')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-4 overflow-hidden">
        <form method="GET" action="{{ route('pranota-bpjs.index') }}">
            <div class="flex flex-wrap items-center divide-x divide-gray-100">

                {{-- Label Filter --}}
                <div class="px-4 py-3 flex items-center gap-2 text-gray-500 flex-shrink-0">
                    <i class="fas fa-filter text-xs text-indigo-400"></i>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Filter</span>
                </div>

                {{-- Bulan --}}
                <div class="px-4 py-3 flex-1 min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-400 uppercase mb-1 tracking-wider">
                        <i class="fas fa-calendar-alt mr-1 text-indigo-300"></i> Bulan
                    </label>
                    <select name="bulan" class="form-select text-sm border-0 p-0 focus:ring-0 bg-transparent font-medium text-gray-700 w-full">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }} — {{ date('F', mktime(0,0,0,$i,1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="px-4 py-3 flex-shrink-0">
                    <label class="block text-xs font-semibold text-gray-400 uppercase mb-1 tracking-wider">
                        <i class="fas fa-calendar mr-1 text-indigo-300"></i> Tahun
                    </label>
                    <input type="number" name="tahun"
                        value="{{ request('tahun') }}"
                        placeholder="{{ date('Y') }}"
                        class="form-input text-sm border-0 p-0 focus:ring-0 bg-transparent font-medium text-gray-700 w-24">
                </div>

                {{-- Tombol Aksi --}}
                <div class="px-4 py-3 ml-auto flex items-center gap-2 flex-shrink-0">
                    @if(request('bulan') || request('tahun'))
                        <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-600 text-xs font-semibold px-2 py-0.5 rounded-full">
                            <i class="fas fa-circle text-indigo-400" style="font-size:6px"></i>
                            Filter aktif
                        </span>
                        <a href="{{ route('pranota-bpjs.index') }}"
                           class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-red-500 border border-gray-200 hover:border-red-300 bg-white hover:bg-red-50 rounded-lg px-3 py-2 transition-all">
                            <i class="fas fa-times text-xs"></i>
                            Reset
                        </a>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg px-4 py-2 shadow-sm shadow-indigo-200 transition-all">
                        <i class="fas fa-search text-xs"></i>
                        Tampilkan
                    </button>
                </div>

            </div>
        </form>
    </div>


    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase text-gray-500 tracking-wider">
                        <th class="px-4 py-3 text-left w-10">#</th>
                        <th class="px-4 py-3 text-left">Nomor Pranota</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-center">Periode</th>
                        <th class="px-4 py-3 text-center">Karyawan</th>
                        <th class="px-4 py-3 text-right">JKN</th>
                        <th class="px-4 py-3 text-right">BP Jamsostek</th>
                        <th class="px-4 py-3 text-right">Grand Total</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($pranotas as $index => $pranota)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            {{-- No --}}
                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ $pranotas->firstItem() + $index }}
                            </td>

                            {{-- Nomor Pranota --}}
                            <td class="px-4 py-3">
                                <a href="{{ route('pranota-bpjs.show', $pranota->id) }}"
                                   class="font-semibold text-teal-600 hover:text-teal-800 hover:underline">
                                    {{ $pranota->nomor_pranota }}
                                </a>
                                @if($pranota->keterangan)
                                    <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $pranota->keterangan }}</p>
                                @endif
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                <i class="fas fa-calendar-alt text-gray-300 mr-1"></i>
                                {{ $pranota->tanggal_pranota->format('d M Y') }}
                            </td>

                            {{-- Periode --}}
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    <i class="fas fa-calendar text-blue-400 text-xs"></i>
                                    {{ str_pad($pranota->periode_bulan, 2, '0', STR_PAD_LEFT) }}/{{ $pranota->periode_tahun }}
                                </span>
                            </td>

                            {{-- Jumlah Karyawan --}}
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    <i class="fas fa-users text-purple-400 text-xs"></i>
                                    {{ $pranota->total_karyawan }}
                                </span>
                            </td>

                            {{-- JKN --}}
                            <td class="px-4 py-3 text-right text-gray-700 font-medium whitespace-nowrap">
                                Rp {{ number_format($pranota->total_bpjs_kesehatan, 0, ',', '.') }}
                            </td>

                            {{-- Jamsostek --}}
                            <td class="px-4 py-3 text-right text-gray-700 font-medium whitespace-nowrap">
                                Rp {{ number_format($pranota->total_bpjs_ketenagakerjaan, 0, ',', '.') }}
                            </td>

                            {{-- Grand Total --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <span class="font-bold text-gray-800">
                                    Rp {{ number_format($pranota->grand_total, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 text-center">
                                @if($pranota->status == 'draft')
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <i class="fas fa-circle text-amber-400" style="font-size:6px"></i>
                                        Draft
                                    </span>
                                @elseif($pranota->status == 'approved')
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <i class="fas fa-circle text-green-400" style="font-size:6px"></i>
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <i class="fas fa-circle text-gray-400" style="font-size:6px"></i>
                                        {{ ucfirst($pranota->status) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    @can('pranota-bpjs-view')
                                        <a href="{{ route('pranota-bpjs.show', $pranota->id) }}"
                                           title="Detail"
                                           class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    @endcan

                                    @can('pranota-bpjs-update')
                                        @if($pranota->status == 'draft')
                                            <a href="{{ route('pranota-bpjs.edit', $pranota->id) }}"
                                               title="Edit"
                                               class="inline-flex items-center justify-center w-7 h-7 rounded bg-yellow-50 hover:bg-yellow-100 text-yellow-600 transition-colors">
                                                <i class="fas fa-pencil-alt text-xs"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    @can('pranota-bpjs-delete')
                                        @if($pranota->status == 'draft')
                                            <form action="{{ route('pranota-bpjs.destroy', $pranota->id) }}"
                                                  method="POST" class="inline-block"
                                                  onsubmit="return confirm('Hapus pranota {{ $pranota->nomor_pranota }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded bg-red-50 hover:bg-red-100 text-red-600 transition-colors">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <i class="fas fa-file-invoice text-5xl opacity-30"></i>
                                    <p class="font-medium text-gray-500">Belum ada data Pranota BPJS</p>
                                    <p class="text-xs">
                                        @can('pranota-bpjs-create')
                                            <a href="{{ route('pranota-bpjs.create') }}" class="text-teal-600 hover:underline">
                                                Buat pranota pertama →
                                            </a>
                                        @else
                                            Tidak ada data yang sesuai dengan filter
                                        @endcan
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pranotas->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">
            {{ $pranotas->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

