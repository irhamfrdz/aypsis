@extends('layouts.app')

@section('title', 'Detail Gate In')
@section('page_title', 'Detail Gate In')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">{{ $gateIn->nomor_gate_in }}</h1>
                            <p class="text-sm text-gray-500 mt-1">{{ $gateIn->tanggal_formatted }} - {{ $gateIn->suratJalans->count() }} kontainer</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $gateIn->status === 'aktif' ? 'bg-green-100 text-green-800' :
                               ($gateIn->status === 'selesai' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            <span class="w-2 h-2 mr-2 rounded-full
                                {{ $gateIn->status === 'aktif' ? 'bg-green-400' :
                                   ($gateIn->status === 'selesai' ? 'bg-blue-400' : 'bg-red-400') }}"></span>
                            {{ ucfirst($gateIn->status) }}
                        </span>

                        <div class="flex items-center space-x-2">
                            @can('gate-in.edit')
                                <a href="{{ route('gate-in.edit', $gateIn) }}"
                                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                            @endcan

                            <a href="{{ route('gate-in.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="px-6 py-4 bg-gray-50">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $gateIn->suratJalans->count() }}</div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kontainer</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $gateIn->suratJalans->where('status_gate_in', 'selesai')->count() }}</div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sudah Gate In</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $gateIn->suratJalans->where('status_gate_in', 'proses')->count() }}</div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dalam Proses</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-600">{{ $gateIn->suratJalans->where('status_gate_in', 'pending')->count() }}</div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Gate In Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Gate In</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Gate In</label>
                                    <p class="text-sm text-gray-900 font-medium">{{ $gateIn->nomor_gate_in }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal</label>
                                    <p class="text-sm text-gray-900">{{ $gateIn->tanggal_formatted }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Terminal</label>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $gateIn->terminal->nama_terminal }}</p>
                                            <p class="text-xs text-gray-500">{{ $gateIn->terminal->kode_terminal }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Kapal</label>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $gateIn->kapal->nama_kapal }}</p>
                                            @if($gateIn->kapal->kode_kapal)
                                                <p class="text-xs text-gray-500">{{ $gateIn->kapal->kode_kapal }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Service</label>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $gateIn->service->nama_service }}</p>
                                            <p class="text-xs text-gray-500">{{ $gateIn->service->kode_service }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $gateIn->status === 'aktif' ? 'bg-green-100 text-green-800' :
                                           ($gateIn->status === 'selesai' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($gateIn->status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Waktu Masuk</label>
                                    <p class="text-sm text-gray-900">{{ $gateIn->waktu_masuk ? $gateIn->waktu_masuk->format('d/m/Y H:i') : '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Waktu Keluar</label>
                                    <p class="text-sm text-gray-900">{{ $gateIn->waktu_keluar ? $gateIn->waktu_keluar->format('d/m/Y H:i') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        @if($gateIn->keterangan)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Keterangan</label>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $gateIn->keterangan }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kontainer List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Kontainer ({{ $gateIn->suratJalans->count() }})</h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Filter:</span>
                                <select id="status-filter" class="text-sm border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="proses">Dalam Proses</option>
                                    <option value="selesai">Sudah Gate In</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        @if($gateIn->suratJalans->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="kontainer-grid">
                                @foreach($gateIn->suratJalans as $suratJalan)
                                <div class="kontainer-item border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors duration-200"
                                     data-status="{{ $suratJalan->status_gate_in ?? 'pending' }}">>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $suratJalan->no_kontainer ?: 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ $suratJalan->size ?: '-' }}ft | {{ $suratJalan->tipe_kontainer ?: '-' }}</div>
                                                <div class="text-xs text-blue-600 mt-1">Surat Jalan: {{ $suratJalan->no_surat_jalan }}</div>
                                                @if($suratJalan->supir)
                                                    <div class="text-xs text-gray-600">Supir: {{ $suratJalan->supir }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end space-y-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ ($suratJalan->status_gate_in ?? 'pending') === 'selesai' ? 'bg-green-100 text-green-800' :
                                                   (($suratJalan->status_gate_in ?? 'pending') === 'proses' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $suratJalan->status_gate_in === 'selesai' ? 'Selesai' : ($suratJalan->status_gate_in === 'proses' ? 'Proses' : 'Pending') }}
                                            </span>
                                            @if($suratJalan->status)
                                                <span class="text-xs text-gray-500">Status SJ: {{ ucfirst($suratJalan->status) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($suratJalan->tanggal_gate_in)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <div class="text-xs">
                                                <div>
                                                    <span class="text-gray-500">Gate In:</span>
                                                    <span class="text-gray-900 ml-1">{{ \Carbon\Carbon::parse($suratJalan->tanggal_gate_in)->format('d/m H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-2">Belum ada kontainer</h3>
                                <p class="text-sm text-gray-500">Kontainer belum ditambahkan ke gate in ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Timeline</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Gate In dibuat <span class="font-medium text-gray-900">{{ $gateIn->nomor_gate_in }}</span></p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $gateIn->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @if($gateIn->waktu_masuk)
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Waktu masuk tercatat</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $gateIn->waktu_masuk->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($gateIn->waktu_keluar)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Waktu keluar tercatat</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $gateIn->waktu_keluar->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistik</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Durasi Proses</span>
                                <span class="text-sm font-medium text-gray-900">
                                    @if($gateIn->waktu_masuk && $gateIn->waktu_keluar)
                                        {{ $gateIn->waktu_masuk->diffForHumans($gateIn->waktu_keluar, true) }}
                                    @elseif($gateIn->waktu_masuk)
                                        {{ $gateIn->waktu_masuk->diffForHumans() }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Total Kontainer</span>
                                <span class="text-sm font-medium text-gray-900">{{ $gateIn->kontainers->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Progress</span>
                                <span class="text-sm font-medium text-gray-900">
                                    @if($gateIn->kontainers->count() > 0)
                                        {{ number_format(($gateIn->kontainers->where('status_gate_in', 'keluar')->count() / $gateIn->kontainers->count()) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            @if($gateIn->kontainers->count() > 0)
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full"
                                         style="width: {{ ($gateIn->kontainers->where('status_gate_in', 'keluar')->count() / $gateIn->kontainers->count()) * 100 }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @can('gate-in.edit')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            @if($gateIn->status === 'aktif')
                                <form action="{{ route('gate-in.update-status', $gateIn) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="selesai">
                                    <button type="submit"
                                            onclick="return confirm('Apakah Anda yakin ingin menyelesaikan gate in ini?')"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Selesaikan Gate In
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('gate-in.edit', $gateIn) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-purple-300 text-purple-700 hover:bg-purple-50 text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Gate In
                            </a>

                            @can('gate-in.delete')
                                @if($gateIn->status !== 'selesai')
                                    <form action="{{ route('gate-in.destroy', $gateIn) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus gate in ini? Tindakan ini tidak dapat dibatalkan.')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-red-700 hover:bg-red-50 text-sm font-medium rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus Gate In
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status filter functionality
    const statusFilter = document.getElementById('status-filter');
    const kontainerItems = document.querySelectorAll('.kontainer-item');

    statusFilter.addEventListener('change', function() {
        const selectedStatus = this.value;

        kontainerItems.forEach(item => {
            const itemStatus = item.dataset.status;

            if (selectedStatus === '' || itemStatus === selectedStatus) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
