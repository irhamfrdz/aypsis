<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Penerima</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md">
        <div class="border-b border-gray-200 p-4 bg-gradient-to-r from-blue-500 to-blue-600">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-user-plus mr-2"></i>
                Tambah Penerima Baru
            </h2>
        </div>

        <form action="{{ route('tanda-terima.penerima.store') }}" method="POST" class="p-6 space-y-4" id="penerimaForm">
            @csrf
            
            <!-- Hidden field to mark as popup -->
            <input type="hidden" name="popup" value="1">

            <!-- Kode (Auto-generated) -->
            <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode
                </label>
                <input type="text" 
                       id="kode" 
                       name="kode" 
                       value="{{ $kodeOtomatis ?? '' }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                       readonly>
                <p class="mt-1 text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>Kode akan dibuat otomatis
                </p>
            </div>

            <!-- Nama Penerima -->
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Penerima <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nama" 
                       name="nama" 
                       value="{{ old('nama') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nama') border-red-500 @enderror"
                       placeholder="Masukkan nama penerima"
                       required
                       autofocus>
                @error('nama')
                    <p class="mt-1 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                    Alamat
                </label>
                <textarea id="alamat" 
                          name="alamat" 
                          rows="3"
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('alamat') border-red-500 @enderror"
                          placeholder="Masukkan alamat lengkap (opsional)">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                @enderror
            </div>

            <!-- NPWP -->
            <div>
                <label for="npwp" class="block text-sm font-medium text-gray-700 mb-2">
                    NPWP
                </label>
                <input type="text" 
                       id="npwp" 
                       name="npwp" 
                       value="{{ old('npwp') }}"
                       maxlength="20"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('npwp') border-red-500 @enderror"
                       placeholder="Masukkan NPWP (opsional)">
                @error('npwp')
                    <p class="mt-1 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>Maksimal 20 karakter
                </p>
            </div>

            <!-- Status (Hidden - default active) -->
            <input type="hidden" name="status" value="active">

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" 
                        onclick="window.close()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-save mr-1"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>

    @if (session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-md z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md z-50">
            <strong class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Terjadi kesalahan:
            </strong>
            <ul class="mt-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus on the nama input when page loads
            const namaInput = document.getElementById('nama');
            if (namaInput) {
                setTimeout(() => {
                    namaInput.focus();
                    namaInput.select();
                }, 100);
            }

            // Handle form submission
            const form = document.getElementById('penerimaForm');
            form.addEventListener('submit', function(e) {
                const nama = document.getElementById('nama').value.trim();
                
                if (!nama) {
                    e.preventDefault();
                    alert('Nama Penerima harus diisi');
                    document.getElementById('nama').focus();
                    return false;
                }
            });

            // Auto-hide messages after 5 seconds
            const messages = document.querySelectorAll('.fixed.bg-red-100, .fixed.bg-green-100');
            messages.forEach(function(msg) {
                setTimeout(function() {
                    msg.style.display = 'none';
                }, 5000);
            });

            // If form submission was successful and this is a popup
            @if(session('success') && session('popup'))
                // Send message to parent window
                if (window.opener) {
                    const penerimaData = {
                        type: 'penerimaAdded',
                        penerima: {
                            nama: '{{ session("penerima_nama") }}',
                            alamat: '{{ session("penerima_alamat") ?? "" }}',
                            npwp: '{{ session("penerima_npwp") ?? "" }}'
                        }
                    };
                    
                    console.log('Sending penerima data to parent:', penerimaData);
                    window.opener.postMessage(penerimaData, window.location.origin);
                    
                    // Close popup after short delay
                    setTimeout(function() {
                        window.close();
                    }, 1000);
                } else {
                    console.error('window.opener not available');
                }
            @endif
        });
    </script>
</body>
</html>
