@extends('layouts.app')

@section('title', 'Surat Jalan Tarik Kosong Batam')
@section('page_title', 'Surat Jalan Tarik Kosong Batam')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Surat Jalan Tarik Kosong Batam</h2>
        @can('surat-jalan-tarik-kosong-batam-create')
        <div class="flex space-x-2">
            <button onclick="buatSuratJalanMassal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <i class="fas fa-list mr-2"></i> Tambah Massal
            </button>
            <a href="{{ route('surat-jalan-tarik-kosong-batam.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah Surat Jalan
            </a>
        </div>
        @endcan
    </div>

    <!-- Form Filter -->
    <form action="{{ route('surat-jalan-tarik-kosong-batam.index') }}" method="GET" class="mb-6 p-4 border rounded-lg bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                <input type="text" name="search" id="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ request('search') }}" placeholder="No. SJ, Kontainer, Supir...">
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ request('start_date') }}">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ request('end_date') }}">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($items as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $item->no_surat_jalan }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->tanggal_surat_jalan->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->no_kontainer ?? '-' }} ({{ $item->size ?? '' }} FT)
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->supir ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->no_plat ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClasses = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'active' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ strtoupper($item->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('surat-jalan-tarik-kosong-batam.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('surat-jalan-tarik-kosong-batam-update')
                        <a href="{{ route('surat-jalan-tarik-kosong-batam.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endcan
                        <a href="{{ route('surat-jalan-tarik-kosong-batam.print', $item->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 mr-3">
                            <i class="fas fa-print"></i>
                        </a>
                        @can('surat-jalan-tarik-kosong-batam-delete')
                        <form action="{{ route('surat-jalan-tarik-kosong-batam.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                        Belum ada data.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>

<!-- Modal Buat Surat Jalan Massal -->
<div id="modalBuatSuratJalanMassal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Buat Surat Jalan Massal</h3>
            <button type="button" onclick="closeBulkModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="mt-4 max-h-[80vh] overflow-y-auto px-1">
            <div id="bulkModalAlertArea"></div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gudang Tujuan Default (Opsional, jika kosong di data)</label>
                <select id="bulk_gudang_tujuan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Pilih Gudang Default</option>
                    @php
                        $gudangs = \App\Models\Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
                    @endphp
                    @foreach($gudangs as $g)
                        <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Guide -->
            <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                <h4 class="text-sm font-semibold text-indigo-800 mb-2">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Panduan Format Data (Semicolon-separated / Dipisahkan Titik Koma)
                </h4>
                <p class="text-xs text-indigo-700 mb-1">Setiap baris = 1 surat jalan. Kolom dipisahkan dengan <strong>Titik Koma (;)</strong>.</p>
                <div class="bg-white rounded px-3 py-2 text-xs text-indigo-900 font-mono overflow-x-auto border border-indigo-100">
                    No SJ ; Tanggal (YYYY-MM-DD) ; No Kontainer ; Size ; Supir ; No Plat ; Tujuan Pengambilan ; Tujuan Pengiriman (Gudang Tujuan) ; Catatan
                </div>
                <p class="text-xs text-indigo-600 mt-1">
                    <strong>Contoh:</strong> SJ-001;2026-06-27;CONT123;20;ANDI;B1234XX;Pelabuhan;Gudang A;Cepat
                </p>
            </div>

            <!-- Textarea Input -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Data Surat Jalan <span class="text-red-500">*</span>
                </label>
                <textarea id="bulkTextarea" rows="10"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Masukkan data di sini...&#10;SJ-001;2026-06-27;CONT123;20;ANDI;B1234XX;Pelabuhan;Gudang A;Cepat"></textarea>
            </div>

            <div class="flex justify-end mb-4">
                <button type="button" onclick="parseBulkData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors">
                    <i class="fas fa-magic mr-1"></i> Proses Data
                </button>
            </div>

            <!-- Preview Area -->
            <div id="bulkPreviewContainer" class="hidden">
                <h4 class="text-md font-semibold text-gray-800 mb-2">Pratinjau Data (<span id="bulkCount">0</span> baris)</h4>
                <div class="overflow-x-auto border rounded-lg max-h-64">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">No SJ</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">Tanggal</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">Kontainer</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">Size</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">Supir</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">No Plat</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">Pengambilan</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">Gudang Tujuan</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">Uang Jalan</th>
                                <th class="px-3 py-2 text-left text-gray-500 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="bulkPreviewBody" class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end pt-3 border-t mt-4 space-x-2">
            <button type="button" onclick="closeBulkModal()" class="px-4 py-2 bg-white text-gray-700 border rounded-lg hover:bg-gray-50 text-sm font-medium">
                Batal
            </button>
            <button type="button" id="btnSubmitBulk" onclick="submitBulkData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium opacity-50 cursor-not-allowed" disabled>
                <i class="fas fa-save mr-1"></i> Simpan Massal
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let bulkParsedRows = [];
const pricelistRings = @json($pricelistRings ?? []);

function buatSuratJalanMassal() {
    document.getElementById('modalBuatSuratJalanMassal').classList.remove('hidden');
    document.getElementById('bulkTextarea').value = '';
    document.getElementById('bulkPreviewContainer').classList.add('hidden');
    document.getElementById('bulkModalAlertArea').innerHTML = '';
    
    const btnSubmit = document.getElementById('btnSubmitBulk');
    btnSubmit.disabled = true;
    btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
}

function closeBulkModal() {
    document.getElementById('modalBuatSuratJalanMassal').classList.add('hidden');
    bulkParsedRows = [];
}

async function parseBulkData() {
    const text = document.getElementById('bulkTextarea').value.trim();
    if (!text) {
        showBulkAlert('warning', 'Teks data masih kosong.');
        return;
    }

    const lines = text.split('\n');
    bulkParsedRows = [];
    const tbody = document.getElementById('bulkPreviewBody');
    tbody.innerHTML = '';
    
    let validCount = 0;

    // Collect all container numbers first
    const noKontainers = [];
    lines.forEach((line) => {
        if (!line.trim()) return;
        const cols = line.split(';').map(c => c.trim());
        if (cols[2]) noKontainers.push(cols[2]);
    });

    let containerSizes = {};
    if (noKontainers.length > 0) {
        try {
            const btnSubmit = document.getElementById('btnSubmitBulk');
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat Data...';
            btnSubmit.disabled = true;

            const response = await fetch('{{ route("surat-jalan-tarik-kosong-batam.check-container-sizes") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ no_kontainers: noKontainers })
            });
            const data = await response.json();
            if (data.success) {
                containerSizes = data.sizes;
            }
        } catch (error) {
            console.error('Error fetching container sizes:', error);
        }
    }

    lines.forEach((line, index) => {
        if (!line.trim()) return;

        const cols = line.split(';').map(c => c.trim());
        
        const row = {
            no_surat_jalan: cols[0] || '',
            tanggal_surat_jalan: cols[1] || '',
            no_kontainer: cols[2] || '',
            size: cols[3] || '',
            supir: cols[4] || '',
            no_plat: cols[5] || '',
            tujuan_pengambilan: cols[6] || '',
            gudang_tujuan: cols[7] || '',
            catatan: cols[8] || '',
            uang_jalan: 0,
            _original_line: line
        };

        // Auto-fill size from database if found
        if (row.no_kontainer && containerSizes[row.no_kontainer]) {
            row.size = containerSizes[row.no_kontainer];
        }

        // Auto-calculate uang jalan
        if (row.tujuan_pengambilan && row.size) {
            const ringData = pricelistRings.find(r => r.name.toLowerCase() === row.tujuan_pengambilan.toLowerCase());
            if (ringData) {
                // Tarik Kosong implies Empty
                const key = `${row.size}_Empty`;
                const rate = ringData.rates[key];
                if (rate) {
                    row.uang_jalan = rate;
                }
            }
        }

        if (row.no_surat_jalan) {
            let displayTanggal = row.tanggal_surat_jalan;
            if (row.tanggal_surat_jalan && !isNaN(row.tanggal_surat_jalan) && parseInt(row.tanggal_surat_jalan) > 10000) {
                const serial = parseInt(row.tanggal_surat_jalan);
                const jsDate = new Date(Math.round((serial - 25569) * 86400 * 1000));
                const y = jsDate.getUTCFullYear();
                const m = String(jsDate.getUTCMonth() + 1).padStart(2, '0');
                const d = String(jsDate.getUTCDate()).padStart(2, '0');
                displayTanggal = `${d}/${m}/${y}`;
                row.tanggal_surat_jalan = `${y}-${m}-${d}`;
            }

            bulkParsedRows.push(row);
            validCount++;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-3 py-2 whitespace-nowrap">${row.no_surat_jalan}</td>
                <td class="px-3 py-2 whitespace-nowrap">${displayTanggal}</td>
                <td class="px-3 py-2 whitespace-nowrap">${row.no_kontainer}</td>
                <td class="px-3 py-2 whitespace-nowrap">${row.size}</td>
                <td class="px-3 py-2 whitespace-nowrap">${row.supir}</td>
                <td class="px-3 py-2 whitespace-nowrap">${row.no_plat}</td>
                <td class="px-3 py-2 whitespace-nowrap">${row.tujuan_pengambilan}</td>
                <td class="px-3 py-2 whitespace-nowrap">${row.gudang_tujuan}</td>
                <td class="px-3 py-2 whitespace-nowrap">Rp ${new Intl.NumberFormat('id-ID').format(row.uang_jalan)}</td>
                <td class="px-3 py-2">${row.catatan}</td>
            `;
            tbody.appendChild(tr);
        }
    });

    document.getElementById('bulkCount').innerText = validCount;
    document.getElementById('bulkPreviewContainer').classList.remove('hidden');

    const btnSubmit = document.getElementById('btnSubmitBulk');
    btnSubmit.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Massal';
    
    if (validCount > 0) {
        btnSubmit.disabled = false;
        btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
        showBulkAlert('success', `Berhasil mem-parsing ${validCount} baris data. Silakan cek pratinjau di bawah, lalu klik Simpan.`);
    } else {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
        showBulkAlert('error', 'Tidak ada data valid yang bisa diparsing.');
    }
}

function submitBulkData() {
    if (bulkParsedRows.length === 0) return;

    const gudang_tujuan_id = document.getElementById('bulk_gudang_tujuan').value;

    const btnSubmit = document.getElementById('btnSubmitBulk');
    const originalText = btnSubmit.innerHTML;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
    btnSubmit.disabled = true;

    fetch('{{ route('surat-jalan-tarik-kosong-batam.store-bulk') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            rows: bulkParsedRows,
            gudang_tujuan_id: gudang_tujuan_id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            let errorHtml = data.message + '<br><br>';
            if (data.errors && data.errors.length > 0) {
                errorHtml += '<ul class="text-left text-sm list-disc pl-5">';
                data.errors.forEach(err => {
                    errorHtml += `<li>${err}</li>`;
                });
                errorHtml += '</ul>';
            }
            
            if (data.failedRows && data.failedRows.length > 0) {
                document.getElementById('bulkTextarea').value = data.failedRows.join('\n');
                parseBulkData(); 
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                html: errorHtml
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server.', 'error');
    })
    .finally(() => {
        btnSubmit.innerHTML = originalText;
        btnSubmit.disabled = false;
    });
}

function showBulkAlert(type, message) {
    const alertArea = document.getElementById('bulkModalAlertArea');
    let colorClass = 'bg-blue-100 text-blue-800 border-blue-200';
    let icon = 'fas fa-info-circle';
    
    if (type === 'success') {
        colorClass = 'bg-green-100 text-green-800 border-green-200';
        icon = 'fas fa-check-circle';
    } else if (type === 'error') {
        colorClass = 'bg-red-100 text-red-800 border-red-200';
        icon = 'fas fa-exclamation-circle';
    } else if (type === 'warning') {
        colorClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
        icon = 'fas fa-exclamation-triangle';
    }
    
    alertArea.innerHTML = `
        <div class="mb-4 p-3 rounded-lg border ${colorClass} text-sm flex items-start">
            <i class="${icon} mt-0.5 mr-2"></i>
            <div>${message}</div>
        </div>
    `;
}
</script>
@endpush
