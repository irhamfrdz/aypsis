@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('master-kapal.index') }}">Master Kapal</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail Kapal</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Detail Kapal</h1>
                <div>
                    @can('master-kapal.edit')
                    <a href="{{ route('master-kapal.edit', $masterKapal->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('master-kapal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Kapal</h6>
            @if($masterKapal->status == 'aktif')
                <span class="badge bg-success fs-6">Aktif</span>
            @else
                <span class="badge bg-secondary fs-6">Nonaktif</span>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td width="40%" class="fw-bold text-muted">ID</td>
                                <td width="5%">:</td>
                                <td>{{ $masterKapal->id }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Kode</td>
                                <td>:</td>
                                <td><span class="badge bg-primary">{{ $masterKapal->kode }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Kode Kapal</td>
                                <td>:</td>
                                <td>{{ $masterKapal->kode_kapal ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Nama Kapal</td>
                                <td>:</td>
                                <td><strong>{{ $masterKapal->nama_kapal }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Lokasi</td>
                                <td>:</td>
                                <td>
                                    @if($masterKapal->lokasi)
                                        <i class="fas fa-map-marker-alt text-danger"></i> {{ $masterKapal->lokasi }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td width="40%" class="fw-bold text-muted">Status</td>
                                <td width="5%">:</td>
                                <td>
                                    @if($masterKapal->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Dibuat Tanggal</td>
                                <td>:</td>
                                <td>{{ $masterKapal->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Diperbarui Tanggal</td>
                                <td>:</td>
                                <td>{{ $masterKapal->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @if($masterKapal->deleted_at)
                            <tr>
                                <td class="fw-bold text-muted">Dihapus Tanggal</td>
                                <td>:</td>
                                <td class="text-danger">{{ $masterKapal->deleted_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            @if($masterKapal->catatan)
            <hr class="my-4">
            <div class="row">
                <div class="col-12">
                    <h6 class="fw-bold text-muted mb-3">Catatan</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $masterKapal->catatan }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @can('master-kapal.delete')
    <div class="card shadow border-danger mb-4">
        <div class="card-header bg-danger text-white py-3">
            <h6 class="m-0 font-weight-bold">Danger Zone</h6>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Hapus Kapal</h6>
                    <p class="text-muted mb-0">Setelah dihapus, data kapal ini akan dipindahkan ke tempat sampah dan dapat dipulihkan.</p>
                </div>
                <form action="{{ route('master-kapal.destroy', $masterKapal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kapal ini?\n\nKode: {{ $masterKapal->kode }}\nNama: {{ $masterKapal->nama_kapal }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus Kapal
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
