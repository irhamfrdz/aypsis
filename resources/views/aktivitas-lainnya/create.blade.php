@extends('layouts.app')

@section('title', 'Tambah Aktivitas Lain-lain')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Aktivitas Lain-lain
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('aktivitas-lainnya.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <form action="{{ route('aktivitas-lainnya.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_aktivitas">Tanggal Aktivitas <span class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control @error('tanggal_aktivitas') is-invalid @enderror"
                                           id="tanggal_aktivitas"
                                           name="tanggal_aktivitas"
                                           value="{{ old('tanggal_aktivitas', date('Y-m-d')) }}"
                                           required>
                                    @error('tanggal_aktivitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kategori">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control @error('kategori') is-invalid @enderror"
                                            id="kategori"
                                            name="kategori"
                                            required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        <option value="operasional" {{ old('kategori') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                                        <option value="maintenance" {{ old('kategori') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="administrasi" {{ old('kategori') == 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                                    </select>
                                    @error('kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi_aktivitas">Deskripsi Aktivitas <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi_aktivitas') is-invalid @enderror"
                                      id="deskripsi_aktivitas"
                                      name="deskripsi_aktivitas"
                                      rows="4"
                                      placeholder="Jelaskan detail aktivitas yang akan dilakukan..."
                                      required>{{ old('deskripsi_aktivitas') }}</textarea>
                            @error('deskripsi_aktivitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_id">Vendor (Opsional)</label>
                                    <select class="form-control @error('vendor_id') is-invalid @enderror"
                                            id="vendor_id"
                                            name="vendor_id">
                                        <option value="">Pilih Vendor (Jika Ada)</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="akun_coa_id">Akun Bank/Kas <span class="text-danger">*</span></label>
                                    <select class="form-control @error('akun_coa_id') is-invalid @enderror"
                                            id="akun_coa_id"
                                            name="akun_coa_id"
                                            required>
                                        <option value="">Pilih Akun Bank/Kas</option>
                                        @foreach($bankAccounts as $bank)
                                            <option value="{{ $bank->id }}" {{ old('akun_coa_id') == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->nomor_akun }} - {{ $bank->nama_akun }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('akun_coa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Pilih akun bank/kas yang akan terpengaruh oleh transaksi ini
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipe_transaksi">Tipe Transaksi <span class="text-danger">*</span></label>
                                    <select class="form-control @error('tipe_transaksi') is-invalid @enderror"
                                            id="tipe_transaksi"
                                            name="tipe_transaksi"
                                            required>
                                        <option value="">Pilih Tipe Transaksi</option>
                                        <option value="debit" {{ old('tipe_transaksi') == 'debit' ? 'selected' : '' }}>Debit (Pemasukan)</option>
                                        <option value="kredit" {{ old('tipe_transaksi', 'kredit') == 'kredit' ? 'selected' : '' }}>Kredit (Pengeluaran)</option>
                                    </select>
                                    @error('tipe_transaksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Debit untuk pemasukan/pendapatan, Kredit untuk pengeluaran/biaya
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nominal">Nominal <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text"
                                               class="form-control @error('nominal') is-invalid @enderror"
                                               id="nominal"
                                               name="nominal"
                                               value="{{ old('nominal') }}"
                                               placeholder="0"
                                               required>
                                    </div>
                                    @error('nominal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan Tambahan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                      id="keterangan"
                                      name="keterangan"
                                      rows="3"
                                      placeholder="Keterangan atau catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" name="action" value="save" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan sebagai Draft
                                </button>
                                <button type="submit" name="action" value="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Simpan & Submit untuk Approval
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('aktivitas-lainnya.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .required {
        color: #e3342f;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Format number input for nominal
    $('#nominal').on('input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        let formattedValue = new Intl.NumberFormat('id-ID').format(value);
        $(this).val(formattedValue);
    });

    // Remove formatting before form submission
    $('form').on('submit', function() {
        let nominal = $('#nominal').val().replace(/[^\d]/g, '');
        $('#nominal').val(nominal);
    });

    // Auto-resize textarea
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Validate form before submission
    $('form').on('submit', function(e) {
        let isValid = true;
        let errors = [];

        // Validate required fields
        $('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                errors.push('Field ' + $(this).attr('name') + ' wajib diisi');
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Validate nominal
        let nominal = $('#nominal').val().replace(/[^\d]/g, '');
        if (nominal == '' || nominal == '0') {
            isValid = false;
            errors.push('Nominal harus lebih dari 0');
            $('#nominal').addClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi:\n' + errors.join('\n'));
        }
    });

    // Clear validation on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endpush
