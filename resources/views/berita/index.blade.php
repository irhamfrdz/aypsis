@extends('layouts.app')

@section('title', 'Berita & Pamflet')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📰 Berita & Pamflet</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola konten berita dan pamflet yang ditampilkan di sistem PWA karyawan</p>
        </div>
        <a href="{{ route('berita.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Baru
        </a>
    </div>

    @if(session('success'))
    <div id="alert-success" class="flex items-center gap-3 mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('berita.index') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-5">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Cari Judul</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik judul..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Tipe</label>
                <select name="tipe" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="berita" @selected(request('tipe') === 'berita')>Berita</option>
                    <option value="pamflet" @selected(request('tipe') === 'pamflet')>Pamflet</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">Cari</button>
                @if(request()->anyFilled(['search','tipe']))
                <a href="{{ route('berita.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] font-semibold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Gambar</th>
                        <th class="px-4 py-3 text-left">Judul</th>
                        <th class="px-4 py-3 text-center">Tipe</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Pin</th>
                        <th class="px-4 py-3 text-left">Publish</th>
                        <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($beritas as $i => $item)
                    <tr class="hover:bg-gray-50 transition-colors" id="row-{{ $item->id }}">
                        <td class="px-4 py-3 text-gray-500">{{ $beritas->firstItem() + $i }}</td>
                        <td class="px-4 py-3">
                            @if($item->gambar)
                                <img src="{{ asset($item->gambar) }}" alt="Gambar" class="w-14 h-10 object-cover rounded-lg border border-gray-200">
                            @else
                                <div class="w-14 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 max-w-xs">
                            <div class="truncate" title="{{ $item->judul }}">{{ $item->judul }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->tipe === 'pamflet')
                                <span class="inline-flex px-2 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">Pamflet</span>
                            @else
                                <span class="inline-flex px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Berita</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button
                                data-id="{{ $item->id }}"
                                class="toggle-active inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold cursor-pointer transition-all
                                       {{ $item->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                <span class="dot w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                <span class="label">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->pinned)
                                <span class="text-yellow-500" title="Dipinkan">📌</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ $item->published_at ? $item->published_at->format('d M Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">
                            {{ $item->creator?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('berita.edit', $item->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition-colors">
                                    Edit
                                </a>
                                <button data-id="{{ $item->id }}" class="delete-btn inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 transition-colors">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                <p class="font-medium">Belum ada berita atau pamflet</p>
                                <a href="{{ route('berita.create') }}" class="text-indigo-600 text-sm hover:underline">Tambah yang pertama</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($beritas->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $beritas->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Berita/Pamflet?</h3>
        <p class="text-gray-500 text-sm mb-5">Tindakan ini tidak dapat dibatalkan. Gambar juga akan dihapus permanen.</p>
        <div class="flex gap-3 justify-end">
            <button id="cancelDelete" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-200">Batal</button>
            <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white font-semibold text-sm rounded-lg hover:bg-red-700">Hapus</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const baseUrl = "{{ url('master/berita') }}";
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

// Toggle aktif
document.querySelectorAll('.toggle-active').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`${baseUrl}/${id}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const label = this.querySelector('.label');
                    const dot   = this.querySelector('.dot');
                    if (data.is_active) {
                        this.classList.replace('bg-gray-100', 'bg-green-100');
                        this.classList.replace('text-gray-500', 'text-green-700');
                        dot.classList.replace('bg-gray-400', 'bg-green-500');
                        label.textContent = 'Aktif';
                    } else {
                        this.classList.replace('bg-green-100', 'bg-gray-100');
                        this.classList.replace('text-green-700', 'text-gray-500');
                        dot.classList.replace('bg-green-500', 'bg-gray-400');
                        label.textContent = 'Nonaktif';
                    }
                }
            })
            .catch(err => console.error(err));
    });
});

// Hapus
let deleteId = null;
const deleteModal = document.getElementById('deleteModal');
const confirmDeleteBtn = document.getElementById('confirmDelete');

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        deleteId = this.dataset.id;
        deleteModal.classList.remove('hidden');
    });
});

document.getElementById('cancelDelete').addEventListener('click', () => {
    deleteModal.classList.add('hidden');
    deleteId = null;
});

confirmDeleteBtn.addEventListener('click', () => {
    if (!deleteId) return;
    confirmDeleteBtn.disabled = true;
    confirmDeleteBtn.textContent = 'Menghapus...';

    fetch(`${baseUrl}/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`row-${deleteId}`)?.remove();
            deleteModal.classList.add('hidden');
            deleteId = null;
        } else {
            alert(data.message || 'Gagal menghapus berita.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan saat menghapus data.');
    })
    .finally(() => {
        confirmDeleteBtn.disabled = false;
        confirmDeleteBtn.textContent = 'Hapus';
    });
});

// Auto-hide alert
setTimeout(() => { document.getElementById('alert-success')?.remove(); }, 4000);
</script>
@endpush
