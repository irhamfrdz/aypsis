@extends('layouts.app')

@section('title', 'Approval Surat Jalan - ' . ucfirst(str_replace('-', ' ', $approvalLevel)))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Approval Surat Jalan - {{ ucfirst(str_replace('-', ' ', $approvalLevel)) }}</h4>
                            <small class="opacity-75">Kelola approval surat jalan untuk level {{ $approvalLevel }}</small>
                        </div>
                        <div class="d-flex gap-3">
                            <div class="text-center">
                                <div class="h5 mb-0">{{ $stats['pending'] }}</div>
                                <small>Pending</small>
                            </div>
                            <div class="text-center">
                                <div class="h5 mb-0">{{ $stats['approved_today'] }}</div>
                                <small>Hari Ini</small>
                            </div>
                            <div class="text-center">
                                <div class="h5 mb-0">{{ $stats['approved_total'] }}</div>
                                <small>Total</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($pendingApprovals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Surat Jalan</th>
                                        <th>Tanggal</th>
                                        <th>Supir</th>
                                        <th>Kegiatan</th>
                                        <th>Nomor Kontainer</th>
                                        <th>No. Seal</th>
                                        <th>Submitted</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingApprovals as $approval)
                                        <tr>
                                            <td>
                                                <strong>{{ $approval->suratJalan->no_surat_jalan }}</strong>
                                            </td>
                                            <td>
                                                {{ $approval->suratJalan->tanggal_surat_jalan ? $approval->suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <i class="fas fa-user me-1"></i>
                                                {{ $approval->suratJalan->supir }}
                                            </td>
                                            <td>
                                                @php
                                                    $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $approval->suratJalan->kegiatan)
                                                                    ->value('nama_kegiatan') ?? $approval->suratJalan->kegiatan;
                                                @endphp
                                                <span class="badge bg-info">{{ $kegiatanName }}</span>
                                            </td>
                                            <td>
                                                <code>{{ $approval->suratJalan->no_kontainer ?: 'Belum diisi' }}</code>
                                            </td>
                                            <td>
                                                <code>{{ $approval->suratJalan->no_seal ?: 'Belum diisi' }}</code>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $approval->created_at->diffForHumans() }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('approval.surat-jalan.show', $approval->suratJalan) }}" 
                                                       class="btn btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $pendingApprovals->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada surat jalan yang perlu di-approve</h5>
                            <p class="text-muted">Semua surat jalan untuk level {{ $approvalLevel }} sudah diproses.</p>
                        </div>
                    @endif
                </div>
            </div>
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
});
</script>
@endsection