@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pranota-bpjs-allowances.css') }}?v={{ filemtime(public_path('css/pranota-bpjs-allowances.css')) }}">
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Pranota BPJS</h1>
        </div>
        <div class="flex items-center space-x-2">
            @can('pranota-bpjs-view')
            <a href="{{ route('pranota-bpjs.history') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"><i class="fas fa-clock-rotate-left" aria-hidden="true"></i>Riwayat Pranota</a>
            @endcan
            <a href="{{ route('pranota-bpjs.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            @can('pranota-bpjs-update')
                @if($pranota_bpj->status === 'draft')
                    <a href="{{ route('pranota-bpjs.edit', $pranota_bpj->id) }}" class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700">Ubah Nominal</a>
                @endif
            @endcan
            <button onclick="window.print()" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-print mr-2"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Print Area -->
    <div id="print-area" class="bg-white shadow-lg rounded-sm border border-gray-200 mb-8 p-6 print:shadow-none print:border-none print:m-0 print:p-0">
        
        <!-- Header Print -->
        <div class="text-center mb-8 border-b-2 border-gray-800 pb-4">
            <h2 class="text-2xl font-bold uppercase tracking-wider">PRANOTA BPJS</h2>
            <p class="text-gray-600 mt-1">Nomor: <span class="font-bold text-gray-800">{{ $pranota_bpj->nomor_pranota }}</span></p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-8">
            <div>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 w-1/3 text-gray-600 font-medium">Tanggal Pranota</td>
                        <td class="py-1 w-4 text-center">:</td>
                        <td class="py-1 font-semibold text-gray-800">{{ $pranota_bpj->tanggal_pranota->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600 font-medium">Periode</td>
                        <td class="py-1 text-center">:</td>
                        <td class="py-1 font-semibold text-gray-800">{{ date('F', mktime(0, 0, 0, $pranota_bpj->periode_bulan, 1)) }} {{ $pranota_bpj->periode_tahun }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 w-1/3 text-gray-600 font-medium">Dibuat Oleh</td>
                        <td class="py-1 w-4 text-center">:</td>
                        <td class="py-1 font-semibold text-gray-800">{{ $pranota_bpj->createdBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600 font-medium">Status</td>
                        <td class="py-1 text-center">:</td>
                        <td class="py-1 font-semibold text-gray-800 uppercase">{{ $pranota_bpj->status }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($pranota_bpj->keterangan)
        <div class="mb-8">
            <h3 class="text-sm font-bold text-gray-800 mb-2">Keterangan:</h3>
            <div class="p-3 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700">
                {{ $pranota_bpj->keterangan }}
            </div>
        </div>
        @endif

        @php
            $componentGroups = [
                'JKN' => ['kis_tunjangan' => 'KIS 4% (TUNJ)', 'kis_biaya' => 'KIS 4% (BIAY)', 'bpjs_ketenagakerjaan' => 'KIS 1% (Hutang)'],
                'PPU' => ['jht_biaya' => 'PPU JHT 3.7%', 'jht_hutang' => 'PPU JHT 2%', 'jkk_tunjangan' => 'PPU JKK 0.24%', 'jkm_tunjangan' => 'PPU JKM 0.3%', 'jp_biaya' => 'PPU JP 2%', 'jp_hutang' => 'PPU JP 1%'],
                'BPU Crew' => ['jkk_hutang' => 'BPU JHT (Kary)', 'bpu_jkk_tunjangan' => 'BPU JKK 1%', 'bpu_jkm' => 'BPU JKM'],
                'BPU Non Crew' => ['noncrew_jht_biaya' => 'JHT 2% (Biaya)', 'noncrew_jht_hutang' => 'JHT 2% (Hutang)', 'noncrew_jkk_tunjangan' => 'JKK 1% (Tunj)', 'noncrew_jkm_tunjangan' => 'JKM (Rp)'],
            ];
            $componentFields = array_map('array_keys', $componentGroups);
            $componentGroups['JKN']['total_jkn'] = 'TOTAL JKN';
            $componentGroups['PPU']['total_ppu'] = 'TOTAL PPU';
            $hasLegacy = $pranota_bpj->details->contains(fn ($detail) => $detail->rincian_bpjs === null);
            $componentGroups['Ringkasan Tunjangan'] = ['allowance_ppu' => 'Tunjangan JKK 0,24% + JKM 0,3%', 'allowance_bpu' => 'Tunjangan BPU JKK 1% + JKM', 'allowance_jkn' => 'Tunjangan JKN 4%', 'allowance_kary_jht' => 'Beban KARY. JHT 2%, JP 1% & BPU JHT', 'allowance_kary_jkn' => 'Beban KARY. JKN 1%', 'allowance_pt' => 'Beban PT. PPU JHT 3.7%, PPU JP 2% & JHT 2% (BIAYA)', 'allowance_non_kary' => 'Beban NON KARY. JKN & BPJS', 'no_voucher' => 'No Voucher'];
            if ($hasLegacy) $componentGroups['Data Lama'] = ['legacy_jamsostek' => 'Jamsostek Gabungan'];
            $amount = function ($detail, $field) use ($componentFields) {
                if ($field === 'total_jkn') return (float) $detail->bpjs_kesehatan + (float) $detail->bpjs_ketenagakerjaan;
                if ($field === 'total_ppu') return collect($componentFields['PPU'])->sum(fn ($key) => (float) data_get($detail->rincian_bpjs, $key, 0));
                if (str_starts_with($field, 'allowance_') && isset($detail->tunjangan_manual[substr($field, 10)])) return (float) $detail->tunjangan_manual[substr($field, 10)];
                if (in_array($field, ['allowance_kary_jht', 'allowance_kary_jkn', 'allowance_pt', 'allowance_non_kary'])) return $detail->summaryBurden(substr($field, 10));
                if ($field === 'allowance_ppu') return $detail->tunjangan_ppu;
                if ($field === 'allowance_jkn') return (float) $detail->kis_tunjangan;
                if (in_array($field, ['allowance_ppu', 'allowance_bpu'])) {
                    $fields = $field === 'allowance_ppu' ? ['jkk_tunjangan', 'jkm_tunjangan']
                        : ['bpu_jkk_tunjangan', 'bpu_jkm', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan'];
                    return collect($fields)->sum(fn ($key) => (float) data_get($detail->rincian_bpjs, $key, 0));
                }
                if (in_array($field, ['kis_tunjangan', 'kis_biaya', 'bpjs_ketenagakerjaan'])) return (float) $detail->$field;
                if ($field === 'legacy_jamsostek') return $detail->rincian_bpjs === null
                    ? (float) $detail->jht_biaya + (float) $detail->jht_hutang + (float) $detail->jkk_tunjangan + (float) $detail->jkm_tunjangan + (float) $detail->jp_biaya + (float) $detail->jp_hutang : 0;
                return (float) data_get($detail->rincian_bpjs, $field, 0);
            };
            $groupTotal = fn ($fields) => $pranota_bpj->details->sum(fn ($detail) => collect($fields)->sum(fn ($field) => $amount($detail, $field)));
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
            @foreach(['Total Karyawan' => $pranota_bpj->total_karyawan . ' Orang', 'Total JKN' => 'Rp ' . number_format($pranota_bpj->total_bpjs_kesehatan, 2, ',', '.'), 'Total PPU' => 'Rp ' . number_format($groupTotal($componentFields['PPU']), 2, ',', '.'), 'Total BPU' => 'Rp ' . number_format($groupTotal(array_merge(array_keys($componentGroups['BPU Crew']), array_keys($componentGroups['BPU Non Crew']))), 2, ',', '.'), 'Grand Total' => 'Rp ' . number_format($pranota_bpj->grand_total, 2, ',', '.')] as $label => $value)
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4"><p class="text-xs font-semibold uppercase text-gray-500">{{ $label }}</p><p class="mt-2 font-bold text-teal-800">{{ $value }}</p></div>
            @endforeach
        </div>
        @if($hasLegacy)
            <p class="rounded-lg border border-amber-200 bg-amber-50 p-3 mb-4 text-sm text-amber-900">Pranota ini memuat data lama tanpa rincian PPU/BPU terpisah. Nominalnya ditampilkan pada kolom Jamsostek Gabungan dan tetap masuk Grand Total. Total PPU/BPU di atas hanya menjumlahkan rincian yang tersedia.</p>
        @endif
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Rincian Iuran BPJS Karyawan</h3>
            <p class="text-xs text-gray-500 mb-4 print:hidden">Geser tabel ke kanan untuk melihat seluruh komponen dan ringkasan tunjangan. Ringkasan tidak ditambahkan kembali ke Grand Total.</p>
            <div class="bpjs-detail-scroll bpjs-table-scroll overflow-x-auto rounded-lg border border-gray-200" role="region" aria-label="Rincian iuran BPJS, gulir untuk melihat seluruh data" tabindex="0">
                <table class="bpjs-detail-table w-full text-xs border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th colspan="5" class="border border-gray-200 p-3">Informasi Karyawan</th>
                            @foreach($componentGroups as $group => $columns)
                                <th colspan="{{ count($columns) }}" class="border border-gray-200 p-3 {{ $group === 'JKN' ? 'bg-indigo-50 text-indigo-800' : 'bg-teal-50 text-teal-800' }}">{{ $group }}</th>
                            @endforeach
                            <th rowspan="2" class="border border-gray-200 p-3 bg-teal-50">Subtotal (Rp)</th>
                        </tr>
                        <tr class="bg-gray-50">
                            @foreach(['No', 'Nama Karyawan', 'Group BPJS', 'Tipe JKN', 'Tipe Jamsostek'] as $label)
                                <th class="border border-gray-200 p-3 text-left">{{ $label }}</th>
                            @endforeach
                            @foreach($componentGroups as $columns)
                                @foreach($columns as $field => $label)<th class="border border-gray-200 p-3 text-right {{ $field === 'total_jkn' ? 'bpjs-group-total-jkn' : ($field === 'total_ppu' ? 'bpjs-group-total-ppu' : '') }}">{{ $label }}</th>@endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pranota_bpj->details as $index => $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-200 p-3 text-center">{{ $index + 1 }}</td>
                                <td class="border border-gray-200 p-3" style="min-width:220px">
                                    <span class="font-semibold">{{ $detail->karyawan->nama_lengkap ?? '-' }}</span>
                                    @if($detail->karyawan?->tanggal_berhenti)<span class="ml-1 rounded bg-red-100 px-2 py-1 text-red-800">Sudah Berhenti</span>@endif
                                    @if($detail->tipe_karyawan === 'App\Models\KaryawanTidakTetap')<span class="ml-1 rounded bg-amber-100 px-2 py-1 text-amber-800">Tidak Tetap</span>@endif
                                    <div class="mt-1 text-gray-500">NIK: {{ $detail->karyawan?->nik ?? '-' }}</div>
                                </td>
                                <td class="border border-gray-200 p-3"><div>{{ $detail->karyawan?->group_jkn ?: '-' }}</div><div>{{ $detail->karyawan?->group_bp_jamsostek ?: '-' }}</div></td>
                                <td class="border border-gray-200 p-3">{{ $detail->karyawan?->tipe_jkn ?: '-' }}</td>
                                <td class="border border-gray-200 p-3">{{ $detail->karyawan?->tipe_bp_jamsostek ?: '-' }}</td>
                                @foreach($componentGroups as $columns)
                                    @foreach($columns as $field => $label)
                                        @if($field === 'no_voucher')
                                            <td class="border border-gray-200 p-3">{{ $detail->no_voucher ?: '-' }}</td>
                                        @else
                                            <td class="border border-gray-200 p-3 text-right font-mono {{ $field === 'total_jkn' ? 'bpjs-group-total-jkn font-bold' : ($field === 'total_ppu' ? 'bpjs-group-total-ppu font-bold' : '') }}">Rp {{ number_format($amount($detail, $field), 2, ',', '.') }}</td>
                                        @endif
                                    @endforeach
                                @endforeach
                                <td class="border border-gray-200 p-3 text-right font-bold text-teal-800">Rp {{ number_format($detail->total, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="5" class="border border-gray-200 p-3">Total {{ $pranota_bpj->total_karyawan }} Karyawan</td>
                            @foreach($componentGroups as $columns)
                                @foreach($columns as $field => $label)<td class="border border-gray-200 p-3 text-right font-mono {{ $field === 'total_jkn' ? 'bpjs-group-total-jkn' : ($field === 'total_ppu' ? 'bpjs-group-total-ppu' : '') }}">{{ $field === 'no_voucher' ? '-' : 'Rp ' . number_format($groupTotal([$field]), 2, ',', '.') }}</td>@endforeach
                            @endforeach
                            <td class="border border-gray-200 p-3 text-right text-teal-800">Rp {{ number_format($pranota_bpj->grand_total, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @include('pranota-bpjs.partials.allowance-summary', ['allowanceSummary' => ['ppu' => $groupTotal(['allowance_ppu']), 'bpu' => $groupTotal(['allowance_bpu']), 'jkn' => $groupTotal(['allowance_jkn']), 'kary_jht' => $groupTotal(['allowance_kary_jht']), 'kary_jkn' => $groupTotal(['allowance_kary_jkn']), 'pt' => $groupTotal(['allowance_pt']), 'non_kary' => $groupTotal(['allowance_non_kary'])]])
        <div class="flex justify-end mt-16 print:mt-10">
            <div class="text-center w-48">
                <p class="mb-16 text-sm text-gray-600">Dibuat Oleh,</p>
                <p class="font-bold border-b border-gray-400 pb-1">{{ $pranota_bpj->createdBy->name ?? '____________________' }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $pranota_bpj->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    @page { size: A3 landscape; margin: 8mm; }
    .bpjs-detail-scroll { overflow: visible !important; }
    .bpjs-detail-table { font-size: 7px; white-space: normal; }
    .bpjs-detail-table th, .bpjs-detail-table td { padding: 3px; min-width: 0 !important; }
    .bpjs-detail-table tr { break-inside: avoid; }
    .bpjs-detail-table thead { display: table-header-group; }
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .print\:shadow-none { box-shadow: none !important; }
    .print\:border-none { border: none !important; }
    .print\:m-0 { margin: 0 !important; }
    .print\:p-0 { padding: 0 !important; }
}
</style>
@endsection
