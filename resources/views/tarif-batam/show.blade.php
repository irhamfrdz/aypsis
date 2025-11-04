@extends('layouts.main')

@section('title', 'Detail Tarif Batam')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">💰 Detail Tarif Batam</h1>
                    <p class="text-muted">Informasi lengkap tarif pengiriman untuk wilayah Batam</p>
                </div>
                <div class="d-flex gap-2">
                    @can('tarif-batam.edit')
                        <a href="{{ route('tarif-batam.edit', $tarifBatam) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endcan
                    <a href="{{ route('tarif-batam.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Info Umum --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Umum</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">ID Tarif</label>
                        <p class="fw-bold">#{{ $tarifBatam->id }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Masa Berlaku</label>
                        <p class="fw-bold">
                            <span class="badge {{ $tarifBatam->masa_berlaku >= now()->toDateString() ? 'bg-success' : 'bg-danger' }} fs-6">
                                {{ $tarifBatam->masa_berlaku->format('d F Y') }}
                            </span>
                        </p>
                        @if($tarifBatam->masa_berlaku < now()->toDateString())
                            <small class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Tarif sudah expired
                            </small>
                        @else
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> Tarif masih berlaku
                            </small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <p class="fw-bold">
                            <span class="badge {{ $tarifBatam->status === 'aktif' ? 'bg-success' : 'bg-secondary' }} fs-6">
                                {{ ucfirst($tarifBatam->status) }}
                            </span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Dibuat</label>
                        <p class="fw-bold">{{ $tarifBatam->created_at->format('d F Y, H:i') }}</p>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-muted">Terakhir Diperbarui</label>
                        <p class="fw-bold">{{ $tarifBatam->updated_at->format('d F Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarif Container --}}
        <div class="col-md-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Tarif</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-muted" style="width: 40%;">Chasis AYP</td>
                                    <td class="text-end fs-5 fw-bold text-primary">
                                        {{ $tarifBatam->chasis_ayp ? 'Rp ' . $tarifBatam->formatted_chasis_ayp : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">20ft Full</td>
                                    <td class="text-end fs-5 fw-bold text-primary">
                                        {{ $tarifBatam->{'20ft_full'} ? 'Rp ' . $tarifBatam->formatted_20ft_full : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">20ft Empty</td>
                                    <td class="text-end fs-5 fw-bold text-primary">
                                        {{ $tarifBatam->{'20ft_empty'} ? 'Rp ' . $tarifBatam->formatted_20ft_empty : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Antar Lokasi</td>
                                    <td class="text-end fs-5 fw-bold text-primary">
                                        {{ $tarifBatam->antar_lokasi ? 'Rp ' . $tarifBatam->formatted_antar_lokasi : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">40ft Full</td>
                                    <td class="text-end fs-5 fw-bold text-primary">
                                        {{ $tarifBatam->{'40ft_full'} ? 'Rp ' . $tarifBatam->formatted_40ft_full : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">40ft Empty</td>
                                    <td class="text-end fs-5 fw-bold text-primary">
                                        {{ $tarifBatam->{'40ft_empty'} ? 'Rp ' . $tarifBatam->formatted_40ft_empty : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">40ft Antar Lokasi</td>
                                    <td class="text-end fs-5 fw-bold text-primary">
                                        {{ $tarifBatam->{'40ft_antar_lokasi'} ? 'Rp ' . $tarifBatam->formatted_40ft_antar_lokasi : '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Keterangan --}}
        @if($tarifBatam->keterangan)
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Keterangan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $tarifBatam->keterangan }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center gap-3">
                        @can('tarif-batam.edit')
                            <a href="{{ route('tarif-batam.edit', $tarifBatam) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Tarif
                            </a>
                        @endcan
                        
                        @can('tarif-batam.delete')
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash"></i> Hapus Tarif
                            </button>
                        @endcan
                        
                        <a href="{{ route('tarif-batam.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
@can('tarif-batam.delete')
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tarif batam ini?</p>
                <div class="alert alert-warning">
                    <strong>Detail Tarif:</strong><br>
                    ID: #{{ $tarifBatam->id }}<br>
                    Masa Berlaku: {{ $tarifBatam->masa_berlaku->format('d F Y') }}<br>
                    Status: {{ ucfirst($tarifBatam->status) }}
                </div>
                <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('tarif-batam.destroy', $tarifBatam) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus!</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush