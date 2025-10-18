@extends('layouts.app')


@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Master Kegiatan')
@section('page_title', 'Master Kegiatan')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Kegiatan</h2>
        <div class="flex items-center space-x-3">
            <a href="{{ route('master.kegiatan.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-0.5 px-2 rounded transition duration-300 text-xs">+ Tambah Kegiatan</a>
            <a href="{{ route('master.kegiatan.export') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-0.5 px-2 rounded transition duration-300 text-xs">Export CSV</a>
            <a href="{{ route('master.kegiatan.template') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-0.5 px-2 rounded transition duration-300 text-xs">Download Template CSV</a>
            <form id="importForm" action="{{ route('master.kegiatan.import') }}" method="POST" enctype="multipart/form-data" class="inline-block">
                @csrf
                <label id="importLabel" class="inline-flex items-center bg-yellow-100 hover:bg-yellow-200 text-gray-800 font-bold py-0.5 px-2 rounded transition duration-300 cursor-pointer text-xs">
                    <input type="file" id="csvFileInput" name="csv_file" accept=".csv,.txt" class="hidden" required onchange="handleFileSelect(this)" />
                    <span id="importText">Import CSV</span>
                </label>
            </form>

            <script>
                function handleFileSelect(input) {
                    if (input.files && input.files[0]) {
                        const fileName = input.files[0].name;
                        const fileExt = fileName.split('.').pop().toLowerCase();

                        // Validate file extension
                        if (!['csv', 'txt'].includes(fileExt)) {
                            alert('File harus berformat CSV atau TXT');
                            input.value = '';
                            return;
                        }

                        // Show loading indicator
                        document.getElementById('importText').textContent = 'Uploading...';
                        document.getElementById('importLabel').classList.add('opacity-50', 'cursor-wait');

                        // Submit form
                        setTimeout(() => {
                            input.form.submit();
                        }, 100);
                    }
                }
            </script>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative mb-4" role="alert">
            <strong>Beberapa baris dilewati saat import:</strong>
            <ul class="mt-2 list-disc pl-5 text-sm">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Kegiatan</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kegiatan</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-700 text-[10px]">
                @forelse ($items as $index => $kegiatan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $items->firstItem() + $index }}</td>
                        <td class="px-4 py-2 font-mono text-[10px]">{{ $kegiatan->kode_kegiatan }}</td>
                        <td class="px-4 py-2">{{ $kegiatan->nama_kegiatan }}</td>
                        <td class="px-4 py-2">{{ $kegiatan->type ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $kegiatan->keterangan ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">
                            @php $s = strtolower($kegiatan->status ?? ''); @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $s == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($s) ?: '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex items-center justify-center space-x-3 text-[10px]">
                                <a href="{{ route('master.kegiatan.edit', $kegiatan->id) }}"
                                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium"
                                   title="Edit Data">
                                    Edit
                                </a>
                                <span class="text-gray-300">|</span>
                                
                                    <!-- Audit Log Link -->
                                    <button type="button"
                                            onclick="showAuditLog('{{ get_class($kegiatan) }}', '{{ $kegiatan->id }}', '{{ $kegiatan->nama_kegiatan }}')"
                                            class="text-purple-600 hover:text-purple-800 hover:underline font-medium cursor-pointer"
                                            title="Lihat Riwayat Perubahan">
                                        Riwayat
                                    </button>
                                    <span class="text-gray-300">|</span><form action="{{ route('master.kegiatan.destroy', $kegiatan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0"
                                            title="Hapus Data">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-center text-gray-500 text-[10px]">Tidak ada data kegiatan yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
