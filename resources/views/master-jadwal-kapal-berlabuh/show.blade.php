@extends('layouts.app')

@section('title', 'Detail Jadwal Kapal Berlabuh')
@section('page_title', 'Detail Jadwal Kapal Berlabuh')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-4xl">
    <!-- Breadcrumb & Top Actions -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('master-jadwal-kapal-berlabuh.index') }}" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Detail Jadwal Kapal Berlabuh</h1>
                <p class="text-xs sm:text-sm text-gray-500">{{ $jadwal->nama_kapal }} &bull; Rute: {{ $jadwal->pelabuhan }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('master-jadwal-kapal-berlabuh-update')
                <a href="{{ route('master-jadwal-kapal-berlabuh.edit', $jadwal->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
            @endcan
        </div>
    </div>

    <!-- Main Card Display -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <!-- Banner Pelabuhan / Header -->
        <div class="bg-sky-500 text-white p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <p class="text-xs font-semibold text-sky-100 uppercase tracking-wider">Pelabuhan / Rute Tujuan</p>
                <h2 class="text-2xl font-black tracking-wide mt-0.5">{{ strtoupper($jadwal->pelabuhan) }}</h2>
            </div>
            <div>
                {!! $jadwal->status_badge !!}
            </div>
        </div>

        <div class="p-6">
            <!-- Timeline Cards (Close, ETD, ETA) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <!-- Close Card -->
                <div class="bg-sky-50 rounded-xl p-4 border border-sky-100 text-center">
                    <p class="text-xs font-bold uppercase text-sky-800 tracking-wider">Tanggal Close</p>
                    <p class="text-xl font-extrabold text-sky-950 mt-1">
                        {{ $jadwal->tanggal_closing ? \Carbon\Carbon::parse($jadwal->tanggal_closing)->format('d-M-Y') : '-' }}
                    </p>
                    <p class="text-[11px] text-sky-600 mt-1">Batas Penerimaan Muatan</p>
                </div>

                <!-- ETD Card (Yellow) -->
                <div class="bg-amber-100/70 rounded-xl p-4 border border-amber-300 text-center">
                    <p class="text-xs font-bold uppercase text-amber-950 tracking-wider">ETD (Keberangkatan)</p>
                    <p class="text-xl font-extrabold text-amber-950 mt-1">
                        {{ $jadwal->tanggal_etd ? \Carbon\Carbon::parse($jadwal->tanggal_etd)->format('d-M-Y') : '-' }}
                    </p>
                    <p class="text-[11px] text-amber-800 mt-1">Estimated Time of Departure</p>
                </div>

                <!-- ETA Card (Red) -->
                <div class="bg-rose-600 text-white rounded-xl p-4 border border-rose-700 text-center shadow-sm">
                    <p class="text-xs font-bold uppercase text-rose-100 tracking-wider">ETA (Tiba / Labuh)</p>
                    <p class="text-xl font-black text-white mt-1">
                        {{ $jadwal->tanggal_eta ? \Carbon\Carbon::parse($jadwal->tanggal_eta)->format('d-M-Y') : '-' }}
                    </p>
                    <p class="text-[11px] text-rose-100 mt-1">Estimated Time of Arrival</p>
                </div>
            </div>

            <!-- Detail Information Table -->
            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Informasi Lengkap</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4 text-xs sm:text-sm">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <dt class="text-gray-500 font-medium">Nama Kapal</dt>
                        <dd class="text-gray-900 font-bold text-base mt-0.5">{{ $jadwal->nama_kapal }}</dd>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <dt class="text-gray-500 font-medium">Nomor Voyage</dt>
                        <dd class="text-gray-900 font-semibold mt-0.5">{{ $jadwal->no_voyage ?? '-' }}</dd>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <dt class="text-gray-500 font-medium">Data Kapal Terkait</dt>
                        <dd class="text-gray-900 font-medium mt-0.5">
                            @if($jadwal->kapal)
                                <a href="{{ route('master-kapal.show', $jadwal->kapal->id) }}" class="text-blue-600 hover:underline">
                                    {{ $jadwal->kapal->nama_kapal }} (Kode: {{ $jadwal->kapal->kode }})
                                </a>
                            @else
                                <span class="text-gray-400">Tidak ditautkan ke Master Kapal</span>
                            @endif
                        </dd>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <dt class="text-gray-500 font-medium">Data Pelabuhan Terkait</dt>
                        <dd class="text-gray-900 font-medium mt-0.5">
                            @if($jadwal->pelabuhanRelation)
                                <a href="{{ route('master-pelabuhan.show', $jadwal->pelabuhanRelation->id) }}" class="text-blue-600 hover:underline">
                                    {{ $jadwal->pelabuhanRelation->nama_pelabuhan }} ({{ $jadwal->pelabuhanRelation->kota }})
                                </a>
                            @else
                                <span class="text-gray-400">Tidak ditautkan ke Master Pelabuhan</span>
                            @endif
                        </dd>
                    </div>

                    <div class="sm:col-span-2 bg-gray-50 p-3 rounded-lg">
                        <dt class="text-gray-500 font-medium">Keterangan / Catatan</dt>
                        <dd class="text-gray-900 font-normal mt-0.5 whitespace-pre-line">{{ $jadwal->keterangan ?? 'Tidak ada keterangan tambahan.' }}</dd>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <dt class="text-gray-500 font-medium">Dibuat Pada</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $jadwal->created_at ? $jadwal->created_at->format('d-M-Y H:i:s') : '-' }}</dd>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <dt class="text-gray-500 font-medium">Terakhir Diperbarui</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $jadwal->updated_at ? $jadwal->updated_at->format('d-M-Y H:i:s') : '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
