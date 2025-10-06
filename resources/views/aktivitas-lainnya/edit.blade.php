@extends('layouts.app')

@section('title', 'Edit Aktivitas Lain-lain')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Aktivitas Lain-lain
                        @if($aktivitas->nomor_aktivitas)
                            <span class="badge badge-info ml-2">{{ $aktivitas->nomor_aktivitas }}</span>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('aktivitas-lainnya.show', $aktivitas->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <a href="{{ route('aktivitas-lainnya.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                @if($aktivitas->status !== 'draft')
                    <div class="alert alert-warning mx-3 mt-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Aktivitas dengan status {{ ucfirst($aktivitas->status) }} memiliki batasan dalam pengeditan.
                    </div>
                @endif

                <form action="{{ route('aktivitas-lainnya.update', $aktivitas->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_aktivitas">Nomor Aktivitas</label>
                                    <input type="text"
                                           class="form-control bg-light"
                                           id="nomor_aktivitas"
                                           value="{{ $aktivitas->nomor_aktivitas }}"
                                           readonly>
                                    <small class="text-muted">Nomor aktivitas dibuat otomatis oleh sistem</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_aktivitas">Tanggal Aktivitas <span class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control @error('tanggal_aktivitas') is-invalid @enderror"
                                           id="tanggal_aktivitas"
                                           name="tanggal_aktivitas"
                                           value="{{ old('tanggal_aktivitas', $aktivitas->tanggal_aktivitas ? $aktivitas->tanggal_aktivitas->format('Y-m-d') : '') }}"
                                           {{ $aktivitas->status === 'paid' ? 'readonly' : '' }}
                                           required>
                                    @error('tanggal_aktivitas')
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
                                      rows="3"
                                      {{ $aktivitas->status === 'paid' ? 'readonly' : '' }}
                                      placeholder="Deskripsi detail aktivitas yang dilakukan..."
                                      required>{{ old('deskripsi_aktivitas', $aktivitas->deskripsi_aktivitas) }}</textarea>
                            @error('deskripsi_aktivitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_id">Vendor</label>
                                    <select class="form-control @error('vendor_id') is-invalid @enderror"
                                            id="vendor_id"
                                            name="vendor_id"
                                            {{ $aktivitas->status === 'paid' ? 'disabled' : '' }}>
                                        <option value="">Pilih Vendor (Opsional)</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}"
                                                    {{ old('vendor_id', $aktivitas->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($aktivitas->status === 'paid')
                                        <input type="hidden" name="vendor_id" value="{{ $aktivitas->vendor_id }}">
                                    @endif
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kategori_aktivitas">Kategori Aktivitas</label>
                                    <select class="form-control @error('kategori_aktivitas') is-invalid @enderror"
                                            id="kategori_aktivitas"
                                            name="kategori_aktivitas"
                                            {{ $aktivitas->status === 'paid' ? 'disabled' : '' }}>
                                        <option value="">Pilih Kategori (Opsional)</option>
                                        <option value="operasional" {{ old('kategori_aktivitas', $aktivitas->kategori_aktivitas) == 'operasional' ? 'selected' : '' }}>Operasional</option>
                                        <option value="maintenance" {{ old('kategori_aktivitas', $aktivitas->kategori_aktivitas) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="administrasi" {{ old('kategori_aktivitas', $aktivitas->kategori_aktivitas) == 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                                        <option value="emergency" {{ old('kategori_aktivitas', $aktivitas->kategori_aktivitas) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                        <option value="lainnya" {{ old('kategori_aktivitas', $aktivitas->kategori_aktivitas) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @if($aktivitas->status === 'paid')
                                        <input type="hidden" name="kategori_aktivitas" value="{{ $aktivitas->kategori_aktivitas }}">
                                    @endif
                                    @error('kategori_aktivitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                            {{ $aktivitas->status === 'paid' ? 'disabled' : '' }}
                                            required>
                                        <option value="">Pilih Tipe Transaksi</option>
                                        <option value="debit" {{ old('tipe_transaksi', $aktivitas->tipe_transaksi ?? 'kredit') == 'debit' ? 'selected' : '' }}>Debit (Pemasukan)</option>
                                        <option value="kredit" {{ old('tipe_transaksi', $aktivitas->tipe_transaksi ?? 'kredit') == 'kredit' ? 'selected' : '' }}>Kredit (Pengeluaran)</option>
                                    </select>
                                    @if($aktivitas->status === 'paid')
                                        <input type="hidden" name="tipe_transaksi" value="{{ $aktivitas->tipe_transaksi }}">
                                    @endif
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
                                               value="{{ old('nominal', number_format($aktivitas->nominal, 0, ',', '.')) }}"
                                               {{ $aktivitas->status === 'paid' ? 'readonly' : '' }}
                                               placeholder="0"
                                               required>
                                    </div>
                                    @error('nominal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prioritas">Prioritas</label>
                                    <select class="form-control @error('prioritas') is-invalid @enderror"
                                            id="prioritas"
                                            name="prioritas"
                                            {{ $aktivitas->status === 'paid' ? 'disabled' : '' }}>
                                        <option value="rendah" {{ old('prioritas', $aktivitas->prioritas) == 'rendah' ? 'selected' : '' }}>Rendah</option>
                                        <option value="normal" {{ old('prioritas', $aktivitas->prioritas ?? 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="tinggi" {{ old('prioritas', $aktivitas->prioritas) == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                        <option value="urgent" {{ old('prioritas', $aktivitas->prioritas) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @if($aktivitas->status === 'paid')
                                        <input type="hidden" name="prioritas" value="{{ $aktivitas->prioritas }}">
                                    @endif
                                    @error('prioritas')
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
                                      {{ $aktivitas->status === 'paid' ? 'readonly' : '' }}
                                      placeholder="Keterangan atau catatan tambahan (opsional)">{{ old('keterangan', $aktivitas->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($aktivitas->status !== 'paid')
                            <div class="form-group">
                                <label for="dokumen">Upload Dokumen Pendukung</label>
                                <input type="file"
                                       class="form-control-file @error('dokumen') is-invalid @enderror"
                                       id="dokumen"
                                       name="dokumen[]"
                                       multiple
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted">
                                    Format yang diizinkan: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX. Maksimal 5MB per file.
                                </small>
                                @error('dokumen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if($aktivitas->dokumen && count($aktivitas->dokumen) > 0)
                            <div class="form-group">
                                <label>Dokumen Saat Ini:</label>
                                <div class="row">
                                    @foreach($aktivitas->dokumen as $index => $doc)
                                        <div class="col-md-3 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2 text-center">
                                                    <i class="fas fa-file fa-2x text-muted mb-2"></i>
                                                    <div class="small">
                                                        <a href="{{ $doc }}" target="_blank" class="text-primary">
                                                            Dokumen {{ $index + 1 }}
                                                        </a>
                                                    </div>
                                                    @if($aktivitas->status === 'draft')
                                                        <button type="button" class="btn btn-sm btn-danger mt-1"
                                                                onclick="removeDocument({{ $index }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <input type="hidden" name="existing_dokumen[]" value="{{ $doc }}">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Status info -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-left-{{ $aktivitas->status === 'paid' ? 'success' : ($aktivitas->status === 'approved' ? 'info' : 'warning') }}">
                                    <div class="card-body py-2">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <strong>Status Saat Ini:</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge badge-{{ $aktivitas->status === 'paid' ? 'success' : ($aktivitas->status === 'approved' ? 'info' : 'warning') }} px-3 py-2">
                                                    {{ ucfirst($aktivitas->status) }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <small class="text-muted">
                                                    Diupdate: {{ $aktivitas->updated_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($aktivitas->status !== 'paid')
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    @if($aktivitas->status === 'draft')
                                        <button type="submit" name="action" value="save" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan sebagai Draft
                                        </button>
                                        <button type="submit" name="action" value="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane"></i> Simpan & Submit untuk Approval
                                        </button>
                                    @elseif($aktivitas->status === 'approved' && auth()->user()->hasPermission('aktivitas-lainnya-approve'))
                                        <div class="alert alert-info mb-2">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Aktivitas ini sudah disetujui. Untuk mengubah status menjadi "Dibayar", gunakan modul Pembayaran Aktivitas.
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('aktivitas-lainnya.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-success {
        border-left: 4px solid #28a745;
    }

    .border-left-info {
        border-left: 4px solid #17a2b8;
    }

    .border-left-warning {
        border-left: 4px solid #ffc107;
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
    // Format nominal input
    $('#nominal').on('input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        let formattedValue = new Intl.NumberFormat('id-ID').format(value);
        $(this).val(formattedValue);
    });

    // Form validation
    $('form').on('submit', function(e) {
        // Remove number formatting before submission
        let nominalValue = $('#nominal').val().replace(/[^\d]/g, '');
        $('#nominal').val(nominalValue);
    });

    // Auto-resize textarea
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

function removeDocument(index) {
    if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
        $('input[name="existing_dokumen[]"]').eq(index).remove();
        $(event.target).closest('.col-md-3').remove();
    }
}
</script>
@endpush
