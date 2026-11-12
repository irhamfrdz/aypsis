@extends('layouts.app')
@section('title', 'Detail Pranota Uang Makan')
@section('page_title', 'Detail Pranota Uang Makan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pranota-uang-makan-detail.css') }}?v={{ filemtime(public_path('css/pranota-uang-makan-detail.css')) }}">
@endpush

@section('content')
@php
    // Gunakan relasi yang sudah dimuat controller; tidak menambah query per baris.
    $details = $pranota->details;
    $attendanceDays = function ($value) {
        preg_match('/\d+/', (string) $value, $matches);
        return (int) ($matches[0] ?? 0);
    };
    $totalAttendance = $details->sum(fn ($detail) => $attendanceDays($detail->kehadiran));
    $totalNominal = $details->sum('total_akhir');
@endphp
<div class="meal-pranota-detail">
    <header class="pmd-header">
        <div class="pmd-heading">
            <a href="{{ route('pranota-uang-makan.index') }}" class="pmd-back" title="Kembali ke Daftar" aria-label="Kembali ke daftar pranota uang makan"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
            <span class="pmd-document-icon"><i class="fa-solid fa-file-invoice" aria-hidden="true"></i></span>
            <div><h1>Detail Pranota Uang Makan</h1><p>Rincian uang makan karyawan berdasarkan kehadiran.</p></div>
        </div>
        <div class="pmd-actions">
            <a href="{{ route('pranota-uang-makan.edit', $pranota->id) }}" class="pmd-button pmd-button-primary"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Edit Pranota</a>
            <button type="button" onclick="window.print()" class="pmd-button"><i class="fa-solid fa-print" aria-hidden="true"></i>Cetak Pranota</button>
            <form action="{{ route('pranota-uang-makan.destroy', $pranota->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota ini? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="pmd-button pmd-button-danger"><i class="fa-solid fa-trash-can" aria-hidden="true"></i>Hapus</button>
            </form>
        </div>
    </header>

    <section class="pmd-panel pmd-information" aria-label="Informasi dan ringkasan pranota">
        <div class="pmd-info"><i class="fa-regular fa-file-lines" aria-hidden="true"></i><div><span>Nomor Pranota</span><strong>{{ $pranota->nomor_pranota }}</strong></div></div>
        <div class="pmd-info"><i class="fa-regular fa-calendar" aria-hidden="true"></i><div><span>Tanggal Pranota</span><strong>{{ $pranota->tanggal_pranota?->format('d/m/Y') ?? '-' }}</strong></div></div>
        <div class="pmd-info"><i class="fa-solid fa-users" aria-hidden="true"></i><div><span>Total Karyawan</span><strong id="pmd-employee-count">{{ number_format($details->count(), 0, ',', '.') }} Orang</strong></div></div>
        <div class="pmd-info"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i><div><span>Total Kehadiran</span><strong id="pmd-attendance">{{ number_format($totalAttendance, 0, ',', '.') }} Hari</strong></div></div>
        <div class="pmd-info pmd-info-total"><i class="fa-solid fa-wallet" aria-hidden="true"></i><div><span>Total Nominal</span><strong id="pmd-total">Rp {{ number_format($totalNominal, 0, ',', '.') }}</strong></div></div>
    </section>

    <section class="pmd-panel pmd-employees" aria-labelledby="pmd-employees-heading">
        <div class="pmd-section-header"><h2 id="pmd-employees-heading"><i class="fa-solid fa-users" aria-hidden="true"></i>Rincian Karyawan</h2><span class="pmd-count">{{ $details->count() }} Data</span></div>
        <div class="pmd-table-scroll">
            <table class="pmd-table">
                <colgroup><col class="pmd-col-number"><col class="pmd-col-employee"><col class="pmd-col-attendance"><col class="pmd-col-money"><col class="pmd-col-adjustment"><col class="pmd-col-notes"><col class="pmd-col-total"></colgroup>
                <thead><tr><th scope="col">No.</th><th scope="col">Karyawan</th><th scope="col" class="pmd-center">Kehadiran</th><th scope="col" class="pmd-money">Nominal Awal</th><th scope="col" class="pmd-money">Adjustment</th><th scope="col">Catatan</th><th scope="col" class="pmd-money">Total Akhir</th></tr></thead>
                <tbody>
                    @forelse($details as $detail)
                    @php
                        $employeeName = $detail->karyawan->nama_lengkap ?? '-';
                        $nameParts = preg_split('/\s+/u', trim($employeeName), -1, PREG_SPLIT_NO_EMPTY);
                        $initials = $employeeName === '-' ? 'U' : mb_strtoupper(mb_substr($nameParts[0] ?? 'U', 0, 1).(count($nameParts) > 1 ? mb_substr(end($nameParts), 0, 1) : ''));
                    @endphp
                    <tr>
                        <td class="pmd-row-number">{{ $loop->iteration }}</td>
                        <td><div class="pmd-employee"><span class="pmd-avatar" aria-hidden="true">{{ $initials }}</span><div><p class="pmd-employee-name">{{ $employeeName }}</p><p class="pmd-nik">{{ $detail->karyawan->nik ?? '-' }}</p></div></div></td>
                        <td class="pmd-center"><span class="pmd-attendance-badge"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i>{{ trim((string) $detail->kehadiran) !== '' ? $attendanceDays($detail->kehadiran).' Hari' : '-' }}</span></td>
                        <td class="pmd-money">Rp {{ number_format($detail->nominal_awal, 0, ',', '.') }}</td>
                        <td class="pmd-money {{ $detail->adjustment > 0 ? 'pmd-positive' : ($detail->adjustment < 0 ? 'pmd-negative' : 'pmd-zero') }}">{{ $detail->adjustment > 0 ? '+ ' : ($detail->adjustment < 0 ? '− ' : '') }}Rp {{ number_format(abs($detail->adjustment), 0, ',', '.') }}</td>
                        <td class="pmd-notes">{{ $detail->catatan ?: '-' }}</td>
                        <td class="pmd-money pmd-total-value">Rp {{ number_format($detail->total_akhir, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="pmd-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><p>Tidak ada rincian data karyawan.</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot><tr><td colspan="6">Total Keseluruhan</td><td class="pmd-money pmd-total-value">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td></tr></tfoot>
            </table>
        </div>
    </section>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print') && urlParams.get('print') === 'true') {
            setTimeout(() => { window.print(); }, 500);
        }
    });
</script>
@endpush
@endsection
