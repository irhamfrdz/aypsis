@extends('layouts.app')

@section('title', 'Detail Tagihan OB')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Detail Tagihan OB #{{ $tagihanOb->id }}
                        </h5>
                        <div class="btn-group">
                            <a href="{{ route('tagihan-ob.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                            @can('tagihan-ob-update')
                                <a href="{{ route('tagihan-ob.edit', $tagihanOb) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column - Basic Info -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-ship me-2"></i>
                                Informasi Kapal & Kontainer
                            </h6>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%;">Nama Kapal:</td>
                                    <td>{{ $tagihanOb->kapal }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Voyage:</td>
                                    <td>{{ $tagihanOb->voyage }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">No. Kontainer:</td>
                                    <td><code class="fs-6">{{ $tagihanOb->nomor_kontainer }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Status Kontainer:</td>
                                    <td>
                                        <span class="badge bg-{{ $tagihanOb->status_kontainer === 'full' ? 'success' : 'warning' }} fs-6">
                                            {{ ucfirst($tagihanOb->status_kontainer) }}
                                            @if($tagihanOb->status_kontainer === 'full')
                                                (Tarik Isi)
                                            @else
                                                (Tarik Kosong)
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Jenis Barang:</td>
                                    <td>{{ $tagihanOb->barang }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Right Column - Financial Info -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Informasi Finansial & Status
                            </h6>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%;">Nama Supir:</td>
                                    <td>{{ $tagihanOb->nama_supir }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Biaya OB:</td>
                                    <td>
                                        <h5 class="text-success mb-0">
                                            Rp {{ number_format($tagihanOb->biaya, 0, ',', '.') }}
                                        </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Status Pembayaran:</td>
                                    <td>
                                        <span class="badge bg-{{ $tagihanOb->status_pembayaran === 'paid' ? 'success' : ($tagihanOb->status_pembayaran === 'pending' ? 'warning' : 'danger') }} fs-6">
                                            {{ ucfirst($tagihanOb->status_pembayaran) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tanggal Dibuat:</td>
                                    <td>{{ $tagihanOb->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @if($tagihanOb->creator)
                                    <tr>
                                        <td class="fw-semibold">Dibuat Oleh:</td>
                                        <td>{{ $tagihanOb->creator->name }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($tagihanOb->keterangan)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3">
                                    <i class="fas fa-comment me-2"></i>
                                    Keterangan
                                </h6>
                                <div class="alert alert-light">
                                    {{ $tagihanOb->keterangan }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($tagihanOb->bl)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="text-warning mb-3">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Informasi Bill of Lading (BL)
                                </h6>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nomor BL:</strong> {{ $tagihanOb->bl->nomor_bl }}<br>
                                                <strong>Kapal BL:</strong> {{ $tagihanOb->bl->kapal }}<br>
                                            </div>
                                            <div class="col-md-6">
                                                @if($tagihanOb->bl->created_at)
                                                    <strong>Tanggal BL:</strong> {{ $tagihanOb->bl->created_at->format('d/m/Y') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @can('bl-view')
                                                <a href="{{ route('bl.show', $tagihanOb->bl) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i>
                                                    Lihat Detail BL
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    @can('tagihan-ob-delete')
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                onclick="confirmDelete()">
                                            <i class="fas fa-trash me-1"></i>
                                            Hapus Tagihan
                                        </button>
                                    @endcan
                                </div>
                                
                                <div class="btn-group">
                                    <a href="{{ route('tagihan-ob.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-list me-1"></i>
                                        Daftar Tagihan
                                    </a>
                                    @can('tagihan-ob-update')
                                        <a href="{{ route('tagihan-ob.edit', $tagihanOb) }}" class="btn btn-warning">
                                            <i class="fas fa-edit me-1"></i>
                                            Edit Data
                                        </a>
                                    @endcan
                                    @can('tagihan-ob-create')
                                        <a href="{{ route('tagihan-ob.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>
                                            Tambah Baru
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tagihan OB ini?</p>
                <div class="alert alert-warning">
                    <strong>Data yang akan dihapus:</strong><br>
                    Kapal: {{ $tagihanOb->kapal }} ({{ $tagihanOb->voyage }})<br>
                    Kontainer: {{ $tagihanOb->nomor_kontainer }}<br>
                    Supir: {{ $tagihanOb->nama_supir }}
                </div>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Data yang sudah dihapus tidak dapat dikembalikan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('tagihan-ob.destroy', $tagihanOb) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush