@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Surat Jalan Bongkaran</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Surat Jalan Bongkaran</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">Daftar Surat Jalan Bongkaran</h4>
                        </div>
                        <div class="col-auto">
                            @can('surat-jalan-bongkaran-create')
                                <a href="{{ route('surat-jalan-bongkaran.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Tambah Surat Jalan Bongkaran
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('surat-jalan-bongkaran.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="order_id" class="form-label">Order</label>
                                <select class="form-select" id="order_id" name="order_id">
                                    <option value="">Semua Order</option>
                                    @foreach($orders as $order)
                                        <option value="{{ $order->id }}" 
                                                {{ request('order_id') == $order->id ? 'selected' : '' }}>
                                            {{ $order->nomor_order }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="kapal_id" class="form-label">Kapal</label>
                                <select class="form-select" id="kapal_id" name="kapal_id">
                                    <option value="">Semua Kapal</option>
                                    @foreach($kapals as $kapal)
                                        <option value="{{ $kapal->id }}" 
                                                {{ request('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                            {{ $kapal->nama_kapal }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-9">
                                <label for="search" class="form-label">Pencarian</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Cari nomor surat jalan, container, seal, pengirim, penerima..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <a href="{{ route('surat-jalan-bongkaran.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Surat Jalan</th>
                                    <th>Tanggal Bongkar</th>
                                    <th>Order</th>
                                    <th>Kapal</th>
                                    <th>Container</th>
                                    <th>Pengirim</th>
                                    <th>Penerima</th>
                                    <th>Status Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suratJalanBongkarans as $index => $sjb)
                                    <tr>
                                        <td>{{ $suratJalanBongkarans->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $sjb->nomor_surat_jalan }}</strong>
                                        </td>
                                        <td>{{ $sjb->tanggal_bongkar ? \Carbon\Carbon::parse($sjb->tanggal_bongkar)->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $sjb->order ? $sjb->order->nomor_order : '-' }}</td>
                                        <td>{{ $sjb->kapal ? $sjb->kapal->nama_kapal : '-' }}</td>
                                        <td>
                                            @if($sjb->nomor_container)
                                                {{ $sjb->nomor_container }}
                                                @if($sjb->ukuran_container)
                                                    <br><small class="text-muted">{{ $sjb->ukuran_container }}</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $sjb->nama_pengirim ?: '-' }}</td>
                                        <td>{{ $sjb->nama_penerima ?: '-' }}</td>
                                        <td>
                                            @if($sjb->status_pembayaran)
                                                @php
                                                    $badgeClass = match($sjb->status_pembayaran) {
                                                        'lunas' => 'bg-success',
                                                        'belum_lunas' => 'bg-warning',
                                                        'pending' => 'bg-secondary',
                                                        default => 'bg-light text-dark'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $sjb->status_pembayaran)) }}</span>
                                            @else
                                                <span class="badge bg-light text-dark">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('surat-jalan-bongkaran-view')
                                                    <a href="{{ route('surat-jalan-bongkaran.show', $sjb) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('surat-jalan-bongkaran-update')
                                                    <a href="{{ route('surat-jalan-bongkaran.edit', $sjb) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('surat-jalan-bongkaran-delete')
                                                    <form action="{{ route('surat-jalan-bongkaran.destroy', $sjb) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat jalan bongkaran ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Tidak ada surat jalan bongkaran</h5>
                                                <p class="text-muted">Belum ada data surat jalan bongkaran yang tersedia.</p>
                                                @can('surat-jalan-bongkaran-create')
                                                    <a href="{{ route('surat-jalan-bongkaran.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus me-1"></i> Tambah Surat Jalan Bongkaran Pertama
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($suratJalanBongkarans->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Menampilkan {{ $suratJalanBongkarans->firstItem() }} sampai {{ $suratJalanBongkarans->lastItem() }} 
                                dari {{ $suratJalanBongkarans->total() }} data
                            </div>
                            <nav>
                                {{ $suratJalanBongkarans->appends(request()->query())->links() }}
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto submit form when date changes
    $('#start_date, #end_date, #order_id, #kapal_id').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush