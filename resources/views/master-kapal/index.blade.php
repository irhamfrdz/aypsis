@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Master Kapal</h1>
                @can('master-kapal.create')
                <a href="{{ route('master-kapal.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kapal
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kapal</h6>
        </div>
        <div class="card-body">
            <!-- Filter & Search -->
            <form method="GET" action="{{ route('master-kapal.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari kode, nama, atau lokasi..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('master-kapal.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Kode</th>
                            <th width="12%">Kode Kapal</th>
                            <th width="20%">Nama Kapal</th>
                            <th width="18%">Lokasi</th>
                            <th width="15%">Catatan</th>
                            <th width="8%">Status</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kapals as $kapal)
                        <tr>
                            <td class="text-center">{{ ($kapals->currentPage() - 1) * $kapals->perPage() + $loop->iteration }}</td>
                            <td><strong>{{ $kapal->kode }}</strong></td>
                            <td>{{ $kapal->kode_kapal ?? '-' }}</td>
                            <td>{{ $kapal->nama_kapal }}</td>
                            <td>{{ $kapal->lokasi ?? '-' }}</td>
                            <td>
                                @if($kapal->catatan)
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;" data-bs-toggle="tooltip" title="{{ $kapal->catatan }}">
                                        {{ $kapal->catatan }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($kapal->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('master-kapal.view')
                                    <a href="{{ route('master-kapal.show', $kapal->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('master-kapal.edit')
                                    <a href="{{ route('master-kapal.edit', $kapal->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('master-kapal.delete')
                                    <form action="{{ route('master-kapal.destroy', $kapal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kapal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data kapal.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $kapals->firstItem() ?? 0 }} sampai {{ $kapals->lastItem() ?? 0 }} dari {{ $kapals->total() }} data
                </div>
                <div>
                    {{ $kapals->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush
@endsection
