@extends('layouts.app')

@section('title', 'Detail Surat Jalan - Approval')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Detail Surat Jalan</h4>
                            <small class="opacity-75">{{ $suratJalan->no_surat_jalan }} - Level {{ ucfirst(str_replace('-', ' ', $approvalLevel)) }}</small>
                        </div>
                        <div>
                            <a href="{{ route('approval.surat-jalan.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Detail Surat Jalan -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informasi Surat Jalan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">No. Surat Jalan</label>
                                    <p class="mb-0">{{ $suratJalan->no_surat_jalan }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal</label>
                                    <p class="mb-0">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Supir</label>
                                    <p class="mb-0"><i class="fas fa-user me-1"></i>{{ $suratJalan->supir }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Kegiatan</label>
                                    @php
                                        $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $suratJalan->kegiatan)
                                                        ->value('nama_kegiatan') ?? $suratJalan->kegiatan;
                                    @endphp
                                    <p class="mb-0"><span class="badge bg-info">{{ $kegiatanName }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ukuran Kontainer</label>
                                    <p class="mb-0">{{ $suratJalan->size }} ft</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jumlah Kontainer</label>
                                    <p class="mb-0">{{ $suratJalan->jumlah_kontainer }} unit</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nomor Kontainer</label>
                                    <p class="mb-0"><code>{{ $suratJalan->no_kontainer ?: 'Belum diisi' }}</code></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">No. Seal</label>
                                    <p class="mb-0"><code>{{ $suratJalan->no_seal ?: 'Belum diisi' }}</code></p>
                                </div>
                                @if($suratJalan->tujuan_pengiriman)
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Tujuan Pengiriman</label>
                                        <p class="mb-0">{{ $suratJalan->tujuan_pengiriman }}</p>
                                    </div>
                                @endif
                                @if($suratJalan->pengirim)
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Pengirim</label>
                                        <p class="mb-0">{{ $suratJalan->pengirim }}</p>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <label class="form-label fw-bold">Status Saat Ini</label>
                                    <p class="mb-0">
                                        @switch($suratJalan->status)
                                            @case('sudah_checkpoint')
                                                <span class="badge bg-warning">Sudah Checkpoint</span>
                                                @break
                                            @case('fully_approved')
                                                <span class="badge bg-success">Fully Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $suratJalan->status }}</span>
                                        @endswitch
                                    </p>
                                </div>
                            </div>

                            <!-- Gambar Checkpoint -->
                            @if($suratJalan->gambar_checkpoint)
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Gambar Checkpoint</label>
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $suratJalan->gambar_checkpoint) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-image me-1"></i>Lihat Gambar
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panel Approval -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Status Approval</h5>
                        </div>
                        <div class="card-body">
                            <!-- Status Semua Level Approval -->
                            <div class="mb-4">
                                @foreach($suratJalan->approvals as $approvalItem)
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded {{ $approvalItem->status === 'approved' ? 'bg-success bg-opacity-10' : ($approvalItem->status === 'rejected' ? 'bg-danger bg-opacity-10' : 'bg-warning bg-opacity-10') }}">
                                        <span class="fw-bold">{{ ucfirst(str_replace('-', ' ', $approvalItem->approval_level)) }}</span>
                                        @switch($approvalItem->status)
                                            @case('approved')
                                                <span class="badge bg-success">Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                                @break
                                            @default
                                                <span class="badge bg-warning">Pending</span>
                                        @endswitch
                                    </div>
                                    @if($approvalItem->approved_at && $approvalItem->approver)
                                        <div class="text-muted small mb-3">
                                            <i class="fas fa-user me-1"></i>{{ $approvalItem->approver->name }}<br>
                                            <i class="fas fa-clock me-1"></i>{{ $approvalItem->approved_at->format('d/m/Y H:i') }}
                                            @if($approvalItem->approval_notes)
                                                <br><i class="fas fa-comment me-1"></i>{{ $approvalItem->approval_notes }}
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Form Approval jika masih pending -->
                            @if($approval->status === 'pending')
                                <div class="border-top pt-4">
                                    <h6 class="mb-3">Aksi Approval</h6>
                                    
                                    <!-- Form Approve -->
                                    <form action="{{ route('approval.surat-jalan.approve', $suratJalan) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="approval_notes" class="form-label">Catatan (Opsional)</label>
                                            <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3" placeholder="Tambahkan catatan untuk approval..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100 mb-2">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                    </form>

                                    <!-- Form Reject -->
                                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">Approval sudah diproses</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('approval.surat-jalan.reject', $suratJalan) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Surat Jalan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">Alasan Reject <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_reason" name="approval_notes" rows="4" placeholder="Jelaskan alasan mengapa surat jalan ini di-reject..." required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Tindakan ini akan membuat surat jalan di-reject dan tidak bisa diproses lebih lanjut.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection