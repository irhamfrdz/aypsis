@extends('layouts.app')

@section('title', 'Detail Aktivitas Lain-lain')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks mr-2"></i>
                        Detail Aktivitas Lain-lain
                        @if($aktivitas->nomor_aktivitas)
                            <span class="badge badge-primary ml-2">{{ $aktivitas->nomor_aktivitas }}</span>
                        @endif
                    </h3>
                    <div class="card-tools">
                        @can('aktivitas-lainnya-update')
                            @if($aktivitas->status !== 'paid')
                                <a href="{{ route('aktivitas-lainnya.edit', $aktivitas->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                        @endcan
                        @can('aktivitas-lainnya-delete')
                            @if($aktivitas->status === 'draft')
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete()">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            @endif
                        @endcan
                        <a href="{{ route('aktivitas-lainnya.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-{{ $aktivitas->status === 'paid' ? 'success' : ($aktivitas->status === 'approved' ? 'info' : 'warning') }} badge-lg px-3 py-2 h5 mb-0">
                                    <i class="fas fa-{{ $aktivitas->status === 'paid' ? 'check-circle' : ($aktivitas->status === 'approved' ? 'clock' : 'edit') }} mr-2"></i>
                                    {{ ucfirst($aktivitas->status) }}
                                </span>
                                <div class="text-right">
                                    <small class="text-muted d-block">Dibuat: {{ $aktivitas->created_at->format('d/m/Y H:i') }}</small>
                                    <small class="text-muted d-block">Diupdate: {{ $aktivitas->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($aktivitas->prioritas === 'urgent' || $aktivitas->prioritas === 'tinggi')
                        <div class="alert alert-{{ $aktivitas->prioritas === 'urgent' ? 'danger' : 'warning' }}">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Aktivitas Prioritas {{ ucfirst($aktivitas->prioritas) }}!</strong>
                            Aktivitas ini memerlukan perhatian khusus.
                        </div>
                    @endif

                    <hr>

                    <!-- Informasi Aktivitas -->
                    <h5 class="mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Aktivitas
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%" class="font-weight-bold">Nomor Aktivitas:</td>
                                    <td>{{ $aktivitas->nomor_aktivitas }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Tanggal Aktivitas:</td>
                                    <td>{{ $aktivitas->tanggal_aktivitas ? $aktivitas->tanggal_aktivitas->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Kategori:</td>
                                    <td>
                                        @if($aktivitas->kategori_aktivitas)
                                            <span class="badge badge-secondary">{{ ucfirst($aktivitas->kategori_aktivitas) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Prioritas:</td>
                                    <td>
                                        @switch($aktivitas->prioritas)
                                            @case('urgent')
                                                <span class="badge badge-danger">Urgent</span>
                                                @break
                                            @case('tinggi')
                                                <span class="badge badge-warning">Tinggi</span>
                                                @break
                                            @case('normal')
                                                <span class="badge badge-info">Normal</span>
                                                @break
                                            @case('rendah')
                                                <span class="badge badge-success">Rendah</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">-</span>
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%" class="font-weight-bold">Vendor:</td>
                                    <td>{{ $aktivitas->vendor->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Nominal:</td>
                                    <td class="text-success font-weight-bold h5">
                                        Rp {{ number_format($aktivitas->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Dibuat Oleh:</td>
                                    <td>{{ $aktivitas->createdBy->name ?? '-' }}</td>
                                </tr>
                                @if($aktivitas->approved_by)
                                    <tr>
                                        <td class="font-weight-bold">Disetujui Oleh:</td>
                                        <td>{{ $aktivitas->approvedBy->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Persetujuan:</td>
                                        <td>{{ $aktivitas->approved_at ? $aktivitas->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Deskripsi Aktivitas:</label>
                                <div class="border rounded p-3 bg-light">
                                    {{ $aktivitas->deskripsi_aktivitas }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($aktivitas->keterangan)
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Keterangan Tambahan:</label>
                                    <div class="border rounded p-3 bg-light">
                                        {{ $aktivitas->keterangan }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Dokumen -->
                    @if($aktivitas->dokumen && count($aktivitas->dokumen) > 0)
                        <hr>
                        <h5 class="mb-3">
                            <i class="fas fa-file-alt mr-2"></i>
                            Dokumen Pendukung
                        </h5>
                        <div class="row">
                            @foreach($aktivitas->dokumen as $index => $doc)
                                <div class="col-md-2 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body p-2">
                                            <i class="fas fa-file fa-3x text-muted mb-2"></i>
                                            <div class="small">
                                                <a href="{{ $doc }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i> Unduh
                                                </a>
                                            </div>
                                            <small class="text-muted d-block mt-1">Dokumen {{ $index + 1 }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Payment Status -->
                    @if($aktivitas->status === 'approved' || $aktivitas->status === 'paid')
                        <hr>
                        <h5 class="mb-3">
                            <i class="fas fa-credit-card mr-2"></i>
                            Status Pembayaran
                        </h5>

                        @if($aktivitas->pembayaran->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Nomor Pembayaran</th>
                                            <th>Tanggal Pembayaran</th>
                                            <th>Metode</th>
                                            <th>Nominal Dibayar</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aktivitas->pembayaran as $pembayaran)
                                            <tr>
                                                <td>{{ $pembayaran->pembayaranAktivitas->nomor_pembayaran ?? '-' }}</td>
                                                <td>{{ $pembayaran->pembayaranAktivitas->tanggal_pembayaran ? $pembayaran->pembayaranAktivitas->tanggal_pembayaran->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @if($pembayaran->pembayaranAktivitas->metode_pembayaran)
                                                        <span class="badge badge-info">{{ ucfirst($pembayaran->pembayaranAktivitas->metode_pembayaran) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-success font-weight-bold">
                                                    Rp {{ number_format($pembayaran->nominal_dibayar, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $pembayaran->pembayaranAktivitas->status === 'paid' ? 'success' : ($pembayaran->pembayaranAktivitas->status === 'approved' ? 'info' : 'warning') }}">
                                                        {{ ucfirst($pembayaran->pembayaranAktivitas->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('pembayaran-aktivitas-lainnya.show', $pembayaran->pembayaran_aktivitas_id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Aktivitas ini belum memiliki pembayaran terkait.
                                @if($aktivitas->status === 'approved')
                                    <a href="{{ route('pembayaran-aktivitas-lainnya.create') }}?aktivitas={{ $aktivitas->id }}" class="btn btn-sm btn-success ml-2">
                                        <i class="fas fa-plus"></i> Buat Pembayaran
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endif

                    <!-- Action Buttons -->
                    @if($aktivitas->status !== 'paid')
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="font-weight-bold mb-3">Aksi Tersedia:</h6>
                                    <div class="btn-group" role="group">
                                        @if($aktivitas->status === 'draft')
                                            @can('aktivitas-lainnya-update')
                                                <a href="{{ route('aktivitas-lainnya.edit', $aktivitas->id) }}" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i> Edit Aktivitas
                                                </a>
                                            @endcan
                                            @can('aktivitas-lainnya-approve')
                                                <form action="{{ route('aktivitas-lainnya.approve', $aktivitas->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui aktivitas ini?')">
                                                        <i class="fas fa-check"></i> Setujui Aktivitas
                                                    </button>
                                                </form>
                                            @endcan
                                        @elseif($aktivitas->status === 'approved')
                                            @can('pembayaran-aktivitas-lainnya-create')
                                                <a href="{{ route('pembayaran-aktivitas-lainnya.create') }}?aktivitas={{ $aktivitas->id }}" class="btn btn-success">
                                                    <i class="fas fa-credit-card"></i> Buat Pembayaran
                                                </a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>Aktivitas Selesai!</strong> Aktivitas ini telah dibayar dan diselesaikan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
@can('aktivitas-lainnya-delete')
    @if($aktivitas->status === 'draft')
        <form id="deleteForm" action="{{ route('aktivitas-lainnya.destroy', $aktivitas->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endcan
@endsection

@push('styles')
<style>
    .badge-lg {
        font-size: 1rem;
    }

    .table-borderless td {
        border: none;
        padding: 0.25rem 0.75rem;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus aktivitas ini? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
