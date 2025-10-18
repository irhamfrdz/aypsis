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

            @if($pendingApprovals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kontainer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Seal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingApprovals as $approval)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $approval->suratJalan->no_surat_jalan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $approval->suratJalan->tanggal_surat_jalan ? $approval->suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="text-sm text-gray-900">{{ $approval->suratJalan->supir }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $approval->suratJalan->kegiatan)
                                                            ->value('nama_kegiatan') ?? $approval->suratJalan->kegiatan;
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $kegiatanName }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                                            {{ $approval->suratJalan->no_kontainer ?: 'Belum diisi' }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                                            {{ $approval->suratJalan->no_seal ?: 'Belum diisi' }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $approval->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('approval.surat-jalan.show', $approval->suratJalan) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Detail
                                        </a>
                                    </td>
                                
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog(get_class($approval), {{ $approval->id }})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endcan
                                    </td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-center mt-4">
                    {{ $pendingApprovals->links() }}
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

@section('scripts')
<script>
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
</script>
@endsection