@extends('layouts.app')

@section('title', 'Daftar Tagihan OB')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-ship me-2"></i>
                            Daftar Tagihan OB (On Board)
                        </h5>
                        @can('tagihan-ob-create')
                            <a href="{{ route('tagihan-ob.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Tambah Tagihan OB
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filter & Search -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari kapal, voyage, kontainer..."
                                       id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="full">Full</option>
                                <option value="empty">Empty</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="pembayaranFilter">
                                <option value="">Status Pembayaran</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kapal</th>
                                    <th>Voyage</th>
                                    <th>No. Kontainer</th>
                                    <th>Nama Supir</th>
                                    <th>Barang</th>
                                    <th>Status</th>
                                    <th>Biaya</th>
                                    <th>Status Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tagihanOb as $index => $item)
                                    <tr>
                                        <td>{{ $tagihanOb->firstItem() + $index }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $item->kapal }}</td>
                                        <td>{{ $item->voyage }}</td>
                                        <td>
                                            <code>{{ $item->nomor_kontainer }}</code>
                                        </td>
                                        <td>{{ $item->nama_supir }}</td>
                                        <td>{{ Str::limit($item->barang, 30) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->status_kontainer === 'full' ? 'success' : 'warning' }}">
                                                {{ ucfirst($item->status_kontainer) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>Rp {{ number_format($item->biaya, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $item->status_pembayaran === 'paid' ? 'success' : ($item->status_pembayaran === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($item->status_pembayaran) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('tagihan-ob-view')
                                                    <a href="{{ route('tagihan-ob.show', $item) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('tagihan-ob-update')
                                                    <a href="{{ route('tagihan-ob.edit', $item) }}" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('tagihan-ob-delete')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Hapus"
                                                            onclick="confirmDelete({{ $item->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada data tagihan OB</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Menampilkan {{ $tagihanOb->firstItem() ?? 0 }} - {{ $tagihanOb->lastItem() ?? 0 }} 
                            dari {{ $tagihanOb->total() }} data
                        </div>
                        {{ $tagihanOb->links() }}
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
                <p class="text-danger"><small>Data yang sudah dihapus tidak dapat dikembalikan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/tagihan-ob/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const table = document.querySelector('tbody');
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Status filters
document.getElementById('statusFilter').addEventListener('change', function() {
    filterTable();
});

document.getElementById('pembayaranFilter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const pembayaranFilter = document.getElementById('pembayaranFilter').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const statusText = row.querySelector('td:nth-child(8) span')?.textContent.toLowerCase() || '';
        const pembayaranText = row.querySelector('td:nth-child(10) span')?.textContent.toLowerCase() || '';
        
        const statusMatch = !statusFilter || statusText.includes(statusFilter);
        const pembayaranMatch = !pembayaranFilter || pembayaranText.includes(pembayaranFilter);
        
        row.style.display = (statusMatch && pembayaranMatch) ? '' : 'none';
    });
}
</script>
@endpush