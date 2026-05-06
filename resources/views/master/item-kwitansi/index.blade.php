@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <!-- Header Section -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="mb-1 font-weight-bold text-dark">📦 Master Item Kwitansi</h4>
                    <p class="text-muted small mb-0">Kelola daftar item barang dan jasa untuk keperluan penerbitan kwitansi.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm d-flex align-items-center justify-content-center transition-all hover-lift" data-toggle="modal" data-target="#modalTambahItem">
                    <i class="fas fa-plus-circle mr-2"></i>
                    <span>Tambah Item Baru</span>
                </button>
            </div>

            <!-- Main Card -->
            <div class="card border-0 shadow-soft overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="mx-4 mt-4">
                            <div class="alert alert-custom alert-success border-0 shadow-sm d-flex align-items-center" role="alert">
                                <div class="alert-icon-container bg-success-soft mr-3">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                                <div>{{ session('success') }}</div>
                                <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover modern-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="60" class="text-center">NO</th>
                                    <th>NAMA ITEM</th>
                                    <th>SATUAN</th>
                                    <th class="text-right">HARGA SATUAN</th>
                                    <th>KETERANGAN</th>
                                    <th width="120" class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                <tr>
                                    <td class="text-center text-muted font-weight-bold small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="item-icon-circle mr-3">
                                                <i class="fas fa-box text-primary small"></i>
                                            </div>
                                            <span class="font-weight-bold text-dark">{{ $item->nama_item }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-custom {{ $item->satuan ? 'badge-info-soft text-info' : 'badge-light-soft text-muted' }}">
                                            {{ $item->satuan ?: 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <span class="font-weight-bold text-primary">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small italic">{{ Str::limit($item->keterangan, 60) ?: '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-action btn-edit btn-edit-item" 
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama_item }}"
                                                    data-satuan="{{ $item->satuan }}"
                                                    data-harga="{{ $item->harga_satuan }}"
                                                    data-keterangan="{{ $item->keterangan }}"
                                                    data-toggle="modal" data-target="#modalEditItem"
                                                    title="Edit Item">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ route('master.item-kwitansi.destroy', $item->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action btn-delete" 
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')"
                                                        title="Hapus Item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center">
                                        <div class="empty-state">
                                            <img src="https://illustrations.popsy.co/amber/box.svg" alt="Empty" style="width: 150px; opacity: 0.6;">
                                            <h6 class="mt-3 text-muted">Belum ada data item kwitansi</h6>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill mt-2" data-toggle="modal" data-target="#modalTambahItem">
                                                Tambah Item Pertama
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark">Tambah Item Baru</h5>
                    <p class="text-muted small mb-0">Lengkapi detail item di bawah ini.</p>
                </div>
                <button type="button" class="close btn-close-custom" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('master.item-kwitansi.store') }}" method="POST" class="needs-validation">
                @csrf
                <div class="modal-body p-4 pt-0">
                    <div class="form-group custom-field mb-4">
                        <label class="small-label">NAMA ITEM <span class="text-danger">*</span></label>
                        <div class="input-group-modern">
                            <i class="fas fa-tag icon"></i>
                            <input type="text" name="nama_item" class="form-control-modern" required placeholder="Masukan nama item atau jasa...">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group custom-field mb-4">
                                <label class="small-label">SATUAN</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-layer-group icon"></i>
                                    <input type="text" name="satuan" class="form-control-modern" placeholder="Rit / Jam / Hari">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group custom-field mb-4">
                                <label class="small-label">HARGA SATUAN <span class="text-danger">*</span></label>
                                <div class="input-group-modern">
                                    <span class="currency-prefix">Rp</span>
                                    <input type="number" name="harga_satuan" class="form-control-modern pl-5" required placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group custom-field mb-0">
                        <label class="small-label">KETERANGAN TAMBAHAN</label>
                        <textarea name="keterangan" class="form-control-modern" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm font-weight-bold">Simpan Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEditItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark">Perbarui Data Item</h5>
                    <p class="text-muted small mb-0">Ubah informasi item yang sudah ada.</p>
                </div>
                <button type="button" class="close btn-close-custom" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formEditItem" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 pt-0">
                    <div class="form-group custom-field mb-4">
                        <label class="small-label text-info">NAMA ITEM <span class="text-danger">*</span></label>
                        <div class="input-group-modern border-info-soft">
                            <i class="fas fa-tag icon text-info"></i>
                            <input type="text" name="nama_item" id="edit_nama" class="form-control-modern" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group custom-field mb-4">
                                <label class="small-label">SATUAN</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-layer-group icon"></i>
                                    <input type="text" name="satuan" id="edit_satuan" class="form-control-modern">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group custom-field mb-4">
                                <label class="small-label">HARGA SATUAN <span class="text-danger">*</span></label>
                                <div class="input-group-modern">
                                    <span class="currency-prefix">Rp</span>
                                    <input type="number" name="harga_satuan" id="edit_harga" class="form-control-modern pl-5" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group custom-field mb-0">
                        <label class="small-label">KETERANGAN TAMBAHAN</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control-modern" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 shadow-sm font-weight-bold text-white">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.btn-edit-item').on('click', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const satuan = $(this).data('satuan');
        const harga = $(this).data('harga');
        const keterangan = $(this).data('keterangan');

        document.getElementById('formEditItem').action = '{{ url("master/item-kwitansi") }}/' + id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_satuan').value = (satuan === 'null' || !satuan) ? '' : satuan;
        document.getElementById('edit_harga').value = harga;
        document.getElementById('edit_keterangan').value = (keterangan === 'null' || !keterangan) ? '' : keterangan;
    });
});
</script>

