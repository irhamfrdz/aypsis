@extends('layouts.app')

@section('title', 'Detail Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('master-kapal.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-ship mr-2"></i>
                    Master Kapal
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail Kapal</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Kapal</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap tentang kapal <span class="font-semibold">{{ $masterKapal->nama_kapal }}</span></p>
            </div>
            <div class="flex items-center space-x-2">
                @can('master-kapal.edit')
                <a href="{{ route('master-kapal.edit', $masterKapal->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @endcan
                <a href="{{ route('master-kapal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Informasi Kapal</h2>
            @if($masterKapal->status == 'aktif')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-circle text-green-500 text-xs mr-2"></i> Aktif
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                    <i class="fas fa-circle text-gray-500 text-xs mr-2"></i> Nonaktif
                </span>
            @endif
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">ID</dt>
                        <dd class="flex-1 text-sm text-gray-900">{{ $masterKapal->id }}</dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Kode</dt>
                        <dd class="flex-1">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $masterKapal->kode }}
                            </span>
                        </dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Kode Kapal</dt>
                        <dd class="flex-1 text-sm text-gray-900">{{ $masterKapal->kode_kapal ?? '-' }}</dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Nama Kapal</dt>
                        <dd class="flex-1 text-sm font-semibold text-gray-900">{{ $masterKapal->nama_kapal }}</dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Nickname</dt>
                        <dd class="flex-1 text-sm text-gray-900">
                            @if($masterKapal->nickname)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $masterKapal->nickname }}
                                </span>
                            @else
                                -
                            @endif
                        </dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Pelayaran (Pemilik)</dt>
                        <dd class="flex-1 text-sm text-gray-900">
                            @if($masterKapal->pelayaran)
                                <i class="fas fa-ship text-blue-500 mr-1"></i> {{ $masterKapal->pelayaran }}
                            @else
                                -
                            @endif
                        </dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Kapasitas Palka</dt>
                        <dd class="flex-1 text-sm text-gray-900">
                            @if($masterKapal->kapasitas_kontainer_palka)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-boxes mr-1"></i>
                                    {{ number_format($masterKapal->kapasitas_kontainer_palka) }}
                                </span>
                            @else
                                -
                            @endif
                        </dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Kapasitas Deck</dt>
                        <dd class="flex-1 text-sm text-gray-900">
                            @if($masterKapal->kapasitas_kontainer_deck)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    {{ number_format($masterKapal->kapasitas_kontainer_deck) }}
                                </span>
                            @else
                                -
                            @endif
                        </dd>
                    </div>

                    <div class="flex pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Gross Tonnage</dt>
                        <dd class="flex-1 text-sm text-gray-900">
                            @if($masterKapal->gross_tonnage)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-weight-hanging mr-1"></i>
                                    {{ number_format($masterKapal->gross_tonnage, 2) }}
                                </span>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Status</dt>
                        <dd class="flex-1">
                            @if($masterKapal->status == 'aktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Nonaktif
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Dibuat Tanggal</dt>
                        <dd class="flex-1 text-sm text-gray-900">
                            <i class="fas fa-calendar-plus text-gray-400 mr-1"></i>
                            {{ $masterKapal->created_at->format('d/m/Y H:i') }}
                        </dd>
                    </div>

                    <div class="flex border-b border-gray-100 pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Diperbarui Tanggal</dt>
                        <dd class="flex-1 text-sm text-gray-900">
                            <i class="fas fa-calendar-check text-gray-400 mr-1"></i>
                            {{ $masterKapal->updated_at->format('d/m/Y H:i') }}
                        </dd>
                    </div>

                    @if($masterKapal->deleted_at)
                    <div class="flex pb-3">
                        <dt class="w-40 text-sm font-medium text-gray-500">Dihapus Tanggal</dt>
                        <dd class="flex-1 text-sm text-red-600">
                            <i class="fas fa-calendar-times text-red-400 mr-1"></i>
                            {{ $masterKapal->deleted_at->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Capacity Summary Section -->
            @if($masterKapal->kapasitas_kontainer_palka || $masterKapal->kapasitas_kontainer_deck || $masterKapal->gross_tonnage)
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                    Kapasitas & Spesifikasi Teknis
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Kapasitas Palka -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-600">Kapasitas Palka</p>
                                <p class="text-2xl font-bold text-blue-900">
                                    {{ $masterKapal->kapasitas_kontainer_palka ? number_format($masterKapal->kapasitas_kontainer_palka) : '0' }}
                                </p>

                            </div>
                            <div class="bg-blue-100 rounded-full p-3">
                                <i class="fas fa-boxes text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Kapasitas Deck -->
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-600">Kapasitas Deck</p>
                                <p class="text-2xl font-bold text-green-900">
                                    {{ $masterKapal->kapasitas_kontainer_deck ? number_format($masterKapal->kapasitas_kontainer_deck) : '0' }}
                                </p>

                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <i class="fas fa-layer-group text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Kapasitas -->
                    <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-600">Total Kapasitas</p>
                                <p class="text-2xl font-bold text-indigo-900">
                                    {{ number_format($masterKapal->total_kapasitas_kontainer) }}
                                </p>

                            </div>
                            <div class="bg-indigo-100 rounded-full p-3">
                                <i class="fas fa-calculator text-indigo-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Gross Tonnage -->
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-600">Gross Tonnage</p>
                                <p class="text-2xl font-bold text-yellow-900">
                                    {{ $masterKapal->gross_tonnage ? number_format($masterKapal->gross_tonnage, 2) : '0' }}
                                </p>

                            </div>
                            <div class="bg-yellow-100 rounded-full p-3">
                                <i class="fas fa-weight-hanging text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                @if($masterKapal->kapasitas_kontainer_palka && $masterKapal->kapasitas_kontainer_deck)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Distribusi Kapasitas Kontainer</h4>
                    @php
                        $totalKapasitas = $masterKapal->total_kapasitas_kontainer;
                        $persentasePalka = $totalKapasitas > 0 ? ($masterKapal->kapasitas_kontainer_palka / $totalKapasitas) * 100 : 0;
                        $persentaseDeck = $totalKapasitas > 0 ? ($masterKapal->kapasitas_kontainer_deck / $totalKapasitas) * 100 : 0;
                    @endphp
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>Palka ({{ number_format($persentasePalka, 1) }}%)</span>
                                <span>Deck ({{ number_format($persentaseDeck, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="h-full flex">
                                    <div class="bg-blue-500 transition-all duration-300" style="width: {{ $persentasePalka }}%"></div>
                                    <div class="bg-green-500 transition-all duration-300" style="width: {{ $persentaseDeck }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Catatan Section -->
            @if($masterKapal->catatan)
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-3">
                    <i class="fas fa-sticky-note text-gray-400 mr-2"></i>Catatan
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $masterKapal->catatan }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Danger Zone -->
    @can('master-kapal.delete')
    <div class="bg-white rounded-lg shadow-sm border-2 border-red-200 overflow-hidden">
        <div class="bg-red-50 px-6 py-4 border-b border-red-200">
            <h2 class="text-lg font-semibold text-red-900 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Danger Zone
            </h2>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 mr-6">
                    <h3 class="text-base font-medium text-gray-900 mb-1">Hapus Kapal</h3>
                    <p class="text-sm text-gray-600">
                        Setelah dihapus, data kapal ini akan dipindahkan ke tempat sampah dan dapat dipulihkan.
                        Tindakan ini akan mempengaruhi relasi data yang terkait.
                    </p>
                </div>
                <form action="{{ route('master-kapal.destroy', $masterKapal->id) }}"
                      method="POST"
                      onsubmit="return confirm('⚠️ PERINGATAN!\n\nApakah Anda yakin ingin menghapus kapal ini?\n\nKode: {{ $masterKapal->kode }}\nNama: {{ $masterKapal->nama_kapal }}\n\nData akan dipindahkan ke tempat sampah.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center whitespace-nowrap">
                        <i class="fas fa-trash mr-2"></i> Hapus Kapal
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection

