@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('master-kapal.index') }}">Master Kapal</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Kapal</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Tambah Kapal Baru</h1>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Kapal</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('master-kapal.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode') is-invalid @enderror" 
                                   id="kode" name="kode" value="{{ old('kode') }}" 
                                   placeholder="Masukkan kode kapal" required>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Kode unik untuk identifikasi kapal (maks. 50 karakter)</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode_kapal" class="form-label">Kode Kapal</label>
                            <input type="text" class="form-control @error('kode_kapal') is-invalid @enderror" 
                                   id="kode_kapal" name="kode_kapal" value="{{ old('kode_kapal') }}" 
                                   placeholder="Masukkan kode alternatif kapal">
                            @error('kode_kapal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Kode alternatif/tambahan (opsional, maks. 100 karakter)</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nama_kapal" class="form-label">Nama Kapal <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_kapal') is-invalid @enderror" 
                           id="nama_kapal" name="nama_kapal" value="{{ old('nama_kapal') }}" 
                           placeholder="Masukkan nama kapal" required>
                    @error('nama_kapal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi</label>
                    <input type="text" class="form-control @error('lokasi') is-invalid @enderror" 
                           id="lokasi" name="lokasi" value="{{ old('lokasi') }}" 
                           placeholder="Masukkan lokasi kapal (pelabuhan, dermaga, dll)">
                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="form-control @error('catatan') is-invalid @enderror" 
                              id="catatan" name="catatan" rows="4" 
                              placeholder="Masukkan catatan tambahan tentang kapal">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('master-kapal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
