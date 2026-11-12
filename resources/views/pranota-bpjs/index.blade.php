@extends('layouts.app')

@section('title', $isHistory ? 'Riwayat Pranota BPJS' : 'Pranota BPJS')
@section('page_title', $isHistory ? 'Riwayat Pranota BPJS' : 'Pranota BPJS')

@push('styles')
@unless($isHistory)
<link rel="stylesheet" href="{{ asset('css/pranota-bpjs-list.css') }}?v={{ filemtime(public_path('css/pranota-bpjs-list.css')) }}">
@endunless
@endpush

@section('content')
@include('pranota-bpjs.partials.ui-styles')
<div class="bpjs-workspace {{ ! $isHistory ? 'bpjs-pranota-list' : '' }} px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    {{-- Page Header --}}
    <div class="bpjs-page-header sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold"><span class="bpjs-heading-icon"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></span>{{ $isHistory ? 'Riwayat Pranota BPJS' : 'Pranota BPJS' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $isHistory ? 'Telusuri pranota yang telah dibuat, lihat rincian, dan cetak kembali.' : 'Kelola daftar pranota pembayaran BPJS karyawan' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route($isHistory ? 'pranota-bpjs.index' : 'pranota-bpjs.history') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"><i class="fas fa-clock-rotate-left" aria-hidden="true"></i>{{ $isHistory ? 'Kembali ke Daftar' : 'Riwayat Pranota' }}</a>
            @can('pranota-bpjs-create')
            <a href="{{ route('pranota-bpjs.create') }}"
               class="bpjs-index-create inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
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
    <div class="bpjs-summary grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {{-- Total Pranota --}}
        <div class="bpjs-index-stat bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-file-invoice text-teal-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Pranota</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pranotas->total() }}</p>
            </div>
        </div>

        {{-- Total Nominal --}}
        <div class="bpjs-index-stat bpjs-index-stat-money bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-indigo-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Nominal (Hasil Filter)</p>
                <p class="text-xl font-bold text-gray-800">
                    Rp {{ number_format($totalNominal, 2, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Draft --}}
        <div class="bpjs-index-stat bpjs-index-stat-draft bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-pencil-alt text-amber-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Status Draft</p>
                <p class="text-2xl font-bold text-gray-800">{{ $draftCount }}</p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <section class="bpjs-index-filters mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm" aria-labelledby="bpjs-filter-title">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 px-5 py-4">
            <h2 id="bpjs-filter-title" class="text-sm font-semibold text-gray-800"><i class="fas fa-sliders mr-2 text-teal-600" aria-hidden="true"></i>Filter Pranota</h2>
            @if(request('bulan') || request('tahun') || request('search') || request('status'))
                <span class="rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-700"><i class="fas fa-check mr-1" aria-hidden="true"></i>Filter aktif</span>
            @endif
        </div>
        <form method="GET" action="{{ route($isHistory ? 'pranota-bpjs.history' : 'pranota-bpjs.index') }}">
            <div class="bpjs-filter-grid">
                <div>
                    <label for="history-search"><i class="fas fa-search" aria-hidden="true"></i>Cari pranota</label>
                    <input id="history-search" type="search" name="search" value="{{ request('search') }}" placeholder="Nomor, pembuat, atau keterangan...">
                </div>
                <div>
                    <label for="history-status"><i class="fas fa-circle-check" aria-hidden="true"></i>Status</label>
                    <select id="history-status" name="status"><option value="">Semua status</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                </div>
                <div>
                    <label for="bpjs-filter-month"><i class="fas fa-calendar-alt" aria-hidden="true"></i>Bulan</label>
                    <select id="bpjs-filter-month" name="bulan">
                        <option value="">Semua bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected(request('bulan') == $i)>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }} - {{ date('F', mktime(0,0,0,$i,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="bpjs-filter-year"><i class="fas fa-calendar" aria-hidden="true"></i>Tahun</label>
                    <input id="bpjs-filter-year" type="number" name="tahun" value="{{ request('tahun') }}" placeholder="{{ date('Y') }}">
                </div>
            </div>
            <div class="bpjs-index-filter-actions flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
                @if(request('bulan') || request('tahun') || request('search') || request('status'))
                    <a href="{{ route($isHistory ? 'pranota-bpjs.history' : 'pranota-bpjs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-rotate-left" aria-hidden="true"></i>Reset</a>
                @endif
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700"><i class="fas fa-search" aria-hidden="true"></i>Tampilkan</button>
            </div>
        </form>
    </section>

    {{-- Table --}}
    <div class="bpjs-index-table-panel bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-gray-800"><i class="fas fa-list-ul mr-2 text-teal-600" aria-hidden="true"></i>{{ $isHistory ? 'Riwayat Pranota BPJS' : 'Daftar Pranota BPJS' }}</h2>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">{{ number_format($pranotas->total(), 0, ',', '.') }} pranota</span>
        </div>
        <div class="overflow-x-auto">
            <table class="bpjs-list-table table-auto w-full">
                @unless($isHistory)
                <colgroup><col style="width:3%"><col style="width:11%"><col style="width:9%"><col style="width:7%"><col style="width:7%"><col style="width:11%"><col style="width:11%"><col style="width:11%"><col style="width:13%"><col style="width:7%"><col style="width:10%"></colgroup>
                @endunless
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase text-gray-500 tracking-wider">
                        <th class="px-4 py-3 text-left w-10">#</th>
                        <th class="px-4 py-3 text-left"><i class="fas fa-file-invoice mr-1 text-gray-400" aria-hidden="true"></i>Nomor Pranota</th>
                        <th class="px-4 py-3 text-left"><i class="fas fa-calendar-alt mr-1 text-gray-400" aria-hidden="true"></i>Tanggal</th>
                        <th class="px-4 py-3 text-center"><i class="fas fa-calendar mr-1 text-gray-400" aria-hidden="true"></i>Periode</th>
                        <th class="px-4 py-3 text-center"><i class="fas fa-users mr-1 text-gray-400" aria-hidden="true"></i>Karyawan</th>
                        <th class="px-4 py-3 text-right">JKN</th>
                        <th class="px-4 py-3 text-right">BP Jamsostek</th>
                        <th class="px-4 py-3 text-right">Grand Total</th>
                        <th class="px-4 py-3 text-left"><i class="fas fa-user mr-1 text-gray-400" aria-hidden="true"></i>Dibuat Oleh</th>
                        <th class="px-4 py-3 text-center"><i class="fas fa-circle-check mr-1 text-gray-400" aria-hidden="true"></i>Status</th>
                        <th class="px-4 py-3 text-center"><i class="fas fa-ellipsis mr-1 text-gray-400" aria-hidden="true"></i>Aksi</th>
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
                                Rp {{ number_format($pranota->total_bpjs_kesehatan, 2, ',', '.') }}
                            </td>

                            {{-- Jamsostek --}}
                            <td class="px-4 py-3 text-right text-gray-700 font-medium whitespace-nowrap">
                                Rp {{ number_format($pranota->total_bpjs_ketenagakerjaan, 2, ',', '.') }}
                            </td>

                            {{-- Grand Total --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <span class="font-bold text-gray-800">
                                    Rp {{ number_format($pranota->grand_total, 2, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-700"><div>{{ $pranota->createdBy?->name ?? '-' }}</div><div class="mt-1 text-xs text-gray-500">{{ $pranota->created_at?->format('d/m/Y H:i') }}</div></td>
                            {{-- Status --}}
                            <td class="px-4 py-3 text-center">
                                @if($pranota->status == 'draft')
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <i class="fas fa-circle text-amber-400" style="font-size:6px"></i>
                                        Draft
                                    </span>
                                @elseif($pranota->status == 'paid')
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full"><i class="fas fa-check-circle" aria-hidden="true"></i>Lunas</span>
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
                                <div class="bpjs-index-row-actions flex items-center justify-center gap-1">
                                    @can('pranota-bpjs-view')
                                        <a href="{{ route('pranota-bpjs.export-excel', $pranota->id) }}" title="Export Excel" class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition-colors">
                                            <i class="fas fa-file-excel text-xs"></i>
                                        </a>
                                    @endcan
                                    @can('pembayaran-pranota-bpjs-create')
                                        @if(in_array($pranota->status, ['draft', 'approved', 'unpaid']) && $pranota->grand_total > 0)
                                            <a href="{{ route('pembayaran-pranota-bpjs.create', ['pranota_id' => $pranota->id]) }}" title="Bayar Pranota" class="inline-flex items-center justify-center w-7 h-7 rounded bg-green-50 text-green-700"><i class="fas fa-money-bill-wave text-xs"></i></a>
                                        @endif
                                    @endcan
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
                            <td colspan="11" class="px-4 py-16 text-center">
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

