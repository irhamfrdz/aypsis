@php
    $canEdit = Illuminate\Support\Facades\Gate::allows('master-gudang-edit');
    $config = [
        'mode' => $mode,
        'canEdit' => $canEdit,
        'active' => $gudang->status === 'aktif',
        'state' => $state,
        'layoutUrl' => route('master-gudang.layout.update', $gudang),
        'positionUrl' => route('denah-gudang.positions.store', $gudang),
        'deleteUrl' => route('denah-gudang.positions.destroy', [$gudang, 0]),
    ];
@endphp
<div id="gudang-planner" class="gp">
    <header class="gp-header">
        <div>
            <p class="gp-eyebrow">{{ $mode === 'layout' ? 'MASTER GUDANG / PENGATURAN LAYOUT' : 'DENAH GUDANG / POSISI KONTAINER' }}</p>
            <h1>{{ $gudang->nama_gudang }}</h1>
            <p>{{ $gudang->lokasi }} &middot; {{ ucfirst($gudang->status) }}</p>
        </div>
        <nav class="gp-actions" aria-label="Navigasi denah">
            <a class="gp-button" href="{{ route('master-gudang.index') }}">Master Gudang</a>
            @if($mode === 'layout')
                <a class="gp-button gp-primary" href="{{ route('denah-gudang.show', $gudang) }}">Input Posisi Kontainer &rarr;</a>
            @else
                <a class="gp-button" href="{{ route('denah-gudang.index') }}">Ganti Gudang</a>
                @if($canEdit)
                <a class="gp-button gp-primary" href="{{ route('master-gudang.layout', $gudang) }}">Atur Layout</a>
                @endif
            @endif
        </nav>
    </header>
    <div id="gp-message" class="gp-message" role="status" aria-live="polite" hidden></div>
    @if(!$canEdit)
        <p class="gp-notice">Mode lihat. Pengaturan memerlukan hak akses edit Master Gudang.</p>
    @endif
    @if($gudang->status !== 'aktif')
        <p class="gp-notice">Gudang nonaktif. Posisi yang sudah ada tetap dapat dilihat dan dilepas; penempatan baru dinonaktifkan.</p>
    @endif
    <fieldset id="gp-controls">
        <div class="gp-stats" id="gp-stats"></div>
        <div class="gp-workspace">
            <aside class="gp-panel gp-sidebar">
                @if($mode === 'layout')
                    <h2>Konfigurasi Area</h2>
                    <p class="gp-help">Atur area penumpukan sesuai pembagian area di lapangan. Slot adalah nomor petak memanjang, baris adalah deretan petak ke samping, dan tingkat adalah susunan kontainer ke atas.</p>
                    <p class="gp-help">Satu slot sepanjang kontainer 20 kaki. Kontainer 40 kaki memakai dua slot berurutan. Tingkat 1 berada di tanah; jumlah tingkat maksimum mengikuti kapasitas operasional gudang.</p>
                    <div id="gp-block-editor"></div>
                    @if($canEdit)
                    <button id="gp-add-block" class="gp-button gp-full" type="button">+ Tambah Area</button>
                    <button id="gp-save-layout" class="gp-button gp-primary gp-full" type="button">Simpan Layout Gudang</button>
                    @endif
                    <p id="gp-dirty" class="gp-help"></p>
                @else
                    <h2>Input Posisi Kontainer</h2>
                    <p class="gp-help">Cari dan pilih kontainer, lalu tentukan area, slot, baris, dan tingkat tujuan. Anda juga bisa klik petak kosong atau menyeret kontainer ke lokasi tujuan.</p>
                    <label class="gp-label" for="gp-search">Cari kontainer / kode lokasi</label>
                    <input id="gp-search" type="search" class="gp-input" placeholder="Nomor kontainer atau A-S03-B02-T01">
                    <label class="gp-label" for="gp-list-filter">Tampilkan</label>
                    <select id="gp-list-filter" class="gp-input">
                        <option value="all">Semua kontainer</option>
                        <option value="unassigned">Belum ditempatkan</option>
                        <option value="assigned">Sudah ditempatkan</option>
                    </select>
                    <div id="gp-container-list" class="gp-container-list"></div>
                    <form id="gp-position-form" class="gp-position-form">
                        <p id="gp-selected" class="gp-help">Belum ada kontainer dipilih.</p>
                        <label class="gp-label" for="gp-input-block">Area</label>
                        <select id="gp-input-block" class="gp-input" required></select>
                        <div class="gp-coordinate-inputs">
                            <label>Slot<select id="gp-input-bay" class="gp-input" required></select></label>
                            <label>Baris<select id="gp-input-row" class="gp-input" required></select></label>
                            <label>Tingkat<select id="gp-input-tier" class="gp-input" required></select></label>
                        </div>
                        <div id="gp-location-summary" class="gp-location-summary" role="status" aria-live="polite"></div>
                        @if($canEdit)
                        <button id="gp-save-position" type="submit" class="gp-button gp-primary gp-full" disabled>Simpan Posisi</button>
                        <button id="gp-remove-position" type="button" class="gp-button gp-danger gp-full" hidden>Lepas dari Denah</button>
                        @endif
                    </form>
                @endif
            </aside>
            <section class="gp-panel gp-map-panel" aria-label="Pratinjau denah gudang">
                <div class="gp-map-heading">
                    <div><h2>{{ $mode === 'layout' ? 'Pratinjau Layout' : 'Denah Posisi Kontainer' }}</h2><p class="gp-help">Tampak atas per area dan tingkat.</p></div>
                    <label>Tingkat <select id="gp-view-tier" class="gp-input"></select></label>
                </div>
                <div id="gp-block-tabs" class="gp-block-tabs" aria-label="Pilih area"></div>
                <p class="gp-help">{{ $mode === 'layout' ? 'Klik petak untuk menandai jalan / area nonaktif pada seluruh tingkat. Petak berisi kontainer tidak dapat dinonaktifkan.' : 'Klik kontainer pada denah untuk melihat koordinat, memindahkan, atau melepas posisinya.' }}</p>
                <div class="gp-legend"><span><i class="gp-swatch"></i>Kosong</span><span><i class="gp-swatch gp-stock"></i>Milik sendiri</span><span><i class="gp-swatch gp-sewa"></i>Sewa</span><span><i class="gp-swatch gp-blocked"></i>Nonaktif / jalan</span><span><i class="gp-swatch gp-stale"></i>Perlu diperiksa</span></div>
                <div class="gp-map-scroll"><div id="gp-grid"></div></div>
                <p class="gp-help">Nomor slot bertambah dari atas ke bawah; nomor baris dari kiri ke kanan. Tingkat 1 adalah dasar tumpukan. Isi tingkat bawah sebelum menumpuk kontainer di atasnya.</p>
                <p class="gp-help">Contoh kode lokasi: <strong>A-S03-B02-T01</strong> = Area A, Slot 03, Baris 02, Tingkat 01. Gunakan penomoran area, slot, dan baris yang sama pada marka lapangan.</p>
            </section>
        </div>
        @if($mode === 'positions')
        <section class="gp-panel gp-position-table">
            <h2>Daftar Posisi Tersimpan</h2>
            <p class="gp-help">Posisi bertanda “Perlu diperiksa” berasal dari kontainer yang sudah pindah gudang, nonaktif, atau data masternya berubah. Lepaskan posisi lama setelah diperiksa.</p>
            <div class="gp-table-scroll"><table><thead><tr><th>Kontainer</th><th>Sumber</th><th>Kode Lokasi</th><th>Area</th><th>Slot</th><th>Baris</th><th>Tingkat</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="gp-position-rows"></tbody></table></div>
        </section>
        @endif
    </fieldset>
</div>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/gudang-plan.css') }}">
@endpush
@push('scripts')
<script>window.gudangPlanConfig = {{ Illuminate\Support\Js::from($config) }};</script>
<script src="{{ asset('js/gudang-plan.js') }}" defer></script>
@endpush
