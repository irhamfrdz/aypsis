<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jenis Barang</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md">
        <div class="border-b border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-900">Tambah Jenis Barang Baru</h2>
        </div>

        <form action="{{ route('order.jenis-barang.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <!-- Hidden field to mark as popup -->
            <input type="hidden" name="popup" value="1">

            <!-- Kode Jenis Barang (Auto-generated) -->
            <div>
                <label for="kode_jenis_barang" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Jenis Barang
                </label>
                <input type="text" 
                       id="kode" 
                       name="kode" 
                       value="{{ $nextCode ?? '' }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                       readonly>
                <p class="mt-1 text-sm text-gray-500">Kode akan dibuat otomatis</p>
            </div>

            <!-- Nama Jenis Barang -->
            <div>
                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Jenis Barang <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nama_barang" 
                       name="nama_barang" 
                       value="{{ old('nama_barang', request('search')) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan nama jenis barang"
                       required
                       autofocus>
                @error('nama_barang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea id="keterangan" 
                          name="keterangan" 
                          rows="3"
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Masukkan keterangan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" 
                        onclick="window.close()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    @if ($errors->any())
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mt-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate kode on page load
            generateKode();
            
            // Focus on the nama_barang input when page loads
            const namaBarangInput = document.getElementById('nama_barang');
            if (namaBarangInput) {
                setTimeout(() => {
                    namaBarangInput.focus();
                    namaBarangInput.select();
                }, 100);
            }

            function generateKode() {
                // Generate simple auto-incrementing code
                fetch('{{ route("order.jenis-barang.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        _generate_code_only: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.code) {
                        document.getElementById('kode').value = data.code;
                    }
                })
                .catch(() => {
                    // Fallback: generate client-side code with timestamp
                    const now = new Date();
                    const year = now.getFullYear().toString().substr(-2);
                    const month = (now.getMonth() + 1).toString().padStart(2, '0');
                    const day = now.getDate().toString().padStart(2, '0');
                    const time = now.getTime().toString().substr(-4);
                    const code = 'JB' + year + month + day + time.substr(0, 1);
                    document.getElementById('kode').value = code;
                });
            }

            // Handle form submission with validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const namaBarang = document.getElementById('nama_barang').value.trim();
                
                if (!namaBarang) {
                    e.preventDefault();
                    alert('Nama Jenis Barang harus diisi');
                    document.getElementById('nama_barang').focus();
                    return false;
                }
            });

            // Auto-hide error messages after 5 seconds
            const errorDiv = document.querySelector('.fixed.bg-red-100');
            if (errorDiv) {
                setTimeout(function() {
                    errorDiv.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</body>
</html>