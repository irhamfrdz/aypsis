@extends('layouts.app')

@section('title', 'Approval Tanda Terima Pelabuhan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Approval Tanda Terima Pelabuhan</h1>
        <p class="mt-1 text-sm text-gray-600">Isi tanggal tanda terima dan checkpoint supir, lalu setujui tanda terima.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('approval-tanda-terima-pelabuhan.index') }}" class="flex flex-col gap-3 border-b border-gray-200 p-4 md:flex-row md:items-end">
            <label class="flex-1 text-sm font-medium text-gray-700">Cari no. surat jalan, kontainer, atau supir
                <input type="search" name="search" value="{{ $search }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Cari tanda terima">
            </label>
            <label class="text-sm font-medium text-gray-700">Status
                <select name="status" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="pending" @selected($status === 'pending')>Menunggu approval</option>
                    <option value="approved" @selected($status === 'approved')>Disetujui</option>
                    <option value="all" @selected($status === 'all')>Semua</option>
                </select>
            </label>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Tampilkan</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Surat jalan</th>
                        <th class="px-4 py-3">Kontainer / Supir</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal tanda terima</th>
                        <th class="px-4 py-3">Tanggal checkpoint supir</th>
                        <th class="px-4 py-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tandaTerimas as $tandaTerima)
                        @php($formId = 'approval-pelabuhan-' . $tandaTerima->id)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @can('tanda-terima-view')
                                    <a href="{{ route('tanda-terima.show', $tandaTerima) }}" class="text-blue-700 hover:underline">{{ $tandaTerima->no_surat_jalan ?: 'TT #' . $tandaTerima->id }}</a>
                                @else
                                    {{ $tandaTerima->no_surat_jalan ?: 'TT #' . $tandaTerima->id }}
                                @endcan
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                <div>{{ $tandaTerima->no_kontainer ?: '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $tandaTerima->supir ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($tandaTerima->status === 'approved')
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">Disetujui</span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <input type="date" form="{{ $formId }}" name="tanggal" value="{{ $tandaTerima->tanggal?->format('Y-m-d') }}" required
                                    class="w-40 rounded-lg border border-gray-300 px-2 py-1.5 text-sm" @cannot('approval-tanda-terima-pelabuhan-approve') disabled @endcannot>
                            </td>
                            <td class="px-4 py-3">
                                <input type="date" form="{{ $formId }}" name="tanggal_checkpoint_supir" value="{{ $tandaTerima->tanggal_checkpoint_supir?->format('Y-m-d') }}" required
                                    class="w-40 rounded-lg border border-gray-300 px-2 py-1.5 text-sm" @cannot('approval-tanda-terima-pelabuhan-approve') disabled @endcannot>
                            </td>
                            <td class="px-4 py-3">
                                @can('approval-tanda-terima-pelabuhan-approve')
                                    <form id="{{ $formId }}" method="POST" action="{{ route('approval-tanda-terima-pelabuhan.approve', $tandaTerima) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <input type="hidden" name="search" value="{{ $search }}">
                                        <button type="submit" class="whitespace-nowrap rounded-lg bg-green-600 px-3 py-2 font-medium text-white hover:bg-green-700">{{ $tandaTerima->status === 'approved' ? 'Simpan tanggal' : 'Setujui' }}</button>
                                    </form>
                                @else
                                    <span class="text-gray-400">Lihat saja</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Tidak ada tanda terima untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 p-4">{{ $tandaTerimas->links() }}</div>
    </div>
</div>
@endsection
