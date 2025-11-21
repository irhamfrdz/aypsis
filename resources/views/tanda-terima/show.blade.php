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
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Jumlah</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->jumlah ? number_format($tandaTerima->jumlah, 0, ',', '.') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Satuan</dt>
                            <dd class="text-sm text-gray-900">{{ $tandaTerima->satuan ?: '-' }}</dd>
                        </div>

                        <!-- Dimensi Items Information -->
                        @if($tandaTerima->dimensi_items)
                            @php
                                $dimensiItems = is_string($tandaTerima->dimensi_items) ? json_decode($tandaTerima->dimensi_items, true) : $tandaTerima->dimensi_items;
                                $totalVolume = 0;
                                $totalTonase = 0;
                            @endphp
                            
                            @if(is_array($dimensiItems) && count($dimensiItems) > 0)
                                <div class="col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Dimensi & Volume</dt>
                                    <dd class="text-sm text-gray-900">
                                        <div class="space-y-2">
                                            @foreach($dimensiItems as $index => $item)
                                                @if(isset($item['panjang']) || isset($item['lebar']) || isset($item['tinggi']) || isset($item['meter_kubik']) || isset($item['tonase']))
                                                    @php
                                                        $totalVolume += $item['meter_kubik'] ?? 0;
                                                        $totalTonase += $item['tonase'] ?? 0;
                                                    @endphp
                                                    <div class="bg-gray-50 p-3 rounded-lg">
                                                        <div class="font-medium text-gray-600 mb-1">Item {{ $index + 1 }}</div>
                                                        @if(isset($item['panjang']) || isset($item['lebar']) || isset($item['tinggi']))
                                                            <div class="text-xs text-gray-600">
                                                                Dimensi:
                                                                @if(isset($item['panjang']))
                                                                    {{ rtrim(rtrim(number_format($item['panjang'], 3, '.', ''), '0'), '.') }} m
                                                                @endif
                                                                @if(isset($item['lebar']))
                                                                    × {{ rtrim(rtrim(number_format($item['lebar'], 3, '.', ''), '0'), '.') }} m
                                                                @endif
                                                                @if(isset($item['tinggi']))
                                                                    × {{ rtrim(rtrim(number_format($item['tinggi'], 3, '.', ''), '0'), '.') }} m
                                                                @endif
                                                            </div>
                                                        @endif
                                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                                            @if(isset($item['meter_kubik']) && $item['meter_kubik'] > 0)
                                                                <div class="text-xs">Volume: {{ rtrim(rtrim(number_format($item['meter_kubik'], 3, '.', ''), '0'), '.') }} m³</div>
                                                            @endif
                                                            @if(isset($item['tonase']) && $item['tonase'] > 0)
                                                                <div class="text-xs">Tonase: {{ rtrim(rtrim(number_format($item['tonase'], 3, '.', ''), '0'), '.') }} Ton</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach

                                            @if($totalVolume > 0 || $totalTonase > 0)
                                                <div class="bg-blue-50 p-3 rounded-lg border-l-4 border-blue-400">
                                                    <div class="font-medium text-blue-800 mb-1">Total Keseluruhan</div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div class="text-sm text-blue-700">Volume: {{ rtrim(rtrim(number_format($totalVolume, 3, '.', ''), '0'), '.') }} m³</div>
                                                        <div class="text-sm text-blue-700">Tonase: {{ rtrim(rtrim(number_format($totalTonase, 3, '.', ''), '0'), '.') }} Ton</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </dd>
                                </div>
                            @else
                                <!-- Fallback to legacy single dimension display when dimensi_items exists but empty -->
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
                        @else
                            <!-- Fallback when no dimensi_items at all -->
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
            @if($tandaTerima->gambar_checkpoint)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Gambar Checkpoint</h2>
                </div>
                <div class="p-6">
                    @php
                        // Check if it's a JSON array (multiple images) or single image path
                        $gambarCheckpoint = $tandaTerima->gambar_checkpoint;
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
                                         onclick="window.open('{{ $fileUrl }}', '_blank')">
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

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->creator->name ?? 'System' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->created_at?->format('d F Y H:i') ?? '-' }}</dd>
                    </div>
                    @if($tandaTerima->updated_by)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Diupdate Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->updater->name ?? '-' }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Terakhir Update</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tandaTerima->updated_at?->format('d F Y H:i') ?? '-' }}</dd>
                    </div>
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
