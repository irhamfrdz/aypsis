@extends('layouts.app')

@section('title', 'Approval Uang Jalan')
@section('page_title', 'Approval Uang Jalan')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-6">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Approval Uang Jalan</h1>
            <p class="mt-1 text-sm text-gray-600">Pengajuan untuk supir dengan dua atau lebih tugas yang belum selesai.</p>
        </div>
        @can('uang-jalan-view')<a href="{{ route('uang-jalan.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Daftar Uang Jalan</a>@endcan
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <form method="GET" class="mb-5 flex items-end gap-3">
        <div>
            <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="rounded-lg border-gray-300 text-sm">
                @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Tampilkan</button>
    </form>

    <div class="space-y-4">
        @forelse($pengajuan as $item)
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-gray-900">{{ $item->nomor_surat_jalan ?: 'Surat jalan #'.$item->surat_jalan_id }}</h2>
                        <p class="mt-1 text-sm text-gray-600">Supir: <strong>{{ $item->supir ?: '-' }}</strong> · {{ $item->jenis_surat_jalan === 'bongkaran' ? 'Bongkaran' : 'Surat jalan biasa' }}</p>
                        <p class="mt-1 text-xs text-gray-500">Diajukan oleh {{ $item->requester?->name ?: '-' }} pada {{ $item->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="h-fit rounded-full px-3 py-1 text-xs font-semibold {{ $item->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($item->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        {{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$item->status] ?? $item->status }}
                    </span>
                </div>
                <div class="mt-4 grid gap-3 rounded-lg bg-gray-50 p-3 text-sm text-gray-700 sm:grid-cols-3">
                    <p><span class="block text-xs text-gray-500">Tugas berjalan saat diajukan</span><strong>{{ $item->jumlah_tugas_berjalan }}</strong></p>
                    <p><span class="block text-xs text-gray-500">Tanggal uang jalan</span><strong>{{ data_get($item->data_uang_jalan, 'tanggal_uang_jalan') ?: '-' }}</strong></p>
                    <p><span class="block text-xs text-gray-500">Jumlah total</span><strong>Rp {{ number_format((float) data_get($item->data_uang_jalan, 'jumlah_total', 0), 0, ',', '.') }}</strong></p>
                </div>
                <details class="mt-3 rounded-lg border border-gray-200 p-3 text-sm">
                    <summary class="cursor-pointer font-medium text-indigo-700">Lihat rincian biaya pengajuan</summary>
                    <div class="mt-3 grid gap-2 text-gray-700 sm:grid-cols-3">
                        @foreach(['jumlah_uang_jalan' => 'Uang jalan', 'jumlah_mel' => 'MEL', 'jumlah_pelancar' => 'Pelancar', 'jumlah_kawalan' => 'Kawalan', 'jumlah_parkir' => 'Parkir', 'jumlah_penyesuaian' => 'Penyesuaian'] as $field => $label)
                            <p><span class="block text-xs text-gray-500">{{ $label }}</span>Rp {{ number_format((float) data_get($item->data_uang_jalan, $field, 0), 0, ',', '.') }}</p>
                        @endforeach
                    </div>
                    @if(data_get($item->data_uang_jalan, 'alasan_penyesuaian'))<p class="mt-2 text-gray-700">Alasan penyesuaian: {{ data_get($item->data_uang_jalan, 'alasan_penyesuaian') }}</p>@endif
                    @if(data_get($item->data_uang_jalan, 'memo'))<p class="mt-2 text-gray-700">Memo: {{ data_get($item->data_uang_jalan, 'memo') }}</p>@endif
                </details>
                @if($item->status === 'pending')
                    @php($tugas = $tugasBerjalan->get($item->id, collect()))
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                        <p class="font-semibold">Tugas belum selesai saat ini: {{ $tugas->count() }}</p>
                        @if($tugas->isNotEmpty())
                            <ul class="mt-2 list-inside list-disc text-xs">
                                @foreach($tugas as $suratJalan)
                                    <li>{{ $suratJalan->no_surat_jalan }} · {{ $suratJalan->tanggal_surat_jalan ? \Carbon\Carbon::parse($suratJalan->tanggal_surat_jalan)->format('d/m/Y') : '-' }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <form method="POST" action="{{ route('uang-jalan.approval.approve', $item) }}" class="rounded-lg border border-green-200 p-3">
                            @csrf
                            <label class="mb-2 block text-xs font-medium text-gray-700">Catatan persetujuan (opsional)</label>
                            <textarea name="catatan_approval" rows="2" maxlength="1000" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                            <button type="submit" class="mt-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700" onclick="return confirm('Setujui pengajuan dan buat uang jalan?')">Setujui & Buat Uang Jalan</button>
                        </form>
                        <form method="POST" action="{{ route('uang-jalan.approval.reject', $item) }}" class="rounded-lg border border-red-200 p-3">
                            @csrf
                            <label class="mb-2 block text-xs font-medium text-gray-700">Alasan penolakan <span class="text-red-600">*</span></label>
                            <textarea name="catatan_approval" rows="2" maxlength="1000" required class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                            <button type="submit" class="mt-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tolak Pengajuan</button>
                        </form>
                    </div>
                @else
                    <p class="mt-3 text-sm text-gray-600">Diproses oleh {{ $item->reviewer?->name ?: '-' }} · {{ $item->reviewed_at?->format('d/m/Y H:i') ?: '-' }}@if($item->uangJalan) · @can('uang-jalan-view')<a class="text-indigo-700 underline" href="{{ route('uang-jalan.show', $item->uangJalan) }}">{{ $item->uangJalan->nomor_uang_jalan }}</a>@else{{ $item->uangJalan->nomor_uang_jalan }}@endcan @endif</p>
                    @if($item->catatan_approval)<p class="mt-1 text-sm text-gray-600">Catatan: {{ $item->catatan_approval }}</p>@endif
                @endif
            </article>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white px-6 py-12 text-center text-sm text-gray-500">Tidak ada pengajuan pada status ini.</div>
        @endforelse
    </div>
    <div class="mt-5">{{ $pengajuan->links() }}</div>
</div>
@endsection
