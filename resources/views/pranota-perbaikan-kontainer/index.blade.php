@extends('layouts.app')

@section('title', 'Daftar Pranota Perbaikan Kontainer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pranota Perbaikan Kontainer</h3>
                    <div class="card-tools">
                        @can('pranota-perbaikan-kontainer.create')
                        <a href="{{ route('pranota-perbaikan-kontainer.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Pranota
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="pranotaTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Perbaikan Kontainer</th>
                                    <th>Tanggal Pranota</th>
                                    <th>Nama Teknisi</th>
                                    <th>Estimasi Biaya</th>
                                    <th>Estimasi Waktu</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pranotaPerbaikanKontainers as $pranota)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $pranota->perbaikanKontainer->kontainer->nomor_kontainer ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($pranota->perbaikanKontainer->deskripsi_perbaikan, 50) }}</small>
                                    </td>
                                    <td>{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                                    <td>{{ $pranota->nama_teknisi }}</td>
                                    <td>Rp {{ number_format($pranota->estimasi_biaya, 0, ',', '.') }}</td>
                                    <td>{{ $pranota->estimasi_waktu }} jam</td>
                                    <td>
                                        <span class="badge badge-{{ $pranota->status == 'approved' ? 'success' : ($pranota->status == 'draft' ? 'secondary' : 'warning') }}">
                                            {{ $pranota->status == 'draft' ? 'Draft' : ($pranota->status == 'approved' ? 'Disetujui' : 'Dalam Proses') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @can('pranota-perbaikan-kontainer.show')
                                            <a href="{{ route('pranota-perbaikan-kontainer.show', $pranota) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('pranota-perbaikan-kontainer.edit')
                                            <a href="{{ route('pranota-perbaikan-kontainer.edit', $pranota) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('pranota-perbaikan-kontainer.delete')
                                            <form action="{{ route('pranota-perbaikan-kontainer.destroy', $pranota) }}" method="POST" class="d-inline">
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
    $('#pranotaTable').DataTable({
        "responsive": true,
        "autoWidth": false,
    });
});
</script>
@endsection
