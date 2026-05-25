@extends('layouts.app')

@section('title', 'Detail Pranota Perbaikan Kontainer')
@section('page_title', 'Detail Pranota Perbaikan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        {{-- Breadcrumb & Actions --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <nav class="flex text-sm text-gray-500 mb-1">
                    <a href="{{ route('pranota-perbaikan-kontainer.index') }}" class="hover:text-blue-600 transition-colors">Pranota Perbaikan</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-800 font-medium">Detail</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-800">Nomor: {{ $pranota->nomor_pranota }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pranota-perbaikan-kontainer.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                @can('pranota-perbaikan-kontainer-print')
                <a href="{{ route('pranota-perbaikan-kontainer.print', $pranota->id) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm">
                    <i class="fas fa-print mr-2"></i> Cetak Pranota
                </a>
                @endcan
            </div>
        </div>

        {{-- Details Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Informasi Pranota</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Pranota</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Vendor/Bengkel</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $pranota->vendor ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Bank / Rekening</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        @if($pranota->bank || $pranota->rekening)
                            {{ $pranota->bank }} - {{ $pranota->rekening }} 
                            @if($pranota->penerima)
                                (a.n. {{ $pranota->penerima }})
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</label>
                    <div class="mt-1">
                        @php
                            $badgeColor = match($pranota->status) {
                                'draft' => 'bg-gray-100 text-gray-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'paid' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            $statusLabel = match($pranota->status) {
                                'draft' => 'Draft',
                                'approved' => 'Disetujui',
                                'paid' => 'Lunas',
                                'cancelled' => 'Batal',
                                default => ucfirst($pranota->status)
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Dibuat Oleh</label>
                    <p class="mt-1 text-sm font-medium text-gray-950">
                        {{ $pranota->creator->name ?? 'System' }}
                        <span class="text-xs text-gray-500 font-normal">({{ $pranota->created_at->format('d/m/Y H:i') }})</span>
                    </p>
                </div>
            </div>
            @if($pranota->keterangan)
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Keterangan</label>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-200/50 italic">{{ $pranota->keterangan }}</p>
                </div>
            @endif
        </div>

        {{-- Items Table Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Daftar Item Perbaikan Kontainer</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500">
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-center">No</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Perbaikan</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Ukuran & Tipe</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Bengkel</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Keterangan Kerusakan</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-right">Estimasi Biaya</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-right">Biaya Riil</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-right">Biaya Terpakai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150">
                        @php $subtotal = 0; @endphp
                        @forelse($pranota->items ?? [] as $index => $item)
                            @php
                                $biayaRiil = floatval($item['biaya_riil'] ?? 0);
                                $estimasi = floatval($item['estimasi_biaya'] ?? 0);
                                $biayaTerpakai = ($biayaRiil > 0) ? $biayaRiil : $estimasi;
                                $subtotal += $biayaTerpakai;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $item['no_perbaikan'] ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $item['no_kontainer'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    @if(!empty($item['ukuran']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $item['ukuran'] }}FT
                                        </span>
                                    @endif
                                    @if(!empty($item['tipe']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-800 ml-1">
                                            {{ $item['tipe'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $item['bengkel'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 max-w-xs break-words">{{ $item['keterangan_kerusakan'] ?? (\App\Models\PerbaikanKontainer::find($item['id'] ?? null)->keterangan_kerusakan ?? '-') }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($estimasi, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-900">
                                    @if($biayaRiil > 0)
                                        Rp {{ number_format($biayaRiil, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-indigo-600 font-semibold">Rp {{ number_format($biayaTerpakai, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500 font-medium">Tidak ada item perbaikan kontainer terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Summary details --}}
            <div class="mt-6 border-t border-gray-150 pt-4 flex flex-col items-end gap-2 text-sm">
                <div class="flex justify-between w-64 text-gray-600">
                    <span>Subtotal Biaya:</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between w-64 text-gray-600">
                    <span>Adjustment:</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between w-64 text-base font-bold text-gray-900 border-t border-gray-100 pt-2">
                    <span>Total Keseluruhan:</span>
                    <span class="text-indigo-600">Rp {{ number_format($subtotal + $pranota->adjustment, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
