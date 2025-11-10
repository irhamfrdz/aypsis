@extends('layouts.app')

@section('title', 'Tambah Tagihan OB')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Tagihan OB (On Board)
                        </h5>
                        <a href="{{ route('tagihan-ob.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tagihan-ob.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kapal" class="form-label">Nama Kapal <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('kapal') is-invalid @enderror" 
                                           id="kapal" 
                                           name="kapal" 
                                           value="{{ old('kapal') }}" 
                                           required>
                                    @error('kapal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="voyage" class="form-label">Voyage <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('voyage') is-invalid @enderror" 
                                           id="voyage" 
                                           name="voyage" 
                                           value="{{ old('voyage') }}" 
                                           required>
                                    @error('voyage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nomor_kontainer" class="form-label">Nomor Kontainer <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nomor_kontainer') is-invalid @enderror" 
                                           id="nomor_kontainer" 
                                           name="nomor_kontainer" 
                                           value="{{ old('nomor_kontainer') }}" 
                                           placeholder="Contoh: GESU1234567"
                                           required>
                                    @error('nomor_kontainer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nama_supir" class="form-label">Nama Supir <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_supir') is-invalid @enderror" 
                                           id="nama_supir" 
                                           name="nama_supir" 
                                           value="{{ old('nama_supir') }}" 
                                           required>
                                    @error('nama_supir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barang" class="form-label">Jenis Barang <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('barang') is-invalid @enderror" 
                                           id="barang" 
                                           name="barang" 
                                           value="{{ old('barang') }}" 
                                           required>
                                    @error('barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status_kontainer" class="form-label">Status Kontainer <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status_kontainer') is-invalid @enderror" 
                                            id="status_kontainer" 
                                            name="status_kontainer" 
                                            required>
                                        <option value="">Pilih Status</option>
                                        <option value="full" {{ old('status_kontainer') === 'full' ? 'selected' : '' }}>
                                            Full (Tarik Isi)
                                        </option>
                                        <option value="empty" {{ old('status_kontainer') === 'empty' ? 'selected' : '' }}>
                                            Empty (Tarik Kosong)
                                        </option>
                                    </select>
                                    @error('status_kontainer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            Full = Tarik Isi, Empty = Tarik Kosong
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="bl_id" class="form-label">Bill of Lading (BL)</label>
                                    <select class="form-select @error('bl_id') is-invalid @enderror" 
                                            id="bl_id" 
                                            name="bl_id">
                                        <option value="">Pilih BL (Opsional)</option>
                                        @foreach($bls as $bl)
                                            <option value="{{ $bl->id }}" {{ old('bl_id') == $bl->id ? 'selected' : '' }}>
                                                {{ $bl->nomor_bl }} - {{ $bl->kapal }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bl_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                              id="keterangan" 
                                              name="keterangan" 
                                              rows="3" 
                                              placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Info Biaya -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calculator fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Informasi Biaya</h6>
                                            <p class="mb-0">Biaya akan dihitung otomatis berdasarkan Master Pricelist OB sesuai dengan status kontainer yang dipilih.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('tagihan-ob.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Tagihan OB
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-fill data when BL is selected
document.getElementById('bl_id').addEventListener('change', function() {
    const blId = this.value;
    if (blId) {
        // You can add AJAX call here to fetch BL data and auto-fill form fields
        // For now, this is just a placeholder for future enhancement
        console.log('Selected BL ID:', blId);
    }
});

// Form validation enhancement
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['kapal', 'voyage', 'nomor_kontainer', 'nama_supir', 'barang', 'status_kontainer'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi.');
    }
});
</script>
@endpush