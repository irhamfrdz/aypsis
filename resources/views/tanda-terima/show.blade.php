@extends('layouts.app')

@section('title', 'Detail Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('tanda-terima.index') }}" class="hover:text-blue-600 transition">Tanda Terima</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Detail</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">Detail Tanda Terima</h1>
                    @if(!$tandaTerima->surat_jalan_id)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-hand-paper text-xs mr-1"></i> Input Manual
                        </span>
                    @endif
                    @php
                        $sudahMasukBl = false;
                        try {
                            $sudahMasukBl = method_exists($tandaTerima, 'sudahMasukBl') ? $tandaTerima->sudahMasukBl() : false;
                        } catch (Exception $e) {
                            // Handle error gracefully
                        }
                    @endphp
                    @if($sudahMasukBl)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-ship text-xs mr-1"></i> Sudah Masuk BL
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-hourglass-half text-xs mr-1"></i> Belum Masuk BL
                        </span>
                    @endif
                </div>
                <p class="text-gray-600 mt-1">No. Surat Jalan: <span class="font-semibold">{{ $tandaTerima->no_surat_jalan }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                @if($tandaTerima->status == 'draft')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-circle text-xs mr-2"></i> Draft
                    </span>
                @elseif($tandaTerima->status == 'submitted')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-paper-plane text-xs mr-2"></i> Submitted
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle text-xs mr-2"></i> Completed
                    </span>
                @endif
                <a href="{{ route('tanda-terima.edit', $tandaTerima->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (Left - 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Surat Jalan -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Data Surat Jalan</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Nomor Tanda Terima</dt>
                            <dd class="text-sm text-gray-900 font-semibold">
                                <code class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded">{{ $tandaTerima->nomor_tanda_terima ?: '-' }}</code>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">No. Surat Jalan</dt>
                            <dd class="text-sm text-gray-900 font-semibold">{{ $tandaTerima->no_surat_jalan }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Tanggal</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->tanggal_surat_jalan?->format('d F Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Supir</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->supir ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Kenek</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->kenek ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Krani</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->krani ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Supir Pengganti</dt>
                            <dd class="text-sm text-gray-900">
                                @if($tandaTerima->supir_pengganti)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ $tandaTerima->supir_pengganti }}
                                    </span>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Kenek Pengganti</dt>
                            <dd class="text-sm text-gray-900">
                                @if($tandaTerima->kenek_pengganti)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ $tandaTerima->kenek_pengganti }}
                                    </span>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Jenis Barang</dt>
                            <dd class="text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $tandaTerima->jenis_barang ?: '-' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Kegiatan</dt>
                            <dd class="text-sm text-gray-900">
                                @php
                                    $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $tandaTerima->kegiatan)
                                                    ->value('nama_kegiatan') ?? $tandaTerima->kegiatan;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $kegiatanName }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Tujuan</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->tujuan_pengiriman ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Pengirim</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->pengirim ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Penerima</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->penerima ?: '-' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Alamat Penerima</dt>
                            <dd class="text-sm text-gray-900 bg-gray-50 p-2 rounded">{{ $tandaTerima->alamat_penerima ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Data Kendaraan & Crew -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Data Kendaraan & Crew</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">No. Polisi</dt>
                            <dd class="text-sm text-gray-900">
                                <code class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded font-mono">{{ $tandaTerima->no_polisi ?: '-' }}</code>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Jenis Kendaraan</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->jenis_kendaraan ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Supir</dt>
                            <dd class="text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                    <span class="font-semibold">{{ $tandaTerima->supir ?: '-' }}</span>
                                </div>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Kenek</dt>
                            <dd class="text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-friends text-green-600 text-xs"></i>
                                    <span class="font-semibold">{{ $tandaTerima->kenek ?: '-' }}</span>
                                </div>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Krani</dt>
                            <dd class="text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-hard-hat text-orange-600 text-xs"></i>
                                    <span class="font-semibold">{{ $tandaTerima->krani ?: '-' }}</span>
                                </div>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Krane</dt>
                            <dd class="text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tools text-purple-600 text-xs"></i>
                                    <span class="font-semibold">{{ $tandaTerima->krane ?: '-' }}</span>
                                </div>
                            </dd>
                        </div>
                        
                        @if($tandaTerima->supir_pengganti || $tandaTerima->kenek_pengganti)
                        <div class="col-span-2 mt-4 pt-4 border-t border-gray-200">
                            <dt class="text-xs font-medium text-orange-600 uppercase mb-3">
                                <i class="fas fa-exchange-alt mr-1"></i> Crew Pengganti
                            </dt>
                            <dd class="text-sm text-gray-900">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($tandaTerima->supir_pengganti)
                                    <div class="bg-orange-50 border border-orange-200 p-3 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-user text-orange-600 text-sm"></i>
                                            <div>
                                                <div class="text-xs text-orange-600 font-medium">Supir Pengganti</div>
                                                <div class="font-semibold text-gray-900">{{ $tandaTerima->supir_pengganti }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($tandaTerima->kenek_pengganti)
                                    <div class="bg-orange-50 border border-orange-200 p-3 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-user-friends text-orange-600 text-sm"></i>
                                            <div>
                                                <div class="text-xs text-orange-600 font-medium">Kenek Pengganti</div>
                                                <div class="font-semibold text-gray-900">{{ $tandaTerima->kenek_pengganti }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Data Kontainer -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Data Kontainer</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">No. Kontainer</dt>
                            <dd class="text-sm text-gray-900">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $tandaTerima->no_kontainer ?: '-' }}</code>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">No. Seal</dt>
                            <dd class="text-sm text-gray-900">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $tandaTerima->no_seal ?: '-' }}</code>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Size</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->size ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Jumlah Kontainer</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->jumlah_kontainer ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Data Tambahan -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Data Tambahan</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Estimasi Nama Kapal</dt>
                            <dd class="text-sm text-gray-900 font-semibold">{{ $tandaTerima->estimasi_nama_kapal ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Tanggal Ambil Kontainer</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->tanggal_ambil_kontainer?->format('d F Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Tanggal Terima Pelabuhan</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->tanggal_terima_pelabuhan?->format('d F Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Tanggal Garasi</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->tanggal_garasi?->format('d F Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Tanggal Checkpoint Supir</dt>
                            <dd class="text-sm text-gray-900">
                                @if($tandaTerima->tanggal_checkpoint_supir)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-green-600 text-xs"></i>
                                        <span class="font-semibold text-green-800">{{ $tandaTerima->tanggal_checkpoint_supir->format('d F Y') }}</span>
                                    </div>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Jumlah</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->jumlah ? number_format($tandaTerima->jumlah, 0, ',', '.') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Satuan</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->satuan ?: '-' }}</dd>
                        </div>

                        <!-- Dimensi Items Information -->
                        @php
                            // Try to get dimensi data from either dimensi_items or dimensi_details
                            $dimensiItems = [];
                            
                            if ($tandaTerima->dimensi_items) {
                                $dimensiItems = is_string($tandaTerima->dimensi_items) ? json_decode($tandaTerima->dimensi_items, true) : $tandaTerima->dimensi_items;
                            } elseif ($tandaTerima->dimensi_details) {
                                $dimensiItems = is_string($tandaTerima->dimensi_details) ? json_decode($tandaTerima->dimensi_details, true) : $tandaTerima->dimensi_details;
                            }
                            
                            $totalVolume = 0;
                            $totalTonase = 0;
                            $totalJumlah = 0;
                        @endphp
                        
                        @if(is_array($dimensiItems) && count($dimensiItems) > 0)
                            <div class="col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase mb-3">Detail Barang & Dimensi</dt>
                                <dd class="text-sm text-gray-900">
                                    <div class="space-y-3">
                                        @foreach($dimensiItems as $index => $item)
                                            @php
                                                $totalVolume += $item['meter_kubik'] ?? 0;
                                                $totalTonase += $item['tonase'] ?? 0;
                                                $totalJumlah += $item['jumlah'] ?? 0;
                                            @endphp
                                            <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg">
                                                <div class="flex items-start justify-between mb-3">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                                            {{ $index + 1 }}
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-gray-900">
                                                                {{ $item['nama_barang'] ?? 'Barang ' . ($index + 1) }}
                                                            </div>
                                                            @if(isset($item['jumlah']) || isset($item['satuan']))
                                                            <div class="text-xs text-gray-600 mt-1">
                                                                @if(isset($item['jumlah']))
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-medium">
                                                                        {{ number_format($item['jumlah'], 0, ',', '.') }} {{ $item['satuan'] ?? 'Unit' }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                                    @if(isset($item['panjang']) && $item['panjang'] > 0)
                                                        <div class="bg-white p-2 rounded">
                                                            <div class="text-xs text-gray-500">Panjang</div>
                                                            <div class="text-sm font-semibold text-gray-900">{{ rtrim(rtrim(number_format($item['panjang'], 3, '.', ''), '0'), '.') }} m</div>
                                                        </div>
                                                    @endif
                                                    @if(isset($item['lebar']) && $item['lebar'] > 0)
                                                        <div class="bg-white p-2 rounded">
                                                            <div class="text-xs text-gray-500">Lebar</div>
                                                            <div class="text-sm font-semibold text-gray-900">{{ rtrim(rtrim(number_format($item['lebar'], 3, '.', ''), '0'), '.') }} m</div>
                                                        </div>
                                                    @endif
                                                    @if(isset($item['tinggi']) && $item['tinggi'] > 0)
                                                        <div class="bg-white p-2 rounded">
                                                            <div class="text-xs text-gray-500">Tinggi</div>
                                                            <div class="text-sm font-semibold text-gray-900">{{ rtrim(rtrim(number_format($item['tinggi'], 3, '.', ''), '0'), '.') }} m</div>
                                                        </div>
                                                    @endif
                                                    @if(isset($item['meter_kubik']) && $item['meter_kubik'] > 0)
                                                        <div class="bg-white p-2 rounded border-l-2 border-blue-500">
                                                            <div class="text-xs text-gray-500">Volume (m³)</div>
                                                            <div class="text-sm font-bold text-blue-700">{{ rtrim(rtrim(number_format($item['meter_kubik'], 3, '.', ''), '0'), '.') }} m³</div>
                                                        </div>
                                                    @endif
                                                    @if(isset($item['tonase']) && $item['tonase'] > 0)
                                                        <div class="bg-white p-2 rounded border-l-2 border-green-500">
                                                            <div class="text-xs text-gray-500">Tonase</div>
                                                            <div class="text-sm font-bold text-green-700">{{ rtrim(rtrim(number_format($item['tonase'], 3, '.', ''), '0'), '.') }} Ton</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        @if(count($dimensiItems) > 1 && ($totalVolume > 0 || $totalTonase > 0))
                                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-300 p-4 rounded-lg">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <div class="font-bold text-blue-900 text-base">Total Keseluruhan</div>
                                                    <span class="ml-auto text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full font-medium">{{ count($dimensiItems) }} Items</span>
                                                </div>
                                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                                    @if($totalJumlah > 0)
                                                        <div class="bg-white p-3 rounded-lg shadow-sm">
                                                            <div class="text-xs text-gray-500 mb-1">Total Jumlah</div>
                                                            <div class="text-lg font-bold text-gray-900">{{ number_format($totalJumlah, 0, ',', '.') }}</div>
                                                        </div>
                                                    @endif
                                                    @if($totalVolume > 0)
                                                        <div class="bg-white p-3 rounded-lg shadow-sm">
                                                            <div class="text-xs text-gray-500 mb-1">Total Volume</div>
                                                            <div class="text-lg font-bold text-blue-700">{{ rtrim(rtrim(number_format($totalVolume, 3, '.', ''), '0'), '.') }} m³</div>
                                                        </div>
                                                    @endif
                                                    @if($totalTonase > 0)
                                                        <div class="bg-white p-3 rounded-lg shadow-sm">
                                                            <div class="text-xs text-gray-500 mb-1">Total Tonase</div>
                                                            <div class="text-lg font-bold text-green-700">{{ rtrim(rtrim(number_format($totalTonase, 3, '.', ''), '0'), '.') }} Ton</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </dd>
                            </div>
                        @else
                            <!-- Fallback to legacy single dimension display -->
                            @if($tandaTerima->panjang || $tandaTerima->lebar || $tandaTerima->tinggi)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Dimensi</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($tandaTerima->panjang)
                                            {{ rtrim(rtrim(number_format($tandaTerima->panjang, 3, '.', ''), '0'), '.') }} m
                                        @endif
                                        @if($tandaTerima->lebar)
                                            × {{ rtrim(rtrim(number_format($tandaTerima->lebar, 3, '.', ''), '0'), '.') }} m
                                        @endif
                                        @if($tandaTerima->tinggi)
                                            × {{ rtrim(rtrim(number_format($tandaTerima->tinggi, 3, '.', ''), '0'), '.') }} m
                                        @endif
                                        @if(!$tandaTerima->panjang && !$tandaTerima->lebar && !$tandaTerima->tinggi)
                                            -
                                        @endif
                                    </dd>
                                </div>
                            @endif

                            @if($tandaTerima->meter_kubik)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Volume</dt>
                                    <dd class="text-sm text-gray-900">{{ rtrim(rtrim(number_format($tandaTerima->meter_kubik, 3, '.', ''), '0'), '.') }} m³</dd>
                                </div>
                            @endif

                            @if($tandaTerima->tonase)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Tonase</dt>
                                    <dd class="text-sm text-gray-900">{{ rtrim(rtrim(number_format($tandaTerima->tonase, 3, '.', ''), '0'), '.') }} Ton</dd>
                                </div>
                            @endif
                        @endif
                    </dl>

                    @if($tandaTerima->catatan)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-xs font-medium text-gray-500 uppercase mb-2">Catatan</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $tandaTerima->catatan }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Gambar Checkpoint -->
            @php
                // Debug info
                $debug = [];
                $debug['tanda_terima_gambar'] = $tandaTerima->gambar_checkpoint ? 'Ada' : 'Tidak ada';
                $debug['surat_jalan_gambar'] = ($tandaTerima->suratJalan && $tandaTerima->suratJalan->gambar_checkpoint) ? 'Ada' : 'Tidak ada';
                
                // Try to get images from tanda terima first, then from related surat jalan
                $gambarCheckpoint = null;
                if ($tandaTerima->gambar_checkpoint) {
                    $gambarCheckpoint = $tandaTerima->gambar_checkpoint;
                    $debug['source'] = 'tanda_terima';
                } elseif ($tandaTerima->suratJalan && $tandaTerima->suratJalan->gambar_checkpoint) {
                    $gambarCheckpoint = $tandaTerima->suratJalan->gambar_checkpoint;
                    $debug['source'] = 'surat_jalan';
                } else {
                    $debug['source'] = 'tidak_ada';
                }
            @endphp
            
            @if($gambarCheckpoint)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Gambar Checkpoint</h2>
                        <div class="flex items-center gap-3">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                Sumber: {{ ucfirst(str_replace('_', ' ', $debug['source'])) }}
                            </span>
                            @if($tandaTerima->tanggal_checkpoint_supir)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-camera text-xs mr-1"></i>
                                    {{ $tandaTerima->tanggal_checkpoint_supir->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @php
                        // Check if it's a JSON array (multiple images) or single image path
                        $isJson = is_string($gambarCheckpoint) && (str_starts_with($gambarCheckpoint, '[') || str_starts_with($gambarCheckpoint, '{'));
                        $imagePaths = $isJson ? json_decode($gambarCheckpoint, true) : [$gambarCheckpoint];
                        $imagePaths = is_array($imagePaths) ? array_filter($imagePaths) : [$gambarCheckpoint];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($imagePaths as $index => $imagePath)
                        @php
                            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
                            $isPdf = $extension === 'pdf';
                            $fileUrl = asset('storage/' . $imagePath);
                        @endphp
                        
                        <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                            @if($isPdf)
                                <div class="text-center p-6">
                                    <i class="fas fa-file-pdf text-red-500 text-4xl mb-3"></i>
                                    <p class="text-sm text-gray-600 mb-3">File PDF {{ $index + 1 }}</p>
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition duration-200">
                                            <i class="fas fa-external-link-alt mr-2"></i> Buka
                                        </a>
                                        <a href="{{ $fileUrl }}" download class="inline-flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition duration-200">
                                            <i class="fas fa-download mr-2"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="relative group">
                                    <img src="{{ $fileUrl }}"
                                         alt="Checkpoint Image {{ $index + 1 }}"
                                         class="w-full h-64 object-cover cursor-pointer"
                                         onclick="window.open('{{ $fileUrl }}', '_blank')"
                                         onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-64 bg-gray-100\'><div class=\'text-center text-gray-500\'><i class=\'fas fa-image-slash text-2xl mb-2\'></i><p class=\'text-sm\'>Gambar tidak ditemukan</p><p class=\'text-xs\'>{{ basename($imagePath) }}</p></div></div>'">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 flex items-center justify-center transition-all">
                                        <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-3 flex gap-2">
                                    <a href="{{ $fileUrl }}" target="_blank" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition duration-200">
                                        <i class="fas fa-external-link-alt mr-2"></i> Buka
                                    </a>
                                    <a href="{{ $fileUrl }}" download class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition duration-200">
                                        <i class="fas fa-download mr-2"></i> Download
                                    </a>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    @if(count($imagePaths) === 0)
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-images text-3xl mb-3"></i>
                            <p class="text-sm">Tidak ada gambar checkpoint yang tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Gambar Checkpoint</h2>
                </div>
                <div class="p-6">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-images text-3xl mb-3"></i>
                        <p class="text-sm">Belum ada gambar checkpoint yang diupload</p>
                        <div class="mt-4 text-xs text-gray-400">
                            <p>Debug Info:</p>
                            <p>Tanda Terima: {{ $debug['tanda_terima_gambar'] }}</p>
                            <p>Surat Jalan: {{ $debug['surat_jalan_gambar'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar (Right - 1/3) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('tanda-terima.edit', $tandaTerima->id) }}"
                       class="block w-full text-center px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i> Edit Tanda Terima
                    </a>
                    <a href="{{ route('tanda-terima.index') }}"
                       class="block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <!-- Timeline Aktivitas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">
                    <i class="fas fa-clock text-blue-600 mr-2"></i>Timeline Aktivitas
                </h3>
                <div class="space-y-4">
                    @if($tandaTerima->tanggal_ambil_kontainer)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-box text-blue-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-900">Ambil Kontainer</div>
                            <div class="text-xs text-gray-600">{{ $tandaTerima->tanggal_ambil_kontainer->format('d F Y') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($tandaTerima->tanggal_terima_pelabuhan)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-ship text-green-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-900">Terima di Pelabuhan</div>
                            <div class="text-xs text-gray-600">{{ $tandaTerima->tanggal_terima_pelabuhan->format('d F Y') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($tandaTerima->tanggal_garasi)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-warehouse text-purple-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-900">Masuk Garasi</div>
                            <div class="text-xs text-gray-600">{{ $tandaTerima->tanggal_garasi->format('d F Y') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($tandaTerima->tanggal_checkpoint_supir)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-camera text-orange-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-900">Checkpoint Supir</div>
                            <div class="text-xs text-gray-600">{{ $tandaTerima->tanggal_checkpoint_supir->format('d F Y') }}</div>
                            @php
                                $hasImages = false;
                                if ($tandaTerima->gambar_checkpoint) {
                                    $hasImages = true;
                                } elseif ($tandaTerima->suratJalan && $tandaTerima->suratJalan->gambar_checkpoint) {
                                    $hasImages = true;
                                }
                            @endphp
                            @if($hasImages)
                                <div class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>Foto tersedia
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(!$tandaTerima->tanggal_ambil_kontainer && !$tandaTerima->tanggal_terima_pelabuhan && !$tandaTerima->tanggal_garasi && !$tandaTerima->tanggal_checkpoint_supir)
                    <div class="text-center text-gray-500 py-4">
                        <i class="fas fa-calendar-times text-2xl mb-2"></i>
                        <p class="text-sm">Belum ada aktivitas tercatat</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
                
                <!-- Statistik Ringkas -->
                @php
                    // Try to get images from tanda terima first, then from related surat jalan
                    $gambarCheckpointSidebar = null;
                    if ($tandaTerima->gambar_checkpoint) {
                        $gambarCheckpointSidebar = $tandaTerima->gambar_checkpoint;
                    } elseif ($tandaTerima->suratJalan && $tandaTerima->suratJalan->gambar_checkpoint) {
                        $gambarCheckpointSidebar = $tandaTerima->suratJalan->gambar_checkpoint;
                    }
                @endphp
                
                @if($gambarCheckpointSidebar)
                @php
                    $isJson = is_string($gambarCheckpointSidebar) && (str_starts_with($gambarCheckpointSidebar, '[') || str_starts_with($gambarCheckpointSidebar, '{'));
                    $imagePaths = $isJson ? json_decode($gambarCheckpointSidebar, true) : [$gambarCheckpointSidebar];
                    $imageCount = is_array($imagePaths) ? count(array_filter($imagePaths)) : 1;
                @endphp
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-images text-blue-600"></i>
                            <span class="text-sm font-semibold text-blue-900">Foto Checkpoint</span>
                        </div>
                        <span class="text-sm font-bold text-blue-700">{{ $imageCount }} file</span>
                    </div>
                </div>
                @endif

                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">ID Tanda Terima</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ $tandaTerima->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-gray-400 text-xs"></i>
                                {{ $tandaTerima->creator->name ?? 'System' }}
                            </div>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-plus text-gray-400 text-xs"></i>
                                {{ $tandaTerima->created_at?->format('d F Y H:i') ?? '-' }}
                            </div>
                        </dd>
                    </div>
                    @if($tandaTerima->updated_by)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Diupdate Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-edit text-gray-400 text-xs"></i>
                                {{ $tandaTerima->updater->name ?? '-' }}
                            </div>
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Terakhir Update</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-gray-400 text-xs"></i>
                                {{ $tandaTerima->updated_at?->format('d F Y H:i') ?? '-' }}
                            </div>
                        </dd>
                    </div>
                    
                    @if($tandaTerima->surat_jalan_id)
                    <div class="pt-3 border-t border-gray-200">
                        <dt class="text-xs font-medium text-green-600 uppercase">Linked to Surat Jalan</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-link text-green-600 text-xs"></i>
                                <code class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">ID: {{ $tandaTerima->surat_jalan_id }}</code>
                            </div>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Related Surat Jalan -->
            @if($tandaTerima->suratJalan)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Surat Jalan Terkait</h3>
                <a href="{{ route('surat-jalan.show', $tandaTerima->surat_jalan_id) }}"
                   class="block p-4 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-blue-900">{{ $tandaTerima->no_surat_jalan }}</p>
                            <p class="text-xs text-blue-600 mt-1">Klik untuk lihat detail</p>
                        </div>
                        <i class="fas fa-arrow-right text-blue-600"></i>
                    </div>
                </a>
            </div>
            @endif

            <!-- Danger Zone -->
            <div class="bg-white rounded-lg shadow-sm border border-red-200 p-6">
                <h3 class="text-md font-semibold text-red-900 mb-4">Danger Zone</h3>
                <form action="{{ route('tanda-terima.destroy', $tandaTerima->id) }}"
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini?\n\nNo. Surat Jalan: {{ $tandaTerima->no_surat_jalan }}\n\nData yang dihapus dapat dipulihkan dari trash.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i> Hapus Tanda Terima
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
