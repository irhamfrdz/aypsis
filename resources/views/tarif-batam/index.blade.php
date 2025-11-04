@extends('layouts.main')

@section('title', 'Tarif Batam')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">💰 Tarif Batam</h1>
                    <p class="text-muted">Kelola tarif pengiriman untuk wilayah Batam</p>
                </div>
                @can('tarif-batam.create')
                    <a href="{{ route('tarif-batam.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Tarif
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tarif-batam.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="masa_berlaku" class="form-label">Masa Berlaku</label>
                    <select name="masa_berlaku" id="masa_berlaku" class="form-select">
                        <option value="">Semua</option>
                        <option value="berlaku" {{ request('masa_berlaku') == 'berlaku' ? 'selected' : '' }}>Masih Berlaku</option>
                        <option value="expired" {{ request('masa_berlaku') == 'expired' ? 'selected' : '' }}>Sudah Expired</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Cari berdasarkan keterangan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('tarif-batam.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Tarif Batam</h6>
            <div class="d-flex align-items-center">
                @include('components.rows-per-page', [
                    'route' => 'tarif-batam.index',
                    'current' => request('per_page', 10)
                ])
            </div>
        </div>
        <div class="card-body p-0">
            @if($tarifBatam->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 120px;">Chasis AYP</th>
                                <th style="width: 120px;">20ft Full</th>
                                <th style="width: 120px;">20ft Empty</th>
                                <th style="width: 120px;">Antar Lokasi</th>
                                <th style="width: 120px;">40ft Full</th>
                                <th style="width: 120px;">40ft Empty</th>
                                <th style="width: 120px;">40ft Antar Lokasi</th>
                                <th style="width: 120px;">Masa Berlaku</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tarifBatam as $index => $tarif)
                                <tr>
                                    <td class="text-center">
                                        {{ ($tarifBatam->currentPage() - 1) * $tarifBatam->perPage() + $index + 1 }}
                                    </td>
                                    <td class="text-end">
                                        {{ $tarif->chasis_ayp ? 'Rp ' . $tarif->formatted_chasis_ayp : '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $tarif->{'20ft_full'} ? 'Rp ' . $tarif->formatted_20ft_full : '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $tarif->{'20ft_empty'} ? 'Rp ' . $tarif->formatted_20ft_empty : '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $tarif->antar_lokasi ? 'Rp ' . $tarif->formatted_antar_lokasi : '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $tarif->{'40ft_full'} ? 'Rp ' . $tarif->formatted_40ft_full : '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $tarif->{'40ft_empty'} ? 'Rp ' . $tarif->formatted_40ft_empty : '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $tarif->{'40ft_antar_lokasi'} ? 'Rp ' . $tarif->formatted_40ft_antar_lokasi : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $tarif->masa_berlaku >= now()->toDateString() ? 'bg-success' : 'bg-danger' }}">
                                            {{ \Carbon\Carbon::parse($tarif->masa_berlaku)->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $tarif->status === 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($tarif->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @can('tarif-batam.view')
                                                <a href="{{ route('tarif-batam.show', $tarif) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('tarif-batam.edit')
                                                <a href="{{ route('tarif-batam.edit', $tarif) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('tarif-batam.delete')
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        title="Hapus" onclick="confirmDelete({{ $tarif->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($tarifBatam->hasPages())
                    @include('components.modern-pagination', ['paginator' => $tarifBatam])
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada data tarif batam</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'masa_berlaku', 'search']))
                            Tidak ditemukan data dengan filter yang diterapkan.
                        @else
                            Belum ada tarif batam yang ditambahkan.
                        @endif
                    </p>
                    @can('tarif-batam.create')
                        <a href="{{ route('tarif-batam.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Tarif Pertama
                        </a>
                    @endcan
                </div>
            @endif
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
                <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" class="d-inline">
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
function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `{{ route('tarif-batam.index') }}/${id}`;
    modal.show();
}
</script>
@endpush