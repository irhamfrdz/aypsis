@extends('layouts.app')

@section('title', 'Aktivitas Lain-lain')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-tasks mr-2"></i>
                        Aktivitas Lain-lain
                    </h3>
                    @can('aktivitas-lainnya-create')
                        <a href="{{ route('aktivitas-lainnya.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Aktivitas
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_status">Status:</label>
                                <select id="filter_status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_kategori">Kategori:</label>
                                <select id="filter_kategori" class="form-control">
                                    <option value="">Semua Kategori</option>
                                    <option value="lainnya">Lainnya</option>
                                    <option value="operasional">Operasional</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="administrasi">Administrasi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_date_from">Tanggal Dari:</label>
                                <input type="date" id="filter_date_from" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_date_to">Tanggal Sampai:</label>
                                <input type="date" id="filter_date_to" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="search" class="form-control" placeholder="Cari berdasarkan nomor aktivitas atau deskripsi...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="search_btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-secondary" id="reset_filters">
                                <i class="fas fa-redo"></i> Reset Filter
                            </button>
                            @can('aktivitas-lainnya-export')
                                <button type="button" class="btn btn-success" id="export_excel">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="aktivitas_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Aktivitas</th>
                                    <th>Tanggal Aktivitas</th>
                                    <th>Deskripsi</th>
                                    <th>Kategori</th>
                                    <th>Vendor</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Status Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aktivitas as $index => $item)
                                    <tr>
                                        <td>{{ $aktivitas->firstItem() + $index }}</td>
                                        <td>
                                            <span class="font-weight-bold text-primary">{{ $item->nomor_aktivitas }}</span>
                                        </td>
                                        <td>{{ $item->tanggal_aktivitas->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $item->deskripsi_aktivitas }}">
                                                {{ $item->deskripsi_aktivitas }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->kategori)
                                                <span class="badge badge-info">{{ ucfirst($item->kategori) }}</span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->vendor->nama_bengkel ?? $item->vendor->nama ?? '-' }}</td>
                                        <td>
                                            <span class="font-weight-bold text-success">
                                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            @switch($item->status)
                                                @case('draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge badge-success">Approved</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                    @break
                                                @case('paid')
                                                    <span class="badge badge-primary">Paid</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($item->hasPaymentPending())
                                                <span class="badge badge-warning">Pending Payment</span>
                                            @elseif($item->status === 'paid')
                                                <span class="badge badge-success">Sudah Dibayar</span>
                                            @elseif($item->status === 'approved')
                                                <span class="badge badge-info">Siap Dibayar</span>
                                            @else
                                                <span class="badge badge-light">Belum Dibayar</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('aktivitas-lainnya-view')
                                                    <a href="{{ route('aktivitas-lainnya.show', $item) }}"
                                                       class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('aktivitas-lainnya-update')
                                                    @if(in_array($item->status, ['draft', 'rejected']))
                                                        <a href="{{ route('aktivitas-lainnya.edit', $item) }}"
                                                           class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                @endcan

                                                @can('aktivitas-lainnya-approve')
                                                    @if($item->status == 'pending')
                                                        <button type="button" class="btn btn-sm btn-success approve-btn"
                                                                data-id="{{ $item->id }}" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger reject-btn"
                                                                data-id="{{ $item->id }}" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                @endcan

                                                @can('aktivitas-lainnya-update')
                                                    @if($item->status == 'draft')
                                                        <button type="button" class="btn btn-sm btn-primary submit-btn"
                                                                data-id="{{ $item->id }}" title="Submit for Approval">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    @endif
                                                @endcan

                                                @can('aktivitas-lainnya-print')
                                                    <a href="{{ route('aktivitas-lainnya.print', $item) }}"
                                                       class="btn btn-sm btn-secondary" target="_blank" title="Print">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                @endcan

                                                @can('aktivitas-lainnya-delete')
                                                    @if($item->status == 'draft')
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                                data-id="{{ $item->id }}" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog(get_class($index), {{ $index->id }})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endcan
                                    </td></tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Tidak ada data aktivitas</h5>
                                            <p class="text-muted">Belum ada aktivitas lain-lain yang dibuat.</p>
                                            @can('aktivitas-lainnya-create')
                                                <a href="{{ route('aktivitas-lainnya.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Tambah Aktivitas Pertama
                                                </a>
                                            @endcan
                                        </td>
                                    
                                    <td>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog(get_class($index), {{ $index->id }})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endcan
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Menampilkan {{ $aktivitas->firstItem() ?? 0 }} - {{ $aktivitas->lastItem() ?? 0 }}
                                dari {{ $aktivitas->total() }} data
                            </small>
                        </div>
                        <div>
                            @include('components.modern-pagination', ['paginator' => $aktivitas])
                            @include('components.rows-per-page')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalTitle">Konfirmasi Approval</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="approvalForm">
                    <input type="hidden" id="approval_aktivitas_id">
                    <input type="hidden" id="approval_action">

                    <div class="form-group">
                        <label for="approval_notes">Catatan:</label>
                        <textarea id="approval_notes" class="form-control" rows="3" placeholder="Tambahkan catatan (opsional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmApproval">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<!-- Submit Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit for Approval</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengirim aktivitas ini untuk disetujui?</p>
                <p><strong>Catatan:</strong> Setelah disubmit, aktivitas tidak dapat diedit lagi sampai disetujui atau ditolak.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">Ya, Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus aktivitas ini?</p>
                <p><strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.75em;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: none;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
    }

    .table-responsive {
        border-radius: 0.25rem;
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let deleteId = null;
    let approvalId = null;
    let approvalAction = null;
    let submitId = null;

    // Approve button click
    $('.approve-btn').on('click', function() {
        approvalId = $(this).data('id');
        approvalAction = 'approve';
        $('#approvalModalTitle').text('Approve Aktivitas');
        $('#approval_aktivitas_id').val(approvalId);
        $('#approval_action').val(approvalAction);
        $('#confirmApproval').removeClass('btn-danger').addClass('btn-success').text('Approve');
        $('#approvalModal').modal('show');
    });

    // Reject button click
    $('.reject-btn').on('click', function() {
        approvalId = $(this).data('id');
        approvalAction = 'reject';
        $('#approvalModalTitle').text('Reject Aktivitas');
        $('#approval_aktivitas_id').val(approvalId);
        $('#approval_action').val(approvalAction);
        $('#confirmApproval').removeClass('btn-success').addClass('btn-danger').text('Reject');
        $('#approvalModal').modal('show');
    });

    // Confirm approval/rejection
    $('#confirmApproval').on('click', function() {
        const id = $('#approval_aktivitas_id').val();
        const action = $('#approval_action').val();
        const notes = $('#approval_notes').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/aktivitas-lainnya/${id}/${action}`,
            method: 'POST',
            data: {
                notes: notes
            },
            success: function(response) {
                $('#approvalModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
            }
        });
    });

    // Submit button click
    $('.submit-btn').on('click', function() {
        submitId = $(this).data('id');
        $('#submitModal').modal('show');
    });

    // Confirm submit
    $('#confirmSubmit').on('click', function() {
        if (submitId) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/aktivitas-lainnya/${submitId}/submit`,
                method: 'POST',
                success: function(response) {
                    $('#submitModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                }
            });
        }
    });

    // Delete button click
    $('.delete-btn').on('click', function() {
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    // Confirm delete
    $('#confirmDelete').on('click', function() {
        if (deleteId) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/aktivitas-lainnya/${deleteId}`,
                method: 'DELETE',
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                }
            });
        }
    });

    // Search functionality
    $('#search_btn').on('click', function() {
        performSearch();
    });

    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            performSearch();
        }
    });

    function performSearch() {
        const search = $('#search').val();
        const status = $('#filter_status').val();
        const kategori = $('#filter_kategori').val();
        const dateFrom = $('#filter_date_from').val();
        const dateTo = $('#filter_date_to').val();

        let url = new URL(window.location.href);
        url.searchParams.set('search', search);
        url.searchParams.set('status', status);
        url.searchParams.set('kategori', kategori);
        url.searchParams.set('date_from', dateFrom);
        url.searchParams.set('date_to', dateTo);

        window.location.href = url.toString();
    }

    // Reset filters
    $('#reset_filters').on('click', function() {
        let url = new URL(window.location.href);
        url.search = '';
        window.location.href = url.toString();
    });

    // Export Excel
    $('#export_excel').on('click', function() {
        const search = $('#search').val();
        const status = $('#filter_status').val();
        const kategori = $('#filter_kategori').val();
        const dateFrom = $('#filter_date_from').val();
        const dateTo = $('#filter_date_to').val();

        let url = '/aktivitas-lainnya/export?';
        url += `search=${encodeURIComponent(search)}&`;
        url += `status=${encodeURIComponent(status)}&`;
        url += `kategori=${encodeURIComponent(kategori)}&`;
        url += `date_from=${encodeURIComponent(dateFrom)}&`;
        url += `date_to=${encodeURIComponent(dateTo)}`;

        window.open(url, '_blank');
    });

    // Set current filter values from URL
    const urlParams = new URLSearchParams(window.location.search);
    $('#search').val(urlParams.get('search') || '');
    $('#filter_status').val(urlParams.get('status') || '');
    $('#filter_kategori').val(urlParams.get('kategori') || '');
    $('#filter_date_from').val(urlParams.get('date_from') || '');
    $('#filter_date_to').val(urlParams.get('date_to') || '');
});
</script>
@endpush
