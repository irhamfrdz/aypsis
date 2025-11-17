@extends('layouts.app')

@section('title', 'Approval Surat Jalan - ' . ucfirst(str_replace('-', ' ', $approvalLevel)))
@section('page_title', 'Approval Surat Jalan - ' . ucfirst(str_replace('-', ' ', $approvalLevel)))

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Approval Surat Jalan - {{ ucfirst(str_replace('-', ' ', $approvalLevel)) }}</h1>
                <p class="text-xs text-gray-600 mt-1">Kelola approval surat jalan untuk level {{ $approvalLevel }}</p>
            </div>
            <div class="flex gap-4 text-sm">
                <div class="text-center">
                    <div class="text-lg font-semibold text-indigo-600">{{ $stats['pending'] }}</div>
                    <div class="text-gray-500 text-xs">Pending</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-green-600">{{ $stats['approved_today'] }}</div>
                    <div class="text-gray-500 text-xs">Hari Ini</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-gray-600">{{ $stats['approved_total'] }}</div>
                    <div class="text-gray-500 text-xs">Total</div>
                </div>
            </div>
        </div>

        <div class="p-4">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Search Form -->
            <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <form method="GET" action="{{ route('approval.surat-jalan.index', $approvalLevel) }}" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label for="search_no_surat_jalan" class="block text-xs font-medium text-gray-700 mb-1">No. Surat Jalan</label>
                            <input type="text" 
                                   name="search_no_surat_jalan" 
                                   id="search_no_surat_jalan"
                                   value="{{ request('search_no_surat_jalan') }}"
                                   placeholder="Cari nomor surat jalan..."
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="search_supir" class="block text-xs font-medium text-gray-700 mb-1">Supir</label>
                            <input type="text" 
                                   name="search_supir" 
                                   id="search_supir"
                                   value="{{ request('search_supir') }}"
                                   placeholder="Cari nama supir..."
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="search_kegiatan" class="block text-xs font-medium text-gray-700 mb-1">Kegiatan</label>
                            <input type="text" 
                                   name="search_kegiatan" 
                                   id="search_kegiatan"
                                   value="{{ request('search_kegiatan') }}"
                                   placeholder="Cari kegiatan..."
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="search_no_pemesanan" class="block text-xs font-medium text-gray-700 mb-1">No. Pemesanan</label>
                            <input type="text" 
                                   name="search_no_pemesanan" 
                                   id="search_no_pemesanan"
                                   value="{{ request('search_no_pemesanan') }}"
                                   placeholder="Cari nomor pemesanan..."
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-md transition-colors duration-150">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        @if(request()->hasAny(['search_no_surat_jalan', 'search_supir', 'search_kegiatan', 'search_no_pemesanan']))
                            <a href="{{ route('approval.surat-jalan.index', $approvalLevel) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-md transition-colors duration-150">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reset
                            </a>
                        @endif
                        @if(request()->hasAny(['search_no_surat_jalan', 'search_supir', 'search_kegiatan', 'search_no_pemesanan']))
                            <span class="text-xs text-gray-600 ml-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filter aktif
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            @if($pendingApprovals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pemesanan</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Kirim</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seal</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingApprovals as $approval)
                                @if($approval->suratJalan)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="text-xs font-semibold text-gray-900">{{ $approval->suratJalan->no_surat_jalan }}</div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="text-xs text-gray-600">
                                            {{ $approval->suratJalan->tanggal_surat_jalan ? $approval->suratJalan->tanggal_surat_jalan->format('d/m/y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="text-xs text-gray-700">{{ Str::limit($approval->suratJalan->supir, 15) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @php
                                            $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $approval->suratJalan->kegiatan)
                                                            ->value('nama_kegiatan') ?? $approval->suratJalan->kegiatan;
                                        @endphp
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ Str::limit($kegiatanName, 12) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-xs text-gray-600 max-w-[120px] truncate" title="{{ $approval->suratJalan->no_pemesanan ?? '-' }}">
                                            {{ $approval->suratJalan->no_pemesanan ? Str::limit($approval->suratJalan->no_pemesanan, 20) : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-xs text-gray-600 max-w-[120px] truncate" title="{{ $approval->suratJalan->tujuan_pengiriman ?? $approval->suratJalan->order->tujuan_kirim ?? '-' }}">
                                            {{ Str::limit($approval->suratJalan->tujuan_pengiriman ?? $approval->suratJalan->order->tujuan_kirim ?? '-', 20) }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if(strtolower($approval->suratJalan->tipe_kontainer ?? '') === 'cargo')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                Cargo
                                            </span>
                                        @else
                                            <div class="flex items-center space-x-1">
                                                <div id="kontainer-display-{{ $approval->suratJalan->id }}">
                                                    @if($approval->status === 'approved')
                                                        <code class="text-xs bg-green-100 px-1.5 py-0.5 rounded border border-green-300">
                                                            {{ Str::limit($approval->suratJalan->no_kontainer ?: 'Belum diisi', 12) }}
                                                        </code>
                                                        <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs bg-green-100 text-green-800" title="Sudah diapprove - tidak dapat diubah">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">
                                                            {{ Str::limit($approval->suratJalan->no_kontainer ?: 'Belum diisi', 12) }}
                                                        </code>
                                                    @endif
                                                </div>
                                                @can('approval-surat-jalan-approve')
                                                    @if($approval->status !== 'approved')
                                                        <button type="button" onclick="editKontainerInline({{ $approval->suratJalan->id }})" 
                                                                class="text-blue-600 hover:text-blue-800 p-1" title="Edit Kontainer">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                @endcan
                                            </div>
                                            <div id="kontainer-edit-{{ $approval->suratJalan->id }}" class="hidden mt-1">
                                                <div class="flex flex-col space-y-1">
                                                    <select id="kontainer-select-{{ $approval->suratJalan->id }}" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                        <option value="">Pilih dari stock...</option>
                                                    </select>
                                                    <input type="text" id="kontainer-manual-{{ $approval->suratJalan->id }}" 
                                                           placeholder="Atau ketik manual" 
                                                           value="{{ $approval->suratJalan->no_kontainer }}"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                    <div class="flex space-x-1">
                                                        <button type="button" onclick="saveKontainerInline({{ $approval->suratJalan->id }})" 
                                                                class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                            Save
                                                        </button>
                                                        <button type="button" onclick="cancelEditKontainerInline({{ $approval->suratJalan->id }})" 
                                                                class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if(strtolower($approval->suratJalan->tipe_kontainer ?? '') === 'cargo')
                                            <span class="text-xs text-gray-400 italic">
                                                -
                                            </span>
                                        @else
                                            <div class="flex items-center space-x-1">
                                                <div id="seal-display-{{ $approval->suratJalan->id }}">
                                                    @if($approval->status === 'approved')
                                                        <code class="text-xs bg-green-100 px-1.5 py-0.5 rounded border border-green-300">
                                                            {{ Str::limit($approval->suratJalan->no_seal ?: 'Belum diisi', 10) }}
                                                        </code>
                                                        <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs bg-green-100 text-green-800" title="Sudah diapprove - tidak dapat diubah">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">
                                                            {{ Str::limit($approval->suratJalan->no_seal ?: 'Belum diisi', 10) }}
                                                        </code>
                                                    @endif
                                                </div>
                                                @can('approval-surat-jalan-approve')
                                                    @if($approval->status !== 'approved')
                                                        <button type="button" onclick="editSealInline({{ $approval->suratJalan->id }})" 
                                                                class="text-blue-600 hover:text-blue-800 p-1" title="Edit Seal">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                @endcan
                                            </div>
                                            <div id="seal-edit-{{ $approval->suratJalan->id }}" class="hidden mt-1">
                                                <div class="flex flex-col space-y-1">
                                                    <input type="text" id="seal-input-{{ $approval->suratJalan->id }}" 
                                                           placeholder="Masukkan nomor seal" 
                                                           value="{{ $approval->suratJalan->no_seal }}"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                    <div class="flex space-x-1">
                                                        <button type="button" onclick="saveSealInline({{ $approval->suratJalan->id }})" 
                                                                class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                            Save
                                                        </button>
                                                        <button type="button" onclick="cancelEditSealInline({{ $approval->suratJalan->id }})" 
                                                                class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="flex items-center text-xs text-gray-500">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $approval->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-center">
                                        <a href="{{ route('approval.surat-jalan.show', $approval->suratJalan) }}"
                                           class="inline-flex items-center px-2 py-1 border border-indigo-300 rounded text-xs font-medium text-indigo-700 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Detail
                                        </a>
                                    </td>

                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="inline-flex items-center px-1.5 py-1 text-xs text-purple-600 hover:bg-purple-50 rounded transition-colors duration-150"
                                                    onclick="showAuditLog('{{ get_class($approval) }}', {{ $approval->id }})"
                                                    title="Lihat Riwayat">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        @endcan
                                    </td></tr>
                                @else
                                <tr class="hover:bg-red-50 transition-colors duration-150">
                                    <td colspan="11" class="px-3 py-2 text-center">
                                        <div class="text-xs text-red-600 font-medium">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Approval ID: {{ $approval->id }} - Surat Jalan tidak ditemukan (mungkin telah dihapus)
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-red-500">Submitted: {{ $approval->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-4 px-2">
                    <div class="text-xs text-gray-500">
                        Menampilkan {{ $pendingApprovals->firstItem() ?? 0 }} - {{ $pendingApprovals->lastItem() ?? 0 }} 
                        dari {{ $pendingApprovals->total() }} data
                    </div>
                    <div class="flex items-center space-x-2">
                        @include('components.modern-pagination', ['paginator' => $pendingApprovals])
                        @include('components.rows-per-page')
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="text-base font-medium text-gray-500 mb-1">Tidak ada surat jalan yang perlu di-approve</h3>
                        <p class="text-sm text-gray-400">Semua surat jalan untuk level {{ $approvalLevel }} sudah diproses.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
// Load Select2 dynamically after jQuery is available
function loadSelect2() {
    return new Promise((resolve, reject) => {
        if (typeof window.$ !== 'undefined' && typeof window.$.fn.select2 !== 'undefined') {
            resolve(); // Select2 already loaded
            return;
        }
        
        // Wait for jQuery first
        function waitForjQuery() {
            if (typeof window.$ !== 'undefined') {
                // jQuery is available, now load Select2
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
                script.onload = () => resolve();
                script.onerror = () => reject('Failed to load Select2');
                document.head.appendChild(script);
            } else {
                setTimeout(waitForjQuery, 100);
            }
        }
        waitForjQuery();
    });
}

// Global variables untuk edit inline
let currentEditingKontainer = null;
let currentEditingSeal = null;

// Function to wait for jQuery and Select2 to be available
function waitForjQuery(callback) {
    loadSelect2().then(() => {
        callback();
    }).catch(error => {
        console.error('Error loading Select2:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto refresh halaman setiap 30 detik
    setTimeout(function() {
        location.reload();
    }, 30000);

    // Add visual indicator for auto refresh
    let countdown = 30;
    const refreshIndicator = document.createElement('div');
    refreshIndicator.className = 'fixed bottom-4 right-4 bg-indigo-600 text-white px-3 py-2 rounded-lg shadow-lg text-sm z-50';
    refreshIndicator.innerHTML = `
        <div class="flex items-center space-x-2">
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Auto refresh dalam <span id="countdown">${countdown}</span>s</span>
        </div>
    `;
    document.body.appendChild(refreshIndicator);

    const countdownEl = document.getElementById('countdown');
    const interval = setInterval(() => {
        countdown--;
        countdownEl.textContent = countdown;
        if (countdown <= 0) {
            clearInterval(interval);
        }
    }, 1000);

    // Hide refresh indicator after 5 seconds
    setTimeout(() => {
        refreshIndicator.style.opacity = '0';
        setTimeout(() => {
            refreshIndicator.remove();
        }, 300);
    }, 5000);
});

// ===== KONTAINER EDIT FUNCTIONS =====

function editKontainerInline(suratJalanId) {
    // Check if this approval is already approved
    const row = document.querySelector(`tr:has(#kontainer-display-${suratJalanId})`);
    const isApproved = row && row.querySelector('.bg-green-100[title*="Sudah diapprove"]');
    
    if (isApproved) {
        showNotificationInline('Surat jalan sudah diapprove, kontainer tidak dapat diubah', 'error');
        return;
    }
    
    if (currentEditingKontainer && currentEditingKontainer !== suratJalanId) {
        cancelEditKontainerInline(currentEditingKontainer);
    }
    
    currentEditingKontainer = suratJalanId;
    
    document.getElementById(`kontainer-display-${suratJalanId}`).classList.add('hidden');
    document.getElementById(`kontainer-edit-${suratJalanId}`).classList.remove('hidden');
    
    // Initialize Select2 untuk dropdown stock kontainer
    initKontainerSelectInline(suratJalanId);
}

function cancelEditKontainerInline(suratJalanId) {
    currentEditingKontainer = null;
    
    document.getElementById(`kontainer-display-${suratJalanId}`).classList.remove('hidden');
    document.getElementById(`kontainer-edit-${suratJalanId}`).classList.add('hidden');
    
    // Destroy Select2 instance if exists
    waitForjQuery(() => {
        const selectElement = $(`#kontainer-select-${suratJalanId}`);
        if (selectElement.hasClass('select2-hidden-accessible')) {
            selectElement.select2('destroy');
        }
        
        // Reset values
        selectElement.val('').empty().append('<option value="">Pilih dari stock...</option>');
    });
    
    // Get original value from display
    const originalValue = document.querySelector(`#kontainer-display-${suratJalanId} code`).textContent.trim();
    document.getElementById(`kontainer-manual-${suratJalanId}`).value = originalValue === 'Belum diisi' ? '' : originalValue;
}

function initKontainerSelectInline(suratJalanId) {
    waitForjQuery(() => {
        $(`#kontainer-select-${suratJalanId}`).select2({
        placeholder: 'Cari nomor kontainer...',
        allowClear: true,
        ajax: {
            url: '{{ route("approval.surat-jalan.api.stock-kontainers") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.nomor,
                            text: item.text,
                            data: item
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        width: '100%',
        dropdownParent: $(`#kontainer-edit-${suratJalanId}`)
    });

    // When stock kontainer is selected, update manual input
    $(`#kontainer-select-${suratJalanId}`).on('select2:select', function (e) {
        const data = e.params.data;
        document.getElementById(`kontainer-manual-${suratJalanId}`).value = data.id;
    });

    // When cleared, clear manual input
    $(`#kontainer-select-${suratJalanId}`).on('select2:clear', function (e) {
        document.getElementById(`kontainer-manual-${suratJalanId}`).value = '';
    });
    });
}

function saveKontainerInline(suratJalanId) {
    const kontainerValue = document.getElementById(`kontainer-manual-${suratJalanId}`).value.trim();
    const stockKontainerId = $(`#kontainer-select-${suratJalanId}`).val();
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    // Make AJAX request
    fetch(`/approval/surat-jalan/${suratJalanId}/update-kontainer-seal`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            no_kontainer: kontainerValue,
            stock_kontainer_id: stockKontainerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display
            const displayValue = kontainerValue || 'Belum diisi';
            document.querySelector(`#kontainer-display-${suratJalanId} code`).textContent = displayValue.length > 12 ? displayValue.substring(0, 12) + '...' : displayValue;
            
            // Show success message
            showNotificationInline('Kontainer berhasil diperbarui', 'success');
            
            // Cancel edit mode
            cancelEditKontainerInline(suratJalanId);
        } else {
            showNotificationInline(data.message || 'Gagal memperbarui kontainer', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotificationInline('Terjadi kesalahan saat memperbarui kontainer', 'error');
    })
    .finally(() => {
        // Restore button state
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

// ===== SEAL EDIT FUNCTIONS =====

function editSealInline(suratJalanId) {
    // Check if this approval is already approved
    const row = document.querySelector(`tr:has(#seal-display-${suratJalanId})`);
    const isApproved = row && row.querySelector('.bg-green-100[title*="Sudah diapprove"]');
    
    if (isApproved) {
        showNotificationInline('Surat jalan sudah diapprove, seal tidak dapat diubah', 'error');
        return;
    }
    
    if (currentEditingSeal && currentEditingSeal !== suratJalanId) {
        cancelEditSealInline(currentEditingSeal);
    }
    
    currentEditingSeal = suratJalanId;
    
    document.getElementById(`seal-display-${suratJalanId}`).classList.add('hidden');
    document.getElementById(`seal-edit-${suratJalanId}`).classList.remove('hidden');
    document.getElementById(`seal-input-${suratJalanId}`).focus();
}

function cancelEditSealInline(suratJalanId) {
    currentEditingSeal = null;
    
    document.getElementById(`seal-display-${suratJalanId}`).classList.remove('hidden');
    document.getElementById(`seal-edit-${suratJalanId}`).classList.add('hidden');
    
    // Reset value
    const originalValue = document.querySelector(`#seal-display-${suratJalanId} code`).textContent.trim();
    document.getElementById(`seal-input-${suratJalanId}`).value = originalValue === 'Belum diisi' ? '' : originalValue;
}

function saveSealInline(suratJalanId) {
    const sealValue = document.getElementById(`seal-input-${suratJalanId}`).value.trim();
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    // Make AJAX request
    fetch(`/approval/surat-jalan/${suratJalanId}/update-kontainer-seal`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            no_seal: sealValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display
            const displayValue = sealValue || 'Belum diisi';
            document.querySelector(`#seal-display-${suratJalanId} code`).textContent = displayValue.length > 10 ? displayValue.substring(0, 10) + '...' : displayValue;
            
            // Show success message
            showNotificationInline('Seal berhasil diperbarui', 'success');
            
            // Cancel edit mode
            cancelEditSealInline(suratJalanId);
        } else {
            showNotificationInline(data.message || 'Gagal memperbarui seal', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotificationInline('Terjadi kesalahan saat memperbarui seal', 'error');
    })
    .finally(() => {
        // Restore button state
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

// ===== UTILITY FUNCTIONS =====

function showNotificationInline(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-3 rounded-lg shadow-lg z-50 max-w-sm ${
        type === 'success' ? 'bg-green-100 border border-green-500 text-green-700' :
        type === 'error' ? 'bg-red-100 border border-red-500 text-red-700' :
        'bg-blue-100 border border-blue-500 text-blue-700'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                    type === 'error' ?
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' :
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                }
            </div>
            <div class="ml-3">
                <span class="font-medium text-sm">${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current hover:opacity-75">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }
    }, 4000);
}

// Handle keyboard events
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (currentEditingKontainer) {
            cancelEditKontainerInline(currentEditingKontainer);
        }
        if (currentEditingSeal) {
            cancelEditSealInline(currentEditingSeal);
        }
    }
});

// Handle Enter key for seal inputs
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && e.target.id.startsWith('seal-input-')) {
        const suratJalanId = e.target.id.split('-')[2];
        saveSealInline(suratJalanId);
    }
    if (e.key === 'Enter' && e.target.id.startsWith('kontainer-manual-')) {
        const suratJalanId = e.target.id.split('-')[2];
        saveKontainerInline(suratJalanId);
    }
});
</script>
@endpush