<style>
    :root {
        --primary-hsl: 221, 83%, 53%;
        --info-hsl: 188, 78%, 41%;
        --success-hsl: 142, 71%, 45%;
        --danger-hsl: 0, 84%, 60%;
        --gray-hsl: 210, 16%, 76%;
    }

    /* General Styling */
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 1rem; }
    
    .shadow-soft {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
    }

    /* Table Styling */
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead th {
        background-color: #f8faff;
        border-top: none;
        border-bottom: 1px solid #edf2f9;
        color: #8492a6;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1.25rem 1.5rem;
    }
    .modern-table tbody tr {
        transition: background-color 0.2s;
    }
    .modern-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-top: 1px solid #edf2f9;
    }
    .modern-table tbody tr:hover {
        background-color: #f8faff;
    }

    .item-icon-circle {
        width: 36px;
        height: 36px;
        background-color: hsla(var(--primary-hsl), 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Badge Styling */
    .badge-custom {
        padding: 0.4em 0.8em;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 8px;
        display: inline-block;
    }
    .badge-info-soft { background-color: hsla(var(--info-hsl), 0.1); }
    .badge-light-soft { background-color: #f1f4f8; }
    .badge-success-soft { background-color: hsla(var(--success-hsl), 0.1); }

    /* Action Buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
    }
    .btn-edit {
        background-color: hsla(var(--info-hsl), 0.1);
        color: hsl(var(--info-hsl));
    }
    .btn-edit:hover {
        background-color: hsl(var(--info-hsl));
        color: white;
    }
    .btn-delete {
        background-color: hsla(var(--danger-hsl), 0.1);
        color: hsl(var(--danger-hsl));
    }
    .btn-delete:hover {
        background-color: hsl(var(--danger-hsl));
        color: white;
    }

    /* Alert Styling */
    .alert-custom {
        border-radius: 12px;
        padding: 1rem 1.25rem;
    }
    .alert-icon-container {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .bg-success-soft { background-color: hsla(var(--success-hsl), 0.15); }

    /* Form Modernization */
    .small-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #8492a6;
        margin-bottom: 0.5rem;
        display: block;
    }
    .input-group-modern {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-group-modern .icon {
        position: absolute;
        left: 15px;
        color: #b1bccd;
        z-index: 5;
    }
    .input-group-modern .currency-prefix {
        position: absolute;
        left: 15px;
        color: hsl(var(--primary-hsl));
        font-weight: 700;
        z-index: 5;
    }
    .form-control-modern {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 40px;
        background-color: #f8faff;
        border: 1.5px solid transparent;
        border-radius: 12px;
        font-size: 0.9rem;
        transition: all 0.2s;
        color: #2d3748;
    }
    textarea.form-control-modern {
        padding-left: 1rem;
    }
    .form-control-modern:focus {
        background-color: #fff;
        border-color: hsl(var(--primary-hsl));
        box-shadow: 0 0 0 4px hsla(var(--primary-hsl), 0.1);
        outline: none;
    }
    .border-info-soft {
        border-color: hsla(var(--info-hsl), 0.2);
    }

    /* Modal Styling */
    .btn-close-custom {
        background-color: #f1f4f8;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        opacity: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: #8492a6;
        border: none;
        transition: all 0.2s;
        margin: 0 !important;
        padding: 0;
    }
    .btn-close-custom:hover {
        background-color: #e2e8f0;
        color: #4a5568;
    }

    /* Empty State */
    .empty-state {
        padding: 2rem;
    }
</style>
@endsection
