@extends('layouts.app')

@section('title', 'Outstanding Orders')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Outstanding Orders</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('outstanding.export', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download fa-sm text-white-50"></i>
                Export Excel
            </a>
            <button class="btn btn-primary btn-sm" id="refreshStats">
                <i class="fas fa-sync-alt fa-sm text-white-50"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" id="statsContainer">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingCount">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Partial Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="partialCount">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedCount">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Outstanding
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="outstandingCount">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filter Outstanding Orders</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#filterCollapse">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form id="filterForm" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="term_id">Term</label>
                                <select name="term_id" id="term_id" class="form-control">
                                    <option value="">All Terms</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                            {{ $term->nama_term }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="pengirim_id">Pengirim</label>
                                <select name="pengirim_id" id="pengirim_id" class="form-control">
                                    <option value="">All Pengirim</option>
                                    @foreach($pengirims as $pengirim)
                                        <option value="{{ $pengirim->id }}" {{ request('pengirim_id') == $pengirim->id ? 'selected' : '' }}>
                                            {{ $pengirim->nama_pengirim }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="completion_percentage">Min. Completion %</label>
                                <input type="number" name="completion_percentage" id="completion_percentage"
                                       class="form-control" min="0" max="100"
                                       value="{{ request('completion_percentage') }}" placeholder="0-100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_from">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_to">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="form-control"
                                       value="{{ request('search') }}" placeholder="Order number, container...">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('outstanding.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Outstanding Orders Data</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="outstandingTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Term</th>
                            <th>Pengirim</th>
                            <th>Jenis Barang</th>
                            <th>Units</th>
                            <th>Sisa</th>
                            <th>Completion</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr data-order-id="{{ $order->id }}">
                            <td>
                                <strong>{{ $order->nomor_order }}</strong>
                                @if($order->no_kontainer)
                                    <br><small class="text-muted">{{ $order->no_kontainer }}</small>
                                @endif
                            </td>
                            <td>{{ $order->term->nama_term ?? '-' }}</td>
                            <td>{{ $order->pengirim->nama_pengirim ?? '-' }}</td>
                            <td>{{ $order->jenisBarang->nama_jenis ?? '-' }}</td>
                            <td>
                                <span class="badge badge-info">{{ number_format($order->units, 0) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-warning">{{ number_format($order->sisa, 0) }}</span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $order->completion_percentage }}%"
                                         aria-valuenow="{{ $order->completion_percentage }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($order->completion_percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>{!! $order->outstanding_status_badge !!}</td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-primary btn-sm" onclick="processUnits({{ $order->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No outstanding orders found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }}
                    of {{ $orders->total() }} results
                </div>
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Process Units Modal -->
<div class="modal fade" id="processUnitsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Units</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="processUnitsForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Order Number</label>
                        <input type="text" id="modalOrderNumber" class="form-control" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Units</label>
                                <input type="number" id="modalTotalUnits" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Current Remaining</label>
                                <input type="number" id="modalCurrentSisa" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Process Units <span class="text-danger">*</span></label>
                        <input type="number" id="processedUnits" name="processed_units"
                               class="form-control" min="1" required>
                        <small class="form-text text-muted">Enter number of units to process</small>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea id="processNotes" name="notes" class="form-control" rows="3"
                                  placeholder="Optional notes about this processing..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Process Units
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load statistics
    loadStatistics();

    // Auto-refresh stats every 30 seconds
    setInterval(loadStatistics, 30000);

    // Manual refresh button
    $('#refreshStats').click(function() {
        loadStatistics();
    });
});

function loadStatistics() {
    $.get('{{ route("outstanding.stats") }}', function(data) {
        $('#pendingCount').html(data.pending);
        $('#partialCount').html(data.partial);
        $('#completedCount').html(data.completed);
        $('#outstandingCount').html(data.total_outstanding);
    }).fail(function() {
        // Show error state
        $('.h5').html('<i class="fas fa-exclamation-triangle text-danger"></i>');
    });
}

function processUnits(orderId) {
    // Get order details via AJAX
    $.get(`/outstanding/${orderId}/details`, function(data) {
        $('#modalOrderNumber').val(data.nomor_order);
        $('#modalTotalUnits').val(data.units);
        $('#modalCurrentSisa').val(data.sisa);
        $('#processedUnits').attr('max', data.sisa);
        $('#processUnitsModal').modal('show');

        // Store order ID for processing
        $('#processUnitsForm').data('order-id', orderId);
    });
}

$('#processUnitsForm').submit(function(e) {
    e.preventDefault();

    const orderId = $(this).data('order-id');
    const processedUnits = $('#processedUnits').val();
    const notes = $('#processNotes').val();

    if (!processedUnits || processedUnits <= 0) {
        alert('Please enter a valid number of units to process');
        return;
    }

    // Disable form during processing
    $('#processUnitsForm button').prop('disabled', true);

    $.ajax({
        url: `/outstanding/${orderId}/process`,
        method: 'POST',
        data: {
            processed_units: processedUnits,
            notes: notes,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Update the row in table
                const row = $(`tr[data-order-id="${orderId}"]`);
                row.find('td:nth-child(6) .badge').text(new Intl.NumberFormat().format(response.order.sisa));
                row.find('.progress-bar').css('width', response.order.completion_percentage + '%')
                    .text(response.order.completion_percentage.toFixed(1) + '%');
                row.find('td:nth-child(8)').html(response.order.status_badge);

                // Close modal and reset form
                $('#processUnitsModal').modal('hide');
                $('#processUnitsForm')[0].reset();

                // Refresh statistics
                loadStatistics();

                // Show success message
                showAlert('success', 'Units processed successfully!');
            } else {
                showAlert('danger', response.message || 'Failed to process units');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert('danger', response?.message || 'Error processing units');
        },
        complete: function() {
            $('#processUnitsForm button').prop('disabled', false);
        }
    });
});

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // Remove existing alerts
    $('.alert').remove();

    // Add new alert at top of container
    $('.container-fluid').prepend(alertHtml);

    // Auto-remove after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush
