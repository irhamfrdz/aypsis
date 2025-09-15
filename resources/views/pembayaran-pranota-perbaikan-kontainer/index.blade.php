@extends('layouts.app')

@section('title', 'Pembayaran Pranota Perbaikan Kontainer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pembayaran Pranota Perbaikan Kontainer</h3>
                    <div class="card-tools">
                        @can('pembayaran-pranota-perbaikan-kontainer.create')
                        <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Pembayaran
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="pembayaranTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pranota</th>
                                    <th>Tanggal Pembayaran</th>
                                    <th>Nominal</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembayaranPranotaPerbaikanKontainers as $pembayaran)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $pembayaran->pranotaPerbaikanKontainer->perbaikanKontainer->kontainer->nomor_kontainer ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($pembayaran->pranotaPerbaikanKontainer->deskripsi_pekerjaan, 50) }}</small>
                                    </td>
                                    <td>{{ $pembayaran->tanggal_pembayaran->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucfirst($pembayaran->metode_pembayaran) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $pembayaran->status_pembayaran == 'paid' ? 'success' : 'warning' }}">
                                            {{ $pembayaran->status_pembayaran == 'paid' ? 'Lunas' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @can('pembayaran-pranota-perbaikan-kontainer.show')
                                            <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.show', $pembayaran) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('pembayaran-pranota-perbaikan-kontainer.edit')
                                            <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.edit', $pembayaran) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('pembayaran-pranota-perbaikan-kontainer.delete')
                                            <form action="{{ route('pembayaran-pranota-perbaikan-kontainer.destroy', $pembayaran) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#pembayaranTable').DataTable({
        "responsive": true,
        "autoWidth": false,
    });
});
</script>
@endsection
